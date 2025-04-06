<?php

namespace App\Filament\Resources;

use Tables\Table;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Campaign;
use Filament\Forms\Form;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Resource;
use App\Models\GivingCalculation;
use Filament\Forms\Components\Card;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard;
use Illuminate\Support\Facades\Blade;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Actions\Action as tableAction;
use App\Filament\Resources\GivingCalculationResource\Pages;
use App\Filament\Resources\GivingCalculationResource\Widgets\GivingWidget;

class GivingCalculationResource extends Resource
{
    protected static ?string $model = GivingCalculation::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationLabel = 'Offerings';

    protected static $totalCheckAmount = [];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    // Step 1: Basic Information
                    Step::make('Basic Information')
                        ->schema([
                            TextInput::make('name')
                                ->label('Name')
                                ->default(Carbon::now()->format('m-d-Y') . ' Service')
                                ->required(),
                            DatePicker::make('date')
                                ->label('Date')
                                ->default(Carbon::now()->format('Y-m-d'))
                                ->required(),
                            Select::make('activity_plan_id')
                                ->relationship('activityPlan', 'event_name')
                                ->label('Event')
                                ->required(),
                            Select::make('campaign_id')
                                ->relationship('campaign', 'name')
                                ->label('Campaign')
                                ->createOptionForm(Campaign::getForm())
                                ->nullable(),
                        ]),

