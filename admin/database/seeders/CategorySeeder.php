<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Sofa',
                'slug' => Str::slug('Sofa'),
                'description' => 'Comfortable and stylish sofas for your living room.',
                'thumbnail' => 'images/categories/category-1.jpg',
                'content' => 'Detailed information about Sofa category.',
                'view_count' => 0,
                'is_published' => true,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
            ],
            [
                'name' => 'Terarce',
                'slug' => Str::slug('Terarce'),
                'description' => 'Beautiful terrace furniture for outdoor spaces.',
                'thumbnail' => 'images/categories/category-2.jpg',
                'content' => 'Detailed information about Terarce category.',
                'view_count' => 0,
                'is_published' => true,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
            ],
            [
                'name' => 'Bed',
                'slug' => Str::slug('Bed'),
                'description' => 'Comfortable beds for a good night\'s sleep.',
                'thumbnail' => 'images/categories/category-3.jpg',
                'content' => 'Detailed information about Bed category.',
                'view_count' => 0,
                'is_published' => true,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
            ],
            [
                'name' => 'Office',
                'slug' => Str::slug('Office'),
                'description' => 'Modern office furniture for productivity.',
                'thumbnail' => 'images/categories/category-4.jpg',
                'content' => 'Detailed information about Office category.',
                'view_count' => 0,
                'is_published' => true,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
            ],
            [
                'name' => 'Outdoor',
                'slug' => Str::slug('Outdoor'),
                'description' => 'Durable outdoor furniture for all weather conditions.',
                'thumbnail' => 'images/categories/category-5.jpg',
                'content' => 'Detailed information about Outdoor category.',
                'view_count' => 0,
                'is_published' => true,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
            ],
            [
                'name' => 'Mattress',
                'slug' => Str::slug('Mattress'),
                'description' => 'High-quality mattresses for ultimate comfort.',
                'thumbnail' => 'images/categories/category-6.jpg',
                'content' => 'Detailed information about Mattress category.',
                'view_count' => 0,
                'is_published' => true,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
            ],
        ];

        DB::table('product_categories')->insert($categories);
    }
}
