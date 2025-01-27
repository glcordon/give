<?php

namespace App\Models;

use App\Models\Campaign;
use App\Models\Member;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    protected $fillable = ['amount', 'campaign_id', 'member_id', 'is_anonymous', 'payment_method', 'giver_name'];

    public function campaign() {
        return $this->belongsTo(Campaign::class);
    }

    public function member() {
        return $this->belongsTo(Member::class);
    }

    public static function getForm()
    {
      return  [
            TextInput::make('amount')->numeric()->required(),
            Select::make('campaign_id')->relationship('campaign', 'name')->required(),
            Select::make('member_id')
                ->searchable()
                ->relationship('member', 'name')->nullable()
                ->createOptionForm(Member::getForm()),
            TextInput::make('giver_name')->nullable(), // For non-members
            Select::make('payment_method')->options([
                'credit_card' => 'Credit Card',
                'cash' => 'Cash',
                'check' => 'Check',
                'Mobile' => 'Mobile',
                'other' => 'Other',
            ])->required(),
        ];
    }
}
