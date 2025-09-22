<?php

namespace App\Services;

class VnpayService
{
    public function createPaymentUrl(int $orderId, int $amountVnd, string $clientIp, string $returnUrl): string
    {
        $params = [
            'vnp_Version'   => '2.1.0',
            'vnp_Command'   => 'pay',
            'vnp_TmnCode'   => env('VNPAY_TMN_CODE'),
            'vnp_Amount'    => $amountVnd * 100,
            'vnp_CurrCode'  => 'VND',
            'vnp_TxnRef'    => $orderId . '-' . time(),
            'vnp_OrderInfo' => 'Thanh toan don hang #' . $orderId,
            'vnp_OrderType' => 'other',
            'vnp_Locale'    => 'vn',
            'vnp_IpAddr'    => $clientIp,
            'vnp_ReturnUrl' => $returnUrl,
            'vnp_CreateDate' => now()->format('YmdHis'),
        ];
        ksort($params);
        $hashData = implode('&', array_map(fn($k) => $k . '=' . $params[$k], array_keys($params)));
        $secure   = hash_hmac('sha512', $hashData, env('VNPAY_HASH_SECRET'));
        return rtrim(env('VNPAY_PAYMENT_URL'), '/') . '?' . http_build_query($params) . '&vnp_SecureHash=' . $secure;
    }

    public function verifyReturn(array $all): bool
    {
        $secureHash = $all['vnp_SecureHash'] ?? '';
        unset($all['vnp_SecureHash'], $all['vnp_SecureHashType']);
        ksort($all);
        $hashData = implode('&', array_map(fn($k) => $k . '=' . $all[$k], array_keys($all)));
        return hash_equals(hash_hmac('sha512', $hashData, env('VNPAY_HASH_SECRET')), $secureHash);
    }
}
