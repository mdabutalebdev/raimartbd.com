@csrf

@php
    $initialAppliesTo = old('applies_to', $coupon->applies_to ?? 'full_store');
    $initialProducts = collect(old('product_ids', $coupon->product_ids ?? []))->map(fn ($id) => (int) $id)->values();
    $initialCategories = collect(old('category_ids', $coupon->category_ids ?? []))->map(fn ($id) => (int) $id)->values();
    $formConfig = [
        'initialAppliesTo' => $initialAppliesTo,
        'initialProducts' => $initialProducts,
        'initialCategories' => $initialCategories,
        'products' => $products ?? [],
        'categories' => $categories ?? [],
    ];
@endphp

<div x-data="couponTargeting({{ Illuminate\Support\Js::from($formConfig) }})" class="space-y-6 rounded-xl bg-white p-6 shadow-sm ring-1 ring-brand-navy/5">
    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-gray-700">Coupon Code <span class="text-red-500">*</span></label>
            <input type="text" name="code" value="{{ old('code', $coupon->code) }}" required placeholder="EID25"
                class="mt-1 w-full rounded-lg border border-brand-navy/15 px-4 py-2.5 text-sm uppercase focus:border-brand-orange focus:outline-none">
            <p class="mt-1 text-xs text-gray-500">Customers type this at checkout. Saved in capitals.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Discount Type <span class="text-red-500">*</span></label>
            @php $type = old('type', $coupon->type ?? 'percent'); @endphp
            <select name="type" class="mt-1 w-full rounded-lg border border-brand-navy/15 px-4 py-2.5 text-sm focus:border-brand-orange focus:outline-none">
                <option value="percent" @selected($type === 'percent')>Percentage (%)</option>
                <option value="fixed" @selected($type === 'fixed')>Fixed amount (৳)</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Discount Value <span class="text-red-500">*</span></label>
            <input type="number" step="0.01" min="0" name="value" value="{{ old('value', $coupon->value) }}" required placeholder="10"
                class="mt-1 w-full rounded-lg border border-brand-navy/15 px-4 py-2.5 text-sm focus:border-brand-orange focus:outline-none">
            <p class="mt-1 text-xs text-gray-500">e.g. 10 = 10% off, or ৳10 off (depending on type).</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Max Discount (৳)</label>
            <input type="number" step="0.01" min="0" name="max_discount" value="{{ old('max_discount', $coupon->max_discount) }}" placeholder="Leave empty for no cap"
                class="mt-1 w-full rounded-lg border border-brand-navy/15 px-4 py-2.5 text-sm focus:border-brand-orange focus:outline-none">
            <p class="mt-1 text-xs text-gray-500">Caps a percentage coupon (ignored for fixed amounts).</p>
        </div>
    </div>

    {{-- APPLY COUPON TO (Targeting Selection) --}}
    <div class="border-t border-brand-navy/10 pt-5">
        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">Apply Coupon To</label>

        {{-- Hidden input storing current selection --}}
        <input type="hidden" name="applies_to" :value="appliesTo">

        {{-- 3 Mode Selection Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            {{-- Full Store --}}
            <button type="button" @click="appliesTo = 'full_store'"
                class="flex flex-col items-center justify-center p-4 rounded-xl border text-center transition cursor-pointer"
                :class="appliesTo === 'full_store' ? 'border-2 border-brand-orange bg-brand-orange/5 text-brand-orange font-bold shadow-sm' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300 hover:bg-gray-50/50'">
                <i class="fa-solid fa-box text-2xl mb-2"></i>
                <span class="text-xs font-bold uppercase tracking-wide">Full Store</span>
                <span class="text-[11px] text-gray-400 mt-0.5 font-normal">Applies to all products</span>
            </button>

            {{-- Specific Products --}}
            <button type="button" @click="appliesTo = 'specific_products'"
                class="flex flex-col items-center justify-center p-4 rounded-xl border text-center transition cursor-pointer"
                :class="appliesTo === 'specific_products' ? 'border-2 border-brand-orange bg-brand-orange/5 text-brand-orange font-bold shadow-sm' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300 hover:bg-gray-50/50'">
                <i class="fa-solid fa-tag text-2xl mb-2"></i>
                <span class="text-xs font-bold uppercase tracking-wide">Specific Products</span>
                <span class="text-[11px] text-gray-400 mt-0.5 font-normal">Select individual products</span>
            </button>

            {{-- Specific Categories --}}
            <button type="button" @click="appliesTo = 'specific_categories'"
                class="flex flex-col items-center justify-center p-4 rounded-xl border text-center transition cursor-pointer"
                :class="appliesTo === 'specific_categories' ? 'border-2 border-brand-orange bg-brand-orange/5 text-brand-orange font-bold shadow-sm' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300 hover:bg-gray-50/50'">
                <i class="fa-solid fa-layer-group text-2xl mb-2"></i>
                <span class="text-xs font-bold uppercase tracking-wide">Specific Categories</span>
                <span class="text-[11px] text-gray-400 mt-0.5 font-normal">Select specific categories</span>
            </button>
        </div>

        {{-- SPECIFIC PRODUCTS PANEL --}}
        <div x-show="appliesTo === 'specific_products'" x-cloak class="mt-4 rounded-xl border border-gray-200 bg-gray-50/50 p-4">
            <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-gray-200">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-brand-navy">Products</span>
                    <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800">
                        <span x-text="selectedProductCount"></span> Selected
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="selectAllProducts()"
                        class="text-xs font-semibold text-brand-orange hover:underline cursor-pointer">Select All</button>
                    <span class="text-gray-300">|</span>
                    <button type="button" @click="clearAllProducts()"
                        class="text-xs font-semibold text-gray-500 hover:underline cursor-pointer">Clear All</button>
                </div>
            </div>

            <div class="relative mt-3">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                <input type="text" x-model="productSearch" placeholder="Search products by name..."
                    class="w-full rounded-lg border border-gray-300 bg-white pl-9 pr-4 py-2 text-sm focus:border-brand-orange focus:outline-none">
            </div>

            <div class="mt-3 max-h-72 overflow-y-auto divide-y divide-gray-100 rounded-lg border border-gray-200 bg-white">
                <template x-for="p in filteredProducts" :key="p.id">
                    <label class="flex items-center gap-3 p-3 transition hover:bg-orange-50/20 cursor-pointer"
                        :class="isProductSelected(p.id) ? 'bg-orange-50/40' : ''">
                        <input type="checkbox" name="product_ids[]" :value="p.id"
                            :checked="isProductSelected(p.id)"
                            @change="toggleProduct(p.id)"
                            class="h-4 w-4 rounded border-gray-300 text-brand-orange focus:ring-brand-orange cursor-pointer">
                        <template x-if="p.image">
                            <img :src="p.image" class="h-10 w-10 shrink-0 rounded-lg object-cover border border-gray-100" :alt="p.name">
                        </template>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-brand-navy" x-text="p.name"></p>
                            <p class="text-xs text-gray-500">৳<span x-text="p.price.toLocaleString()"></span></p>
                        </div>
                    </label>
                </template>

                <div x-show="filteredProducts.length === 0" class="py-8 text-center text-xs text-gray-400">
                    No products found matching "<span x-text="productSearch"></span>"
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-500">Only the selected products in customer's cart will receive the coupon discount.</p>
        </div>

        {{-- SPECIFIC CATEGORIES PANEL --}}
        <div x-show="appliesTo === 'specific_categories'" x-cloak class="mt-4 rounded-xl border border-gray-200 bg-gray-50/50 p-4">
            <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-gray-200">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-brand-navy">Categories</span>
                    <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800">
                        <span x-text="selectedCategoryCount"></span> Selected
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="selectAllCategories()"
                        class="text-xs font-semibold text-brand-orange hover:underline cursor-pointer">Select All</button>
                    <span class="text-gray-300">|</span>
                    <button type="button" @click="clearAllCategories()"
                        class="text-xs font-semibold text-gray-500 hover:underline cursor-pointer">Clear All</button>
                </div>
            </div>

            <div class="relative mt-3">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                <input type="text" x-model="categorySearch" placeholder="Search categories by name..."
                    class="w-full rounded-lg border border-gray-300 bg-white pl-9 pr-4 py-2 text-sm focus:border-brand-orange focus:outline-none">
            </div>

            <div class="mt-3 max-h-72 overflow-y-auto divide-y divide-gray-100 rounded-lg border border-gray-200 bg-white">
                <template x-for="c in filteredCategories" :key="c.id">
                    <label class="flex items-center gap-3 p-3 transition hover:bg-orange-50/20 cursor-pointer"
                        :class="isCategorySelected(c.id) ? 'bg-orange-50/40' : ''">
                        <input type="checkbox" name="category_ids[]" :value="c.id"
                            :checked="isCategorySelected(c.id)"
                            @change="toggleCategory(c.id)"
                            class="h-4 w-4 rounded border-gray-300 text-brand-orange focus:ring-brand-orange cursor-pointer">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-brand-navy" x-text="c.name"></p>
                        </div>
                    </label>
                </template>

                <div x-show="filteredCategories.length === 0" class="py-8 text-center text-xs text-gray-400">
                    No categories found matching "<span x-text="categorySearch"></span>"
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-500">Products belonging to the selected categories (including subcategories) will receive the coupon discount.</p>
        </div>
    </div>

    <div class="grid gap-5 sm:grid-cols-2 border-t border-brand-navy/10 pt-5">
        <div>
            <label class="block text-sm font-medium text-gray-700">Minimum Order (৳)</label>
            <input type="number" step="0.01" min="0" name="min_order_amount" value="{{ old('min_order_amount', $coupon->min_order_amount) }}" placeholder="No minimum"
                class="mt-1 w-full rounded-lg border border-brand-navy/15 px-4 py-2.5 text-sm focus:border-brand-orange focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Usage Limit</label>
            <input type="number" min="1" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}" placeholder="Unlimited"
                class="mt-1 w-full rounded-lg border border-brand-navy/15 px-4 py-2.5 text-sm focus:border-brand-orange focus:outline-none">
            <p class="mt-1 text-xs text-gray-500">Total number of times this code can be used.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Starts At</label>
            <input type="date" name="starts_at" value="{{ old('starts_at', $coupon->starts_at?->format('Y-m-d')) }}"
                class="mt-1 w-full rounded-lg border border-brand-navy/15 px-4 py-2.5 text-sm focus:border-brand-orange focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Expires At</label>
            <input type="date" name="expires_at" value="{{ old('expires_at', $coupon->expires_at?->format('Y-m-d')) }}"
                class="mt-1 w-full rounded-lg border border-brand-navy/15 px-4 py-2.5 text-sm focus:border-brand-orange focus:outline-none">
        </div>
    </div>

    <div class="grid gap-4 border-t border-brand-navy/10 pt-5 sm:grid-cols-2">
        <label class="flex cursor-pointer items-center gap-3">
            <input type="hidden" name="free_shipping" value="0">
            <input type="checkbox" name="free_shipping" value="1" @checked(old('free_shipping', $coupon->free_shipping))
                class="h-4 w-4 rounded border-brand-navy/30 text-brand-orange focus:ring-brand-orange">
            <span class="text-sm font-medium">Also make delivery free</span>
        </label>

        <label class="flex cursor-pointer items-center gap-3">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $coupon->is_active ?? true))
                class="h-4 w-4 rounded border-brand-navy/30 text-brand-orange focus:ring-brand-orange">
            <span class="text-sm font-medium">Active</span>
        </label>
    </div>

    <div class="flex items-center gap-3 pt-2">
        <button type="submit" class="rounded-lg bg-brand-orange px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-navy">
            {{ $submitLabel ?? 'Save Coupon' }}
        </button>
        <a href="{{ route('admin.coupons.index') }}" class="text-sm font-medium text-brand-navy/60 hover:text-brand-navy">Cancel</a>
    </div>
