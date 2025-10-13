<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Mail\OtpCodeMail;

class PassworddController extends Controller
{
    /** 1) Form nhập email */
    public function requestForm()
    {
        return view('auth.forgot.request');
    }

    /** 2) Gửi OTP: lưu vào password_otps(user_id, code, expires_at) */
    public function sendOtp(Request $r)
    {
        $v = Validator::make($r->all(), [
            'email' => ['required', 'email', 'max:255'],
        ], [], ['email' => 'Email']);
        if ($v->fails()) return back()->withErrors($v)->withInput();

        $email = strtolower(trim($r->input('email')));
        $user  = DB::table('users')->where('email', $email)->first();
        if (!$user) return back()->with('error', 'Email không tồn tại.');

        // Chặn spam đơn giản: mỗi 60s
        $latest = DB::table('password_otps')
            ->where('user_id', $user->user_id ?? $user->id ?? null)
            ->orderByDesc('id')->first();

        if ($latest && Carbon::parse($latest->created_at)->diffInSeconds(now()) < 60) {
            return back()->with('error', 'Bạn vừa yêu cầu OTP, vui lòng thử lại sau ít phút.');
        }

        // Tạo OTP 6 số
        $otp = (string) random_int(100000, 999999);

        DB::table('password_otps')->insert([
            'user_id'    => $user->user_id ?? $user->id,
            'code'       => $otp,                   // (Ghi chú bảo mật: xem phần NOTE bên dưới)
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Mail::to($email)->send(new OtpCodeMail($otp));

        // Lưu vào session để đi tiếp
        $r->session()->put('otp_user_id', $user->user_id ?? $user->id);
        $r->session()->put('otp_email', $email);

        return redirect()->route('profile.password.verify.form')
            ->with('ok', 'Mã OTP đã được gửi tới email của bạn.');
    }

    /** 3) Form nhập OTP */
    public function verifyForm(Request $r)
    {
        if (!$r->session()->has('otp_user_id')) {
            return redirect()->route('profile.password.request');
        }
        return view('auth.forgot.verify', ['email' => $r->session()->get('otp_email')]);
    }

    /** 4) Xử lý xác minh OTP: so sánh với cột code và hết hạn */
    public function verifySubmit(Request $r)
    {
        $v = Validator::make($r->all(), [
            'otp' => ['required', 'digits:6'],
        ], [], ['otp' => 'Mã OTP']);
        if ($v->fails()) return back()->withErrors($v)->withInput();

        $userId = $r->session()->get('otp_user_id');
        if (!$userId) return redirect()->route('profile.password.request');

        // Lấy OTP còn hạn, chưa dùng, mới nhất
        $row = DB::table('password_otps')
            ->where('user_id', $userId)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->orderByDesc('id')
            ->first();

        if (!$row) return back()->with('error', 'Không có mã OTP hợp lệ. Vui lòng gửi lại mã.');

        if ($r->input('otp') !== $row->code) {
            return back()->with('error', 'Mã OTP không đúng.')->withInput();
        }

        // Đánh dấu đã dùng
        DB::table('password_otps')->where('id', $row->id)->update([
            'used_at'    => now(),
            'updated_at' => now(),
        ]);

        $r->session()->put('otp_verified', true);

        return redirect()->route('profile.password.reset.form')
            ->with('ok', 'Xác minh OTP thành công. Hãy đặt mật khẩu mới.');
    }

    /** 5) Form đặt mật khẩu mới */
    public function resetForm(Request $r)
    {
        if (!$r->session()->get('otp_verified') || !$r->session()->get('otp_user_id')) {
            return redirect()->route('profile.password.request');
        }
        return view('auth.forgot.reset');
    }

    /** 6) Lưu mật khẩu mới cho users.user_id */
    public function resetSubmit(Request $r)
    {
        if (!$r->session()->get('otp_verified') || !$r->session()->get('otp_user_id')) {
            return redirect()->route('profile.password.request');
        }

        $v = Validator::make($r->all(), [
            'password' => ['required', 'string', 'min:6', 'max:72', 'confirmed'],
        ], [], ['password' => 'Mật khẩu']);
        if ($v->fails()) return back()->withErrors($v);

        $userId = $r->session()->get('otp_user_id');

        DB::table('users')->where('user_id', $userId)->update([
            'password' => Hash::make($r->input('password')),
        ]);

        // Dọn session
        $r->session()->forget(['otp_user_id', 'otp_email', 'otp_verified']);

        return redirect('/login')->with('ok', 'Đổi mật khẩu thành công. Vui lòng đăng nhập.');
    }
}
