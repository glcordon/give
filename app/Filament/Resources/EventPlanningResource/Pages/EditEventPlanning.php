<?php

namespace App\Filament\Resources\EventPlanningResource\Pages;

use App\Filament\Resources\EventPlanningResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEventPlanning extends EditRecord
{
    protected static string $resource = EventPlanningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
