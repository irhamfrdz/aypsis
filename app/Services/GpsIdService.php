<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GpsIdService
{
    protected $baseUrl;
    protected $username;
    protected $password;

    public function __construct()
    {
        $this->baseUrl = env('GPS_ID_BASE_URL', 'https://portal.gps.id/backend/seen/public');
        $this->username = env('GPS_ID_USERNAME', '');
        $this->password = env('GPS_ID_PASSWORD', '');
    }

    /**
     * Dapatkan token otentikasi.
     * Token berlaku 24 jam, jadi kita cache selama 23 jam (1380 menit) untuk amannya.
     */
    protected function getToken()
    {
        if (empty($this->username) || empty($this->password)) {
            return null;
        }

        return Cache::remember('gps_id_token', 1380, function () {
            try {
                $response = Http::post("{$this->baseUrl}/login", [
                    'username' => $this->username,
                    'password' => $this->password,
                ]);

                if ($response->successful() && isset($response['token'])) {
                    return $response['token']; // Asumsi response memiliki key 'token'
                }
                
                // Coba struktur data lain jika format response berbeda
                if ($response->successful() && isset($response['data']['token'])) {
                    return $response['data']['token'];
                }

                Log::error('GPS.id Login Failed: ' . $response->body());
                return null;
            } catch (\Exception $e) {
                Log::error('GPS.id Login Error: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Get the latest location and status for a given IMEI.
     */
    public function getLatestLocation($imei)
    {
        $token = $this->getToken();

        if (empty($token)) {
            Log::warning('GPS.id Username/Password is not set in .env or login failed');
            return $this->mockLocationData($imei); // Fallback to mock data for testing UI
        }

        try {
            $date = date('Y-m-d');
            
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}", // Terkadang butuh prefix Bearer, jika tidak, hapus 'Bearer '
                'Accept' => 'application/json',
            ])->get("{$this->baseUrl}/command/log/{$imei}", [
                'date' => $date
            ]);

            // Jika token invalid (misal 401), hapus cache agar request selanjutnya minta token baru
            if ($response->status() === 401) {
                Cache::forget('gps_id_token');
            }

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("GPS.id API Error for IMEI {$imei}: " . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error("GPS.id Connection Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate mock data to test the Google Maps UI before real API is active.
     */
    private function mockLocationData($imei)
    {
        return [
            'success' => true,
            'data' => [
                'imei' => $imei,
                'lat' => -6.208800 + (rand(-50, 50) / 10000), // Jakarta
                'lng' => 106.845600 + (rand(-50, 50) / 10000), // Jakarta
                'speed' => rand(0, 60),
                'status' => 'Moving',
                'last_update' => now()->format('Y-m-d H:i:s')
            ]
        ];
    }
}

