<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Socialite;
use App\Models\User; // hoặc model User của bạn
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FacebookController extends Controller
{
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            // lấy dữ liệu người dùng từ Facebook
            $facebookUser = Socialite::driver('facebook')->stateless()->user();

            // tìm hoặc tạo user mới
          $user = User::firstOrCreate(
    ['email' => $facebookUser->getEmail()],
    [
        'full_name' => $facebookUser->getName(), // đổi name → full_name
        'email' => $facebookUser->getEmail(),
        'password' => bcrypt(str()->random(16)),
        'facebook_id' => $facebookUser->getId(), // lưu luôn id facebook
        'role_id' => 3 // nếu muốn mặc định là khách hàng
    ]
);


            // Đăng nhập user và tái tạo session
            Auth::login($user);
            session()->regenerate();
            Log::info('User logged in via Facebook: ' . $user->id);

            // Chuyển hướng đến trang home
            return redirect()->route('home')->with('success', 'Đăng nhập Facebook thành công!');
        } catch (\Exception $e) {
            Log::error('Facebook callback failed: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Đăng nhập Facebook thất bại: ' . $e->getMessage());
        }
    }
}
