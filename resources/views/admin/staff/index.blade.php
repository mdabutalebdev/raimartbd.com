<x-admin-layout title="All Staff - Raimart Admin">
    <div x-data="{ addModalOpen: false }">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-serif text-2xl font-bold text-brand-navy">All Staff</h1>
                <p class="mt-1 text-sm text-brand-navy/60">Manage your staff members and their roles.</p>
            </div>
            <div>
                <button type="button" @click="addModalOpen = true"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 cursor-pointer">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Add Staff</span>
                </button>
            </div>
        </div>

        @if (session('status'))
            <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Filters & Search --}}
        <div class="mt-6 rounded-2xl border border-brand-navy/10 bg-white p-4 shadow-xs">
            <form action="{{ route('admin.staff.index') }}" method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-brand-navy/40"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by name, phone, email..."
                        class="w-full rounded-xl border border-brand-navy/15 bg-brand-bg/30 py-2 pl-10 pr-4 text-sm focus:border-brand-orange focus:bg-white focus:outline-none">
                </div>

                <div class="w-full sm:w-48">
                    <select name="role" onchange="this.form.submit()"
                        class="w-full rounded-xl border border-brand-navy/15 bg-brand-bg/30 py-2 px-3 text-sm focus:border-brand-orange focus:bg-white focus:outline-none">
                        <option value="">All Roles</option>
                        <option value="super_admin" @selected(request('role') === 'super_admin')>Super Admin</option>
                        <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                        <option value="staff" @selected(request('role') === 'staff')>Staff</option>
                    </select>
                </div>

                @if (request()->hasAny(['search', 'role']))
                    <a href="{{ route('admin.staff.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-brand-navy/15 px-3 py-2 text-sm font-medium text-brand-navy/70 hover:bg-brand-bg">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="mt-6 overflow-hidden rounded-2xl border border-brand-navy/10 bg-white shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-brand-navy/10 bg-brand-bg/60 text-xs font-bold uppercase tracking-wider text-brand-navy/60">
                        <tr>
                            <th class="py-3.5 pl-6 pr-3">ID</th>
                            <th class="px-3 py-3.5">Customer Info</th>
                            <th class="px-3 py-3.5">Phone</th>
                            <th class="px-3 py-3.5">Role</th>
                            <th class="px-3 py-3.5">Joined</th>
                            <th class="py-3.5 pl-3 pr-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-navy/5 text-brand-navy">
                        @forelse ($staff as $member)
                            <tr class="transition hover:bg-brand-bg/30">
                                <td class="py-4 pl-6 pr-3 font-mono text-xs font-semibold text-brand-navy/60">
                                    #{{ $member->id }}
                                </td>
                                <td class="px-3 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $member->avatar_url }}" alt="{{ $member->name }}"
                                            class="h-10 w-10 shrink-0 rounded-full object-cover border border-brand-navy/10">
                                        <div class="min-w-0">
                                            <a href="{{ route('admin.customers.show', $member) }}" wire:navigate
                                                class="font-semibold text-brand-navy hover:text-brand-orange truncate block">
                                                {{ $member->name }}
                                            </a>
                                            <span class="text-xs text-brand-navy/50 truncate block">{{ $member->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-4 text-xs font-medium text-brand-navy/70">
                                    {{ $member->phone ?: 'N/A' }}
                                </td>
                                <td class="px-3 py-4">
                                    @php $badge = $member->role_badge; @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold border {{ $badge['class'] }}">
                                        {{ $badge['label'] }}
                                    </span>
                                </td>
                                <td class="px-3 py-4 text-xs text-brand-navy/60">
                                    {{ $member->created_at?->format('m/d/Y') }}
                                </td>
                                <td class="py-4 pl-3 pr-6 text-right">
                                    <a href="{{ route('admin.customers.show', $member) }}" wire:navigate
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-brand-navy/15 text-brand-navy/60 transition hover:border-brand-orange hover:bg-brand-orange/5 hover:text-brand-orange"
                                        title="View Details">
                                        <i class="fa-regular fa-eye text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-sm text-brand-navy/40">
                                    No staff members found matching your criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($staff->hasPages())
                <div class="border-t border-brand-navy/10 px-6 py-4">
                    {{ $staff->links() }}
                </div>
            @endif
        </div>

        {{-- Add Staff Modal --}}
        <div x-show="addModalOpen" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-brand-navy/60 p-4 backdrop-blur-xs"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <div @click.away="addModalOpen = false"
                class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl transition-all">
                <div class="flex items-center justify-between border-b border-brand-navy/10 pb-4">
                    <div>
                        <h2 class="text-lg font-bold text-brand-navy">Add New Staff</h2>
                        <p class="text-xs text-brand-navy/50">Assign role and grant admin dashboard access</p>
                    </div>
                    <button type="button" @click="addModalOpen = false" class="text-brand-navy/40 hover:text-brand-navy cursor-pointer">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form action="{{ route('admin.staff.store') }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-brand-navy/70">Full Name *</label>
                        <input type="text" name="name" required placeholder="e.g. Yeasin Arafat"
                            class="mt-1 w-full rounded-xl border border-brand-navy/15 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-brand-navy/70">Email Address *</label>
                        <input type="email" name="email" required placeholder="staff@raimart.com"
                            class="mt-1 w-full rounded-xl border border-brand-navy/15 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-brand-navy/70">Phone Number</label>
                        <input type="text" name="phone" placeholder="017XXXXXXXX"
                            class="mt-1 w-full rounded-xl border border-brand-navy/15 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-brand-navy/70">Role / Account Type *</label>
                        <select name="role" required
                            class="mt-1 w-full rounded-xl border border-brand-navy/15 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
                            <option value="staff">Staff (Orders & Support)</option>
                            <option value="admin">Admin (Catalog, Orders & Reports)</option>
                            <option value="super_admin">Super Admin (Full Access)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-brand-navy/70">Password *</label>
                        <input type="password" name="password" required minlength="6" placeholder="At least 6 characters"
                            class="mt-1 w-full rounded-xl border border-brand-navy/15 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3 pt-3 border-t border-brand-navy/10">
                        <button type="button" @click="addModalOpen = false"
                            class="rounded-xl border border-brand-navy/15 px-4 py-2.5 text-sm font-semibold text-brand-navy/70 hover:bg-brand-bg cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                            class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 cursor-pointer">
                            Create Staff
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
