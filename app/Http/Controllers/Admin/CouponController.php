<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $coupons = Coupon::when($request->search, fn ($q, $s) => $q->where('code', 'like', "%{$s}%"))
            ->when($request->status === 'active', fn ($q) => $q->where('is_active', true))
            ->when($request->status === 'inactive', fn ($q) => $q->where('is_active', false))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        $coupon = new Coupon([
            'type' => 'percent',
            'applies_to' => 'full_store',
            'is_active' => true,
        ]);

        [$products, $categories] = $this->formOptions();

        return view('admin.coupons.create', compact('coupon', 'products', 'categories'));
    }

    public function store(Request $request)
    {
        Coupon::create($this->validated($request));

        return redirect()->route('admin.coupons.index')->with('status', 'Coupon created successfully.');
    }

    public function edit(Coupon $coupon)
    {
        [$products, $categories] = $this->formOptions();

        return view('admin.coupons.edit', compact('coupon', 'products', 'categories'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $coupon->update($this->validated($request, $coupon));

        return redirect()->route('admin.coupons.index')->with('status', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('status', 'Coupon deleted.');
    }

    private function formOptions(): array
    {
        $products = Product::active()
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'main_image'])
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'price' => (float) $p->price,
                'image' => image_url($p->main_image, urlencode($p->name)),
            ])
            ->values();

        $categories = Category::active()
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id'])
            ->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
            ])
            ->values();

        return [$products, $categories];
    }

    private function validated(Request $request, ?Coupon $coupon = null): array
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('coupons', 'code')->ignore($coupon?->id)],
            'type' => ['required', 'in:percent,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'applies_to' => ['required', 'in:full_store,specific_products,specific_categories'],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['integer', 'exists:products,id'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'free_shipping' => ['nullable', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['code'] = strtoupper(trim($data['code']));
        $data['free_shipping'] = (bool) ($data['free_shipping'] ?? false);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        if ($data['applies_to'] === 'full_store') {
            $data['product_ids'] = null;
            $data['category_ids'] = null;
        } elseif ($data['applies_to'] === 'specific_products') {
            $data['product_ids'] = array_values(array_unique(array_map('intval', $data['product_ids'] ?? [])));
            $data['category_ids'] = null;
        } elseif ($data['applies_to'] === 'specific_categories') {
            $data['category_ids'] = array_values(array_unique(array_map('intval', $data['category_ids'] ?? [])));
            $data['product_ids'] = null;
        }

        // A percentage cap only makes sense on percentage coupons.
        if ($data['type'] === 'fixed') {
            $data['max_discount'] = null;
        }

        return $data;
    }
}
