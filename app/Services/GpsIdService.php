<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GpsIdService
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        $this->baseUrl = env('GPS_ID_BASE_URL', 'https://portal.gps.id/backend/seen/public');
        $this->token = env('GPS_ID_TOKEN', '');
    }

    /**
     * Get the latest location and status for a given IMEI.
     * Based on the API reference provided.
     */
    public function getLatestLocation($imei)
    {
        if (empty($this->token)) {
            Log::warning('GPS.id Token is not set in .env');
            return $this->mockLocationData($imei); // Fallback to mock data for testing UI
        }

        try {
            // As per screenshot, there is a GET /command/log/{imei}
            // but usually to get latest location there's a dedicated endpoint like /get-data or similar
            // Assuming there's a generic endpoint to fetch current device state, or we fetch the log for today
            $date = date('Y-m-d');
            
            $response = Http::withHeaders([
                'Authorization' => $this->token,
                'Accept' => 'application/json',
            ])->get("{$this->baseUrl}/command/log/{$imei}", [
                'date' => $date
            ]);

            if ($response->successful()) {
                // The actual structure depends on GPS.id response format
                // This is a placeholder structure
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
        // Return a mock coordinate somewhere in Jakarta (or Batam, since they use SuratJalanBongkaranBatam)
        // Let's use a coordinate in Batam: 1.1301, 104.0529
        return [
            'success' => true,
            'data' => [
                'imei' => $imei,
                'lat' => 1.130100 + (rand(-50, 50) / 10000),
                'lng' => 104.052900 + (rand(-50, 50) / 10000),
                'speed' => rand(0, 60),
                'status' => 'Moving',
                'last_update' => now()->format('Y-m-d H:i:s')
            ]
        ];
    }
}
