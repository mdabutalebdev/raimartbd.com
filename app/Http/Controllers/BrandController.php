<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::active()
            ->withCount(['products' => fn ($query) => $query->where('is_active', true)])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('brands', compact('brands'));
    }

    public function show(Request $request, Brand $brand, ShopController $shopController)
    {
        abort_unless($brand->is_active, 404);

        // Prepend / merge this brand into the request so ShopController filters by it
        $request->merge(['brand' => $brand->slug]);

        return $shopController->index($request);
    }
}
