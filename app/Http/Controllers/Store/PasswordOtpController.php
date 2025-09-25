<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Mail\PasswordOtpMail;
use App\Models\PasswordOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password as PasswordRule;

class PasswordOtpController extends Controller
{
    private int $ttlMinutes = 10; // hiệu lực OTP 10 phút

    /** Trang “Đổi mật khẩu qua OTP” */
    public function requestForm(Request $request)
    {
        $user = $request->user();

        return view('store.profile.password.request', [
            'user'      => $user,
            'canSendAt' => Cache::get($this->rateLimitKey($user->user_id)), // thời điểm hết chờ (nếu có)
        ]);
    }

    /** Gửi OTP tới email người dùng (rate-limit 60s) */
    public function sendOtp(Request $request)
    {
        $user = $request->user();

        // Chống spam: chỉ cho gửi 1 lần / 60s
        $key = $this->rateLimitKey($user->user_id);
        if (Cache::has($key)) {
            return back()->withErrors('Bạn vừa yêu cầu mã OTP, vui lòng thử lại sau ít phút.');
        }
        Cache::put($key, now()->addSeconds(60)->timestamp, 60);

        // Xoá mã cũ chưa dùng (nếu có) rồi tạo mã mới
        PasswordOtp::where('user_id', $user->user_id)->whereNull('used_at')->delete();

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        PasswordOtp::create([
            'user_id'    => $user->user_id,
            'code'       => $code,
            'expires_at' => now()->addMinutes($this->ttlMinutes),
        ]);

        // Gửi email
        Mail::to($user->email)->send(
            new PasswordOtpMail($user->full_name ?: $user->email, $code, $this->ttlMinutes)
        );

        return redirect()->route('profile.password.verify.form')
            ->with('ok', 'Đã gửi mã OTP đến email ' . $user->email);
    }

    /** Form nhập OTP */
    public function verifyForm(Request $request)
    {
        return view('store.profile.password.verify');
    }

    /** Xử lý xác thực OTP */
    public function verifySubmit(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ], [], ['code' => 'Mã OTP']);

        $user = $request->user();

        $otp = PasswordOtp::query()
            ->where('user_id', $user->user_id)
            ->where('code', $request->input('code'))
            ->orderByDesc('id')
            ->first();

        if (!$otp) {
            return back()->withErrors('Mã OTP không đúng.');
        }
        if ($otp->isUsed()) {
            return back()->withErrors('Mã OTP đã được sử dụng.');
        }
        if ($otp->isExpired()) {
            return back()->withErrors('Mã OTP đã hết hạn.');
        }

        // Đánh dấu đã dùng + mở session cho bước đặt mật khẩu
        $otp->update(['used_at' => now()]);
        session([
            'pwdotp.verified' => true,
            'pwdotp.user'     => $user->user_id,
            'pwdotp.otp_id'   => $otp->id,
        ]);

        return redirect()->route('profile.password.reset.form')
            ->with('ok', 'Xác thực OTP thành công. Vui lòng đặt mật khẩu mới.');
    }

    /** Form đặt mật khẩu mới */
    public function resetForm(Request $request)
    {
        $this->ensureVerified($request);
        return view('store.profile.password.reset');
    }

    /** Đổi mật khẩu */
    public function resetSubmit(Request $request)
    {
        $this->ensureVerified($request);

        $data = $request->validate([
            'password' => [
                'required',
                'confirmed',
                'string',
                PasswordRule::min(6), // quy tắc độ mạnh dùng PasswordRule
                'max:100',            // giới hạn độ dài dùng rule chuỗi thông thường
            ],
        ], [], ['password' => 'Mật khẩu mới']);


        $user = $request->user();

        // User model của bạn đã có mutator setPasswordAttribute() => gán thẳng là sẽ tự hash
        $user->password = $data['password'];
        $user->save();

        // Dọn session xác thực OTP
        $request->session()->forget(['pwdotp.verified', 'pwdotp.user', 'pwdotp.otp_id']);

        // (tuỳ chọn) đăng xuất các thiết bị khác với tài khoản này
        Auth::logoutOtherDevices($data['password']);

        return redirect()->route('profile.password.request')->with('ok', 'Đổi mật khẩu thành công!');
    }

    private function ensureVerified(Request $request): void
    {
        $user = $request->user();
        if (!session('pwdotp.verified') || session('pwdotp.user') !== $user->user_id) {
            abort(403, 'Bạn cần xác thực OTP trước khi đặt mật khẩu.');
        }
    }

    private function rateLimitKey(int $userId): string
    {
        return 'pwdotp:send:' . $userId;
    }
}
