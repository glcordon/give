<?php

namespace App\Filament\Widgets;

use App\Models\Campaign;
use App\Models\GivingCalculation;
use App\Models\Member;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Giving', '$' . number_format(GivingCalculation::sum('total_giving'), 2))
                ->description('Includes cash, coins, and checks')
                ->icon('heroicon-o-currency-dollar'),

            Stat::make('Total Giving This Month', '$' . number_format(GivingCalculation::whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->sum('total_giving'), 2))
                ->description('Includes cash, coins, and checks')
                ->icon('heroicon-o-currency-dollar'),

            Stat::make('Total Bank Deposits This Month', '$' . number_format(GivingCalculation::whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->sum('total_bank_deposit'), 2))
                ->icon('heroicon-o-banknotes'),

            Stat::make('Total Bank Deposits', '$' . number_format(GivingCalculation::sum('total_bank_deposit'), 2))
                ->description('Total amount prepared for deposits')
                ->icon('heroicon-o-banknotes'),

            // Stat::make('Active Campaigns', Campaign::where('is_active', true)->count())
            //     ->description('Ongoing campaigns')
            //     ->icon('heroicon-o-folder-open'),

            Stat::make('Total Members', Member::count())
                ->description('All registered church members')
                ->icon('heroicon-o-user-group'),

            Stat::make('Upcoming Activities', Campaign::whereDate('start_date', '>=', now())->count())
                ->description('Scheduled events and campaigns')
                ->icon('heroicon-o-calendar'),
        ];
    }
}
