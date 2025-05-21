<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductStatsOverview extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Products', Product::count())
                ->description('Total number of products')
                ->descriptionIcon('heroicon-m-square-3-stack-3d')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->color('success'),

            Stat::make('Active Products', Product::where([])->count())
                ->description('Products available for sale')
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart([4, 5, 3, 7, 4, 5, 2, 6])
                ->color('info'),

            Stat::make('Total Brands', Brand::count())
                ->description('Number of brands')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->chart([3, 5, 4, 4, 3, 3, 4, 3])
                ->color('warning'),
        ];
    }
}