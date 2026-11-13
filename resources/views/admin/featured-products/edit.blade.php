<x-admin-layout title="Featured Products - Raimart Admin">
    <div x-data="featuredProducts({{ Illuminate\Support\Js::from($products) }})">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="font-serif text-2xl font-bold">Featured Products</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Manage the products displayed in the <span class="font-semibold text-brand-navy">Our Featured Products</span> section on the home page.
                    The top 10 products with lowest order number appear on the homepage.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="rounded-full bg-brand-orange/10 px-3 py-1.5 text-sm font-semibold text-brand-orange">
                    <span x-text="selected.length"></span> featured selected
                </span>
            </div>
        </div>

        @if (session('status'))
            <div class="mt-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                <i class="fa-solid fa-circle-check mr-1.5"></i> {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('admin.homepage.featured-products.update') }}" method="POST" class="mt-6">
            @csrf
            @method('PUT')

            {{-- Filter & Actions Toolbar --}}
            <div class="flex flex-col gap-3 rounded-xl bg-white p-4 shadow-sm ring-1 ring-brand-navy/5 sm:flex-row sm:items-center sm:justify-between">
                <div class="relative flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                    <input type="text" x-model="search" @input="limit = 20" placeholder="Search products by name or category..."
                        class="w-full rounded-lg border border-brand-navy/15 bg-white pl-9 pr-4 py-2 text-sm focus:border-brand-orange focus:outline-none focus:ring-1 focus:ring-brand-orange">
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    {{-- Tab Switcher --}}
                    <div class="flex rounded-lg border border-gray-200 p-0.5 bg-gray-50">
                        <button type="button" @click="tab = 'all'; limit = 20"
                            class="rounded-md px-3 py-1.5 text-xs font-semibold transition cursor-pointer"
                            :class="tab === 'all' ? 'bg-white text-brand-navy shadow-sm' : 'text-gray-500 hover:text-gray-900'">
                            All Products (<span x-text="all.length"></span>)
                        </button>
                        <button type="button" @click="tab = 'selected'; limit = 20"
                            class="rounded-md px-3 py-1.5 text-xs font-semibold transition cursor-pointer"
                            :class="tab === 'selected' ? 'bg-white text-brand-orange shadow-sm font-bold' : 'text-gray-500 hover:text-gray-900'">
                            Featured Only (<span x-text="selected.length"></span>)
                        </button>
                    </div>

                    <button type="button" @click="autoNumberSelected()"
                        class="rounded-lg border border-brand-orange/30 bg-brand-orange/5 px-3 py-2 text-xs font-semibold text-brand-orange hover:bg-brand-orange hover:text-white transition cursor-pointer"
                        title="Sequentially renumber all featured products (1, 2, 3...)">
                        <i class="fa-solid fa-arrow-down-1-9 mr-1"></i> Auto Number
                    </button>
                </div>
            </div>

            {{-- Products Table with Infinite Scroll Pagination --}}
            <div class="mt-4 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-brand-navy/5">
                <div class="grid grid-cols-[auto_1fr_auto_150px] items-center gap-4 border-b border-gray-100 px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-400">
                    <span class="pl-2">Featured</span>
                    <span>Product</span>
                    <span class="hidden sm:block text-right">Price</span>
                    <span class="text-center">Homepage Serial / Order</span>
                </div>

                <div class="divide-y divide-gray-100">
                    <template x-for="(item, index) in visibleItems" :key="item.id">
                        <div class="grid grid-cols-[auto_1fr_auto_150px] items-center gap-4 px-5 py-3 transition hover:bg-gray-50/70"
                            :class="item.selected ? 'bg-orange-50/20' : ''">
                            {{-- Checkbox --}}
                            <div class="flex items-center pl-2">
                                <input type="checkbox" :id="'prod_' + item.id"
                                    :checked="item.selected"
                                    @change="toggle(item)"
                                    class="h-4 w-4 rounded border-gray-300 text-brand-orange focus:ring-brand-orange cursor-pointer">
                            </div>

                            {{-- Product Info --}}
                            <label :for="'prod_' + item.id" class="flex min-w-0 items-center gap-3 cursor-pointer">
                                <img :src="item.image" class="h-11 w-11 shrink-0 rounded-lg object-cover border border-gray-100" :alt="item.name">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <p class="truncate text-sm font-semibold text-brand-navy" x-text="item.name"></p>
                                        <template x-if="item.selected && item.order > 0 && item.order <= 10">
                                            <span class="shrink-0 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800">
                                                Top 10
                                            </span>
                                        </template>
                                    </div>
                                    <p class="truncate text-xs text-gray-400">
                                        <span x-text="item.category"></span>
                                        <span class="sm:hidden font-medium text-brand-navy ml-1.5">৳<span x-text="item.price.toLocaleString()"></span></span>
                                    </p>
                                </div>
                            </label>

                            {{-- Price --}}
                            <div class="hidden text-right text-sm font-semibold text-brand-navy sm:block">
                                ৳<span x-text="item.price.toLocaleString()"></span>
                            </div>

                            {{-- Serial / Order Controls --}}
                            <div class="flex items-center justify-center gap-1">
                                <button type="button" @click="moveUp(item)" :disabled="!item.selected"
                                    class="h-7 w-7 rounded border border-gray-200 text-xs text-gray-600 hover:bg-gray-100 disabled:opacity-20 disabled:cursor-not-allowed transition"
                                    title="Move earlier in order">
                                    <i class="fa-solid fa-arrow-up"></i>
                                </button>
                                <input type="number" min="1" x-model.number="item.order" :disabled="!item.selected"
                                    class="w-16 rounded-lg border border-gray-300 px-2 py-1.5 text-center text-sm font-semibold focus:border-brand-orange focus:outline-none focus:ring-1 focus:ring-brand-orange disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed">
                                <button type="button" @click="moveDown(item)" :disabled="!item.selected"
                                    class="h-7 w-7 rounded border border-gray-200 text-xs text-gray-600 hover:bg-gray-100 disabled:opacity-20 disabled:cursor-not-allowed transition"
                                    title="Move later in order">
                                    <i class="fa-solid fa-arrow-down"></i>
                                </button>
                            </div>
                        </div>
                    </template>

                    {{-- Empty State --}}
                    <template x-if="filteredList.length === 0">
                        <div class="py-14 text-center text-gray-400 text-sm">
                            <i class="fa-solid fa-box-open text-3xl mb-2 text-gray-300 block"></i>
                            No products found matching "<span x-text="search"></span>"
                        </div>
                    </template>
                </div>

                {{-- Infinite Scroll Sentinel & Load More Footer --}}
                <div id="featured-scroll-sentinel" class="border-t border-gray-100 bg-gray-50/50 p-4 text-center">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
                        <span>
                            Showing <strong class="text-brand-navy" x-text="visibleItems.length"></strong> of <strong class="text-brand-navy" x-text="filteredList.length"></strong> products
                            <template x-if="hasMore">
                                <span class="text-gray-400">(First 20 loaded, scroll down for 10 more)</span>
                            </template>
                        </span>

                        <template x-if="hasMore">
                            <button type="button" @click="loadMore()"
                                class="rounded-lg border border-gray-300 bg-white px-4 py-2 font-semibold text-brand-navy shadow-sm hover:bg-gray-100 transition cursor-pointer">
                                <i class="fa-solid fa-arrow-down mr-1"></i> Load 10 More Products
                            </button>
                        </template>

                        <template x-if="!hasMore && filteredList.length > 0">
                            <span class="text-emerald-600 font-medium">
                                <i class="fa-solid fa-check mr-1"></i> All products loaded
                            </span>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Hidden inputs submitted to server for ALL selected products --}}
            <template x-for="item in selected" :key="item.id">
                <div>
                    <input type="hidden" name="products[]" :value="item.id">
                    <input type="hidden" :name="`order[${item.id}]`" :value="item.order">
                </div>
            </template>

            <div class="mt-6 flex gap-3">
                <button type="submit" class="rounded-lg bg-brand-orange px-8 py-3 text-sm font-bold text-white shadow-sm hover:bg-brand-navy hover:shadow-md transition-all cursor-pointer">
                    <i class="fa-solid fa-floppy-disk mr-2"></i> Save Changes
                </button>
                <a href="{{ route('home') }}" target="_blank" class="rounded-lg border border-gray-200 bg-white px-8 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fa-solid fa-eye mr-2"></i> View on Site
                </a>
            </div>
        </form>
    </div>

    <script>
        window.featuredProducts = function (products) {
            return {
                all: (products || []).map((p) => ({
                    id: p.id,
                    name: p.name,
                    category: p.category,
                    price: p.price,
                    image: p.image,
                    selected: Boolean(p.selected),
                    order: p.order || 0,
                })),
                search: '',
                tab: 'all',
                limit: 20, // Initial 20 as requested

                init() {
                    this.setupScrollListener();
                },

                get selected() {
                    return this.all
                        .filter((p) => p.selected)
                        .sort((a, b) => (a.order || 9999) - (b.order || 9999));
                },

                get filteredList() {
                    let list = this.tab === 'selected' ? this.selected : this.all;
                    const q = this.search.trim().toLowerCase();
                    if (q) {
                        list = list.filter((p) => (p.name && p.name.toLowerCase().includes(q)) || (p.category && p.category.toLowerCase().includes(q)));
                    }
                    return list;
                },

                get visibleItems() {
                    return this.filteredList.slice(0, this.limit);
                },

                get hasMore() {
                    return this.limit < this.filteredList.length;
                },

                loadMore() {
                    if (this.hasMore) {
                        this.limit = Math.min(this.filteredList.length, this.limit + 10);
                    }
                },

                setupScrollListener() {
                    // 1. IntersectionObserver on sentinel
                    if ('IntersectionObserver' in window) {
                        const observer = new IntersectionObserver((entries) => {
                            if (entries[0].isIntersecting && this.hasMore) {
                                this.loadMore();
                            }
                        }, { rootMargin: '300px' });

                        this.$nextTick(() => {
                            const sentinel = document.getElementById('featured-scroll-sentinel');
                            if (sentinel) observer.observe(sentinel);
                        });
                    }

                    // 2. Window scroll event fallback
                    window.addEventListener('scroll', () => {
                        if (this.hasMore && (window.innerHeight + window.scrollY) >= (document.documentElement.scrollHeight - 450)) {
                            this.loadMore();
                        }
                    }, { passive: true });
                },

                toggle(item) {
                    item.selected = !item.selected;
                    if (item.selected) {
                        const max = Math.max(0, ...this.all.filter((p) => p.selected && p.id !== item.id).map((p) => p.order || 0));
                        item.order = max + 1;
                    } else {
                        item.order = 0;
                    }
                },

                moveUp(item) {
                    const currentSelected = this.selected;
                    const idx = currentSelected.findIndex((p) => p.id === item.id);
                    if (idx <= 0) return;
                    const prev = currentSelected[idx - 1];
                    const temp = item.order;
                    item.order = prev.order;
                    prev.order = temp;
                },

                moveDown(item) {
                    const currentSelected = this.selected;
                    const idx = currentSelected.findIndex((p) => p.id === item.id);
                    if (idx === -1 || idx >= currentSelected.length - 1) return;
                    const next = currentSelected[idx + 1];
                    const temp = item.order;
                    item.order = next.order;
                    next.order = temp;
                },

                autoNumberSelected() {
                    this.selected.forEach((p, idx) => {
                        p.order = idx + 1;
                    });
                }
            };
        };
    </script>
</x-admin-layout>
