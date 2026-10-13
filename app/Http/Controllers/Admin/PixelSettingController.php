<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PixelSettingController extends Controller
{
    private const KEYS = [
        // Meta (Facebook) Pixel
        'fb_pixel_enabled',
        'fb_pixel_id',
        'fb_capi_token',
        'fb_test_event_code',
        'pixel_track_pageview',
        'pixel_track_view_content',
        'pixel_track_add_to_cart',
        'pixel_track_initiate_checkout',
        'pixel_track_purchase',
        'pixel_advanced_matching',

        // Google Tag Manager & GA4
        'gtm_container_id',
        'ga4_measurement_id',

        // TikTok Pixel
        'tiktok_pixel_id',

        // Custom Code Injection
        'custom_head_scripts',
        'custom_body_scripts',
    ];

    public function edit()
    {
        $settings = SiteSetting::getAll();

        return view('admin.pixel.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'fb_pixel_enabled' => ['nullable', 'string', 'in:0,1'],
            'fb_pixel_id' => ['nullable', 'string', 'max:100'],
            'fb_capi_token' => ['nullable', 'string'],
            'fb_test_event_code' => ['nullable', 'string', 'max:100'],
            'pixel_track_pageview' => ['nullable', 'string', 'in:0,1'],
            'pixel_track_view_content' => ['nullable', 'string', 'in:0,1'],
            'pixel_track_add_to_cart' => ['nullable', 'string', 'in:0,1'],
            'pixel_track_initiate_checkout' => ['nullable', 'string', 'in:0,1'],
            'pixel_track_purchase' => ['nullable', 'string', 'in:0,1'],
            'pixel_advanced_matching' => ['nullable', 'string', 'in:0,1'],
            'gtm_container_id' => ['nullable', 'string', 'max:50'],
            'ga4_measurement_id' => ['nullable', 'string', 'max:50'],
            'tiktok_pixel_id' => ['nullable', 'string', 'max:100'],
            'custom_head_scripts' => ['nullable', 'string'],
            'custom_body_scripts' => ['nullable', 'string'],
        ]);

        foreach (self::KEYS as $key) {
            SiteSetting::set($key, $data[$key] ?? '0');
        }

        return redirect()->route('admin.pixel.edit')->with('status', 'Pixel & Tracking settings updated successfully.');
    }

    public function testCapi(Request $request)
    {
        $pixelId = trim((string) SiteSetting::get('fb_pixel_id'));
        $token = trim((string) SiteSetting::get('fb_capi_token'));
        $testCode = trim((string) ($request->input('test_event_code') ?: SiteSetting::get('fb_test_event_code', '')));

        if (empty($pixelId) || empty($token)) {
            return redirect()->back()->withErrors([
                'capi' => 'Both Meta Pixel ID and Conversions API Access Token are required to test CAPI.',
            ]);
        }

        try {
            $payload = [
                'data' => [
                    [
                        'event_name' => 'TestEvent',
                        'event_time' => time(),
                        'event_source_url' => url('/'),
                        'action_source' => 'website',
                        'user_data' => [
                            'client_ip_address' => $request->ip(),
                            'client_user_agent' => $request->userAgent(),
                        ],
                        'custom_data' => [
                            'currency' => 'BDT',
                            'value' => 100,
                            'status' => 'Raimart Admin Test Connection',
                        ],
                    ],
                ],
                'access_token' => $token,
            ];

            if (!empty($testCode)) {
                $payload['test_event_code'] = $testCode;
            }

            $response = Http::timeout(10)->post("https://graph.facebook.com/v19.0/{$pixelId}/events", $payload);

            if ($response->successful()) {
                $result = $response->json();
                $received = $result['events_received'] ?? 1;

                return redirect()->route('admin.pixel.edit')
                    ->with('status', "Meta CAPI test event sent successfully! Meta acknowledged {$received} event(s). Check your Events Manager 'Test Events' tab.");
            }

            $errData = $response->json();
            $errMsg = $errData['error']['message'] ?? $response->body();

            return redirect()->back()->withErrors([
                'capi' => "Meta API Error: {$errMsg}",
            ]);
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'capi' => "Connection Error: {$e->getMessage()}",
            ]);
        }
    }
}
