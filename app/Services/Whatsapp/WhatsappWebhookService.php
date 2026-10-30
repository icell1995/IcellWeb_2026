<?php

namespace App\Services\Whatsapp;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WhatsappWebhookService
{
    public function sendMessageTemplate($destinationPhoneNumber, $templateId, $props = [], $sandbox = 'false')
    {
        if (is_numeric($destinationPhoneNumber) && Str::startsWith($destinationPhoneNumber, '62')) {
            try {
                $appKey = env('WHATSAPP_BOT_APP_KEY');
                $authKey = env('WHATSAPP_BOT_AUTH_KEY');
                $to = $destinationPhoneNumber;
                $templateId = $templateId;
                $props = $props;
                $sandbox = $sandbox;

                $response = Http::post(env('WHATSAPP_BOT_API_URL') . 'create-message', [
                    'appkey' => $appKey,
                    'authkey' => $authKey,
                    'to' => $to,
                    'sandbox' => $sandbox,
                    'template_id' => $templateId,
                    'variables' => $props,
                ]);

                $json = $response->json();

                Log::info('WhatsApp OTP response :' . json_encode($json)); // logging json saja

                if (!$json || strtolower($json['message_status'] ?? '') !== 'success') {
                    Log::error('Saungwa OTP failed' . json_encode($json));
                    throw new \Exception('Failed to send message: ' . ($json['message'] ?? 'Unknown error'), $response->status());
                }

                return true;
            } catch (\Exception $e) {
                // return false;
                Log::error("WhatsApp API Error: " . $e->getMessage());
                throw $e;
            }
        }
        return false;
    }
}
