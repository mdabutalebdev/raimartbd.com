@php
    $qcEnabled = $siteSettings['quick_contact_enabled'] ?? '1';
    
    // Messenger URL
    $qcMessengerRaw = trim($siteSettings['quick_messenger_url'] ?? '');
    if (!array_key_exists('quick_messenger_url', $siteSettings) && empty($qcMessengerRaw)) {
        if (!empty($siteSettings['facebook_url'])) {
            $qcMessengerRaw = str_replace(['facebook.com/', 'fb.com/'], 'm.me/', $siteSettings['facebook_url']);
        } else {
            $qcMessengerRaw = 'https://m.me/raimartbd';
        }
    }
    
    if (!empty($qcMessengerRaw)) {
        if (!preg_match('/^https?:\/\//i', $qcMessengerRaw)) {
            $qcMessengerUrl = 'https://m.me/' . ltrim($qcMessengerRaw, '@/');
        } else {
            $qcMessengerUrl = $qcMessengerRaw;
        }
    } else {
        $qcMessengerUrl = '';
    }

    // WhatsApp Number
    if (!array_key_exists('quick_whatsapp_number', $siteSettings)) {
        $qcWhatsappRaw = trim($siteSettings['contact_phone'] ?? '01895288808');
    } else {
        $qcWhatsappRaw = trim($siteSettings['quick_whatsapp_number'] ?? '');
    }
    $qcWhatsappDigits = preg_replace('/\D+/', '', $qcWhatsappRaw);
    if (str_starts_with($qcWhatsappDigits, '01')) {
        $qcWhatsappDigits = '88' . $qcWhatsappDigits;
    }
    $qcWhatsappUrl = !empty($qcWhatsappDigits) ? "https://wa.me/{$qcWhatsappDigits}" : '';

    // Phone / Call Number
    if (!array_key_exists('quick_phone_number', $siteSettings)) {
        $qcPhone = trim($siteSettings['contact_phone'] ?? '01895288808');
    } else {
        $qcPhone = trim($siteSettings['quick_phone_number'] ?? '');
    }
@endphp

@if ($qcEnabled == '1')
{{-- Floating quick-contact widget --}}
<div x-data="{ open: false }" 
     @click.outside="open = false" 
     @keydown.escape.window="open = false"
     class="fixed bottom-20 right-4 sm:bottom-8 sm:right-8 z-50 flex flex-col items-center gap-3 select-none">
     
    {{-- Pop-out items (Messenger, WhatsApp, Call) --}}
    <div x-show="open" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="flex flex-col items-center gap-3">
         
        {{-- 1. Messenger Circle (Top) --}}
        @if (filled($qcMessengerUrl))
            <a href="{{ $qcMessengerUrl }}" target="_blank" rel="noopener noreferrer" 
               title="Messenger"
               aria-label="Chat on Messenger"
               class="flex h-13 w-13 sm:h-14 sm:w-14 items-center justify-center rounded-full bg-[#0084FF] text-white shadow-lg hover:scale-105 active:scale-95 transition-transform duration-200 cursor-pointer">
                <svg class="w-7 h-7 pointer-events-none" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.477 2 2 6.145 2 11.258c0 2.91 1.455 5.517 3.735 7.18V22l3.414-1.874c.907.251 1.868.388 2.851.388 5.523 0 10-4.145 10-9.256C22 6.145 17.523 2 12 2zm1.002 12.448l-2.56-2.73-5.002 2.73 5.498-5.836 2.624 2.73 4.938-2.73-5.498 5.836z"/>
                </svg>
            </a>
        @endif

        {{-- 2. WhatsApp Circle --}}
        @if (filled($qcWhatsappUrl))
            <a href="{{ $qcWhatsappUrl }}" target="_blank" rel="noopener noreferrer" 
               title="WhatsApp"
               aria-label="Chat on WhatsApp"
               class="flex h-13 w-13 sm:h-14 sm:w-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-lg hover:scale-105 active:scale-95 transition-transform duration-200 cursor-pointer">
                <svg class="w-7 h-7 pointer-events-none" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2m.01 1.67c4.54 0 8.24 3.7 8.24 8.24 0 2.2-.86 4.28-2.42 5.84-1.56 1.56-3.64 2.42-5.84 2.42-1.42 0-2.82-.37-4.04-1.08l-.29-.17-3.12.82.83-3.04-.19-.3a8.216 8.216 0 0 1-1.26-4.49c0-4.54 3.7-8.24 8.24-8.24m4.52 11.64c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.02-1.24-.74-.66-1.25-1.48-1.39-1.73-.14-.25-.02-.39.11-.51.11-.11.25-.29.37-.43.13-.14.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.13-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.87.85-.87 2.08 0 1.23.89 2.42 1.02 2.59.13.17 1.76 2.69 4.27 3.77.6.26 1.06.41 1.43.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.06-.11-.23-.17-.48-.29"/>
                </svg>
            </a>
        @endif

        {{-- 3. Call Us Circle --}}
        @if (filled($qcPhone))
            <a href="tel:{{ $qcPhone }}" 
               title="Call Us ({{ $qcPhone }})"
               aria-label="Call Us"
               class="flex h-13 w-13 sm:h-14 sm:w-14 items-center justify-center rounded-full bg-brand-orange text-white shadow-lg hover:scale-105 active:scale-95 transition-transform duration-200 cursor-pointer">
                <svg class="w-6 h-6 pointer-events-none" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M6.62 10.79a15.053 15.053 0 0 0 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                </svg>
            </a>
        @endif
    </div>

    {{-- Main Toggle Button (Solid Orange with Headset when closed, Solid Dark with Cross when opened) --}}
    <button type="button" 
        id="quick-contact-toggle"
        @click.stop="open = !open"
        :title="open ? 'Close' : 'Quick Contact'"
        aria-label="Quick contact"
        :class="open ? '!bg-[#1F2937]' : '!bg-brand-orange'"
        class="flex h-13 w-13 sm:h-14 sm:w-14 items-center justify-center rounded-full text-white shadow-xl hover:scale-105 active:scale-95 transition-all duration-200 cursor-pointer focus:outline-none">
        
        {{-- Closed: Headset Icon --}}
        <svg x-show="!open" class="w-7 h-7 pointer-events-none" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 1a9 9 0 0 0-9 9v7c0 1.66 1.34 3 3 3h3v-8H5v-2a7 7 0 0 1 14 0v2h-4v8h3c1.66 0 3-1.34 3-3v-7a9 9 0 0 0-9-9z"/>
        </svg>

        {{-- Opened: Cross (X) Icon --}}
        <svg x-show="open" x-cloak class="w-7 h-7 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>
@endif
