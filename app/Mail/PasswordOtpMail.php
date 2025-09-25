<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;
    public string $code;
    public int $ttl;

    public function __construct(string $name, string $code, int $ttlMinutes = 10)
    {
        $this->name = $name;
        $this->code = $code;
        $this->ttl  = $ttlMinutes;
    }

    public function build()
    {
        return $this->subject('Mã xác thực đổi mật khẩu')
            ->view('emails.password_otp');
    }
}
