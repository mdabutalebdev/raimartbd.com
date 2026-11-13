<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class PipraPayService
{
    private string $baseUrl;

    private string $apiKey;

    private string $currency;

    private bool $enabled;

    public function __construct()
    {
        $this->baseUrl = rtrim(SiteSetting::get('piprapay_base_url') ?: (config('services.piprapay.base_url') ?: 'https://sandbox.piprapay.com'), '/');
        $this->apiKey = (string) (SiteSetting::get('piprapay_api_key') ?: config('services.piprapay.api_key', ''));
        $this->currency = (string) (SiteSetting::get('piprapay_currency') ?: config('services.piprapay.currency', 'BDT'));
        $this->enabled = (bool) (SiteSetting::get('piprapay_enabled', '1') === '1');
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function isConfigured(): bool
    {
        return filled($this->apiKey) && filled($this->baseUrl);
    }

    public function isReady(): bool
    {
        return $this->isEnabled() && $this->isConfigured();
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Create a payment charge and return the response array from PipraPay.
     */
    public function createCharge(array $data): array
    {
        if (! $this->isConfigured()) {
            Log::warning('PipraPay createCharge aborted: API Key or Base URL is not configured.');
            return [
                'status' => false,
                'message' => 'PipraPay gateway is not configured properly in settings.',
            ];
        }

        $endpoint = "{$this->baseUrl}/api/checkout/redirect";

        try {
            Log::info('PipraPay initiating charge', [
                'endpoint' => $endpoint,
                'amount' => $data['amount'] ?? null,
                'currency' => $this->currency,
                'order' => $data['metadata']['order_number'] ?? null,
            ]);

            $response = Http::withoutVerifying()
                ->timeout(15)
                ->withHeaders([
                    'MHS-PIPRAPAY-API-KEY' => $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->post($endpoint, [
                    'full_name' => $data['full_name'],
                    'email_address' => $data['email_address'],
                    'mobile_number' => $data['mobile_number'],
                    'amount' => (string) $data['amount'],
                    'currency' => $this->currency,
                    'metadata' => json_encode($data['metadata'] ?? []),
                    'return_url' => $data['return_url'],
                    'webhook_url' => $data['webhook_url'],
                ]);

            if ($response->status() === 404) {
                Log::error("PipraPay endpoint 404 Not Found at {$endpoint}. Please check PIPRAPAY_BASE_URL.", [
                    'body' => substr($response->body(), 0, 300),
                ]);
                return [
                    'status' => false,
                    'message' => "Payment gateway URL is invalid (404 Not Found at {$endpoint}). Please check Base URL in settings.",
                ];
            }

            $json = $response->json();

            if (! $response->successful() || empty($json)) {
                Log::error('PipraPay createCharge HTTP error', [
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 500),
                ]);

                $errorMsg = $json['message'] ?? 'Gateway returned HTTP ' . $response->status();
                return [
                    'status' => false,
                    'message' => $errorMsg,
                ];
            }

            if (empty($json['pp_url'])) {
                Log::warning('PipraPay charge response missing pp_url', ['response' => $json]);
                return [
                    'status' => false,
                    'message' => $json['message'] ?? 'No redirect URL returned by gateway.',
                ];
            }

            return $json;
        } catch (Throwable $e) {
            Log::error('PipraPay createCharge connection exception', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verify a payment by its pp_id.
     */
    public function verifyPayment(string $ppId): array
    {
        $endpoint = "{$this->baseUrl}/api/verify-payment";

        try {
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->withHeaders([
                    'MHS-PIPRAPAY-API-KEY' => $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->post($endpoint, [
                    'pp_id' => $ppId,
                ]);

            $json = $response->json();

            if (! $response->successful() || empty($json)) {
                Log::error('PipraPay verifyPayment error', [
                    'pp_id' => $ppId,
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 500),
                ]);
                return ['status' => false, 'message' => 'Invalid response from payment gateway.'];
            }

            return $json;
        } catch (Throwable $e) {
            Log::error('PipraPay verifyPayment exception', [
                'pp_id' => $ppId,
                'error' => $e->getMessage(),
            ]);
            return ['status' => false, 'message' => 'Gateway connection error: ' . $e->getMessage()];
        }
    }

    /**
     * Refund a payment.
     */
    public function refundPayment(string $ppId): array
    {
        $endpoint = "{$this->baseUrl}/api/refund-payment";

        try {
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->withHeaders([
                    'MHS-PIPRAPAY-API-KEY' => $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->post($endpoint, [
                    'pp_id' => $ppId,
                ]);

            return $response->json() ?? ['status' => false, 'message' => 'Invalid response from payment gateway.'];
        } catch (Throwable $e) {
            Log::error('PipraPay refund exception', ['pp_id' => $ppId, 'error' => $e->getMessage()]);
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Test gateway connectivity and credentials from Admin Settings.
     */
    public function testConnection(): array
    {
        if (! filled($this->baseUrl)) {
            return ['ok' => false, 'message' => 'Base URL is empty. Please enter your PipraPay Base URL.'];
        }

        if (! filled($this->apiKey)) {
            return ['ok' => false, 'message' => 'API Key is empty. Please enter your PipraPay API Key.'];
        }

        $endpoint = "{$this->baseUrl}/api/verify-payment";

        try {
            $response = Http::withoutVerifying()
                ->timeout(10)
                ->withHeaders([
                    'MHS-PIPRAPAY-API-KEY' => $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->post($endpoint, [
                    'pp_id' => 'ping_test_' . time(),
                ]);

            if ($response->status() === 404) {
                return [
                    'ok' => false,
                    'message' => "404 Not Found at {$endpoint}. Your Base URL ('{$this->baseUrl}') is incorrect or does not have the PipraPay API routes.",
                ];
            }

            if ($response->status() === 401 || $response->status() === 403) {
                return [
                    'ok' => false,
                    'message' => "Authentication failed (HTTP {$response->status()}). Please check if your API Key is correct.",
                ];
            }

            $json = $response->json();

            // PipraPay typically returns JSON with status false and "Transaction not found" for a dummy pp_id,
            // which confirms the endpoint reached PipraPay and authenticated successfully!
            if (is_array($json)) {
                $msg = $json['message'] ?? 'Connected successfully';
                return [
                    'ok' => true,
                    'message' => "Gateway connected successfully! PipraPay responded: \"{$msg}\" (HTTP {$response->status()}).",
                ];
            }

            return [
                'ok' => false,
                'message' => "Received unexpected non-JSON response (HTTP {$response->status()}): " . substr($response->body(), 0, 150),
            ];
        } catch (Throwable $e) {
            return [
                'ok' => false,
                'message' => 'Connection test failed: ' . $e->getMessage(),
            ];
        }
    }
}
