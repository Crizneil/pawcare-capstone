<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send an SMS message using Semaphore API.
     *
     * @param string $number
     * @param string $message
     * @return bool
     */
    public static function send($number, $message)
    {
        $apiKey = env('SEMAPHORE_API_KEY');
        $senderName = env('SEMAPHORE_SENDER_NAME', 'PawCare');

        // Log the attempt with a very obvious prefix
        Log::debug("========================================");
        Log::debug("SMS_SIMULATION [TO: $number]");
        Log::debug("MESSAGE: $message");
        Log::debug("========================================");

        // If no API key is provided, we simulate a successful send in dev mode
        if (!$apiKey) {
            Log::warning("SEMAPHORE_API_KEY is missing. This is a SIMULATED SMS.");
            return true;
        }

        try {
            $response = Http::post('https://semaphore.co/api/v4/messages', [
                'apikey' => $apiKey,
                'number' => $number,
                'message' => $message,
                'sendername' => $senderName,
            ]);

            if ($response->successful()) {
                Log::info("SMS Sent successfully to $number.");
                return true;
            }

            Log::error("SMS Failed to $number. Response: " . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error("SMS Exception for $number: " . $e->getMessage());
            return false;
        }
    }
}
