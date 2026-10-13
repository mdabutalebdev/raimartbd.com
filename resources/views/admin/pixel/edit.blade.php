<x-admin-layout title="Pixel & Tracking Setup - Raimart Admin">
    <div class="mb-4">
        <a href="{{ route('admin.settings.index') }}" wire:navigate class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-brand-orange transition cursor-pointer">
            <i class="fa-solid fa-arrow-left"></i> Back to Settings
        </a>
    </div>

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-gray-100">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="font-serif text-2xl font-bold text-gray-900">Pixel & Tracking Setup</h1>
                @php
                    $isPixelActive = ($settings['fb_pixel_enabled'] ?? '1') === '1' && !empty($settings['fb_pixel_id'] ?? '');
                @endphp
                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $isPixelActive ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                    <span class="h-1.5 w-1.5 rounded-full {{ $isPixelActive ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400' }}"></span>
                    {{ $isPixelActive ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Configure Meta (Facebook) Pixel, Server-side Conversions API (CAPI), Google Tag Manager, GA4, and TikTok Pixel.
            </p>
        </div>

        <a href="https://adsmanager.facebook.com/events_manager2" target="_blank"
            class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3.5 py-2 text-xs font-semibold text-gray-700 shadow-2xs hover:bg-gray-50 hover:text-blue-600 transition">
            <i class="fa-brands fa-facebook text-blue-600"></i>
            <span>Meta Events Manager</span>
            <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-gray-400"></i>
        </a>
    </div>

    @if (session('status'))
        <div class="mt-6 flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-sm font-semibold text-emerald-800 shadow-2xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                <span>{{ session('status') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="mt-6 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700 shadow-2xs">
            <div class="flex items-center gap-2 font-semibold">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>Please check the following errors:</span>
            </div>
            <ul class="mt-1.5 list-inside list-disc text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_360px]" x-data="{ showCapiToken: false }">
        {{-- Main Settings Form --}}
        <form action="{{ route('admin.pixel.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- 1. Meta (Facebook) Pixel & CAPI Card --}}
            <div class="rounded-2xl border border-gray-200/80 bg-white shadow-2xs overflow-hidden">
                <div class="flex items-center justify-between border-b border-gray-100 bg-gradient-to-r from-blue-50/60 to-transparent px-6 py-4">
                    <div class="flex items-center gap-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600 text-white shadow-2xs">
                            <i class="fa-brands fa-facebook-f text-base"></i>
                        </span>
                        <div>
                            <h2 class="font-bold text-gray-900 text-base">Meta (Facebook) Pixel & CAPI</h2>
                            <p class="text-xs text-gray-500">Browser & Server-side tracking for Meta Ads</p>
                        </div>
                    </div>

                    {{-- Enable/Disable Switch --}}
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="fb_pixel_enabled" value="0">
                        <input type="checkbox" name="fb_pixel_enabled" value="1"
                            @checked(old('fb_pixel_enabled', $settings['fb_pixel_enabled'] ?? '1') == '1')
                            class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Pixel ID --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-800 uppercase tracking-wide">
                            Meta Pixel ID <span class="text-rose-500">*</span>
                        </label>
                        <div class="mt-1.5 relative">
                            <input type="text" name="fb_pixel_id"
                                value="{{ old('fb_pixel_id', $settings['fb_pixel_id'] ?? '') }}"
                                placeholder="e.g. 123456789012345"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50/40 px-4 py-2.5 text-xs font-mono font-semibold text-gray-900 focus:border-brand-orange focus:bg-white focus:outline-none">
                        </div>
                        <p class="mt-1 text-[11px] text-gray-500">
                            Find your Pixel ID in Meta Events Manager &gt; Data Sources &gt; Settings.
                        </p>
                    </div>

                    {{-- Conversions API (CAPI) Access Token --}}
                    <div>
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-gray-800 uppercase tracking-wide">
                                Conversions API (CAPI) Access Token
                            </label>
                            <button type="button" @click="showCapiToken = !showCapiToken"
                                class="text-[11px] font-semibold text-blue-600 hover:text-blue-800 cursor-pointer">
                                <span x-text="showCapiToken ? 'Hide Token' : 'Show Token'"></span>
                            </button>
                        </div>
                        <div class="mt-1.5">
                            <textarea name="fb_capi_token" rows="3"
                                placeholder="e.g. EAAGm0PX4ZC8... (Generate in Events Manager > Settings > Conversions API)"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50/40 px-4 py-2.5 text-xs font-mono text-gray-900 focus:border-brand-orange focus:bg-white focus:outline-none"
                                :class="!showCapiToken && 'filter blur-[3px] hover:filter-none transition-all'"
                            >{{ old('fb_capi_token', $settings['fb_capi_token'] ?? '') }}</textarea>
                        </div>
                        <p class="mt-1 text-[11px] text-gray-500">
                            Required for server-side event tracking, bypassing iOS 14.5+ ad blockers and browser restrictions.
                        </p>
                    </div>

                    {{-- Test Event Code --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-800 uppercase tracking-wide">
                            Meta Test Event Code (Optional)
                        </label>
                        <input type="text" name="fb_test_event_code"
                            value="{{ old('fb_test_event_code', $settings['fb_test_event_code'] ?? '') }}"
                            placeholder="e.g. TEST12345 (Leave empty in live production)"
                            class="mt-1.5 w-full rounded-xl border border-gray-200 bg-gray-50/40 px-4 py-2 text-xs font-mono text-gray-900 focus:border-brand-orange focus:bg-white focus:outline-none">
                        <p class="mt-1 text-[11px] text-gray-500">
                            Used in Meta Events Manager &gt; Test Events tab to verify real-time events.
                        </p>
                    </div>

                    {{-- Standard E-Commerce Events --}}
                    <div class="border-t border-gray-100 pt-5">
                        <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wide mb-3">
                            Standard E-Commerce Events Tracking
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:bg-gray-50/80 cursor-pointer transition">
                                <input type="hidden" name="pixel_track_pageview" value="0">
                                <input type="checkbox" name="pixel_track_pageview" value="1"
                                    @checked(old('pixel_track_pageview', $settings['pixel_track_pageview'] ?? '1') == '1')
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <div>
                                    <span class="block text-xs font-bold text-gray-900">PageView</span>
                                    <span class="block text-[11px] text-gray-500">Fires on every storefront page</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:bg-gray-50/80 cursor-pointer transition">
                                <input type="hidden" name="pixel_track_view_content" value="0">
                                <input type="checkbox" name="pixel_track_view_content" value="1"
                                    @checked(old('pixel_track_view_content', $settings['pixel_track_view_content'] ?? '1') == '1')
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <div>
                                    <span class="block text-xs font-bold text-gray-900">ViewContent</span>
                                    <span class="block text-[11px] text-gray-500">Fires when viewing product details</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:bg-gray-50/80 cursor-pointer transition">
                                <input type="hidden" name="pixel_track_add_to_cart" value="0">
                                <input type="checkbox" name="pixel_track_add_to_cart" value="1"
                                    @checked(old('pixel_track_add_to_cart', $settings['pixel_track_add_to_cart'] ?? '1') == '1')
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <div>
                                    <span class="block text-xs font-bold text-gray-900">AddToCart</span>
                                    <span class="block text-[11px] text-gray-500">Fires on Add to Cart / Quick Buy</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:bg-gray-50/80 cursor-pointer transition">
                                <input type="hidden" name="pixel_track_initiate_checkout" value="0">
                                <input type="checkbox" name="pixel_track_initiate_checkout" value="1"
                                    @checked(old('pixel_track_initiate_checkout', $settings['pixel_track_initiate_checkout'] ?? '1') == '1')
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <div>
                                    <span class="block text-xs font-bold text-gray-900">InitiateCheckout</span>
                                    <span class="block text-[11px] text-gray-500">Fires when visiting checkout page</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:bg-gray-50/80 cursor-pointer transition">
                                <input type="hidden" name="pixel_track_purchase" value="0">
                                <input type="checkbox" name="pixel_track_purchase" value="1"
                                    @checked(old('pixel_track_purchase', $settings['pixel_track_purchase'] ?? '1') == '1')
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <div>
                                    <span class="block text-xs font-bold text-gray-900">Purchase</span>
                                    <span class="block text-[11px] text-gray-500">Fires on order confirmation</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:bg-gray-50/80 cursor-pointer transition">
                                <input type="hidden" name="pixel_advanced_matching" value="0">
                                <input type="checkbox" name="pixel_advanced_matching" value="1"
                                    @checked(old('pixel_advanced_matching', $settings['pixel_advanced_matching'] ?? '1') == '1')
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <div>
                                    <span class="block text-xs font-bold text-gray-900">Advanced Matching</span>
                                    <span class="block text-[11px] text-gray-500">Passes customer phone/email for ad attribution</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Google & TikTok Tracking Card --}}
            <div class="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-2xs space-y-5">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-500 text-white shadow-2xs">
                        <i class="fa-brands fa-google text-base"></i>
                    </span>
                    <div>
                        <h2 class="font-bold text-gray-900 text-base">Google &amp; TikTok Analytics</h2>
                        <p class="text-xs text-gray-500">Google Tag Manager, Google Analytics 4 &amp; TikTok Pixel</p>
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-bold text-gray-800 uppercase tracking-wide">
                            Google Tag Manager (GTM) ID
                        </label>
                        <input type="text" name="gtm_container_id"
                            value="{{ old('gtm_container_id', $settings['gtm_container_id'] ?? '') }}"
                            placeholder="e.g. GTM-XXXXXXX"
                            class="mt-1.5 w-full rounded-xl border border-gray-200 bg-gray-50/40 px-4 py-2.5 text-xs font-mono text-gray-900 focus:border-brand-orange focus:bg-white focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-800 uppercase tracking-wide">
                            Google Analytics 4 (GA4) ID
                        </label>
                        <input type="text" name="ga4_measurement_id"
                            value="{{ old('ga4_measurement_id', $settings['ga4_measurement_id'] ?? '') }}"
                            placeholder="e.g. G-XXXXXXXXXX"
                            class="mt-1.5 w-full rounded-xl border border-gray-200 bg-gray-50/40 px-4 py-2.5 text-xs font-mono text-gray-900 focus:border-brand-orange focus:bg-white focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 uppercase tracking-wide">
                        TikTok Pixel ID
                    </label>
                    <input type="text" name="tiktok_pixel_id"
                        value="{{ old('tiktok_pixel_id', $settings['tiktok_pixel_id'] ?? '') }}"
                        placeholder="e.g. C1234567890ABC"
                        class="mt-1.5 w-full rounded-xl border border-gray-200 bg-gray-50/40 px-4 py-2.5 text-xs font-mono text-gray-900 focus:border-brand-orange focus:bg-white focus:outline-none">
                </div>
            </div>

            {{-- 3. Custom Tracking Scripts (Advanced Injection) --}}
            <div class="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-2xs space-y-5">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-purple-600 text-white shadow-2xs">
                        <i class="fa-solid fa-code text-base"></i>
                    </span>
                    <div>
                        <h2 class="font-bold text-gray-900 text-base">Custom Tracking Scripts</h2>
                        <p class="text-xs text-gray-500">Inject custom verification tags, live chat widgets, or scripts</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 uppercase tracking-wide">
                        Custom &lt;head&gt; Scripts
                    </label>
                    <textarea name="custom_head_scripts" rows="3"
                        placeholder="<script>/* Custom tracking or meta verification tag */</script>"
                        class="mt-1.5 w-full rounded-xl border border-gray-200 bg-gray-50/40 px-4 py-2.5 text-xs font-mono text-gray-900 focus:border-brand-orange focus:bg-white focus:outline-none">{{ old('custom_head_scripts', $settings['custom_head_scripts'] ?? '') }}</textarea>
                    <p class="mt-1 text-[11px] text-gray-500">
                        Injected directly before &lt;/head&gt; on all storefront pages.
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-800 uppercase tracking-wide">
                        Custom &lt;body&gt; Scripts (Footer)
                    </label>
                    <textarea name="custom_body_scripts" rows="3"
                        placeholder="<script>/* Live chat widget or footer tracking code */</script>"
                        class="mt-1.5 w-full rounded-xl border border-gray-200 bg-gray-50/40 px-4 py-2.5 text-xs font-mono text-gray-900 focus:border-brand-orange focus:bg-white focus:outline-none">{{ old('custom_body_scripts', $settings['custom_body_scripts'] ?? '') }}</textarea>
                    <p class="mt-1 text-[11px] text-gray-500">
                        Injected directly before &lt;/body&gt; on all storefront pages.
                    </p>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="flex items-center justify-between rounded-2xl bg-white p-5 border border-gray-200/80 shadow-2xs">
                <p class="text-xs text-gray-500">
                    Changes will take effect immediately across all storefront pages.
                </p>
                <button type="submit"
                    class="rounded-xl bg-brand-orange px-6 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-brand-navy transition cursor-pointer">
                    Save Tracking Settings
                </button>
            </div>
        </form>

        {{-- Sidebar: Test CAPI & Diagnostics --}}
        <div class="space-y-6">
            {{-- Test Conversions API (CAPI) --}}
            <div class="rounded-2xl border border-blue-200/80 bg-white p-6 shadow-2xs">
                <div class="flex items-center gap-2.5 text-blue-600 font-bold text-sm">
                    <i class="fa-solid fa-bolt"></i>
                    <span>Test CAPI Server Connection</span>
                </div>
                <p class="mt-1.5 text-xs text-gray-500 leading-relaxed">
                    Send a test payload directly from your server to Meta to verify your Pixel ID and Access Token without creating an ad.
                </p>

                <form action="{{ route('admin.pixel.test-capi') }}" method="POST" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase">Test Event Code</label>
                        <input type="text" name="test_event_code"
                            value="{{ $settings['fb_test_event_code'] ?? '' }}"
                            placeholder="e.g. TEST12345"
                            class="mt-1 w-full rounded-xl border border-gray-200 bg-gray-50/50 px-3 py-2 text-xs font-mono text-gray-900 focus:border-blue-500 focus:outline-none">
                        <span class="text-[10px] text-gray-400">Get this from Meta Events Manager &gt; Test Events.</span>
                    </div>

                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-blue-700 transition cursor-pointer">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Send Test CAPI Event</span>
                    </button>
                </form>
            </div>

            {{-- Guide / Checklist --}}
            <div class="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-2xs space-y-4">
                <h3 class="font-bold text-gray-900 text-xs uppercase tracking-wider">
                    <i class="fa-solid fa-clipboard-check text-emerald-500 mr-1.5"></i> Setup Checklist
                </h3>
                <ul class="space-y-2.5 text-xs text-gray-600">
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-check text-emerald-500 mt-0.5 text-[11px]"></i>
                        <span>Meta Pixel ID entered and enabled</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-check text-emerald-500 mt-0.5 text-[11px]"></i>
                        <span>CAPI Access Token generated from Meta Events Manager</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-check text-emerald-500 mt-0.5 text-[11px]"></i>
                        <span>Use <a href="https://chrome.google.com/webstore/detail/meta-pixel-helper" target="_blank" class="text-blue-600 underline font-semibold">Meta Pixel Helper</a> Chrome extension to verify browser events</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-check text-emerald-500 mt-0.5 text-[11px]"></i>
                        <span>Check Events Manager &gt; Test Events tab for live logs</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</x-admin-layout>
