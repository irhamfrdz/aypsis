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
                'offline' => true,
                'message' => 'Gateway server merespon dengan status HTTP ' . $response->status()
            ];
        } catch (\Throwable $e) {
            return [
                'status' => false,
                'isReady' => false,
                'connection' => 'offline',
                'offline' => true,
                'message' => 'Microservice WA Gateway belum aktif di port 3000: ' . $e->getMessage()
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

    /**
     * Dapatkan data QR Code dari Gateway
     */
    public function getQrData(): array
    {
        try {
            $response = Http::timeout(4)->get("{$this->gatewayUrl}/qr-data");
            if ($response->successful()) {
                $data = $response->json();
                $data['offline'] = false;
                return $data;
            }

            return [
                'status' => false,
                'isReady' => false,
                'offline' => true,
                'message' => 'Gagal mengambil QR code dari Gateway (HTTP ' . $response->status() . ')'
            ];
        } catch (\Throwable $e) {
            return [
                'status' => false,
                'isReady' => false,
                'offline' => true,
                'message' => 'Microservice WA Gateway belum aktif di server: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Putuskan sesi login di Gateway
     */
    public function logout(): array
    {
        try {
            $response = Http::timeout(5)->post("{$this->gatewayUrl}/logout");
            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => false,
                'error' => 'Gagal melakukan logout di Gateway'
            ];
        } catch (\Throwable $e) {
            return [
                'status' => false,
                'error' => 'Microservice WA Gateway belum aktif: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Reset sesi dan paksa buat QR code baru
     */
    public function reset(): array
    {
        try {
            $response = Http::timeout(5)->post("{$this->gatewayUrl}/reset");
            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => false,
                'error' => 'Gagal me-reset gateway'
            ];
        } catch (\Throwable $e) {
            return [
                'status' => false,
                'error' => 'Microservice WA Gateway belum aktif: ' . $e->getMessage()
            ];
        }
    }
}