                    // Step 2: Envelope Verification
                    Step::make('Envelopes')
                        ->schema([
                            Toggle::make('skip_envelope_verification')
                                ->label('Skip Envelope Verification')
                                ->live(),
                            Fieldset::make('Envelope Verification')
                                ->hidden(fn($get) => $get('skip_envelope_verification'))
                                ->schema([
                                    Repeater::make('envelopes')
                                        ->itemLabel(function (array $state): ?string {
                                            // Use the donor_name as the label if it exists
                                            return $state['donor_name'] ?? 'New Envelope';
                                        })
                                        ->label()
                                        ->schema([
                                            TextInput::make('donor_name')
                                                ->label('Donor Name')
                                                ->columnSpanFull()
                                                ->live()
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
                                                ->columnSpanFull()
                                                ->defaultItems(1),
                                            TextInput::make('verification_reason')
                                                ->label('Reason for Discrepancy')
                                                ->hidden(fn($get) => $get('written_amount') == $get('actual_amount')),
                                        ])
                                        ->columns(2)
                                        ->collapsible()
                                        ->columnSpanFull()
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
                    Step::make('Cash & Coins')
                        ->schema([
                            Fieldset::make('Cash Denominations')
                                ->schema([
                                    Forms\Components\TextInput::make('denomination_1')
                                        ->label('$1 Bills')
                                        ->numeric()
                                        ->placeholder(0)
                                        ->default(0)
                                        ->live(onBlur: true)

                                        ->afterStateupdated(function ($set, $get) {
                                            Self::updateDenominationsTotal($set, $get);
                                            Self::setTotalAmount($set, $get);
                                        })
                                        ->reactive(),
                                    Forms\Components\TextInput::make('denomination_5')
                                        ->label('$5 Bills')
                                        ->numeric()
                                        ->placeholder(0)
                                        ->default(0)
                                        ->live(onBlur: true)

                                        ->afterStateupdated(function ($set, $get) {
                                            Self::updateDenominationsTotal($set, $get);
                                            Self::setTotalAmount($set, $get);
                                        })
                                        ->reactive(),
                                    Forms\Components\TextInput::make('denomination_10')
                                        ->label('$10 Bills')
                                        ->numeric()

                                        ->placeholder(0)
                                        ->default(0)
                                        ->afterStateupdated(function ($set, $get) {
                                            Self::updateDenominationsTotal($set, $get);
                                            Self::setTotalAmount($set, $get);
                                        })
                                        ->live(onBlur: true)
                                        ->reactive(),
                                    Forms\Components\TextInput::make('denomination_20')
                                        ->label('$20 Bills')
                                        ->numeric()

                                        ->placeholder(0)
                                        ->default(0)
                                        ->afterStateupdated(function ($set, $get) {
                                            Self::updateDenominationsTotal($set, $get);
                                            Self::setTotalAmount($set, $get);
                                        })
                                        ->live(onBlur: true)
                                        ->reactive(),
                                    Forms\Components\TextInput::make('denomination_50')
                                        ->label('$50 Bills')
                                        ->numeric()

                                        ->placeholder(0)
                                        ->default(0)
                                        ->afterStateupdated(function ($set, $get) {
                                            Self::updateDenominationsTotal($set, $get);
                                            Self::setTotalAmount($set, $get);
                                        })
                                        ->live(onBlur: true)
                                        ->reactive(),
                                    Forms\Components\TextInput::make('denomination_100')
                                        ->label('$100 Bills')
                                        ->numeric()

                                        ->placeholder(0)
                                        ->default(0)
                                        ->afterStateupdated(function ($set, $get) {
                                            Self::updateDenominationsTotal($set, $get);
                                            Self::setTotalAmount($set, $get);
                                        })
                                        ->live(onBlur: true)
                                        ->reactive(),
                                ]),
                            // Cash Denominations
                            Fieldset::make('coins')
                                ->schema([
                                    Forms\Components\TextInput::make('denomination_penny')
                                        ->label('Penny')
                                        ->numeric()
                                        ->placeholder(0)
                                        ->default(0)
                                        ->live(onBlur: true)

                                        ->afterStateupdated(function ($set, $get) {
                                            Self::updateCoinTotal($set, $get);
                                            Self::setTotalAmount($set, $get);
                                        })
                                        ->reactive(),
                                    Forms\Components\TextInput::make('denomination_nickel')
                                        ->label('Nickel')
                                        ->numeric()
                                        ->placeholder(0)
                                        ->default(0)
                                        ->live(onBlur: true)

                                        ->afterStateupdated(function ($set, $get) {
                                            Self::updateCoinTotal($set, $get);
                                            Self::setTotalAmount($set, $get);
                                        })
                                        ->reactive(),
                                    Forms\Components\TextInput::make('denomination_dime')
                                        ->label('Dime')
                                        ->numeric()

                                        ->placeholder(0)
                                        ->default(0)
                                        ->afterStateupdated(function ($set, $get) {
                                            Self::updateCoinTotal($set, $get);
                                            Self::setTotalAmount($set, $get);
                                        })
                                        ->live(onBlur: true)
                                        ->reactive(),
                                    Forms\Components\TextInput::make('denomination_quarter')
                                        ->label('Quarter')
                                        ->numeric()

                                        ->placeholder(0)
                                        ->default(0)
                                        ->afterStateupdated(function ($set, $get) {
                                            Self::updateCoinTotal($set, $get);
                                            Self::setTotalAmount($set, $get);
                                        })
                                        ->live(onBlur: true)
                                        ->reactive(),
                                    Forms\Components\TextInput::make('denomination_half_dollar')
                                        ->label('Half Dollar')
                                        ->numeric()

                                        ->placeholder(0)
                                        ->default(0)
                                        ->afterStateupdated(function ($set, $get) {
                                            Self::updateCoinTotal($set, $get);
                                            Self::setTotalAmount($set, $get);
                                        })
                                        ->live(onBlur: true)
                                        ->reactive(),
                                    Forms\Components\TextInput::make('denomination_coin_dollar')
                                        ->label('Coin Dollar')
                                        ->numeric()

                                        ->placeholder(0)
                                        ->default(0)
                                        ->afterStateupdated(function ($set, $get) {
                                            Self::updateCoinTotal($set, $get);
                                            Self::setTotalAmount($set, $get);
                                        })
                                        ->live(onBlur: true)
                                        ->reactive(),
                                ]),

                        ]),

                    // Step 4: Checks & Other Donations
                    Step::make('Checks & Other Donations')
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
                                        ->columnSpanFull()
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
                    Step::make('Summary')
                        ->schema([
                            TextInput::make('total_cash')
                                ->label('Total Cash ($)')
                                ->numeric()
                                ->prefix('$')
                                ->placeholder(0)
                                ->default(0)
                                ->readOnly()
                                ->reactive(),
                            TextInput::make('total_coin')
                                ->label('Total Coin ($)')
                                ->numeric()
                                ->prefix('$')
                                ->placeholder(0)
                                ->default(0)
                                ->readOnly()
                                ->reactive(),
                            TextInput::make('total_cash_coin')
                                ->label('Total Cash + Coin ($)')
                                ->numeric()
                                ->prefix('$')
                                ->placeholder(0)
                                ->default(0)
                                ->readOnly()
                                ->reactive(),
                            TextInput::make('total_checks')
                                ->label('Total Checks ($)')
                                ->numeric()
                                ->prefix('$')
                                ->placeholder(0)
                                ->default(0)
                                ->readOnly()
                                ->reactive(),
                            TextInput::make('total_other_donations')
                                ->label('Total Other Donations ($)')
                                ->numeric()
                                ->prefix('$')
                                ->placeholder(0)
                                ->default(0)
                                ->readOnly()
                                ->reactive(),
                            TextInput::make('total_envelopes')
                                ->label('Total Envelopes ($)')
                                ->numeric()
                                ->prefix('$')
                                ->placeholder(0)
                                ->default(0)
                                ->readOnly()
                                ->reactive(),
                            TextInput::make('total_bank_deposit')
                                ->label('Total Bank Deposit ($)')
                                ->numeric()
                                ->prefix('$')
                                ->placeholder(0)
                                ->default(0)
                                ->readOnly()
                                ->reactive(),
                            TextInput::make('total_giving')
                                ->label('Total Giving ($)')
                                ->numeric()
                                ->prefix('$')
                                ->placeholder(0)
                                ->default(0)
                                ->readOnly()
                                ->reactive(),
                            TextInput::make('discrepancy_reason')
                                ->label('Reason for Discrepancy')
                                ->hidden(fn($get) => (
                                    $get('skip_envelope_verification') ||
                                    $get('total_envelopes') == ($get('total_cash_coin') + $get('total_checks') + $get('total_other_donations'))
                                ))
                                ->required(),
                        ]),
                ])
                    ->columnSpanFull(),
            ]);
    }
    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
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
                TextColumn::make('total_bank_deposit')->label('Total Giving')->money('USD'),
            ])
            ->defaultSort('date', 'desc')
            ->actions([
                tableAction::make('delete')
                    ->requiresConfirmation()
                    ->action(fn(GivingCalculation $record) => $record->delete()),
                tableAction::make('pdf')
                    ->label('PDF')
                    ->color('success')
                    // ->icon('heroicon-s-download')
                    ->action(function (GivingCalculation $record) {
                        return response()->streamDownload(function () use ($record) {
                            echo Pdf::loadHtml(
                                Blade::render('pdf', ['record' => $record])
                            )->stream();
                        }, $record->name . '.pdf');
                    }),

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
        $set(
            'total_cash',
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
        $totalCoin = (floatval($get('denomination_penny')) ?? 0) * 0.01 +
            (floatval($get('denomination_nickel')) ?? 0) * 0.05 +
            (floatval($get('denomination_dime')) ?? 0) * 0.10 +
            (floatval($get('denomination_quarter')) ?? 0) * 0.25 +
            (floatval($get('denomination_half_dollar')) ?? 0) * 0.50 +
            (floatval($get('denomination_coin_dollar')) ?? 0) * 1;
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

    public static function setTotalAmount($set, $get)
    {
        $totalCash = $get('total_cash') ?? 0;
        $totalChecks = $get('total_checks') ?? 0;
        $totalCoin = $get('total_coin') ?? 0;
        $totalOther = $get('total_other_donations') ?? 0;
        $set('total_bank_deposit', $totalCash + $totalChecks + $totalCoin);
        $set('total_giving', $totalCash + $totalChecks + $totalCoin + $totalOther);
        $set('total_cash_coin', $totalCash + $totalCoin);
    }

    public static function setBankDeposit($set, $get)
    {
        $totalCash = $get('total_cash') ?? 0;
        $totalChecks = $get('total_checks') ?? 0;
        $totalCoin = $get('total_coin') ?? 0;
        $set('total_bank_deposit', $totalCash + $totalChecks + $totalCoin);
    }

    public static function getWidgets(): array
    {
        return [
            GivingWidget::class,
        ];
    }
}
