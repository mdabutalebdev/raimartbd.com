<x-layout title="User Dashboard - Raimart">
    <div class="bg-[#F8F9FA] min-h-screen py-10">
        <div class="container mx-auto gb-container">
            
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-brand-navy">My Account</h1>
                <p class="text-gray-500 mt-2">Manage your profile, track orders, and update your settings.</p>
            </div>

            @if(session('success') || session('status'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl shadow-sm flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
                    <p class="text-green-800 font-medium">{{ session('success') ?: session('status') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation text-red-500 text-xl shrink-0"></i>
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation text-red-500 text-xl mt-0.5"></i>
                    <div class="text-red-800">
                        <ul class="list-disc pl-4 space-y-1 font-medium">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar -->
                <aside class="w-full lg:w-1/4">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden lg:sticky lg:top-24">
                        <!-- Profile Summary -->
                        <div class="p-6 border-b border-gray-100 text-center bg-gradient-to-b from-brand-orange/10 to-transparent">
                            <div class="relative w-24 h-24 mx-auto mb-4">
                                <img id="sidebar_avatar_img" src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full rounded-full object-cover border-4 border-white shadow-md" referrerpolicy="no-referrer">
                            </div>
                            <h2 class="text-xl font-bold text-brand-navy">{{ $user->name }}</h2>
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        </div>
                        
                        <!-- Navigation -->
                        <nav class="p-4 space-y-2">
                            <button onclick="switchTab('dashboard')" id="tab-btn-dashboard" class="w-full flex items-center gap-3 px-4 py-3 text-left rounded-xl transition-all duration-200 bg-brand-orange text-white font-medium shadow-md shadow-brand-orange/20 cursor-pointer">
                                <i class="fa-solid fa-gauge-high w-5 text-center"></i>
                                Dashboard
                            </button>
                            <button onclick="switchTab('orders')" id="tab-btn-orders" class="w-full flex items-center gap-3 px-4 py-3 text-left rounded-xl transition-all duration-200 text-brand-navy hover:bg-brand-bg hover:text-brand-orange font-medium cursor-pointer">
                                <i class="fa-solid fa-box w-5 text-center"></i>
                                My Orders
                            </button>
                            <button onclick="switchTab('settings')" id="tab-btn-settings" class="w-full flex items-center gap-3 px-4 py-3 text-left rounded-xl transition-all duration-200 text-brand-navy hover:bg-brand-bg hover:text-brand-orange font-medium cursor-pointer">
                                <i class="fa-solid fa-user-pen w-5 text-center"></i>
                                Profile Settings
                            </button>
                            @if(!$user->isGoogleUser())
                                <button onclick="switchTab('password')" id="tab-btn-password" class="w-full flex items-center gap-3 px-4 py-3 text-left rounded-xl transition-all duration-200 text-brand-navy hover:bg-brand-bg hover:text-brand-orange font-medium cursor-pointer">
                                    <i class="fa-solid fa-shield-halved w-5 text-center"></i>
                                    Change Password
                                </button>
                            @endif
                            
                            <hr class="my-4 border-gray-100">
                            
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-left rounded-xl transition-colors text-red-600 hover:bg-red-50 font-medium">
                                    <i class="fa-solid fa-arrow-right-from-bracket w-5 text-center"></i>
                                    Logout
                                </button>
                            </form>
                        </nav>
                    </div>
                </aside>

                <!-- Main Content -->
                <div class="w-full lg:w-3/4">
                    
                    <!-- DASHBOARD TAB -->
                    <div id="tab-dashboard" class="space-y-6 block animate-fade-in">
                        <!-- Overview Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5 hover:shadow-md transition">
                                <div class="w-14 h-14 rounded-full bg-brand-bg flex items-center justify-center text-brand-orange text-2xl shrink-0">
                                    <i class="fa-solid fa-basket-shopping"></i>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm font-medium">Total Orders</p>
                                    <h3 class="text-2xl font-bold text-brand-navy">{{ $orders->count() }}</h3>
                                </div>
                            </div>
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5 hover:shadow-md transition">
                                <div class="w-14 h-14 rounded-full bg-green-50 flex items-center justify-center text-green-500 text-2xl shrink-0">
                                    <i class="fa-solid fa-truck-fast"></i>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm font-medium">Delivered</p>
                                    <h3 class="text-2xl font-bold text-brand-navy">{{ $orders->where('status', 'delivered')->count() }}</h3>
                                </div>
                            </div>
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5 hover:shadow-md transition">
                                <div class="w-14 h-14 rounded-full bg-yellow-50 flex items-center justify-center text-yellow-500 text-2xl shrink-0">
                                    <i class="fa-solid fa-spinner"></i>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm font-medium">Processing</p>
                                    <h3 class="text-2xl font-bold text-brand-navy">{{ $orders->whereIn('status', ['pending', 'processing'])->count() }}</h3>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Orders Preview -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                                <h3 class="text-xl font-bold text-brand-navy">Recent Orders</h3>
                                <button onclick="switchTab('orders')" class="text-brand-orange hover:text-brand-navy text-sm font-medium transition flex items-center gap-1">
                                    View All <i class="fa-solid fa-angle-right"></i>
                                </button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-white text-gray-500 text-sm border-b border-gray-100">
                                            <th class="py-4 px-6 font-medium">Order ID</th>
                                            <th class="py-4 px-6 font-medium">Date</th>
                                            <th class="py-4 px-6 font-medium">Total</th>
                                            <th class="py-4 px-6 font-medium">Status</th>
                                            <th class="py-4 px-6 font-medium text-right">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($orders->take(3) as $order)
                                            <tr class="border-b border-gray-50 hover:bg-brand-bg/50 transition">
                                                <td class="py-4 px-6 font-semibold text-brand-navy">
                                                    <a href="{{ route('user.orders.show', $order) }}" class="hover:text-brand-orange transition">
                                                        #{{ $order->order_number }}
                                                    </a>
                                                </td>
                                                <td class="py-4 px-6">
                                                    <div class="text-sm font-medium text-brand-navy">{{ $order->created_at->format('M d, Y') }}</div>
                                                    <div class="text-xs text-gray-400 mt-0.5 flex items-center gap-1.5">
                                                        <i class="fa-regular fa-clock text-[10px] text-brand-orange"></i>
                                                        <span>{{ $order->created_at->format('h:i A') }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-4 px-6 font-medium text-brand-navy">৳{{ number_format($order->total, 2) }}</td>
                                                <td class="py-4 px-6">
                                                    @php
                                                        $statusClasses = [
                                                            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                            'processing' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                            'shipped' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                                            'delivered' => 'bg-green-100 text-green-800 border-green-200',
                                                            'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                                                        ];
                                                        $badgeClass = $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                                    @endphp
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold capitalize border {{ $badgeClass }}">
                                                        {{ $order->status }}
                                                    </span>
                                                </td>
                                                <td class="py-4 px-6 text-right">
                                                    <a href="{{ route('user.orders.show', $order) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-brand-orange hover:text-brand-navy transition">
                                                        View <i class="fa-solid fa-angle-right"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="py-12 px-6 text-center text-gray-500">
                                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400 text-2xl">
                                                        <i class="fa-solid fa-box-open"></i>
                                                    </div>
                                                    <p>You haven't placed any orders yet.</p>
                                                    <a href="{{ route('shop') }}" wire:navigate class="mt-4 inline-block bg-brand-navy text-white px-6 py-2 rounded-lg hover:bg-brand-orange transition shadow-sm font-medium">Start Shopping</a>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- ORDERS TAB -->
                    <div id="tab-orders" class="hidden animate-fade-in">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                                <h3 class="text-xl font-bold text-brand-navy">Order History</h3>
                                <p class="text-gray-500 text-sm mt-1">View and track all your orders in one place.</p>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-white text-gray-500 text-sm border-b border-gray-100">
                                            <th class="py-4 px-6 font-medium">Order ID</th>
                                            <th class="py-4 px-6 font-medium">Date</th>
                                            <th class="py-4 px-6 font-medium">Total</th>
                                            <th class="py-4 px-6 font-medium">Status</th>
                                            <th class="py-4 px-6 font-medium text-right">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($orders as $order)
                                            <tr class="border-b border-gray-50 hover:bg-brand-bg/50 transition group">
                                                <td class="py-4 px-6 font-semibold text-brand-navy">
                                                    <a href="{{ route('user.orders.show', $order) }}" class="hover:text-brand-orange transition">
                                                        #{{ $order->order_number }}
                                                    </a>
                                                </td>
                                                <td class="py-4 px-6">
                                                    <div class="text-sm font-medium text-brand-navy">{{ $order->created_at->format('M d, Y') }}</div>
                                                    <div class="text-xs text-gray-400 mt-0.5 flex items-center gap-1.5">
                                                        <i class="fa-regular fa-clock text-[10px] text-brand-orange"></i>
                                                        <span>{{ $order->created_at->format('h:i A') }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-4 px-6 font-medium text-brand-navy">৳{{ number_format($order->total, 2) }}</td>
                                                <td class="py-4 px-6">
                                                    @php
                                                        $badgeClass = $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                                    @endphp
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold capitalize border {{ $badgeClass }}">
                                                        {{ $order->status }}
                                                    </span>
                                                </td>
                                                <td class="py-4 px-6 text-right">
                                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                                        <a href="{{ route('user.orders.show', $order) }}" class="inline-flex items-center gap-1.5 bg-brand-navy text-white hover:bg-brand-orange px-3 py-1.5 rounded-lg text-xs font-semibold transition shadow-sm" title="View Order Details">
                                                            <i class="fa-solid fa-eye text-xs"></i> Details
                                                        </a>
                                                        <a href="{{ route('user.orders.invoice', $order) }}" target="_blank" class="inline-flex items-center gap-1.5 bg-white border border-gray-200 text-brand-navy hover:text-brand-orange hover:border-brand-orange px-3 py-1.5 rounded-lg text-xs font-semibold transition shadow-sm" title="View or Print Invoice">
                                                            <i class="fa-solid fa-file-invoice text-brand-orange text-xs"></i> Invoice
                                                        </a>
                                                        <a href="{{ route('track') }}?order_number={{ $order->order_number }}" wire:navigate class="inline-flex items-center gap-1.5 bg-white border border-brand-orange/40 text-brand-orange hover:bg-brand-orange hover:text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition shadow-sm" title="Track Shipment">
                                                            <i class="fa-solid fa-truck-fast text-xs"></i> Track
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="py-16 px-6 text-center text-gray-500">
                                                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300 text-4xl">
                                                        <i class="fa-solid fa-box-open"></i>
                                                    </div>
                                                    <p class="text-lg font-medium text-brand-navy mb-1">No Orders Found</p>
                                                    <p class="mb-5">Looks like you haven't made your first purchase yet.</p>
                                                    <a href="{{ route('shop') }}" wire:navigate class="inline-block bg-brand-orange text-white px-8 py-3 rounded-xl hover:bg-brand-navy transition shadow-md font-medium">Explore Products</a>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- SETTINGS TAB (Personal Details & Avatar) -->
                    <div id="tab-settings" class="hidden animate-fade-in">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                                <h3 class="text-xl font-bold text-brand-navy">Profile Settings</h3>
                                <p class="text-gray-500 text-sm mt-1">Manage your personal information and profile picture.</p>
                            </div>
                            
                            <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8">
                                @csrf
                                
                                <!-- Profile Picture Section with Live Interactive Preview -->
                                <div class="mb-8 p-5 sm:p-6 bg-gradient-to-r from-brand-orange/5 via-brand-bg/60 to-brand-orange/5 rounded-2xl border border-brand-orange/15 shadow-2xs">
                                    <div class="flex flex-col sm:flex-row items-center gap-6">
                                        {{-- Circular Avatar Preview with Camera Overlay --}}
                                        <div class="relative group cursor-pointer shrink-0" onclick="document.getElementById('avatar_input').click()" title="Click to choose a photo">
                                            <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-full overflow-hidden bg-white shadow-md ring-4 ring-brand-orange/30 group-hover:ring-brand-orange transition-all duration-300">
                                                <img id="avatar_preview" src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover" referrerpolicy="no-referrer">
                                            </div>
                                            <div class="absolute bottom-0 right-0 w-8 h-8 rounded-full bg-brand-orange text-white flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-200">
                                                <i class="fa-solid fa-camera text-xs"></i>
                                            </div>
                                        </div>

                                        {{-- Actions and Information --}}
                                        <div class="flex-1 text-center sm:text-left space-y-2.5">
                                            <div>
                                                <h4 class="text-base font-semibold text-brand-navy">Profile Photo</h4>
                                                <p class="text-xs text-brand-navy/60 mt-0.5">Click on the avatar or the button below to upload a new picture.</p>
                                            </div>

                                            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3">
                                                <input type="file" id="avatar_input" name="avatar" accept="image/jpeg,image/png,image/webp,image/jpg" class="hidden" onchange="previewAvatar(this)">
                                                
                                                <button type="button" onclick="document.getElementById('avatar_input').click()" class="inline-flex items-center gap-2 bg-brand-orange hover:bg-brand-orange/90 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-xs hover:shadow-md transition cursor-pointer">
                                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                                    Choose Photo
                                                </button>

                                                <button type="button" id="avatar_cancel_btn" onclick="resetAvatarPreview()" class="hidden inline-flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-3.5 py-2.5 rounded-xl transition cursor-pointer">
                                                    <i class="fa-solid fa-xmark"></i>
                                                    Cancel
                                                </button>
                                            </div>

                                            <div id="avatar_selected_badge" class="hidden items-center gap-2 px-3 py-1.5 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold rounded-lg inline-flex">
                                                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                                                <span id="avatar_selected_text">New photo chosen — click "Save Changes" to apply</span>
                                            </div>

                                            <p class="text-xs text-gray-400">Accepted formats: JPG, PNG, WEBP (Max 5MB). Recommended 1:1 square.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Personal Details Form -->
                                <div class="space-y-6">
                                    <h4 class="text-lg font-semibold text-brand-navy flex items-center gap-2 border-b border-gray-100 pb-3">
                                        <i class="fa-regular fa-id-card text-brand-orange"></i> Personal Details
                                    </h4>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="space-y-2">
                                            <label for="name" class="block text-sm font-semibold text-gray-700">Full Name <span class="text-red-500">*</span></label>
                                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-xl border-gray-200 focus:border-brand-orange focus:ring-brand-orange bg-gray-50 focus:bg-white transition-colors shadow-sm py-2.5 px-4" required>
                                        </div>

                                        <div class="space-y-2">
                                            <label for="email" class="block text-sm font-semibold text-gray-700 flex items-center justify-between">
                                                <span>Email Address</span>
                                                @if($user->isGoogleUser())
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                        <i class="fa-brands fa-google text-[10px]"></i> Google Account
                                                    </span>
                                                @else
                                                    <span class="text-xs text-gray-400 font-normal">Cannot be changed</span>
                                                @endif
                                            </label>
                                            <div class="relative">
                                                <input type="email" id="email" value="{{ $user->email }}" class="w-full rounded-xl border-gray-200 bg-gray-100 text-gray-500 cursor-not-allowed shadow-sm py-2.5 px-4 pl-10" readonly>
                                                <i class="fa-solid fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                            </div>
                                            @if($user->isGoogleUser())
                                                <p class="text-xs text-gray-400">Signed in via Google. Password change is managed through your Google account.</p>
                                            @endif
                                        </div>

                                        <div class="space-y-2">
                                            <label for="phone" class="block text-sm font-semibold text-gray-700">Phone Number</label>
                                            <div class="relative">
                                                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="e.g. 01700000000" class="w-full rounded-xl border-gray-200 focus:border-brand-orange focus:ring-brand-orange bg-gray-50 focus:bg-white transition-colors shadow-sm py-2.5 px-4 pl-10">
                                                <i class="fa-solid fa-phone absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                            </div>
                                        </div>

                                        <div class="space-y-2">
                                            <label for="city" class="block text-sm font-semibold text-gray-700">City / District</label>
                                            <div class="relative">
                                                <input type="text" id="city" name="city" value="{{ old('city', $user->city) }}" placeholder="e.g. Dhaka, Chattogram" class="w-full rounded-xl border-gray-200 focus:border-brand-orange focus:ring-brand-orange bg-gray-50 focus:bg-white transition-colors shadow-sm py-2.5 px-4 pl-10">
                                                <i class="fa-solid fa-location-dot absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                            </div>
                                        </div>

                                        <div class="space-y-2 md:col-span-2">
                                            <label for="address" class="block text-sm font-semibold text-gray-700">Delivery Address</label>
                                            <textarea id="address" name="address" rows="2" placeholder="House, Road, Area details..." class="w-full rounded-xl border-gray-200 focus:border-brand-orange focus:ring-brand-orange bg-gray-50 focus:bg-white transition-colors shadow-sm py-2.5 px-4">{{ old('address', $user->address) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end">
                                    <button type="submit" class="bg-brand-navy hover:bg-brand-orange text-white font-semibold py-3 px-8 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 flex items-center gap-2 cursor-pointer">
                                        <i class="fa-solid fa-cloud-arrow-up"></i> Save Profile Details
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if(!$user->isGoogleUser())
                    <!-- PASSWORD TAB (Dedicated Security & Password Update) -->
                    <div id="tab-password" class="hidden animate-fade-in">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                                <h3 class="text-xl font-bold text-brand-navy">Change Password</h3>
                                <p class="text-gray-500 text-sm mt-1">Update your password to keep your Raimart account safe.</p>
                            </div>
                            
                            <form action="{{ route('user.password.update') }}" method="POST" class="p-6 md:p-8">
                                @csrf

                                <div class="max-w-xl space-y-6">
                                    @if(!empty($user->password))
                                        <div class="space-y-2">
                                            <label for="current_password" class="block text-sm font-semibold text-gray-700">Current Password <span class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <input type="password" id="current_password" name="current_password" class="w-full rounded-xl border-gray-200 focus:border-brand-orange focus:ring-brand-orange bg-gray-50 focus:bg-white transition-colors shadow-sm py-2.5 px-4 pr-11" placeholder="Enter your current password" required>
                                                <button type="button" onclick="togglePasswordVisibility('current_password', this)" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-brand-orange focus:outline-none cursor-pointer transition-colors" aria-label="Toggle password visibility">
                                                    <i class="fa-solid fa-eye text-sm eye-open"></i>
                                                    <i class="fa-solid fa-eye-slash text-sm eye-closed" style="display: none;"></i>
                                                </button>
                                            </div>
                                            @error('current_password')
                                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    @endif

                                    <div class="space-y-2">
                                        <label for="new_password" class="block text-sm font-semibold text-gray-700">New Password <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <input type="password" id="new_password" name="password" class="w-full rounded-xl border-gray-200 focus:border-brand-orange focus:ring-brand-orange bg-gray-50 focus:bg-white transition-colors shadow-sm py-2.5 px-4 pr-11" placeholder="Minimum 8 characters" required>
                                            <button type="button" onclick="togglePasswordVisibility('new_password', this)" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-brand-orange focus:outline-none cursor-pointer transition-colors" aria-label="Toggle new password visibility">
                                                <i class="fa-solid fa-eye text-sm eye-open"></i>
                                                <i class="fa-solid fa-eye-slash text-sm eye-closed" style="display: none;"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700">Confirm New Password <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <input type="password" id="password_confirmation" name="password_confirmation" class="w-full rounded-xl border-gray-200 focus:border-brand-orange focus:ring-brand-orange bg-gray-50 focus:bg-white transition-colors shadow-sm py-2.5 px-4 pr-11" placeholder="Re-enter new password" required>
                                            <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-brand-orange focus:outline-none cursor-pointer transition-colors" aria-label="Toggle confirm password visibility">
                                                <i class="fa-solid fa-eye text-sm eye-open"></i>
                                                <i class="fa-solid fa-eye-slash text-sm eye-closed" style="display: none;"></i>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Security tips box --}}
                                    <div class="p-4 bg-blue-50/70 border border-blue-100 rounded-xl text-xs text-blue-800 space-y-1.5">
                                        <p class="font-semibold flex items-center gap-1.5">
                                            <i class="fa-solid fa-shield-halved text-blue-600"></i> Password Guidelines:
                                        </p>
                                        <ul class="list-disc pl-5 space-y-1 text-blue-700/90">
                                            <li>At least 8 characters in length</li>
                                            <li>Combine uppercase, lowercase, numbers and symbols for strong security</li>
                                        </ul>
                                    </div>

                                    <div class="pt-4">
                                        <button type="submit" class="bg-brand-navy hover:bg-brand-orange text-white font-semibold py-3 px-8 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 flex items-center gap-2 cursor-pointer">
                                            <i class="fa-solid fa-key"></i> Update Password
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .animate-fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <script>
        const originalAvatarUrl = @json($user->avatar_url);

        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size exceeds 5MB limit. Please choose an image smaller than 5MB.');
                    input.value = '';
                    return;
                }

                const objectUrl = URL.createObjectURL(file);
                
                const previewImg = document.getElementById('avatar_preview');
                if (previewImg) previewImg.src = objectUrl;

                const sidebarImg = document.getElementById('sidebar_avatar_img');
                if (sidebarImg) sidebarImg.src = objectUrl;

                const cancelBtn = document.getElementById('avatar_cancel_btn');
                if (cancelBtn) cancelBtn.classList.remove('hidden');

                const badge = document.getElementById('avatar_selected_badge');
                const badgeText = document.getElementById('avatar_selected_text');
                if (badge && badgeText) {
                    const sizeInMb = (file.size / (1024 * 1024)).toFixed(2);
                    badgeText.textContent = `${file.name} (${sizeInMb} MB) — Click "Save Profile Details" to apply`;
                    badge.classList.remove('hidden');
                    badge.classList.add('inline-flex');
                }
            }
        }

        function resetAvatarPreview() {
            const input = document.getElementById('avatar_input');
            if (input) input.value = '';

            const previewImg = document.getElementById('avatar_preview');
            if (previewImg) previewImg.src = originalAvatarUrl;

            const sidebarImg = document.getElementById('sidebar_avatar_img');
            if (sidebarImg) sidebarImg.src = originalAvatarUrl;

            const cancelBtn = document.getElementById('avatar_cancel_btn');
            if (cancelBtn) cancelBtn.classList.add('hidden');

            const badge = document.getElementById('avatar_selected_badge');
            if (badge) {
                badge.classList.add('hidden');
                badge.classList.remove('inline-flex');
            }
        }

        const canChangePassword = @json(!$user->isGoogleUser());

        function switchTab(tabId) {
            if (tabId === 'password' && !canChangePassword) {
                tabId = 'dashboard';
            }
            const tabs = ['dashboard', 'orders', 'settings'];
            if (canChangePassword) tabs.push('password');

            tabs.forEach(id => {
                const el = document.getElementById('tab-' + id);
                if (el) el.classList.add('hidden');
            });
            
            const targetEl = document.getElementById('tab-' + tabId);
            if (targetEl) targetEl.classList.remove('hidden');

            const inactiveClasses = 'w-full flex items-center gap-3 px-4 py-3 text-left rounded-xl transition-all duration-200 text-brand-navy hover:bg-brand-bg hover:text-brand-orange font-medium cursor-pointer'.split(' ');
            const activeClasses = 'w-full flex items-center gap-3 px-4 py-3 text-left rounded-xl transition-all duration-200 bg-brand-orange text-white font-medium shadow-md shadow-brand-orange/20 cursor-pointer'.split(' ');

            tabs.forEach(id => {
                const btn = document.getElementById('tab-btn-' + id);
                if (btn) {
                    btn.className = '';
                    btn.classList.add(...inactiveClasses);
                }
            });

            const activeBtn = document.getElementById('tab-btn-' + tabId);
            if (activeBtn) {
                activeBtn.className = '';
                activeBtn.classList.add(...activeClasses);
            }

            if (history.replaceState) {
                history.replaceState(null, null, '#' + tabId);
            }
        }

        function togglePasswordVisibility(inputId, button) {
            const input = document.getElementById(inputId) || button.closest('.relative')?.querySelector('input');
            if (!input) return;

            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';

            const openIcon = button.querySelector('.eye-open');
            const closedIcon = button.querySelector('.eye-closed');

            if (openIcon && closedIcon) {
                if (isPassword) {
                    openIcon.style.display = 'none';
                    closedIcon.style.display = '';
                    button.classList.add('text-brand-orange');
                    button.classList.remove('text-gray-400');
                } else {
                    openIcon.style.display = '';
                    closedIcon.style.display = 'none';
                    button.classList.remove('text-brand-orange');
                    button.classList.add('text-gray-400');
                }
            }
        }

        // Initialize active tab
        (() => {
            const defaultTab = @json(request('tab') ?: ($errors->has('current_password') || $errors->has('password') ? 'password' : ($errors->any() ? 'settings' : 'dashboard')));
            let tabToOpen = defaultTab;

            if (window.location.hash) {
                const hash = window.location.hash.substring(1);
                const allowedTabs = ['dashboard', 'orders', 'settings'];
                if (canChangePassword) allowedTabs.push('password');
                if (allowedTabs.includes(hash)) {
                    tabToOpen = hash;
                }
            }
            if (tabToOpen === 'password' && !canChangePassword) {
                tabToOpen = 'dashboard';
            }
            switchTab(tabToOpen);
        })();

        // Handle URL hashes on Livewire navigation
        window.addEventListener('livewire:navigated', () => {
            if (window.location.hash) {
                const tab = window.location.hash.substring(1);
                const allowedTabs = ['dashboard', 'orders', 'settings'];
                if (canChangePassword) allowedTabs.push('password');
                if (allowedTabs.includes(tab)) {
                    switchTab(tab);
                }
            }
        });
    </script>
</x-layout>
