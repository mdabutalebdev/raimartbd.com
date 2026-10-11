<x-admin-layout title="{{ $customer->name }} - Customer Details">
    <div x-data="{ editModalOpen: false }">
        {{-- Back & Top Bar --}}
        <div>
            <a href="{{ route('admin.customers.index') }}" wire:navigate
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                <span>Back to Customers</span>
            </a>
            <div class="mt-2 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-wrap items-center gap-2.5">
                    <h1 class="font-serif text-2xl font-bold text-brand-navy">{{ $customer->name }}</h1>
                    <span class="font-mono text-sm font-semibold text-brand-navy/50">#{{ $customer->id }}</span>

                    @if ($customer->google_id)
                        <span class="inline-flex items-center gap-1 rounded-full border border-red-200 bg-red-50 px-2.5 py-0.5 text-xs font-semibold text-red-600">
                            <svg class="h-3 w-3" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                            </svg>
                            Google Account
                        </span>
                    @endif

                    @php $badge = $customer->role_badge; @endphp
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold border {{ $badge['class'] }}">
                        {{ $badge['label'] }}
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" @click="editModalOpen = true"
                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 cursor-pointer">
                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                        <span>Edit User</span>
                    </button>
                </div>
            </div>
        </div>

        @if (session('status'))
            <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        {{-- 4 Quick Stat Cards --}}
        <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div class="flex items-center gap-3.5 rounded-2xl border border-brand-navy/10 bg-white p-4 shadow-xs">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <i class="fa-solid fa-bag-shopping text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-brand-navy/50">TOTAL ORDERS</p>
                    <p class="text-lg font-bold text-brand-navy">{{ $orders->count() }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3.5 rounded-2xl border border-brand-navy/10 bg-white p-4 shadow-xs">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <i class="fa-solid fa-wallet text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-brand-navy/50">TOTAL SPENT</p>
                    <p class="text-lg font-bold text-brand-orange">৳{{ number_format($totalSpent) }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3.5 rounded-2xl border border-brand-navy/10 bg-white p-4 shadow-xs">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-teal-50 text-teal-600">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-brand-navy/50">DELIVERED ORDERS</p>
                    <p class="text-lg font-bold text-brand-navy">{{ $deliveredCount }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3.5 rounded-2xl border border-brand-navy/10 bg-white p-4 shadow-xs">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                    <i class="fa-solid fa-hourglass-half text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-brand-navy/50">IN-PROGRESS</p>
                    <p class="text-lg font-bold text-brand-navy">{{ $inProgressCount }}</p>
                </div>
            </div>
        </div>

        {{-- Main Content Grid --}}
        <div class="mt-6 grid gap-6 lg:grid-cols-[340px_1fr]">
            {{-- Profile Card --}}
            <div class="rounded-2xl border border-brand-navy/10 bg-white p-6 shadow-xs">
                <div class="flex flex-col items-center text-center">
                    <img src="{{ $customer->avatar_url }}" alt="{{ $customer->name }}"
                        class="h-24 w-24 rounded-full border-2 border-emerald-500/20 object-cover shadow-sm">
                    <h2 class="mt-3 text-lg font-bold text-brand-navy">{{ $customer->name }}</h2>
                    <p class="text-xs text-brand-navy/50">{{ $customer->email }}</p>

                    <div class="mt-2.5 flex items-center gap-2">
                        @if ($customer->email_verified_at)
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Verified
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Unverified
                            </span>
                        @endif
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-semibold border {{ $badge['class'] }}">
                            {{ $badge['label'] }}
                        </span>
                    </div>
                </div>

                <div class="mt-6 space-y-3.5 border-t border-brand-navy/10 pt-5 text-xs text-brand-navy/70">
                    <div class="flex items-center gap-3">
                        <i class="fa-regular fa-envelope w-4 text-center text-brand-navy/40"></i>
                        <span class="truncate">{{ $customer->email }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-phone w-4 text-center text-brand-navy/40"></i>
                        <span>{{ $customer->phone ?: 'Not provided' }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-shield-halved w-4 text-center text-brand-navy/40"></i>
                        <span>Login Method: <strong class="text-brand-navy">{{ $customer->google_id ? 'Google OAuth' : 'Email & Password' }}</strong></span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="fa-regular fa-calendar w-4 text-center text-brand-navy/40"></i>
                        <span>Joined: <strong class="text-brand-navy">{{ $customer->created_at?->format('M d, Y') }}</strong></span>
                    </div>
                    @if ($customer->address || $customer->city)
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-location-dot w-4 text-center text-brand-navy/40 mt-0.5"></i>
                            <span>{{ implode(', ', array_filter([$customer->address, $customer->city])) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Orders History --}}
            <div class="rounded-2xl border border-brand-navy/10 bg-white shadow-xs">
                <div class="border-b border-brand-navy/10 px-6 py-4">
                    <h3 class="font-bold text-brand-navy">Orders History</h3>
                    <p class="text-xs text-brand-navy/50">Showing all orders placed by this customer</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-brand-navy/10 bg-brand-bg/40 text-xs font-bold uppercase tracking-wider text-brand-navy/60">
                            <tr>
                                <th class="py-3 pl-6 pr-3">Order ID</th>
                                <th class="px-3 py-3">Date</th>
                                <th class="px-3 py-3">Total</th>
                                <th class="px-3 py-3">Payment</th>
                                <th class="px-3 py-3">Status</th>
                                <th class="py-3 pl-3 pr-6 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-navy/5 text-brand-navy">
                            @forelse ($orders as $order)
                                <tr class="transition hover:bg-brand-bg/30">
                                    <td class="py-3.5 pl-6 pr-3 font-mono text-xs font-bold text-emerald-600">
                                        #{{ $order->order_number }}
                                    </td>
                                    <td class="px-3 py-3.5 text-xs text-brand-navy/60">
                                        {{ $order->created_at?->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="px-3 py-3.5 font-semibold text-brand-navy">
                                        ৳{{ number_format($order->total) }}
                                    </td>
                                    <td class="px-3 py-3.5 text-xs capitalize text-brand-navy/70">
                                        {{ $order->payment_method ?? 'COD' }}
                                    </td>
                                    <td class="px-3 py-3.5">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium capitalize
                                            {{ $order->status === 'delivered' ? 'bg-emerald-50 text-emerald-700' : ($order->status === 'cancelled' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') }}">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 pl-3 pr-6 text-right">
                                        <a href="{{ route('admin.orders.show', $order) }}" wire:navigate
                                            class="inline-flex items-center gap-1 text-xs font-semibold text-brand-navy/60 hover:text-brand-orange">
                                            <i class="fa-regular fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-sm text-brand-navy/40">
                                        This customer hasn't placed any orders yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Edit Customer Profile Modal (Ariomart Style) --}}
        <div x-show="editModalOpen" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-brand-navy/60 p-4 backdrop-blur-xs"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <div @click.away="editModalOpen = false"
                class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl transition-all">
                <div class="flex items-center justify-between border-b border-brand-navy/10 pb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </span>
                        <div>
                            <h2 class="text-base font-bold text-brand-navy">Edit Customer Profile</h2>
                            <p class="text-xs text-brand-navy/50">Update customer data, role, and details</p>
                        </div>
                    </div>
                    <button type="button" @click="editModalOpen = false" class="text-brand-navy/40 hover:text-brand-navy cursor-pointer">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form action="{{ route('admin.customers.update', $customer) }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-brand-navy/70">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name', $customer->name) }}" required
                            class="mt-1 w-full rounded-xl border border-brand-navy/15 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-brand-navy/70">Email Address *</label>
                        <input type="email" name="email" value="{{ old('email', $customer->email) }}" required
                            class="mt-1 w-full rounded-xl border border-brand-navy/15 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-brand-navy/70">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" placeholder="e.g. 01712345678"
                            class="mt-1 w-full rounded-xl border border-brand-navy/15 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-brand-navy/70">Role / Account Type</label>
                        <select name="role" required
                            class="mt-1 w-full rounded-xl border border-brand-navy/15 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:outline-none font-medium">
                            <option value="customer" @selected(old('role', $customer->role) === 'customer') @disabled($customer->id === auth()->id())>
                                Customer {{ $customer->id === auth()->id() ? '(Cannot demote your own account)' : '' }}
                            </option>
                            <option value="staff" @selected(old('role', $customer->role) === 'staff')>Staff</option>
                            <option value="admin" @selected(old('role', $customer->role) === 'admin')>Admin</option>
                            <option value="super_admin" @selected(old('role', $customer->role) === 'super_admin')>Super Admin (Staff)</option>
                        </select>
                        <p class="mt-1 text-[11px] text-brand-navy/50">Changing to Staff, Admin, or Super Admin grants access to the admin dashboard.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-brand-navy/70">Profile Picture / Avatar</label>
                        <div class="mt-1.5 flex items-center gap-3">
                            <img src="{{ $customer->avatar_url }}" alt="Preview" class="h-10 w-10 rounded-full object-cover border border-brand-navy/10">
                            <input type="file" name="avatar" accept="image/*"
                                class="w-full text-xs text-brand-navy/60 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-navy/5 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-brand-navy hover:file:bg-brand-navy/10">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-brand-navy/70">New Password (Optional)</label>
                        <input type="password" name="password" minlength="6" placeholder="Leave empty to keep current password"
                            class="mt-1 w-full rounded-xl border border-brand-navy/15 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
                    </div>

                    <label class="flex items-center gap-2.5 pt-1 cursor-pointer">
                        <input type="checkbox" name="email_verified" value="1"
                            @checked(old('email_verified', (bool) $customer->email_verified_at))
                            class="h-4 w-4 rounded border-brand-navy/20 text-emerald-600 focus:ring-emerald-500">
                        <span class="text-xs font-medium text-brand-navy/70">Mark email as verified</span>
                    </label>

                    <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-brand-navy/10">
                        <button type="button" @click="editModalOpen = false"
                            class="rounded-xl border border-brand-navy/15 px-4 py-2.5 text-sm font-semibold text-brand-navy/70 hover:bg-brand-bg cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                            class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 cursor-pointer">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
