<?php

namespace App\Models;

use App\Models\User;
use App\Models\Member;
use Filament\Forms\Get;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TimePicker;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityPlan extends Model
{
    use HasFactory;

    protected $casts = [
        'rooms' => 'array',
        'resources' => 'array',
        'expenses' => 'array',
    ];

    protected $fillable = [
        'event_name',
        'description',
        'event_date',
        'start_time',
        'end_time',
        'location',
        'rooms',
        'resources',
        'budget',
        'special_notes',
        'user_id',
        'member_id',
        'total_raised',
        'expenses',
        'total_expenses'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public static function getForm()
    {
        return [
            Fieldset::make('Event Details')
                ->schema([
                    TextInput::make('event_name')->required(),
                    Select::make('type')
                        ->native()
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
                    DatePicker::make('end_date'),
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
                    // Select::make('member_id')
                    //     ->relationship('member', 'name')
                    //     ->hidden(fn(): bool => ! auth()->id())
                    //     ->default(auth()->id()),
                ])->columns(1),
        ];
    }
}
