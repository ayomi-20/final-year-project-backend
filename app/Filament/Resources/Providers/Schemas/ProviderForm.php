<?php

namespace App\Filament\Resources\Providers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProviderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('business_name')
                    ->required(),
                TextInput::make('business_type')
                    ->required(),
                TextInput::make('district')
                    ->required(),
                TextInput::make('address')
                    ->required(),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('logo')
                    ->default(null),
                TextInput::make('cover_photo')
                    ->default(null),
                Select::make('status')
                    ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'])
                    ->default('pending')
                    ->required(),
                Textarea::make('rejection_reason')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
