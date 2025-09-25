<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PayOSService
{
    public function createPaymentLink(array $payload): array
    {
        $cfg = config('services.payos');

        $body = [
            'orderCode' => $payload['orderCode'], // số đơn duy nhất (int)
            'amount' => (int) $payload['amount'], // đơn vị VND
            'description' => $payload['description'], // ND chuyển khoản
            'returnUrl' => $cfg['return_url'],
            'cancelUrl' => $cfg['cancel_url'],
            'buyerName' => $payload['buyerName'] ?? null,
            'buyerEmail' => $payload['buyerEmail'] ?? null,
            'buyerPhone' => $payload['buyerPhone'] ?? null,
            'items' => $payload['items'] ?? [],
            // có thể thêm expiredAt (unix timestamp) nếu muốn
        ];

        // Tạo chữ ký HMAC SHA256 từ body (theo thứ tự field)
        $signature = $this->signature($body, $cfg['checksum_key']);
        $body['signature'] = $signature;

        $res = Http::withHeaders([
            'x-client-id' => $cfg['client_id'],
            'x-api-key' => $cfg['api_key'],
            'Content-Type' => 'application/json',
        ])->post($cfg['endpoint'] . '/payment-requests', $body)
            ->throw();

        return $res->json(); // gồm checkoutUrl, qrCode, paymentLinkId, ...
    }

    public function verifySignature(array $data): bool
    {
        $cfg = config('services.payos');
        if (!isset($data['signature'])) return false;

        $sig = $data['signature'];
        unset($data['signature']);

        return hash_equals($sig, $this->signature($data, $cfg['checksum_key']));
    }

    private function signature(array $data, string $key): string
    {
        // Ghép theo thứ tự key tăng dần, bỏ null
        ksort($data);
        $flat = [];
        foreach ($data as $k => $v) {
            if (is_null($v)) continue;
            $flat[] = $k . '=' . $v;
        }
        $raw = implode('&', $flat);
        return hash_hmac('sha256', $raw, $key);
    }
}
