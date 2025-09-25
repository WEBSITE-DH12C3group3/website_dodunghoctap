<!doctype html>
<html>
  <body style="font-family: Arial, sans-serif; color:#111">
    <h2>Xin chào {{ $name }},</h2>
    <p>Mã xác thực đổi mật khẩu của bạn là:</p>
    <p style="font-size:24px; font-weight:700; letter-spacing:3px;">
      {{ $code }}
    </p>
    <p>Mã có hiệu lực trong {{ $ttl }} phút. Nếu không phải bạn yêu cầu, vui lòng bỏ qua email này.</p>
    <hr>
    <p style="font-size:12px; color:#666">Hệ thống {{ config('app.name') }}</p>
  </body>
</html>
