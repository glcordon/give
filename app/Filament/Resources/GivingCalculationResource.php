<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GivingCalculationResource\Pages;
use App\Models\Campaign;
use App\Models\GivingCalculation;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Tables\Table;
use Filament\Tables\Actions\Action;

class GivingCalculationResource extends Resource
{
    protected static ?string $model = GivingCalculation::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationLabel = 'Giving';

    protected static $totalCheckAmount = [];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->default(Carbon::now()->format('m-d-Y') . ' Service')
                            ->required(),
                        DatePicker::make('date')
                            ->label('Date')
                            ->default(Carbon::now()->format('Y-m-d'))
                            ->required(),
                        // Activity Plan Relationship
                        Forms\Components\Select::make('activity_plan_id')
                            ->relationship('activityPlan', 'event_name')
                            ->label('Activity Plan')
                            ->required(),

                        // Campaign Relationship
                        Forms\Components\Select::make('campaign_id')
                            ->relationship('campaign', 'name')
                            ->label('Campaign')
                            ->createOptionForm(Campaign::getForm())
                            ->nullable(),

                        // Cash Denominations
                        Fieldset::make('Cash Denominations')
                            ->schema([
                                Forms\Components\TextInput::make('denomination_1')
                                    ->label('$1 Bills')
                                    ->numeric()
                                    ->default(0)
                                    ->live()
                                    ->afterStateupdated(function ($set, $get) {
                                        Self::updateDenominationsTotal($set, $get);
                                        Self::setTotalAmount($set, $get);
                                    })
                                    ->reactive(),
                                Forms\Components\TextInput::make('denomination_5')
                                    ->label('$5 Bills')
                                    ->numeric()
                                    ->default(0)
                                    ->live()
                                    ->afterStateupdated(function ($set, $get) {
                                        Self::updateDenominationsTotal($set, $get);
                                        Self::setTotalAmount($set, $get);
                                    })
                                    ->reactive(),
                                Forms\Components\TextInput::make('denomination_10')
                                    ->label('$10 Bills')
                                    ->numeric()
                                    ->default(0)
                                    ->afterStateupdated(function ($set, $get) {
                                        Self::updateDenominationsTotal($set, $get);
                                        Self::setTotalAmount($set, $get);
                                    })
                                    ->live()
                                    ->reactive(),
                                Forms\Components\TextInput::make('denomination_20')
                                    ->label('$20 Bills')
                                    ->numeric()
                                    ->default(0)
                                    ->afterStateupdated(function ($set, $get) {
                                        Self::updateDenominationsTotal($set, $get);
                                        Self::setTotalAmount($set, $get);
                                    })
                                    ->live()
                                    ->reactive(),
                                Forms\Components\TextInput::make('denomination_50')
                                    ->label('$50 Bills')
                                    ->numeric()
                                    ->default(0)
                                    ->afterStateupdated(function ($set, $get) {
                                        Self::updateDenominationsTotal($set, $get);
                                        Self::setTotalAmount($set, $get);
                                    })
                                    ->live()
                                    ->reactive(),
                                Forms\Components\TextInput::make('denomination_100')
                                    ->label('$100 Bills')
                                    ->numeric()
                                    ->default(0)
                                    ->afterStateupdated(function ($set, $get) {
                                        Self::updateDenominationsTotal($set, $get);
                                        Self::setTotalAmount($set, $get);
                                    })
                                    ->live()
                                    ->reactive(),
                            ]),
                        // Cash Denominations
                        Fieldset::make('coins')
                            ->schema([
                                Forms\Components\TextInput::make('denomination_penny')
                                    ->label('Penny')
                                    ->numeric()
                                    ->default(0)
                                    ->live()
                                    ->afterStateupdated(function ($set, $get) {
                                        Self::updateCoinTotal($set, $get);
                                        Self::setTotalAmount($set, $get);
                                    })
                                    ->reactive(),
                                Forms\Components\TextInput::make('denomination_nickel')
                                    ->label('Nickel')
                                    ->numeric()
                                    ->default(0)
                                    ->live()
                                    ->afterStateupdated(function ($set, $get) {
                                        Self::updateCoinTotal($set, $get);
                                        Self::setTotalAmount($set, $get);
                                    })
                                    ->reactive(),
                                Forms\Components\TextInput::make('denomination_dime')
                                    ->label('Dime')
                                    ->numeric()
                                    ->default(0)
                                    ->afterStateupdated(function ($set, $get) {
                                        Self::updateCoinTotal($set, $get);
                                        Self::setTotalAmount($set, $get);
                                    })
                                    ->live()
                                    ->reactive(),
                                Forms\Components\TextInput::make('denomination_quarter')
                                    ->label('Quarter')
                                    ->numeric()
                                    ->default(0)
                                    ->afterStateupdated(function ($set, $get) {
                                        Self::updateCoinTotal($set, $get);
                                        Self::setTotalAmount($set, $get);
                                    })
                                    ->live()
                                    ->reactive(),
                                Forms\Components\TextInput::make('denomination_half_dollar')
                                    ->label('Half Dollar')
                                    ->numeric()
                                    ->default(0)
                                    ->afterStateupdated(function ($set, $get) {
                                        Self::updateCoinTotal($set, $get);
                                        Self::setTotalAmount($set, $get);
                                    })
                                    ->live()
                                    ->reactive(),
                                Forms\Components\TextInput::make('denomination_coin_dollar')
                                    ->label('Coin Dollar')
                                    ->numeric()
                                    ->default(0)
                                    ->afterStateupdated(function ($set, $get) {
                                        Self::updateCoinTotal($set, $get);
                                        Self::setTotalAmount($set, $get);
                                    })
                                    ->live()
                                    ->reactive(),
                            ]),

