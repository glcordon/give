<?php

namespace App\Filament\Resources;

use App\Models\Campaign;
use App\Models\Event;
use App\Models\GivingCalculation;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Blade;

class GivingCalculationResource extends Resource
{
    protected static ?string $model = GivingCalculation::class;
    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationLabel = 'Offerings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    // Step 1: Basic Information
                    Wizard\Step::make('Basic Information')
                        ->schema([
                            TextInput::make('name')
                                ->label('Name')
                                ->default(Carbon::now()->format('m-d-Y') . ' Service')
                                ->required(),
                            DatePicker::make('date')
                                ->label('Date')
                                ->default(Carbon::now()->format('Y-m-d'))
                                ->required(),
                            Select::make('event_id')
                                ->relationship('event', 'name')
                                ->label('Event')
                                ->required(),
                            Select::make('campaign_id')
                                ->relationship('campaign', 'name')
                                ->label('Campaign')
                                ->createOptionForm(Campaign::getForm())
                                ->nullable(),
                        ]),

                    // Step 2: Envelope Verification
                    Wizard\Step::make('Envelopes')
                        ->schema([
                            Toggle::make('skip_envelope_verification')
                                ->label('Skip Envelope Verification')
                                ->live(),
                            Fieldset::make('Envelope Verification')
                                ->hidden(fn ($get) => $get('skip_envelope_verification'))
                                ->schema([
                                    Repeater::make('envelopes')
                                        ->schema([
                                            TextInput::make('donor_name')
                                                ->label('Donor Name')
                                                ->required(),
                                            TextInput::make('written_amount')
                                                ->label('Written Amount ($)')
                                                ->numeric()
                                                ->required(),
                                            TextInput::make('actual_amount')
                                                ->label('Actual Amount ($)')
                                                ->numeric()
                                                ->required(),
                                            Repeater::make('allocations')
                                                ->schema([
                                                    Select::make('campaign_id')
                                                        ->relationship('campaign', 'name')
                                                        ->required(),
                                                    TextInput::make('amount')
                                                        ->label('Amount ($)')
                                                        ->numeric()
                                                        ->required(),
                                                ])
                                                ->columns(2)
                                                ->defaultItems(1),
                                            TextInput::make('verification_reason')
                                                ->label('Reason for Discrepancy')
                                                ->hidden(fn ($get) => $get('written_amount') == $get('actual_amount')),
                                        ])
                                        ->columns(2)
                                        ->defaultItems(0)
                                        ->label('Envelopes'),
                                ]),
                            Actions::make([
                                Action::make('calculateEnvelopeTotals')
                                    ->label('Calculate Envelope Totals')
                                    ->action(function ($set, $get) {
                                        $totalEnvelopes = array_reduce($get('envelopes') ?? [], function ($carry, $item) {
                                            return floatval($carry) + (floatval($item['actual_amount']) ?? 0);
                                        }, 0);
                                        $set('total_envelopes', $totalEnvelopes);
                                    }),
                            ]),
                        ])
                        ->afterValidation(function ($set, $get) {
                            if (!$get('skip_envelope_verification') && $get('total_envelopes') === null) {
                                throw new \Exception('Please calculate envelope totals before proceeding.');
                            }
                        }),

                    // Step 3: Cash & Coins
                    Wizard\Step::make('Cash & Coins')
                        ->schema([
                            Fieldset::make('Cash Denominations')
                                ->schema([
                                    TextInput::make('denomination_1')
                                        ->label('$1 Bills')
                                        ->numeric()
                                        ->placeholder(0)
                                        ->live(debounce: 500)
                                        ->afterStateUpdated(function ($set, $get) {
                                            Self::updateDenominationsTotal($set, $get);
                                            Self::setTotalAmount($set, $get);
                                        })
                                        ->reactive(),
                                    // Add other denominations ($5, $10, $20, $50, $100) similarly...
                                ]),
                            Fieldset::make('Coins')
                                ->schema([
                                    TextInput::make('denomination_penny')
                                        ->label('Pennies')
                                        ->numeric()
                                        ->placeholder(0)
                                        ->live(debounce: 500)
                                        ->afterStateUpdated(function ($set, $get) {
                                            Self::updateCoinTotal($set, $get);
                                            Self::setTotalAmount($set, $get);
                                        })
                                        ->reactive(),
                                    // Add other coins (nickels, dimes, quarters, etc.) similarly...
                                ]),
                        ]),

