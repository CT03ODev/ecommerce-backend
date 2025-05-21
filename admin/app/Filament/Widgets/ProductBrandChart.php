<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\Brand;
use Filament\Widgets\ChartWidget;

class ProductBrandChart extends ChartWidget
{
    protected static ?int $sort = 6;
    protected static ?string $heading = 'Products by Brand';
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $brands = Brand::withCount('products')->get();

        return [
            'datasets' => [
                [
                    'label' => 'Number of Products',
                    'data' => $brands->pluck('products_count')->values()->toArray(),
                    'backgroundColor' => [
                        '#10B981', '#3B82F6', '#F59E0B', '#EF4444',
                        '#6366F1', '#8B5CF6', '#EC4899', '#14B8A6',
                        '#854d0e', '#065f46', '#713f12', '#0891b2'
                    ],
                ],
            ],
            'labels' => $brands->pluck('name')->values()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}