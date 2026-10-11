<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategorySectionController extends Controller
{
    public function index(Request $request)
    {
        // Normalize any uninitialized or duplicate home_order values
        if (Category::where('home_order', 0)->exists() || Category::select('home_order')->groupBy('home_order')->havingRaw('count(*) > 1')->exists()) {
            $this->normalizeOrders();
        }

        $query = Category::with(['parent', 'children'])
            ->withCount(['products', 'children']);

        // Search
        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Filter by homepage status
        if ($status = $request->input('status')) {
            if ($status === 'active') {
                $query->where('show_on_home', true);
            } elseif ($status === 'inactive') {
                $query->where('show_on_home', false);
            }
        }

        // Filter by level (all, main, sub)
        if ($level = $request->input('level')) {
            if ($level === 'main') {
                $query->whereNull('parent_id');
            } elseif ($level === 'sub') {
                $query->whereNotNull('parent_id');
            }
        }

        $categories = $query->orderBy('home_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $activeOnHomeCount = Category::where('show_on_home', true)->count();
        $totalCount = Category::count();

        return view('admin.homepage.category-sections', compact(
            'categories',
            'activeOnHomeCount',
            'totalCount'
        ));
    }

    public function toggle(Category $category)
    {
        $category->show_on_home = !$category->show_on_home;
        $category->save();

        Cache::forget('home:categories');

        return response()->json([
            'success' => true,
            'show_on_home' => (bool) $category->show_on_home,
            'active_count' => Category::where('show_on_home', true)->count(),
        ]);
    }

    public function reorder(Request $request)
    {
        $direction = $request->input('direction'); // 'up' or 'down'
        $id = $request->input('id');

        $current = Category::findOrFail($id);
        
        // Ensure all categories have a clean sequential home_order
        if (Category::where('home_order', 0)->exists() || Category::select('home_order')->groupBy('home_order')->havingRaw('count(*) > 1')->exists()) {
            $this->normalizeOrders();
            $current->refresh();
        }

        if ($direction === 'up') {
            $previous = Category::where('home_order', '<', $current->home_order)
                ->orderBy('home_order', 'desc')
                ->first();

            if ($previous) {
                $temp = $current->home_order;
                $current->home_order = $previous->home_order;
                $previous->home_order = $temp;
                $current->save();
                $previous->save();
            }
        } elseif ($direction === 'down') {
            $next = Category::where('home_order', '>', $current->home_order)
                ->orderBy('home_order', 'asc')
                ->first();

            if ($next) {
                $temp = $current->home_order;
                $current->home_order = $next->home_order;
                $next->home_order = $temp;
                $current->save();
                $next->save();
            }
        }

        Cache::forget('home:categories');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'current_id' => $current->id,
                'new_order' => $current->home_order,
            ]);
        }

        return redirect()->back()->with('status', 'Order updated successfully.');
    }

    /**
     * Normalize all categories to have unique, sequential 1..N home_order integers.
     */
    protected function normalizeOrders(): void
    {
        $all = Category::orderBy('home_order', 'asc')
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        foreach ($all as $index => $item) {
            $expected = $index + 1;
            if ($item->home_order !== $expected) {
                $item->home_order = $expected;
                $item->save();
            }
        }
    }

    public function updateSettings(Request $request, Category $category)
    {
        $validated = $request->validate([
            'home_layout' => 'required|in:slider,grid',
            'home_max_products' => 'required|integer|min:1|max:500',
            'home_per_page' => 'required|integer|min:1|max:100',
        ]);

        $category->update($validated);

        Cache::forget('home:categories');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'category' => $category,
            ]);
        }

        return redirect()->back()->with('status', 'Section settings updated successfully.');
    }
}
