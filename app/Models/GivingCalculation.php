<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GivingCalculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'name',
        'activity_plan_id',
        'campaign_id',
        'denominations', // JSON field for cash denominations
        'coins',         // JSON field for coin denominations
        'checks',        // JSON field for checks
        'counters',      // JSON field for counters
        'other_donations', // JSON field for other donations
        'total_cash',    // Total cash value
        'total_coin',    // Total coin value
        'total_cash_coin', // Total cash + coin value
        'total_checks',  // Total checks value
        'total_giving',  // Total giving (cash + coin + checks)
        'total_bank_deposit', // Total bank deposit,
        'denomination_1',
        'denomination_5',
        'denomination_10',
        'denomination_20',
        'denomination_50',
        'denomination_100',
        'denomination_penny',
        'denomination_nickel',
        'denomination_dime',
        'denomination_quarter',
        'denomination_half_dollar',
        'denomination_coin_dollar',
        
    ];

    protected $casts = [
        'denominations' => 'array',
        'coins' => 'array',
        'checks' => 'array',
        'counters' => 'array',
        'other_donations' => 'array',
        'total_cash' => 'float',
        'total_coin' => 'float',
        'total_cash_coin' => 'float',
        'total_checks' => 'float',
        'total_giving' => 'float',
        'total_bank_deposit' => 'float',
    ];

    public function activityPlan()
    {
        return $this->belongsTo(ActivityPlan::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
