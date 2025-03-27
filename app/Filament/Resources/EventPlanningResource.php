<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\EventPlan;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\EventPlanningResource\Pages;

class EventPlanningResource extends Resource
{
    protected static ?string $model = EventPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Event Planning';

    protected static ?string $recordTitleAttribute = 'event_name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Snapshot')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\DatePicker::make('event_date')
                            ->label('Event Date')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, Set $set) => $set('end_date', $state))
                            ->minDate(now()->addDays(15))
                            ->maxDate(now()->addYear())
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->minDate(now()->addDays(15))
                            ->maxDate(now()->addYear())
                            ->label('End Date'),
                        Forms\Components\TextInput::make('event_name')
                            ->label('Event Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('purpose_of_event')
                            ->label('Purpose of Event')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('event_coordinators')
                            ->label('Event Coordinator(s)')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('monetary_goal')
                            ->label('Monetary Goal')
                            ->helperText('All funds collected are to be turned in to the assigned trustee at the completion of the event.')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\TextInput::make('budget')
                            ->label('Budget')
                            ->helperText('Your goal should include a $100 fee for use of the facility. If used, an additional $50 is required for the kitchen.')
                            ->required()
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\TextInput::make('auxiliary_tribe_lead')
                            ->label('Auxiliary/Tribe Lead')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('assigned_trustee')
                            ->label('Assigned Trustee')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('vision_support')
                            ->label('How does this event support our vision?')
                            ->columnSpanFull()
                            ->rows(3)
                            ->required(),
                        Forms\Components\CheckboxList::make('main_purpose')
                            ->label('What is the main purpose?')
                            ->options([
                                'gather' => 'Gather',
                                'connect' => 'Connect',
                                'serve' => 'Serve',
                                'grow' => 'Grow',
                                'outreach' => 'Outreach',
                            ])
                            ->required(),
                        Forms\Components\CheckboxList::make('target_population')
                            ->label('Target Population?')
                            ->options([
                                'community' => 'Community',
                                'all_church' => 'All Church',
                                'children_youth' => 'Children/Youth',
                                'young_adults' => 'Young Adults',
                                'men_women' => 'Men/Women',
                                'seasoned_saints' => 'Seasoned Saints',
                            ]),
                        Forms\Components\TextInput::make('target_population_other')
                            ->label('Other Target Population')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('event_description')
                            ->label('Description of Event (can be used in all publicity)')
                            ->helperText('What will you be doing? Why should people come? Who should come?')
                            ->rows(6)
                            ->required(),
                        Repeater::make('guest_speakers_musicians')
                            ->label('Guest Speakers and Musicians')
                            ->columnSpanFull()
                            ->schema([
                                Forms\Components\TextInput::make('Name'),
                                Forms\Components\Select::make('Type')
                                    ->options([
                                        'speaker' => 'Speaker',
                                        'musician' => 'Musician',
                                        'other' => 'Other'
                                    ]),
                                Forms\Components\Checkbox::make('honorarium_required')
                                    ->live(),
                                Forms\Components\TextInput::make('honorarium')
                                    ->visible(fn(Get $get) => $get('honorarium_required'))
                                    ->label('Amount/Fee Agreed Upon')
                                    ->numeric()
                                    ->prefix('$'),

                            ])
                            ->columns(2)
                    ]),

                Forms\Components\Section::make('The Basics')
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('expected_attendees')
                            ->label('Approximate number of people expected to attend/participate')
                            ->required()
                            ->numeric(),
                        Forms\Components\TimePicker::make('setup_time')
                            ->label('Setup Time')
                            ->required(),
                        Forms\Components\TimePicker::make('start_time')
                            ->label('Start Time')
                            ->required(),
                        Forms\Components\TimePicker::make('end_time')
                            ->label('End Time')
                            ->required(),
                        Forms\Components\TimePicker::make('tear_down_time')
                            ->label('Tear Down Time')
                            ->required(),
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('onsite_rooms')
                                    ->label('On site, preferred rooms'),
                                Forms\Components\TextInput::make('offsite_location')
                                    ->label('Off site, location'),
                            ]),
                        Forms\Components\Toggle::make('is_registration_required')
                            ->label('Is advance registration required?')
                            ->onIcon('heroicon-s-check')
                            ->offIcon('heroicon-s-x-mark'),
                        Forms\Components\DatePicker::make('registration_start_date')
                            ->label('Registration Start Date')
                            ->visible(fn(Get $get) => $get('is_registration_required')),
                        Forms\Components\DatePicker::make('registration_deadline')
                            ->label('Deadline to Register')
                            ->visible(fn(Get $get) => $get('is_registration_required')),
                        Forms\Components\Toggle::make('is_participation_limited')
                            ->label('Is participation limited?')
                            ->onIcon('heroicon-s-check')
                            ->offIcon('heroicon-s-x-mark'),
                        Forms\Components\TextInput::make('max_registrations')
                            ->label('How many may register?')
                            ->numeric()
                            ->visible(fn(Get $get) => $get('is_participation_limited')),
                        Forms\Components\TextInput::make('cost_per_person')
                            ->label('Cost per person')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\DatePicker::make('payment_deadline')
                            ->label('Deadline for payment'),
                        Forms\Components\CheckboxList::make('registration_methods')
                            ->label('Registration Methods')
                            ->options([
                                'email' => 'Email (less than 50 expected)',
                                'online_form' => 'Online form (more than 50 or payment/fees involved)',
                                'lower_level' => 'Lower Level - dates',
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('registration_period_start')
                                    ->label('Registration Start Date'),
                                Forms\Components\DatePicker::make('registration_period_end')
                                    ->label('Registration End Date'),
                            ]),
                        Forms\Components\Toggle::make('release_forms_needed')
                            ->label('Are release or permission forms needed?')
                            ->onIcon('heroicon-s-check')
                            ->offIcon('heroicon-s-x-mark'),
                    ]),

                Forms\Components\Section::make('Event Elements & Details')
                    ->schema([
                        Forms\Components\CheckboxList::make('major_elements')
                            ->label('Check all that apply')
                            ->options([
                                'invitations' => 'Invitations',
                                'ticket_sales' => 'Ticket Sales',
                                'decorations' => 'Decorations',
                                'transportation' => 'Transportation',
                                'lodging' => 'Lodging',
                                'venue_reservation' => 'Venue Reservation and Contract (events at other locations)',
                                'partnership_agreements' => 'Partnership Agreements',
                                'online_reg_form' => 'Online Registration Form (complex with 50 or more expected or to collect money)',
                                'technology' => 'Technology',
                                'honoraria_fees' => 'Honoraria/Fees',
                                'food' => 'Food',
                                'pastoral_approval' => 'Pastoral Approval – submit list to MPM prior to contact',
                                'solicit_donations' => 'Solicit in-kind Donations (List of organizations)',
                                'special_speaker' => 'Special Speaker(s) (Name, affiliation, brief bio)',
                                'guest_musician' => 'Guest Musician (Name, affiliation, brief bio)',
                            ])
                            ->columns(2),
                        Forms\Components\TextInput::make('major_elements_other')
                            ->label('Other Major Elements')
                            ->maxLength(255),
                    ]),

                Forms\Components\Section::make('Facility Resource Needs')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('facility_contact')
                            ->label('Point of Contact for Facility')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('facility_contact_means')
                            ->label('Preferred Means of Contact')
                            ->maxLength(255),
                        Forms\Components\CheckboxList::make('facility_needs')
                            ->label('Check all that apply')
                            ->options([
                                'tables_chairs' => 'Tables/chairs',
                                'podium' => 'Podium',
                                'willkie_talkies' => 'Willkie Talkies',
                                'safety_vests' => 'Safety Vests',
                                'collection_bins' => 'Collection Bins',
                                'church_vans' => 'Church Vans',
                                'short_term_storage' => 'Short term storage (Room or refrigerator/freezer)',
                                'sort_package' => 'Space to sort, package, distribute',
                            ])
                            ->columns(2),
                        Forms\Components\Textarea::make('short_term_storage_items')
                            ->label('Short Term Storage Items')
                            ->rows(4)
                            ->visible(fn(Get $get) => in_array('short_term_storage', $get('facility_needs') ?? [])),
                        Forms\Components\Textarea::make('items_to_sort')
                            ->label('Items to Sort')
                            ->rows(4)
                            ->visible(fn(Get $get) => in_array('sort_package', $get('facility_needs') ?? [])),
                    ]),

                Forms\Components\Section::make('Comestible Needs')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('comestible_contact')
                            ->label('Point of Contact for Comestible')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('comestible_contact_means')
                            ->label('Preferred Means of Contact')
                            ->maxLength(255),
                        Forms\Components\CheckboxList::make('comestible_needs')
                            ->label('Check all that apply')
                            ->options([
                                'in_house_caterer' => 'In House Caterer',
                                'external_caterer' => 'External Caterer (Must be certified and meet with Kitchen staff)',
                                'napkins' => 'Napkins',
                                'cups' => 'Cups',
                                'forks_knives' => 'Forks/Knives',
                                'ice' => 'Ice',
                                'bottled_water' => 'Bottled water',
                            ])
                            ->columns(2),
                        Forms\Components\TextInput::make('comestible_other')
                            ->label('Other Comestible Needs')
                            ->maxLength(255),
                    ]),

                Forms\Components\Section::make('Technology Needs')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('technology_contact')
                            ->label('Point of Contact for Technology')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('technology_contact_means')
                            ->label('Preferred Means of Contact')
                            ->maxLength(255),
                        Forms\Components\CheckboxList::make('technology_needs')
                            ->label('Check all that apply')
                            ->options([
                                'powerpoint' => 'Will a power point presentation be shown?',
                                'dvd_cd' => 'Will a DVD or CD be played?',
                                'microphone' => 'Microphone',
                                'music_video' => 'Is music or video being played?',
                                'service_recorded' => 'Will the service be recorded?',
                            ]),
                        Forms\Components\CheckboxList::make('recording_type')
                            ->label('Recording Type')
                            ->options([
                                'audio' => 'Audio',
                                'video' => 'Video',
                            ])
                            ->visible(fn(Get $get) => in_array('service_recorded', $get('technology_needs') ?? [])),
                    ]),

                Forms\Components\Section::make('Administrative Support')
                    ->schema([
                        Forms\Components\TextInput::make('admin_contact')
                            ->label('Point of Contact for Administrative Tasks')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('admin_contact_means')
                            ->label('Preferred Means of Contact')
                            ->maxLength(255),
                        Forms\Components\CheckboxList::make('admin_needs')
                            ->label('Check all that apply')
                            ->options([
                                'online_reg_form_admin' => 'Online Registration Form',
                                'photocopy_print' => 'Photocopy/Print',
                                'facility_transport' => 'Facility Transport/Pickup of Items',
                                'office_supplies' => 'Office Supplies',
                                'nametags' => 'Nametags',
                                'pens' => 'Pens',
                                'pads' => 'Pads',
                                'labels' => 'Labels',
                                'folders' => 'Folders',
                                'copyright_clearance' => 'Copyright clearance (Show a movie, play music or video)',
                            ])
                            ->columns(2),
                        Forms\Components\TextInput::make('admin_other')
                            ->label('Other Administrative Needs')
                            ->maxLength(255),
                    ]),

                Forms\Components\Section::make('Personnel Needs')
                    ->schema([
                        Forms\Components\TextInput::make('personnel_contact')
                            ->label('Point of Contact for Personnel')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('personnel_contact_means')
                            ->label('Preferred Means of Contact')
                            ->maxLength(255),
                        Forms\Components\CheckboxList::make('personnel_needs')
                            ->label('Check all that apply')
                            ->options([
                                'volunteers' => 'Volunteers',
                                'childcare' => 'Childcare',
                                'ushers' => 'Ushers',
                                'greeters' => 'Greeters',
                                'security' => 'Security (ministry or police officers)',
                                'parking_attendants' => 'Parking Attendants',
                                'van_driver' => 'Approved Church Van Driver',
                                'health_wellness' => 'Health and Wellness (medical staff)',
                            ])
                            ->columns(2),
                        Forms\Components\TextInput::make('personnel_other')
                            ->label('Other Personnel Needs')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('volunteers_details')
                            ->label('Volunteers')
                            ->rows(6)
                            ->visible(fn(Get $get) => in_array('volunteers', $get('personnel_needs') ?? [])),
                    ]),

                Forms\Components\Section::make('Financial Needs')
                    ->schema([
                        Forms\Components\TextInput::make('financial_contact')
                            ->label('Point of Contact for Finances/Budget Management')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('financial_contact_means')
                            ->label('Preferred Means of Contact')
                            ->maxLength(255),
                        Forms\Components\CheckboxList::make('financial_needs')
                            ->label('Check all that apply')
                            ->options([
                                'rent_supplies' => 'Rent Supplies',
                                'contract_agreement' => 'Contract or Agreement needs to be signed',
                                'solicit_donations_fin' => 'Solicit donations',
                                'honorarium' => 'Will an honorarium be given out?',
                                'plaque' => 'Will a plaque be ordered',
                                'leader_guides' => 'Will leader/facilitator guides be ordered',
                            ]),
                        Forms\Components\Textarea::make('rent_supplies_details')
                            ->label('Rent Supplies Details')
                            ->rows(2)
                            ->visible(fn(Get $get) => in_array('rent_supplies', $get('financial_needs') ?? [])),
                    ]),

                Forms\Components\Section::make('Checklist')
                    ->schema([
                        Forms\Components\DatePicker::make('checklist_event_date')
                            ->label('Event Date'),
                        Forms\Components\DatePicker::make('checklist_registration_start')
                            ->label('Registration Start Date'),
                        Forms\Components\DatePicker::make('checklist_publicity_start')
                            ->label('Publicity Start Date'),

                        Forms\Components\Section::make('Event Planning')
                            ->schema([
                                Forms\Components\Checkbox::make('form_completed')
                                    ->label('Has Event Planner Form Been Completed?'),
                                Forms\Components\Checkbox::make('form_submitted')
                                    ->label('Has Event Planner Form Been Submitted?'),
                                Forms\Components\Checkbox::make('event_approved')
                                    ->label('Has Event Been Approved?'),
                                Forms\Components\Checkbox::make('planning_meeting_scheduled')
                                    ->label('Has Event Planning Meeting been scheduled?'),
                                Forms\Components\Checkbox::make('planning_meeting_completed')
                                    ->label('Has Event Planning Meeting been completed?'),
                            ])
                            ->columns(2),

                        Forms\Components\Section::make('Schedule Church Resources')
                            ->schema([
                                Forms\Components\Checkbox::make('facility_forms_submitted')
                                    ->label('Submit Facility Use & Resources Forms'),
                            ]),

                        Forms\Components\Section::make('Public Relations')
                            ->schema([
                                Forms\Components\Checkbox::make('persuasive_verbiage_created')
                                    ->label('Create Persuasive Verbiage'),
                                Forms\Components\Checkbox::make('monitor_slide_created')
                                    ->label('Create Monitor Slide'),
                                Forms\Components\Checkbox::make('community_flyer_created')
                                    ->label('Create Flyer for Community'),
                                Forms\Components\Checkbox::make('advertising_request_submitted')
                                    ->label('Submit Request to Advertise'),
                            ])
                            ->columns(2),

                        Forms\Components\Section::make('Finances')
                            ->schema([
                                Forms\Components\Checkbox::make('contracts_negotiated')
                                    ->label('Negotiate Contract/Agreements, Get Vendor Quotes or Invoices'),
                                Forms\Components\Checkbox::make('contract_submitted')
                                    ->label('Submit Contract/Agreement for Review & Signature'),
                                Forms\Components\Checkbox::make('fund_requests_submitted')
                                    ->label('Submit Fund Requests for church to procure/order items'),
                                Forms\Components\Checkbox::make('caterer_fund_request')
                                    ->label('Submit Caterer fund request'),
                                Forms\Components\Checkbox::make('vendor_payment_request')
                                    ->label('Submit Fund Request to pay Vendors'),
                            ])
                            ->columns(2),

                        Forms\Components\Section::make('Secure Additional Staff')
                            ->schema([
                                Forms\Components\Checkbox::make('security_secured')
                                    ->label('Security/Parking Attendants'),
                                Forms\Components\Checkbox::make('hospitality_secured')
                                    ->label('Hospitality'),
                                Forms\Components\Checkbox::make('ushers_secured')
                                    ->label('Ushers/Greeters'),
                                Forms\Components\Checkbox::make('comestible_secured')
                                    ->label('Comestible'),
                                Forms\Components\Checkbox::make('media_secured')
                                    ->label('Media'),
                                Forms\Components\Checkbox::make('pr_secured')
                                    ->label('Public Relations'),
                                Forms\Components\Checkbox::make('choir_secured')
                                    ->label('Choir'),
                                Forms\Components\Checkbox::make('volunteers_secured')
                                    ->label('Volunteers'),
                                Forms\Components\Checkbox::make('childcare_secured')
                                    ->label('Childcare'),
                            ])
                            ->columns(3),

                        Forms\Components\Section::make('Other')
                            ->schema([
                                Forms\Components\Checkbox::make('mc_contacted')
                                    ->label('Master/Mistress of Ceremony Contacted'),
                                Forms\Components\Checkbox::make('backup_mc_contacted')
                                    ->label('(Back-Up) Master/Mistress of Ceremony Contacted'),
                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'approved' => 'Approved',
                                        'denied' => 'denied',
                                    ]),
                            ])
                            ->columns(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event_date')
                    ->label('Event Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('purpose_of_event')
                    ->label('Purpose')
                    ->searchable(),
                Tables\Columns\TextColumn::make('event_coordinators')
                    ->label('Coordinator(s)')
                    ->searchable(),
                Tables\Columns\TextColumn::make('expected_attendees')
                    ->label('Expected Attendees')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('budget')
                    ->label('Budget')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\IconColumn::make('event_approved')
                    ->label('Approved')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('main_purpose')
                    ->multiple()
                    ->options([
                        'gather' => 'Gather',
                        'connect' => 'Connect',
                        'serve' => 'Serve',
                        'grow' => 'Grow',
                        'outreach' => 'Outreach',
                    ]),
                Tables\Filters\Filter::make('event_date')
                    ->form([
                        Forms\Components\DatePicker::make('event_date_from'),
                        Forms\Components\DatePicker::make('event_date_to'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['event_date_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('event_date', '>=', $date),
                            )
                            ->when(
                                $data['event_date_to'],
                                fn(Builder $query, $date): Builder => $query->whereDate('event_date', '<=', $date),
                            );
                    }),
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->attribute('event_approved')
                    ->label('Approval Status')
                    ->queries(
                        true: fn(Builder $query): Builder => $query->where('event_approved', true),
                        false: fn(Builder $query): Builder => $query->where('event_approved', false),
                        blank: fn(Builder $query): Builder => $query
                    )
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEventPlannings::route('/'),
            'create' => Pages\CreateEventPlanning::route('/create'),
            // 'view' => Pages\ViewEventPlanning::route('/{record}'),
            'edit' => Pages\EditEventPlanning::route('/{record}/edit'),
        ];
    }
}
