<x-admin-layout title="Product Reviews - Raimart Admin">
    <div>
        <h1 class="font-serif text-2xl font-bold">Product Reviews</h1>
        <p class="mt-0.5 text-sm text-brand-navy/60">Manage customer reviews. Approve or reject reviews before they appear on the product page.</p>
    </div>

    {{-- Status Chips --}}
    @php
        $chips = [
            ['label' => 'All', 'count' => $counts['all'], 'params' => []],
            ['label' => 'Pending', 'count' => $counts['pending'], 'params' => ['status' => 'pending']],
            ['label' => 'Approved', 'count' => $counts['approved'], 'params' => ['status' => 'approved']],
        ];
    @endphp
    <div class="mt-4 flex flex-wrap gap-2">
        @foreach ($chips as $chip)
            @php $active = count($chip['params']) ? request('status') === $chip['params']['status'] : ! request('status'); @endphp
            <a href="{{ route('admin.product-reviews.index', $chip['params']) }}"
                class="flex items-center gap-2 rounded-lg border px-4 py-2 text-xs font-semibold transition
                    {{ $active ? 'border-brand-orange bg-brand-orange text-white shadow-sm' : 'border-brand-navy/15 text-brand-navy hover:border-brand-orange hover:text-brand-orange bg-white' }}">
                {{ $chip['label'] }}
                <span class="rounded-full px-2 py-0.5 text-[10px] {{ $active ? 'bg-white/20' : 'bg-brand-navy/5' }}">{{ $chip['count'] }}</span>
            </a>
        @endforeach
    </div>

    {{-- Filters --}}
    <form action="{{ route('admin.product-reviews.index') }}" class="mt-4 rounded-xl border border-brand-navy/10 bg-white p-4 shadow-sm">
        <div class="flex flex-wrap items-end gap-3">
            <input type="hidden" name="status" value="{{ request('status') }}">

            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-brand-navy/60 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Reviewer name or text..."
                    class="w-full rounded-lg border border-brand-navy/15 px-3 py-2 text-sm focus:border-brand-orange focus:outline-none focus:ring-1 focus:ring-brand-orange/30">
            </div>

            <div class="w-[130px]">
                <label class="block text-xs font-medium text-brand-navy/60 mb-1">Rating</label>
                <select name="rating" class="w-full rounded-lg border border-brand-navy/15 px-3 py-2 text-sm focus:border-brand-orange focus:outline-none focus:ring-1 focus:ring-brand-orange/30">
                    <option value="">Any</option>
                    @for ($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" @selected(request('rating') == $i)>{{ $i }} star{{ $i > 1 ? 's' : '' }}</option>
                    @endfor
                </select>
            </div>

            <div class="w-[160px]">
                <label class="block text-xs font-medium text-brand-navy/60 mb-1">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="w-full rounded-lg border border-brand-navy/15 px-3 py-2 text-sm focus:border-brand-orange focus:outline-none focus:ring-1 focus:ring-brand-orange/30">
            </div>

            <div class="w-[160px]">
                <label class="block text-xs font-medium text-brand-navy/60 mb-1">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="w-full rounded-lg border border-brand-navy/15 px-3 py-2 text-sm focus:border-brand-orange focus:outline-none focus:ring-1 focus:ring-brand-orange/30">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="rounded-lg bg-brand-navy px-5 py-2 text-sm font-semibold text-white transition hover:bg-brand-orange">
                    <i class="fa-solid fa-filter mr-1.5 text-xs"></i> Filter
                </button>
                <a href="{{ route('admin.product-reviews.index') }}" class="rounded-lg border border-brand-navy/15 px-4 py-2 text-sm font-medium text-brand-navy/60 transition hover:border-brand-orange hover:text-brand-orange">
                    Clear
                </a>
            </div>
        </div>
    </form>

    {{-- Data Table --}}
    <div class="mt-4 overflow-hidden rounded-xl border border-brand-navy/10 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-brand-navy/10 bg-brand-navy/[0.02]">
                        <th class="px-4 py-3 font-semibold text-brand-navy/70 whitespace-nowrap">Reviewer</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy/70 whitespace-nowrap">Product</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy/70 whitespace-nowrap">Rating</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy/70 whitespace-nowrap">Review</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy/70 whitespace-nowrap">Date</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy/70 whitespace-nowrap">Status</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy/70 whitespace-nowrap text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-navy/5">
                    @forelse ($reviews as $review)
                        <tr class="transition hover:bg-brand-orange/[0.02]">
                            {{-- Reviewer --}}
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-3">
                                    @if ($review->profile_image)
                                        <img src="{{ image_url($review->profile_image) }}" alt="{{ $review->name }}" class="h-9 w-9 rounded-full object-cover border border-brand-navy/10">
                                    @else
                                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-navy/10 font-semibold text-brand-navy/50 text-xs">
                                            {{ strtoupper(mb_substr($review->name, 0, 1)) }}
                                        </span>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-semibold text-brand-navy truncate max-w-[150px]">{{ $review->name }}</p>
                                        @if ($review->phone)
                                            <p class="text-xs text-brand-navy/50">{{ $review->phone }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Product --}}
                            <td class="px-4 py-3.5">
                                @if ($review->product)
                                    <a href="{{ route('products.show', $review->product) }}" target="_blank" class="font-medium text-brand-orange hover:underline truncate max-w-[180px] block">
                                        {{ Str::limit($review->product->name, 35) }}
                                    </a>
                                @else
                                    <span class="italic text-brand-navy/40">Deleted product</span>
                                @endif
                            </td>

                            {{-- Rating --}}
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-1 text-brand-orange text-xs">
                                    @for ($i = 0; $i < 5; $i++)
                                        <i class="fa-{{ $i < $review->rating ? 'solid' : 'regular' }} fa-star"></i>
                                    @endfor
                                </div>
                            </td>

                            {{-- Review Text --}}
                            <td class="px-4 py-3.5">
                                <p class="text-sm text-brand-navy/80 max-w-[250px] truncate" title="{{ $review->text }}">{{ $review->text }}</p>
                            </td>

                            {{-- Date --}}
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <p class="text-sm text-brand-navy/70">{{ $review->created_at?->format('d M, Y') }}</p>
                                <p class="text-xs text-brand-navy/40">{{ $review->created_at?->format('h:i A') }}</p>
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold
                                    {{ $review->is_approved ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $review->is_approved ? 'bg-green-500' : 'bg-amber-500' }}"></span>
                                    {{ $review->is_approved ? 'Approved' : 'Pending' }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-end gap-2">
                                    @if (! $review->is_approved)
                                        <form action="{{ route('admin.product-reviews.approve', $review) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="rounded-lg bg-green-600 px-3.5 py-1.5 text-xs font-semibold text-white transition hover:bg-green-700" title="Approve">
                                                <i class="fa-solid fa-check mr-1"></i> Approve
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.product-reviews.reject', $review) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="rounded-lg border border-amber-300 bg-amber-50 px-3.5 py-1.5 text-xs font-semibold text-amber-700 transition hover:bg-amber-100" title="Reject">
                                                <i class="fa-solid fa-eye-slash mr-1"></i> Reject
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.product-reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('Delete this review permanently?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="rounded-lg border border-red-200 px-3.5 py-1.5 text-xs font-semibold text-red-500 transition hover:bg-red-50" title="Delete">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-brand-navy/40">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="fa-regular fa-comment-dots text-3xl text-brand-navy/20"></i>
                                    <p class="text-sm">No reviews found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if ($reviews->hasPages())
        <div class="mt-4">{{ $reviews->links() }}</div>
    @endif
</x-admin-layout>
