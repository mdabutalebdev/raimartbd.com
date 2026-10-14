<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $orders = $user->orders()->latest()->get();

        return view('user.dashboard', compact('user', 'orders'));
    }

    public function showOrder(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);

        $order->load(['items.product', 'payments']);

        return view('user.orders.show', compact('order'));
    }

    public function invoice(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);

        $order->load(['items.product', 'payments']);

        return view('admin.orders.invoice', [
            'order' => $order,
            'settings' => SiteSetting::getAll(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
        ]);

        $user->name = $validated['name'];
        if (array_key_exists('phone', $validated)) {
            $user->phone = $validated['phone'];
        }
        if (array_key_exists('city', $validated)) {
            $user->city = $validated['city'];
        }
        if (array_key_exists('address', $validated)) {
            $user->address = $validated['address'];
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $avatarPath = app(\App\Services\ImageOptimizer::class)->store($request->file('avatar'), 'avatars');
            $user->avatar = $avatarPath;
        }

        $user->save();

        return redirect()->route('user.dashboard', ['tab' => 'settings'])->with('success', 'Profile details updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        if ($user->isGoogleUser()) {
            return redirect()->route('user.dashboard')->withErrors([
                'error' => 'Password change is disabled for accounts signed in with Google.',
            ]);
        }

        $rules = [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        // If user already has a set password, verify current password
        if (!empty($user->password)) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        $validated = $request->validate($rules, [
            'current_password.current_password' => 'The current password you entered is incorrect.',
            'password.required' => 'Please enter a new password.',
            'password.min' => 'The new password must be at least 8 characters.',
            'password.confirmed' => 'The new password confirmation does not match.',
        ]);

        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->route('user.dashboard', ['tab' => 'password'])->with('success', 'Password updated successfully.');
    }
}
