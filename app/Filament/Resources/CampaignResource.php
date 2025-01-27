<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampaignResource\Pages;
use App\Filament\Resources\CampaignResource\Pages\CreateCampaign;
use App\Filament\Resources\CampaignResource\Pages\EditCampaign;
use App\Filament\Resources\CampaignResource\Pages\ListCampaigns;
use App\Filament\Resources\GiftResource;
use App\Models\Campaign;
use App\Models\Gift;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\NumberInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichText;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;
    protected static ?string $navigationIcon = 'heroicon-c-presentation-chart-line';

    protected static array $getOptions = [
        'status' => [
            'Pastors Anniversary' => 'Pastors Anniversary',
            'Church Anniversary' => 'Church Anniversary',
            'General Stewardship' => 'General Stewardship',
        ],
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                Select::make('type')
                    ->options(Self::$getOptions['status'])
                    ->default('Pastors Anniversary')
                    ->required(),
                TextInput::make('goal_amount')->required()->numeric()->minValue(0),
                RichText::make('description')->required(),
                DatePicker::make('start_date')->required(),
                DatePicker::make('end_date')->required(),
                Hidden::make('created_by')->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('type')->searchable(),
                TextColumn::make('goal_amount')->sortable()->money('USD'),
                TextColumn::make('start_date')->sortable(),
                TextColumn::make('end_date')->sortable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCampaigns::route('/'),
            'create' => Pages\CreateCampaign::route('/create'),
            'edit' => Pages\EditCampaign::route('/{record}/edit'),
            // 'report' => Pages\CampaignReportPage::route('/report'),
        ];
    }
}
