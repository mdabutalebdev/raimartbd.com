<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Services\Cart;
use App\Services\CouponService;
use App\Services\FacebookCapiService;
use App\Services\Ga4MeasurementProtocol;
use App\Services\PipraPayService;
use App\Services\TelegramNotifier;
use App\Support\Shipping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(
        private Ga4MeasurementProtocol $ga4,
        private FacebookCapiService $capi,
        private TelegramNotifier $telegram,
        private CouponService $coupons,
    ) {
    }

    public function index(Cart $cart)
    {
        $items = $cart->items();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $subtotal = $cart->subtotal();
        // Mirror the district → area rule used on submit, so the summary opens with the right rate.
        $city = old('city', auth()->user()->city ?? '');
        $area = $city ? (strcasecmp(trim($city), 'Dhaka') === 0 ? 'inside' : 'outside') : Shipping::DEFAULT_AREA;
        $discount = $this->coupons->discount($subtotal);
        $shippingFee = Shipping::fee($area, $subtotal, $this->coupons->givesFreeShipping($subtotal));
        $total = max(0, $subtotal - $discount) + $shippingFee;

        return view('checkout', [
            'items' => $items,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'coupon' => $this->coupons->coupon(),
            'shippingFee' => $shippingFee,
            'total' => $total,
            'area' => $area,
            'areas' => Shipping::areas(),
            'freeThreshold' => Shipping::threshold(),
            'piprapayReady' => app(PipraPayService::class)->isReady(),
        ]);
    }

    /** Apply a coupon code from the checkout page. */
    public function applyCoupon(Request $request, Cart $cart)
    {
        $data = $request->validate(['code' => ['required', 'string', 'max:50']]);

        [$ok, $message] = $this->coupons->apply($data['code'], $cart->subtotal());

        if ($request->wantsJson()) {
            return response()->json($this->couponJsonPayload($ok, $message, $cart));
        }

        return back()->with($ok ? 'status' : 'error', $message);
    }

    public function removeCoupon(Request $request, Cart $cart)
    {
        $this->coupons->clear();

        if ($request->wantsJson()) {
            return response()->json($this->couponJsonPayload(true, 'Coupon removed.', $cart));
        }

        return back()->with('status', 'Coupon removed.');
    }

    /** Build a JSON payload with coupon state + recalculated totals for AJAX coupon actions. */
    private function couponJsonPayload(bool $ok, string $message, Cart $cart): array
    {
        $subtotal = $cart->subtotal();
        $coupon = $this->coupons->coupon();
        $discount = $this->coupons->discount($subtotal);
        $city = request('city', auth()->user()->city ?? '');
        $area = $city ? (strcasecmp(trim($city), 'Dhaka') === 0 ? 'inside' : 'outside') : Shipping::DEFAULT_AREA;
        $shippingFee = Shipping::fee($area, $subtotal, $this->coupons->givesFreeShipping($subtotal));
        $total = max(0, $subtotal - $discount) + $shippingFee;

        return [
            'ok'       => $ok,
            'message'  => $message,
            'coupon'   => $coupon ? [
                'code'  => $coupon->code,
                'label' => $coupon->label,
            ] : null,
            'subtotal'     => round($subtotal),
            'discount'     => round($discount),
            'shippingFee'  => round($shippingFee),
            'total'        => round($total),
            'freeShipping' => $shippingFee <= 0,
        ];
    }

    public function store(Request $request, Cart $cart)
    {
        $items = $cart->items();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^(?:\+?88)?01[3-9]\d{8}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'thana' => ['nullable', 'string', 'max:100'],
            'delivery_area' => ['nullable', 'in:inside,outside'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:cod,piprapay'],
            'ga_client_id' => ['nullable', 'string', 'max:64'],
            'ga_session_id' => ['nullable', 'string', 'max:64'],
        ], [
            'name.required' => 'Please provide your full name.',
            'phone.required' => 'Please provide your 11-digit mobile number.',
            'phone.regex' => 'Phone number must be a valid 11-digit Bangladeshi mobile number (e.g. 01700000000).',
            'address.required' => 'Please enter your delivery address.',
            'city.required' => 'Please select your delivery district.',
        ]);

        foreach ($items as $item) {
            if ($item->quantity > $item->product->stock) {
                return back()->withErrors(['quantity' => "Only {$item->product->stock} units of {$item->product->name} are available."])->withInput();
            }
        }

        // The district decides the rate — derived server-side so a disabled or
        // tampered-with hidden field can never change the delivery charge.
        $data['delivery_area'] = strcasecmp(trim($data['city']), 'Dhaka') === 0 ? 'inside' : 'outside';

        $subtotal = $cart->subtotal();
        $coupon = $this->coupons->coupon();
        $discount = $this->coupons->discount($subtotal);
        $shippingFee = Shipping::fee($data['delivery_area'], $subtotal, $this->coupons->givesFreeShipping($subtotal));
        $total = max(0, $subtotal - $discount) + $shippingFee;

        // If online payment is selected, check upfront whether the gateway is ready
        if ($data['payment_method'] === 'piprapay') {
            $pipraPay = app(PipraPayService::class);
            if (! $pipraPay->isReady()) {
                return back()->withInput()->withErrors([
                    'payment_method' => 'অনলাইন পেমেন্ট বর্তমানে সাময়িকভাবে বন্ধ আছে। অনুগ্রহ করে ক্যাশ অন ডেলিভারি (Cash on Delivery) সিলেক্ট করুন।',
                ]);
            }
        }

        DB::beginTransaction();
        try {
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => auth()->id(),
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'address' => $data['address'],
                'city' => $data['city'],
                'thana' => $data['thana'] ?? null,
                'delivery_area' => $data['delivery_area'],
                'notes' => $data['notes'] ?? null,
                'subtotal' => $subtotal,
                'coupon_code' => $coupon?->code,
                'discount' => $discount,
                'shipping_fee' => $shippingFee,
                'total' => $total,
                'payment_method' => $data['payment_method'],
                'payment_status' => 'unpaid',
                'status' => 'pending',
                'ga_client_id' => $data['ga_client_id'] ?? null,
                'ga_session_id' => $data['ga_session_id'] ?? null,
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product->id,
                    'product_name' => $item->product->name,
                    'options' => $item->options ?: null,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                ]);

                $item->product->update([
                    'stock' => max(0, (int) $item->product->stock - (int) $item->quantity),
                ]);
            }

            $coupon?->increment('used_count');

            if ($order->payment_method === 'piprapay') {
                $response = $pipraPay->createCharge([
                    'full_name' => $order->name,
                    'email_address' => $order->email ?: 'no-email@example.com',
                    'mobile_number' => $order->phone,
                    'amount' => $order->total,
                    'metadata' => ['order_number' => $order->order_number],
                    'return_url' => route('checkout.callback', $order),
                    'webhook_url' => route('checkout.webhook'),
                ]);

                if (empty($response['pp_url'])) {
                    // Gateway failed to generate payment link: Rollback so order is NOT saved as success/pending!
                    DB::rollBack();
                    $errorMsg = $response['message'] ?? 'Unable to connect to payment gateway.';
                    Log::warning('PipraPay checkout initiation failed - order rolled back', [
                        'order' => $order->order_number,
                        'response' => $response,
                    ]);

                    return back()->withInput()->withErrors([
                        'payment_method' => 'পেমেন্ট গেটওয়েতে সংযোগ করতে সমস্যা হচ্ছে: ' . $errorMsg . '। অনুগ্রহ করে ক্যাশ অন ডেলিভারি (Cash on Delivery) সিলেক্ট করুন অথবা পুনরায় চেষ্টা করুন।',
                    ]);
                }

                Payment::create([
                    'order_id' => $order->id,
                    'gateway' => 'piprapay',
                    'pp_id' => $response['pp_id'],
                    'amount' => $order->total,
                    'status' => 'pending',
                    'raw_response' => $response,
                ]);
            }

            DB::commit();

            $cart->clear();
            $this->coupons->clear();
            session()->forget('delivery_area');

            if ($order->payment_method === 'piprapay') {
                return redirect()->away($response['pp_url']);
            }

            // COD: the order is confirmed the moment it's placed → track purchase server-side.
            $this->telegram->orderPlaced($order);
            $this->ga4->purchase($order);
            $this->capi->sendPurchase($order);

            return redirect()->route('checkout.confirmation', $order);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Checkout store failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()->withErrors([
                'payment_method' => 'অর্ডার প্রসেস করতে ত্রুটি দেখা দিয়েছে: ' . $e->getMessage(),
            ]);
        }
    }

    public function pay(Order $order)
    {
        abort_if($order->payment_method !== 'piprapay' || $order->payment_status === 'paid', 404);

        return $this->initiatePipraPayPayment($order);
    }

    public function callback(Request $request, Order $order, PipraPayService $pipraPay)
    {
        $ppId = $request->query('pp_id');

        if ($ppId) {
            $this->verifyAndUpdate($order, $ppId, $pipraPay);
        }

        if ($order->fresh()->payment_status === 'paid') {
            return redirect()->route('checkout.confirmation', $order)->with('status', 'Payment completed successfully.');
        }

        return redirect()->route('checkout.confirmation', $order)->with('error', 'Payment was not completed or verification failed. You can retry payment below.');
    }

    public function cancel(Order $order)
    {
        return redirect()->route('checkout.confirmation', $order)->with('error', 'Payment was cancelled. You can retry payment below.');
    }

    public function webhook(Request $request, PipraPayService $pipraPay)
    {
        $ppId = $request->input('pp_id');

        if (! $ppId) {
            return response()->json(['status' => false], 400);
        }

        $payment = Payment::where('pp_id', $ppId)->first();

        if ($payment) {
            $this->verifyAndUpdate($payment->order, $ppId, $pipraPay);
        }

        return response()->json(['status' => true]);
    }

    public function confirmation(Order $order)
    {
        $order->load('items');

        return view('checkout-confirmation', compact('order'));
    }

    private function initiatePipraPayPayment(Order $order)
    {
        $pipraPay = app(PipraPayService::class);

        if (! $pipraPay->isReady()) {
            return redirect()->route('checkout.confirmation', $order)
                ->with('error', 'Online payment is not available right now. Please contact support or choose Cash on Delivery.');
        }

        $response = $pipraPay->createCharge([
            'full_name' => $order->name,
            'email_address' => $order->email ?: 'no-email@example.com',
            'mobile_number' => $order->phone,
            'amount' => $order->total,
            'metadata' => ['order_number' => $order->order_number],
            'return_url' => route('checkout.callback', $order),
            'webhook_url' => route('checkout.webhook'),
        ]);

        if (empty($response['pp_url'])) {
            Log::warning('PipraPay createCharge failed on retry', ['order' => $order->order_number, 'response' => $response]);

            return redirect()->route('checkout.confirmation', $order)
                ->with('error', 'Could not start online payment: '.($response['message'] ?? 'unknown error').'. Please retry or contact support.');
        }

        Payment::create([
            'order_id' => $order->id,
            'gateway' => 'piprapay',
            'pp_id' => $response['pp_id'],
            'amount' => $order->total,
            'status' => 'pending',
            'raw_response' => $response,
        ]);

        return redirect()->away($response['pp_url']);
    }

    private function verifyAndUpdate(Order $order, string $ppId, PipraPayService $pipraPay): void
    {
        $result = $pipraPay->verifyPayment($ppId);
        $isCompleted = ($result['status'] ?? null) === 'completed';

        $payment = Payment::where('order_id', $order->id)->where('pp_id', $ppId)->first();

        if ($payment) {
            $payment->update([
                'transaction_id' => $result['transaction_id'] ?? null,
                'status' => $isCompleted ? 'completed' : 'failed',
                'raw_response' => $result,
            ]);
        }

        if ($isCompleted) {
            $wasAlreadyPaid = $order->payment_status === 'paid';
            $order->update(['payment_status' => 'paid', 'status' => 'processing']);

            if (! $wasAlreadyPaid) {
                // Online payment confirmed → notify telegram & track purchase
                $this->telegram->orderPlaced($order->fresh());
                $this->ga4->purchase($order->fresh());
                $this->capi->sendPurchase($order->fresh());
            }
        }
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'RM'.now()->format('ymd').strtoupper(Str::random(5));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