                    // Step 4: Checks & Other Donations
                    Wizard\Step::make('Checks & Other Donations')
                        ->schema([
                            Fieldset::make('Checks')
                                ->schema([
                                    Repeater::make('checks')
                                        ->schema([
                                            TextInput::make('check_number')
                                                ->label('Check Number')
                                                ->required(),
                                            TextInput::make('issuer_name')
                                                ->label('Issuer Name')
                                                ->required(),
                                            TextInput::make('amount')
                                                ->label('Amount ($)')
                                                ->numeric()
                                                ->prefix('$')
                                                ->live(debounce: 500)
                                                ->required(),
                                        ])
                                        ->afterStateUpdated(function ($set, $get) {
                                            Self::updateChecksTotal($set, $get);
                                            Self::setTotalAmount($set, $get);
                                        })
                                        ->columns(3)
                                        ->defaultItems(0)
                                        ->label('Checks'),
                                ]),
                            Fieldset::make('Other Donations')
                                ->schema([
                                    Repeater::make('other_donations')
                                        ->schema([
                                            Select::make('source')
                                                ->options([
                                                    'cash_app' => 'Cash App',
                                                    'givelify' => 'Givelify',
                                                    'money_order' => 'Money Order',
                                                    'zelle' => 'Zelle',
                                                    'other' => 'Other',
                                                ])
                                                ->label('Source'),
                                            TextInput::make('amount')
                                                ->label('Amount ($)')
                                                ->prefix('$')
                                                ->live(debounce: 500)
                                                ->numeric(),
                                        ])
                                        ->afterStateUpdated(function ($set, $get) {
                                            Self::updateOtherTotal($set, $get);
                                            Self::setTotalAmount($set, $get);
                                        })
                                        ->columns(2)
                                        ->defaultItems(0)
                                        ->label('Other Donations'),
                                ]),
                        ]),

