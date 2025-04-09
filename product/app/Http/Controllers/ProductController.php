<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('is_published', 1)
            ->with(['category', 'brand', 'variants' => function ($query) {
                $query->orderBy('price', 'asc'); // Lấy giá đầu tiên theo thứ tự tăng dần
            }]);

        if ($request->has('q') && $request->q) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        if ($request->has('category') && $request->category) {
            $category = ProductCategory::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        if ($request->has('brand') && $request->brand) {
            $brand = Brand::where('slug', $request->brand)->first();
            if ($brand) {
                $query->where('brand_id', $brand->id);
            }
        }

        if ($request->has('sort') && $request->sort === 'price') {
            $query->join('product_variants', 'products.id', '=', 'product_variants.product_id')
                ->select('products.*', DB::raw('MIN(product_variants.price) as min_price'))
                ->groupBy('products.id')
                ->orderBy('min_price', $request->sort_type ?? 'asc');
        }

        if ($request->has('sort') && $request->sort === 'created_at') {
            $query->orderBy('created_at', $request->sort_type ?? 'asc');
        }

        $limit = $request->get('limit', 12);

        $products = $query->paginate($limit);

        return response()->json($products);
    }

    public function show(String $slug)
    {
        $product = Product::where(['slug' => $slug, 'is_published' => 1])
            ->with(['category', 'brand', 'images', 'variants', 'reviews.user'])
            ->first();

        if (!$product) {
            abort(404);
        }

        return response()->json($product);
    }
}
