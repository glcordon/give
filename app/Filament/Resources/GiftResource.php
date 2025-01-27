<?php

namespace App\Filament\Resources;

use App\Filament\Exports\GiftsExporter;
use App\Filament\Resources\GiftResource\Pages;
use App\Filament\Resources\GiftResource\Pages\CreateGift;
use App\Filament\Resources\GiftResource\Pages\EditGift;
use App\Filament\Resources\GiftResource\Pages\ListGifts;
use App\Models\Gift;
use Filament\Forms;
use Filament\Forms\Components\NumberInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

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
                TextColumn::make('payment_method'),
                TextColumn::make('giver_name'),
                TextColumn::make('created_at')->sortable(),
            ])
            ->filters([
                SelectFilter::make('campaign')
                ->relationship('campaign', 'name')
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
