<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityPlanResource\Pages;
use App\Filament\Resources\ActivityPlanResource\Pages\CreateActivityPlan;
use App\Filament\Resources\ActivityPlanResource\Pages\EditActivityPlan;
use App\Filament\Resources\ActivityPlanResource\Pages\ListActivityPlans;
use App\Models\ActivityPlan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;


class ActivityPlanResource extends Resource
{
    protected static ?string $model = ActivityPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Event Details')
                    ->schema([
                        TextInput::make('event_name')->required(),
                        Select::make('type')
                            ->options([
                                'service' => 'Service',
                                'meeting' => 'Meeting',
                                'event' => 'Event',
                            ])
                            ->default('service')
                            ->required(),
                        Textarea::make('description')->columnSpanFull(),
                        TextInput::make('budget')->numeric()->prefix('$'),
                        DatePicker::make('event_date')->required(),
                        TimePicker::make('start_time')->required(),
                        TimePicker::make('end_time')->required(),
                    ])->columns(2),
                Fieldset::make('Location Details')
                    ->schema([
                        Select::make('location')
                            ->options([
                                'Windsor Campus' => 'Windsor Campus',
                                'Lewiston-Woodville Campus' => 'Lewiston-Woodville Campus',
                            ])->required(),
                        Repeater::make('rooms')
                        ->helperText('I.E. Sanctuary, Kitchen, etc. Bathrooms are a given.')
                        ->label('Room(s) Requested')
                        ->schema([
                            TextInput::make('name')->required(),
                        ]),
                    ])->columns(1),
                // Textarea::make('resources'),
                Fieldset::make('Required Resources')
                    ->schema([
                Repeater::make('resources')
                    ->schema([
                        TextInput::make('name')->required(),
                        Select::make('role')
                            ->options([
                                'member' => 'Member',
                                'administrator' => 'Administrator',
                                'trustee' => 'Trustee',
                                'security' => 'Security',
                                'media' => 'Media',
                                'other' => 'Other',
                            ])
                            ->required(),
                    ])
                    ->columns(2),
                ])->columns(1),
                Section::make('Additional Information')
                    ->schema([
                        Textarea::make('special_notes'),
                        Hidden::make('user_id')->default(auth()->id()),
                        Select::make('member_id')
                            ->relationship('member', 'name')
                            ->default(auth()->id()),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event_name')->searchable(),
                TextColumn::make('event_date')->date(),
                TextColumn::make('location'),
                TextColumn::make('user.name')->label('Organizer'),
                TextColumn::make('member.name')->label('Member'),
            ])
            ->filters([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityPlans::route('/'),
            'create' => Pages\CreateActivityPlan::route('/create'),
            'edit' => Pages\EditActivityPlan::route('/{record}/edit'),
        ];
    }
}
