<?php

namespace App\Filament\Resources\EventProposalResource\Pages;

use App\Filament\Resources\EventProposalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEventProposals extends ListRecords
{
    protected static string $resource = EventProposalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
