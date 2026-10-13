@props(['title' => 'Admin - Raimart'])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    
    <!-- Favicon -->
    @include('partials.favicons')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    @livewireStyles
    
    <!-- Quill.js -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
</head>
<body class="bg-brand-bg font-sans text-brand-navy antialiased" x-data="{ mobileMenuOpen: false }">
        {{-- Mobile Backdrop --}}
        <div x-show="mobileMenuOpen" style="display: none;" x-transition.opacity class="fixed inset-0 bg-gray-900/60 z-40 lg:hidden" @click="mobileMenuOpen = false"></div>

        <aside :class="mobileMenuOpen ? '!flex fixed inset-y-0 left-0 z-50 shadow-2xl' : 'hidden'"
            class="w-64 shrink-0 flex-col bg-brand-navy text-white lg:!flex lg:fixed lg:inset-y-0 lg:left-0 lg:z-50 overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-5 border-b border-white/5">
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="flex items-center gap-2 font-serif text-xl font-bold">
                    <img src="{{ asset('images/logo-white.png') }}" alt="Raimart" class="h-7">
                </a>
                <button type="button" @click="mobileMenuOpen = false" class="lg:hidden text-white/60 hover:text-white cursor-pointer" aria-label="Close menu">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <nav x-data="{
                openOrders: {{ request()->routeIs('admin.orders.*') ? 'true' : 'false' }},
                openCategories: {{ request()->routeIs('admin.categories.*') ? 'true' : 'false' }},
                openProducts: {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.brands.*') || request()->routeIs('admin.top-selling.*') || request()->routeIs('admin.product-reviews.*') ? 'true' : 'false' }},
                openHomepage: {{ request()->routeIs('admin.banners.*') || request()->routeIs('admin.promo-banners.*') || request()->routeIs('admin.homepage.*') || request()->routeIs('admin.testimonials.*') || request()->routeIs('admin.quick-contact.*') ? 'true' : 'false' }}
            }" class="flex-1 space-y-1 px-3 pb-6">

                <div class="px-3 pt-2 pb-1 text-[11px] font-bold uppercase tracking-wider text-white/40">Management</div>

                {{-- Dashboard --}}
                <a href="{{ route('admin.dashboard') }}" wire:navigate
                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition cursor-pointer
                        {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white font-semibold' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
                    <i class="fa-solid fa-gauge w-4 text-center"></i>
                    <span>Overview</span>
                </a>

                {{-- Orders Accordion --}}
                <div>
                    <button type="button" @click="openOrders = !openOrders"
                        class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-sm font-medium transition cursor-pointer
                            {{ request()->routeIs('admin.orders.*') ? 'text-white bg-white/5' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-bag-shopping w-4 text-center"></i>
                            <span>Orders</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[11px] transition-transform duration-200" :class="openOrders ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openOrders" x-cloak class="mt-1 space-y-1 pl-9 pr-1">
                        <a href="{{ route('admin.orders.index') }}" wire:navigate
                            class="block rounded-lg px-2.5 py-1.5 text-xs transition cursor-pointer
                                {{ request()->routeIs('admin.orders.index') ? 'text-brand-orange font-bold bg-white/5' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                            All Orders
                        </a>
                        <a href="{{ route('admin.orders.create') }}" wire:navigate
                            class="block rounded-lg px-2.5 py-1.5 text-xs transition cursor-pointer
                                {{ request()->routeIs('admin.orders.create') ? 'text-brand-orange font-bold bg-white/5' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                            Create Order
                        </a>
                    </div>
                </div>

                {{-- Categories Accordion --}}
                <div>
                    <button type="button" @click="openCategories = !openCategories"
                        class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-sm font-medium transition cursor-pointer
                            {{ request()->routeIs('admin.categories.*') ? 'text-white bg-white/5' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-list w-4 text-center"></i>
                            <span>Categories</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[11px] transition-transform duration-200" :class="openCategories ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openCategories" x-cloak class="mt-1 space-y-1 pl-9 pr-1">
                        <a href="{{ route('admin.categories.index') }}" wire:navigate
                            class="block rounded-lg px-2.5 py-1.5 text-xs transition cursor-pointer
                                {{ request()->routeIs('admin.categories.index') ? 'text-brand-orange font-bold bg-white/5' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                            All Categories
                        </a>
                        <a href="{{ route('admin.categories.create') }}" wire:navigate
                            class="block rounded-lg px-2.5 py-1.5 text-xs transition cursor-pointer
                                {{ request()->routeIs('admin.categories.create') ? 'text-brand-orange font-bold bg-white/5' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                            Create Category
                        </a>
                    </div>
                </div>

                {{-- Products Accordion --}}
                <div>
                    <button type="button" @click="openProducts = !openProducts"
                        class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-sm font-medium transition cursor-pointer
                            {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.brands.*') || request()->routeIs('admin.top-selling.*') || request()->routeIs('admin.product-reviews.*') ? 'text-white bg-white/5' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-box w-4 text-center"></i>
                            <span>Products</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[11px] transition-transform duration-200" :class="openProducts ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openProducts" x-cloak class="mt-1 space-y-1 pl-9 pr-1">
                        <a href="{{ route('admin.products.index') }}" wire:navigate
                            class="block rounded-lg px-2.5 py-1.5 text-xs transition cursor-pointer
                                {{ request()->routeIs('admin.products.index') ? 'text-brand-orange font-bold bg-white/5' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                            All Products
                        </a>
                        <a href="{{ route('admin.products.create') }}" wire:navigate
                            class="block rounded-lg px-2.5 py-1.5 text-xs transition cursor-pointer
                                {{ request()->routeIs('admin.products.create') ? 'text-brand-orange font-bold bg-white/5' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                            Create Product
                        </a>
                        <a href="{{ route('admin.brands.index') }}" wire:navigate
                            class="block rounded-lg px-2.5 py-1.5 text-xs transition cursor-pointer
                                {{ request()->routeIs('admin.brands.*') ? 'text-brand-orange font-bold bg-white/5' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                            Brands
                        </a>
                        <a href="{{ route('admin.top-selling.edit') }}" wire:navigate
                            class="block rounded-lg px-2.5 py-1.5 text-xs transition cursor-pointer
                                {{ request()->routeIs('admin.top-selling.*') ? 'text-brand-orange font-bold bg-white/5' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                            Top Selling
                        </a>
                        <a href="{{ route('admin.product-reviews.index') }}" wire:navigate
                            class="block rounded-lg px-2.5 py-1.5 text-xs transition cursor-pointer
                                {{ request()->routeIs('admin.product-reviews.*') ? 'text-brand-orange font-bold bg-white/5' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                            Product Reviews
                        </a>
                    </div>
                </div>

                {{-- Homepage Accordion --}}
                <div>
                    <button type="button" @click="openHomepage = !openHomepage"
                        class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-sm font-medium transition cursor-pointer
                            {{ request()->routeIs('admin.banners.*') || request()->routeIs('admin.promo-banners.*') || request()->routeIs('admin.homepage.*') || request()->routeIs('admin.testimonials.*') || request()->routeIs('admin.quick-contact.*') ? 'text-white bg-white/5' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-table-columns w-4 text-center"></i>
                            <span>Homepage</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[11px] transition-transform duration-200" :class="openHomepage ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openHomepage" x-cloak class="mt-1 space-y-1 pl-9 pr-1">
                        <a href="{{ route('admin.banners.index') }}" wire:navigate
                            class="block rounded-lg px-2.5 py-1.5 text-xs transition cursor-pointer
                                {{ request()->routeIs('admin.banners.*') ? 'text-brand-orange font-bold bg-white/5' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                            Hero Banner
                        </a>
                        <a href="{{ route('admin.promo-banners.index') }}" wire:navigate
                            class="block rounded-lg px-2.5 py-1.5 text-xs transition cursor-pointer
                                {{ request()->routeIs('admin.promo-banners.*') ? 'text-brand-orange font-bold bg-white/5' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                            Promo Banner
                        </a>
                        <a href="{{ route('admin.homepage.category-sections.index') }}" wire:navigate
                            class="block rounded-lg px-2.5 py-1.5 text-xs transition cursor-pointer
                                {{ request()->routeIs('admin.homepage.category-sections.*') ? 'text-brand-orange font-bold bg-white/5' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                            Category Sections
                        </a>
                        <a href="{{ route('admin.testimonials.index') }}" wire:navigate
                            class="block rounded-lg px-2.5 py-1.5 text-xs transition cursor-pointer
                                {{ request()->routeIs('admin.testimonials.*') ? 'text-brand-orange font-bold bg-white/5' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                            Customer Reviews
                        </a>
                        <a href="{{ route('admin.quick-contact.edit') }}" wire:navigate
                            class="block rounded-lg px-2.5 py-1.5 text-xs transition cursor-pointer
                                {{ request()->routeIs('admin.quick-contact.*') ? 'text-brand-orange font-bold bg-white/5' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                            Quick Contact Widget
                        </a>
                    </div>
                </div>

                {{-- Customers (Accordion) --}}
                <div x-data="{ open: {{ request()->routeIs('admin.customers.*') || request()->routeIs('admin.staff.*') ? 'true' : 'false' }} }">
                    <button type="button" @click="open = !open"
                        class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-sm font-medium transition cursor-pointer
                            {{ request()->routeIs('admin.customers.*') || request()->routeIs('admin.staff.*') ? 'bg-white/10 text-white font-semibold' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-users w-4 text-center"></i>
                            <span>Customers</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" class="mt-1 space-y-1 pl-9 pr-2">
                        <a href="{{ route('admin.customers.index') }}" wire:navigate
                            class="block rounded-lg px-2.5 py-1.5 text-xs transition cursor-pointer
                                {{ request()->routeIs('admin.customers.*') ? 'text-brand-orange font-bold bg-white/5' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                            All Customers
                        </a>
                        <a href="{{ route('admin.staff.index') }}" wire:navigate
                            class="block rounded-lg px-2.5 py-1.5 text-xs transition cursor-pointer
                                {{ request()->routeIs('admin.staff.*') ? 'text-brand-orange font-bold bg-white/5' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                            All Staff
                        </a>
                    </div>
                </div>

                {{-- Reports --}}
                <a href="{{ route('admin.reports.index') }}" wire:navigate
                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition cursor-pointer
                        {{ request()->routeIs('admin.reports.*') ? 'bg-white/10 text-white font-semibold' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
                    <i class="fa-solid fa-chart-line w-4 text-center"></i>
                    <span>Reports</span>
                </a>

                {{-- Coupons --}}
                <a href="{{ route('admin.coupons.index') }}" wire:navigate
                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition cursor-pointer
                        {{ request()->routeIs('admin.coupons.*') ? 'bg-white/10 text-white font-semibold' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
                    <i class="fa-solid fa-ticket w-4 text-center"></i>
                    <span>Coupons</span>
                </a>

                {{-- Messages --}}
                <a href="{{ route('admin.messages.index') }}" wire:navigate
                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition cursor-pointer
                        {{ request()->routeIs('admin.messages.*') ? 'bg-white/10 text-white font-semibold' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
                    <i class="fa-solid fa-envelope w-4 text-center"></i>
                    <span>Contact Messages</span>
                </a>

                {{-- Settings --}}
                <a href="{{ route('admin.settings.index') }}" wire:navigate
                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition cursor-pointer
                        {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.pixel.*') || request()->routeIs('admin.logs.*') || request()->routeIs('admin.steadfast.*') || request()->routeIs('admin.delivery.*') || request()->routeIs('admin.social-links.*') || request()->routeIs('admin.telegram.*') || request()->routeIs('admin.smtp.*') || request()->routeIs('admin.pages.*') || request()->routeIs('admin.about.*') || request()->routeIs('admin.contact-page.*') || request()->routeIs('admin.quick-contact.*') ? 'bg-emerald-600/30 text-emerald-400 font-bold border border-emerald-500/30' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
                    <i class="fa-solid fa-gear w-4 text-center"></i>
                    <span>Settings</span>
                </a>

            </nav>

            <form action="{{ route('logout') }}" method="POST" class="px-3 pb-6">
                @csrf
                <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-white/60 hover:bg-white/5 hover:text-white cursor-pointer">
                    <i class="fa-solid fa-arrow-right-from-bracket w-4 text-center"></i>
                    Logout
                </button>
            </form>
        </aside>

        <div class="flex-1 flex flex-col min-h-screen lg:ml-64">
            <header class="sticky top-0 z-40 flex items-center justify-between border-b border-gray-200 bg-white/90 backdrop-blur-sm px-6 py-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <button type="button" @click="mobileMenuOpen = true" class="lg:hidden flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100 text-gray-700 hover:text-brand-orange cursor-pointer" aria-label="Open sidebar">
                        <i class="fa-solid fa-bars text-base"></i>
                    </button>
                    <a href="{{ route('admin.dashboard') }}" wire:navigate class="lg:hidden flex items-center">
                        <img src="{{ asset('images/logo.png') }}" alt="Raimart" class="h-7">
                    </a>
                </div>
                
                <div class="flex items-center gap-4">
                    <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-2 rounded-lg bg-gray-100 px-4 py-2.5 text-sm font-semibold text-brand-navy hover:bg-gray-200 hover:text-brand-orange transition-colors">
                        <i class="fa-solid fa-globe"></i>
                        View Website
                    </a>

                    <form action="{{ route('logout') }}" method="POST" class="lg:hidden">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-gray-500 hover:text-red-500">Logout</button>
                    </form>
                </div>
            </header>

            <main class="p-6 lg:p-10">
                @if (session('status'))
                    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                        {{ session('error') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>

        @stack('scripts')
        @livewireScripts
</body>
</html>
