<x-layout
    title="আমাদের সম্পর্কে - Raimart | Everything You Need in One Place"
    description="Raimart বাংলাদেশের বিশ্বস্ত অনলাইন শপিং প্ল্যাটফর্ম। কসমেটিকস ও স্কিনকেয়ার, আধুনিক গ্যাজেট, চায়না ট্রেন্ডিং পণ্য ও লাইফস্টাইল প্রয়োজনীয় সবকিছু এক জায়গায়।"
>
    @php
        $specialties = [
            [
                'title' => 'কসমেটিকস ও স্কিনকেয়ার',
                'description' => 'আপনার ত্বক ও রূপচর্চায় শতভাগ খাঁটি এবং প্রিমিয়াম কোয়ালিটির স্কিনকেয়ার পণ্য। নিশ্চিন্তে বেছে নিন আপনার পছন্দের পণ্যটি।',
                'icon' => 'fa-solid fa-wand-magic-sparkles',
            ],
            [
                'title' => 'গ্যাজেটস ও টেক এক্সেসরিজ',
                'description' => 'দৈনন্দিন জীবনকে আরও স্মার্ট ও সহজ করতে প্রয়োজনীয় আধুনিক সব টেক গ্যাজেট ও কোয়ালিটি এক্সেসরিজ এক ছাদের নিচে।',
                'icon' => 'fa-solid fa-headphones',
            ],
            [
                'title' => 'চায়না সোর্সড প্রোডাক্টস',
                'description' => 'গ্রাহকদের চাহিদা ও বর্তমান ট্রেন্ড বিবেচনায় চীন থেকে সরাসরি সংগৃহীত সেরা মানের ও ইউনিক সব আকর্ষণীয় কালেকশন।',
                'icon' => 'fa-solid fa-box-open',
            ],
            [
                'title' => 'লেটেস্ট ও ট্রেন্ডিং প্রোডাক্টস',
                'description' => 'সোশ্যাল মিডিয়া ও বর্তমান সময়ের ভাইরাল ও জনপ্রিয় ট্রেন্ডিং পণ্যগুলো খুঁজে নিন এক জায়গায় খুব সহজেই।',
                'icon' => 'fa-solid fa-bolt',
            ],
            [
                'title' => 'লাইফস্টাইল ও নিত্যপ্রয়োজনীয় পণ্য',
                'description' => 'প্রতিদিনের প্রয়োজনীয় সামগ্রী থেকে লাইফস্টাইল পণ্য—সবকিছুই পাবেন Raimart-এ সহজ, সাশ্রয়ী ও কোয়ালিটি শপিংয়ে।',
                'icon' => 'fa-solid fa-bag-shopping',
            ],
        ];

        $defaultWhyChooseUs = [
            [
                'icon' => 'fa-solid fa-shield-halved',
                'title' => '১০০% অথেনটিক কোয়ালিটি',
                'description' => 'স্কিনকেয়ার ও গ্যাজেট সহ প্রতিটি পণ্য যাচাই-বাছাই করে সর্বোচ্চ মানের নিশ্চয়তা দিয়ে থাকি।'
            ],
            [
                'icon' => 'fa-solid fa-truck-fast',
                'title' => 'দ্রুততম হোম ডেলিভারি',
                'description' => 'সারা দেশে দ্রুত ও নিরাপদ হোম ডেলিভারির সুবিধা, যাতে সময়মতো আপনার পণ্য পৌঁছে যায়।'
            ],
            [
                'icon' => 'fa-solid fa-hand-holding-dollar',
                'title' => 'ক্যাশ অন ডেলিভারি সুবিধা',
                'description' => 'পণ্য হাতে পেয়ে মূল্য পরিশোধের সুবিধা, ফলে কোনো প্রকার ঝুঁকি ছাড়াই কেনাকাটা করুন।'
            ],
            [
                'icon' => 'fa-solid fa-headset',
                'title' => 'আন্তরিক কাস্টমার সেবা',
                'description' => 'অর্ডার থেকে ডেলিভারি পর্যন্ত যেকোনো প্রয়োজনে আমাদের ফ্রেন্ডলি সাপোর্ট টিম সবসময় পাশে আছে।'
            ]
        ];
        $trustFeatures = (isset($whyChooseUs) && $whyChooseUs->isNotEmpty()) ? $whyChooseUs : collect($defaultWhyChooseUs);

        $aboutImage = filled($settings['about_image'] ?? null)
            ? (str_starts_with($settings['about_image'], 'images/') ? asset($settings['about_image']) : image_url($settings['about_image'], 'About Raimart'))
            : asset('images/raimart-store.jpeg');
    @endphp

    {{-- TOP HERO SECTION (Breadcrumb removed, gap reduced) --}}
    <section class="bg-white pt-6 sm:pt-8 md:pt-10 pb-10 sm:pb-12 border-b border-gray-100">
        <div class="gb-container">
            <div class="grid lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                
                {{-- Left Text Info --}}
                <div class="lg:col-span-7">
                    {{-- Slogan Badge --}}
                    <div class="inline-flex items-center gap-2 rounded-full bg-brand-orange/10 px-3.5 py-1 text-xs font-semibold text-brand-orange mb-3.5">
                        <span class="h-1.5 w-1.5 rounded-full bg-brand-orange"></span>
                        <span>Everything You Need in One Place</span>
                    </div>

                    {{-- Main Heading --}}
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-brand-navy tracking-tight leading-snug">
                        {{ str_replace([' — ', ' —', '— ', '—', ' - '], ' ', $settings['about_hero_title'] ?? 'Raimart বাংলাদেশের বিশ্বস্ত অনলাইন শপিং প্ল্যাটফর্ম') }}
                    </h1>

                    {{-- Story Description --}}
                    <div class="mt-4 space-y-3 text-xs sm:text-sm text-gray-700 leading-relaxed">
                        @if (filled($settings['about_content'] ?? null))
                            @foreach (preg_split('/\R{2,}/', trim($settings['about_content'])) as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        @else
                            <p>Raimart বাংলাদেশের অন্যতম সেরা ই-কমার্স প্ল্যাটফর্ম হওয়ার লক্ষ্য নিয়ে আপনার দৈনন্দিন কেনাকাটাকে আরও সহজ, সুবিধাজনক ও আনন্দদায়ক করে তুলতে কাজ করছে। কসমেটিকস ও স্কিনকেয়ার থেকে শুরু করে আধুনিক গ্যাজেট, টেক অ্যাক্সেসরিজ, চায়না থেকে সংগৃহীত ট্রেন্ডিং পণ্য এবং লাইফস্টাইলের প্রয়োজনীয় সবকিছু—আপনার পছন্দের পণ্যগুলো এক জায়গায় খুঁজে পেতে পাশে আছে Raimart।</p>
                            <p>আমরা বিশ্বাস করি, অনলাইন শপিং হওয়া উচিত সহজ, নির্ভরযোগ্য ও সবার জন্য স্বাচ্ছন্দ্যপূর্ণ। তাই গ্রাহকদের চাহিদা, পছন্দ এবং সময়ের সঙ্গে তাল মিলিয়ে নতুন ও জনপ্রিয় পণ্য নিয়ে আসার চেষ্টা করি প্রতিনিয়ত।</p>
                        @endif
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        <a href="{{ route('shop') }}" class="inline-flex items-center gap-2 rounded-lg bg-brand-orange px-5 py-2.5 text-xs sm:text-sm font-semibold text-white hover:bg-orange-600 transition-colors">
                            <i class="fa-solid fa-basket-shopping text-xs"></i>
                            কেনাকাটা শুরু করুন
                        </a>
                        <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-xs sm:text-sm font-semibold text-brand-navy hover:bg-gray-50 transition-colors">
                            <i class="fa-solid fa-phone text-xs text-brand-orange"></i>
                            যোগাযোগ করুন
                        </a>
                    </div>
                </div>

                {{-- Right Image (Simple, clean, no heavy border/shadow) --}}
                <div class="lg:col-span-5">
                    <div class="overflow-hidden rounded-xl bg-gray-50 border border-gray-100">
                        <img src="{{ $aboutImage }}"
                             alt="Raimart - Everything You Need in One Place"
                             class="w-full h-auto object-cover rounded-xl block">
                    </div>
                </div>

            </div>

            {{-- Flat Simple Stats Bar --}}
            <div class="mt-10 grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 border-t border-gray-100 pt-8">
                @if ($counters->isNotEmpty())
                    @foreach ($counters->take(4) as $counter)
                        <div class="rounded-lg bg-gray-50 border border-gray-100 p-4 text-center">
                            <div class="text-xl sm:text-2xl font-bold text-brand-navy">
                                {{ $counter['value'] }}<span class="text-brand-orange">{{ $counter['suffix'] ?? '' }}</span>
                            </div>
                            <div class="mt-1 text-xs text-gray-600 font-medium">{{ $counter['label'] }}</div>
                        </div>
                    @endforeach
                @else
                    <div class="rounded-lg bg-gray-50 border border-gray-100 p-4 text-center">
                        <div class="text-xl sm:text-2xl font-bold text-brand-navy">১০০<span class="text-brand-orange">%</span></div>
                        <div class="mt-1 text-xs text-gray-600 font-medium">খাঁটি পণ্য ও কোয়ালিটি</div>
                    </div>
                    <div class="rounded-lg bg-gray-50 border border-gray-100 p-4 text-center">
                        <div class="text-xl sm:text-2xl font-bold text-brand-navy">১০k<span class="text-brand-orange">+</span></div>
                        <div class="mt-1 text-xs text-gray-600 font-medium">সন্তুষ্ট গ্রাহক</div>
                    </div>
                    <div class="rounded-lg bg-gray-50 border border-gray-100 p-4 text-center">
                        <div class="text-xl sm:text-2xl font-bold text-brand-navy">৬৪<span class="text-brand-orange text-sm font-semibold"> জেলা</span></div>
                        <div class="mt-1 text-xs text-gray-600 font-medium">সারাদেশে হোম ডেলিভারি</div>
                    </div>
                    <div class="rounded-lg bg-gray-50 border border-gray-100 p-4 text-center">
                        <div class="text-xl sm:text-2xl font-bold text-brand-navy">২৪<span class="text-brand-orange text-sm">/৭</span></div>
                        <div class="mt-1 text-xs text-gray-600 font-medium">কাস্টমার সাপোর্ট</div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- OUR SPECIALTIES (Reference Style: Top-left rounded icon box, generous padding, clean hierarchy) --}}
    <section class="bg-gray-50/70 py-12 md:py-16 border-b border-gray-100">
        <div class="gb-container">
            <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-12">
                <span class="inline-flex items-center gap-2 rounded-full bg-brand-orange/10 px-3.5 py-1 text-xs font-semibold text-brand-orange mb-3">
                    <span class="h-1.5 w-1.5 rounded-full bg-brand-orange"></span>
                    <span>আমাদের স্পেশালিটি ও কালেকশন</span>
                </span>
                <h2 class="text-2xl sm:text-3xl font-bold text-brand-navy tracking-tight font-bangla">
                    আমাদের স্পেশালিটি
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-2 leading-relaxed">
                    আপনার প্রয়োজনীয় সবকিছু এক ছাদের নিচে — প্রতিটি ক্যাটাগরিতে বাছাইকৃত সেরা পণ্য।
                </p>
            </div>

            {{-- 5 Reference-Style Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($specialties as $item)
                    <div class="rounded-2xl border border-gray-100 bg-white p-7 sm:p-8 shadow-sm hover:shadow-md hover:border-brand-orange/40 hover:-translate-y-1 transition-all duration-300 flex flex-col justify-start">
                        <div class="w-12 h-12 rounded-xl bg-brand-orange text-white flex items-center justify-center text-xl mb-6 shadow-sm shadow-orange-500/20 shrink-0">
                            <i class="{{ $item['icon'] }}"></i>
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold text-brand-navy mb-3 tracking-tight">
                            {{ $item['title'] }}
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-500 leading-relaxed">
                            {{ $item['description'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- WHY CHOOSE US (Trust & Benefit Cards in the Same Clean Style) --}}
    <section class="bg-white py-12 md:py-16 border-b border-gray-100">
        <div class="gb-container">
            <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-12">
                <span class="inline-flex items-center gap-2 rounded-full bg-brand-orange/10 px-3.5 py-1 text-xs font-semibold text-brand-orange mb-3">
                    <span class="h-1.5 w-1.5 rounded-full bg-brand-orange"></span>
                    <span>Commitment & Trust</span>
                </span>
                <h2 class="text-2xl sm:text-3xl font-bold text-brand-navy tracking-tight font-bangla">
                    {{ $settings['about_why_choose_us_title'] ?? 'কেন Raimart-এ কেনাকাটা করবেন?' }}
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-2 leading-relaxed font-bangla">
                    কেনাকাটায় Raimart কেন আপনার সেরা পছন্দ? জেনে নিন আমাদের বিশেষ সুবিধা ও মূল প্রতিশ্রুতিসমূহ।
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($trustFeatures as $feature)
                    <div class="rounded-2xl border border-gray-100 bg-white p-6 sm:p-7 shadow-sm hover:shadow-md hover:border-brand-orange/40 hover:-translate-y-1 transition-all duration-300 flex flex-col justify-start">
                        <div class="w-12 h-12 rounded-xl bg-brand-orange text-white flex items-center justify-center text-xl mb-6 shadow-sm shadow-orange-500/20 shrink-0">
                            <i class="{{ $feature['icon'] ?? 'fa-solid fa-check' }}"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-brand-navy mb-2.5 tracking-tight">
                            {{ $feature['title'] }}
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-500 leading-relaxed">
                            {{ $feature['description'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- OUR MISSION & VISION (Simple, Clean, Minimalist Design) --}}
    <section class="bg-gray-50/70 py-12 md:py-16">
        <div class="gb-container">
            <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-12">
                <span class="inline-flex items-center gap-2 rounded-full bg-brand-orange/10 px-3.5 py-1 text-xs font-semibold text-brand-orange mb-3">
                    <span class="h-1.5 w-1.5 rounded-full bg-brand-orange"></span>
                    <span>আমাদের লক্ষ্য ও দৃষ্টিভঙ্গি</span>
                </span>
                <h2 class="text-2xl sm:text-3xl font-bold text-brand-navy tracking-tight font-bangla">
                    মিশন ও ভিশন
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-2 leading-relaxed font-bangla">
                    মানসম্মত পণ্য ও আস্থাশীল সেবায় আপনার নির্ভরযোগ্য অনলাইন সঙ্গী।
                </p>
            </div>
            <div class="grid sm:grid-cols-2 gap-6">
                
                {{-- Our Mission --}}
                <div class="rounded-2xl border border-gray-100 bg-white p-7 sm:p-8 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-brand-orange text-white flex items-center justify-center text-xl mb-6 shadow-sm shadow-orange-500/20 shrink-0">
                        <i class="fa-solid fa-bullseye"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-brand-navy mb-3 tracking-tight">
                        {{ $settings['about_mission_title'] ?? 'Our Mission' }}
                    </h3>
                    <div class="text-xs sm:text-sm text-gray-600 leading-relaxed space-y-2.5">
                        @if (filled($settings['about_mission_content'] ?? null))
                            @foreach (preg_split('/\R{2,}/', trim($settings['about_mission_content'])) as $p)
                                <p>{{ $p }}</p>
                            @endforeach
                        @else
                            <p>Raimart-এর লক্ষ্য শুধু পণ্য বিক্রি করা নয়, বরং মানসম্মত পণ্য, নির্ভরযোগ্য সেবা এবং একটি সুন্দর শপিং অভিজ্ঞতার মাধ্যমে গ্রাহকদের আস্থা অর্জন করা।</p>
                            <p>আপনার প্রতিদিনের প্রয়োজন থেকে শুরু করে পছন্দের ট্রেন্ডিং পণ্য—সবকিছুর জন্য Raimart-এর সঙ্গেই থাকুন।</p>
                        @endif
                    </div>
                </div>

                {{-- Our Vision --}}
                <div class="rounded-2xl border border-gray-100 bg-white p-7 sm:p-8 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-brand-navy text-white flex items-center justify-center text-xl mb-6 shadow-sm shadow-navy-500/20 shrink-0">
                        <i class="fa-solid fa-eye"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-brand-navy mb-3 tracking-tight">
                        {{ $settings['about_vision_title'] ?? 'Our Vision' }}
                    </h3>
                    <div class="text-xs sm:text-sm text-gray-600 leading-relaxed space-y-2.5">
                        @if (filled($settings['about_vision_content'] ?? null))
                            @foreach (preg_split('/\R{2,}/', trim($settings['about_vision_content'])) as $p)
                                <p>{{ $p }}</p>
                            @endforeach
                        @else
                            <p>বাংলাদেশের প্রতিটি প্রান্তে প্রতিটি মানুষের কাছে ন্যায্য মূল্যে খাঁটি ও ট্রেন্ডিং পণ্য পৌঁছে দিয়ে দেশের সবচেয়ে নির্ভরযোগ্য ও পছন্দের অনলাইন শপিং গন্তব্য হয়ে ওঠা।</p>
                            <p>অনলাইন কেনাকাটাকে সবার জন্য সহজ, নিরাপদ ও শতভাগ ঝুঁকিমুক্ত করাই আমাদের মূল প্রত্যয়।</p>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </section>
</x-layout>
