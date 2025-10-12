<?php

use App\Models\ChatSession;
use Illuminate\Support\Facades\Broadcast;

/**
 * Nếu không dùng channel user mặc định, có thể xoá block này cho gọn.
 * Nếu muốn giữ, sửa về user_id để khớp PK của bạn.
 */
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->user_id === (int) $id;
});

/**
 * Private channel cho từng phiên chat
 * - Chủ phiên (khách) nghe được
 * - Nhân sự có quyền manage_livechat nghe được (toàn quyền)
 *   -> nếu muốn siết chặt hơn, bật thêm điều kiện assigned ở dưới
 */
Broadcast::channel('chat.session.{sessionId}', function ($user, $sessionId) {
    $isOwner = ChatSession::where('session_id', $sessionId)
        ->where('user_id', $user->user_id)
        ->exists();

    $canManage = method_exists($user, 'hasPermission')
        && $user->hasPermission('manage_livechat');

    // Nếu muốn chỉ agent đã được assign mới nghe được, dùng thêm:
    // $assigned = ChatSession::where('session_id', $sessionId)
    //     ->where('assigned_admin_id', $user->user_id)
    //     ->exists();
    // return $isOwner || ($canManage && $assigned);

    return $isOwner || $canManage;
});
