<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GoogleLoginController extends Controller
{
    /**
     * Chuyển hướng người dùng đến trang xác thực Google.
     *
     * @return \Illuminate\Http\RedirectResponse
     */

    public function redirectToGoogle()
    {
        \Log::info('Redirecting to Google'); // Ghi log
        return Socialite::driver('google')->redirect();
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

            // Tìm user theo email
            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                // Nếu user tồn tại, cập nhật google_id và avatar nếu cần
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->avatar ?? null,
                    ]);
                }
            } else {
                // Tạo user mới nếu chưa tồn tại
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar ?? null,
                    'password' => Hash::make(rand(100000, 999999)), // Password ngẫu nhiên
                ]);
            }



            // Đăng nhập user
            Auth::login($user);

            // Chuyển hướng đến dashboard (hoặc trang mong muốn)
            return redirect()->intended('/dashboard');
        } catch (\Exception $e) {
            // Xử lý lỗi và chuyển hướng về login
            return redirect('/login')->with('error', 'Đăng nhập Google thất bại: ' . $e->getMessage());
        }
    }
}
