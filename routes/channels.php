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
    $session = ChatSession::select('user_id','assigned_admin_id')->find($sessionId);
    if (!$session) return false;

    if ((int)$session->user_id === (int)$user->user_id) return true; // chủ phiên

    $canManage = method_exists($user,'hasPermission') && $user->hasPermission('manage_livechat');
    if (!$canManage) return false;

    // chưa ai nhận -> tất cả admin có quyền được nghe
    if (is_null($session->assigned_admin_id)) return true;

    // đã nhận -> chỉ admin được assign nghe
    return (int)$session->assigned_admin_id === (int)$user->user_id;
});