                    // Step 5: Summary
                    Wizard\Step::make('Summary')
                        ->schema([
                            TextInput::make('total_cash')
                                ->label('Total Cash ($)')
                                ->numeric()
                                ->prefix('$')
                                ->placeholder(0)
                                ->readOnly()
                                ->reactive(),
                            TextInput::make('total_coin')
                                ->label('Total Coin ($)')
                                ->numeric()
                                ->prefix('$')
                                ->placeholder(0)
                                ->readOnly()
                                ->reactive(),
                            TextInput::make('total_cash_coin')
                                ->label('Total Cash + Coin ($)')
                                ->numeric()
                                ->prefix('$')
                                ->placeholder(0)
                                ->readOnly()
                                ->reactive(),
                            TextInput::make('total_checks')
                                ->label('Total Checks ($)')
                                ->numeric()
                                ->prefix('$')
                                ->placeholder(0)
                                ->readOnly()
                                ->reactive(),
                            TextInput::make('total_other_donations')
                                ->label('Total Other Donations ($)')
                                ->numeric()
                                ->prefix('$')
                                ->placeholder(0)
                                ->readOnly()
                                ->reactive(),
                            TextInput::make('total_envelopes')
                                ->label('Total Envelopes ($)')
                                ->numeric()
                                ->prefix('$')
                                ->placeholder(0)
                                ->readOnly()
                                ->reactive(),
                            TextInput::make('total_bank_deposit')
                                ->label('Total Bank Deposit ($)')
                                ->numeric()
                                ->prefix('$')
                                ->placeholder(0)
                                ->readOnly()
                                ->reactive(),
                            TextInput::make('total_giving')
                                ->label('Total Giving ($)')
                                ->numeric()
                                ->prefix('$')
                                ->placeholder(0)
                                ->readOnly()
                                ->reactive(),
                            TextInput::make('discrepancy_reason')
                                ->label('Reason for Discrepancy')
                                ->hidden(fn ($get) => (
                                    $get('skip_envelope_verification') ||
                                    $get('total_envelopes') == ($get('total_cash_coin') + $get('total_checks') + $get('total_other_donations'))
                                )
                                ->required(),
                        ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Name'),
                TextColumn::make('date')->label('Date'),
                TextColumn::make('event.name')->label('Event'),
                TextColumn::make('campaign.name')->label('Campaign'),
                TextColumn::make('total_cash')->label('Total Cash')->money('USD'),
                TextColumn::make('total_checks')->label('Total Checks')->money('USD'),
                TextColumn::make('total_giving')->label('Total Giving')->money('USD'),
                TextColumn::make('total_bank_deposit')->label('Total Bank Deposit')->money('USD'),
            ])
            ->actions([
                TableAction::make('delete')
                    ->requiresConfirmation()
                    ->action(fn(GivingCalculation $record) => $record->delete()),
                TableAction::make('pdf')
                    ->label('PDF')
                    ->color('success')
                    ->action(function (GivingCalculation $record) {
                        return response()->streamDownload(function () use ($record) {
                            echo Pdf::loadHtml(
                                Blade::render('pdf', ['record' => $record])
                            )->stream();
                        }, $record->name . '.pdf');
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGivingCalculations::route('/'),
            'create' => Pages\CreateGivingCalculation::route('/create'),
            'edit' => Pages\EditGivingCalculation::route('/{record}/edit'),
        ];
    }

    // Helper Methods
    public static function updateDenominationsTotal($set, $get)
    {
        $set('total_cash', (
            (intval($get('denomination_1')) * 1) +
            (intval($get('denomination_5')) * 5) +
            (intval($get('denomination_10')) * 10) +
            (intval($get('denomination_20')) * 20) +
            (intval($get('denomination_50')) * 50) +
            (intval($get('denomination_100')) * 100)
        ));
    }

    public static function updateCoinTotal($set, $get)
    {
        $totalCoin = (
            (floatval($get('denomination_penny')) * 0.01) +
            (floatval($get('denomination_nickel')) * 0.05) +
            (floatval($get('denomination_dime')) * 0.10) +
            (floatval($get('denomination_quarter')) * 0.25) +
            (floatval($get('denomination_half_dollar')) * 0.50) +
            (intval($get('denomination_coin_dollar')) * 1)
        );
        $set('total_coin', number_format($totalCoin, 2));
    }

    public static function updateChecksTotal($set, $get)
    {
        $set('total_checks', array_reduce($get('checks') ?? [], function ($carry, $item) {
            return floatval($carry) + (floatval($item['amount']) ?? 0);
        }, 0));
    }

    public static function updateOtherTotal($set, $get)
    {
        $set('total_other_donations', array_reduce($get('other_donations') ?? [], function ($carry, $item) {
            return floatval($carry) + (floatval($item['amount']) ?? 0);
        }, 0));
    }

    public static function updateEnvelopeTotal($set, $get)
    {
        $set('total_envelopes', array_reduce($get('envelopes') ?? [], function ($carry, $item) {
            return floatval($carry) + (floatval($item['actual_amount']) ?? 0);
        }, 0));
    }

    public static function setTotalAmount($set, $get)
    {
        $totalCash = $get('total_cash') ?? 0;
        $totalCoin = $get('total_coin') ?? 0;
        $totalChecks = $get('total_checks') ?? 0;
        $totalOther = $get('total_other_donations') ?? 0;
        $totalEnvelopes = $get('total_envelopes') ?? 0;

        $set('total_cash_coin', $totalCash + $totalCoin);
        $set('total_bank_deposit', $totalCash + $totalCoin + $totalChecks);
        $set('total_giving', $totalCash + $totalCoin + $totalChecks + $totalOther + $totalEnvelopes);
    }
}