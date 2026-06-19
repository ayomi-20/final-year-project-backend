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
use Filament\Forms\Components\FileUpload;
use App\Models\Category;
use BackedEnum;

class ProviderResource extends Resource
{
    protected static ?string $model = Provider::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';
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

            Select::make('business_type')
                ->label('Business Type')
                ->options(Category::all()->pluck('name', 'name'))
                ->searchable()
                ->required(),

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

            FileUpload::make('logo')
                ->label('Business Logo')
                ->image()
                ->disk('public')
                ->directory('provider-logos')
                ->nullable(),

            FileUpload::make('national_id')
                ->label('National ID')
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                ->disk('public')
                ->directory('provider-documents')
                ->nullable(),

            FileUpload::make('trading_license')
                ->label('Trading License / Business License')
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                ->disk('public')
                ->directory('provider-documents')
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('business_name')->searchable()->sortable(),
            TextColumn::make('business_type')->searchable(),
            TextColumn::make('district'),
            TextColumn::make('user.email')->label('Email'),
            TextColumn::make('national_id')
                ->label('National ID')
                ->formatStateUsing(fn ($state) => $state ? '✅ View' : '❌ Missing')
                ->color(fn ($state) => $state ? 'success' : 'danger')
                ->url(fn (Provider $record) => $record->national_id
                    ? asset('storage/' . $record->national_id)
                    : null)
                ->openUrlInNewTab(),
            TextColumn::make('trading_license')
                ->label('Trading License')
                ->formatStateUsing(fn ($state) => $state ? '✅ View' : '❌ Missing')
                ->color(fn ($state) => $state ? 'success' : 'danger')
                ->url(fn (Provider $record) => $record->trading_license
                    ? asset('storage/' . $record->trading_license)
                    : null)
                ->openUrlInNewTab(),
            TextColumn::make('logo')
                ->label('Logo')
                ->formatStateUsing(fn ($state) => $state ? '✅ View' : '❌ Missing')
                ->color(fn ($state) => $state ? 'success' : 'danger')
                ->url(fn (Provider $record) => $record->logo
                    ? asset('storage/' . $record->logo)
                    : null)
                ->openUrlInNewTab(),
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
                ->action(function (Provider $record) {
                    $record->update(['status' => 'approved']);
                    $record->user->update(['role' => 'provider']);

                    try {
                        \Illuminate\Support\Facades\Mail::to($record->user->email)
                            ->send(new \App\Mail\ProviderApproved($record));
                        \Illuminate\Support\Facades\Log::info('Approval email sent to: ' . $record->user->email);
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Approval email failed: ' . $e->getMessage());
                    }
                })
                ->requiresConfirmation()
                ->visible(fn (Provider $record) => $record->status !== 'approved'),

            Action::make('reject')
                ->color('danger')
                ->form([
                    \Filament\Forms\Components\Textarea::make('rejection_reason')
                        ->label('Reason for Rejection')
                        ->placeholder('Explain why this application is being rejected...')
                        ->required()
                        ->rows(4),
                ])
                ->action(function (Provider $record, array $data) {
                    $record->update([
                        'status'           => 'rejected',
                        'rejection_reason' => $data['rejection_reason'],
                    ]);
                    $record->user->update(['role' => 'tourist']);

                    try {
                        \Illuminate\Support\Facades\Mail::to($record->user->email)
                            ->send(new \App\Mail\ProviderRejected($record, $data['rejection_reason']));
                        \Illuminate\Support\Facades\Log::info('Rejection email sent to: ' . $record->user->email);
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Rejection email failed: ' . $e->getMessage());
                    }
                })
                ->requiresConfirmation()
                ->modalHeading('Reject Provider Application')
                ->modalDescription('Please provide a reason for rejection. This will be emailed to the applicant.')
                ->visible(fn (Provider $record) => $record->status !== 'rejected'),

            Action::make('set_pending')
                ->label('Set Pending')
                ->color('warning')
                ->action(function (Provider $record) {
                    $record->update(['status' => 'pending']);
                    $record->user->update(['role' => 'tourist']);
                })
                ->requiresConfirmation()
                ->modalHeading('Set Application Back to Pending')
                ->modalDescription('This will revert the provider status to pending and change the user role back to tourist.')
                ->visible(fn (Provider $record) => $record->status === 'approved'),
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