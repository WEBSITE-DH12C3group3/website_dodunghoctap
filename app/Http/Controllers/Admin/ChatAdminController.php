<?php

namespace App\Http\Controllers\Admin;

use App\Events\ChatMessageCreated;
use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ChatAdminController extends Controller
{
    public function __construct()
    {
        // Chỉ những user có quyền manage_livechat mới được vào khu vực này
        $this->middleware(['auth', 'permission:manage_livechat']);
    }

    /**
     * Danh sách phiên chat với filter:
     *  - q: tìm theo #id hoặc tên khách
     *  - mine: all | me | unassigned
     *  - unread: 1 => chỉ phiên có tin chưa đọc đối với tôi
     *  - sort: last_message_at|created_at, dir: asc|desc
     *  - size: page size
     */
    public function index(Request $request)
    {
        $meId = (int) auth()->user()->user_id;

        $q      = trim((string) $request->query('q', ''));
        $mine   = $request->query('mine', 'all');
        $unread = $request->boolean('unread', false);
        $sort   = $request->query('sort', 'last_message_at');
        $dir    = strtolower($request->query('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $size   = max(5, min(200, (int) $request->query('size', 20)));

        $query = ChatSession::query()
            ->with(['user:user_id,full_name', 'assignedAdmin:user_id,full_name'])
            ->addSelect([
                'unread_count' => Message::query()
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('messages.session_id', 'chat_sessions.session_id')
                    ->where('messages.is_read', false)
                    ->where('messages.sender_id', '<>', $meId)
            ]);

        if ($mine === 'me') {
            $query->where('assigned_admin_id', $meId);
        } elseif ($mine === 'unassigned') {
            $query->whereNull('assigned_admin_id');
        }

        if ($unread) {
            $query->whereRaw(
                "(SELECT COUNT(*) FROM messages 
                  WHERE messages.session_id = chat_sessions.session_id
                    AND messages.is_read = 0 
                    AND messages.sender_id <> ?) > 0", [$meId]
            );
        }

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                if (preg_match('/^\#?(\d+)$/', $q, $m)) {
                    $sid = (int) $m[1];
                    $sub->orWhere('session_id', $sid);
                }
                $sub->orWhereHas('user', function ($u) use ($q) {
                    $u->where('full_name', 'like', '%' . $q . '%');
                });
            });
        }

        if (!in_array($sort, ['last_message_at', 'created_at'], true)) {
            $sort = 'last_message_at';
        }
        $query->orderBy($sort, $dir);

        $sessions = $query->paginate($size)->appends($request->query());

        return view('admin.chat.index', [
            'sessions' => $sessions,
            'filters'  => compact('q','mine','unread','sort','dir','size'),
        ]);
    }

    /** Nhận phiên về mình (assign to me) */
    public function assign(Request $request, $sessionId)
    {
        $meId = (int) auth()->user()->user_id;
        $session = ChatSession::findOrFail($sessionId);

        $force = $request->boolean('force', false);
        if ($session->assigned_admin_id && $session->assigned_admin_id !== $meId && !$force) {
            return back()->withErrors(['assign' => 'Phiên đã được phân công cho người khác. Thêm ?force=1 để tiếp nhận.']);
        }

        $session->assigned_admin_id = $meId;
        $session->save();

        return back()->with('ok', 'Đã nhận phụ trách phiên #' . $session->session_id);
    }

    /** Bỏ phân công */
    public function unassign(Request $request, $sessionId)
    {
        $meId = (int) auth()->user()->user_id;
        $session = ChatSession::findOrFail($sessionId);

        if ($session->assigned_admin_id !== $meId) {
            abort(403, 'Chỉ người đang phụ trách mới được bỏ phân công.');
        }

        $session->assigned_admin_id = null;
        $session->save();

        return back()->with('ok', 'Đã bỏ phân công phiên #' . $session->session_id);
    }

    /** Xem chi tiết (đồng thời markRead tin nhắn đến từ phía còn lại) */
    public function view($sessionId)
    {
        $meId = (int) auth()->user()->user_id;

        $session = ChatSession::with([
            'messages' => function ($q) {
                $q->orderBy('created_at', 'asc');
            },
            'messages.sender:user_id,full_name',
            'user:user_id,full_name',
            'assignedAdmin:user_id,full_name',
        ])->findOrFail($sessionId);

        // Chính sách: ai có manage_livechat đều xem được (nếu muốn siết theo assigned thì bật dòng dưới)
        // if ($session->assigned_admin_id && $session->assigned_admin_id !== $meId) abort(403);

        Message::where('session_id', $session->session_id)
            ->where('sender_id', '<>', $meId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('admin.chat.show', compact('session'));
    }

    /** Gửi tin nhắn với tư cách agent */
    public function send(Request $request, $sessionId)
    {
        $meId    = (int) auth()->user()->user_id;
        $session = \App\Models\ChatSession::findOrFail($sessionId);

        if ($session->assigned_admin_id && $session->assigned_admin_id !== $meId) {
            return response()->json(['ok'=>false,'error'=>'Phiên đã do người khác phụ trách.'], 403);
        }

        $data = $request->validate([
            'content' => ['nullable','string','max:5000'],
            'text'    => ['nullable','string','max:5000'],
        ]);
        $content = trim((string)($data['content'] ?? $data['text'] ?? ''));

        if ($content === '') {
            return response()->json(['ok'=>false,'error'=>'Nội dung không được để trống.'], 422);
        }

        $msg = \App\Models\Message::create([
            'session_id' => $session->session_id,
            'sender_id'  => $meId,
            'content'    => $content,
            'is_read'    => 0,
        ]);

        $session->last_message_at   = now();
        $session->assigned_admin_id = $session->assigned_admin_id ?: $meId;
        $session->save();

        try {
            // nạp sẵn sender để event lấy tên
            $msg->load('sender:user_id,full_name');
            broadcast(new \App\Events\ChatMessageCreated($msg))->toOthers();
        } catch (\Throwable $e) {
            \Log::warning('Broadcast chat fail: '.$e->getMessage());
            // Không để lỗi broadcast làm hỏng request
        }

        return response()->json([
            'ok'  => true,
            'msg' => [
                'id'               => $msg->message_id,  // dùng đúng PK
                'sender_id'        => $msg->sender_id,
                'sender_name'      => optional($msg->sender)->full_name ?? 'Tôi',
                'content'          => $msg->content,
                'created_at'       => $msg->created_at,
                'created_at_label' => optional($msg->created_at)->format('H:i d/m'),
            ],
        ]);
    }


    /** API JSON: load messages (phục vụ AJAX refresh khi đang mở trang) */
    public function messages(Request $request, $sessionId)
    {
        $meId   = (int) auth()->user()->user_id;
        $session= ChatSession::with(['user:user_id,full_name','assignedAdmin:user_id,full_name'])
                    ->findOrFail($sessionId);

        // (Tùy chính sách) chặn xem khi đã assign người khác:
        // if ($session->assigned_admin_id && $session->assigned_admin_id !== $meId) abort(403);

// ChatAdminController@messages
        $messages = Message::with('sender:user_id,full_name')
            ->where('session_id', $session->session_id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($m) {
                return [
                    'id'               => $m->message_id,   // 👈 dùng đúng PK
                    'session_id'       => $m->session_id,
                    'sender_id'        => $m->sender_id,
                    'sender_name'      => $m->sender?->full_name ?? 'N/A',
                    'content'          => $m->content,
                    'created_at'       => $m->created_at,
                    'created_at_label' => optional($m->created_at)->format('H:i d/m'),
                ];
            });


        // mark read tin từ phía còn lại
        Message::where('session_id', $session->session_id)
            ->where('sender_id', '<>', $meId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'session'  => [
                'session_id'     => $session->session_id,
                'user'           => ['full_name' => $session->user?->full_name],
                'assigned_admin' => $session->assignedAdmin?->only(['user_id','full_name']),
            ],
            'messages' => $messages,
        ]);
    }

}
