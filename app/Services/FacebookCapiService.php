<?php

namespace App\Services;

use App\Models\Order;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookCapiService
{
    /**
     * Determine if Meta CAPI is enabled and credentials are configured.
     */
    public function isConfigured(): bool
    {
        $enabled = (SiteSetting::get('fb_pixel_enabled', '1')) === '1';
        $pixelId = trim((string) SiteSetting::get('fb_pixel_id', ''));
        $token = trim((string) SiteSetting::get('fb_capi_token', ''));

        return $enabled && !empty($pixelId) && !empty($token);
    }

    /**
     * Send a server-side Purchase event to Meta Conversions API.
     */
    public function sendPurchase(Order $order, ?Request $request = null): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        $trackPurchase = (SiteSetting::get('pixel_track_purchase', '1')) === '1';
        if (! $trackPurchase) {
            return false;
        }

        $order->loadMissing('items.product');

        $request = $request ?? request();

        // Customer user data (hashed per Meta specifications)
        $userData = [
            'client_ip_address' => $request?->ip(),
            'client_user_agent' => $request?->userAgent(),
        ];

        // Advanced Matching hashes
        if (!empty($order->email)) {
            $userData['em'] = [hash('sha256', strtolower(trim($order->email)))];
        }

        if (!empty($order->phone)) {
            $cleanPhone = preg_replace('/\D+/', '', $order->phone);
            if (str_starts_with($cleanPhone, '0')) {
                $cleanPhone = '88' . $cleanPhone;
            } elseif (!str_starts_with($cleanPhone, '880') && strlen($cleanPhone) === 10) {
                $cleanPhone = '880' . $cleanPhone;
            }
            $userData['ph'] = [hash('sha256', $cleanPhone)];
        }

        if (!empty($order->name)) {
            $parts = explode(' ', trim($order->name));
            $userData['fn'] = [hash('sha256', strtolower($parts[0]))];
            if (count($parts) > 1) {
                $userData['ln'] = [hash('sha256', strtolower(end($parts)))];
            }
        }

        if (!empty($order->city)) {
            $userData['ct'] = [hash('sha256', strtolower(trim($order->city)))];
        }

        $userData['country'] = [hash('sha256', 'bd')];

        // Capture Meta click ID & browser ID cookies if present
        if ($fbp = $request?->cookie('_fbp')) {
            $userData['fbp'] = $fbp;
        }
        if ($fbc = $request?->cookie('_fbc')) {
            $userData['fbc'] = $fbc;
        }

        // Custom e-commerce data
        $contents = [];
        foreach ($order->items as $item) {
            $contents[] = [
                'id' => (string) $item->product_id,
                'quantity' => (int) $item->quantity,
                'item_price' => (float) $item->price,
            ];
        }

        $customData = [
            'currency' => 'BDT',
            'value' => round((float) $order->total, 2),
            'content_type' => 'product',
            'contents' => $contents,
            'num_items' => (int) $order->items->sum('quantity'),
            'order_id' => $order->order_number,
        ];

        return $this->sendEvent(
            eventName: 'Purchase',
            customData: $customData,
            userData: $userData,
            eventId: $order->order_number,
            eventSourceUrl: url('/checkout/confirmation/' . $order->order_number)
        );
    }

    /**
     * Send arbitrary event to Meta Conversions API Graph Endpoint.
     */
    public function sendEvent(
        string $eventName,
        array $customData,
        array $userData,
        ?string $eventId = null,
        ?string $eventSourceUrl = null
    ): bool {
        $pixelId = trim((string) SiteSetting::get('fb_pixel_id', ''));
        $token = trim((string) SiteSetting::get('fb_capi_token', ''));
        $testCode = trim((string) SiteSetting::get('fb_test_event_code', ''));

        if (empty($pixelId) || empty($token)) {
            return false;
        }

        $eventPayload = [
            'event_name' => $eventName,
            'event_time' => time(),
            'event_source_url' => $eventSourceUrl ?: url('/'),
            'action_source' => 'website',
            'user_data' => array_filter($userData, fn ($v) => $v !== null && $v !== []),
            'custom_data' => $customData,
        ];

        if (!empty($eventId)) {
            $eventPayload['event_id'] = (string) $eventId;
        }

        $body = [
            'data' => [$eventPayload],
            'access_token' => $token,
        ];

        if (!empty($testCode) && $testCode !== '0') {
            $body['test_event_code'] = $testCode;
        }

        try {
            $response = Http::timeout(5)->post("https://graph.facebook.com/v19.0/{$pixelId}/events", $body);

            if ($response->successful()) {
                Log::info("Meta CAPI event '{$eventName}' sent successfully", [
                    'event_id' => $eventId,
                    'result' => $response->json(),
                ]);
                return true;
            }

            Log::warning("Meta CAPI event '{$eventName}' failed", [
                'status' => $response->status(),
                'error' => $response->json(),
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::warning("Meta CAPI exception for '{$eventName}': " . $e->getMessage());
            return false;
        }
    }
}
