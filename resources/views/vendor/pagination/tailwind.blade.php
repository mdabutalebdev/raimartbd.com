@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="my-8 flex items-center justify-center">
        <div class="flex items-center justify-center gap-1.5 sm:gap-2">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}"
                    class="flex h-9 w-9 sm:h-10 sm:w-10 items-center justify-center rounded-md text-brand-navy/30 cursor-not-allowed select-none transition">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}"
                    class="flex h-9 w-9 sm:h-10 sm:w-10 items-center justify-center rounded-md text-brand-navy/70 transition hover:bg-brand-orange/10 hover:text-brand-orange cursor-pointer">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span aria-disabled="true"
                        class="flex h-9 min-w-9 sm:h-10 sm:min-w-10 items-center justify-center px-1 text-sm font-medium text-brand-navy/40 select-none">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                class="flex h-9 min-w-9 sm:h-10 sm:min-w-10 items-center justify-center rounded-md bg-brand-orange px-2 text-sm font-semibold text-white shadow-xs select-none">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                                class="flex h-9 min-w-9 sm:h-10 sm:min-w-10 items-center justify-center rounded-md px-2 text-sm font-medium text-brand-navy/80 transition hover:bg-brand-orange/10 hover:text-brand-orange cursor-pointer">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}"
                    class="flex h-9 w-9 sm:h-10 sm:w-10 items-center justify-center rounded-md text-brand-navy/70 transition hover:bg-brand-orange/10 hover:text-brand-orange cursor-pointer">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
            @else
                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}"
                    class="flex h-9 w-9 sm:h-10 sm:w-10 items-center justify-center rounded-md text-brand-navy/30 cursor-not-allowed select-none transition">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </span>
            @endif
        </div>
    </nav>
@endif
