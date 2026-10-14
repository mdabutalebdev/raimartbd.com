<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access Forbidden (403) - Raimart</title>
    @include('partials.favicons')
    @vite(['resources/css/app.css'])
    <style>
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .pulse-ring {
            animation: pulseRing 2.5s ease-out infinite;
        }
        @keyframes pulseRing {
            0% { transform: scale(0.95); opacity: 0.6; }
            50% { transform: scale(1.06); opacity: 0.25; }
            100% { transform: scale(0.95); opacity: 0.6; }
        }
    </style>
</head>
<body class="bg-[#FBF9F5] font-sans text-brand-navy antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-12">

        {{-- Animated 403 illustration --}}
        <div class="relative mb-8 float-animation">
            {{-- Background pulse ring --}}
            <div class="pulse-ring absolute inset-0 -m-4 rounded-full border-2 border-red-500/20"></div>

            {{-- Main circle --}}
            <div class="flex h-44 w-44 items-center justify-center rounded-full bg-gradient-to-br from-red-500/10 via-brand-orange/5 to-transparent sm:h-56 sm:w-56 relative shadow-inner">
                <div class="text-center">
                    <div class="mx-auto mb-1 flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-full bg-red-100 text-red-600 shadow-sm">
                        <i class="fa-solid fa-shield-halved text-lg sm:text-xl"></i>
                    </div>
                    <span class="block text-5xl font-extrabold text-brand-navy sm:text-7xl tracking-tight" style="line-height: 1;">403</span>
                    <div class="mx-auto mt-2 h-1 w-12 rounded-full bg-red-500/40"></div>
                </div>

                {{-- Corner floating lock badge --}}
                <div class="absolute -bottom-2 -right-2 flex h-11 w-11 items-center justify-center rounded-full bg-brand-navy text-white shadow-md border-2 border-white">
                    <i class="fa-solid fa-lock text-sm text-brand-orange"></i>
                </div>
            </div>

            {{-- Floating accent dots --}}
            <span class="absolute -right-3 top-4 h-3 w-3 rounded-full bg-red-400/30" style="animation: float 2.5s ease-in-out infinite 0.3s;"></span>
            <span class="absolute -left-2 bottom-8 h-2 w-2 rounded-full bg-brand-navy/20" style="animation: float 3.2s ease-in-out infinite 0.6s;"></span>
            <span class="absolute right-2 -bottom-1 h-2.5 w-2.5 rounded-full bg-brand-orange/30" style="animation: float 2.8s ease-in-out infinite 1s;"></span>
        </div>

        {{-- Content --}}
        <div class="fade-in max-w-md text-center" style="animation-delay: 0.15s;">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-600 border border-red-200 mb-3">
                <i class="fa-solid fa-circle-exclamation text-[11px]"></i> Access Denied
            </span>

            <h1 class="text-2xl font-bold text-brand-navy sm:text-3xl">
                Unauthorized Access
            </h1>
            <p class="mt-3 text-sm leading-relaxed text-brand-navy/60 sm:text-base">
                {{ (isset($exception) && $exception?->getMessage()) ? $exception->getMessage() : "You don't have permission to access this area or resource. If you think this is a mistake, please contact our support team." }}
            </p>
        </div>

        {{-- Actions --}}
        <div class="fade-in mt-8 flex flex-col gap-3 sm:flex-row" style="animation-delay: 0.3s;">
            @auth
                <a href="{{ route('user.dashboard') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-orange px-6 py-3 text-sm font-semibold text-white shadow-md shadow-brand-orange/20 transition-all duration-200 hover:bg-brand-navy hover:shadow-lg cursor-pointer">
                    <i class="fa-solid fa-gauge-high text-xs"></i>
                    My Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-orange px-6 py-3 text-sm font-semibold text-white shadow-md shadow-brand-orange/20 transition-all duration-200 hover:bg-brand-navy hover:shadow-lg cursor-pointer">
                    <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i>
                    Sign In
                </a>
            @endauth

            <a href="{{ url('/') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-brand-navy/15 bg-white px-6 py-3 text-sm font-semibold text-brand-navy shadow-2xs transition hover:border-brand-orange hover:text-brand-orange hover:bg-orange-50/30 cursor-pointer">
                <i class="fa-solid fa-house text-xs"></i>
                Go Home
            </a>
            
            <a href="{{ url('/shop') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-brand-navy/15 bg-white px-6 py-3 text-sm font-semibold text-brand-navy shadow-2xs transition hover:border-brand-orange hover:text-brand-orange hover:bg-orange-50/30 cursor-pointer">
                <i class="fa-solid fa-bag-shopping text-xs"></i>
                Browse Shop
            </a>
        </div>

        {{-- Helpful links --}}
        <div class="fade-in mt-10 flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-sm text-brand-navy/50" style="animation-delay: 0.45s;">
            <a href="{{ url('/contact') }}" class="transition hover:text-brand-orange">Contact Support</a>
            <span class="hidden sm:inline text-brand-navy/20">•</span>
            <a href="{{ url('/track-order') }}" class="transition hover:text-brand-orange">Track Order</a>
            <span class="hidden sm:inline text-brand-navy/20">•</span>
            <a href="{{ url('/about') }}" class="transition hover:text-brand-orange">About Raimart</a>
        </div>

        {{-- Brand footer --}}
        <p class="fade-in mt-12 text-xs text-brand-navy/30" style="animation-delay: 0.6s;">
            &copy; {{ date('Y') }} Raimart. All rights reserved.
        </p>
    </div>

    @vite(['resources/js/app.js'])
</body>
</html>
