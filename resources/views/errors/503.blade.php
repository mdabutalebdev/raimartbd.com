<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Under Maintenance - Raimart</title>
    @include('partials.favicons')
    @vite(['resources/css/app.css'])
    <style>
        .gear-spin {
            animation: gearSpin 8s linear infinite;
        }
        .gear-spin-reverse {
            animation: gearSpin 6s linear infinite reverse;
        }
        @keyframes gearSpin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .progress-bar {
            animation: progress 3s ease-in-out infinite;
        }
        @keyframes progress {
            0% { width: 10%; }
            50% { width: 70%; }
            100% { width: 10%; }
        }
    </style>
</head>
<body class="bg-[#FBF9F5] font-sans text-brand-navy antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-12">

        {{-- Gear animation --}}
        <div class="relative mb-10">
            <div class="flex items-end gap-1">
                {{-- Large gear --}}
                <div class="gear-spin text-brand-orange/70">
                    <svg xmlns="http://www.w3.org/2000/svg" width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20a8 8 0 1 0 0-16 8 8 0 0 0 0 16Z"/>
                        <path d="M12 14a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/>
                        <path d="M12 2v2"/><path d="M12 20v2"/>
                        <path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/>
                        <path d="M2 12h2"/><path d="M20 12h2"/>
                        <path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>
                    </svg>
                </div>
                {{-- Small gear --}}
                <div class="gear-spin-reverse text-brand-navy/30 -ml-3 -mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20a8 8 0 1 0 0-16 8 8 0 0 0 0 16Z"/>
                        <path d="M12 14a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/>
                        <path d="M12 2v2"/><path d="M12 20v2"/>
                        <path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/>
                        <path d="M2 12h2"/><path d="M20 12h2"/>
                        <path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="fade-in max-w-lg text-center" style="animation-delay: 0.15s;">
            <span class="mb-4 inline-flex items-center gap-2 rounded-full bg-brand-orange/10 px-4 py-1.5 text-sm font-semibold text-brand-orange">
                <span class="h-2 w-2 rounded-full bg-brand-orange animate-pulse"></span>
                Maintenance Mode
            </span>
            <h1 class="mt-4 text-2xl font-bold text-brand-navy sm:text-3xl">
                We'll be right back
            </h1>
            <p class="mt-4 text-sm leading-relaxed text-brand-navy/60 sm:text-base">
                We're performing scheduled maintenance to improve your shopping experience.
                We'll be back online shortly — thank you for your patience!
            </p>
        </div>

        {{-- Progress indicator --}}
        <div class="fade-in mt-8 w-full max-w-xs" style="animation-delay: 0.3s;">
            <div class="h-1.5 w-full overflow-hidden rounded-full bg-brand-navy/10">
                <div class="progress-bar h-full rounded-full bg-gradient-to-r from-brand-orange/60 via-brand-orange to-brand-orange/60"></div>
            </div>
            <p class="mt-3 text-center text-xs text-brand-navy/40">Working on it...</p>
        </div>

        {{-- Contact info --}}
        <div class="fade-in mt-10 rounded-xl border border-brand-navy/10 bg-white px-6 py-5 text-center shadow-sm" style="animation-delay: 0.45s;">
            <p class="text-sm font-medium text-brand-navy/70">Need urgent help?</p>
            <div class="mt-3 flex flex-wrap items-center justify-center gap-4 text-sm">
                <a href="tel:{{ \App\Models\SiteSetting::get('contact_phone', '') }}" class="inline-flex items-center gap-2 text-brand-navy/60 transition hover:text-brand-orange">
                    <i class="fa-solid fa-phone text-xs"></i>
                    <span>Call Us</span>
                </a>
                <a href="mailto:{{ \App\Models\SiteSetting::get('contact_email', 'support@raimart.com') }}" class="inline-flex items-center gap-2 text-brand-navy/60 transition hover:text-brand-orange">
                    <i class="fa-solid fa-envelope text-xs"></i>
                    <span>Email Us</span>
                </a>
            </div>
        </div>

        {{-- Brand footer --}}
        <p class="fade-in mt-12 text-xs text-brand-navy/30" style="animation-delay: 0.6s;">
            &copy; {{ date('Y') }} Raimart. All rights reserved.
        </p>
    </div>

    @vite(['resources/js/app.js'])
</body>
</html>
