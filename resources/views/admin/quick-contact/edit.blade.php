<x-admin-layout title="Quick Contact Widget - Raimart Admin">
    <div class="mb-4">
        <a href="{{ route('admin.settings.index') }}" wire:navigate class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-brand-orange transition cursor-pointer">
            <i class="fa-solid fa-arrow-left"></i> Back to Settings
        </a>
    </div>
    <div class="max-w-5xl">
        {{-- Header --}}
        <div class="flex items-center justify-between pb-6 border-b border-gray-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 font-sans">Quick Contact Widget</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Manage the floating support bubble (Messenger, WhatsApp & Call) on your storefront.</p>
            </div>
            <a href="{{ route('home') }}" target="_blank" class="hidden sm:inline-flex items-center gap-2 rounded-xl bg-gray-100 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-200 transition">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                View Site
            </a>
        </div>

        @if (session('status'))
            <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-sm font-semibold text-emerald-800 flex items-center gap-3 shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Form Column --}}
            <div class="lg:col-span-2">
                <form action="{{ route('admin.quick-contact.update') }}" method="POST" class="space-y-6 rounded-2xl bg-white p-6 sm:p-7 shadow-sm ring-1 ring-brand-navy/5">
                    @csrf
                    @method('PUT')

                    {{-- Enable / Disable Widget Toggle --}}
                    <label class="flex items-center justify-between rounded-xl border border-brand-navy/10 bg-slate-50/60 p-4 cursor-pointer hover:bg-slate-50 transition">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-orange/10 text-brand-orange">
                                <i class="fa-solid fa-headset text-lg"></i>
                            </span>
                            <div>
                                <span class="block text-sm font-bold text-gray-900">Show Floating Contact Widget</span>
                                <span class="block text-xs text-gray-500">Enable or disable the floating button on the bottom-right corner</span>
                            </div>
                        </div>
                        <input type="hidden" name="quick_contact_enabled" value="0">
                        <input type="checkbox" name="quick_contact_enabled" value="1"
                            @checked(old('quick_contact_enabled', $settings['quick_contact_enabled'] ?? '1') == '1')
                            class="h-5 w-5 rounded border-brand-navy/30 text-brand-orange focus:ring-brand-orange cursor-pointer">
                    </label>

                    {{-- Messenger Field --}}
                    <div class="rounded-xl border border-brand-navy/10 p-5 bg-white space-y-2">
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#0084FF]/10 text-[#0084FF]">
                                <i class="fa-brands fa-facebook-messenger text-base"></i>
                            </span>
                            <div>
                                <label class="text-sm font-bold text-gray-900">Messenger Link</label>
                                <span class="text-[11px] text-gray-400 block">Direct chat with your Facebook page</span>
                            </div>
                        </div>
                        <input type="text" name="quick_messenger_url" 
                            value="{{ old('quick_messenger_url', $settings['quick_messenger_url'] ?? '') }}"
                            placeholder="https://m.me/yourpage or username"
                            class="w-full rounded-xl border border-brand-navy/15 px-4 py-2.5 text-sm focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">
                        <p class="text-[11px] text-gray-400">Example: <code class="bg-gray-100 px-1 py-0.5 rounded text-gray-600">https://m.me/raimartbd</code></p>
                    </div>

                    {{-- WhatsApp Field --}}
                    <div class="rounded-xl border border-brand-navy/10 p-5 bg-white space-y-2">
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#25D366]/10 text-[#25D366]">
                                <i class="fa-brands fa-whatsapp text-lg"></i>
                            </span>
                            <div>
                                <label class="text-sm font-bold text-gray-900">WhatsApp Number</label>
                                <span class="text-[11px] text-gray-400 block">For WhatsApp direct messaging</span>
                            </div>
                        </div>
                        <input type="text" name="quick_whatsapp_number" 
                            value="{{ old('quick_whatsapp_number', $settings['quick_whatsapp_number'] ?? '') }}"
                            placeholder="8801895288808 or 01895288808"
                            class="w-full rounded-xl border border-brand-navy/15 px-4 py-2.5 text-sm focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">
                        <p class="text-[11px] text-gray-400">Example: <code class="bg-gray-100 px-1 py-0.5 rounded text-gray-600">01895288808</code> (country code 88 is added automatically if needed)</p>
                    </div>

                    {{-- Phone / Call Field --}}
                    <div class="rounded-xl border border-brand-navy/10 p-5 bg-white space-y-2">
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-navy/10 text-brand-navy">
                                <i class="fa-solid fa-phone text-sm"></i>
                            </span>
                            <div>
                                <label class="text-sm font-bold text-gray-900">Phone Number (Call Us)</label>
                                <span class="text-[11px] text-gray-400 block">Instant call button for customers</span>
                            </div>
                        </div>
                        <input type="text" name="quick_phone_number" 
                            value="{{ old('quick_phone_number', $settings['quick_phone_number'] ?? '') }}"
                            placeholder="+8801895288808"
                            class="w-full rounded-xl border border-brand-navy/15 px-4 py-2.5 text-sm focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">
                        <p class="text-[11px] text-gray-400">Example: <code class="bg-gray-100 px-1 py-0.5 rounded text-gray-600">+8801895288808</code></p>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <p class="text-xs text-gray-400"><i class="fa-solid fa-circle-info mr-1"></i> Leave any field empty to hide that specific button.</p>
                        <button type="submit" class="rounded-xl bg-brand-orange px-6 py-2.5 text-sm font-bold text-white shadow-md shadow-orange-500/20 hover:bg-orange-600 hover:shadow-lg transition cursor-pointer">
                            <i class="fa-solid fa-check mr-1.5"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>

            {{-- Live Info / Preview Column --}}
            <div class="space-y-6">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-brand-navy/5">
                    <h2 class="text-base font-bold text-gray-900">Storefront Preview</h2>
                    <p class="mt-1 text-xs text-gray-500">When visitors click the floating headset button on your website, they see:</p>

                    <div class="mt-5 rounded-2xl bg-slate-900/95 p-6 flex flex-col items-center gap-3 shadow-inner">
                        {{-- 1. Messenger Circle --}}
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#0084FF] text-white shadow-lg">
                            <i class="fa-brands fa-facebook-messenger text-2xl"></i>
                        </div>
                        {{-- 2. WhatsApp Circle --}}
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#25D366] text-white shadow-lg">
                            <i class="fa-brands fa-whatsapp text-2xl"></i>
                        </div>
                        {{-- 3. Call Circle --}}
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-orange text-white shadow-lg">
                            <i class="fa-solid fa-phone text-lg"></i>
                        </div>
                        {{-- 4. Close Circle --}}
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#222222] text-white shadow-xl">
                            <i class="fa-solid fa-xmark text-2xl"></i>
                        </div>
                    </div>

                    <div class="mt-4 rounded-xl bg-amber-50 border border-amber-200/60 p-3.5 text-xs text-amber-800 space-y-1">
                        <div class="font-bold flex items-center gap-1.5"><i class="fa-solid fa-lightbulb"></i> How it works</div>
                        <p class="text-[11px] text-amber-700 leading-relaxed">
                            Normally, only 1 orange headset button is shown. Clicking it opens all 3 circular contact buttons with the Cross (X) at the bottom. Clicking the Cross (X) closes them back to the single button.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
