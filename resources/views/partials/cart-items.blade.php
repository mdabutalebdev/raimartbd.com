@php
    use App\Services\Cart;
    use App\Support\Shipping;

    $items = $items ?? app(Cart::class)->items();
    $subtotal = $subtotal ?? app(Cart::class)->subtotal();

    $freeThreshold = Shipping::threshold();
    $freeRemaining = Shipping::remainingForFree((float) $subtotal);
    $freeProgress = Shipping::freeProgress((float) $subtotal);

    // "You May Also Like" — Related products from the same categories as items in cart,
    // supplemented with top-rated and best-selling products.
    $cartProductIds = $items->pluck('product.id')->filter()->unique()->values()->all();
    $cartCategoryIds = $items->pluck('product.category_id')->filter()->unique()->values()->all();

    if (empty($cartProductIds)) {
        $suggestions = collect();
    } else {
        // Step 1: Related products from same category/categories (excluding items already in cart)
        $related = \App\Models\Product::with(['images', 'category:id,name', 'variants:id,product_id'])
            ->active()
            ->whereNotIn('id', $cartProductIds)
            ->when(!empty($cartCategoryIds), fn($q) => $q->whereIn('category_id', $cartCategoryIds))
            ->orderByDesc('rating')
            ->orderByDesc('is_top_selling')
            ->orderByDesc('is_best_seller')
            ->orderByDesc('is_featured')
            ->latest('id')
            ->take(8)
            ->get();

        // Step 2: If fewer than 8, supplement with top-rated and popular products
        $needed = 8 - $related->count();
        if ($needed > 0) {
            $alreadySuggestedIds = array_merge($cartProductIds, $related->pluck('id')->all());
            $topRated = \App\Models\Product::with(['images', 'category:id,name', 'variants:id,product_id'])
                ->active()
                ->whereNotIn('id', $alreadySuggestedIds)
                ->orderByDesc('rating')
                ->orderByDesc('is_top_selling')
                ->orderByDesc('is_best_seller')
                ->orderByDesc('is_featured')
                ->latest('id')
                ->take($needed)
                ->get();
            $suggestions = $related->concat($topRated);
        } else {
            $suggestions = $related;
        }
    }
@endphp

@if ($items->isEmpty())
    <div class="flex flex-1 flex-col items-center justify-center px-6 py-16 text-center">
        <i class="fa-solid fa-cart-shopping text-4xl text-brand-navy/15"></i>
        <p class="mt-4 text-sm text-brand-navy/50">Your cart is empty.</p>
        <button type="button" data-cart-close class="mt-4 rounded-md bg-brand-orange px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-navy cursor-pointer">
            Continue Shopping
        </button>
    </div>
