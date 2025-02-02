<?php

namespace App\Filament\Resources\GivingCalculationResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GivingWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            'data' => 'data2'
        ];
    }
}
