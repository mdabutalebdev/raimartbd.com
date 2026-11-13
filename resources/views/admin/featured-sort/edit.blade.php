<x-admin-layout title="Featured Sort - Raimart Admin">
    <div x-data="featuredSort({{ Illuminate\Support\Js::from($categories) }})">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="font-serif text-2xl font-bold">Featured Category Tabs (Featured Sort)</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Select which category tabs appear under <span class="font-semibold text-brand-navy">Our Featured Products</span> on the homepage and set their exact display order / serial.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="rounded-full bg-brand-orange/10 px-3 py-1.5 text-sm font-semibold text-brand-orange">
                    <span x-text="selectedCount"></span> tabs active
                </span>
            </div>
        </div>

        @if (session('status'))
            <div class="mt-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                <i class="fa-solid fa-circle-check mr-1"></i> {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('admin.homepage.featured-sort.update') }}" method="POST" class="mt-6">
            @csrf
            @method('PUT')

            {{-- Filter & Actions Toolbar --}}
            <div class="flex flex-col gap-3 rounded-xl bg-white p-4 shadow-sm ring-1 ring-brand-navy/5 sm:flex-row sm:items-center sm:justify-between">
                <div class="relative flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                    <input type="text" x-model="search" placeholder="Search categories..."
                        class="w-full rounded-lg border border-brand-navy/15 bg-white pl-9 pr-4 py-2 text-sm focus:border-brand-orange focus:outline-none focus:ring-1 focus:ring-brand-orange">
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="selectAll()"
                        class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                        Select All
                    </button>
                    <button type="button" @click="deselectAll()"
                        class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                        Clear All
                    </button>
                    <button type="button" @click="autoOrderSelected()"
                        class="rounded-lg border border-brand-orange/30 bg-brand-orange/5 px-3 py-2 text-xs font-semibold text-brand-orange hover:bg-brand-orange hover:text-white transition">
                        <i class="fa-solid fa-arrow-down-1-9 mr-1"></i> Auto Number (1, 2, 3...)
                    </button>
                </div>
            </div>

            {{-- Categories Table --}}
            <div class="mt-4 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-brand-navy/5">
                <div class="grid grid-cols-[auto_1fr_auto_140px] items-center gap-4 border-b border-gray-100 px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-400">
                    <span class="pl-2">Show Tab</span>
                    <span>Category</span>
                    <span class="hidden sm:block text-right">Products</span>
                    <span class="text-center">Tab Serial / Order</span>
                </div>

                <div class="divide-y divide-gray-100">
                    <template x-for="cat in filteredCategories" :key="cat.id">
                        <div class="grid grid-cols-[auto_1fr_auto_140px] items-center gap-4 px-5 py-3 transition hover:bg-gray-50/70"
                            :class="cat.selected ? 'bg-orange-50/20' : ''">
                            {{-- Checkbox --}}
                            <div class="flex items-center pl-2">
                                <input type="checkbox" :id="'cat_' + cat.id" :value="cat.id"
                                    x-model="cat.selected"
                                    @change="onToggle(cat)"
                                    class="h-4 w-4 rounded border-gray-300 text-brand-orange focus:ring-brand-orange cursor-pointer">
                            </div>

                            {{-- Category Info --}}
                            <label :for="'cat_' + cat.id" class="flex min-w-0 items-center gap-3 cursor-pointer">
                                <template x-if="cat.image">
                                    <img :src="cat.image" class="h-10 w-10 shrink-0 rounded-lg object-cover" :alt="cat.name">
                                </template>
                                <template x-if="!cat.image">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-400 text-sm">
                                        <i class="fa-solid fa-shapes"></i>
                                    </div>
                                </template>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-brand-navy" x-text="cat.name"></p>
                                    <p class="truncate text-xs text-gray-400 sm:hidden">
                                        <span x-text="cat.products_count"></span> products
                                    </p>
                                </div>
                            </label>

                            {{-- Products Count --}}
                            <div class="hidden text-right text-xs font-medium text-gray-500 sm:block">
                                <span class="rounded-full bg-gray-100 px-2.5 py-1" x-text="cat.products_count + ' products'"></span>
                            </div>

                            {{-- Serial / Order Input --}}
                            <div class="flex items-center justify-center gap-1">
                                <input type="number" min="1" x-model.number="cat.order" :disabled="!cat.selected"
                                    class="w-20 rounded-lg border border-gray-300 px-2 py-1.5 text-center text-sm font-semibold focus:border-brand-orange focus:outline-none focus:ring-1 focus:ring-brand-orange disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed">
                            </div>

                            {{-- Hidden inputs submitted only if selected --}}
                            <template x-if="cat.selected">
                                <div>
                                    <input type="hidden" name="categories[]" :value="cat.id">
                                    <input type="hidden" :name="`order[${cat.id}]`" :value="cat.order">
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="filteredCategories.length === 0">
                        <div class="py-12 text-center text-gray-400 text-sm">
                            No categories matching "<span x-text="search"></span>"
                        </div>
                    </template>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" class="rounded-lg bg-brand-orange px-8 py-3 text-sm font-bold text-white shadow-sm hover:bg-brand-navy hover:shadow-md transition-all">
                    <i class="fa-solid fa-floppy-disk mr-2"></i> Save Changes
                </button>
                <a href="{{ route('home') }}" target="_blank" class="rounded-lg border border-gray-200 bg-white px-8 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fa-solid fa-eye mr-2"></i> View on Site
                </a>
            </div>
        </form>
    </div>

    <script>
        window.featuredSort = function (categories) {
            return {
                items: (categories || []).map((c) => ({
                    id: c.id,
                    name: c.name,
                    image: c.image,
                    products_count: c.products_count || 0,
                    selected: Boolean(c.selected),
                    order: c.order || 0,
                })),
                search: '',

                get selectedCount() {
                    return this.items.filter((c) => c.selected).length;
                },

                get filteredCategories() {
                    const q = this.search.trim().toLowerCase();
                    if (!q) return this.items;
                    return this.items.filter((c) => c.name.toLowerCase().includes(q));
                },

                onToggle(cat) {
                    if (cat.selected && (!cat.order || cat.order === 0)) {
                        const maxOrder = Math.max(0, ...this.items.filter((c) => c.selected).map((c) => c.order || 0));
                        cat.order = maxOrder + 1;
                    }
                },

                selectAll() {
                    let order = 1;
                    this.items.forEach((c) => {
                        c.selected = true;
                        if (!c.order || c.order === 0) {
                            c.order = order++;
                        }
                    });
                },

                deselectAll() {
                    this.items.forEach((c) => {
                        c.selected = false;
                        c.order = 0;
                    });
                },

                autoOrderSelected() {
                    let order = 1;
                    this.items
                        .filter((c) => c.selected)
                        .forEach((c) => {
                            c.order = order++;
                        });
                }
            };
        };
    </script>
</x-admin-layout>

