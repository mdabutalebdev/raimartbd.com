@props([
    'title' => 'Raimart - Everything You Need in One Place | বাংলাদেশের বিশ্বস্ত অনলাইন শপিং',
    'description' => null,
    'image' => null,
    'type' => 'website',
    'product' => null,
])
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>

    {{-- SEO + social sharing --}}
    @php
        $metaDescription = $description
            ?: (\App\Models\SiteSetting::get('site_description')
                ?: 'Raimart — বাংলাদেশের বিশ্বস্ত অনলাইন শপিং প্ল্যাটফর্ম। কসমেটিকস ও স্কিনকেয়ার, স্মার্ট গ্যাজেট, চায়না ট্রেন্ডিং পণ্য ও লাইফস্টাইল প্রয়োজনীয় সবকিছু এক জায়গায়। Everything You Need in One Place.');
        $metaImage = $image ? image_url($image) : asset('images/logo.png');
        
        $orgSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'OnlineStore',
            'name' => 'Raimart',
            'alternateName' => 'রাইমার্ট',
            'url' => url('/'),
            'logo' => asset('images/logo.png'),
            'slogan' => 'Everything You Need in One Place',
            'description' => 'Raimart বাংলাদেশের বিশ্বস্ত অনলাইন শপিং প্ল্যাটফর্ম। আপনার প্রয়োজন, আপনার পছন্দ, সবকিছু এক জায়গায়।',
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => \App\Models\SiteSetting::get('contact_phone', '01622243011'),
                'contactType' => 'customer service',
                'areaServed' => 'BD',
                'availableLanguage' => ['Bengali', 'English']
            ]
        ];

        $socialLinks = array_values(array_filter([
            \App\Models\SiteSetting::get('facebook_url'),
            \App\Models\SiteSetting::get('instagram_url'),
            \App\Models\SiteSetting::get('twitter_url'),
        ]));
        if (!empty($socialLinks)) {
            $orgSchema['sameAs'] = $socialLinks;
        }

        $websiteSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'Raimart',
            'alternateName' => 'রাইমার্ট',
            'url' => url('/'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => url('/shop') . '?search={search_term_string}',
                'query-input' => 'required name=search_term_string'
            ]
        ];
    @endphp

    <script type="application/ld+json">
        {!! json_encode($orgSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    <script type="application/ld+json">
        {!! json_encode($websiteSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($metaDescription), 160) }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:type" content="{{ $type ?? 'website' }}">
    @if(($type ?? 'website') === 'product' && $product)
        <meta property="product:price:amount" content="{{ $product->price }}">
        <meta property="product:price:currency" content="BDT">
        <meta property="product:availability" content="{{ $product->stock > 0 ? 'in stock' : 'out of stock' }}">
    @endif
    <meta property="og:site_name" content="Raimart">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($metaDescription), 200) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $metaImage }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($metaDescription), 200) }}">
    <meta name="twitter:image" content="{{ $metaImage }}">

    {{-- Data layer initialization & page events --}}
    <script>window.dataLayer = window.dataLayer || [];</script>
    @stack('datalayer')

    <!-- Favicon -->
    @include('partials.favicons')
    
    <!-- Google Fonts: Hind Siliguri for Bengali -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    {{-- Meta Pixel & Analytics Tracker --}}
    @include('partials.pixel-tracker')
</head>
<body class="bg-[#FBF9F5] font-sans text-brand-navy antialiased">
    @if (config('services.ga4.gtm_id'))
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ config('services.ga4.gtm_id') }}"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    @endif

    @include('partials.header')

    {{-- No bottom padding here: the clearance for the fixed mobile nav lives on the
         footer instead, otherwise it shows up as a blank gap above the footer. --}}
    <main>
        {{ $slot }}
    </main>

    @include('partials.footer')
    @include('partials.mobile-nav')
    @include('partials.category-drawer')
    @include('partials.mobile-menu-drawer')
    @include('partials.cart-drawer')
    @include('partials.quick-contact')
    @include('partials.toast')
    
    @livewireScripts

    {{-- Custom Body Tracking Scripts --}}
    @include('partials.pixel-body')
</body>
</html>
