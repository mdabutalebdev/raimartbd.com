<x-layout title="Contact Us - Raimart">
    <div class="bg-[#F8FAFC] py-10 sm:py-14">
        <div class="gb-container w-full">
            
            {{-- Breadcrumb (Centered, matching reference) --}}
            <div class="flex items-center justify-center gap-2 text-xs text-gray-400 mb-3">
                <a href="{{ route('home') }}" class="hover:text-brand-orange transition-colors">Home</a>
                <span>&rsaquo;</span>
                <span class="text-gray-700 font-medium">Contact Us</span>
            </div>

            {{-- Header Section (Centered Title & Subtitle, matching reference) --}}
            <div class="text-center max-w-xl mx-auto mb-8 sm:mb-10">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-[#0F172A] tracking-tight mb-2">
                    Get In Touch
                </h1>
                <p class="text-xs sm:text-sm text-gray-500 leading-relaxed">
                    Have a question, feedback, or need assistance with your order?<br class="hidden sm:inline">
                    Our team is ready to help you every step of the way.
                </p>
            </div>

            {{-- Top 4-Column Contact Cards (Full gb-container Width, Matching Reference) --}}
            <div class="w-full bg-white rounded-2xl border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] overflow-hidden mb-10">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-gray-100">
                    
                    {{-- 1. CALL US --}}
                    <div class="p-6 text-center hover:bg-gray-50/40 transition-colors">
                        <div class="w-10 h-10 rounded-full bg-orange-50 text-[#EA580C] flex items-center justify-center text-base mx-auto mb-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <p class="text-[10px] font-bold text-gray-400 tracking-widest uppercase">CALL US</p>
                        <a href="tel:01622243011" class="text-sm sm:text-base font-bold text-gray-900 hover:text-[#EA580C] transition-colors mt-1 block">
                            01622243011
                        </a>
                        <p class="text-[11px] text-gray-400 mt-1">24 Hours Open</p>
                    </div>

                    {{-- 2. WHATSAPP --}}
                    <div class="p-6 text-center hover:bg-gray-50/40 transition-colors">
                        <div class="w-10 h-10 rounded-xl bg-[#E8F8EE] text-[#22C55E] flex items-center justify-center text-lg mx-auto mb-3">
                            <i class="fa-brands fa-whatsapp"></i>
                        </div>
                        <p class="text-[10px] font-bold text-gray-400 tracking-widest uppercase">WHATSAPP</p>
                        <a href="https://wa.me/8801622243011?text=Hello%20Raimart" target="_blank" rel="noopener noreferrer" class="text-sm sm:text-base font-bold text-gray-900 hover:text-[#22C55E] transition-colors mt-1 block">
                            01622243011
                        </a>
                        <p class="text-[11px] text-gray-400 mt-1">Quick reply within minutes</p>
                    </div>

                    {{-- 3. EMAIL US --}}
                    <div class="p-6 text-center hover:bg-gray-50/40 transition-colors">
                        <div class="w-10 h-10 rounded-xl bg-[#EEF2FF] text-[#6366F1] flex items-center justify-center text-base mx-auto mb-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <p class="text-[10px] font-bold text-gray-400 tracking-widest uppercase">EMAIL US</p>
                        <a href="mailto:support@raimartbd.com" class="text-sm sm:text-base font-bold text-gray-900 hover:text-[#EA580C] transition-colors mt-1 block truncate px-2">
                            support@raimartbd.com
                        </a>
                        <p class="text-[11px] text-gray-400 mt-1">We reply within 24 hours</p>
                    </div>

                    {{-- 4. ADDRESS --}}
                    <div class="p-6 text-center hover:bg-gray-50/40 transition-colors">
                        <div class="w-10 h-10 rounded-full bg-orange-50 text-[#EA580C] flex items-center justify-center text-base mx-auto mb-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <p class="text-[10px] font-bold text-gray-400 tracking-widest uppercase">ADDRESS</p>
                        <p class="text-sm sm:text-base font-bold text-gray-900 mt-1 font-bangla">
                            সাভার, ঢাকা
                        </p>
                        <p class="text-[11px] text-gray-400 mt-0.5 leading-snug font-bangla">
                            East Dendabor, Ashuliya, Savar, 1340
                        </p>
                    </div>

                </div>
            </div>

            {{-- Lower Two-Column Section (Full gb-container Width, Matching Reference) --}}
            <div class="w-full grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                {{-- Left Column: Send Us a Message Form (lg:col-span-8) --}}
                <div class="lg:col-span-8">
                    
                    {{-- Header with Outline Speech Bubble Icon --}}
                    <div class="flex items-center gap-2.5 mb-6">
                        <svg class="w-5 h-5 text-[#EA580C] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <div>
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900 leading-tight">
                                Send Us a Message
                            </h2>
                            <p class="text-xs text-gray-400 mt-0.5">
                                We'll get back to you within 24 hours
                            </p>
                        </div>
                    </div>

                    {{-- Flash Status Alert --}}
                    @if (session('status'))
                        <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-700 flex items-center gap-3 text-xs sm:text-sm font-bangla">
                            <i class="fa-solid fa-circle-check text-green-600 text-base shrink-0"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    {{-- Validation Errors --}}
                    @if ($errors->any())
                        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-600 text-xs sm:text-sm font-bangla">
                            <div class="flex items-center gap-2 font-semibold mb-1">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <span>দয়া করে নিচের তথ্যগুলো চেক করুন:</span>
                            </div>
                            <ul class="list-inside list-disc space-y-0.5 text-xs text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Form --}}
                    <form action="{{ route('contact.store') }}" method="POST" class="space-y-4">
                        @csrf
                        
                        {{-- Row 1: Full Name & Phone Number --}}
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                    Full Name <span class="text-[#EA580C]">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Your full name"
                                    class="w-full h-11 rounded-lg border border-gray-200 bg-white px-3.5 text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#EA580C] focus:ring-1 focus:ring-[#EA580C] transition">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                    Phone Number
                                </label>
                                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="01XXXXXXXXX"
                                    class="w-full h-11 rounded-lg border border-gray-200 bg-white px-3.5 text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#EA580C] focus:ring-1 focus:ring-[#EA580C] transition">
                            </div>
                        </div>

                        {{-- Row 2: Email Address (With envelope icon inside) --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                Email Address <span class="text-[#EA580C]">*</span>
                            </label>
                            <div class="relative">
                                <input type="email" name="email" value="{{ old('email') }}" required placeholder="example@email.com"
                                    class="w-full h-11 rounded-lg border border-gray-200 bg-white pl-3.5 pr-10 text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#EA580C] focus:ring-1 focus:ring-[#EA580C] transition">
                                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-teal-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Row 3: Message --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                Message <span class="text-[#EA580C]">*</span>
                            </label>
                            <textarea name="message" rows="5" required placeholder="Describe your issue or question in detail..."
                                class="w-full rounded-lg border border-gray-200 bg-white p-3.5 text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#EA580C] focus:ring-1 focus:ring-[#EA580C] transition resize-none">{{ old('message') }}</textarea>
                        </div>

                        {{-- Submit Button (Matching reference) --}}
                        <div class="pt-1">
                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-[#EA580C] hover:bg-[#C2410C] text-white px-7 py-3 text-xs sm:text-sm font-semibold shadow-sm transition-all cursor-pointer">
                                <svg class="w-3.5 h-3.5 rotate-45 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                <span>Send Message</span>
                            </button>
                        </div>

                    </form>
                </div>

                {{-- Right Column: Dark Contact Information Card (Matching Reference Image 2) --}}
                <div class="lg:col-span-4 bg-gradient-to-b from-[#18212F] via-[#121824] to-[#0C101A] rounded-[28px] p-7 sm:p-8 text-white shadow-[0_20px_45px_rgba(0,0,0,0.18)] border border-white/[0.06]">
                    <h3 class="text-2xl font-bold text-white tracking-tight mb-7 sm:mb-8">
                        Contact Information
                    </h3>

                    <div class="space-y-6 sm:space-y-7">
                        
                        {{-- 1. Our Office --}}
                        <div class="flex items-center gap-4">
                            <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-2xl bg-[#232B39] border border-white/[0.05] flex items-center justify-center shrink-0 text-[#EA580C] shadow-inner">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium mb-1">Our Office</p>
                                <p class="text-sm sm:text-base font-semibold text-white leading-snug">Dhaka, Bangladesh</p>
                                <p class="text-[11px] text-gray-400 mt-0.5 leading-snug font-bangla">East Dendabor, Ashuliya, Savar, 1340</p>
                            </div>
                        </div>

                        {{-- 2. Call Us --}}
                        <div class="flex items-center gap-4">
                            <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-2xl bg-[#232B39] border border-white/[0.05] flex items-center justify-center shrink-0 text-[#EA580C] shadow-inner">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56a.977.977 0 00-1.01.24l-2.2 2.2a15.05 15.05 0 01-6.59-6.59l2.2-2.21a.96.96 0 00.25-1A11.36 11.36 0 018.5 3.93c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1 0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.55c0-.55-.45-1-.99-1z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium mb-1">Call Us</p>
                                <a href="tel:01622243011" class="text-sm sm:text-base font-semibold text-white hover:text-brand-orange transition-colors leading-snug block">
                                    +880 1622243011
                                </a>
                            </div>
                        </div>

                        {{-- 3. Email Us --}}
                        <div class="flex items-center gap-4">
                            <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-2xl bg-[#232B39] border border-white/[0.05] flex items-center justify-center shrink-0 text-[#EA580C] shadow-inner">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-gray-400 font-medium mb-1">Email Us</p>
                                <a href="mailto:support@raimartbd.com" class="text-sm sm:text-base font-semibold text-white hover:text-brand-orange transition-colors leading-snug truncate block">
                                    support@raimartbd.com
                                </a>
                            </div>
                        </div>

                        {{-- 4. Working Hours --}}
                        <div class="flex items-center gap-4">
                            <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-2xl bg-[#232B39] border border-white/[0.05] flex items-center justify-center shrink-0 text-[#EA580C] shadow-inner">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium mb-1">Working Hours</p>
                                <p class="text-sm sm:text-base font-semibold text-white leading-snug">
                                    Sat - Thu: 09:00 AM - 08:00 PM
                                </p>
                                <p class="text-[11px] text-[#EA580C] font-medium mt-0.5">
                                    24 Hours Open
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>
</x-layout>
