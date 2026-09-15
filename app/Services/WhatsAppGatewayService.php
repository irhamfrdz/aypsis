<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppGatewayService
{
    protected string $gatewayUrl;

    public function __construct()
    {
        $this->gatewayUrl = rtrim(config('services.wa_gateway.url', env('WA_GATEWAY_URL', 'http://127.0.0.1:3000')), '/');
    }

    /**
     * Cek apakah WhatsApp Gateway terhubung dan siap kirim
     */
    public function getStatus(): array
    {
        try {
            $response = Http::timeout(3)->get("{$this->gatewayUrl}/status");
            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => false,
                'isReady' => false,
                'connection' => 'error',
                'message' => 'Gateway server merespon dengan error HTTP ' . $response->status()
            ];
        } catch (\Throwable $e) {
            return [
                'status' => false,
                'isReady' => false,
                'connection' => 'offline',
                'message' => 'Microservice WA Gateway belum aktif: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Kirim pesan WhatsApp ke satu nomor
     */
    public function sendMessage(string $phone, string $message): array
    {
        try {
            $response = Http::timeout(10)->post("{$this->gatewayUrl}/send-message", [
                'phone' => $phone,
                'message' => $message,
            ]);

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('WhatsAppGatewayService Error:', ['phone' => $phone, 'error' => $e->getMessage()]);

            return [
                'status' => false,
                'error' => 'Gagal terhubung ke Gateway: ' . $e->getMessage()
            ];
        }
    }
}