                        // Checks
                        Fieldset::make('Checks')
                        ->schema([
                        Forms\Components\Repeater::make('checks')
                            ->schema([
                                Forms\Components\TextInput::make('check_number')
                                    ->label('Check Number')
                                    ->required(),
                                Forms\Components\TextInput::make('issuer_name')
                                    ->label('Issuer Name')
                                    ->required(),
                                Forms\Components\TextInput::make('amount')
                                    ->label('Amount ($)')
                                    ->numeric()
                                    ->prefix('$')
                                    ->live(debounce:1000)
                                    ->required(),
                            ])->afterStateUpdated(function ($set, $get) {
                                        Self::updateChecksTotal($set, $get);
                                        Self::setTotalAmount($set, $get);
                                    })
                            ->columns(3)
                            ->defaultItems(0)
                            ->label('Checks'),
                                ])
                                ->columns(1),

                        // Other Donations
                        Fieldset::make('Other Donations')
                        ->schema([
                        Forms\Components\Repeater::make('other_donations')
                            ->schema([
                                Forms\Components\TextInput::make('source')
                                    ->label('Source'),
                                Forms\Components\TextInput::make('amount')
                                    ->label('Amount ($)')
                                    ->prefix('$')
                                    ->numeric(),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->label('Other Donations'),
                            ])
                            ->columns(1),

                        // Other Donations
                        Forms\Components\Repeater::make('counters')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Name')
                                    ->required(),
                                Forms\Components\TextInput::make('role')
                                    ->label('Role')
                                    ->required(),
                            ])
                            ->minItems(2)
                            ->label('Counters'),

                        // Summary Fields
                        Forms\Components\TextInput::make('total_cash')
                            ->label('Total Cash ($)')
                            ->numeric()
                            ->prefix('$')
                            ->readOnly()
                            ->reactive(),

                        Forms\Components\TextInput::make('total_coin')
                            ->label('Total Coin ($)')
                            ->numeric()
                            ->prefix('$')
                            ->readOnly()
                            ->reactive(),

                        Forms\Components\TextInput::make('total_cash_coin')
                            ->label('Total Cash + Coin ($)')
                            ->numeric()
                            ->prefix('$')
                            ->readOnly()
                            ->reactive(),

                        Forms\Components\TextInput::make('total_checks')
                            ->label('Total Checks ($)')
                            ->numeric()
                            ->prefix('$')
                            ->readOnly()
                            ->reactive(),

                        Forms\Components\TextInput::make('total_giving')
                            ->label('Total Giving ($)')
                            ->numeric()
                            ->prefix('$')
                            ->readOnly()
                            ->reactive(),
                    ]),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Name'),
                TextColumn::make('date')->label('Date'),
                TextColumn::make('activityPlan.event_name')->label('Activity'),
                TextColumn::make('campaign.name')->label('Campaign'),
                TextColumn::make('total_cash')->label('Total Cash')->money('USD'),
                TextColumn::make('total_checks')->label('Total Checks')->money('USD'),
                TextColumn::make('total_giving')->label('Total Giving')->money('USD'),
            ])
            ->actions([
                Action::make('delete')
                ->requiresConfirmation()
                ->action(fn (GivingCalculation $record) => $record->delete())
        ])
            ;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGivingCalculations::route('/'),
            'create' => Pages\CreateGivingCalculation::route('/create'),
            'edit' => Pages\EditGivingCalculation::route('/{record}/edit'),
        ];
    }

    public static function updateDenominationsTotal($set, $get)
    {
        $set('total_cash', 
            (intval($get('denomination_1')) ?? 0) * 1 +
            (intval($get('denomination_5')) ?? 0) * 5 +
            (intval($get('denomination_10')) ?? 0) * 10 +
            (intval($get('denomination_20')) ?? 0) * 20 +
            (intval($get('denomination_50')) ?? 0) * 50 +
            (intval($get('denomination_100')) ?? 0) * 100
        );
    }
    public static function updateCoinTotal($set, $get)
    {
        $totalCoin = (intval($get('denomination_penny')) ?? 0) * 0.01 +
        (intval($get('denomination_nickel')) ?? 0) * 0.05 +
        (intval($get('denomination_dime')) ?? 0) * 0.10 +
        (intval($get('denomination_quarter')) ?? 0) * 0.25 +
        (intval($get('denomination_half_dollar')) ?? 0) * 0.50 +
        (intval($get('denomination_coin_dollar')) ?? 0) * 1;
        $set('total_coin', number_format($totalCoin, 2));

    }

    public static function updateChecksTotal($set, $get)
    {
        $set('total_checks', array_reduce($get('checks') ?? [], function ($carry, $item) {
            return intval($carry) + (intval($item['amount']) ?? 0);
        }, 0));
    }

    public static function setTotalAmount($set, $get)
    {
        $totalCash = $get('total_cash') ?? 0;
        $totalChecks = $get('total_checks') ?? 0;
        $totalCoin = $get('total_coin') ?? 0;
        $set('total_giving',$totalCash + $totalChecks + $totalCoin);
        $set('total_cash_coin',$totalCash + $totalCoin);
    }
}
