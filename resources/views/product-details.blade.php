<x-layout
    :title="($product->meta_title ?: $product->name).' - Raimart'"
    :description="$product->meta_description ?: $product->short_description"
    :image="$product->main_image"
>
    @php
        $phone = preg_replace('/\D+/', '', \App\Models\SiteSetting::get('contact_phone', ''));
        $waPhone = '8801622243011';
        $waMsg = urlencode("Assalamu Alaikum, I would like to order: {$product->name} (৳" . number_format($product->price) . "). Link: " . route('products.show', $product));

        // Build a de-duplicated media list (main image first, then gallery)
        $media = collect();
        if ($product->main_image) {
            $media->push(['url' => image_url($product->main_image, urlencode($product->name)), 'type' => is_video($product->main_image) ? 'video' : 'image']);
        }
        foreach ($product->images as $img) {
            $u = image_url($img->image);
            if (! $media->contains(fn ($m) => $m['url'] === $u)) {
                $media->push(['url' => $u, 'type' => is_video($img->image) ? 'video' : 'image']);
            }
        }
        $media = $media->values()->all();

        $variantsData = $product->variants->map(function($v) {
            $firstImg = !empty($v->images) && is_array($v->images) ? image_url($v->images[0]) : null;
            $name = collect($v->attributes)->map(fn($val, $key) => "$val")->join(' / ');
            return [
                'id' => $v->id,
                'sku' => $v->sku,
                'current_price' => (float)$v->current_price,
                'original_price' => $v->original_price ? (float)$v->original_price : null,
                'stock' => (int)$v->stock,
                'attributes' => $v->attributes,
                'name' => $name ?: 'Option',
                'image' => $firstImg,
            ];
        })->values()->all();

        $showVariantImage = !empty($product->attributes['show_variant_image']);
    @endphp

    @php
        $schema = [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $product->name,
            'image' => array_column($media, 'url'),
            'description' => strip_tags($product->short_description ?: $product->description ?: $product->name),
            'sku' => $product->sku ?: (string)$product->id,
            'offers' => [
                '@type' => 'Offer',
                'url' => route('products.show', $product),
                'priceCurrency' => 'BDT',
                'price' => $product->price,
                'priceValidUntil' => now()->addYear()->format('Y-m-d'),
                'itemCondition' => 'https://schema.org/NewCondition',
                'availability' => $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            ],
        ];

        if ($product->brand) {
            $schema['brand'] = [
                '@type' => 'Brand',
                'name' => $product->brand->name,
            ];
        }

        if ($product->reviews_count > 0 && $product->rating > 0) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => (float)$product->rating,
                'reviewCount' => (int)$product->reviews_count,
            ];
            
            if (isset($reviews) && $reviews->count() > 0) {
                $schema['review'] = $reviews->map(function($review) {
                    return [
                        '@type' => 'Review',
                        'reviewRating' => [
                            '@type' => 'Rating',
                            'ratingValue' => (int)$review->rating,
                            'bestRating' => '5',
                        ],
                        'author' => [
                            '@type' => 'Person',
                            'name' => $review->name,
                        ],
                        'reviewBody' => $review->text,
                        'datePublished' => $review->created_at->format('Y-m-d'),
                    ];
                })->toArray();
            }
        }
        
        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Home',
                    'item' => route('home')
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => $product->category->name,
                    'item' => route('shop', ['category' => $product->category->slug])
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $product->name,
                    'item' => route('products.show', $product)
                ]
            ]
        ];
    @endphp

    <script type="application/ld+json">
        {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    <script type="application/ld+json">
        {!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    {{-- GA4: view_item --}}
    <x-ga-event event="view_item" :ecommerce="[
        'currency' => \App\Support\Ga4::currency(),
        'value' => round((float) $product->price, 2),
        'items' => [\App\Support\Ga4::item($product)],
    ]" />

    <div class="bg-[#F8F9FA] min-h-screen py-3 sm:py-5"
        x-data="{
            items: {{ Illuminate\Support\Js::from($media) }},
            variants: {{ Illuminate\Support\Js::from($variantsData) }},
            showVariantImage: {{ $showVariantImage ? 'true' : 'false' }},
            idx: 0,
            isZoomed: false,
            selectedVariant: null,
            selectedPrice: '{{ number_format($product->price) }}',
            selectedOldPrice: '{{ $product->old_price ? number_format($product->old_price) : '' }}',
            selectedSku: '{{ $product->sku ?: 'RM-'.$product->id }}',
            qty: 1,
            maxQty: {{ max(1, (int)$product->stock) }},
            shareOpen: false,
            copied: false,
            get current() { return this.items[this.idx] || { url: '', type: 'image' } },
            go(i) { this.idx = (i + this.items.length) % this.items.length },
            selectVariant(v) {
                this.selectedVariant = v;
                this.selectedPrice = Number(v.current_price).toLocaleString();
                this.selectedOldPrice = v.original_price ? Number(v.original_price).toLocaleString() : '';
                if (v.sku) this.selectedSku = v.sku;
                if (v.stock) this.maxQty = v.stock;
                if (v.image) {
                    let fIdx = this.items.findIndex(it => it.url === v.image);
                    if (fIdx >= 0) {
                        this.idx = fIdx;
                    } else {
                        this.items.unshift({ url: v.image, type: 'image' });
                        this.idx = 0;
                    }
                }
            },
            copyShareLink() {
                navigator.clipboard.writeText(window.location.href).then(() => {
                    this.copied = true;
                    setTimeout(() => { this.copied = false; }, 2500);
                });
            },
            thumbSwiper: null,
            initThumbSwiper() {
                if (window.Swiper && this.$refs.thumbSwiperEl) {
                    if (this.thumbSwiper) {
                        try { this.thumbSwiper.destroy(true, true); } catch(e) {}
                    }
                    this.thumbSwiper = new window.Swiper(this.$refs.thumbSwiperEl, {
                        slidesPerView: 4,
                        spaceBetween: 8,
                        watchOverflow: true,
                        navigation: {
                            prevEl: this.$refs.thumbPrevBtn,
                            nextEl: this.$refs.thumbNextBtn,
                        },
                        breakpoints: {
                            640: { slidesPerView: 5, spaceBetween: 10 },
                            1024: { slidesPerView: 5, spaceBetween: 10 }
                        }
                    });
                }
            },
            init() {
                this.$nextTick(() => {
                    this.initThumbSwiper();
                });
                this.$watch('idx', (val) => {
                    if (this.thumbSwiper && typeof this.thumbSwiper.slideTo === 'function') {
                        this.thumbSwiper.slideTo(val);
                    }
                });
            },
            triggerShare() {
                if (navigator.share) {
                    navigator.share({
                        title: '{{ addslashes($product->name) }}',
                        url: window.location.href,
                    }).catch(() => {});
                } else {
                    this.shareOpen = !this.shareOpen;
                }
            }
        }">

        <div class="gb-container">
            {{-- Breadcrumb --}}
            <nav class="mb-3 flex items-center text-xs sm:text-sm text-gray-500 overflow-x-auto whitespace-nowrap scrollbar-hide py-0.5">
                <a href="{{ route('home') }}" wire:navigate class="hover:text-brand-orange transition-colors cursor-pointer">Home</a>
                <span class="mx-2 text-gray-300">/</span>
                <a href="{{ route('shop', ['category' => $product->category->slug]) }}" wire:navigate class="hover:text-brand-orange transition-colors cursor-pointer">{{ $product->category->name }}</a>
                <span class="mx-2 text-gray-300">/</span>
                <span class="text-gray-800 font-medium truncate max-w-xs sm:max-w-md">{{ $product->name }}</span>
            </nav>

            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-xs sm:text-sm text-red-600">
                    <ul class="list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Main Product Card: Top-aligned and clean --}}
            <div class="">
                <div class="w-full flex flex-col lg:flex-row gap-6 lg:gap-12 bg-white rounded-2xl border border-gray-100 p-4 sm:p-6 lg:p-5">
                    
                    {{-- Left Column: Product Image & Gallery --}}
                    <div class="w-full lg:w-4/12">
                        {{-- Main Image Box --}}
                        <div class="group relative flex items-center justify-center overflow-hidden rounded-2xl border border-gray-100 bg-[#FAFAFA] w-full aspect-square"
                             style="">
                            <template x-if="current.type === 'image'">
                                <img :src="current.url" @click="isZoomed = true" alt="{{ $product->name }}" class="h-full w-full object-cover aspect-square transition-transform cursor-pointer">
                            </template>
                            <template x-if="current.type === 'video'">
                                <video :src="current.url" controls class="h-full w-full object-cover bg-black cursor-default"></video>
                            </template>
                            {{-- Save Discount Badge on Top-Left of Main Active Image --}}
                            @if ($product->discount_percent)
                                <div class="absolute left-2 top-2 z-10 pointer-events-none">
                                    <span class="rounded-full bg-red-500 px-3 py-1 text-xs font-bold text-white shadow-sm">
                                        Save {{ $product->discount_percent }}%
                                    </span>
                                </div>
                            @endif

                            {{-- Zoom Button --}}
                            <button @click="isZoomed = true" type="button" x-show="current.type === 'image'" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-white/90 text-gray-700 shadow-md opacity-0 group-hover:opacity-100 transition-all duration-300 hover:bg-brand-orange hover:text-white cursor-pointer" aria-label="Zoom image">
                                <i class="fa-solid fa-expand text-base"></i>
                            </button>

                            {{-- Prev / Next Navigation Arrows --}}
                            <template x-if="items.length > 1">
                                <div>
                                    <button type="button" @click="go(idx - 1)" class="absolute left-3 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-gray-700 shadow-md opacity-0 group-hover:opacity-100 transition-all duration-300 hover:bg-brand-orange hover:text-white cursor-pointer" aria-label="Previous image">
                                        <i class="fa-solid fa-chevron-left text-sm"></i>
                                    </button>
                                    <button type="button" @click="go(idx + 1)" class="absolute right-3 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-gray-700 shadow-md opacity-0 group-hover:opacity-100 transition-all duration-300 hover:bg-brand-orange hover:text-white cursor-pointer" aria-label="Next image">
                                        <i class="fa-solid fa-chevron-right text-sm"></i>
                                    </button>
                                </div>
                            </template>
                        </div>

                        {{-- Thumbnails Slider/Row with Swiper --}}
                        <template x-if="items.length > 1">
                            <div class="mt-3.5 sm:mt-4 flex items-center gap-1.5 sm:gap-2 w-full max-w-[550px]">
                                <button type="button" 
                                    x-ref="thumbPrevBtn"
                                    x-show="items.length > 4" 
                                    class="flex-shrink-0 flex h-7 w-7 sm:h-8 sm:w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-brand-orange transition cursor-pointer shadow-xs disabled:opacity-30 disabled:pointer-events-none" 
                                    aria-label="Previous thumbnails">
                                    <i class="fa-solid fa-chevron-left text-[11px] sm:text-xs"></i>
                                </button>

                                <div x-ref="thumbSwiperEl" class="swiper product-thumb-swiper flex-1 overflow-hidden py-1">
                                    <div class="swiper-wrapper">
                                        <template x-for="(m, i) in items" :key="i">
                                            <div class="swiper-slide">
                                                <button type="button" @click="idx = i; if(thumbSwiper) thumbSwiper.slideTo(i)" 
                                                    class="relative w-full aspect-square overflow-hidden rounded-xl border-2 bg-white transition-all duration-200 cursor-pointer flex items-center justify-center"
                                                    :class="idx === i ? 'border-brand-orange ring-2 ring-brand-orange/30 shadow-sm' : 'border-gray-200 hover:border-gray-400 opacity-70 hover:opacity-100'">
                                                    <template x-if="m.type === 'image'">
                                                        <img :src="m.url" class="h-full w-full object-cover">
                                                    </template>
                                                    <template x-if="m.type === 'video'">
                                                        <div class="relative h-full w-full bg-gray-900 flex items-center justify-center">
                                                            <i class="fa-solid fa-play text-white text-xs"></i>
                                                        </div>
                                                    </template>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <button type="button" 
                                    x-ref="thumbNextBtn"
                                    x-show="items.length > 4" 
                                    class="flex-shrink-0 flex h-7 w-7 sm:h-8 sm:w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-brand-orange transition cursor-pointer shadow-xs disabled:opacity-30 disabled:pointer-events-none" 
                                    aria-label="Next thumbnails">
                                    <i class="fa-solid fa-chevron-right text-[11px] sm:text-xs"></i>
                                </button>
                            </div>
                        </template>

                        {{-- Zoom Modal --}}
                        <div x-show="isZoomed" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
                             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                            <button @click="isZoomed = false" type="button" class="absolute right-5 top-5 z-[110] flex h-10 w-10 items-center justify-center rounded-full bg-white/20 text-white hover:bg-white/40 cursor-pointer" aria-label="Close">
                                <i class="fa-solid fa-xmark text-xl"></i>
                            </button>
                            <div class="relative flex h-full w-full items-center justify-center" @click.outside="isZoomed = false">
                                <img :src="current.url" class="max-h-[90vh] max-w-full object-contain rounded-2xl shadow-2xl">
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Product Info & Actions --}}
                    <div class="w-full lg:w-7/12">
                        {{-- Title & Category Header --}}
                        <div>
                            <h1 class="text-lg sm:text-2xl lg:text-[32px] font-semibold text-black leading-snug tracking-tight">
                                {{ $product->name }}
                            </h1>

                            {{-- Desktop Meta Info (Category, Brand, Ratings, Short Description) --}}
                            <div class="hidden lg:block">
                                <div class="mt-2.5 flex items-center gap-2 text-xs sm:text-sm text-gray-600 flex-wrap">
                                    <span>Category:</span>
                                    <a href="{{ route('shop', ['category' => $product->category->slug]) }}" wire:navigate class="font-semibold text-brand-orange hover:underline cursor-pointer">
                                        {{ $product->category->name }}
                                    </a>
                                    @if($product->brand)
                                        <span class="text-gray-300">•</span>
                                        <span>Brand:</span>
                                        <span class="font-semibold text-gray-800">{{ $product->brand->name }}</span>
                                    @endif
                                </div>

                                <div class="mt-2 flex items-center gap-2.5 text-xs sm:text-sm text-gray-500 flex-wrap">
                                    <div class="flex items-center text-amber-400">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fa-{{ $i <= round($product->rating) ? 'solid' : 'regular' }} fa-star text-xs"></i>
                                        @endfor
                                    </div>
                                    <span>({{ number_format($product->rating, 1) }} / 5)</span>
                                    <span class="text-gray-300">•</span>
                                    <span>{{ $product->reviews_count }} Ratings</span>
                                    <span class="text-gray-300">|</span>
                                    <span class="text-gray-300">•</span>
                                    <span>Sold: <strong class="text-gray-500 font-semibold">{{ max(1, $product->order_items_count ?? 1) }} units</strong></span>
                                </div>

                                @if($product->short_description)
                                    <div class="text-xs sm:text-sm max-w-[87%] leading-[26px] text-gray-600 border-t border-gray-100 mt-3 pt-2 mb-2 line-clamp-3">
                                        {{ $product->short_description }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Price Highlight Box (Raimart Brand Orange Theme) --}}
                        <div class="py-3.5 flex items-center gap-3.5 flex-wrap">
                            <span class="text-xl sm:text-3xl font-semibold flex items-baseline gap-1" style="color: #F58502;">
                                <span>৳</span><span x-text="selectedPrice">{{ number_format($product->price) }}</span>
                            </span>

                            <template x-if="selectedOldPrice">
                                <span class="text-lg sm:text-xl pt-2 text-gray-400 line-through">
                                    ৳<span x-text="selectedOldPrice"></span>
                                </span>
                            </template>

                        </div>

                        {{-- Add to Cart Form --}}
                        <form action="{{ route('cart.add', $product) }}" method="POST" data-add-to-cart data-product-name="{{ $product->name }}" class="space-y-3.5">
                            @csrf

                            {{-- Variant Image Swatches (When show_variant_image is active or variants have images) --}}
                            <template x-if="variants.length > 0 && showVariantImage">
                                <div class="space-y-2 border-b border-gray-100 pb-3">
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700">
                                        Select Option: <span class="text-brand-orange font-bold" x-text="selectedVariant ? selectedVariant.name : 'Choose one'"></span>
                                    </label>
                                    <div class="flex flex-wrap gap-2.5">
                                        <template x-for="(v, vIdx) in variants" :key="v.id || vIdx">
                                            <button type="button" @click="selectVariant(v)"
                                                class="flex items-center gap-2 p-1.5 pr-3 rounded-xl border-2 transition-all bg-white cursor-pointer"
                                                :class="selectedVariant?.id === v.id ? 'border-brand-orange ring-2 ring-brand-orange/20 shadow-sm' : 'border-gray-200 hover:border-gray-400'">
                                                <template x-if="v.image">
                                                    <img :src="v.image" class="h-9 w-9 rounded-lg object-cover">
                                                </template>
                                                <template x-if="!v.image">
                                                    <div class="h-9 w-9 rounded-lg bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-600">
                                                        <i class="fa-regular fa-image"></i>
                                                    </div>
                                                </template>
                                                <div class="text-left">
                                                    <p class="text-xs font-semibold text-gray-800" x-text="v.name"></p>
                                                    <p class="text-xs font-semibold" style="color: #F58502;">৳<span x-text="Number(v.current_price).toLocaleString()"></span></p>
                                                </div>
                                            </button>
                                        </template>
                                    </div>
                                    <input type="hidden" name="variant_id" :value="selectedVariant?.id || ''">
                                </div>
                            </template>

                            {{-- Standard Dynamic Attributes (When show_variant_image is false or for classic options) --}}
                            @if($product->attributes && empty($product->attributes['show_variant_image']))
                                <div class="space-y-2.5 border-b border-gray-100 pb-3">
                                    @foreach($product->attributes as $attribute)
                                        @php
                                            if (!isset($attribute['name'])) continue;
                                            $optionsArray = array_values(array_filter(array_map('trim', explode(',', $attribute['values'] ?? ''))));
                                        @endphp
                                        @if(count($optionsArray) > 0)
                                            <div>
                                                @if(count($optionsArray) == 1)
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-700">{{ $attribute['name'] }}:</span>
                                                        <span class="text-xs font-semibold bg-gray-100 text-gray-800 px-2.5 py-1 rounded-md">{{ $optionsArray[0] }}</span>
                                                    </div>
                                                    <input type="hidden" name="options[{{ $attribute['name'] }}]" value="{{ $optionsArray[0] }}">
                                                @else
                                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">{{ $attribute['name'] }} <span class="text-red-500">*</span></label>
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach($optionsArray as $optIndex => $option)
                                                            <label class="cursor-pointer">
                                                                <input type="radio" name="options[{{ $attribute['name'] }}]" value="{{ $option }}" @checked($optIndex === 0) class="peer sr-only">
                                                                <span class="inline-block px-3.5 py-1.5 text-xs font-semibold rounded-lg border-2 border-gray-200 peer-checked:border-brand-orange peer-checked:bg-brand-orange/5 peer-checked:text-brand-orange hover:border-gray-300 transition-colors cursor-pointer">
                                                                    {{ $option }}
                                                                </span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            {{-- Action Controls: 2 Rows Layout --}}
                            <div class="space-y-2">
                                {{-- Row 1: Quantity Selector + WhatsApp Button --}}
                                <div class="flex items-center gap-3">
                                    {{-- Quantity Selector (Count product barano komano) --}}
                                    <div class="flex h-11 w-32 items-center justify-between rounded-xl border border-gray-300 bg-white px-2.5 shadow-xs flex-shrink-0">
                                        <button type="button" @click="if(qty > 1) qty--" class="h-8 w-8 flex items-center justify-center text-gray-500 hover:text-gray-900 transition-colors cursor-pointer">
                                            <i class="fa-solid fa-minus text-xs"></i>
                                        </button>
                                        <input type="number" name="quantity" x-model="qty" min="1" :max="maxQty" class="w-8 text-center font-bold text-gray-800 border-none p-0 focus:outline-none bg-transparent text-base" readonly>
                                        <button type="button" @click="if(qty < maxQty) qty++" class="h-8 w-8 flex items-center justify-center text-gray-500 hover:text-gray-900 transition-colors cursor-pointer">
                                            <i class="fa-solid fa-plus text-xs"></i>
                                        </button>
                                    </div>

                                    {{-- WhatsApp Button --}}
                                    <a href="https://wa.me/{{ $waPhone }}?text={{ $waMsg }}" target="_blank" rel="noopener"
                                        style="background-color: #25D366; color: #ffffff;"
                                        class="h-11 flex-1 sm:flex-initial sm:w-36 flex items-center justify-center rounded-[10px] text-white shadow-md shadow-emerald-500/20 hover:scale-105 active:scale-95 transition-all cursor-pointer"
                                        title="WhatsApp">
                                        <i class="fa-brands fa-whatsapp text-2xl text-white"></i>
                                    </a>

                                    {{-- Wishlist Button --}}
                                    @php
                                        $isWishlisted = in_array($product->id, $wishlistIds ?? []);
                                    @endphp
                                    <button type="button"
                                        data-wishlist-toggle
                                        data-toggle-url="{{ route('wishlist.toggle', $product) }}"
                                        aria-label="{{ $isWishlisted ? 'Remove from wishlist' : 'Add to wishlist' }}"
                                        aria-pressed="{{ $isWishlisted ? 'true' : 'false' }}"
                                        class="wishlist-btn h-11 w-11 flex-shrink-0 flex items-center justify-center rounded-[10px] border border-gray-200 bg-white shadow-xs hover:border-brand-orange hover:text-brand-orange transition-all cursor-pointer {{ $isWishlisted ? 'text-brand-orange' : 'text-gray-400' }}">
                                        <i class="fa-solid fa-heart text-lg" style="{{ $isWishlisted ? '' : 'display: none;' }}"></i>
                                        <i class="fa-regular fa-heart text-lg" style="{{ $isWishlisted ? 'display: none;' : '' }}"></i>
                                    </button>
                                </div>

                                {{-- Row 2: Add to Cart + Buy Now --}}
                                <div class="grid grid-cols-2 gap-3 sm:gap-4 mt-3">
                                    {{-- Add to Cart Button (Theme Brand Orange #F58502) --}}
                                    <button type="submit" @disabled($product->stock <= 0)
                                        style="background-color: #F58502; color: #ffffff;"
                                        class="py-3 flex items-center justify-center gap-2 rounded-xl font-bold text-sm sm:text-base tracking-wide transition shadow-sm hover:opacity-90 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                        <i class="fa-solid fa-cart-shopping text-base text-white"></i>
                                        <span class="text-white">Add to Cart</span>
                                    </button>

                                    {{-- Buy Now Button (Theme Brand Navy #011939 - Clean, No Icon) --}}
                                    <button type="submit" name="redirect" value="checkout" @disabled($product->stock <= 0)
                                        style="background-color: #011939; color: #ffffff;"
                                        class="py-[13px] flex items-center justify-center rounded-xl font-bold text-sm sm:text-base tracking-wide transition shadow-sm hover:opacity-90 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span class="text-white">Buy Now</span>
                                    </button>
                                </div>
                            </div>
                        </form>

                        {{-- Mobile Meta Info (Category, Brand, Ratings, Short Description) - Displayed after Action Buttons and before SKU on Mobile --}}
                        <div class="block lg:hidden mt-5 pt-3.5 border-t border-gray-100 space-y-2.5">
                            <div class="flex items-center gap-2 text-xs sm:text-sm text-gray-600 flex-wrap">
                                <span>Category:</span>
                                <a href="{{ route('shop', ['category' => $product->category->slug]) }}" wire:navigate class="font-semibold text-brand-orange hover:underline cursor-pointer">
                                    {{ $product->category->name }}
                                </a>
                                @if($product->brand)
                                    <span class="text-gray-300">•</span>
                                    <span>Brand:</span>
                                    <span class="font-semibold text-gray-800">{{ $product->brand->name }}</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-2.5 text-xs sm:text-sm text-gray-500 flex-wrap">
                                <div class="flex items-center text-amber-400">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fa-{{ $i <= round($product->rating) ? 'solid' : 'regular' }} fa-star text-xs"></i>
                                    @endfor
                                </div>
                                <span>({{ number_format($product->rating, 1) }} / 5)</span>
                                <span class="text-gray-300">•</span>
                                <span>{{ $product->reviews_count }} Ratings</span>
                                <span class="text-gray-300">|</span>
                                <span class="text-gray-300">•</span>
                                <span>Sold: <strong class="text-gray-500 font-semibold">{{ max(1, $product->order_items_count ?? 1) }} units</strong></span>
                            </div>

                            @if($product->short_description)
                                <div class="text-xs sm:text-sm leading-relaxed text-gray-600 border-t border-gray-100 pt-2 line-clamp-3">
                                    {{ $product->short_description }}
                                </div>
                            @endif
                        </div>

                        {{-- SKU & Share Section --}}
                        <div class="mt-4 lg:mt-6 ml-2">
                            <p class="text-xs sm:text-sm text-black font-medium">
                                SKU: <span class="text-gray-400 font-semibold" x-text="selectedSku">{{ $product->sku ?: 'RM-'.$product->id }}</span>
                            </p>

                            {{-- Share Product Full Options --}}
                            <div class="relative mt-2.5 pt-2.5 border-t border-gray-100">
                                <button type="button" @click="triggerShare()" class="inline-flex items-center gap-2 text-xs sm:text-sm font-medium text-gray-500 hover:text-brand-orange transition-colors cursor-pointer group">
                                    <svg class="w-4 h-4 text-gray-500 mt-[2px] group-hover:text-brand-orange transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="18" cy="5" r="3"></circle>
                                        <circle cx="6" cy="12" r="3"></circle>
                                        <circle cx="18" cy="19" r="3"></circle>
                                        <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                                        <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                                    </svg>
                                    <span>Share Product</span>
                                </button>

                                {{-- Share Menu / Popover --}}
                                <div x-show="shareOpen" @click.outside="shareOpen = false" style="display: none;"
                                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                    class="absolute right-0 bottom-full mb-2 p-3 bg-white rounded-xl border border-gray-200 shadow-xl z-30 min-w-[260px]">
                                    <p class="text-xs font-semibold text-gray-700 mb-2">Share this product:</p>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        {{-- Facebook --}}
                                        <a :href="'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(window.location.href)" target="_blank" rel="noopener"
                                            style="background-color: #1877F2;"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg text-white hover:opacity-90 transition shadow-sm cursor-pointer" title="Share on Facebook">
                                            <i class="fa-brands fa-facebook-f text-xs text-white"></i>
                                        </a>
                                        {{-- WhatsApp --}}
                                        <a :href="'https://api.whatsapp.com/send?text=' + encodeURIComponent('{{ addslashes($product->name) }}: ') + encodeURIComponent(window.location.href)" target="_blank" rel="noopener"
                                            style="background-color: #25D366;"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg text-white hover:opacity-90 transition shadow-sm cursor-pointer" title="Share on WhatsApp">
                                            <i class="fa-brands fa-whatsapp text-xs text-white"></i>
                                        </a>
                                        {{-- Twitter / X --}}
                                        <a :href="'https://twitter.com/intent/tweet?text=' + encodeURIComponent('{{ addslashes($product->name) }}') + '&url=' + encodeURIComponent(window.location.href)" target="_blank" rel="noopener"
                                            style="background-color: #000000;"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg text-white hover:opacity-90 transition shadow-sm cursor-pointer" title="Share on X">
                                            <i class="fa-brands fa-x-twitter text-xs text-white"></i>
                                        </a>
                                        {{-- Telegram --}}
                                        <a :href="'https://t.me/share/url?url=' + encodeURIComponent(window.location.href) + '&text=' + encodeURIComponent('{{ addslashes($product->name) }}')" target="_blank" rel="noopener"
                                            style="background-color: #229ED9;"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg text-white hover:opacity-90 transition shadow-sm cursor-pointer" title="Share on Telegram">
                                            <i class="fa-brands fa-telegram text-xs text-white"></i>
                                        </a>
                                        {{-- Copy Link Button --}}
                                        <button type="button" @click="copyShareLink()"
                                            class="flex items-center gap-1 px-2.5 h-8 rounded-lg border border-gray-300 bg-gray-50 hover:bg-gray-100 text-[11px] font-semibold text-gray-700 transition cursor-pointer">
                                            <i class="fa-solid fa-link text-xs"></i>
                                            <span x-text="copied ? 'Copied!' : 'Copy'">Copy</span>
                                        </button>
                                    </div>
                                    <template x-if="copied">
                                        <p class="text-[11px] text-emerald-600 font-semibold mt-1.5">
                                            <i class="fa-solid fa-check mr-1"></i> Product link copied!
                                        </p>
                                    </template>
                                </div>
                            </div>
                        </div>

                    </div>
            </div>

            {{-- Description / Reviews Tabs --}}
            <div class="mt-8 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm" x-data="{ tab: '{{ $errors->any() || session('status') ? 'reviews' : 'desc' }}' }">
                <div class="flex overflow-x-auto border-b border-gray-100 whitespace-nowrap scrollbar-hide px-2 sm:px-4">
                    <button type="button" @click="tab = 'desc'"
                        :class="tab === 'desc' ? 'border-brand-orange text-brand-navy font-bold' : 'border-transparent text-gray-500 hover:text-gray-800'"
                        class="border-b-2 px-4 sm:px-6 py-3 sm:py-3.5 text-xs sm:text-sm font-semibold transition cursor-pointer">
                        Description
                    </button>
                    <button type="button" @click="tab = 'reviews'"
                        :class="tab === 'reviews' ? 'border-brand-orange text-brand-navy font-bold' : 'border-transparent text-gray-500 hover:text-gray-800'"
                        class="border-b-2 px-4 sm:px-6 py-3 sm:py-3.5 text-xs sm:text-sm font-semibold transition cursor-pointer">
                        Customer Reviews ({{ $reviews->count() }})
                    </button>
                </div>

                {{-- Description panel with Dynamic Accordion --}}
                <div x-show="tab === 'desc'" class="p-4 sm:p-6 lg:p-7">
                    @if($product->description)
                        @php
                            $descHtml = trim($product->description);
                            $accordionSections = [];

                            // Heading pattern matching bold lines and h1-h6 tags
                            $headingPattern = '/(?:<h[1-6][^>]*>(?:<[^>]+>)*([^<]+)(?:<\/[^>]+>)*<\/h[1-6]>|<p[^>]*>\s*<(?:strong|b)[^>]*>(?:<[^>]+>)*([^<]+)(?:<\/[^>]+>)*<\/(?:strong|b)>\s*:?\s*<\/p>|<p[^>]*>\s*<(?:strong|b)[^>]*>(?:<[^>]+>)*([^<]+)(?:<\/[^>]+>)*<\/(?:strong|b)>\s*:?\s*<br\s*\/?>)/i';

                            preg_match_all($headingPattern, $descHtml, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

                            if (empty($matches)) {
                                $accordionSections[] = [
                                    'title' => 'Description',
                                    'content' => $descHtml,
                                ];
                            } else {
                                $firstMatchOffset = $matches[0][0][1];
                                if ($firstMatchOffset > 0) {
                                    $preContent = trim(substr($descHtml, 0, $firstMatchOffset));
                                    if ($preContent !== '' && strip_tags($preContent) !== '') {
                                        $accordionSections[] = [
                                            'title' => 'Description',
                                            'content' => $preContent,
                                        ];
                                    }
                                }

                                for ($i = 0; $i < count($matches); $i++) {
                                    $currentMatch = $matches[$i];
                                    $fullMatch = $currentMatch[0][0];
                                    $startOffset = $currentMatch[0][1];

                                    $titleRaw = '';
                                    for ($g = 1; $g < count($currentMatch); $g++) {
                                        if (isset($currentMatch[$g]) && $currentMatch[$g][1] !== -1 && trim($currentMatch[$g][0]) !== '') {
                                            $titleRaw = $currentMatch[$g][0];
                                            break;
                                        }
                                    }
                                    $title = rtrim(trim(strip_tags($titleRaw)), ' :');

                                    $contentStart = $startOffset + strlen($fullMatch);
                                    if ($i + 1 < count($matches)) {
                                        $nextOffset = $matches[$i + 1][0][1];
                                        $content = substr($descHtml, $contentStart, $nextOffset - $contentStart);
                                    } else {
                                        $content = substr($descHtml, $contentStart);
                                    }

                                    $content = trim($content);
                                    if ($title === '') {
                                        $title = 'Details';
                                    }

                                    // If match consumed <p> with <br>, ensure opening <p> is preserved
                                    if (str_starts_with(trim($fullMatch), '<p') && preg_match('/<br\s*\/?>$/i', trim($fullMatch))) {
                                        $content = '<p>' . $content;
                                    }

                                    $accordionSections[] = [
                                        'title' => $title,
                                        'content' => $content,
                                    ];
                                }
                            }

                            if (count($accordionSections) === 1 && empty($accordionSections[0]['title'])) {
                                $accordionSections[0]['title'] = 'Description';
                            }
                        @endphp

                        <div x-data="{
                            activeSection: 0,
                            toggle(idx) {
                                this.activeSection = (this.activeSection === idx) ? null : idx;
                            }
                        }" class="divide-y divide-gray-200">
                            @foreach($accordionSections as $idx => $sec)
                                @php
                                    $hasBody = trim(strip_tags($sec['content'])) !== '';
                                @endphp
                                <div class="py-3.5 sm:py-4">
                                    @if($hasBody)
                                        <button type="button" 
                                            @click="toggle({{ $idx }})" 
                                            class="w-full flex items-center justify-between text-left group cursor-pointer focus:outline-none">
                                            <span class="text-base sm:text-lg font-bold text-gray-900 group-hover:text-brand-orange transition-colors">
                                                {{ $sec['title'] }}
                                            </span>
                                            <span class="flex-shrink-0 ml-4 flex items-center justify-center w-6 h-6 text-gray-900">
                                                @if($idx === 0)
                                                    <i class="fa-solid fa-minus text-sm text-gray-900" x-show="activeSection === 0"></i>
                                                    <i class="fa-solid fa-plus text-sm text-gray-900" x-show="activeSection !== 0" style="display: none;"></i>
                                                @else
                                                    <i class="fa-solid fa-minus text-sm text-gray-900" x-show="activeSection === {{ $idx }}" style="display: none;"></i>
                                                    <i class="fa-solid fa-plus text-sm text-gray-900" x-show="activeSection !== {{ $idx }}"></i>
                                                @endif
                                            </span>
                                        </button>
                                        <div 
                                            @if($idx === 0)
                                                x-show="activeSection === 0"
                                            @else
                                                x-show="activeSection === {{ $idx }}"
                                                style="display: none;"
                                            @endif
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 -translate-y-1"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            class="pt-3 pb-1 text-sm sm:text-base leading-relaxed text-gray-700 prose prose-brand max-w-none">
                                            {!! $sec['content'] !!}
                                        </div>
                                    @else
                                        {{-- Standalone Informational Row (e.g. Barcode: 8941153903133) --}}
                                        <div class="flex items-center justify-between text-left py-0.5">
                                            <span class="text-base sm:text-lg font-bold text-gray-900">
                                                {{ $sec['title'] }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No description available for this product.</p>
                    @endif
                </div>

                {{-- Reviews panel --}}
                <div x-show="tab === 'reviews'" style="display: none;" class="p-4 sm:p-7">
                    <div class="max-w-4xl w-full">
                        @if (session('status'))
                            <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-xs sm:text-sm text-green-700 flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-green-600"></i>
                                <span>{{ session('status') }}</span>
                            </div>
                        @endif

                        {{-- Rating Overview & Breakdown --}}
                        <div class="rounded-2xl border border-gray-100 bg-gray-50/70 p-4 sm:p-6 lg:p-8 mb-6 sm:mb-8">
                            <div class="grid gap-5 sm:gap-6 md:grid-cols-[180px_1fr] md:items-center">
                                {{-- Score Summary --}}
                                <div class="text-left border-b border-gray-200/70 pb-4 md:border-b-0 md:pb-0 md:border-r md:border-gray-200/80 md:pr-6">
                                    <p class="text-4xl sm:text-5xl font-black text-gray-900 tracking-tight leading-none">{{ number_format($product->rating, 1) }}</p>
                                    <div class="mt-2 flex items-center justify-start gap-1 text-amber-400 text-base sm:text-lg">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fa-{{ $i <= round($product->rating) ? 'solid' : 'regular' }} fa-star"></i>
                                        @endfor
                                    </div>
                                    <p class="mt-1.5 text-xs sm:text-sm text-gray-500 font-medium">Based on {{ $reviews->count() }} {{ Str::plural('review', $reviews->count()) }}</p>
                                </div>

                                {{-- Star Distribution Bars (Balanced Width) --}}
                                <div class="space-y-2 sm:space-y-2.5 max-w-md w-full">
                                    @foreach ([5, 4, 3, 2, 1] as $star)
                                        @php
                                            $count = $ratingCounts[$star] ?? 0;
                                            $percent = $reviews->count() > 0 ? round(($count / $reviews->count()) * 100) : 0;
                                        @endphp
                                        <div class="flex items-center gap-2 sm:gap-3 text-xs sm:text-sm">
                                            <span class="w-8 sm:w-10 text-gray-700 font-semibold flex items-center gap-1">
                                                {{ $star }} <i class="fa-solid fa-star text-amber-400 text-[10px] sm:text-[11px]"></i>
                                            </span>
                                            <div class="h-2 sm:h-2.5 flex-1 overflow-hidden rounded-full bg-gray-200/80">
                                                <div class="h-full rounded-full bg-amber-400 transition-all duration-500" style="width: {{ $percent }}%"></div>
                                            </div>
                                            <span class="w-7 sm:w-10 text-right text-gray-500 font-medium">{{ $count }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Write a Review Form --}}
                        <div class="rounded-2xl border border-gray-200/80 bg-white p-4 sm:p-6 lg:p-8 shadow-xs">
                            <div class="border-b border-gray-100 pb-3.5 mb-5">
                                <h3 class="text-base sm:text-xl font-bold text-gray-900">Write a Review</h3>
                                <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Share your experience with this product with other shoppers.</p>
                            </div>

                            <form action="{{ route('products.reviews.store', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-4 sm:space-y-5">
                                @csrf

                                {{-- Interactive Star Rating (Mobile responsive flex) --}}
                                <div x-data="{ rating: 5, hover: 0 }" class="bg-amber-50/50 border border-amber-100 rounded-xl p-3 sm:p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div>
                                        <label class="block text-xs sm:text-sm font-bold text-gray-800">Your Rating *</label>
                                        <p class="text-[11px] text-gray-500 mt-0.5">Tap a star to rate this product</p>
                                    </div>
                                    <div class="flex items-center justify-between sm:justify-end gap-3 w-full sm:w-auto">
                                        <div class="flex gap-1.5 sm:gap-2 text-2xl sm:text-3xl cursor-pointer">
                                            <template x-for="i in 5" :key="i">
                                                <button type="button" 
                                                    @click="rating = i" 
                                                    @mouseenter="hover = i" 
                                                    @mouseleave="hover = 0" 
                                                    class="text-amber-400 hover:scale-110 active:scale-95 transition-transform focus:outline-none cursor-pointer">
                                                    <i :class="(hover || rating) >= i ? 'fa-solid fa-star' : 'fa-regular fa-star'"></i>
                                                </button>
                                            </template>
                                        </div>
                                        <span class="text-xs sm:text-sm font-bold text-amber-600 min-w-[65px] text-right" 
                                              x-text="['Terrible', 'Poor', 'Average', 'Good', 'Excellent'][(hover || rating) - 1]"></span>
                                    </div>
                                    <input type="hidden" name="rating" x-model="rating">
                                </div>

                                {{-- Name & Phone --}}
                                <div class="grid gap-3 sm:gap-4 grid-cols-1 sm:grid-cols-2">
                                    <div>
                                        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1">Name *</label>
                                        <input type="text" name="name" required value="{{ old('name') }}" placeholder="Your full name"
                                            class="block w-full h-11 sm:h-12 rounded-xl border border-gray-300 px-3.5 sm:px-4 text-xs sm:text-sm focus:border-brand-orange focus:ring-2 focus:ring-brand-orange/20 outline-none transition bg-white shadow-xs">
                                    </div>
                                    <div>
                                        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1">Phone Number</label>
                                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="017XXXXXXXX"
                                            class="block w-full h-11 sm:h-12 rounded-xl border border-gray-300 px-3.5 sm:px-4 text-xs sm:text-sm focus:border-brand-orange focus:ring-2 focus:ring-brand-orange/20 outline-none transition bg-white shadow-xs">
                                    </div>
                                </div>

                                {{-- Email & Profile Image --}}
                                <div class="grid gap-3 sm:gap-4 grid-cols-1 sm:grid-cols-2">
                                    <div>
                                        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1">Email</label>
                                        <input type="email" name="email" value="{{ old('email') }}" placeholder="name@example.com"
                                            class="block w-full h-11 sm:h-12 rounded-xl border border-gray-300 px-3.5 sm:px-4 text-xs sm:text-sm focus:border-brand-orange focus:ring-2 focus:ring-brand-orange/20 outline-none transition bg-white shadow-xs">
                                    </div>
                                    <div x-data="{ preview: null }">
                                        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1">Profile Photo (Optional)</label>
                                        <div class="flex items-center gap-2.5 sm:gap-3 h-11 sm:h-12 px-3 border border-gray-300 rounded-xl bg-white shadow-xs min-w-0">
                                            <template x-if="preview">
                                                <img :src="preview" class="h-7 w-7 sm:h-8 sm:w-8 rounded-full object-cover border-2 border-brand-orange flex-shrink-0">
                                            </template>
                                            <template x-if="!preview">
                                                <span class="flex h-7 w-7 sm:h-8 sm:w-8 items-center justify-center rounded-full bg-gray-100 text-gray-400 flex-shrink-0">
                                                    <i class="fa-solid fa-camera text-xs"></i>
                                                </span>
                                            </template>
                                            <label class="flex-1 cursor-pointer flex items-center justify-between min-w-0 text-xs sm:text-sm text-gray-600 hover:text-brand-orange transition">
                                                <span class="truncate mr-2 text-xs sm:text-sm" x-text="preview ? 'Change Photo' : 'Upload photo...'"></span>
                                                <span class="rounded-lg bg-gray-100 hover:bg-gray-200 px-2 py-1 text-[11px] sm:text-xs font-semibold text-gray-700 transition flex-shrink-0">Browse</span>
                                                <input type="file" name="profile_image" accept="image/*" class="hidden" @change="preview = URL.createObjectURL($event.target.files[0])">
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Review Textarea --}}
                                <div>
                                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1">Your Review *</label>
                                    <textarea name="text" rows="4" required placeholder="What did you like or dislike about this product? How did it meet your expectations?"
                                        class="block w-full rounded-xl border border-gray-300 p-3.5 sm:p-4 text-xs sm:text-sm focus:border-brand-orange focus:ring-2 focus:ring-brand-orange/20 outline-none transition bg-white shadow-xs resize-y min-h-[100px]">{{ old('text') }}</textarea>
                                </div>

                                {{-- Submit Button --}}
                                <div class="pt-1">
                                    <button type="submit" 
                                        style="background-color: #F58502; color: #ffffff;"
                                        class="w-full sm:w-auto h-11 sm:h-12 px-8 flex items-center justify-center rounded-xl font-bold text-sm sm:text-base tracking-wide text-white transition shadow-sm hover:opacity-95 active:scale-98 cursor-pointer">
                                        Submit Review
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Review List --}}
                        @if ($reviews->count())
                            <div class="mt-8 border-t border-gray-200/80 pt-6">
                                <h4 class="text-base sm:text-lg font-bold text-gray-900 mb-4">Customer Feedback ({{ $reviews->count() }})</h4>
                                <div class="space-y-4">
                                    @foreach ($reviews as $review)
                                        <div class="rounded-2xl border border-gray-100 bg-gray-50/50 p-5 transition hover:border-gray-200">
                                            <div class="flex items-start gap-3.5">
                                                @if ($review->profile_image)
                                                    <img src="{{ image_url($review->profile_image) }}" alt="{{ $review->name }}" class="h-10 w-10 flex-shrink-0 rounded-full object-cover border border-gray-200">
                                                @else
                                                    <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-brand-navy font-bold text-xs sm:text-sm text-white">
                                                        {{ strtoupper(mb_substr($review->name, 0, 1)) }}
                                                    </span>
                                                @endif
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex flex-wrap items-center justify-between gap-1">
                                                        <p class="font-bold text-gray-900 text-xs sm:text-sm">{{ $review->name }}</p>
                                                        <span class="text-[11px] text-gray-400">{{ $review->created_at->format('d M, Y') }}</span>
                                                    </div>
                                                    <div class="mt-1 flex items-center gap-1 text-amber-400 text-xs">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            <i class="fa-{{ $i <= $review->rating ? 'solid' : 'regular' }} fa-star"></i>
                                                        @endfor
                                                    </div>
                                                    <p class="mt-2 text-xs sm:text-sm leading-relaxed text-gray-700">{{ $review->text }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Related Products --}}
            @if ($relatedProducts->count())
                <div class="mt-8 mb-6">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900">Related Products</h2>
                    <div class="mt-4 product-row">
                        @foreach ($relatedProducts as $related)
                            <x-product-card :product="$related" />
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layout>