<x-admin-layout title="Order {{ $order->order_number }} - Raimart Admin">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <h1 class="font-serif text-2xl font-bold">Order {{ $order->order_number }}</h1>
            <span class="rounded-full px-2.5 py-0.5 text-xs font-bold uppercase tracking-wider
                @if ($order->status === 'delivered') bg-emerald-100 text-emerald-800
                @elseif ($order->status === 'cancelled') bg-rose-100 text-rose-800
                @elseif ($order->status === 'shipped') bg-blue-100 text-blue-800
                @elseif ($order->status === 'processing') bg-amber-100 text-amber-800
                @else bg-gray-100 text-gray-800 @endif">
                {{ ucfirst($order->status) }}
            </span>
        </div>
        <div class="flex items-center gap-2.5">
            {{-- Edit Order Button --}}
            <button type="button" onclick="openEditConfirmModal()"
                class="inline-flex items-center gap-1.5 rounded-lg border border-brand-navy/20 bg-white px-3.5 py-2 text-xs font-bold text-brand-navy shadow-xs transition hover:bg-brand-navy hover:text-white cursor-pointer">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Edit Order</span>
            </button>

            {{-- Delete Order Button --}}
            <button type="button" onclick="openDeleteConfirmModal()"
                class="inline-flex items-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-3.5 py-2 text-xs font-bold text-rose-600 shadow-xs transition hover:bg-rose-600 hover:text-white cursor-pointer">
                <i class="fa-solid fa-trash-can"></i>
                <span>Delete Order</span>
            </button>

            {{-- Invoice Button --}}
            <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-orange px-4 py-2 text-xs font-bold text-white shadow-xs transition hover:bg-brand-navy cursor-pointer">
                <i class="fa-solid fa-file-invoice"></i>
                <span>Invoice</span>
            </a>

            <a href="{{ route('admin.orders.index') }}" class="text-xs font-medium text-brand-navy/60 hover:text-brand-orange ml-1">&larr; Back to Orders</a>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-brand-navy/5 lg:col-span-2">
            <h2 class="font-serif text-lg font-bold">Items</h2>
            <table class="mt-4 w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-brand-navy/10 text-brand-navy/50">
                        <th class="pb-2 font-medium">Product</th>
                        <th class="pb-2 font-medium">Price</th>
                        <th class="pb-2 font-medium">Qty</th>
                        <th class="pb-2 font-medium">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr class="border-b border-brand-navy/5">
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-12 w-12 rounded-lg border border-brand-navy/10 bg-gray-50 overflow-hidden shrink-0 flex items-center justify-center">
                                        <img src="{{ image_url($item->product?->main_image, urlencode($item->product_name)) }}" alt="{{ $item->product_name }}" class="h-full w-full object-cover">
                                    </div>
                                    <div class="min-w-0">
                                        @if ($item->product)
                                            <a href="{{ route('products.show', $item->product->slug) }}" target="_blank" class="font-medium text-brand-navy hover:text-brand-orange transition line-clamp-2" title="View product on site">
                                                {{ $item->product_name }}
                                            </a>
                                        @else
                                            <span class="font-medium text-brand-navy">{{ $item->product_name }}</span>
                                        @endif
                                        @if (! empty($item->options))
                                            <p class="text-xs text-brand-navy/50 mt-0.5">
                                                @foreach ($item->options as $key => $val)
                                                    {{ ucfirst($key) }}: {{ $val }}@if (! $loop->last) &middot; @endif
                                                @endforeach
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 whitespace-nowrap">৳{{ number_format($item->price, 2) }}</td>
                            <td class="py-3">{{ $item->quantity }}</td>
                            <td class="py-3 whitespace-nowrap">৳{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4 space-y-1 text-sm">
                <div class="flex justify-between"><span class="text-brand-navy/60">Subtotal</span><span>৳{{ number_format($order->subtotal, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-brand-navy/60">Shipping</span><span>৳{{ number_format($order->shipping_fee, 2) }}</span></div>
                <div class="flex justify-between font-semibold"><span>Total</span><span>৳{{ number_format($order->total, 2) }}</span></div>
            </div>

            @if ($order->payments->count())
                <h2 class="mt-6 font-serif text-lg font-bold">Payments</h2>
                <table class="mt-3 w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-brand-navy/10 text-brand-navy/50">
                            <th class="pb-2 font-medium">Gateway</th>
                            <th class="pb-2 font-medium">Transaction ID</th>
                            <th class="pb-2 font-medium">Amount</th>
                            <th class="pb-2 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->payments as $payment)
                            <tr class="border-b border-brand-navy/5">
                                <td class="py-3 capitalize">{{ $payment->gateway }}</td>
                                <td class="py-3">{{ $payment->transaction_id ?: '—' }}</td>
                                <td class="py-3">৳{{ number_format($payment->amount, 2) }}</td>
                                <td class="py-3 capitalize">{{ $payment->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="space-y-6">
            <!-- Customer / Shipping Details Card -->
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-brand-navy/5">
                <div class="flex items-center justify-between mb-4 border-b border-brand-navy/10 pb-3">
                    <h2 class="font-serif text-lg font-bold text-gray-900">Shipping Details</h2>
                    <span class="text-xs text-brand-navy/50 font-mono">#{{ $order->order_number }}</span>
                </div>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-regular fa-user text-gray-400"></i> Receiver
                        </dt>
                        <dd class="font-medium text-gray-900 mt-0.5">{{ $order->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-phone text-gray-400"></i> Phone
                        </dt>
                        <dd class="font-medium text-gray-900 mt-0.5 flex items-center justify-between">
                            <span>{{ $order->phone }}</span>
                            <a href="tel:{{ $order->phone }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold">Call</a>
                        </dd>
                    </div>
                    @if ($order->email)
                        <div>
                            <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fa-regular fa-envelope text-gray-400"></i> Email
                            </dt>
                            <dd class="text-gray-700 mt-0.5">{{ $order->email }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-location-dot text-gray-400"></i> Address
                        </dt>
                        <dd class="font-medium text-gray-900 mt-0.5 leading-relaxed">
                            {{ $order->address }}
                            @if ($order->thana), {{ $order->thana }}@endif
                            @if ($order->city), {{ $order->city }}@endif
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Steadfast Courier Card (Matches Image 3) -->
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-brand-navy/5" id="steadfast_courier_card">
                <!-- Header -->
                <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-orange-50 text-brand-orange flex items-center justify-center shrink-0 border border-orange-100">
                            <i class="fa-solid fa-truck-fast text-base"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Steadfast Courier</h3>
                            <p class="text-[11px] text-gray-400">Parcel Delivery &amp; Tracking</p>
                        </div>
                    </div>
                    <div>
                        @if (!empty($order->steadfast_consignment_id) || !empty($order->steadfast_tracking_code))
                            <span id="sf_status_badge" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <i class="fa-solid fa-check text-[10px]"></i> Booked
                            </span>
                        @else
                            <span id="sf_status_badge" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                Not Booked
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Card Body -->
                <div class="mt-4">
                    @if (!empty($order->steadfast_consignment_id) || !empty($order->steadfast_tracking_code))
                        <!-- Already Booked View -->
                        <div id="sf_booked_view" class="space-y-3">
                            <div class="rounded-xl bg-gray-50/90 p-4 border border-gray-200/80 text-xs space-y-2 font-sans">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500 font-medium">Consignment ID:</span>
                                    <div class="flex items-center gap-1.5 font-bold text-gray-900">
                                        <span id="sf_cid_val">#{{ $order->steadfast_consignment_id ?: '—' }}</span>
                                        @if ($order->steadfast_consignment_id)
                                            <button type="button" onclick="copyText('{{ $order->steadfast_consignment_id }}', this)" class="text-gray-400 hover:text-gray-700 cursor-pointer" title="Copy CID">
                                                <i class="fa-regular fa-copy text-[11px]"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500 font-medium">Tracking Code:</span>
                                    <div class="flex items-center gap-1.5 font-mono font-bold text-gray-900">
                                        <span id="sf_tracking_val">{{ $order->steadfast_tracking_code ?: '—' }}</span>
                                        @if ($order->steadfast_tracking_code)
                                            <button type="button" onclick="copyText('{{ $order->steadfast_tracking_code }}', this)" class="text-gray-400 hover:text-gray-700 cursor-pointer" title="Copy Tracking Code">
                                                <i class="fa-regular fa-copy text-[11px]"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500 font-medium">COD Amount:</span>
                                    <span class="font-bold text-gray-900">৳{{ number_format($order->payment_status === 'paid' ? 0 : (float)$order->total, 2) }}</span>
                                </div>
                                @if ($order->steadfast_status)
                                    <div class="flex items-center justify-between pt-1 border-t border-gray-200">
                                        <span class="text-gray-500 font-medium">Current Status:</span>
                                        <span id="sf_live_status" class="font-bold text-emerald-700 uppercase tracking-wide">
                                            {{ str_replace('_', ' ', $order->steadfast_status) }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-2">
                                <a href="https://steadfast.com.bd/t/{{ $order->steadfast_tracking_code ?: $order->steadfast_consignment_id }}" target="_blank"
                                    class="w-full flex items-center justify-center gap-2 rounded-xl bg-[#0f172a] hover:bg-[#1e293b] text-white py-2.5 px-4 text-xs font-semibold shadow-xs transition cursor-pointer">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[11px]"></i>
                                    <span>Track on Steadfast</span>
                                </a>

                                <button type="button" onclick="checkSteadfastStatus()" id="sf_status_btn"
                                    class="w-full flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 py-2.5 px-4 text-xs font-semibold transition cursor-pointer">
                                    <i class="fa-solid fa-arrows-rotate text-[11px]"></i>
                                    <span>Check Status</span>
                                </button>
                            </div>
                        </div>
                    @else
                        <!-- Not Booked View - Fast Booking Form -->
                        <div id="sf_unbooked_view" class="space-y-3">
                            <div class="rounded-xl bg-orange-50/50 p-3.5 border border-orange-100 text-xs text-gray-600 space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500">COD to collect:</span>
                                    <div class="flex items-center gap-1">
                                        <span class="font-bold text-gray-800">৳</span>
                                        <input type="number" id="sf_cod_input" value="{{ $order->payment_status === 'paid' ? '0' : (float)$order->total }}" step="any"
                                            class="w-24 rounded border border-gray-300 bg-white px-2 py-1 text-xs font-bold text-right focus:border-brand-orange focus:outline-none">
                                    </div>
                                </div>
                                <div>
                                    <span class="text-gray-500 block mb-1">Delivery note:</span>
                                    <input type="text" id="sf_note_input" value="{{ $order->notes }}" placeholder="Fragile / Check parcel before receive"
                                        class="w-full rounded border border-gray-300 bg-white px-2.5 py-1 text-xs focus:border-brand-orange focus:outline-none">
                                </div>
                            </div>

                            <button type="button" onclick="sendToSteadfast()" id="sf_send_btn"
                                class="w-full flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white py-3 px-4 text-xs font-bold shadow-xs transition cursor-pointer">
                                <i class="fa-solid fa-paper-plane text-[11px]"></i>
                                <span>Send to Steadfast</span>
                            </button>
                        </div>
                    @endif

                    <!-- Customer Delivery Record Section -->
                    <div class="mt-4 pt-3.5 border-t border-gray-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-shield-halved text-emerald-500 text-sm"></i>
                                <span class="text-xs font-bold text-gray-800">Customer Delivery Record</span>
                            </div>
                            <button type="button" onclick="checkCustomerRecord()" id="btn_customer_record"
                                class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-600 hover:text-brand-orange transition cursor-pointer">
                                <i class="fa-solid fa-magnifying-glass text-[11px]"></i>
                                <span>Check Record</span>
                            </button>
                        </div>

                        <!-- Customer Record Display Container -->
                        <div id="customer_record_result" class="hidden mt-3 rounded-xl border border-gray-200 bg-gray-50/80 p-3 text-xs font-sans">
                            <!-- Populated via JS -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status & Notes Management Card -->
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-brand-navy/5">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h2 class="font-serif text-lg font-bold text-gray-900">Notes &amp; Status</h2>
                </div>

                <!-- Customer Note -->
                <div class="mt-4">
                    <p class="text-[11px] font-bold tracking-wider uppercase text-gray-400">Customer Note</p>
                    <div class="mt-1 rounded-lg bg-amber-50/60 border border-amber-200/80 p-3 text-xs text-amber-900 leading-relaxed">
                        {{ $order->notes ?: 'No customer note provided.' }}
                    </div>
                </div>

                <!-- Admin Note -->
                <div class="mt-4">
                    <p class="text-[11px] font-bold tracking-wider uppercase text-gray-400">Admin Note</p>
                    <div class="mt-1 text-xs text-gray-500 italic">
                        {{ $order->admin_note ?: 'No admin notes added.' }}
                    </div>
                </div>

                <!-- Quick Status Update -->
                <div class="mt-6 pt-4 border-t border-gray-100">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Order Status</h3>
                    <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="space-y-3">
                        @csrf
                        @method('PATCH')
                        <div class="flex gap-2">
                            <select name="status" class="w-full rounded-lg border border-brand-navy/15 px-3 py-2 text-xs focus:border-brand-orange focus:outline-none">
                                @foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $status)
                                    <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="shrink-0 rounded-lg bg-brand-navy px-4 py-2 text-xs font-semibold text-white hover:bg-brand-orange transition cursor-pointer">
                                Update
                            </button>
                        </div>
                    </form>

                    @if ($order->status !== 'delivered' && $order->status !== 'cancelled')
                        <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="mt-3">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $order->status === 'pending' ? 'processing' : ($order->status === 'processing' ? 'shipped' : 'delivered') }}">
                            <button type="submit" class="w-full flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white py-2.5 px-4 text-xs font-bold transition shadow-xs cursor-pointer">
                                <span>Proceed to {{ ucfirst($order->status === 'pending' ? 'processing' : ($order->status === 'processing' ? 'shipped' : 'delivered')) }}</span>
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Payment Status -->
                <div class="mt-5 pt-4 border-t border-gray-100">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Payment Status ({{ strtoupper($order->payment_method) }})</h3>
                    <form action="{{ route('admin.orders.payment', $order) }}" method="POST" class="flex gap-2">
                        @csrf
                        @method('PATCH')
                        <select name="payment_status" class="w-full rounded-lg border border-brand-navy/15 px-3 py-2 text-xs focus:border-brand-orange focus:outline-none">
                            @foreach (['unpaid', 'paid', 'refunded'] as $ps)
                                <option value="{{ $ps }}" @selected($order->payment_status === $ps)>{{ ucfirst($ps) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="shrink-0 rounded-lg border border-brand-orange px-3.5 py-2 text-xs font-semibold text-brand-orange transition hover:bg-brand-orange hover:text-white cursor-pointer">
                            Mark
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Steadfast integration and interactions -->
    <script>
        function copyText(text, btn) {
            navigator.clipboard.writeText(text).then(() => {
                const orig = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-check text-emerald-500 text-[11px]"></i>';
                setTimeout(() => {
                    btn.innerHTML = orig;
                }, 1800);
            });
        }

        function openEditConfirmModal() {
            document.getElementById('modal_confirm_edit').classList.remove('hidden');
        }
        function closeEditConfirmModal() {
            document.getElementById('modal_confirm_edit').classList.add('hidden');
        }

        function openDeleteConfirmModal() {
            document.getElementById('modal_confirm_delete').classList.remove('hidden');
        }
        function closeDeleteConfirmModal() {
            document.getElementById('modal_confirm_delete').classList.add('hidden');
        }

        let sfModalOnClose = null;

        function showSfModal({ type, title, subtitle, detailsHtml, buttonText, onClose }) {
            const modal = document.getElementById('sf_action_modal');
            const iconContainer = document.getElementById('sf_modal_icon');
            const titleEl = document.getElementById('sf_modal_title');
            const subEl = document.getElementById('sf_modal_subtitle');
            const detailsContainer = document.getElementById('sf_modal_details');
            const actionBtn = document.getElementById('sf_modal_action_btn');

            sfModalOnClose = onClose || null;

            if (type === 'success') {
                iconContainer.className = 'mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 mb-3.5';
                iconContainer.innerHTML = '<i class="fa-solid fa-circle-check text-2xl"></i>';
                actionBtn.className = 'w-full inline-flex items-center justify-center gap-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-5 py-2.5 text-xs font-bold text-white transition shadow-xs cursor-pointer';
            } else if (type === 'error') {
                iconContainer.className = 'mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-rose-100 text-rose-600 mb-3.5';
                iconContainer.innerHTML = '<i class="fa-solid fa-circle-xmark text-2xl"></i>';
                actionBtn.className = 'w-full inline-flex items-center justify-center gap-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 px-5 py-2.5 text-xs font-bold text-white transition shadow-xs cursor-pointer';
            } else {
                iconContainer.className = 'mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-blue-100 text-brand-navy mb-3.5';
                iconContainer.innerHTML = '<i class="fa-solid fa-truck-fast text-2xl"></i>';
                actionBtn.className = 'w-full inline-flex items-center justify-center gap-1.5 rounded-xl bg-brand-navy hover:bg-brand-orange px-5 py-2.5 text-xs font-bold text-white transition shadow-xs cursor-pointer';
            }

            titleEl.textContent = title;
            subEl.textContent = subtitle || '';

            if (detailsHtml) {
                detailsContainer.innerHTML = detailsHtml;
                detailsContainer.classList.remove('hidden');
            } else {
                detailsContainer.classList.add('hidden');
            }

            actionBtn.innerHTML = buttonText || 'OK';
            modal.classList.remove('hidden');
        }

        function closeSfModal() {
            document.getElementById('sf_action_modal').classList.add('hidden');
            if (typeof sfModalOnClose === 'function') {
                sfModalOnClose();
            }
        }

        function sendToSteadfast() {
            const btn = document.getElementById('sf_send_btn');
            const codInput = document.getElementById('sf_cod_input');
            const noteInput = document.getElementById('sf_note_input');

            const origHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs mr-1.5"></i> Sending to Steadfast...';
            btn.disabled = true;

            fetch("{{ route('admin.orders.steadfast.book', $order) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    cod_amount: codInput ? codInput.value : null,
                    note: noteInput ? noteInput.value : ''
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = origHtml;

                if (data.success) {
                    const detailsHtml = `
                        <div class="flex items-center justify-between pb-2 border-b border-gray-200">
                            <span class="text-gray-500 font-medium">Consignment ID:</span>
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-gray-900">#${data.consignment_id}</span>
                                <button type="button" onclick="copyText('${data.consignment_id}', this)" class="text-gray-400 hover:text-brand-orange p-0.5 cursor-pointer" title="Copy">
                                    <i class="fa-regular fa-copy text-xs"></i>
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pb-2 border-b border-gray-200">
                            <span class="text-gray-500 font-medium">Tracking Code:</span>
                            <div class="flex items-center gap-1.5">
                                <span class="font-mono font-bold text-gray-900 text-[11px]">${data.tracking_code}</span>
                                <button type="button" onclick="copyText('${data.tracking_code}', this)" class="text-gray-400 hover:text-brand-orange p-0.5 cursor-pointer" title="Copy">
                                    <i class="fa-regular fa-copy text-xs"></i>
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-0.5">
                            <span class="text-gray-500 font-medium">Current Status:</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 uppercase">In Review</span>
                        </div>
                    `;

                    showSfModal({
                        type: 'success',
                        title: 'Sent to Steadfast Courier!',
                        subtitle: 'Order successfully booked with courier partner.',
                        detailsHtml: detailsHtml,
                        buttonText: '<i class="fa-solid fa-rotate-right mr-1.5"></i> Reload & View Live Tracking',
                        onClose: () => {
                            window.location.reload();
                        }
                    });
                } else {
                    showSfModal({
                        type: 'error',
                        title: 'Steadfast Booking Failed',
                        subtitle: data.message || 'Could not book consignment with Steadfast.',
                        buttonText: 'Close'
                    });
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = origHtml;
                showSfModal({
                    type: 'error',
                    title: 'Connection Error',
                    subtitle: err.message,
                    buttonText: 'Close'
                });
            });
        }

        function checkSteadfastStatus() {
            const btn = document.getElementById('sf_status_btn');
            const origHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs mr-1"></i> Checking...';
            btn.disabled = true;

            fetch("{{ route('admin.orders.steadfast.status', $order) }}")
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = origHtml;

                if (data.success) {
                    const statusText = data.status_label || data.status;
                    const liveStatus = document.getElementById('sf_live_status');
                    if (liveStatus) {
                        liveStatus.textContent = statusText;
                    }
                    showSfModal({
                        type: 'info',
                        title: 'Latest Steadfast Status',
                        subtitle: 'Live courier delivery status updated.',
                        detailsHtml: `
                            <div class="text-center py-2">
                                <span class="text-gray-400 block text-[10px] font-bold uppercase tracking-wider mb-1">Status from Steadfast</span>
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-brand-navy text-white tracking-wide">${statusText}</span>
                            </div>
                        `,
                        buttonText: 'Got It'
                    });
                } else {
                    showSfModal({
                        type: 'error',
                        title: 'Status Check Failed',
                        subtitle: data.message || 'Could not fetch status from Steadfast.',
                        buttonText: 'Close'
                    });
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = origHtml;
                showSfModal({
                    type: 'error',
                    title: 'Connection Error',
                    subtitle: err.message,
                    buttonText: 'Close'
                });
            });
        }

        function checkCustomerRecord() {
            const btn = document.getElementById('btn_customer_record');
            const resultBox = document.getElementById('customer_record_result');

            const origHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs mr-1"></i> Checking...';
            btn.disabled = true;
            resultBox.classList.remove('hidden');
            resultBox.innerHTML = '<div class="text-center py-2 text-gray-500"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Scanning Steadfast database for customer delivery history...</div>';

            fetch("{{ route('admin.orders.steadfast.customerRecord', $order) }}")
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = origHtml;

                if (data.success) {
                    let badgeColor = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                    let badgeText = 'Low Risk (Safe)';

                    if (data.risk_level === 'high') {
                        badgeColor = 'bg-red-50 text-red-700 border-red-200';
                        badgeText = 'High Return Risk';
                    } else if (data.risk_level === 'medium') {
                        badgeColor = 'bg-amber-50 text-amber-700 border-amber-200';
                        badgeText = 'Moderate Risk';
                    } else if (data.risk_level === 'neutral') {
                        badgeColor = 'bg-blue-50 text-blue-700 border-blue-200';
                        badgeText = 'New Buyer';
                    }

                    let statsGrid = '';
                    if (data.has_exact_counts) {
                        statsGrid = `
                            <div class="grid grid-cols-3 gap-2 text-center pt-1">
                                <div class="bg-white rounded-lg p-1.5 border border-gray-100">
                                    <span class="text-[10px] text-gray-400 block uppercase">Total</span>
                                    <span class="font-bold text-gray-800 text-xs">${data.total_parcels}</span>
                                </div>
                                <div class="bg-white rounded-lg p-1.5 border border-emerald-100 text-emerald-700">
                                    <span class="text-[10px] text-emerald-500 block uppercase">Delivered</span>
                                    <span class="font-bold text-xs">${data.delivered_parcels}</span>
                                </div>
                                <div class="bg-white rounded-lg p-1.5 border border-red-100 text-red-600">
                                    <span class="text-[10px] text-red-400 block uppercase">Cancelled</span>
                                    <span class="font-bold text-xs">${data.cancelled_parcels}</span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center text-[11px] pt-1">
                                <span class="text-gray-500 font-medium">Delivery Success Ratio:</span>
                                <span class="font-bold text-gray-900">${data.delivery_ratio}</span>
                            </div>
                        `;
                    } else {
                        statsGrid = `
                            <div class="grid grid-cols-3 gap-2 text-center pt-1">
                                <div class="bg-white rounded-lg p-1.5 border border-emerald-100 text-emerald-700">
                                    <span class="text-[10px] text-emerald-600 block uppercase font-medium">Success Rate</span>
                                    <span class="font-bold text-xs">${data.delivery_ratio}</span>
                                </div>
                                <div class="bg-white rounded-lg p-1.5 border border-red-100 text-red-600">
                                    <span class="text-[10px] text-red-500 block uppercase font-medium">Cancel Rate</span>
                                    <span class="font-bold text-xs">${data.cancellation_ratio}</span>
                                </div>
                                <div class="bg-white rounded-lg p-1.5 border border-gray-100 text-gray-700">
                                    <span class="text-[10px] text-gray-400 block uppercase font-medium">Volume</span>
                                    <span class="font-bold text-xs">${data.volume_display}</span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center text-[11px] pt-1">
                                <span class="text-gray-500">Fraud / Dispute Reports:</span>
                                <span class="font-bold ${data.reports_count > 0 ? 'text-red-600' : 'text-emerald-700'}">${data.reports_count} ${data.reports_count === 1 ? 'report' : 'reports'}</span>
                            </div>
                        `;
                    }

                    resultBox.innerHTML = `
                        <div class="space-y-2">
                            <div class="flex items-center justify-between pb-1.5 border-b border-gray-200">
                                <span class="font-bold text-gray-800">Phone: ${data.phone}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border ${badgeColor}">${badgeText}</span>
                            </div>
                            ${statsGrid}
                            <div class="text-[10px] text-gray-400 pt-0.5 flex items-center justify-between border-t border-gray-100 mt-1">
                                <span>Steadfast Network Verification</span>
                                <span class="text-emerald-600 font-medium inline-flex items-center gap-1"><i class="fa-solid fa-circle-check text-[9px]"></i> Live Sync</span>
                            </div>
                        </div>
                    `;
                } else {
                    resultBox.innerHTML = `<p class="text-red-600"><i class="fa-solid fa-circle-exclamation mr-1"></i> ${data.message || 'Could not verify delivery record'}</p>`;
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = origHtml;
                resultBox.innerHTML = `<p class="text-red-600"><i class="fa-solid fa-circle-exclamation mr-1"></i> Error: ${err.message}</p>`;
            });
        }
    </script>

    {{-- Edit Order Confirmation Modal --}}
    <div id="modal_confirm_edit" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl ring-1 ring-black/5 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center gap-3.5 pb-3 border-b border-gray-100">
                <div class="h-11 w-11 rounded-full bg-amber-50 text-brand-orange flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-pen-to-square text-lg"></i>
                </div>
                <div>
                    <h3 class="font-serif text-base font-bold text-gray-900">Edit Order Confirmation</h3>
                    <p class="text-xs text-gray-500">Order #{{ $order->order_number }}</p>
                </div>
            </div>
            <div class="mt-3.5 text-xs text-gray-600 leading-relaxed">
                Do you want to edit Order <span class="font-bold text-gray-800">#{{ $order->order_number }}</span>? You will be taken to the edit screen where you can add/remove products, adjust quantities, change customer address, or update pricing and status.
            </div>
            <div class="mt-5 flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeEditConfirmModal()"
                    class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition cursor-pointer">
                    Cancel
                </button>
                <a href="{{ route('admin.orders.edit', $order) }}"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-brand-orange hover:bg-brand-navy px-4 py-2 text-xs font-bold text-white transition shadow-xs cursor-pointer">
                    <i class="fa-solid fa-arrow-right text-[11px]"></i>
                    <span>Proceed to Edit</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Delete Order Confirmation Modal --}}
    <div id="modal_confirm_delete" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl ring-1 ring-black/5 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center gap-3.5 pb-3 border-b border-gray-100">
                <div class="h-11 w-11 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                </div>
                <div>
                    <h3 class="font-serif text-base font-bold text-gray-900">Delete Order Permanently</h3>
                    <p class="text-xs text-rose-500 font-semibold">Warning: This action cannot be reversed</p>
                </div>
            </div>
            <div class="mt-3.5 text-xs text-gray-600 leading-relaxed">
                Are you sure you want to permanently delete Order <span class="font-bold text-gray-900">#{{ $order->order_number }}</span>?
                <ul class="mt-2 space-y-1 list-disc list-inside text-gray-500">
                    <li>Reserved product quantities will be returned to inventory.</li>
                    <li>All item records and payment logs will be removed.</li>
                </ul>
            </div>
            <form id="delete_order_form" action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="mt-5 flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeDeleteConfirmModal()"
                    class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition cursor-pointer">
                    Cancel
                </button>
                <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 px-4 py-2 text-xs font-bold text-white transition shadow-xs cursor-pointer">
                    <i class="fa-solid fa-trash-can text-[11px]"></i>
                    <span>Yes, Delete Order</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Steadfast Action Result Modal (Replacing browser alert) --}}
    <div id="sf_action_modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl ring-1 ring-black/5 animate-in fade-in zoom-in-95 duration-150">
            <div id="sf_modal_icon" class="mx-auto flex h-14 w-14 items-center justify-center rounded-full mb-3.5">
                <!-- Set via JS -->
            </div>
            <div class="text-center">
                <h3 id="sf_modal_title" class="font-serif text-lg font-bold text-gray-900"></h3>
                <p id="sf_modal_subtitle" class="mt-1 text-xs text-gray-500"></p>
            </div>
            
            <div id="sf_modal_details" class="mt-4 hidden space-y-2 rounded-xl bg-gray-50/90 border border-gray-200/80 p-3.5 text-xs">
                <!-- Set via JS -->
            </div>

            <div class="mt-5 flex items-center justify-center gap-3 pt-3 border-t border-gray-100">
                <button type="button" id="sf_modal_action_btn" onclick="closeSfModal()"
                    class="w-full inline-flex items-center justify-center gap-1.5 rounded-xl bg-brand-navy hover:bg-brand-orange px-5 py-2.5 text-xs font-bold text-white transition shadow-xs cursor-pointer">
                    <span>Close</span>
                </button>
            </div>
        </div>
    </div>
</x-admin-layout>
