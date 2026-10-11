<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Support\Csv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = $this->filtered($request)
            ->paginate((int) $request->input('per_page', 20))
            ->withQueryString();

        $stats = [
            'total' => User::where('role', 'customer')->orWhere('is_admin', false)->count(),
            'with_orders' => Order::whereNotNull('user_id')->distinct('user_id')->count('user_id'),
            'guests' => Order::whereNull('user_id')->count(),
        ];

        return view('admin.customers.index', compact('customers', 'stats'));
    }

    public function show(User $customer)
    {
        $orders = $customer->orders()->with('items')->latest()->get();

        return view('admin.customers.show', [
            'customer' => $customer,
            'orders' => $orders,
            'totalSpent' => (float) $orders->where('payment_status', 'paid')->sum('total'),
            'deliveredCount' => $orders->where('status', 'delivered')->count(),
            'inProgressCount' => $orders->whereIn('status', ['pending', 'processing', 'shipped'])->count(),
        ]);
    }

    public function update(Request $request, User $customer)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($customer->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'in:customer,staff,admin,super_admin'],
            'password' => ['nullable', 'string', 'min:6'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'email_verified' => ['nullable', 'boolean'],
        ]);

        // Safeguard: Prevent logged in admin from demoting their own account to customer
        if ($customer->id === auth()->id() && $data['role'] === 'customer') {
            return back()->withErrors([
                'role' => 'You cannot change your own account role to Customer, as you would lose access to the admin dashboard.',
            ]);
        }

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
            'is_admin' => in_array($data['role'], ['staff', 'admin', 'super_admin']),
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        if ($request->hasFile('avatar')) {
            $updateData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->has('email_verified')) {
            $updateData['email_verified_at'] = $request->boolean('email_verified') 
                ? ($customer->email_verified_at ?: now()) 
                : null;
        }

        $customer->update($updateData);

        return redirect()->route('admin.customers.show', $customer)->with('status', 'User details updated successfully.');
    }

    public function export(Request $request)
    {
        $rows = $this->filtered($request)->get()->map(fn (User $u) => [
            $u->id,
            $u->name,
            $u->email,
            $u->phone,
            $u->address,
            $u->city,
            $u->orders_count,
            $u->orders_total,
            $u->created_at?->format('Y-m-d'),
        ]);

        return Csv::download(
            'customers-'.now()->format('Y-m-d'),
            ['ID', 'Name', 'Email', 'Phone', 'Address', 'City', 'Orders', 'Total Spent', 'Joined'],
            $rows,
        );
    }

    /** Customers with their order count and lifetime value attached. */
    private function filtered(Request $request)
    {
        return User::where('role', 'customer')
            ->withCount('orders')
            ->withSum(['orders as orders_total' => fn ($q) => $q->where('payment_status', 'paid')], 'total')
            ->when($request->search, fn ($q, $s) => $q->where(function ($sub) use ($s) {
                $sub->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            }))
            ->when($request->has_orders === 'yes', fn ($q) => $q->has('orders'))
            ->when($request->has_orders === 'no', fn ($q) => $q->doesntHave('orders'))
            ->when($request->sort === 'spent', fn ($q) => $q->orderByDesc('orders_total'))
            ->when($request->sort === 'orders', fn ($q) => $q->orderByDesc('orders_count'))
            ->when($request->sort === 'name', fn ($q) => $q->orderBy('name'))
            ->when(! in_array($request->sort, ['spent', 'orders', 'name']), fn ($q) => $q->latest());
    }
}
