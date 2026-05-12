<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsappService
{
    public static function send($target, $message)
    {
        $token = setting('wa_token');
        $active = setting('wa_active') == '1';

        if (!$active || !$token || !$target) {
            return [
                'status' => false,
                'message' => 'WhatsApp notification is inactive or missing configuration.'
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62', // Default Indonesia
            ]);

            return $response->json();
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
