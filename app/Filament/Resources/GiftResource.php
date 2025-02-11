<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Gift;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use App\Filament\Exports\GiftsExporter;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\NumberInput;
use App\Filament\Resources\GiftResource\Pages;
use App\Filament\Resources\GiftResource\Pages\EditGift;
use App\Filament\Resources\GiftResource\Pages\ListGifts;
use App\Filament\Resources\GiftResource\Pages\CreateGift;

class GiftResource extends Resource
{
    protected static ?string $model = Gift::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(gift::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('campaign.name')->searchable(),
                TextColumn::make('amount')->sortable()->money('USD'),
                TextColumn::make('giver_name'),
                TextColumn::make('created_at')->sortable(),
            ])
            ->filters([
                SelectFilter::make('campaign')
                    ->relationship('campaign', 'name')
            ])
            ->actions([
                Action::make('delete')
                    ->requiresConfirmation()
                    ->action(fn(Self $record) => $record->delete()),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(GiftsExporter::class)
            ])
        ;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGifts::route('/'),
            'create' => Pages\CreateGift::route('/create'),
            'edit' => Pages\EditGift::route('/{record}/edit'),
        ];
    }
}
