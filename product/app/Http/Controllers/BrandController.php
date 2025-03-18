<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::where(['is_published' => 1])->get();

        return $brands;
    }

    public function products($brandId)
    {
        $products = Product::where(['is_published' => 1, 'brand_id' => $brandId])->with(['category', 'images', 'variants'])->get();
        return $products;
    }
}
