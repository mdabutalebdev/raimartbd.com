<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where(function ($q) {
            $q->where('is_admin', true)
              ->orWhereIn('role', ['staff', 'admin', 'super_admin']);
        });

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($sub) use ($s) {
                $sub->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        if ($request->filled('role') && in_array($request->input('role'), ['staff', 'admin', 'super_admin'])) {
            $query->where('role', $request->input('role'));
        }

        $staff = $query->latest()->paginate(20)->withQueryString();

        return view('admin.staff.index', compact('staff'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'in:staff,admin,super_admin'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
            'is_admin' => true,
            'password' => Hash::make($data['password']),
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.staff.index')->with('status', "Staff member {$user->name} created successfully.");
    }
}
