<?php

namespace App\Models;

use App\Models\Member;
use Filament\Forms\Set;
use App\Models\Campaign;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;

class Gift extends Model
{
    protected $fillable = ['amount', 'gifts', 'campaign_id', 'member_id', 'is_anonymous', 'payment_method', 'giver_name'];

    protected $casts = [
        'gifts' => 'array',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public static function getForm()
    {
        return  [
            Select::make('member_id')
                ->searchable()
                ->relationship('member', 'name')
                ->nullable()
                ->live()
                // ->preload()
                ->getSearchResultsUsing(
                    fn(string $search): array =>
                    Member::where('name', 'like', "%{$search}%")
                        ->orWhere('membership_number', 'like', "%{$search}%")
                        ->pluck('name', 'id')->toArray()
                )
                ->afterStateUpdated(function ($state, Set $set) {
                    $set('giver_name', Member::find($state)->name);
                })
                // ->getOptionLabelsUsing(fn(array $values): array => Member::whereIn('id', $values)->pluck('name', 'id')->toArray())
                ->createOptionForm(Member::getForm()),
            TextInput::make('giver_name')
                ->nullable(), // For non-members
            Repeater::make('gifts')
                ->schema([
                    Select::make('campaign_id')->relationship('campaign', 'name')->required(),
                    TextInput::make('amount')->numeric()->required(),
                    Select::make('payment_method')->options([
                        'credit_card' => 'Credit Card',
                        'cash' => 'Cash',
                        'check' => 'Check',
                        'Mobile' => 'Mobile',
                        'other' => 'Other',
                    ])->required(),
                ])->columnSpanFull(),
            TextInput::make('amount')->columnSpanFull()->readOnly()->default(0)->numeric()->required(),
        ];
    }
}
