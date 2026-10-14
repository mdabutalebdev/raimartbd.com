<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Cart;
use App\Support\Ga4;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Cart $cart)
    {
        $items = $cart->items();
        $subtotal = $cart->subtotal();

        return view('cart', compact('items', 'subtotal'));
    }

    /** Rendered cart-items fragment for the slide-out drawer (AJAX). */
    public function partial(Cart $cart)
    {
        return response()->json($this->cartPayload($cart));
    }

    private function cartPayload(Cart $cart): array
    {
        return [
            'count' => $cart->count(),
            'subtotal' => $cart->subtotal(),
            'html' => view('partials.cart-items', [
                'items' => $cart->items(),
                'subtotal' => $cart->subtotal(),
            ])->render(),
        ];
    }

    public function add(Request $request, Cart $cart, Product $product)
    {
        $quantity = max(1, (int) $request->input('quantity', 1));
        
        $options = $request->input('options', []);
        
        // Validate options if product has attributes
        if ($product->attributes) {
            foreach ($product->attributes as $attribute) {
                $values = array_filter(array_map('trim', explode(',', $attribute['values'] ?? '')));
                if (count($values) > 0) {
                    if (empty($options[$attribute['name']])) {
                        return back()->withErrors(['options' => "Please select a {$attribute['name']}."]);
                    }
                }
            }
        }
        
        if (is_array($options)) {
            $options = array_filter($options);
        }

        $variantId = $request->filled('variant_id') ? (int) $request->input('variant_id') : null;
        $variant = null;
        if ($variantId) {
            $variant = $product->variants()->find($variantId);
        } elseif (!empty($options) && $product->variants()->exists()) {
            $variant = $product->variants->first(function ($v) use ($options) {
                if (!is_array($v->attributes)) return false;
                foreach ($options as $k => $val) {
                    if (($v->attributes[$k] ?? null) != $val) return false;
                }
                return true;
            });
            if ($variant) {
                $variantId = $variant->id;
            }
        }

        $availableStock = $variant ? (int) $variant->stock : (int) $product->stock;

        // Check stock availability with existing quantity in cart
        ksort($options);
        $cartItemId = $product->id . '_' . ($variantId ? 'v'.$variantId.'_' : '') . md5(json_encode($options));
        $rawItems = $cart->raw();
        // Also check if previously added under non-variant cartItemId
        $legacyItemId = $product->id . '_' . md5(json_encode($options));
        $existingQty = $rawItems[$cartItemId]['quantity'] ?? ($rawItems[$legacyItemId]['quantity'] ?? 0);
        $totalDesired = $existingQty + $quantity;

        if ($availableStock <= 0 || $totalDesired > $availableStock) {
            $errorMsg = 'Oops! You have already added all available items into your cart.';
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => $errorMsg,
                    'message' => $errorMsg,
                    'available_stock' => $availableStock,
                    'current_in_cart' => $existingQty,
                ], 422);
            }
            return back()->with('error', $errorMsg)->withErrors(['quantity' => $errorMsg]);
        }

        $cart->add($product, $quantity, $options, $variantId);

        if ($request->input('redirect') === 'checkout') {
            return redirect()->route('checkout.index');
        }

        if ($request->wantsJson()) {
            return response()->json($this->cartPayload($cart) + [
                'message' => "{$product->name} added to cart.",
                'ga' => [
                    'event' => 'add_to_cart',
                    'ecommerce' => [
                        'currency' => Ga4::currency(),
                        'value' => round((float) $product->price * $quantity, 2),
                        'items' => [Ga4::item($product, array_filter([
                            'quantity' => $quantity,
                            'item_variant' => Ga4::variant($options ?: []),
                        ], fn ($v) => $v !== null))],
                    ],
                ],
                'pixel' => [
                    'content_name' => $product->name,
                    'content_ids' => [(string) $product->id],
                    'content_type' => 'product',
                    'value' => round((float) $product->price * $quantity, 2),
                    'currency' => 'BDT',
                    'num_items' => (int) $quantity,
                ],
            ]);
        }

        return back()->with('status', "{$product->name} added to cart.");
    }

    public function update(Request $request, Cart $cart, string $id)
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:0']]);

        $item = $cart->items()->firstWhere('id', $id);
        if ($item && $data['quantity'] > 0) {
            $maxStock = (int) ($item->stock ?? $item->product->stock);
            if ($data['quantity'] > $maxStock) {
                $errorMsg = 'Oops! You have already added all available items into your cart.';
                if ($request->wantsJson()) {
                    return response()->json([
                        'error' => $errorMsg,
                        'message' => $errorMsg,
                        'available_stock' => $maxStock,
                    ] + $this->cartPayload($cart), 422);
                }
                return back()->with('error', $errorMsg);
            }
        }

        $cart->update($id, $data['quantity']);

        if ($request->wantsJson()) {
            return response()->json($this->cartPayload($cart));
        }

        return back()->with('status', 'Cart updated.');
    }

    public function remove(Request $request, Cart $cart, string $id)
    {
        // Grab the line before it's gone so we can report it to GA4.
        $removed = $cart->items()->firstWhere('id', $id);

        $cart->remove($id);

        if ($request->wantsJson()) {
            $payload = $this->cartPayload($cart);

            if ($removed) {
                $payload['ga'] = [
                    'event' => 'remove_from_cart',
                    'ecommerce' => [
                        'currency' => Ga4::currency(),
                        'value' => round((float) $removed->product->price * $removed->quantity, 2),
                        'items' => [Ga4::cartLine($removed)],
                    ],
                ];
            }

            return response()->json($payload);
        }

        return back()->with('status', 'Item removed from cart.');
    }
}
