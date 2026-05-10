<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class LowStockAlert extends BaseWidget
{
    protected function getStats(): array
    {
        $lowStockCount = DB::table('branch_product')
            ->whereColumn('stock_level', '<=', 'low_stock_threshold')
            ->count();

        return [
            Stat::make('Low Stock Items', $lowStockCount)
                ->description('Products needing restock across branches')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }
}