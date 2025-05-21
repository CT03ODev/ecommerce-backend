<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductCategory;
use Filament\Widgets\ChartWidget;

class ProductCategoryChart extends ChartWidget
{
    protected static ?int $sort = 5;
    protected static ?string $heading = 'Products by Category';
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $categories = ProductCategory::withCount('products')->get();

        return [
            'datasets' => [
                [
                    'label' => 'Number of Products',
                    'data' => $categories->pluck('products_count')->values()->toArray(),
                    'backgroundColor' => [
                        '#10B981', '#3B82F6', '#F59E0B', '#EF4444',
                        '#6366F1', '#8B5CF6', '#EC4899', '#14B8A6'
                    ],
                ],
            ],
            'labels' => $categories->pluck('name')->values()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}