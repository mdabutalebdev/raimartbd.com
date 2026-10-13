<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class CustomScriptController extends Controller
{
    public function edit()
    {
        $settings = SiteSetting::getAll();

        return view('admin.scripts.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'custom_head_scripts' => ['nullable', 'string'],
            'custom_body_scripts' => ['nullable', 'string'],
        ]);

        SiteSetting::set('custom_head_scripts', $data['custom_head_scripts'] ?? '');
        SiteSetting::set('custom_body_scripts', $data['custom_body_scripts'] ?? '');

        return redirect()->route('admin.scripts.edit')->with('status', 'Site scripts updated successfully.');
    }
}
