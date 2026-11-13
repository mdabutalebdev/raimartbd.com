<x-admin-layout title="Featured Products - Raimart Admin">
    <div x-data="featuredProducts({{ Illuminate\Support\Js::from($products) }})">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="font-serif text-2xl font-bold">Featured Products</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Manage the products displayed in the <span class="font-semibold text-brand-navy">Our Featured Products</span> section on the home page.
                    The top 10 products appear on the homepage. Set their display position (1st, 2nd, 3rd, etc.).
                </p>
            </div>
            <span class="rounded-full bg-brand-orange/10 px-3 py-1.5 text-sm font-semibold text-brand-orange">
                <span x-text="selected.length"></span> selected
            </span>
        </div>

        @if (session('status'))
            <div class="mt-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                <i class="fa-solid fa-circle-check mr-1"></i> {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('admin.homepage.featured-products.update') }}" method="POST" class="mt-6">
            @csrf
            @method('PUT')

            {{-- Add product --}}
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-brand-navy/5">
                <label class="text-sm font-semibold text-brand-navy">Add a product to Featured</label>
                <div class="mt-2 flex flex-col gap-3 sm:flex-row">
                    <select x-model="addId"
                        class="flex-1 rounded-lg border border-brand-navy/15 px-4 py-2.5 text-sm focus:border-brand-orange focus:outline-none focus:ring-1 focus:ring-brand-orange bg-white">
                        <option value="">— Select a product to add —</option>
                        <template x-for="p in available" :key="p.id">
                            <option :value="p.id" x-text="p.name + '  (৳' + p.price.toLocaleString() + ')'"></option>
                        </template>
                    </select>
                    <button type="button" @click="add()" :disabled="!addId"
                        class="rounded-lg bg-brand-orange px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-navy disabled:cursor-not-allowed disabled:opacity-40">
                        <i class="fa-solid fa-plus mr-1"></i> Add
                    </button>
                </div>
                <p class="mt-2 text-xs text-gray-500" x-show="available.length === 0" style="display:none;">
                    All active products are already in the list.
                </p>
            </div>

            {{-- Selected products --}}
            <div class="mt-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-400">Featured Products Display Order</h2>
                    <span class="text-xs text-gray-500">First 10 products will be shown on homepage</span>
                </div>

                <template x-if="selected.length === 0">
                    <div class="rounded-xl border border-dashed border-gray-200 bg-white py-14 text-center">
                        <i class="fa-solid fa-star text-4xl text-gray-200"></i>
                        <p class="mt-4 text-gray-500">No featured products added yet. Use “Add a product to Featured” above.</p>
                    </div>
                </template>

                <template x-if="selected.length > 0">
                    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-brand-navy/5">
                        <div class="hidden grid-cols-[60px_1fr_auto_140px_60px] items-center gap-4 border-b border-gray-100 px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-400 sm:grid">
                            <span class="text-center">#</span>
                            <span>Product</span>
                            <span class="text-right">Price</span>
                            <span class="text-center">Serial / Order</span>
                            <span></span>
                        </div>

                        <div class="divide-y divide-gray-100">
                            <template x-for="(item, index) in selected" :key="item.id">
                                <div class="grid grid-cols-[1fr_auto] items-center gap-4 px-5 py-3 sm:grid-cols-[60px_1fr_auto_140px_60px]">
                                    <div class="hidden text-center text-xs font-bold text-gray-400 sm:block" x-text="index + 1"></div>

                                    <div class="flex min-w-0 items-center gap-3">
                                        <img :src="product(item.id).image" class="h-11 w-11 shrink-0 rounded-lg object-cover" :alt="product(item.id).name">
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-medium text-brand-navy" x-text="product(item.id).name"></p>
                                            <p class="truncate text-xs text-gray-500" x-text="product(item.id).category"></p>
                                        </div>
                                    </div>

                                    <div class="hidden text-right text-sm font-semibold text-brand-navy sm:block">
                                        ৳<span x-text="product(item.id).price.toLocaleString()"></span>
                                    </div>

                                    <div class="col-span-2 flex items-center justify-end gap-2 sm:col-span-1 sm:justify-center">
                                        <span class="text-xs text-gray-400 sm:hidden">Order</span>
                                        <div class="flex items-center gap-1">
                                            <button type="button" @click="moveUp(index)" :disabled="index === 0"
                                                class="h-7 w-7 rounded border border-gray-200 text-xs text-gray-600 hover:bg-gray-100 disabled:opacity-30 disabled:cursor-not-allowed">
                                                <i class="fa-solid fa-arrow-up"></i>
                                            </button>
                                            <input type="number" min="1" x-model.number="item.order"
                                                class="w-16 rounded-lg border border-gray-300 px-2 py-1.5 text-center text-sm focus:border-brand-orange focus:outline-none focus:ring-1 focus:ring-brand-orange">
                                            <button type="button" @click="moveDown(index)" :disabled="index === selected.length - 1"
                                                class="h-7 w-7 rounded border border-gray-200 text-xs text-gray-600 hover:bg-gray-100 disabled:opacity-30 disabled:cursor-not-allowed">
                                                <i class="fa-solid fa-arrow-down"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="flex justify-end">
                                        <button type="button" @click="remove(item.id)"
                                            class="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 transition hover:bg-red-50 hover:text-red-500" title="Remove">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>

                                    {{-- Hidden inputs submitted to server --}}
                                    <input type="hidden" name="products[]" :value="item.id">
                                    <input type="hidden" :name="`order[${item.id}]`" :value="item.order">
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
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
        window.featuredProducts = function (products) {
            return {
                all: products || [],
                addId: '',
                selected: (products || [])
                    .filter((p) => p.selected)
                    .sort((a, b) => a.order - b.order)
                    .map((p, idx) => ({ id: p.id, order: p.order || (idx + 1) })),

                get available() {
                    return this.all.filter((p) => !this.isSelected(p.id));
                },
                isSelected(id) {
                    return this.selected.some((s) => s.id === id);
                },
                product(id) {
                    return this.all.find((p) => p.id === id) || { name: '', category: '', price: 0, image: '' };
                },
                add() {
                    const id = parseInt(this.addId);
                    if (!id || this.isSelected(id)) return;
                    this.selected.push({ id, order: this.selected.length + 1 });
                    this.addId = '';
                },
                remove(id) {
                    this.selected = this.selected.filter((s) => s.id !== id);
                    this.recalcOrders();
                },
                moveUp(index) {
                    if (index <= 0) return;
                    const temp = this.selected[index];
                    this.selected[index] = this.selected[index - 1];
                    this.selected[index - 1] = temp;
                    this.recalcOrders();
                },
                moveDown(index) {
                    if (index >= this.selected.length - 1) return;
                    const temp = this.selected[index];
                    this.selected[index] = this.selected[index + 1];
                    this.selected[index + 1] = temp;
                    this.recalcOrders();
                },
                recalcOrders() {
                    this.selected.forEach((item, idx) => {
                        item.order = idx + 1;
                    });
                }
            };
        };
    </script>
</x-admin-layout>

