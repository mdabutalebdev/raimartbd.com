<x-layout title="Order Confirmation - Raimart">
    <section class="mx-auto max-w-3xl px-4 py-14 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="mb-6 rounded-lg border border-brand-orange/30 bg-brand-bg px-4 py-3 text-sm text-brand-navy">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-xl bg-white p-8 text-center shadow-sm ring-1 ring-brand-navy/5">
            @if ($order->payment_status === 'paid')
                <i class="fa-solid fa-circle-check text-4xl text-green-500"></i>
                <h1 class="mt-4 font-serif text-2xl font-bold">Payment Successful!</h1>
            @else
                <i class="fa-solid fa-circle-check text-4xl text-brand-orange"></i>
                <h1 class="mt-4 font-serif text-2xl font-bold">Order Placed Successfully!</h1>
            @endif

            <p class="mt-2 text-brand-navy/60">Thank you, {{ $order->name }}.</p>

            <div class="mt-3 inline-flex flex-wrap items-center justify-center gap-2 rounded-xl bg-gray-50 border border-gray-200/80 px-4 py-2 shadow-xs transition hover:border-brand-orange/40">
                <span class="text-xs font-medium text-brand-navy/60">Order Number:</span>
                <span id="orderNumberValue" class="font-mono text-base font-bold text-brand-navy select-all tracking-wide">{{ $order->order_number }}</span>
                <button type="button" 
                    id="copyOrderBtn"
                    onclick="copyOrderNumber('{{ $order->order_number }}')" 
                    class="ml-1 inline-flex items-center gap-1.5 rounded-lg bg-white px-2.5 py-1 text-xs font-semibold text-brand-navy border border-gray-200 hover:border-brand-orange hover:text-brand-orange active:scale-95 transition-all shadow-xs cursor-pointer"
                    title="Copy Order ID">
                    <i id="copyIcon" class="fa-regular fa-copy text-xs text-brand-orange"></i>
                    <span id="copyBtnText">Copy</span>
                </button>
            </div>

            @if ($order->payment_method === 'piprapay' && $order->payment_status !== 'paid')
                <div class="mt-6">
                    <a href="{{ route('checkout.pay', $order) }}" class="inline-block rounded-md bg-brand-orange px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-navy">
                        Pay Now
                    </a>
                </div>
            @endif
        </div>

        <div class="mt-6 rounded-xl bg-white p-6 shadow-sm ring-1 ring-brand-navy/5">
            <h2 class="font-serif text-lg font-bold">Order Details</h2>

            <table class="mt-4 w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-brand-navy/10 text-brand-navy/50">
                        <th class="pb-2 font-medium">Product</th>
                        <th class="pb-2 font-medium">Qty</th>
                        <th class="pb-2 font-medium">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr class="border-b border-brand-navy/5">
                            <td class="py-2">{{ $item->product_name }}</td>
                            <td class="py-2">{{ $item->quantity }}</td>
                            <td class="py-2">৳{{ number_format($item->subtotal) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4 space-y-1 text-sm">
                <div class="flex justify-between"><span class="text-brand-navy/60">Subtotal</span><span>৳{{ number_format($order->subtotal, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-brand-navy/60">Shipping</span><span>৳{{ number_format($order->shipping_fee, 2) }}</span></div>
                <div class="flex justify-between font-semibold"><span>Total</span><span>৳{{ number_format($order->total, 2) }}</span></div>
            </div>

            <div class="mt-4 flex justify-between border-t border-brand-navy/10 pt-4 text-sm">
                <span class="text-brand-navy/60">Payment Method</span>
                <span class="capitalize">{{ $order->payment_method_name }} &middot; {{ $order->payment_status }}</span>
            </div>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('shop') }}" class="text-sm font-medium text-brand-orange hover:underline">Continue Shopping &rarr;</a>
        </div>
    </section>

    @php
        $trackPurchase = (\App\Models\SiteSetting::get('pixel_track_purchase', '1')) === '1';
    @endphp
    @if ($trackPurchase)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof window.trackPixelEvent === 'function') {
                    window.trackPixelEvent('Purchase', {
                        content_type: 'product',
                        content_ids: @json($order->items->pluck('product_id')->map(fn($id) => (string)$id)->values()),
                        value: {{ (float) $order->total }},
                        currency: 'BDT',
                        num_items: {{ (int) $order->items->sum('quantity') }},
                        order_id: @json($order->order_number),
                        event_id: @json($order->order_number)
                    });
                }
            });
        </script>
    @endif

    <script>
        function copyOrderNumber(orderNumber) {
            const btn = document.getElementById('copyOrderBtn');
            const icon = document.getElementById('copyIcon');
            const text = document.getElementById('copyBtnText');

            function showCopied() {
                if (icon) icon.className = 'fa-solid fa-check text-xs text-emerald-600';
                if (text) {
                    text.textContent = 'Copied!';
                    text.className = 'text-emerald-600 font-bold';
                }
                if (btn) btn.classList.add('bg-emerald-50', 'border-emerald-300');

                if (window.showToast) {
                    window.showToast('Order ID copied to clipboard!', 'success');
                }

                setTimeout(() => {
                    if (icon) icon.className = 'fa-regular fa-copy text-xs text-brand-orange';
                    if (text) {
                        text.textContent = 'Copy';
                        text.className = '';
                    }
                    if (btn) btn.classList.remove('bg-emerald-50', 'border-emerald-300');
                }, 2000);
            }

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(orderNumber)
                    .then(showCopied)
                    .catch(() => fallbackCopy(orderNumber));
            } else {
                fallbackCopy(orderNumber);
            }

            function fallbackCopy(str) {
                const temp = document.createElement('textarea');
                temp.value = str;
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
