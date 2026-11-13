<x-admin-layout title="PipraPay Gateway Settings - Raimart Admin">
    <div class="mb-4">
        <a href="{{ route('admin.settings.index') }}" wire:navigate class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-brand-orange transition cursor-pointer">
            <i class="fa-solid fa-arrow-left"></i> Back to Settings
        </a>
    </div>

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-gray-100">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="font-serif text-2xl font-bold text-gray-900">PipraPay Payment Gateway</h1>
                @php
                    $isLive = $enabled && $configured;
                @endphp
                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $isLive ? 'bg-emerald-100 text-emerald-800' : ($enabled ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-600') }}">
                    <span class="h-1.5 w-1.5 rounded-full {{ $isLive ? 'bg-emerald-500 animate-pulse' : ($enabled ? 'bg-amber-500' : 'bg-gray-400') }}"></span>
                    {{ $isLive ? 'Active & Ready' : ($enabled ? 'Enabled (Missing Key)' : 'Disabled') }}
                </span>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Configure PipraPay payment gateway API credentials, base URL, currency, and test the connection.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <button type="submit" form="piprapayForm"
                class="inline-flex items-center gap-2 rounded-xl bg-brand-orange px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-brand-navy transition cursor-pointer">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Save Settings</span>
            </button>
        </div>
    </div>

    {{-- Alerts --}}
    @if (session('status'))
        <div class="mt-6 flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-sm font-semibold text-emerald-800 shadow-2xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                <span>{{ session('status') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="mt-6 flex items-center justify-between rounded-xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-sm font-semibold text-rose-800 shadow-2xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="mt-6 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700 shadow-2xs">
            <div class="flex items-center gap-2 font-semibold">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>Please fix the following errors:</span>
            </div>
            <ul class="mt-1.5 list-inside list-disc text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_380px]">
        {{-- Left: Settings Form --}}
        <form id="piprapayForm" action="{{ route('admin.piprapay.update') }}" method="POST" class="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-sm space-y-6">
            @csrf
            @method('PUT')

            {{-- Enable/Disable Gateway Toggle --}}
            <div class="flex items-center justify-between pb-5 border-b border-gray-100">
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Enable PipraPay Gateway</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Allow customers to choose online payment (bKash, Nagad, Rocket, Cards) at checkout.</p>
                </div>
                <label class="relative inline-flex cursor-pointer items-center">
                    <input type="hidden" name="piprapay_enabled" value="0">
                    <input type="checkbox" name="piprapay_enabled" value="1" class="peer sr-only" @checked(old('piprapay_enabled', $enabled))>
                    <div class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:top-[2px] after:left-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-emerald-500 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none"></div>
                </label>
            </div>

            {{-- API Base URL --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                    API Base URL <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <input type="url" name="piprapay_base_url" id="piprapay_base_url"
                        value="{{ old('piprapay_base_url', $baseUrl) }}"
                        placeholder="https://sandbox.piprapay.com"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-gray-900 focus:border-brand-orange focus:bg-white focus:outline-none transition">
                </div>
                <div class="mt-2 flex flex-wrap gap-2 text-xs">
                    <span class="text-gray-500">Quick fill:</span>
                    <button type="button" onclick="document.getElementById('piprapay_base_url').value = 'https://sandbox.piprapay.com'"
                        class="rounded-lg bg-gray-100 px-2 py-0.5 font-medium text-gray-700 hover:bg-gray-200 transition cursor-pointer">
                        Sandbox (Testing)
                    </button>
                    <button type="button" onclick="document.getElementById('piprapay_base_url').value = 'https://piprapay.com'"
                        class="rounded-lg bg-gray-100 px-2 py-0.5 font-medium text-gray-700 hover:bg-gray-200 transition cursor-pointer">
                        Production (Live)
                    </button>
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    Do not add <code>/api/...</code> at the end. Enter only the base domain (e.g. <code>https://sandbox.piprapay.com</code>).
                </p>
            </div>

            {{-- API Key --}}
            <div x-data="{ showKey: false }">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                    PipraPay API Key <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <input :type="showKey ? 'text' : 'password'" name="piprapay_api_key"
                        value="{{ old('piprapay_api_key', $apiKey) }}"
                        placeholder="Enter your MHS-PIPRAPAY-API-KEY"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm font-mono text-gray-900 focus:border-brand-orange focus:bg-white focus:outline-none transition pr-10">
                    <button type="button" @click="showKey = !showKey"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 cursor-pointer">
                        <i :class="showKey ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" class="text-sm"></i>
                    </button>
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    Obtained from your PipraPay Merchant Dashboard (passed in headers as <code>MHS-PIPRAPAY-API-KEY</code>).
                </p>
            </div>

            {{-- Currency --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                    Currency
                </label>
                <input type="text" name="piprapay_currency"
                    value="{{ old('piprapay_currency', $currency) }}"
                    placeholder="BDT"
                    class="w-full max-w-[160px] rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-sm text-gray-900 uppercase focus:border-brand-orange focus:bg-white focus:outline-none transition">
                <p class="mt-1 text-xs text-gray-500">Default: <code>BDT</code> (supports BDT, USD, etc.).</p>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-brand-orange px-6 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-brand-navy transition cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Save PipraPay Settings</span>
                </button>
            </div>
        </form>

        {{-- Right: Test Connection & Webhook URLs --}}
        <div class="space-y-6">
            {{-- Test Connection Card --}}
            <div class="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-2.5 pb-4 border-b border-gray-100">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                        <i class="fa-solid fa-plug-circle-bolt text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Test Connection</h3>
                        <p class="text-xs text-gray-500">Verify your Base URL and API Key</p>
                    </div>
                </div>

                <div class="mt-4 space-y-3">
                    <p class="text-xs text-gray-600 leading-relaxed">
                        Click below to verify if the server can connect to your PipraPay API endpoint and authenticate with your API key.
                    </p>

                    <form action="{{ route('admin.piprapay.test') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gray-900 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-brand-orange transition cursor-pointer">
                            <i class="fa-solid fa-bolt"></i>
                            <span>Test PipraPay Connection</span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Webhook & Callback Info Card --}}
            <div class="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-2.5 pb-4 border-b border-gray-100">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <i class="fa-solid fa-link text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Webhook & Callback URL</h3>
                        <p class="text-xs text-gray-500">PipraPay Merchant Portal Settings</p>
                    </div>
                </div>

                <div class="mt-4 space-y-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-500 mb-1">Webhook URL (Instant Payment Notification):</label>
                        <div class="flex items-center gap-1.5 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs font-mono text-gray-700 select-all">
                            <span class="truncate">{{ route('checkout.webhook') }}</span>
                        </div>
                    </div>

                    <div class="rounded-xl border border-blue-100 bg-blue-50/60 p-3.5 text-xs text-blue-900 space-y-1.5">
                        <p class="font-semibold flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-info text-blue-600"></i>
                            <span>Important Notes</span>
                        </p>
                        <ul class="list-disc list-inside space-y-1 text-blue-800 text-[11px]">
                            <li>In PipraPay Domains whitelist, add both <b>www.raimartbd.com</b> and <b>raimartbd.com</b> to prevent "Return URL not whitelisted" errors.</li>
                            <li>If your Base URL returns <b>404</b>, the gateway cannot initiate checkouts. Ensure the domain is correct.</li>
                            <li>When PipraPay is disabled, customers will only see <b>Cash On Delivery</b> at checkout.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

