<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::where(['is_published' => 1])->get();
        return $categories;
    }

    public function products(string $categoryId)
    {
        $products = Product::where('category_id', $categoryId)->with(['brand', 'images', 'variants'])->get();
        return $products;
    }
}
