<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationResource\Pages;
use App\Filament\Resources\NotificationResource\Pages\CreateNotification;
use App\Filament\Resources\NotificationResource\Pages\EditNotification;
use App\Filament\Resources\NotificationResource\Pages\ListNotifications;
use App\Models\Notification;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NotificationResource extends Resource
{
    protected static ?string $model = Notification::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('type')
                    ->options([
                        'sms' => 'SMS',
                        'email' => 'Email',
                    ])
                    ->required(),
                TextInput::make('recipient')->required(),
                TextArea::make('message')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')->sortable(),
                TextColumn::make('recipient'),
                TextColumn::make('message')->limit(50),
                TextColumn::make('sent'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotifications::route('/'),
            'create' => Pages\CreateNotification::route('/create'),
            'edit' => Pages\EditNotification::route('/{record}/edit'),
        ];
    }
}
