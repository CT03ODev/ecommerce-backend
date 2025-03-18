<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where(['is_published' => 1])->with(['category', 'brand'])->get();
        return $products;
    }

    public function show($id)
    {
        $product = Product::where(['id' => $id, 'is_published' => 1])
            ->with(['category', 'brand', 'images', 'variants', 'reviews.user'])
            ->first();

        if (!$product) {
            abort(404);
        }

        return response()->json($product);
    }
}
