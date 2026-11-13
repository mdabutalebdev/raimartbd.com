<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FeaturedSortController extends Controller
{
    public function edit()
    {
        $categories = Category::active()
            ->withCount('products')
            ->orderByDesc('is_featured_tab')
            ->orderBy('featured_tab_order')
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($cat) => [
                'id' => $cat->id,
                'name' => $cat->name,
                'image' => $cat->image ? image_url($cat->image) : null,
                'products_count' => (int) $cat->products_count,
                'selected' => (bool) $cat->is_featured_tab,
                'order' => (int) $cat->featured_tab_order,
            ])
            ->values();

        return view('admin.featured-sort.edit', compact('categories'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
            'order' => ['nullable', 'array'],
        ]);

        $ids = collect($data['categories'] ?? [])->map(fn ($id) => (int) $id)->unique()->values();

        // Reset unselected categories
        Category::where('is_featured_tab', true)->whereNotIn('id', $ids)->update([
            'is_featured_tab' => false,
            'featured_tab_order' => 0,
        ]);

        $order = $data['order'] ?? [];

        foreach ($ids as $index => $id) {
            $position = isset($order[$id]) ? (int) $order[$id] : ($index + 1);

            Category::where('id', $id)->update([
                'is_featured_tab' => true,
                'featured_tab_order' => $position,
            ]);
        }

        Cache::forget('home:categories');
        Cache::forget('home:featured_tabs');

        return redirect()->route('admin.homepage.featured-sort.edit')
            ->with('status', 'Featured category tabs and display order updated successfully.');
    }
}

