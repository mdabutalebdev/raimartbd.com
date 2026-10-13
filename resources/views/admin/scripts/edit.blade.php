<x-admin-layout title="Site Scripts & Tracking - Raimart Admin">
    <div class="mb-4">
        <a href="{{ route('admin.settings.index') }}" wire:navigate class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-brand-orange transition cursor-pointer">
            <i class="fa-solid fa-arrow-left"></i> Back to Settings
        </a>
    </div>

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-gray-100">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="font-sans text-2xl font-bold text-gray-900">Site Scripts &amp; Tracking</h1>
                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-800">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Active
                </span>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Add Facebook Pixel, Google Analytics, Google Tag Manager, or any other custom scripts to your storefront.
            </p>
        </div>

        <a href="{{ route('home') }}" target="_blank"
            class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3.5 py-2 text-xs font-semibold text-gray-700 shadow-2xs hover:bg-gray-50 hover:text-emerald-600 transition">
            <i class="fa-solid fa-arrow-up-right-from-square text-xs text-gray-400"></i>
            <span>View Site</span>
        </a>
    </div>

    {{-- Status Alert --}}
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

    {{-- Form --}}
    <form action="{{ route('admin.scripts.update') }}" method="POST" class="mt-6 space-y-6">
        @csrf
        @method('PUT')

        {{-- 1. Header Scripts (<head>) --}}
        <div class="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-2xs space-y-4">
            <div class="flex items-start gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 font-mono font-bold text-sm shrink-0 shadow-2xs">
                    &lt;/&gt;
                </span>
                <div>
                    <h2 class="font-bold text-gray-900 text-base">Header Scripts (&lt;head&gt;)</h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Add Facebook Pixel, Google Analytics, Google Tag Manager, or any other scripts that belong in the <code class="bg-gray-100 text-gray-800 px-1.5 py-0.5 rounded text-[11px]">&lt;head&gt;</code> tag.
                    </p>
                </div>
            </div>

            <div class="pt-1">
                <textarea name="custom_head_scripts" rows="10" spellcheck="false"
                    placeholder="<!-- Paste your Google Analytics, Google Tag Manager, or Meta Pixel Code here -->&#10;<script>&#10;  window.dataLayer = window.dataLayer || [];&#10;  function gtag(){dataLayer.push(arguments);}&#10;  gtag('js', new Date());&#10;  gtag('config', 'G-XXXXXXXXXX');&#10;</script>"
                    class="w-full rounded-xl border border-gray-200 bg-gray-50/50 p-4 text-xs font-mono text-gray-900 placeholder-gray-400 focus:border-emerald-500 focus:bg-white focus:outline-none transition leading-relaxed resize-y shadow-inner">{{ old('custom_head_scripts', $settings['custom_head_scripts'] ?? '') }}</textarea>
                <p class="text-[11px] text-gray-400 mt-1">
                    Scripts placed here will be rendered right before the closing <code class="text-gray-600">&lt;/head&gt;</code> tag across all pages.
                </p>
            </div>
        </div>

        {{-- 2. Body Scripts (Footer / <body>) --}}
        <div class="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-2xs space-y-4">
            <div class="flex items-start gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-teal-50 text-teal-600 font-mono font-bold text-sm shrink-0 shadow-2xs">
                    [ ]
                </span>
                <div>
                    <h2 class="font-bold text-gray-900 text-base">Body Scripts (Footer / &lt;/body&gt;)</h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Add chat widgets (Crisp, Tawk.to, WhatsApp, Messenger), fallback tracking pixels, or other scripts that belong right before the closing <code class="bg-gray-100 text-gray-800 px-1.5 py-0.5 rounded text-[11px]">&lt;/body&gt;</code> tag.
                    </p>
                </div>
            </div>

            <div class="pt-1">
                <textarea name="custom_body_scripts" rows="8" spellcheck="false"
                    placeholder="<!-- Your Footer / Body Scripts Here -->&#10;<!-- e.g. Live chat widget or conversion tracking pixels -->"
                    class="w-full rounded-xl border border-gray-200 bg-gray-50/50 p-4 text-xs font-mono text-gray-900 placeholder-gray-400 focus:border-emerald-500 focus:bg-white focus:outline-none transition leading-relaxed resize-y shadow-inner">{{ old('custom_body_scripts', $settings['custom_body_scripts'] ?? '') }}</textarea>
                <p class="text-[11px] text-gray-400 mt-1">
                    Scripts placed here will be rendered at the very bottom right before <code class="text-gray-600">&lt;/body&gt;</code> across all pages.
                </p>
            </div>
        </div>

        {{-- Footer Save Button --}}
        <div class="flex items-center justify-between rounded-2xl bg-white p-5 border border-gray-200/80 shadow-2xs">
            <p class="text-xs text-gray-500 flex items-center gap-1.5">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                <span>Changes will take effect immediately across all storefront pages.</span>
            </p>
            <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-7 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 transition cursor-pointer">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Save Scripts</span>
            </button>
        </div>
    </form>
</x-admin-layout>
