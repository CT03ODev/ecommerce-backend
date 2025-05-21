<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class AdvancedRevenueChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected static ?string $heading = 'Revenue Analysis';
    protected static ?string $maxHeight = '400px';
    protected int | string | array $columnSpan = 'full';

    protected function getFilters(): ?array
    {
        return [
            'week' => 'This Week',
            'month' => 'This Month',
            'quarter' => 'This Quarter',
            'year' => 'This Year',
            'custom' => 'Custom Range',
        ];
    }

    protected function getData(): array
    {
        $data = match ($this->filter) {
            'week' => $this->getWeekData(),
            'month' => $this->getMonthData(),
            'quarter' => $this->getQuarterData(),
            'year' => $this->getYearData(),
            default => $this->getWeekData(),
        };

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => array_values($data['revenues']),
                    // 'borderColor' => '#10B981',
                    'fill' => 'false',
                    'tension' => 0.1
                ],
            ],
            'labels' => array_values($data['labels']),
        ];
    }

    private function getWeekData(): array
    {
        $days = collect(range(6, 0))->map(function ($day) {
            return Carbon::now()->subDays($day);
        });

        $revenues = $days->map(function ($date) {
            return Transaction::where('status', 'completed')
                ->whereDate('created_at', $date)
                ->sum('amount');
        });

        return [
            'revenues' => $revenues->values()->toArray(),
            'labels' => $days->map(fn ($date) => $date->format('D'))->toArray(),
        ];
    }

    private function getMonthData(): array
    {
        $days = collect(range(29, 0))->map(function ($day) {
            return Carbon::now()->subDays($day);
        });

        $revenues = $days->map(function ($date) {
            return Transaction::where('status', 'completed')
                ->whereDate('created_at', $date)
                ->sum('amount');
        });

        return [
            'revenues' => $revenues->values()->toArray(),
            'labels' => $days->map(fn ($date) => $date->format('d M'))->toArray(),
        ];
    }

    private function getQuarterData(): array
    {
        $startOfQuarter = Carbon::now()->startOfQuarter();
        $months = collect(range(0, 2))->map(function ($addMonths) use ($startOfQuarter) {
            return $startOfQuarter->copy()->addMonths($addMonths);
        });

        $revenues = $months->map(function ($month) {
            return Transaction::where('status', 'completed')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount');
        });

        return [
            'revenues' => $revenues->values()->toArray(),
            'labels' => $months->map(fn ($date) => $date->format('M Y'))->toArray(),
        ];
    }

    private function getYearData(): array
    {
        $months = collect(range(11, 0))->map(function ($month) {
            return Carbon::now()->subMonths($month);
        });

        $revenues = $months->map(function ($month) {
            return Transaction::where('status', 'completed')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount');
        });

        return [
            'revenues' => $revenues->values()->toArray(),
            'labels' => $months->map(fn ($date) => $date->format('M Y'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}