</div>

<script>
    window.couponTargeting = function (config) {
        return {
            appliesTo: config.initialAppliesTo || 'full_store',
            selectedProducts: (config.initialProducts || []).map(Number),
            selectedCategories: (config.initialCategories || []).map(Number),
            allProducts: config.products || [],
            allCategories: config.categories || [],
            productSearch: '',
            categorySearch: '',

            get selectedProductCount() {
                return this.selectedProducts.length;
            },
            get selectedCategoryCount() {
                return this.selectedCategories.length;
            },

            get filteredProducts() {
                const q = this.productSearch.trim().toLowerCase();
                if (!q) return this.allProducts;
                return this.allProducts.filter((p) => p.name && p.name.toLowerCase().includes(q));
            },
            get filteredCategories() {
                const q = this.categorySearch.trim().toLowerCase();
                if (!q) return this.allCategories;
                return this.allCategories.filter((c) => c.name && c.name.toLowerCase().includes(q));
            },

            isProductSelected(id) {
                return this.selectedProducts.includes(Number(id));
            },
            toggleProduct(id) {
                const num = Number(id);
                const idx = this.selectedProducts.indexOf(num);
                if (idx > -1) {
                    this.selectedProducts.splice(idx, 1);
                } else {
                    this.selectedProducts.push(num);
                }
            },
            selectAllProducts() {
                this.selectedProducts = this.allProducts.map((p) => Number(p.id));
            },
            clearAllProducts() {
                this.selectedProducts = [];
            },

            isCategorySelected(id) {
                return this.selectedCategories.includes(Number(id));
            },
            toggleCategory(id) {
                const num = Number(id);
                const idx = this.selectedCategories.indexOf(num);
                if (idx > -1) {
                    this.selectedCategories.splice(idx, 1);
                } else {
                    this.selectedCategories.push(num);
                }
            },
            selectAllCategories() {
                this.selectedCategories = this.allCategories.map((c) => Number(c.id));
            },
            clearAllCategories() {
                this.selectedCategories = [];
            }
        };
    };
</script>
