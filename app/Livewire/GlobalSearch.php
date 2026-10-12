<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class GlobalSearch extends Component
{
    public $query = '';

    public function render()
    {
        $results = collect();
        $matchedCategories = collect();
        $totalCount = 0;

        $searchTerm = trim($this->query);

        if (strlen($searchTerm) >= 2) {
            // Search matching categories
            $matchedCategories = Category::active()
                ->where('name', 'like', '%' . $searchTerm . '%')
                ->withCount('products')
                ->take(4)
                ->get();

            // Search products by name OR category OR brand
            $baseQuery = Product::active()
                ->with(['category:id,name,slug', 'images'])
                ->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhereHas('category', fn ($c) => $c->where('name', 'like', '%' . $searchTerm . '%'))
                        ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', '%' . $searchTerm . '%'));
                });

            $totalCount = $baseQuery->count();
            $results = $baseQuery->take(6)->get();
        }

        $allCategories = Cache::remember('nav:search_categories', now()->addHours(6), function () {
            return Category::active()->orderBy('sort_order')->pluck('name')->values()->all();
        });

        return view('livewire.global-search', [
            'results' => $results,
            'matchedCategories' => $matchedCategories,
            'totalCount' => $totalCount,
            'allCategories' => $allCategories,
        ]);
    }
}
