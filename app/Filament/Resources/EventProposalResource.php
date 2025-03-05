<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\EventPlan;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\EventProposalResource\Pages;

class EventProposalResource extends Resource
{
    protected static ?string $model = EventPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Event Proposals';

    protected static ?string $recordTitleAttribute = 'event_name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Event Proposal')
                    ->description('Submit your event idea for review. We will follow up for more details if needed!')
                    ->schema([
                        Forms\Components\TextInput::make('event_name')
                            ->label('Event Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('event_date')
                            ->label('Proposed Event Date')
                            ->required()
                            ->minDate(now()->addDays(7))
                            ->helperText('Choose a date at least 7 days from today.'),
                        Forms\Components\Textarea::make('event_description')
                            ->label('Event Description')
                            ->required()
                            ->rows(4)
                            ->helperText('What’s the event about? Why should people attend? Who’s it for?'),
                        Forms\Components\TextInput::make('purpose_of_event')
                            ->label('Purpose of the Event')
                            ->required()
                            ->maxLength(255)
                            ->helperText('e.g., fundraiser, community gathering, workshop'),
                        Forms\Components\TextInput::make('proposer_name')
                            ->label('Your Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('proposer_email')
                            ->label('Your Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->helperText('We’ll use this to contact you.'),
                        Forms\Components\TextInput::make('proposer_phone')
                            ->label('Your Phone Number')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('expected_attendees')
                            ->label('Estimated Number of Attendees')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->helperText('Rough estimate is fine.'),
                        Forms\Components\CheckboxList::make('target_population')
                            ->label('Who is this event for?')
                            ->options([
                                'community' => 'Community',
                                'all_church' => 'All Church',
                                'children_youth' => 'Children/Youth',
                                'young_adults' => 'Young Adults',
                                'men_women' => 'Men/Women',
                                'seasoned_saints' => 'Seasoned Saints',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('target_population_other')
                            ->label('Other Target Audience')
                            ->maxLength(255),
                        Forms\Components\TimePicker::make('start_time')
                            ->label('Preferred Start Time')
                            ->required(),
                        Forms\Components\TimePicker::make('end_time')
                            ->label('Preferred End Time')
                            ->required()
                            ->after('start_time'),
                        Forms\Components\Toggle::make('is_registration_required')
                            ->label('Will attendees need to register in advance?')
                            ->onIcon('heroicon-s-check')
                            ->offIcon('heroicon-s-x-mark'),
                        Forms\Components\TextInput::make('cost_per_person')
                            ->label('Cost per Person (if any)')
                            ->numeric()
                            ->prefix('$')
                            ->helperText('Leave blank if free.'),
                    ]),
                Forms\Components\Section::make('Guest Speakers and Musicians')
                    ->description('Suggest any speakers or musicians you’d like to feature.')
                    ->schema([
                        Forms\Components\Repeater::make('guest_speakers_musicians')
                            ->label('Potential Speakers or Musicians')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Select::make('type')
                                    ->label('Type')
                                    ->options([
                                        'speaker' => 'Speaker',
                                        'musician' => 'Musician',
                                        'other' => 'Other',
                                    ])
                                    ->required(),
                                Forms\Components\Textarea::make('details')
                                    ->label('Details')
                                    ->rows(2)
                                    ->maxLength(500)
                                    ->helperText('e.g., affiliation, why they’re a good fit.'),
                            ])
                            ->addActionLabel('Add Another Speaker/Musician')
                            ->maxItems(5)
                            ->helperText('Optional: Add up to 5 potential speakers or musicians.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event_name')
                    ->label('Event Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('event_date')
                    ->label('Proposed Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('proposer_name')
                    ->label('Proposer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('proposer_email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('expected_attendees')
                    ->label('Est. Attendees')
                    ->numeric(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'approved' => 'success',
                        'denied' => 'danger', // Added for denied
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted On')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'denied' => 'Denied', // Added
                    ]),
                Tables\Filters\Filter::make('event_date')
                    ->form([
                        Forms\Components\DatePicker::make('event_date_from'),
                        Forms\Components\DatePicker::make('event_date_to'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['event_date_from'], fn($q, $date) => $q->whereDate('event_date', '>=', $date))
                            ->when($data['event_date_to'], fn($q, $date) => $q->whereDate('event_date', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('convert')
                    ->label('Approve and Plan')
                    ->action(function (EventPlan $record) {
                        $record->update(['status' => 'approved']);
                        return redirect()->route('filament.resources.event-plannings.edit', $record);
                    })
                    ->requiresConfirmation()
                    ->visible(fn(EventPlan $record) => $record->status === 'pending'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEventProposals::route('/'),
            'create' => Pages\CreateEventProposal::route('/create'), // Optional for admin use
            'edit' => Pages\EditEventProposal::route('/{record}/edit'),
        ];
    }
}
