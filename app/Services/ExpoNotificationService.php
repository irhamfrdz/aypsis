<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExpoNotificationService
{
    /**
     * Send push notification using Expo Push API
     *
     * @param string $to Expo Push Token (e.g. ExponentPushToken[xxx])
     * @param string $title Notification title
     * @param string $body Notification body content
     * @param array $data Optional custom data payload
     * @return bool
     */
    public static function send(string $to, string $title, string $body, array $data = []): bool
    {
        if (empty($to) || !str_starts_with($to, 'ExponentPushToken')) {
            Log::warning("Expo Notification: Invalid push token: {$to}");
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Accept-Encoding' => 'gzip, deflate',
                'Content-Type' => 'application/json',
            ])->post('https://exp.host/--/api/v2/push/send', [
                'to' => $to,
                'title' => $title,
                'body' => $body,
                'data' => $data,
                'sound' => 'default',
                'priority' => 'high',
            ]);

            if ($response->successful()) {
                $resData = $response->json();
                if (isset($resData['data']['status']) && $resData['data']['status'] === 'ok') {
                    return true;
                }
                Log::warning("Expo Notification Error response: " . json_encode($resData));
            } else {
                Log::error("Expo Notification HTTP Failed: {$response->status()} - {$response->body()}");
            }
        } catch (\Exception $e) {
            Log::error("Expo Notification Exception: " . $e->getMessage());
        }

        return false;
    }
}
