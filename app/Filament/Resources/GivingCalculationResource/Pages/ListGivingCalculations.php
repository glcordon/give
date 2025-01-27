<?php

namespace App\Filament\Resources\GivingCalculationResource\Pages;

use App\Filament\Resources\GivingCalculationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGivingCalculations extends ListRecords
{
    protected static string $resource = GivingCalculationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
