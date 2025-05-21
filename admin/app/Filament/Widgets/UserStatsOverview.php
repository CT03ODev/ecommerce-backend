<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class UserStatsOverview extends BaseWidget
{
    protected static ?int $sort = 4;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('Total registered users')
                ->descriptionIcon('heroicon-m-users')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->color('success'),

            Stat::make('New Users This Month', User::whereMonth('created_at', Carbon::now()->month)->count())
                ->description('Users registered this month')
                ->descriptionIcon('heroicon-m-user-plus')
                ->chart([4, 5, 3, 7, 4, 5, 2, 6])
                ->color('info'),

            Stat::make('Active Users', User::where([])->count())
                ->description('Currently active users')
                ->descriptionIcon('heroicon-m-user-circle')
                ->chart([3, 5, 4, 4, 3, 3, 4, 3])
                ->color('warning'),
        ];
    }
}