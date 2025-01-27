<?php

namespace App\Models;

use App\Models\Gift;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = ['name', 'type', 'goal_amount', 'description', 'start_date', 'end_date', 'created_by'];

    public function user() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function gifts() {
        return $this->hasMany(Gift::class);
    }

    public static function getForm()
    {
        return [
            TextInput::make('name')->required(),
            Select::make('type')
                ->options([
                    'Pastors Anniversary' => 'Pastors Anniversary',
                    'Church Anniversary' => 'Church Anniversary',
                    'General Stewardship' => 'General Stewardship',
                ])
                ->default('Pastors Anniversary')
                ->required(),
            TextInput::make('goal_amount')->required()->numeric()->minValue(0),
            Textarea::make('description')->required(),
            DatePicker::make('start_date')->required(),
            DatePicker::make('end_date')->required(),
            TextInput::make('created_by')->disabled()->default(auth()->id())->hidden(),
        ];
    }

}
