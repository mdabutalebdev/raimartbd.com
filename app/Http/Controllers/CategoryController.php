<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::active()->topLevel()->withCount('products')->orderBy('sort_order')->get();

        return view('categories', compact('categories'));
    }

    public function show(Request $request, Category $category, ShopController $shopController)
    {
        abort_unless($category->is_active, 404);

        // Prepend / merge this category into the request so ShopController filters by it
        $request->merge(['category' => $category->slug]);

        return $shopController->index($request);
    }
}
