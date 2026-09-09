<x-layout title="About Us - Raimart">
    {{-- Main Content --}}
    <div class="relative gb-container py-8 md:py-12 bg-[#FFF9F5] overflow-hidden">
        {{-- Faint dot/grid background pattern --}}
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle at 2px 2px, black 1px, transparent 0); background-size: 24px 24px;"></div>

        <div class="relative z-10 grid items-center gap-8 lg:grid-cols-2">
            
            {{-- Image Left --}}
            <div class="relative order-2 lg:order-1">
                <div class="absolute -inset-4 bg-[#FFEFE5] rounded-full blur-2xl opacity-60"></div>
                <div class="relative overflow-hidden rounded-2xl shadow-sm bg-white flex items-center justify-center aspect-[4/3]">
                    @if (filled($settings['about_image'] ?? null))
                        <img src="{{ image_url($settings['about_image'], 'About Raimart') }}" alt="About Raimart" class="w-full h-full object-cover">
                    @else
                        {{-- Reliable Fallback --}}
                        <div class="w-full h-full bg-orange-50 flex items-center justify-center p-8">
                            <img src="{{ asset('images/logo.png') }}" alt="Raimart Logo" class="max-w-[200px] opacity-20 grayscale">
                        </div>
                    @endif
                </div>
            </div>

            {{-- Content Right --}}
            <div class="order-1 lg:order-2">
                {{-- Badge --}}
                <div class="inline-flex items-center gap-2 rounded-full bg-white border border-brand-orange/20 px-3 py-1 text-xs font-semibold text-brand-orange mb-4 shadow-sm">
                    <span class="h-1.5 w-1.5 rounded-full bg-brand-orange"></span> About US
                </div>
                
                {{-- Heading --}}
                <h1 class="text-3xl sm:text-4xl lg:text-[44px] font-bold text-[#111827] leading-[1.2] mb-4">
                    {{ $settings['about_title'] ?? 'We Build Digital Experiences That Grow Businesses' }}
                </h1>
                
                {{-- Paragraph --}}
                <div class="text-[#4B5563] text-base leading-relaxed mb-8">
                    @if (filled($settings['about_content'] ?? null))
                        @foreach (preg_split('/\R{2,}/', trim($settings['about_content'])) as $paragraph)
                            <p class="mb-3 last:mb-0">{{ $paragraph }}</p>
                        @endforeach
                    @else
                        <p>At Raimart, we help customers find the best quality products for their daily needs. Our team combines creativity, technology, and strategy to deliver a seamless shopping experience that drives satisfaction and success.</p>
                    @endif
                </div>
                
                {{-- Stats Grid --}}
                <div class="grid grid-cols-3 gap-3 sm:gap-4">
                    @if ($counters->isNotEmpty())
                        @foreach ($counters->take(3) as $counter)
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center hover:shadow-md transition-shadow">
                                <p class="text-xl sm:text-2xl font-bold text-[#111827] mb-1">
                                    {{ $counter['value'] }}{{ $counter['suffix'] ?? '' }}
                                </p>
                                <p class="text-[10px] sm:text-xs font-medium text-gray-500">{{ $counter['label'] }}</p>
                            </div>
                        @endforeach
                    @else
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center hover:shadow-md transition-shadow">
                            <p class="text-xl sm:text-2xl font-bold text-[#111827] mb-1">5+</p>
                            <p class="text-[10px] sm:text-xs font-medium text-gray-500">Years Experience</p>
                        </div>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center hover:shadow-md transition-shadow">
                            <p class="text-xl sm:text-2xl font-bold text-[#111827] mb-1">500+</p>
                            <p class="text-[10px] sm:text-xs font-medium text-gray-500">Projects Completed</p>
                        </div>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center hover:shadow-md transition-shadow">
                            <p class="text-xl sm:text-2xl font-bold text-[#111827] mb-1">98%</p>
                            <p class="text-[10px] sm:text-xs font-medium text-gray-500">Client Satisfaction</p>
                        </div>
                    @endif
                </div>
                
            </div>
        </div>
    </div>

    {{-- Mission Section --}}
    @if(filled($settings['about_mission_title'] ?? '') || filled($settings['about_mission_content'] ?? ''))
    <div class="gb-container py-8 md:py-12 bg-white border-b border-brand-navy/5">
        <div class="grid md:grid-cols-2 gap-8 items-center">
            <div class="order-2 md:order-1">
                <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 border border-blue-100 px-3 py-1 text-xs font-semibold text-blue-600 mb-4 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Our Mission
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold text-brand-navy mb-4">
                    {{ $settings['about_mission_title'] ?? 'Our Mission' }}
                </h2>
                <div class="text-[#4B5563] text-base leading-relaxed whitespace-pre-line">
                    {{ $settings['about_mission_content'] ?? 'To empower businesses and individuals with the best products and digital experiences.' }}
                </div>
            </div>
            <div class="order-1 md:order-2">
                <div class="rounded-2xl overflow-hidden shadow-sm border border-brand-navy/5 bg-blue-50 relative h-48 md:h-64 flex items-center justify-center">
                    @if (filled($settings['about_mission_image'] ?? null))
                        <img src="{{ image_url($settings['about_mission_image'], 'Our Mission') }}" alt="Our Mission" class="w-full h-full object-cover">
                    @else
                        <img src="{{ asset('images/logo.png') }}" alt="Mission" class="max-w-[150px] opacity-10 grayscale">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-100/50 to-indigo-50/50"></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Vision Section --}}
    @if(filled($settings['about_vision_title'] ?? '') || filled($settings['about_vision_content'] ?? ''))
    <div class="gb-container py-8 md:py-12 bg-[#F8FAFC]">
        <div class="grid md:grid-cols-2 gap-8 items-center">
            <div>
                <div class="rounded-2xl overflow-hidden shadow-sm border border-brand-navy/5 bg-orange-50 relative h-48 md:h-64 flex items-center justify-center">
                    @if (filled($settings['about_vision_image'] ?? null))
                        <img src="{{ image_url($settings['about_vision_image'], 'Our Vision') }}" alt="Our Vision" class="w-full h-full object-cover">
                    @else
                        <img src="{{ asset('images/logo.png') }}" alt="Vision" class="max-w-[150px] opacity-10 grayscale">
                        <div class="absolute inset-0 bg-gradient-to-br from-orange-100/50 to-amber-50/50"></div>
                    @endif
                </div>
            </div>
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-orange-50 border border-orange-100 px-3 py-1 text-xs font-semibold text-brand-orange mb-4 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    Our Vision
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold text-brand-navy mb-4">
                    {{ $settings['about_vision_title'] ?? 'Our Vision' }}
                </h2>
                <div class="text-[#4B5563] text-base leading-relaxed whitespace-pre-line">
                    {{ $settings['about_vision_content'] ?? 'To become the most trusted platform globally, making every shopping experience seamless.' }}
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Why Choose Us Section --}}
    @if(isset($whyChooseUs) && $whyChooseUs->isNotEmpty())
    <div class="gb-container py-8 md:py-12 bg-white">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <h2 class="text-2xl sm:text-3xl font-bold text-brand-navy mb-3">
                {{ $settings['about_why_choose_us_title'] ?? 'Why Choose Us' }}
            </h2>
            <div class="w-16 h-1 bg-brand-orange mx-auto rounded-full"></div>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
            @foreach($whyChooseUs as $why)
            <div class="relative bg-white p-6 rounded-2xl border border-gray-100 shadow-[0_2px_15px_rgba(0,0,0,0.02)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.06)] hover:-translate-y-1 transition-all duration-300 group overflow-hidden">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-brand-orange/5 rounded-full blur-2xl group-hover:bg-brand-orange/10 transition-colors duration-500"></div>
                <div class="w-12 h-12 bg-gradient-to-br from-brand-orange to-orange-600 rounded-xl flex items-center justify-center text-white text-lg shadow-sm shadow-orange-500/30 mb-6 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                    <i class="{{ $why['icon'] ?? 'fa-solid fa-check' }}"></i>
                </div>
                <h3 class="text-lg font-bold text-brand-navy mb-2">{{ $why['title'] }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed relative z-10">{{ $why['description'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</x-layout>
