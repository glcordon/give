<?php

namespace App\Models;

use App\Models\Gift;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = ['name', 'phone', 'address', 'status', 'membership_number'];

    public function gifts()
    {
        return $this->hasMany(Gift::class);
    }

    public static function getForm()
    {
        return  [
            TextInput::make('name')->required(),
            TextInput::make('phone')->required(),
            TextInput::make('address')->required(),
            Select::make('status')->options([
                'active' => 'Active',
                'inactive' => 'Inactive',
            ])->required(),
            TextInput::make('membership_number')->required(),
        ];
    }
    public static function assignRandomNumber($id)
    {
        $rand = mt_rand(1000, 9999);
        if (!Self::whereMembershipNumber($rand)->count()) {
            Self::find($id)->update(['membership_number' => $rand]);
        } else {
            Self::assignRandomNumber($id);
        }
    }
}
