<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [];
        $variants = [];

        $productNames = [
            'Modern Sofa',
            'Luxury Bed',
            'Office Chair',
            'Dining Table',
            'Outdoor Lounge',
            'Bookshelf',
            'Coffee Table',
            'Wardrobe',
            'TV Stand',
            'Recliner Chair',
            'Bar Stool',
            'Patio Set'
        ];

        foreach ($productNames as $index => $name) {
            $productId = $index + 1;

            $products[] = [
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => 'High-quality ' . strtolower($name) . ' designed for comfort and style.',
                'thumbnail' => 'images/products/product' . $productId . '.jpg',
                'content' => 'Detailed information about ' . $name . '. Perfect for enhancing your living space.',
                'category_id' => rand(1, 6), 
                'brand_id' => rand(1, 10),
                'is_published' => true,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            for ($j = 1; $j <= 3; $j++) {
                $variants[] = [
                    'product_id' => $productId,
                    'size' => "Size " . rand(1, 3) . 'x ' . rand(1, 3) . 'm',
                    'price' => rand(100, 1000) * 10, 
                    'stock_quantity' => rand(10, 100), 
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('products')->insert($products);
        DB::table('product_variants')->insert($variants);
    }
}