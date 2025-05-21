<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Orders', Order::count())
                ->description('Total number of orders')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->color('success'),

            Stat::make('Revenue', Transaction::where('status', 'completed')->sum('amount') . "$")
                ->description('Total revenue from orders')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->chart([4, 5, 3, 7, 4, 5, 2, 6])
                ->color('warning'),

            Stat::make('Products', Product::count())
                ->description('Total number of products')
                ->descriptionIcon('heroicon-m-cube')
                ->chart([3, 5, 4, 4, 3, 3, 4, 3])
                ->color('info'),
        ];
    }
}