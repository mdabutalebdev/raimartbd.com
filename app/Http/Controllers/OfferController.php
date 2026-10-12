<?php

namespace App\Http\Controllers;

use App\Models\Product;

class OfferController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'images', 'brand', 'variants'])
            ->active()
            ->whereNotNull('old_price')
            ->whereColumn('old_price', '>', 'price')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('offers', compact('products'));
    }
}
