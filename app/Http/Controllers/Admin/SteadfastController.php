<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SteadfastController extends Controller
{
    public function index()
    {
        $steadfast = \App\Models\SteadfastIntegration::first();
        return view('admin.steadfast.index', compact('steadfast'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'api_key' => 'nullable|string',
            'secret_key' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $steadfast = \App\Models\SteadfastIntegration::first();
        if (!$steadfast) {
            $steadfast = new \App\Models\SteadfastIntegration();
        }

        $steadfast->api_key = $request->api_key;
        $steadfast->secret_key = $request->secret_key;
        $steadfast->is_active = $request->has('is_active');
        $steadfast->save();

        return redirect()->back()->with('status', 'Steadfast integration settings updated successfully!');
    }

    public function track(Request $request)
    {
        $code = $request->input('code');
        if (!$code) {
            return response()->json(['success' => false, 'message' => 'Tracking code required']);
        }

        $steadfast = \App\Models\SteadfastIntegration::first();
        if (!$steadfast || !$steadfast->is_active || !$steadfast->api_key || !$steadfast->secret_key) {
            return response()->json(['success' => false, 'message' => 'Steadfast API keys are not configured or active']);
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Api-Key' => $steadfast->api_key,
                'Secret-Key' => $steadfast->secret_key,
                'Content-Type' => 'application/json'
            ])->get("https://portal.steadfast.com.bd/api/v1/status_by_cid/{$code}");

            if ($response->successful()) {
                $data = $response->json();
                // Steadfast response format usually has 'delivery_status'
                if (isset($data['delivery_status'])) {
                    return response()->json([
                        'success' => true,
                        'tracking' => [
                            'status' => $data['delivery_status'],
                            'consignment_id' => $data['consignment_id'] ?? $code,
                            'recipient_name' => $data['recipient_name'] ?? 'N/A',
                            'recipient_phone' => $data['recipient_phone'] ?? 'N/A',
                            'cod_amount' => $data['cod_amount'] ?? 0,
                            'updated_at' => $data['updated_at'] ?? null,
                        ]
                    ]);
                }
            }

            return response()->json(['success' => false, 'message' => 'Invalid tracking code or data not found.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error connecting to Steadfast API.']);
        }
    }
}
