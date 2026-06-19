<?php

namespace App\Filament\Resources\Reviews;

use App\Filament\Resources\Reviews\Pages\EditReview;
use App\Filament\Resources\Reviews\Pages\ListReviews;
use App\Models\Review;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use BackedEnum;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Textarea::make('comment')
                ->rows(4)
                ->disabled(),

            Toggle::make('is_hidden')
                ->label('Hide from public')
                ->default(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('tourist.first_name')
                ->label('Tourist')
                ->formatStateUsing(fn ($record) =>
                    $record->tourist->first_name . ' ' . $record->tourist->last_name)
                ->searchable(),

            TextColumn::make('service.title')
                ->label('Service')
                ->searchable(),

            TextColumn::make('rating')
                ->badge()
                ->color(fn (int $state): string => match (true) {
                    $state >= 4 => 'success',
                    $state == 3 => 'warning',
                    default     => 'danger',
                })
                ->formatStateUsing(fn (int $state) => str_repeat('⭐', $state)),

            TextColumn::make('comment')
                ->limit(50)
                ->wrap(),

            ToggleColumn::make('is_hidden')
                ->label('Hidden'),

            TextColumn::make('created_at')
                ->dateTime()
                ->sortable(),
        ])
        ->filters([
            SelectFilter::make('rating')
                ->options([
                    5 => '5 Stars',
                    4 => '4 Stars',
                    3 => '3 Stars',
                    2 => '2 Stars',
                    1 => '1 Star',
                ]),
        ])
        ->actions([
            Action::make('delete')
                ->label('Delete')
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->action(fn (Review $record) => $record->delete()),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReviews::route('/'),
            'edit'  => EditReview::route('/{record}/edit'),
        ];
    }
}