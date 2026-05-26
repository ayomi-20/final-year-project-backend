<?php

namespace App\Filament\Resources\Providers;

use App\Filament\Resources\Providers\Pages\CreateProvider;
use App\Filament\Resources\Providers\Pages\EditProvider;
use App\Filament\Resources\Providers\Pages\ListProviders;
use App\Models\Provider;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProviderResource extends Resource
{
    protected static ?string $model = Provider::class;
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')
                ->label('User Account')
                ->options(User::all()->pluck('email', 'id'))
                ->searchable()
                ->required(),

            TextInput::make('business_name')->required(),
            TextInput::make('business_type')->required(),
            TextInput::make('district')->required(),
            TextInput::make('address')->required(),

            Textarea::make('description')->rows(4)->nullable(),

            Select::make('status')
                ->options([
                    'pending'  => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ])
                ->default('pending')
                ->required(),

            Textarea::make('rejection_reason')
                ->rows(3)
                ->nullable()
                ->label('Rejection Reason (if rejected)'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('business_name')->searchable()->sortable(),
            TextColumn::make('business_type')->searchable(),
            TextColumn::make('district'),
            TextColumn::make('user.email')->label('Email'),
            TextColumn::make('status')->badge()
                ->color(fn (string $state): string => match ($state) {
                    'pending'  => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger',
                }),
            TextColumn::make('created_at')->dateTime()->sortable(),
        ])->filters([
            SelectFilter::make('status')
                ->options([
                    'pending'  => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ]),
        ])->actions([
            Action::make('approve')
                ->color('success')
                ->action(fn (Provider $record) => $record->update(['status' => 'approved']))
                ->requiresConfirmation()
                ->visible(fn (Provider $record) => $record->status === 'pending'),

            Action::make('reject')
                ->color('danger')
                ->action(fn (Provider $record) => $record->update(['status' => 'rejected']))
                ->requiresConfirmation()
                ->visible(fn (Provider $record) => $record->status === 'pending'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListProviders::route('/'),
            'create' => CreateProvider::route('/create'),
            'edit'   => EditProvider::route('/{record}/edit'),
        ];
    }
}