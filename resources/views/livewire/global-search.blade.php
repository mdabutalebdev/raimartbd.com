<div class="hidden flex-1 items-center md:flex relative"
     x-data="typewriterSearch(@js($allCategories ?? $searchCategories ?? []))"
     @click.outside="open = false">
    <form action="{{ route('shop') }}" method="GET" class="w-full">
        <div class="relative w-full">
            <input
                type="text"
                name="search"
                x-ref="searchInput"
                :placeholder="placeholder"
                wire:model.live.debounce.300ms="query"
                @focus="open = true"
                autocomplete="off"
                class="w-full rounded-full border border-brand-navy/15 bg-brand-bg py-2.5 pl-5 pr-12 text-sm text-brand-navy placeholder:text-brand-navy/40 focus:border-brand-orange focus:bg-white focus:outline-none transition-all duration-200"
            >
            <button type="submit" class="absolute right-1.5 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full bg-brand-orange text-white transition hover:bg-brand-navy cursor-pointer" aria-label="Search">
                <i class="fa-solid fa-magnifying-glass text-sm"></i>
            </button>
        </div>
    </form>

    <!-- Search Results Dropdown -->
    @if(strlen($query) >= 2)
    <div x-show="open" class="absolute left-0 right-0 top-full mt-2 bg-white rounded-2xl shadow-[0_12px_40px_rgb(0,0,0,0.12)] border border-gray-100 z-50 overflow-hidden divide-y divide-gray-100" x-cloak>

        {{-- Matching Categories --}}
        @if(isset($matchedCategories) && $matchedCategories->isNotEmpty())
            <div class="bg-gray-50/70 p-3">
                <p class="text-[11px] font-bold uppercase tracking-wider text-brand-navy/50 mb-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-layer-group text-[10px] text-brand-orange"></i>
                    <span>Categories</span>
                </p>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($matchedCategories as $cat)
                        <a href="{{ route('shop', ['category' => $cat->slug]) }}"
                           class="group inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-brand-navy border border-gray-200 transition hover:border-brand-orange hover:bg-brand-orange hover:text-white shadow-2xs">
                            <i class="fa-solid fa-folder text-[10px] text-brand-orange transition-colors group-hover:text-white"></i>
                            <span>{{ $cat->name }}</span>
                            <span class="rounded-full bg-brand-bg px-1.5 py-0.5 text-[10px] text-brand-navy/60 transition-colors group-hover:bg-white/20 group-hover:text-white">
                                {{ $cat->products_count }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Matching Products --}}
        @if($results->isNotEmpty())
            <div class="p-2">
                <p class="px-3 pt-1 pb-1.5 text-[11px] font-bold uppercase tracking-wider text-brand-navy/50">Products</p>
                <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
                    @foreach($results as $product)
                        <a href="{{ route('products.show', $product->slug) }}" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-brand-bg transition group">
                            <img src="{{ image_url($product->main_image) }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded-lg bg-gray-100 shrink-0 border border-gray-100">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs sm:text-sm font-medium text-brand-navy group-hover:text-brand-orange transition truncate">{{ $product->name }}</h4>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-brand-orange font-bold text-xs sm:text-sm">৳{{ number_format($product->price) }}</span>
                                    @if($product->old_price > $product->price)
                                        <span class="text-gray-400 text-xs line-through">৳{{ number_format($product->old_price) }}</span>
                                    @endif
                                    @if($product->category)
                                        <span class="text-[11px] text-brand-navy/40 truncate">• {{ $product->category->name }}</span>
                                    @endif
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xs text-brand-navy/20 group-hover:text-brand-orange transition shrink-0"></i>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Empty state --}}
        @if((!isset($matchedCategories) || $matchedCategories->isEmpty()) && $results->isEmpty())
            <div class="p-6 text-center text-gray-500 text-sm">
                <i class="fa-solid fa-magnifying-glass text-2xl text-brand-navy/20 mb-2"></i>
                <p>No categories or products found for "<span class="font-semibold text-brand-navy">{{ $query }}</span>"</p>
            </div>
        @endif

        {{-- View All Results Footer --}}
        @if($totalCount > 6)
            <div class="bg-gray-50/70 p-2.5 text-center">
                <a href="{{ route('shop', ['search' => $query]) }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm text-brand-orange font-semibold hover:text-brand-navy transition">
                    <span>View all {{ $totalCount }} results</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        @endif
    </div>
    @endif
</div>
