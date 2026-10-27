<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SteadfastIntegration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SteadfastController extends Controller
{
    private function getIntegration()
    {
        $steadfast = SteadfastIntegration::first();
        if (!$steadfast) {
            $steadfast = new SteadfastIntegration();
            $steadfast->base_url = 'https://portal.packzy.com/api/v1';
            $steadfast->is_active = true;
        }
        return $steadfast;
    }

    private function getBaseUrl(?SteadfastIntegration $steadfast): string
    {
        $url = $steadfast?->base_url ?: 'https://portal.packzy.com/api/v1';
        return rtrim($url, '/');
    }

    public function index()
    {
        $steadfast = SteadfastIntegration::first();
        if (!$steadfast) {
            $steadfast = new SteadfastIntegration([
                'base_url' => 'https://portal.packzy.com/api/v1',
                'is_active' => true,
            ]);
        }
        return view('admin.steadfast.index', compact('steadfast'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'api_key' => 'nullable|string|max:255',
            'secret_key' => 'nullable|string|max:255',
            'base_url' => 'nullable|url|max:255',
            'webhook_token' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $steadfast = SteadfastIntegration::first() ?? new SteadfastIntegration();

        $steadfast->api_key = trim($request->api_key ?? '');
        $steadfast->secret_key = trim($request->secret_key ?? '');
        $steadfast->base_url = trim($request->base_url ?: 'https://portal.packzy.com/api/v1');
        $steadfast->webhook_token = trim($request->webhook_token ?? '');
        $steadfast->is_active = $request->boolean('is_active', true);
        $steadfast->save();

        return redirect()->back()->with('status', 'Steadfast configuration saved successfully!');
    }

    public function ping(Request $request)
    {
        $steadfast = SteadfastIntegration::first();
        if (!$steadfast || !$steadfast->api_key || !$steadfast->secret_key) {
            return response()->json([
                'success' => false,
                'message' => 'API Key and Secret Key are not configured yet.',
            ]);
        }

        $baseUrl = $this->getBaseUrl($steadfast);
        $startTime = microtime(true);

        try {
            $response = Http::withHeaders([
                'Api-Key' => $steadfast->api_key,
                'Secret-Key' => $steadfast->secret_key,
                'Accept' => 'application/json',
            ])->timeout(10)->get("{$baseUrl}/get_balance");

            $latency = round((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'status' => 'Operational',
                    'latency' => $latency,
                    'message' => "Steadfast Server connected ({$latency}ms response time).",
                    'balance' => $data['current_balance'] ?? $data['balance'] ?? null,
                ]);
            }

            $errMsg = $response->json('message') ?? 'HTTP Error ' . $response->status();
            return response()->json([
                'success' => false,
                'status' => 'Authentication Error',
                'latency' => $latency,
                'message' => "Steadfast responded with error: {$errMsg}",
            ]);
        } catch (\Exception $e) {
            $latency = round((microtime(true) - $startTime) * 1000);
            return response()->json([
                'success' => false,
                'status' => 'Unreachable',
                'latency' => $latency,
                'message' => 'Failed to reach Steadfast server: ' . $e->getMessage(),
            ]);
        }
    }

    public function getBalance(Request $request)
    {
        $steadfast = SteadfastIntegration::first();
        if (!$steadfast || !$steadfast->api_key || !$steadfast->secret_key) {
            return response()->json([
                'success' => false,
                'message' => 'API Key and Secret Key are not configured yet.',
            ]);
        }

        $baseUrl = $this->getBaseUrl($steadfast);

        try {
            $response = Http::withHeaders([
                'Api-Key' => $steadfast->api_key,
                'Secret-Key' => $steadfast->secret_key,
                'Accept' => 'application/json',
            ])->timeout(10)->get("{$baseUrl}/get_balance");

            if ($response->successful()) {
                $data = $response->json();
                $balance = $data['current_balance'] ?? $data['balance'] ?? 0;
                return response()->json([
                    'success' => true,
                    'balance' => number_format((float)$balance, 2),
                    'raw_balance' => (float)$balance,
                    'message' => 'Balance fetched successfully',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $response->json('message') ?? 'Could not fetch balance. (HTTP ' . $response->status() . ')',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error connecting to Steadfast: ' . $e->getMessage(),
            ]);
        }
    }

    public function track(Request $request)
    {
        $code = trim($request->input('code'));
        if (!$code) {
            return response()->json(['success' => false, 'message' => 'Tracking code required']);
        }

        $steadfast = SteadfastIntegration::first();
        if (!$steadfast || !$steadfast->api_key || !$steadfast->secret_key) {
            return response()->json(['success' => false, 'message' => 'Steadfast API keys are not configured']);
        }

        $baseUrl = $this->getBaseUrl($steadfast);

        try {
            // First try status_by_trackingcode or status_by_cid
            $endpoint = is_numeric($code)
                ? "{$baseUrl}/status_by_cid/{$code}"
                : "{$baseUrl}/status_by_trackingcode/{$code}";

            $response = Http::withHeaders([
                'Api-Key' => $steadfast->api_key,
                'Secret-Key' => $steadfast->secret_key,
                'Accept' => 'application/json',
            ])->timeout(10)->get($endpoint);

            if ($response->successful()) {
                $data = $response->json();
                $deliveryStatus = $data['delivery_status'] ?? $data['status'] ?? null;
                if ($deliveryStatus) {
                    return response()->json([
                        'success' => true,
                        'tracking' => [
                            'status' => ucfirst(str_replace('_', ' ', $deliveryStatus)),
                            'consignment_id' => $data['consignment_id'] ?? $code,
                            'tracking_code' => $data['tracking_code'] ?? $code,
                            'recipient_name' => $data['recipient_name'] ?? 'N/A',
                            'recipient_phone' => $data['recipient_phone'] ?? 'N/A',
                            'cod_amount' => $data['cod_amount'] ?? 0,
                            'updated_at' => $data['updated_at'] ?? now()->toDateTimeString(),
                        ]
                    ]);
                }
            }

            // Fallback: try status_by_invoice
            $invoiceResponse = Http::withHeaders([
                'Api-Key' => $steadfast->api_key,
                'Secret-Key' => $steadfast->secret_key,
                'Accept' => 'application/json',
            ])->timeout(10)->get("{$baseUrl}/status_by_invoice/{$code}");

            if ($invoiceResponse->successful()) {
                $data = $invoiceResponse->json();
                $deliveryStatus = $data['delivery_status'] ?? $data['status'] ?? null;
                if ($deliveryStatus) {
                    return response()->json([
                        'success' => true,
                        'tracking' => [
                            'status' => ucfirst(str_replace('_', ' ', $deliveryStatus)),
                            'consignment_id' => $data['consignment_id'] ?? $code,
                            'tracking_code' => $data['tracking_code'] ?? $code,
                            'recipient_name' => $data['recipient_name'] ?? 'N/A',
                            'recipient_phone' => $data['recipient_phone'] ?? 'N/A',
                            'cod_amount' => $data['cod_amount'] ?? 0,
                            'updated_at' => $data['updated_at'] ?? now()->toDateTimeString(),
                        ]
                    ]);
                }
            }

            return response()->json(['success' => false, 'message' => 'No consignment found for code: ' . $code]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error querying Steadfast API: ' . $e->getMessage()]);
        }
    }

    /**
     * Book / Send order to Steadfast Courier
     */
    public function bookOrder(Request $request, Order $order)
    {
        $steadfast = SteadfastIntegration::first();
        if (!$steadfast || !$steadfast->api_key || !$steadfast->secret_key) {
            return response()->json([
                'success' => false,
                'message' => 'Steadfast API keys are not configured. Please configure them in Settings > Steadfast.',
            ], 422);
        }

        $baseUrl = $this->getBaseUrl($steadfast);

        // Calculate default COD Amount: if already paid, COD is 0; otherwise total order amount
        $defaultCod = $order->payment_status === 'paid' ? 0 : (float)$order->total;
        $codAmount = $request->has('cod_amount') ? (float)$request->input('cod_amount') : $defaultCod;
        $note = $request->input('note', $order->notes ?: 'Order #' . $order->order_number);

        // Prepare delivery address
        $fullAddress = trim(implode(', ', array_filter([
            $order->address,
            $order->thana,
            $order->city,
        ])));

        // Normalize phone number (strip whitespace, ensure proper format)
        $phone = preg_replace('/[^0-9]/', '', $order->phone);
        if (str_starts_with($phone, '880')) {
            $phone = substr($phone, 2);
        }

        $payload = [
            'invoice' => (string)$order->order_number,
            'recipient_name' => (string)$order->name,
            'recipient_phone' => (string)$phone,
            'recipient_address' => $fullAddress ?: 'Bangladesh',
            'cod_amount' => $codAmount,
            'note' => (string)$note,
        ];

        try {
            $response = Http::withHeaders([
                'Api-Key' => $steadfast->api_key,
                'Secret-Key' => $steadfast->secret_key,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(15)->post("{$baseUrl}/create_order", $payload);

            $data = $response->json();

            if ($response->successful() && isset($data['status']) && $data['status'] == 200) {
                $consignment = $data['consignment'] ?? [];

                $order->steadfast_consignment_id = $consignment['consignment_id'] ?? null;
                $order->steadfast_tracking_code = $consignment['tracking_code'] ?? null;
                $order->steadfast_status = $consignment['status'] ?? 'in_review';
                $order->steadfast_response = json_encode($data);
                $order->courier_name = 'Steadfast';
                $order->tracking_number = $consignment['tracking_code'] ?? (string)($consignment['consignment_id'] ?? '');

                if ($order->status === 'pending') {
                    $order->status = 'processing';
                }

                $order->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Order sent to Steadfast successfully!',
                    'consignment_id' => $order->steadfast_consignment_id,
                    'tracking_code' => $order->steadfast_tracking_code,
                    'status' => $order->steadfast_status,
                    'order' => $order,
                ]);
            }

            $errorMessage = $data['message'] ?? 'Failed to book consignment with Steadfast';
            if (isset($data['errors']) && is_array($data['errors'])) {
                $errorMessage .= ' (' . implode(', ', array_map(fn($k, $v) => "$k: " . (is_array($v) ? implode(' ', $v) : $v), array_keys($data['errors']), $data['errors'])) . ')';
            }

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'raw' => $data,
            ], 422);

        } catch (\Exception $e) {
            Log::error('Steadfast booking error: ' . $e->getMessage(), ['order' => $order->order_number]);
            return response()->json([
                'success' => false,
                'message' => 'Error communicating with Steadfast: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check real-time order status from Steadfast
     */
    public function orderStatus(Request $request, Order $order)
    {
        $steadfast = SteadfastIntegration::first();
        if (!$steadfast || !$steadfast->api_key || !$steadfast->secret_key) {
            return response()->json([
                'success' => false,
                'message' => 'Steadfast API keys are not configured.',
            ], 422);
        }

        $baseUrl = $this->getBaseUrl($steadfast);

        $code = $order->steadfast_consignment_id ?: $order->steadfast_tracking_code ?: $order->order_number;
        if (!$code) {
            return response()->json([
                'success' => false,
                'message' => 'This order has not been booked with Steadfast yet.',
            ], 422);
        }

        try {
            // Try by consignment ID first if available
            if ($order->steadfast_consignment_id) {
                $endpoint = "{$baseUrl}/status_by_cid/{$order->steadfast_consignment_id}";
            } elseif ($order->steadfast_tracking_code) {
                $endpoint = "{$baseUrl}/status_by_trackingcode/{$order->steadfast_tracking_code}";
            } else {
                $endpoint = "{$baseUrl}/status_by_invoice/{$order->order_number}";
            }

            $response = Http::withHeaders([
                'Api-Key' => $steadfast->api_key,
                'Secret-Key' => $steadfast->secret_key,
                'Accept' => 'application/json',
            ])->timeout(10)->get($endpoint);

            if ($response->successful()) {
                $data = $response->json();
                $deliveryStatus = $data['delivery_status'] ?? $data['status'] ?? null;

                if ($deliveryStatus) {
                    $order->steadfast_status = $deliveryStatus;

                    // Auto-sync order state if delivered or cancelled
                    if (strtolower($deliveryStatus) === 'delivered' && $order->status !== 'delivered') {
                        $order->status = 'delivered';
                        $order->payment_status = 'paid';
                    } elseif (strtolower($deliveryStatus) === 'cancelled' && $order->status !== 'cancelled') {
                        // don't force cancel without admin, but update steadfast_status
                    }

                    $order->save();

                    return response()->json([
                        'success' => true,
                        'status' => $deliveryStatus,
                        'status_label' => ucfirst(str_replace('_', ' ', $deliveryStatus)),
                        'consignment_id' => $order->steadfast_consignment_id,
                        'tracking_code' => $order->steadfast_tracking_code,
                        'message' => 'Steadfast status updated: ' . ucfirst(str_replace('_', ' ', $deliveryStatus)),
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Could not retrieve latest status from Steadfast.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check Customer Delivery Record / Fraud Check via Steadfast API
     */
    public function customerRecord(Request $request, Order $order)
    {
        $steadfast = SteadfastIntegration::first();
        if (!$steadfast || !$steadfast->api_key || !$steadfast->secret_key) {
            return response()->json([
                'success' => false,
                'message' => 'Steadfast API keys are not configured.',
            ], 422);
        }

        $phone = preg_replace('/[^0-9]/', '', $order->phone);
        if (str_starts_with($phone, '880')) {
            $phone = substr($phone, 2);
        }

        if (strlen($phone) < 10) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid phone number for fraud check: ' . $order->phone,
            ]);
        }

        $baseUrl = $this->getBaseUrl($steadfast);

        try {
            // Steadfast Fraud Check Score API (current official endpoint)
            $response = Http::withHeaders([
                'Api-Key' => $steadfast->api_key,
                'Secret-Key' => $steadfast->secret_key,
                'Accept' => 'application/json',
            ])->timeout(8)->get("{$baseUrl}/fraud_check/score/{$phone}");

            $result = null;
            if ($response->successful()) {
                $result = $response->json();
            } else {
                // Fallback to legacy endpoint if score endpoint fails
                $fallback = Http::withHeaders([
                    'Api-Key' => $steadfast->api_key,
                    'Secret-Key' => $steadfast->secret_key,
                    'Accept' => 'application/json',
                ])->timeout(8)->get("{$baseUrl}/fraud_check/{$phone}");

                if ($fallback->successful()) {
                    $result = $fallback->json();
                }
            }

            if ($result) {
                // Check if Steadfast provided delivery ratio
                $hasRatio = isset($result['delivery_ratio']) && $result['delivery_ratio'] !== null;
                $deliveryRatio = $hasRatio ? (float)$result['delivery_ratio'] : null;
                $cancellationRatio = isset($result['cancellation_ratio']) ? (float)$result['cancellation_ratio'] : ($result['return_ratio'] ?? null);

                // Volume band and range (new Steadfast API)
                $volumeBand = $result['volume_band'] ?? null;
                $volumeRange = $result['volume_range'] ?? null;

                // Total reports
                $reports = (int)($result['total_reports'] ?? (is_array($result['total_fraud_reports'] ?? null) ? count($result['total_fraud_reports']) : 0));

                // Exact counts (legacy endpoint or if explicitly returned)
                $delivered = $result['delivered_parcels'] ?? $result['total_delivered'] ?? $result['delivered_count'] ?? null;
                $cancelled = $result['cancelled_parcels'] ?? $result['total_cancelled'] ?? $result['cancelled_count'] ?? null;
                $total = $result['Total_parcels'] ?? $result['total_parcels'] ?? $result['total_orders'] ?? null;

                $hasExactCounts = ($delivered !== null && $cancelled !== null && ($delivered > 0 || $cancelled > 0 || $total > 0));

                if (!$hasRatio && $hasExactCounts && $total > 0) {
                    $deliveryRatio = round(($delivered / $total) * 100, 1);
                    $cancellationRatio = round(($cancelled / $total) * 100, 1);
                }

                // Determine risk level
                $risk = 'low';
                if ($volumeBand === 'none' || ($deliveryRatio === null && $reports === 0)) {
                    $risk = 'neutral';
                } elseif ($reports > 0 || ($deliveryRatio !== null && $deliveryRatio < 50)) {
                    $risk = 'high';
                } elseif ($deliveryRatio !== null && $deliveryRatio < 80) {
                    $risk = 'medium';
                }

                // Format volume display string
                $volumeDisplay = 'N/A';
                if ($volumeRange) {
                    $volumeDisplay = $volumeBand ? ucfirst($volumeBand) . " (~{$volumeRange})" : "~{$volumeRange} parcels";
                } elseif ($volumeBand && $volumeBand !== 'none') {
                    $volumeDisplay = ucfirst($volumeBand);
                } elseif ($hasExactCounts) {
                    $volumeDisplay = "{$total} parcels";
                } elseif ($volumeBand === 'none') {
                    $volumeDisplay = 'None (New)';
                }

                return response()->json([
                    'success' => true,
                    'phone' => $phone,
                    'has_exact_counts' => $hasExactCounts,
                    'total_parcels' => $total,
                    'delivered_parcels' => $delivered,
                    'cancelled_parcels' => $cancelled,
                    'delivery_ratio' => $deliveryRatio !== null ? $deliveryRatio . '%' : 'N/A',
                    'cancellation_ratio' => $cancellationRatio !== null ? $cancellationRatio . '%' : '0%',
                    'volume_display' => $volumeDisplay,
                    'reports_count' => $reports,
                    'risk_level' => $risk,
                    'raw' => $result,
                ]);
            }

            return response()->json([
                'success' => true,
                'phone' => $phone,
                'has_exact_counts' => false,
                'delivery_ratio' => 'New Customer',
                'cancellation_ratio' => '0%',
                'volume_display' => 'No History',
                'reports_count' => 0,
                'risk_level' => 'neutral',
                'message' => 'No delivery history found with Steadfast for this number (New buyer).',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error querying delivery record: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Steadfast Webhook Receiver
     */
    public function webhook(Request $request)
    {
        $steadfast = SteadfastIntegration::first();
        if ($steadfast && $steadfast->webhook_token) {
            $authHeader = $request->header('Authorization');
            $signature = $request->header('X-Signature');
            
            // Check Bearer token
            if ($authHeader && !str_contains($authHeader, $steadfast->webhook_token)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }

        $consignmentId = $request->input('consignment_id');
        $invoice = $request->input('invoice');
        $status = $request->input('status') ?? $request->input('delivery_status');

        Log::info('Steadfast Webhook Received', [
            'consignment_id' => $consignmentId,
            'invoice' => $invoice,
            'status' => $status,
            'payload' => $request->all(),
        ]);

        if ($invoice || $consignmentId) {
            $order = Order::where(function ($q) use ($invoice, $consignmentId) {
                if ($invoice) {
                    $q->where('order_number', $invoice);
                }
                if ($consignmentId) {
                    $q->orWhere('steadfast_consignment_id', (string)$consignmentId);
                }
            })->first();

            if ($order && $status) {
                $order->steadfast_status = $status;
                if (strtolower($status) === 'delivered') {
                    $order->status = 'delivered';
                    $order->payment_status = 'paid';
                }
                $order->save();
            }
        }

        return response()->json(['status' => 'success']);
    }
}
