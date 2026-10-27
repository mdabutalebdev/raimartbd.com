<x-admin-layout title="Edit Order {{ $order->order_number }} - Raimart Admin">
    <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-gray-200">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="font-serif text-2xl font-bold text-gray-900">Edit Order {{ $order->order_number }}</h1>
                <span class="rounded-full px-2.5 py-0.5 text-xs font-bold uppercase tracking-wider
                    @if ($order->status === 'delivered') bg-emerald-100 text-emerald-800
                    @elseif ($order->status === 'cancelled') bg-rose-100 text-rose-800
                    @elseif ($order->status === 'shipped') bg-blue-100 text-blue-800
                    @elseif ($order->status === 'processing') bg-amber-100 text-amber-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
            <p class="mt-1 text-xs text-brand-navy/60">Modify customer info, order items, quantities, pricing or order status.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-semibold text-gray-700 shadow-xs hover:bg-gray-50 transition">
                <i class="fa-solid fa-arrow-left text-xs"></i> Back to Order Details
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-600">
            <div class="flex items-center gap-2 font-bold mb-1">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>Please correct the errors below:</span>
            </div>
            <ul class="list-inside list-disc text-xs space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $productData = $products->mapWithKeys(fn ($p) => [$p->id => [
            'name' => $p->name,
            'price' => (float) $p->price,
            'stock' => (int) $p->stock,
        ]])->all();

        $existingLines = $order->items->map(fn ($item) => [
            'product_id' => (string) $item->product_id,
            'quantity' => (int) $item->quantity,
        ])->values()->all();
    @endphp

    <div x-data="editOrder(
        @js($productData),
        @js($existingLines),
        {{ $areas['inside']['fee'] }},
        {{ $areas['outside']['fee'] }},
        '{{ addslashes($order->city) }}',
        {{ (float) $order->discount }},
        {{ (float) $order->shipping_fee }}
    )">
        <form id="edit-order-form" action="{{ route('admin.orders.update', $order) }}" method="POST"
            class="mt-6 grid items-start gap-6 lg:grid-cols-[1fr_360px]">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                {{-- Order Items --}}
                <div class="rounded-xl border border-brand-navy/10 bg-white p-5 shadow-xs">
                    <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-boxes-stacked text-brand-orange text-sm"></i>
                            <h2 class="font-serif text-base font-bold text-gray-900">Ordered Products</h2>
                        </div>
                        <span class="text-xs text-gray-500" x-text="lines.length + ' item(s)'"></span>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(line, index) in lines" :key="index">
                            <div class="p-3 rounded-lg border border-gray-100 bg-gray-50/60 grid gap-2.5 sm:grid-cols-[1fr_110px_110px_42px] items-center">
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-500 mb-1">Select Product *</label>
                                    <select :name="`items[${index}][product_id]`" x-model="line.product_id" required
                                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">
                                        <option value="">Select a product</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">
                                                {{ $product->name }} — ৳{{ number_format($product->price) }} ({{ $product->stock }} in stock)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-500 mb-1">Quantity *</label>
                                    <input type="number" :name="`items[${index}][quantity]`" x-model.number="line.quantity" min="1" required
                                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none text-center">
                                </div>

                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-500 mb-1">Subtotal</label>
                                    <div class="flex items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-bold text-gray-800"
                                        x-text="'৳' + lineTotal(line).toLocaleString()"></div>
                                </div>

                                <div class="pt-5 flex justify-center">
                                    <button type="button" @click="removeLine(index)" x-show="lines.length > 1" title="Remove item"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-600 transition hover:bg-rose-600 hover:text-white cursor-pointer">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="addLine()"
                        class="mt-3 inline-flex items-center gap-2 rounded-lg border border-brand-orange px-4 py-2 text-xs font-bold text-brand-orange transition hover:bg-brand-orange hover:text-white cursor-pointer">
                        <i class="fa-solid fa-plus text-xs"></i> Add Another Product
                    </button>
                </div>

                {{-- Customer Information --}}
                <div class="rounded-xl border border-brand-navy/10 bg-white p-5 shadow-xs">
                    <div class="flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                        <i class="fa-solid fa-user-pen text-brand-orange text-sm"></i>
                        <h2 class="font-serif text-base font-bold text-gray-900">Customer & Shipping Information</h2>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Customer Name *</label>
                            <input type="text" name="name" value="{{ old('name', $order->name) }}" required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Phone Number *</label>
                            <input type="text" name="phone" value="{{ old('phone', $order->phone) }}" required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Email Address (Optional)</label>
                            <input type="email" name="email" value="{{ old('email', $order->email) }}"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Full Street Address *</label>
                            <input type="text" name="address" value="{{ old('address', $order->address) }}" required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">District / City *</label>
                            <select name="city" x-model="district" required
                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">
                                <option value="">Select District</option>
                                @foreach ($districts as $district)
                                    <option value="{{ $district }}" @selected(old('city', $order->city) === $district)>{{ $district }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Thana / Area (Optional)</label>
                            <input type="text" name="thana" value="{{ old('thana', $order->thana) }}" placeholder="Thana"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Customer Note</label>
                            <textarea name="notes" rows="2" placeholder="Customer order note..."
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">{{ old('notes', $order->notes) }}</textarea>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Admin Internal Note</label>
                            <textarea name="admin_note" rows="2" placeholder="Internal remarks for staff only..."
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">{{ old('admin_note', $order->admin_note) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary & Status Column --}}
            <div class="space-y-6">
                {{-- Payment & Status --}}
                <div class="rounded-xl border border-brand-navy/10 bg-white p-5 shadow-xs">
                    <div class="flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                        <i class="fa-solid fa-sliders text-brand-orange text-sm"></i>
                        <h2 class="font-serif text-base font-bold text-gray-900">Status & Payment</h2>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Order Status *</label>
                            <select name="status" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">
                                @foreach (\App\Http\Controllers\Admin\OrderController::STATUSES as $status)
                                    <option value="{{ $status }}" @selected(old('status', $order->status) === $status)>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Payment Method *</label>
                            <select name="payment_method" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">
                                <option value="cod" @selected(old('payment_method', $order->payment_method) === 'cod')>Cash on Delivery</option>
                                <option value="manual" @selected(old('payment_method', $order->payment_method) === 'manual')>Manual / Bank / bKash</option>
                                <option value="piprapay" @selected(old('payment_method', $order->payment_method) === 'piprapay')>PipraPay Gateway</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Payment Status *</label>
                            <select name="payment_status" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs focus:border-brand-orange focus:ring-1 focus:ring-brand-orange focus:outline-none">
                                <option value="unpaid" @selected(old('payment_status', $order->payment_status) === 'unpaid')>Unpaid</option>
                                <option value="paid" @selected(old('payment_status', $order->payment_status) === 'paid')>Paid</option>
                                <option value="refunded" @selected(old('payment_status', $order->payment_status) === 'refunded')>Refunded</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Totals Card --}}
                <div class="rounded-xl border border-brand-navy/10 bg-white p-5 shadow-xs">
                    <div class="flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                        <i class="fa-solid fa-receipt text-brand-orange text-sm"></i>
                        <h2 class="font-serif text-base font-bold text-gray-900">Order Totals</h2>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="flex items-center justify-between py-1">
                            <span class="text-gray-500 font-medium">Subtotal</span>
                            <span class="font-bold text-gray-800 text-sm" x-text="'৳' + subtotal.toLocaleString()"></span>
                        </div>

                        <div class="flex items-center justify-between gap-2 py-1">
                            <label class="text-gray-500 font-medium">Discount (৳)</label>
                            <input type="number" name="discount" x-model.number="discount" min="0" step="1"
                                class="w-24 rounded-lg border border-gray-300 px-2.5 py-1 text-right text-xs font-semibold focus:border-brand-orange focus:outline-none">
                        </div>

                        <div class="flex items-center justify-between gap-2 py-1">
                            <label class="text-gray-500 font-medium">Shipping Fee (৳)</label>
                            <input type="number" name="shipping_fee" x-model.number="shipping" min="0" step="1"
                                class="w-24 rounded-lg border border-gray-300 px-2.5 py-1 text-right text-xs font-semibold focus:border-brand-orange focus:outline-none">
                        </div>

                        <div class="flex justify-between items-center border-t border-gray-200 pt-3 text-sm font-bold">
                            <span class="text-gray-900">Total Amount</span>
                            <span class="text-brand-orange text-base" x-text="'৳' + total.toLocaleString()"></span>
                        </div>
                    </div>

                    <button type="button" @click="confirmSave()"
                        class="mt-5 w-full flex items-center justify-center gap-2 rounded-xl bg-brand-orange hover:bg-brand-navy py-3 px-4 text-xs font-bold uppercase tracking-wider text-white transition shadow-sm cursor-pointer">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Save Changes</span>
                    </button>
                    <p class="mt-2 text-center text-[11px] text-gray-400">Inventory stock will automatically be synchronized.</p>
                </div>
            </div>
        </form>

        {{-- Edit Confirmation Modal --}}
        <div x-show="showConfirmModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl ring-1 ring-black/5"
                @click.away="showConfirmModal = false">
                <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                    <div class="h-10 w-10 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-pen-to-square text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-serif text-base font-bold text-gray-900">Confirm Order Update</h3>
                        <p class="text-xs text-gray-500">Order #{{ $order->order_number }}</p>
                    </div>
                </div>
                <div class="mt-3 text-xs text-gray-600 leading-relaxed">
                    Are you sure you want to save these changes? The customer details, items, stock adjustments, and total payable will be updated.
                </div>
                <div class="mt-5 flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                    <button type="button" @click="showConfirmModal = false"
                        class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition cursor-pointer">
                        Cancel
                    </button>
                    <button type="button" @click="submitForm()"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-brand-orange px-4 py-2 text-xs font-bold text-white hover:bg-brand-navy transition cursor-pointer">
                        <i class="fa-solid fa-check"></i>
                        <span>Yes, Save Changes</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function editOrder(products, initialLines, insideFee, outsideFee, initialDistrict, initialDiscount, initialShipping) {
            return {
                products,
                lines: initialLines && initialLines.length > 0 ? initialLines : [{ product_id: '', quantity: 1 }],
                district: initialDistrict || '',
                discount: Number(initialDiscount) || 0,
                shipping: Number(initialShipping) || insideFee,
                showConfirmModal: false,

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
                init() {
                    this.$watch('district', (value) => {
                        if (value === 'Dhaka') {
                            this.shipping = insideFee;
                        } else if (value) {
                            this.shipping = outsideFee;
                        }
                    });
                },
                confirmSave() {
                    this.showConfirmModal = true;
                },
                submitForm() {
                    this.showConfirmModal = false;
                    document.getElementById('edit-order-form').submit();
                }
            };
        }
    </script>
</x-admin-layout>
