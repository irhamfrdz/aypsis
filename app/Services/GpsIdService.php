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

        if (Cache::has('gps_id_token')) {
            $cached = Cache::get('gps_id_token');
            return $cached === 'FAILED' ? null : $cached;
        }

        try {
            $response = Http::post("{$this->baseUrl}/login", [
                'username' => $this->username,
                'password' => $this->password,
            ]);

            $json = $response->json();

            if ($response->successful()) {
                $token = null;
                if (isset($json['token'])) {
                    $token = $json['token'];
                } elseif (isset($json['data']['token'])) {
                    $token = $json['data']['token'];
                } elseif (isset($json['message']['data']['token'])) {
                    $token = $json['message']['data']['token'];
                }

                if ($token) {
                    // Cache selama 23 jam (23 * 3600 detik = 82800)
                    Cache::put('gps_id_token', $token, 82800);
                    return $token;
                }
            }

            Log::error('GPS.id Login Failed: ' . $response->body());
            // Jika gagal (termasuk too many requests), blokir hit ke API selama 10 menit
            Cache::put('gps_id_token', 'FAILED', 600);
            return null;
        } catch (\Exception $e) {
            Log::error('GPS.id Login Error: ' . $e->getMessage());
            Cache::put('gps_id_token', 'FAILED', 600);
            return null;
        }
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

