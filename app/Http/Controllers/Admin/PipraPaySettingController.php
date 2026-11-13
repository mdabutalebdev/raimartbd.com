<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Services\PipraPayService;
use Illuminate\Http\Request;

class PipraPaySettingController extends Controller
{
    public function edit(PipraPayService $pipraPay)
    {
        $settings = SiteSetting::getAll();

        return view('admin.settings.piprapay', [
            'settings' => $settings,
            'enabled' => $pipraPay->isEnabled(),
            'configured' => $pipraPay->isConfigured(),
            'baseUrl' => $pipraPay->getBaseUrl(),
            'apiKey' => $pipraPay->getApiKey(),
            'currency' => $pipraPay->getCurrency(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'piprapay_enabled' => ['nullable', 'in:0,1'],
            'piprapay_base_url' => ['nullable', 'string', 'max:255'],
            'piprapay_api_key' => ['nullable', 'string', 'max:255'],
            'piprapay_currency' => ['nullable', 'string', 'max:10'],
        ]);

        SiteSetting::set('piprapay_enabled', $data['piprapay_enabled'] ?? '0');
        SiteSetting::set('piprapay_base_url', !empty($data['piprapay_base_url']) ? rtrim(trim($data['piprapay_base_url']), '/') : null);
        SiteSetting::set('piprapay_api_key', !empty($data['piprapay_api_key']) ? trim($data['piprapay_api_key']) : null);
        SiteSetting::set('piprapay_currency', !empty($data['piprapay_currency']) ? strtoupper(trim($data['piprapay_currency'])) : 'BDT');

        return redirect()->route('admin.piprapay.edit')->with('status', 'PipraPay settings updated successfully.');
    }

    public function test(PipraPayService $pipraPay)
    {
        $result = $pipraPay->testConnection();

        return redirect()->route('admin.piprapay.edit')
            ->with($result['ok'] ? 'status' : 'error', $result['message']);
    }
}

