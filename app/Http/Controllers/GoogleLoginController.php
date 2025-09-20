<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class GoogleLoginController extends Controller
{
    /**
     * Chuyển hướng người dùng đến trang xác thực Google.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToGoogle()
    {
        Log::info('Redirecting to Google');
        try {
            return Socialite::driver('google')->redirect();
        } catch (\Exception $e) {
            Log::error('Google redirect failed: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Không thể kết nối với Google. Vui lòng thử lại.');
        }
    }

    /**
     * Xử lý callback từ Google sau khi xác thực.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback()
    {
        try {
            // Lấy thông tin người dùng từ Google
            $googleUser = Socialite::driver('google')->stateless()->user();
            Log::info('Google user retrieved: ' . $googleUser->email);

            // Tìm hoặc tạo user
            $user = User::updateOrCreate(
                ['email' => $googleUser->email],
                [
                    'full_name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'role_id' => 3,
                    'password' => Hash::make(rand(100000, 999999)),
                    'is_active' => 1,
                ]
            );
            Log::info('User processed: ' . $user->id);

            // Đăng nhập user và tái tạo session
            Auth::login($user);
            session()->regenerate();
            Log::info('User logged in: ' . $user->id);

            // Chuyển hướng đến trang home
            $redirect = redirect()->route('home')->with('success', 'Đăng nhập thành công!');
            Log::info('Redirecting to: ' . route('home') . ', Response: ' . $redirect->getTargetUrl());
            return $redirect;
        } catch (\Exception $e) {
            Log::error('Google callback failed: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Đăng nhập Google thất bại: ' . $e->getMessage());
        }
    }
}