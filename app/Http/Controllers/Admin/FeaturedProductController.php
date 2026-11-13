<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FeaturedProductController extends Controller
{
    public function edit()
    {
        $products = Product::where('is_active', true)
            ->with('category')
            ->orderByDesc('is_featured')
            ->orderBy('featured_order')
            ->orderByDesc('id')
            ->get()
            ->map(fn ($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category->name ?? '—',
                'price' => (float) $product->price,
                'image' => image_url($product->main_image, urlencode($product->name)),
                'selected' => (bool) $product->is_featured,
                'order' => (int) $product->featured_order,
            ])
            ->values();

        return view('admin.featured-products.edit', compact('products'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'products' => ['nullable', 'array'],
            'products.*' => ['integer', 'exists:products,id'],
            'order' => ['nullable', 'array'],
        ]);

        $ids = collect($data['products'] ?? [])->map(fn ($id) => (int) $id)->unique()->values();

        // Reset unselected products
        Product::where('is_featured', true)->whereNotIn('id', $ids)->update([
            'is_featured' => false,
            'featured_order' => 0,
        ]);

        $order = $data['order'] ?? [];

        foreach ($ids as $index => $id) {
            $position = isset($order[$id]) ? (int) $order[$id] : ($index + 1);

            Product::where('id', $id)->update([
                'is_featured' => true,
                'featured_order' => $position,
            ]);
        }

        Cache::forget('home:featured_products');

        return redirect()->route('admin.homepage.featured-products.edit')
            ->with('status', 'Featured products and order updated successfully.');
    }
}

