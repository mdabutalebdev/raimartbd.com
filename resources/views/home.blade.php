<x-layout>
    {{-- Hero --}}
    <section class="gb-container pt-5 md:pt-6">
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-[5fr_1fr]">
            <div class="relative overflow-hidden rounded-2xl swiper hero-swiper w-full min-w-0 h-[200px] sm:h-[300px] lg:h-auto">
                <div class="swiper-wrapper">
                    @foreach ($heroBanners as $index => $hero)
                        @php $hasText = filled($hero->title) || filled($hero->subtitle); @endphp
                        <div class="swiper-slide hero-slide relative aspect-[12/5]" data-ga-promotion="{{ json_encode(\App\Support\Ga4::promotion($hero)) }}">
                            <img src="{{ image_url($hero->image, urlencode($hero->title ?? 'Raimart')) }}" alt="{{ $hero->title }}" class="absolute inset-0 h-full w-full object-cover">

                            @if ($hasText)
                                <div class="absolute inset-0 bg-gradient-to-r from-brand-navy/60 via-brand-navy/20 to-transparent"></div>
                            @endif

                            @if ($hasText || filled($hero->button_text))
                                <div class="absolute inset-0 flex flex-col justify-center px-5 text-white sm:px-8 lg:px-14">
                                    @if (filled($hero->title))
                                        <h1 class="max-w-md font-serif text-xl font-bold leading-tight sm:text-3xl lg:text-5xl">
                                            {{ $hero->title }}
                                        </h1>
                                    @endif
                                    @if (filled($hero->subtitle))
                                        <p class="mt-1 max-w-sm text-xs text-white/80 sm:mt-4 sm:text-base">
                                            {{ $hero->subtitle }}
                                        </p>
                                    @endif

                                    @if (filled($hero->button_text))
                                        <a href="{{ $hero->link ?: route('shop') }}" class="mt-3 inline-flex w-fit items-center gap-2 rounded-md bg-brand-orange px-4 py-2 text-xs font-semibold text-white transition hover:bg-white hover:text-brand-navy sm:mt-8 sm:px-6 sm:py-3 sm:text-sm">
                                            {{ $hero->button_text }}
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if ($heroBanners->count() > 1)
                    <div class="swiper-pagination"></div>
                    <button type="button" class="hero-prev absolute left-4 top-1/2 z-10 hidden h-9 w-9 -translate-y-1/2 items-center justify-center rounded-[5px] border border-brand-orange bg-gray-50 text-brand-orange transition hover:bg-brand-orange hover:text-white lg:flex" aria-label="Previous">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>
                    <button type="button" class="hero-next absolute right-4 top-1/2 z-10 hidden h-9 w-9 -translate-y-1/2 items-center justify-center rounded-[5px] border border-brand-orange bg-gray-50 text-brand-orange transition hover:bg-brand-orange hover:text-white lg:flex" aria-label="Next">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                @endif
            </div>

            <div class="hidden gap-4 lg:flex lg:flex-col">
                @foreach ($promoBanners->take(2) as $promo)
                    <a href="{{ $promo->link ?? '#' }}" class="relative aspect-square overflow-hidden rounded-2xl" data-ga-promotion="{{ json_encode(\App\Support\Ga4::promotion($promo)) }}">
                        <img src="{{ image_url($promo->image, urlencode($promo->title ?? 'Promo')) }}" alt="{{ $promo->title }}" class="absolute inset-0 h-full w-full object-cover">
                        @if ($promo->badge_text)
                            <span class="absolute left-3 top-3 rounded-full bg-brand-orange px-3 py-1 text-xs font-semibold text-white">{{ $promo->badge_text }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Browse category: two rows, 8 cards in view, slides two columns at a time --}}
    <section class="gb-container py-4 md:py-6 relative overflow-hidden">
        <div>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl sm:text-3xl font-bold text-gray-800 mb-2 sm:mb-4">Browse Category</h2>
                <div class="flex shrink-0 gap-2">
                    <button type="button" class="category-prev flex h-9 w-9 items-center justify-center rounded-[5px] border border-brand-orange text-brand-orange transition hover:bg-brand-orange hover:text-white" aria-label="Scroll left">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>
                    <button type="button" class="category-next flex h-9 w-9 items-center justify-center rounded-[5px] border border-brand-orange text-brand-orange transition hover:bg-brand-orange hover:text-white" aria-label="Scroll right">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                </div>
            </div>

            <div class="swiper category-swiper w-full min-w-0 mt-4 md:mt-5 pb-2 overflow-hidden">
                <div class="swiper-wrapper">
                    @foreach ($categories as $category)
                        <div class="swiper-slide">
                            <a href="{{ route('shop', ['category' => $category->slug]) }}"
                                class="group block text-center">
                                {{-- Plain circle: the image fills it edge to edge, no tint, no padding --}}
                                <div class="mx-auto aspect-square w-full overflow-hidden rounded-full">
                                    <img src="{{ image_url($category->image, urlencode($category->name)) }}" alt="{{ $category->name }}"
                                        class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105"
                                        loading="lazy">
                                </div>
        
                                <p class="mt-2.5 text-[11px] font-semibold uppercase leading-tight tracking-wide text-brand-navy transition-colors group-hover:text-brand-orange sm:text-xs">
                                    {{ $category->name }}
                                </p>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Featured products --}}
    <section class="py-4 md:py-6">
        @php $featuredCategories = $featuredProducts->pluck('category')->filter()->unique('id')->values(); @endphp

        <div class="gb-container">
            <div class="text-center">
                <h2 class="text-xl sm:text-3xl font-bold text-gray-800 mb-2 sm:mb-4">Our Featured Products</h2>
            </div>

            {{-- Tab bar: horizontal scroll on mobile. touchend listeners (below) handle
                 all tap events reliably — no click-in-scroll-container conflicts. --}}
            <div class="-mx-3 sm:mx-0 overflow-x-auto scrollbar-hide">
                <div id="feat-tabs" class="mt-6 flex flex-nowrap sm:flex-wrap sm:justify-center gap-2 px-3 sm:px-0 pb-1 w-max sm:w-auto">
                    <button type="button" data-tab="all"
                        class="feat-tab-btn active-tab shrink-0 rounded-lg border px-5 py-2 text-sm font-semibold transition">All</button>
                    @foreach ($featuredCategories as $cat)
                        <button type="button" data-tab="{{ $cat->id }}"
                            class="feat-tab-btn shrink-0 rounded-lg border px-5 py-2 text-sm font-semibold transition">{{ $cat->name }}</button>
                    @endforeach
                </div>
            </div>

            {{-- Product grid --}}
            <div id="feat-grid" class="product-row mt-4 md:mt-5">
                @foreach ($featuredProducts as $product)
                    <div data-cat="{{ $product->category_id }}">
                        <x-product-card :product="$product" />
                    </div>
                @endforeach
            </div>

            <div class="mt-6 text-center">
                <a href="{{ route('shop', ['flags' => ['featured']]) }}" wire:navigate
                    class="inline-flex items-center gap-2 rounded-md border border-brand-orange px-6 py-2.5 text-sm font-semibold text-brand-orange transition hover:bg-brand-orange hover:text-white">
                    See More Products
                </a>
            </div>
        </div>
    </section>

    <script>
        function featFilter(tab) {
            document.querySelectorAll('.feat-tab-btn').forEach(function(btn) {
                btn.classList.toggle('active-tab', btn.dataset.tab == tab);
            });
            document.querySelectorAll('#feat-grid > div[data-cat]').forEach(function(card) {
                card.style.display = (tab === 'all' || card.dataset.cat == tab) ? '' : 'none';
            });
        }

        // Use touchend as primary handler on mobile — more reliable than click
        // inside scroll containers. Track touchstart to distinguish tap from swipe.
        (function() {
            var tStartX, tStartY;

            function bindTabs() {
                document.querySelectorAll('.feat-tab-btn').forEach(function(btn) {
                    btn.addEventListener('touchstart', function(e) {
                        tStartX = e.touches[0].clientX;
                        tStartY = e.touches[0].clientY;
                    }, { passive: true });

                    btn.addEventListener('touchend', function(e) {
                        var dx = Math.abs(e.changedTouches[0].clientX - tStartX);
                        var dy = Math.abs(e.changedTouches[0].clientY - tStartY);
                        if (dx < 10 && dy < 10) {
                            e.preventDefault(); // block ghost click
                            featFilter(this.dataset.tab);
                        }
                    });

                    btn.addEventListener('click', function() {
                        featFilter(this.dataset.tab);
                    });
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', bindTabs);
            } else {
                bindTabs();
            }

            // Re-bind after Livewire SPA navigation
            document.addEventListener('livewire:navigated', bindTabs);
        })();
    </script>
    <style>
        .feat-tab-btn {
            border-color: rgb(15 23 42 / 0.2);
            color: #0f172a;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            user-select: none;
        }
        .feat-tab-btn.active-tab {
            background-color: #F58502;
            border-color: #F58502;
            color: #fff;
        }
    </style>

    {{-- Best selling --}}
    <section class="py-4 md:py-6">
        <div class="gb-container relative">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl sm:text-3xl font-bold text-gray-800 mb-2 sm:mb-4">Best Selling Product</h2>
                <div class="flex shrink-0 gap-2">
                    <button type="button" class="flex h-9 w-9 items-center justify-center rounded-[5px] border border-brand-orange text-brand-orange transition hover:bg-brand-orange hover:text-white" aria-label="Scroll left">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>
                    <button type="button" class="flex h-9 w-9 items-center justify-center rounded-[5px] border border-brand-orange text-brand-orange transition hover:bg-brand-orange hover:text-white" aria-label="Scroll right">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                </div>
            </div>

            <div class="swiper product-swiper w-full min-w-0 mt-5 md:mt-8 pb-2">
                <div class="swiper-wrapper">
                    @forelse ($bestSelling as $product)
                        <div class="swiper-slide">
                            <x-product-card :product="$product" />
                        </div>
                    @empty
                        <p class="py-6 text-center text-brand-navy/40 w-full">No best sellers yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    {{-- New arrivals --}}
    <section class="py-4 md:py-6">
        <div class="gb-container relative">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl sm:text-3xl font-bold text-gray-800 mb-2 sm:mb-4">New Arrival Product</h2>
                <div class="flex shrink-0 gap-2">
                    <button type="button" class="flex h-9 w-9 items-center justify-center rounded-[5px] border border-brand-orange text-brand-orange transition hover:bg-brand-orange hover:text-white" aria-label="Scroll left">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>
                    <button type="button" class="flex h-9 w-9 items-center justify-center rounded-[5px] border border-brand-orange text-brand-orange transition hover:bg-brand-orange hover:text-white" aria-label="Scroll right">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                </div>
            </div>

            <div class="swiper product-swiper w-full min-w-0 mt-5 md:mt-8 pb-2">
                <div class="swiper-wrapper">
                    @forelse ($newArrivals as $product)
                        <div class="swiper-slide">
                            <x-product-card :product="$product" />
                        </div>
                    @empty
                        <p class="py-6 text-center text-brand-navy/40 w-full">No new arrivals yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    {{-- Just for you — 15 shown, then 5 more per "Show More" click --}}
    <section class="gb-container py-4 md:py-6">
        <div x-data="paginator({{ $justForYou->count() }}, 10)" x-ref="top">
            <h2 class="text-center text-xl sm:text-3xl font-bold text-gray-800 mb-2 sm:mb-4">Just for you</h2>

            {{-- 10 products per page, paged client-side (no reload) --}}
            <div class="product-row mt-4 md:mt-5">
                @foreach ($justForYou as $index => $product)
                    <div x-show="shows({{ $index }})" @if ($index >= 10) style="display:none;" @endif>
                        <x-product-card :product="$product" />
                    </div>
                @endforeach
            </div>

            @if ($justForYou->count() > 10)
                <nav class="mt-6 flex items-center justify-center gap-1.5" aria-label="Just for you pages">
                    <button type="button" @click="prev()" :disabled="page === 1"
                        class="flex h-9 w-9 items-center justify-center rounded-md text-brand-navy/60 transition hover:text-brand-orange disabled:cursor-not-allowed disabled:opacity-30"
                        aria-label="Previous page">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>

                    <template x-for="(n, i) in numbers" :key="i">
                        <button type="button" @click="go(n)" :disabled="n === '…'"
                            :class="n === page
                                ? 'bg-brand-orange text-white'
                                : (n === '…' ? 'cursor-default text-brand-navy/40' : 'text-brand-navy hover:bg-brand-orange/10 hover:text-brand-orange')"
                            class="flex h-9 min-w-9 items-center justify-center rounded-md px-2 text-sm font-semibold transition"
                            x-text="n"></button>
                    </template>

                    <button type="button" @click="next()" :disabled="page === pages"
                        class="flex h-9 w-9 items-center justify-center rounded-md text-brand-navy/60 transition hover:text-brand-orange disabled:cursor-not-allowed disabled:opacity-30"
                        aria-label="Next page">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                </nav>
            @endif
        </div>
    </section>

    {{-- Testimonials --}}
    <section class="py-4 md:py-6">
        <div class="gb-container">
            <h2 class="text-center text-xl sm:text-3xl font-bold text-gray-800 mb-2 sm:mb-4">Customer Reviews</h2>
            <p class="mx-auto mt-2 max-w-xl text-center text-sm text-brand-navy/60">
                Real stories, genuine smiles. Discover why thousands trust Raimart for their everyday shopping.
            </p>

            <div class="swiper testimonial-swiper w-full min-w-0 mt-4 md:mt-6 pb-2">
                <div class="swiper-wrapper flex">
                    @foreach ($testimonials as $testimonial)
                        <div class="swiper-slide !h-auto">
                            <div class="flex h-full flex-col justify-between rounded-xl bg-brand-bg p-6">
                                <div>
                                    <div class="flex items-center gap-3">
                                        @if ($testimonial->avatar)
                                            <img src="{{ image_url($testimonial->avatar) }}" class="h-10 w-10 rounded-full object-cover shrink-0" alt="{{ $testimonial->name ?? $testimonial->phone }}">
                                        @else
                                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-navy font-serif text-sm font-semibold text-white">
                                                <i class="fa-solid fa-user"></i>
                                            </span>
                                        @endif
                                        <div>
                                            <p class="text-sm font-semibold text-brand-navy">{{ $testimonial->name ?? $testimonial->phone ?? 'Customer' }}</p>
                                            @if($testimonial->location)
                                                <p class="text-xs text-brand-navy/50">{{ $testimonial->location }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mt-3 text-xs text-brand-orange">
                                        @for ($i = 0; $i < 5; $i++)
                                            <i class="fa-{{ $i < $testimonial->rating ? 'solid' : 'regular' }} fa-star"></i>
                                        @endfor
                                    </div>

                                    <p class="mt-3 text-sm leading-relaxed text-brand-navy/70">{{ $testimonial->text }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if(count($testimonials) > 0)
                    <div class="testi-pagination flex justify-center items-center mt-6"></div>
                @endif
            </div>

        </div>
    </section>
</x-layout>
