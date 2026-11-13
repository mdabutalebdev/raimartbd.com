<x-admin-layout title="Create Order - Raimart Admin">
    <div class="flex items-center justify-between gap-3 pb-3 border-b border-gray-100">
        <div>
            <h1 class="font-serif text-2xl font-bold text-gray-900">Create Order</h1>
            <p class="mt-0.5 text-xs sm:text-sm text-brand-navy/60">Place an order by hand — for phone, WhatsApp or walk-in customers.</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" wire:navigate class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-brand-navy/70 hover:text-brand-orange transition cursor-pointer">
            <i class="fa-solid fa-arrow-left text-xs"></i> Back to orders
        </a>
    </div>

    @if ($errors->any())
        <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-600">
            <div class="flex items-center gap-2 font-bold mb-1">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>Please fix the following errors:</span>
            </div>
            <ul class="list-inside list-disc text-xs space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        // [id => [name, price, stock]] for the line-item picker
        $productData = $products->mapWithKeys(fn ($p) => [$p->id => [
            'name' => $p->name,
            'price' => (float) $p->price,
            'stock' => (int) $p->stock,
        ]])->all();
    @endphp

    <form action="{{ route('admin.orders.store') }}" method="POST"
        x-data="{
            products: @js($productData),
            thanaMap: @js($thanaMap ?? []),
            lines: [{ product_id: '', quantity: 1 }],
            district: '{{ old('city', 'Dhaka') }}',
            thana: '{{ old('thana', '') }}',
            discount: {{ (float) old('discount', 0) }},
            shipping: {{ old('city') === 'Dhaka' || !old('city') ? $areas['inside']['fee'] : $areas['outside']['fee'] }},
            insideFee: {{ $areas['inside']['fee'] }},
            outsideFee: {{ $areas['outside']['fee'] }},

            addLine() {
                this.lines.push({ product_id: '', quantity: 1 });
            },
            removeLine(index) {
                if (this.lines.length > 1) {
                    this.lines.splice(index, 1);
                }
            },
            lineTotal(line) {
                const product = this.products[line.product_id];
                return product ? Math.round(product.price * (line.quantity || 0)) : 0;
            },
            get subtotal() {
                return this.lines.reduce((sum, line) => sum + this.lineTotal(line), 0);
            },
            get total() {
                return Math.max(0, this.subtotal - (this.discount || 0)) + (this.shipping || 0);
            },
            get availableThanas() {
                return this.thanaMap[this.district] || [];
            },
            onDistrictChange() {
                this.shipping = this.district === 'Dhaka' ? this.insideFee : this.outsideFee;
                this.thana = '';
            }
        }"
        class="mt-4 grid items-start gap-5 lg:grid-cols-[minmax(0,1fr)_320px] min-w-0">
        @csrf

        {{-- Left: Products & Customer Details --}}
        <div class="space-y-5 min-w-0">
            {{-- Ordered Products --}}
            <div class="rounded-2xl border border-brand-navy/10 bg-white p-5 shadow-xs min-w-0">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
                    <h2 class="border-l-4 border-brand-orange pl-3 text-base font-bold text-gray-900">Products</h2>
                    <span class="text-xs text-gray-400 font-medium" x-text="lines.length + ' item(s)'"></span>
                </div>

                <div class="space-y-3 min-w-0">
                    <template x-for="(line, index) in lines" :key="index">
                        <div class="grid gap-2 grid-cols-[minmax(0,1fr)_75px_85px_34px] sm:grid-cols-[minmax(0,1fr)_90px_110px_36px] items-center p-2.5 rounded-xl border border-gray-100 bg-gray-50/60 min-w-0">
                            {{-- Product Dropdown --}}
                            <div class="min-w-0">
                                <select :name="`items[${index}][product_id]`" x-model="line.product_id" required
                                    class="w-full min-w-0 truncate rounded-lg border border-gray-300 bg-white px-2.5 sm:px-3 py-2 text-xs sm:text-sm text-gray-900 focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">
                                    <option value="">Select product *</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">
                                            {{ $product->name }} — ৳{{ number_format($product->price) }} ({{ $product->stock }} in stock)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Quantity --}}
                            <div class="min-w-0">
                                <input type="number" :name="`items[${index}][quantity]`" x-model.number="line.quantity" min="1" required placeholder="Qty"
                                    class="w-full min-w-0 rounded-lg border border-gray-300 bg-white px-2 py-2 text-center text-xs sm:text-sm text-gray-900 focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">
                            </div>

                            {{-- Line Subtotal --}}
                            <div class="min-w-0">
                                <div class="flex h-[38px] items-center justify-end rounded-lg border border-gray-200 bg-white px-2.5 text-xs sm:text-sm font-bold text-gray-800 truncate"
                                    x-text="'৳' + lineTotal(line).toLocaleString()"></div>
                            </div>

                            {{-- Delete Action --}}
                            <div class="flex justify-center">
                                <button type="button" @click="removeLine(index)" x-show="lines.length > 1"
                                    class="flex h-8 w-8 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-600 transition hover:bg-rose-600 hover:text-white cursor-pointer"
                                    title="Remove item">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <button type="button" @click="addLine()"
                    class="mt-3.5 inline-flex items-center gap-1.5 rounded-xl border border-brand-orange px-4 py-2 text-xs font-bold text-brand-orange transition hover:bg-brand-orange hover:text-white cursor-pointer shadow-2xs">
                    <i class="fa-solid fa-plus text-xs"></i> Add Another Product
                </button>
            </div>

            {{-- Customer Information (Checkout Style) --}}
            <div class="rounded-2xl border border-brand-navy/10 bg-white p-5 shadow-xs min-w-0">
                <h2 class="mb-4 border-l-4 border-brand-orange pl-3 text-base font-bold text-gray-900">Customer &amp; Shipping Address</h2>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Md Abu Taleb"
                            class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Mobile Number *</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="01XXXXXXXXX"
                            class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Email Address (Optional)</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="example@mail.com"
                            class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Delivery Address *</label>
                        <input type="text" name="address" value="{{ old('address') }}" required placeholder="House / Street / Area address *"
                            class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">
                    </div>

                    {{-- District (Zilla) --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">District / City *</label>
                        <select name="city" x-model="district" @change="onDistrictChange()" required
                            class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">
                            <option value="">Select District *</option>
                            @foreach ($districts as $district)
                                <option value="{{ $district }}" @selected(old('city') === $district)>{{ $district }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Thana / Upazila (follows District) --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Thana / Upazila (Optional)</label>
                        <select name="thana" x-model="thana"
                            class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">
                            <option value="">Select Thana (Optional)</option>
                            <template x-for="t in availableThanas" :key="t">
                                <option :value="t" x-text="t" :selected="t === thana"></option>
                            </template>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Order Note (Optional)</label>
                        <textarea name="notes" rows="2" placeholder="Special customer instructions (optional)..."
                            class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-xs sm:text-sm text-gray-900 focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Payment, Status & Totals Summary --}}
        <div class="space-y-5 min-w-0">
            {{-- Payment & Status --}}
            <div class="rounded-2xl border border-brand-navy/10 bg-white p-5 shadow-xs">
                <h2 class="mb-3.5 border-l-4 border-brand-orange pl-3 text-base font-bold text-gray-900">Payment &amp; Status</h2>

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Payment Method</label>
                        <select name="payment_method" class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-xs sm:text-sm text-gray-900 focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">
                            <option value="cod">Cash on Delivery</option>
                            <option value="manual">Manual / Bank Transfer</option>
                            <option value="piprapay">PipraPay Gateway</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Payment Status</label>
                        <select name="payment_status" class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-xs sm:text-sm text-gray-900 focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">
                            <option value="unpaid">Unpaid</option>
                            <option value="paid">Paid</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Order Status</label>
                        <select name="status" class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-xs sm:text-sm text-gray-900 focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">
                            @foreach (\App\Http\Controllers\Admin\OrderController::STATUSES as $status)
                                <option value="{{ $status }}" @selected($status === 'pending')>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Totals Summary --}}
            <div class="rounded-2xl border border-brand-navy/10 bg-white p-5 shadow-xs">
                <h2 class="mb-3.5 border-l-4 border-brand-orange pl-3 text-base font-bold text-gray-900">Totals</h2>

                <div class="space-y-2.5 text-xs sm:text-sm">
                    <div class="flex items-center justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span class="font-bold text-gray-900" x-text="'৳' + subtotal.toLocaleString()"></span>
                    </div>

                    <div class="flex items-center justify-between gap-2 text-gray-600">
                        <label>Discount ৳</label>
                        <input type="number" name="discount" x-model.number="discount" min="0" step="1"
                            class="w-24 rounded-lg border border-gray-300 px-2 py-1 text-right text-xs sm:text-sm font-semibold text-gray-900 focus:border-brand-orange focus:outline-none">
                    </div>

                    <div class="flex items-center justify-between gap-2 text-gray-600">
                        <div>
                            <span>Delivery ৳</span>
                            <span class="block text-[10px] text-gray-400" x-text="district === 'Dhaka' ? 'Inside Dhaka rate' : 'Outside Dhaka rate'"></span>
                        </div>
                        <input type="number" name="shipping_fee" x-model.number="shipping" min="0" step="1"
                            class="w-24 rounded-lg border border-gray-300 px-2 py-1 text-right text-xs sm:text-sm font-semibold text-gray-900 focus:border-brand-orange focus:outline-none">
                    </div>

                    <div class="flex justify-between items-center border-t border-gray-100 pt-3 text-sm sm:text-base font-bold text-gray-900">
                        <span>Grand Total</span>
                        <span class="text-brand-orange text-lg font-extrabold" x-text="'৳' + total.toLocaleString()"></span>
                    </div>
                </div>

                <button type="submit" class="mt-5 w-full rounded-xl bg-brand-orange py-3 text-xs sm:text-sm font-bold uppercase tracking-wider text-white transition hover:bg-brand-navy shadow-sm cursor-pointer">
                    Create Order
                </button>
                <p class="mt-2 text-center text-[11px] text-gray-400">Stock is automatically updated and Telegram alert is sent.</p>
            </div>
        </div>
    </form>
</x-admin-layout>