@else
    {{-- Free delivery unlock card --}}
    @if ($freeThreshold)
        <div class="shrink-0 border-b border-brand-navy/10 p-3">
            <div class="overflow-hidden rounded-lg border border-brand-orange/25 bg-brand-orange/[0.06]">
                <div class="flex items-center gap-3 px-3 py-2.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-orange/15 text-brand-orange">
                        <i class="fa-solid fa-gift"></i>
                    </span>
                    <div class="min-w-0 flex-1">
                        @if ($freeRemaining > 0)
                            <p class="text-[13px] font-semibold leading-tight text-brand-navy">Get FREE delivery</p>
                            <p class="text-xs leading-tight text-brand-navy/60">
                                Add <span class="font-bold text-brand-orange">৳{{ number_format($freeRemaining) }}</span> more to unlock!
                            </p>
                        @else
                            <p class="text-[13px] font-semibold leading-tight text-green-600">FREE delivery unlocked!</p>
                            <p class="text-xs leading-tight text-brand-navy/60">No delivery charge on this order.</p>
                        @endif
                    </div>
                </div>

                <div class="h-1.5 w-full bg-brand-navy/10">
                    <div class="h-full transition-all duration-500 {{ $freeRemaining > 0 ? 'bg-brand-orange' : 'bg-green-500' }}"
                        style="width: {{ $freeProgress }}%"></div>
                </div>
            </div>
        </div>
    @endif

    {{-- Cart lines --}}
    <div class="flex-1 divide-y divide-brand-navy/10 overflow-y-auto">
        @foreach ($items as $item)
            <div data-cart-item="{{ $item->id }}" class="flex gap-3 px-4 py-3">
                <a href="{{ route('products.show', $item->product) }}" wire:navigate class="shrink-0">
                    <img src="{{ image_url($item->product->main_image, urlencode($item->product->name)) }}" alt="{{ $item->product->name }}"
                        class="h-16 w-16 rounded border border-brand-navy/10 object-cover">
                </a>

                <div class="min-w-0 flex-1">
                    <div class="flex items-start justify-between gap-2">
                        <a href="{{ route('products.show', $item->product) }}" wire:navigate class="line-clamp-2 text-sm font-medium text-brand-navy hover:text-brand-orange">{{ $item->product->name }}</a>
                        <button type="button" data-cart-remove data-id="{{ $item->id }}" class="shrink-0 text-brand-navy/30 transition hover:text-red-500 cursor-pointer" aria-label="Remove">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    @if (! empty($item->options))
                        <div class="mt-1 flex flex-wrap gap-1 text-[11px] text-brand-navy/60">
                            @foreach ($item->options as $key => $val)
                                <span class="rounded bg-brand-bg px-1.5 py-0.5">{{ $key }}: <span class="font-medium text-brand-navy">{{ $val }}</span></span>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-2 flex items-center gap-2">
                        <div class="flex h-8 items-center overflow-hidden rounded border border-brand-navy/15">
                            <button type="button" data-cart-dec data-id="{{ $item->id }}" data-qty="{{ $item->quantity }}" class="flex h-full w-7 items-center justify-center text-brand-navy transition hover:bg-brand-navy/5 cursor-pointer">
                                <i class="fa-solid fa-minus text-[10px]"></i>
                            </button>
                            <span class="w-8 text-center text-sm font-semibold text-brand-navy">{{ $item->quantity }}</span>
                            <button type="button" data-cart-inc data-id="{{ $item->id }}" data-qty="{{ $item->quantity }}" data-max="{{ $item->product->stock }}" class="flex h-full w-7 items-center justify-center text-brand-navy transition hover:bg-brand-navy/5 cursor-pointer">
                                <i class="fa-solid fa-plus text-[10px]"></i>
                            </button>
                        </div>

                        <span class="text-xs text-brand-navy/40">&times; ৳{{ number_format($item->product->price) }}</span>
                        <span class="ml-auto text-sm font-bold text-brand-orange">৳{{ number_format($item->subtotal) }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- You may also like --}}
    @if ($suggestions->isNotEmpty())
        <div class="shrink-0 border-t border-brand-navy/10 bg-brand-bg/60 px-4 py-3">
            <div class="flex items-center justify-between gap-2">
                <h3 class="text-sm font-bold text-brand-navy">You May Also Like</h3>
                <div class="flex gap-1.5">
                    <button type="button" data-suggest-prev class="flex h-7 w-7 items-center justify-center rounded-full bg-brand-orange text-white transition hover:bg-brand-navy cursor-pointer" aria-label="Previous">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    </button>
                    <button type="button" data-suggest-next class="flex h-7 w-7 items-center justify-center rounded-full bg-brand-orange text-white transition hover:bg-brand-navy cursor-pointer" aria-label="Next">
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </button>
                </div>
            </div>

            <div id="cart-suggestions" class="flex gap-2.5 overflow-x-auto no-scrollbar scroll-smooth w-full min-w-0 mt-2.5 pb-1" style="scrollbar-width: none; -ms-overflow-style: none;">
                @foreach ($suggestions as $suggestion)
                    <div class="w-[168px] shrink-0">
                        <div class="h-full flex flex-col justify-between rounded-lg border border-brand-navy/10 bg-white p-2.5 shadow-[0_1px_3px_rgba(0,0,0,0.04)] hover:border-brand-orange/40 transition">
                            <div class="flex gap-2.5">
                                <a href="{{ route('products.show', $suggestion) }}" wire:navigate class="shrink-0 group">
                                    <img src="{{ image_url($suggestion->main_image, urlencode($suggestion->name)) }}" alt="{{ $suggestion->name }}"
                                        class="h-12 w-12 rounded object-cover border border-brand-navy/10 group-hover:opacity-90 transition">
                                </a>
                                <div class="min-w-0 flex-1">
                                    @if($suggestion->category)
                                        <span class="block truncate text-[10px] font-medium text-brand-navy/40 leading-none mb-1">{{ $suggestion->category->name }}</span>
                                    @endif
                                    <a href="{{ route('products.show', $suggestion) }}" wire:navigate class="line-clamp-2 text-[11px] font-semibold leading-tight text-brand-navy hover:text-brand-orange transition" title="{{ $suggestion->name }}">{{ $suggestion->name }}</a>
                                    <div class="mt-1 flex items-baseline gap-1.5">
                                        <span class="text-xs font-bold text-brand-orange">৳{{ number_format($suggestion->price) }}</span>
                                        @if($suggestion->old_price && $suggestion->old_price > $suggestion->price)
                                            <span class="text-[10px] text-brand-navy/35 line-through">৳{{ number_format($suggestion->old_price) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if ($suggestion->hasVariants())
                                {{-- Has variants: the customer must pick options on the product page --}}
                                <a href="{{ route('products.show', $suggestion) }}" wire:navigate
                                    class="mt-2.5 flex w-full items-center justify-center gap-1 rounded border border-brand-orange bg-brand-orange/5 py-1 text-[11px] font-semibold text-brand-orange transition hover:bg-brand-orange hover:text-white cursor-pointer">
                                    <i class="fa-solid fa-plus text-[9px]"></i> Add
                                </a>
                            @else
                                <form action="{{ route('cart.add', $suggestion) }}" method="POST" data-add-to-cart class="mt-2.5">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center justify-center gap-1 rounded border border-brand-orange bg-brand-orange/5 py-1 text-[11px] font-semibold text-brand-orange transition hover:bg-brand-orange hover:text-white cursor-pointer shadow-xs">
                                        <i class="fa-solid fa-plus text-[9px]"></i> Add
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Totals + checkout --}}
    <div class="shrink-0 border-t border-brand-navy/10 bg-white p-4">
        <div class="mb-3 flex items-center justify-between">
            <span class="text-sm font-semibold text-brand-navy">Total:</span>
            <span class="text-lg font-bold text-brand-navy">৳{{ number_format($subtotal) }}</span>
        </div>
        <a href="{{ route('checkout.index') }}" class="block rounded-md bg-brand-orange py-3 text-center text-sm font-bold uppercase tracking-wide text-white transition hover:bg-brand-navy cursor-pointer">
            Checkout
        </a>
        <a href="{{ route('cart.index') }}" wire:navigate class="mt-2 block text-center text-xs font-medium text-brand-navy/50 hover:text-brand-orange cursor-pointer">View full cart</a>
    </div>
@endif
