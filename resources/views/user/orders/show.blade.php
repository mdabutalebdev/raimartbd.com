<x-layout title="Order #{{ $order->order_number }} - Raimart">
    <div class="bg-[#F8F9FA] min-h-screen py-8">
        <div class="container mx-auto gb-container max-w-6xl px-4">
            
            {{-- Breadcrumb & Back --}}
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <nav class="flex items-center gap-2 text-sm text-gray-500">
                    <a href="{{ route('home') }}" class="hover:text-brand-orange transition">Home</a>
                    <i class="fa-solid fa-angle-right text-xs text-gray-400"></i>
                    <a href="{{ route('user.dashboard') }}" class="hover:text-brand-orange transition">My Account</a>
                    <i class="fa-solid fa-angle-right text-xs text-gray-400"></i>
                    <a href="{{ route('user.dashboard') }}#orders" class="hover:text-brand-orange transition">Orders</a>
                    <i class="fa-solid fa-angle-right text-xs text-gray-400"></i>
                    <span class="text-brand-navy font-semibold">#{{ $order->order_number }}</span>
                </nav>

                <a href="{{ route('user.dashboard') }}#orders" class="inline-flex items-center gap-2 text-sm font-medium text-brand-navy/70 hover:text-brand-orange transition bg-white px-3.5 py-1.5 rounded-lg border border-gray-200 shadow-sm">
                    <i class="fa-solid fa-arrow-left text-xs"></i> Back to Orders
                </a>
            </div>

            {{-- Order Header Card --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-gray-100">
                    <div>
                        <div class="flex items-center gap-3 flex-wrap">
                            <div class="flex items-center gap-2">
                                <h1 class="text-2xl font-bold text-brand-navy">Order #{{ $order->order_number }}</h1>
                                <button type="button" 
                                    onclick="copyOrderId('{{ $order->order_number }}', this)" 
                                    class="inline-flex items-center gap-1 rounded-lg bg-gray-100 hover:bg-brand-orange/10 text-gray-500 hover:text-brand-orange px-2.5 py-1 text-xs font-medium transition cursor-pointer"
                                    title="Copy Order ID">
                                    <i class="fa-regular fa-copy text-xs"></i>
                                    <span>Copy</span>
                                </button>
                            </div>
                            @php
                                $statusStyles = [
                                    'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'processing' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'shipped' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    'delivered' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                                ];
                                $orderBadge = $statusStyles[$order->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border {{ $orderBadge }}">
                                {{ $order->status }}
                            </span>

                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $order->payment_status === 'paid' ? 'bg-emerald-500' : 'bg-rose-500' }} mr-1.5"></span>
                                {{ $order->payment_status }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1.5">
                            Placed on <span class="font-medium text-brand-navy">{{ $order->created_at->format('d M, Y \a\t h:i A') }}</span>
                        </p>
                    </div>

                    <div class="flex items-center gap-3 flex-wrap">
                        <a href="{{ route('user.orders.invoice', $order) }}" target="_blank"
                            class="inline-flex items-center gap-2 bg-white border border-gray-200 text-brand-navy hover:text-brand-orange hover:border-brand-orange px-4 py-2 rounded-xl text-sm font-semibold transition shadow-sm">
                            <i class="fa-solid fa-file-invoice text-brand-orange"></i> View Invoice
                        </a>
                        <a href="{{ route('track') }}?order_number={{ $order->order_number }}"
                            class="inline-flex items-center gap-2 bg-brand-orange text-white hover:bg-brand-navy px-4 py-2 rounded-xl text-sm font-semibold transition shadow-sm shadow-brand-orange/20">
                            <i class="fa-solid fa-truck-fast"></i> Track Order
                        </a>
                    </div>
                </div>

                {{-- Order Progress Timeline (if not cancelled) --}}
                @if ($order->status === 'cancelled')
                    <div class="mt-6 p-4 rounded-xl bg-red-50 border border-red-200 flex items-center gap-3 text-red-800 text-sm">
                        <i class="fa-solid fa-circle-xmark text-lg text-red-500"></i>
                        <div>
                            <span class="font-semibold">This order has been cancelled.</span>
                            <p class="text-xs text-red-600 mt-0.5">If you have any questions or paid online, please contact our customer support.</p>
                        </div>
                    </div>
                @else
                    @php
                        $steps = ['pending', 'processing', 'shipped', 'delivered'];
                        $currentIndex = array_search($order->status, $steps);
                        if ($currentIndex === false) $currentIndex = 0;
                    @endphp
                    <div class="mt-6 pt-2">
                        <div class="grid grid-cols-4 gap-2 relative">
                            @php
                                $stepLabels = [
                                    0 => ['title' => 'Order Placed', 'icon' => 'fa-clipboard-check'],
                                    1 => ['title' => 'Processing', 'icon' => 'fa-gear'],
                                    2 => ['title' => 'Shipped', 'icon' => 'fa-truck'],
                                    3 => ['title' => 'Delivered', 'icon' => 'fa-house-circle-check'],
                                ];
                            @endphp

                            @foreach ($stepLabels as $idx => $step)
                                @php
                                    $isPassed = $idx <= $currentIndex;
                                    $isCurrent = $idx === $currentIndex;
                                @endphp
                                <div class="flex flex-col items-center text-center">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold mb-2 transition-all duration-300 {{ $isPassed ? 'bg-brand-orange text-white shadow-md shadow-brand-orange/25' : 'bg-gray-100 text-gray-400' }} {{ $isCurrent ? 'ring-4 ring-brand-orange/20 scale-105' : '' }}">
                                        <i class="fa-solid {{ $step['icon'] }}"></i>
                                    </div>
                                    <span class="text-xs md:text-sm font-medium {{ $isPassed ? 'text-brand-navy' : 'text-gray-400' }}">{{ $step['title'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- 2-Column Content --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Left: Ordered Items & Financial Summary --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                            <h2 class="text-lg font-bold text-brand-navy">Ordered Items ({{ $order->items->sum('quantity') }})</h2>
                            <span class="text-xs text-gray-500 font-medium">Items list</span>
                        </div>

                        <div class="divide-y divide-gray-100">
                            @foreach ($order->items as $item)
                                <div class="p-5 flex items-center gap-4 hover:bg-gray-50/50 transition">
                                    <div class="w-16 h-16 rounded-xl bg-gray-100 border border-gray-100 overflow-hidden shrink-0 flex items-center justify-center">
                                        <img src="{{ image_url($item->product?->main_image, urlencode($item->product_name)) }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        @if ($item->product)
                                            <a href="{{ route('products.show', $item->product->slug) }}" class="font-semibold text-brand-navy hover:text-brand-orange transition line-clamp-1 text-sm md:text-base">
                                                {{ $item->product_name }}
                                            </a>
                                        @else
                                            <p class="font-semibold text-brand-navy line-clamp-1 text-sm md:text-base">{{ $item->product_name }}</p>
                                        @endif

                                        @if (! empty($item->options))
                                            <div class="flex flex-wrap gap-1.5 mt-1">
                                                @foreach ($item->options as $optKey => $optVal)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-600">
                                                        {{ ucfirst($optKey) }}: {{ $optVal }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif

                                        <p class="text-xs text-gray-500 mt-1">
                                            ৳{{ number_format($item->price, 2) }} × {{ $item->quantity }}
                                        </p>
                                    </div>

                                    <div class="text-right shrink-0">
                                        <p class="font-bold text-brand-navy text-sm md:text-base">৳{{ number_format($item->subtotal, 2) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Total Calculation --}}
                        <div class="p-5 bg-gray-50/50 border-t border-gray-100 space-y-2.5 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span class="font-medium text-brand-navy">৳{{ number_format($order->subtotal, 2) }}</span>
                            </div>

                            @if ($order->discount > 0)
                                <div class="flex justify-between text-emerald-600">
                                    <span class="flex items-center gap-1.5">
                                        Discount
                                        @if ($order->coupon_code)
                                            <span class="px-2 py-0.5 text-[11px] font-mono font-bold bg-emerald-100 text-emerald-800 rounded">
                                                {{ $order->coupon_code }}
                                            </span>
                                        @endif
                                    </span>
                                    <span class="font-medium">− ৳{{ number_format($order->discount, 2) }}</span>
                                </div>
                            @endif

                            <div class="flex justify-between text-gray-600">
                                <span>Delivery Fee ({{ $order->delivery_area === 'inside' ? 'Inside Dhaka' : 'Outside Dhaka' }})</span>
                                <span class="font-medium text-brand-navy">{{ $order->shipping_fee > 0 ? '৳' . number_format($order->shipping_fee, 2) : 'Free' }}</span>
                            </div>

                            <div class="border-t border-gray-200 pt-3 flex justify-between items-center text-base">
                                <span class="font-bold text-brand-navy">Total Amount</span>
                                <span class="text-xl font-extrabold text-brand-orange">৳{{ number_format($order->total, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Transactions (if any) --}}
                    @if ($order->payments->isNotEmpty())
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                            <h3 class="font-bold text-brand-navy mb-3 text-base flex items-center gap-2">
                                <i class="fa-solid fa-credit-card text-brand-orange"></i> Payment Details
                            </h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs md:text-sm">
                                    <thead>
                                        <tr class="border-b border-gray-100 text-gray-400">
                                            <th class="pb-2 font-medium">Gateway</th>
                                            <th class="pb-2 font-medium">Transaction ID</th>
                                            <th class="pb-2 font-medium">Amount</th>
                                            <th class="pb-2 font-medium text-right">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @foreach ($order->payments as $payment)
                                            <tr>
                                                <td class="py-2.5 font-medium uppercase text-brand-navy">{{ $payment->gateway }}</td>
                                                <td class="py-2.5 font-mono text-gray-600">{{ $payment->transaction_id ?: '—' }}</td>
                                                <td class="py-2.5 font-semibold text-brand-navy">৳{{ number_format($payment->amount, 2) }}</td>
                                                <td class="py-2.5 text-right">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold uppercase {{ $payment->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
                                                        {{ $payment->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Right: Delivery & Payment Details --}}
                <div class="space-y-6">
                    
                    {{-- Delivery Information --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                        <div class="flex items-center gap-2 pb-3 mb-4 border-b border-gray-100">
                            <div class="w-8 h-8 rounded-lg bg-brand-orange/10 flex items-center justify-center text-brand-orange">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <h3 class="font-bold text-brand-navy text-base">Delivery Address</h3>
                        </div>

                        <dl class="space-y-3 text-sm">
                            <div>
                                <dt class="text-xs text-gray-400 font-medium">Recipient Name</dt>
                                <dd class="font-semibold text-brand-navy mt-0.5">{{ $order->name }}</dd>
                            </div>

                            <div>
                                <dt class="text-xs text-gray-400 font-medium">Phone Number</dt>
                                <dd class="font-medium text-brand-navy mt-0.5">{{ $order->phone }}</dd>
                            </div>

                            @if ($order->email)
                                <div>
                                    <dt class="text-xs text-gray-400 font-medium">Email</dt>
                                    <dd class="text-gray-700 mt-0.5">{{ $order->email }}</dd>
                                </div>
                            @endif

                            <div>
                                <dt class="text-xs text-gray-400 font-medium">Full Address</dt>
                                <dd class="text-gray-700 mt-0.5 leading-relaxed">
                                    {{ $order->address }}
                                </dd>
                            </div>

                            @if ($order->thana)
                                <div>
                                    <dt class="text-xs text-gray-400 font-medium">Thana / Upazila</dt>
                                    <dd class="font-medium text-brand-navy mt-0.5">{{ $order->thana }}</dd>
                                </div>
                            @endif

                            @if ($order->city)
                                <div>
                                    <dt class="text-xs text-gray-400 font-medium">District</dt>
                                    <dd class="font-medium text-brand-navy mt-0.5">{{ $order->city }}</dd>
                                </div>
                            @endif

                            @if ($order->notes)
                                <div class="p-3 bg-amber-50/50 rounded-xl border border-amber-100 text-xs text-amber-900 mt-2">
                                    <span class="font-bold block mb-0.5">Order Note:</span>
                                    {{ $order->notes }}
                                </div>
                            @endif
                        </dl>
                    </div>

                    {{-- Payment Summary --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                        <div class="flex items-center gap-2 pb-3 mb-4 border-b border-gray-100">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                                <i class="fa-solid fa-wallet"></i>
                            </div>
                            <h3 class="font-bold text-brand-navy text-base">Payment Method</h3>
                        </div>

                        <div class="space-y-3 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Method</span>
                                <span class="font-semibold text-brand-navy uppercase">{{ $order->payment_method_name }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Status</span>
                                <span class="font-semibold capitalize {{ $order->payment_status === 'paid' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $order->payment_status }}
                                </span>
                            </div>

                            @if ($order->payment_method === 'piprapay' && $order->payment_status !== 'paid' && $order->status !== 'cancelled')
                                <div class="pt-3 border-t border-gray-100">
                                    <a href="{{ route('checkout.pay', $order) }}"
                                        class="w-full flex items-center justify-center gap-2 rounded-xl bg-brand-orange py-2.5 text-sm font-bold text-white transition hover:bg-brand-navy shadow-md shadow-brand-orange/20">
                                        <i class="fa-solid fa-lock"></i> Pay Now (৳{{ number_format($order->total, 2) }})
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Courier Tracking Details (if available) --}}
                    @if ($order->courier_name || $order->tracking_number)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                            <div class="flex items-center gap-2 pb-3 mb-4 border-b border-gray-100">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                                    <i class="fa-solid fa-truck-ramp-box"></i>
                                </div>
                                <h3 class="font-bold text-brand-navy text-base">Courier Details</h3>
                            </div>

                            <dl class="space-y-2 text-sm">
                                @if ($order->courier_name)
                                    <div>
                                        <dt class="text-xs text-gray-400 font-medium">Courier</dt>
                                        <dd class="font-semibold text-brand-navy">{{ $order->courier_name }}</dd>
                                    </div>
                                @endif

                                @if ($order->tracking_number)
                                    <div>
                                        <dt class="text-xs text-gray-400 font-medium">Tracking Number</dt>
                                        <dd class="font-mono font-bold text-brand-navy">{{ $order->tracking_number }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    @endif

                    {{-- Customer Support Card --}}
                    <div class="bg-gradient-to-br from-brand-navy to-brand-navy/90 text-white rounded-2xl p-5 shadow-sm">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center text-brand-orange">
                                <i class="fa-solid fa-headset"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm">Need help with this order?</h4>
                                <p class="text-xs text-gray-300">We're here to assist you anytime.</p>
                            </div>
                        </div>
                        <a href="{{ route('contact') }}" class="mt-3 inline-flex items-center justify-center w-full py-2 px-3 text-xs font-semibold rounded-lg bg-white/10 hover:bg-white hover:text-brand-navy transition text-center">
                            Contact Customer Care
                        </a>
                    </div>

                </div>

            </div>

    </div>

    <script>
        function copyOrderId(id, btn) {
            function showCopied() {
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-check text-xs text-emerald-600"></i> <span class="text-emerald-600 font-bold">Copied!</span>';
                btn.classList.add('bg-emerald-50');
                if (window.showToast) {
                    window.showToast('Order ID copied to clipboard!', 'success');
                }
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.classList.remove('bg-emerald-50');
                }, 2000);
            }

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(id).then(showCopied).catch(() => fallback(id));
            } else {
                fallback(id);
            }

            function fallback(val) {
                const temp = document.createElement('textarea');
                temp.value = val;
                temp.style.position = 'fixed';
                temp.style.opacity = '0';
                document.body.appendChild(temp);
                temp.focus();
                temp.select();
                try {
                    document.execCommand('copy');
                    showCopied();
                } catch (e) {
                    console.error('Copy failed', e);
                }
                document.body.removeChild(temp);
            }
        }
    </script>
</x-layout>
