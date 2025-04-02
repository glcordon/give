<?php

namespace App\Filament\Resources\EventPlanningResource\Pages;

use App\Filament\Resources\EventPlanningResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEventPlannings extends ListRecords
{
    protected static string $resource = EventPlanningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
