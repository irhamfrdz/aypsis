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
        $this->baseUrl = 'https://portal.gps.id/backend/seen/public';
        $this->username = 'alexindo';
        $this->password = 'Alexindo';
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

                $json = $response->json();

                if ($response->successful()) {
                    if (isset($json['token'])) {
                        return $json['token'];
                    }
                    if (isset($json['data']['token'])) {
                        return $json['data']['token'];
                    }
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
            return null;
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
}

