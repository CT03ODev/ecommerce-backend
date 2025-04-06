<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Urban Living',
                'slug' => Str::slug('Urban Living'),
                'description' => 'Modern furniture for urban spaces.',
                'content' => 'Urban Living offers a wide range of furniture designed for modern city living.',
                'thumbnail' => 'images/brands/default.png',
                'is_published' => true,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
            ],
            [
                'name' => 'Modern Habitat',
                'slug' => Str::slug('Modern Habitat'),
                'description' => 'Stylish and functional furniture.',
                'content' => 'Modern Habitat focuses on creating furniture that blends style and functionality.',
                'thumbnail' => 'images/brands/default.png',
                'is_published' => true,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
            ],
            [
                'name' => 'CozyNest',
                'slug' => Str::slug('CozyNest'),
                'description' => 'Comfortable furniture for your home.',
                'content' => 'CozyNest specializes in furniture that makes your home feel warm and inviting.',
                'thumbnail' => 'images/brands/default.png',
                'is_published' => true,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
            ],
            [
                'name' => 'Elegant Interiors',
                'slug' => Str::slug('Elegant Interiors'),
                'description' => 'Luxury furniture for elegant spaces.',
                'content' => 'Elegant Interiors provides high-end furniture for sophisticated homes.',
                'thumbnail' => 'images/brands/default.png',
                'is_published' => true,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
            ],
            [
                'name' => 'Timeless Furniture',
                'slug' => Str::slug('Timeless Furniture'),
                'description' => 'Classic designs that never go out of style.',
                'content' => 'Timeless Furniture offers pieces that stand the test of time.',
                'thumbnail' => 'images/brands/default.png',
                'is_published' => true,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
            ],
            [
                'name' => 'HomeCraft',
                'slug' => Str::slug('HomeCraft'),
                'description' => 'Handcrafted furniture for every home.',
                'content' => 'HomeCraft delivers unique, handcrafted furniture for all styles.',
                'thumbnail' => 'images/brands/default.png',
                'is_published' => true,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
            ],
            [
                'name' => 'Luxury Haven',
                'slug' => Str::slug('Luxury Haven'),
                'description' => 'Premium furniture for luxurious living.',
                'content' => 'Luxury Haven creates furniture that defines luxury and comfort.',
                'thumbnail' => 'images/brands/default.png',
                'is_published' => true,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
            ],
            [
                'name' => 'Rustic Charm',
                'slug' => Str::slug('Rustic Charm'),
                'description' => 'Rustic furniture with a modern twist.',
                'content' => 'Rustic Charm combines rustic aesthetics with modern functionality.',
                'thumbnail' => 'images/brands/default.png',
                'is_published' => true,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
            ],
            [
                'name' => 'Comfort & Style',
                'slug' => Str::slug('Comfort & Style'),
                'description' => 'Furniture that balances comfort and style.',
                'content' => 'Comfort & Style offers furniture that is both stylish and comfortable.',
                'thumbnail' => 'images/brands/default.png',
                'is_published' => true,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
            ],
            [
                'name' => 'Heritage Home',
                'slug' => Str::slug('Heritage Home'),
                'description' => 'Furniture inspired by heritage designs.',
                'content' => 'Heritage Home brings classic designs to modern homes.',
                'thumbnail' => 'images/brands/default.png',
                'is_published' => true,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
            ],
        ];

        DB::table('brands')->insert($brands);
    }
}
