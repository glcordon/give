<?php

namespace App\Filament\Resources\ActivityPlanResource\Pages;

use App\Filament\Resources\ActivityPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListActivityPlans extends ListRecords
{
    protected static string $resource = ActivityPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
