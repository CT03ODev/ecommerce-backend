<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(string $productId)
    {
        $reviews = Review::where(['product_id' => $productId, 'is_published' => 1])->with('user')->get();
        return $reviews;
    }
}
