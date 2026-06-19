<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Mail\AdminCreated;
use App\Models\AppNotification;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Users';
    protected static ?int $navigationSort = 0;

    public static function form(Schema $schema): Schema
    {
        $authUser = auth()->user();
        $isSuperAdmin = $authUser?->role === 'super_admin';

        $roleOptions = [
            'tourist'  => 'Tourist',
            'provider' => 'Provider',
        ];

        if ($isSuperAdmin) {
            $roleOptions['admin']       = 'Admin';
            $roleOptions['super_admin'] = 'Super Admin';
        }

        return $schema->components([
            TextInput::make('first_name')->required(),
            TextInput::make('last_name')->required(),
            TextInput::make('email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true),
            TextInput::make('contact')->required(),
            TextInput::make('password')
                ->password()
                ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $operation) => $operation === 'create')
                ->label(fn (string $operation) => $operation === 'edit'
                    ? 'New Password (leave blank to keep current)'
                    : 'Password'),
            Select::make('role')
                ->options($roleOptions)
                ->default('tourist')
                ->required()
                ->disabled(fn (string $operation) =>
                    $operation === 'edit' && !$isSuperAdmin
                ),
        ]);
    }

    public static function table(Table $table): Table
    {
        $authUser = auth()->user();
        $isSuperAdmin = $authUser?->role === 'super_admin';

        return $table->columns([
            TextColumn::make('first_name')
                ->label('Name')
                ->formatStateUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                ->searchable(),
            TextColumn::make('email')->searchable(),
            TextColumn::make('contact'),
            TextColumn::make('role')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'super_admin' => 'danger',
                    'admin'       => 'warning',
                    'provider'    => 'info',
                    'tourist'     => 'success',
                    'revoked'     => 'gray',
                    default       => 'gray',
                }),
            TextColumn::make('created_at')->dateTime()->sortable(),
        ])
        ->filters([
            SelectFilter::make('role')
                ->options([
                    'tourist'     => 'Tourist',
                    'provider'    => 'Provider',
                    'admin'       => 'Admin',
                    'super_admin' => 'Super Admin',
                    'revoked'     => 'Revoked',
                ]),
        ])
        ->actions([
            Action::make('revoke')
                ->label('Revoke')
                ->color('warning')
                ->icon('heroicon-o-no-symbol')
                ->form([
                    \Filament\Forms\Components\Textarea::make('revocation_reason')
                        ->label('Reason for Revocation')
                        ->required()
                        ->rows(4),
                ])
                ->action(function (User $record, array $data) {
                    AppNotification::create([
                        'user_id'  => $record->id,
                        'type'     => 'account_revoked',
                        'title'    => 'Your account has been revoked',
                        'message'  => 'Your account has been revoked by an administrator. Reason: ' . $data['revocation_reason'],
                        'is_admin' => false,
                        'is_read'  => false,
                    ]);
                    $record->update(['role' => 'revoked']);
                })
                ->requiresConfirmation()
                ->modalHeading('Revoke User Account')
                ->modalDescription('The user will be notified with the reason. You can permanently delete afterwards.')
                ->visible(fn (User $record) =>
                    $isSuperAdmin &&
                    $record->role !== 'revoked' &&
                    $record->id !== auth()->id()
                ),

            Action::make('permanently_delete')
                ->label('Delete')
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading('Permanently Delete User')
                ->modalDescription('This cannot be undone. The user and all their data will be permanently removed.')
                ->action(fn (User $record) => $record->delete())
                ->visible(fn (User $record) =>
                    $isSuperAdmin &&
                    $record->id !== auth()->id()
                ),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit'   => EditUser::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
{
    return auth()->check() && auth()->user()->role === 'super_admin';
}

public static function shouldRegisterNavigation(): bool
{
    return true;
}

}