<?php

namespace App\Http\Controllers\Store;

use App\Events\ChatMessageCreated;
use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    public function __construct()
    {
        // Khách chỉ cần đăng nhập là dùng được widget
        $this->middleware(['auth']);
    }

    /** Mở (hoặc lấy) phiên chat của user hiện tại */
    public function open(Request $request)
    {
        $user = Auth::user();

        $session = ChatSession::firstOrCreate(
            ['user_id' => $user->user_id],
            ['last_message_at' => now()]
        );

        return response()->json(['session_id' => $session->session_id]);
    }

    /** Lấy lịch sử tin nhắn */
   public function history($sessionId)
{
    /** @var \App\Models\User $user */
    $user    = Auth::user();
    $session = ChatSession::findOrFail($sessionId);

    $canManage = method_exists($user, 'hasPermission') && $user->hasPermission('manage_livechat');
    abort_unless($user && ($user->user_id === $session->user_id || $canManage), 403);

        $messages = $session->messages()->with('sender:user_id,full_name')->get();

        // Khi mở lịch sử, đánh dấu đã đọc các tin nhắn đến từ phía còn lại
        Message::where('session_id', $session->session_id)
            ->where('sender_id', '<>', $user->user_id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['messages' => $messages]);
    }

    /** Gửi tin nhắn */
public function send(Request $request)
{
    $data = $request->validate([
        'session_id' => ['required','integer','exists:chat_sessions,session_id'],
        'content'    => ['required','string','max:5000'],
    ]);

    /** @var \App\Models\User $user */
    $user    = Auth::user();
    $session = ChatSession::findOrFail($data['session_id']);

    $canManage = method_exists($user, 'hasPermission') && $user->hasPermission('manage_livechat');
    abort_unless($user->user_id === $session->user_id || $canManage, 403);

    if ($canManage && !$session->assigned_admin_id) {
        $session->assigned_admin_id = $user->user_id;
    }

    $msg = Message::create([
        'session_id' => $session->session_id,
        'sender_id'  => $user->user_id,
        'content'    => $data['content'],
        'is_read'    => 0,
    ]);

    $session->last_message_at = Carbon::now();
    $session->save();

    broadcast(new ChatMessageCreated($msg))->toOthers();

    return response()->json(['message' => 'ok', 'data' => $msg]);
}



    /** Đánh dấu đã đọc (theo người đang xem) */
    public function markRead(Request $request): JsonResponse
    {
        $data = $request->validate([
            'session_id' => ['required', 'integer', 'exists:chat_sessions,session_id'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $session = ChatSession::findOrFail($data['session_id']);

        // Cho phép: chủ phiên hoặc người có quyền manage_livechat
        $canManage = method_exists($user, 'hasPermission') && $user->hasPermission('manage_livechat');
        abort_unless($user && ($user->user_id === $session->user_id || $canManage), 403);

        // Đánh dấu đã đọc các tin nhắn đến từ phía còn lại
        Message::where('session_id', $session->session_id)
            ->where('sender_id', '<>', $user->user_id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['message' => 'ok']);
}
}
