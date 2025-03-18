<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']); 
    Route::get('/{id}', [ProductController::class, 'show']);
});

Route::prefix('product-categories')->group(function () {
    Route::get('/', [ProductCategoryController::class, 'index']);
    Route::get('/{categoryId}/products', [ProductCategoryController::class, 'products']); 
});

Route::prefix('brands')->group(function () {
    Route::get('/', [BrandController::class, 'index']);
    Route::get('/{brandId}/products', [BrandController::class, 'products']);
});

Route::prefix('reviews')->group(function () {
    Route::get('/{productId}', [ReviewController::class, 'index']);
});

Route::fallback(function () {
    return response()->json([
        'message' => 'Route not found. Please check the API documentation1.',
        'status' => 404
    ], 404);
});