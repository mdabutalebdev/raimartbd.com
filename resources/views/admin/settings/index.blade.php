<x-admin-layout title="Settings - Raimart Admin">
    <div>
        {{-- Header --}}
        <div class="flex items-center justify-between pb-6 border-b border-gray-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 font-sans">Settings</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Configure your store system, integrations, delivery rates, and communications.</p>
            </div>
            <a href="{{ route('home') }}" target="_blank" class="hidden sm:inline-flex items-center gap-2 rounded-xl bg-gray-100 px-4 py-2.5 text-xs font-semibold text-gray-700 hover:bg-gray-200 transition">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                View Site
            </a>
        </div>

        @if(session('status'))
            <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        {{-- Settings Cards Grid (Ariomart Style) --}}
        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @php
                $cards = [
                    [
                        'title' => 'Branding Settings',
                        'desc' => 'Logo, footer, colors & identity',
                        'icon' => 'fa-solid fa-palette',
                        'route' => 'admin.settings.general',
                        'color' => 'bg-emerald-50 text-emerald-600',
                    ],
                    [
                        'title' => 'Email SMTP',
                        'desc' => 'Mail server configuration & test',
                        'icon' => 'fa-regular fa-envelope',
                        'route' => 'admin.smtp.edit',
                        'color' => 'bg-teal-50 text-teal-600',
                    ],
                    [
                        'title' => 'Steadfast Configuration',
                        'desc' => 'Courier integration keys & tracking',
                        'icon' => 'fa-solid fa-truck-fast',
                        'route' => 'admin.steadfast.index',
                        'color' => 'bg-emerald-50 text-emerald-600',
                    ],
                    [
                        'title' => 'Delivery Settings',
                        'desc' => 'Shipping rates & free delivery',
                        'icon' => 'fa-solid fa-box',
                        'route' => 'admin.delivery.edit',
                        'color' => 'bg-green-50 text-green-600',
                    ],
                    [
                        'title' => 'Telegram Bot',
                        'desc' => 'Alerts & order notification bot',
                        'icon' => 'fa-solid fa-paper-plane',
                        'route' => 'admin.telegram.edit',
                        'color' => 'bg-sky-50 text-sky-600',
                    ],
                    [
                        'title' => 'Social Links',
                        'desc' => 'Facebook, Instagram & YouTube',
                        'icon' => 'fa-solid fa-share-nodes',
                        'route' => 'admin.social-links.edit',
                        'color' => 'bg-purple-50 text-purple-600',
                    ],
                    [
                        'title' => 'Quick Contact',
                        'desc' => 'Messenger, WhatsApp & Call widget',
                        'icon' => 'fa-solid fa-headset',
                        'route' => 'admin.quick-contact.edit',
                        'color' => 'bg-cyan-50 text-cyan-600',
                    ],
                    [
                        'title' => 'Custom Pages',
                        'desc' => 'Privacy, Terms & FAQ pages',
                        'icon' => 'fa-regular fa-file-lines',
                        'route' => 'admin.pages.index',
                        'color' => 'bg-indigo-50 text-indigo-600',
                    ],
                    [
                        'title' => 'About Page',
                        'desc' => 'Company story & mission',
                        'icon' => 'fa-solid fa-circle-info',
                        'route' => 'admin.about.edit',
                        'color' => 'bg-amber-50 text-amber-600',
                    ],
                    [
                        'title' => 'Contact Page',
                        'desc' => 'Contact form & branch info',
                        'icon' => 'fa-solid fa-address-book',
                        'route' => 'admin.contact-page.edit',
                        'color' => 'bg-blue-50 text-blue-600',
                    ],
                    [
                        'title' => 'Laravel Logs',
                        'desc' => 'View, edit, download & clear system logs',
                        'icon' => 'fa-solid fa-file-code',
                        'route' => 'admin.logs.index',
                        'color' => 'bg-rose-50 text-rose-600',
                    ],
                    [
                        'title' => 'Pixel Setup',
                        'desc' => 'Meta Pixel, CAPI, GTM & tracking setup',
                        'icon' => 'fa-brands fa-facebook',
                        'route' => 'admin.pixel.edit',
                        'color' => 'bg-blue-50 text-blue-600',
                    ],
                ];
            @endphp

            @foreach ($cards as $card)
                <a href="{{ route($card['route']) }}" wire:navigate
                    class="group relative flex flex-col items-center text-center rounded-2xl border border-gray-200/80 bg-white p-7 transition-all duration-200 hover:-translate-y-1 hover:border-emerald-400 hover:shadow-lg cursor-pointer">
                    <span class="flex h-14 w-14 items-center justify-center rounded-2xl {{ $card['color'] }} mb-4 transition-transform duration-200 group-hover:scale-110 shadow-2xs">
                        <i class="{{ $card['icon'] }} text-xl"></i>
                    </span>
                    <h3 class="font-bold text-gray-900 text-base group-hover:text-emerald-700 transition-colors">
                        {{ $card['title'] }}
                    </h3>
                    <p class="mt-1 text-xs text-gray-500 leading-relaxed">
                        {{ $card['desc'] }}
                    </p>
                </a>
            @endforeach
        </div>
    </div>
</x-admin-layout>
