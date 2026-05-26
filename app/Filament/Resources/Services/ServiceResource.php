<?php

namespace App\Filament\Resources\Services;

use App\Filament\Resources\Services\Pages\CreateService;
use App\Filament\Resources\Services\Pages\EditService;
use App\Filament\Resources\Services\Pages\ListServices;
use App\Models\Category;
use App\Models\Provider;
use App\Models\Region;
use App\Models\Service;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('provider_id')
                ->label('Provider')
                ->options(Provider::all()->pluck('business_name', 'id'))
                ->searchable()
                ->required(),

            Select::make('category_id')
                ->label('Category')
                ->options(Category::all()->pluck('name', 'id'))
                ->searchable()
                ->required(),

            Select::make('region_id')
                ->label('Region')
                ->options(Region::all()->pluck('name', 'id'))
                ->searchable()
                ->required(),

            TextInput::make('title')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, callable $set) =>
                    $set('slug', Str::slug($state))),

            TextInput::make('slug')
                ->required()
                ->unique(ignoreRecord: true),

            Textarea::make('description')
                ->nullable()
                ->rows(4),

            TextInput::make('price')
                ->numeric()
                ->prefix('UGX')
                ->required(),

            Select::make('status')
                ->options([
                    'active'    => 'Active',
                    'inactive'  => 'Inactive',
                    'suspended' => 'Suspended',
                ])
                ->default('active')
                ->required(),

            Toggle::make('is_featured')
                ->label('Featured')
                ->default(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable()->sortable(),
            TextColumn::make('provider.business_name')->label('Provider')->searchable(),
            TextColumn::make('category.name')->label('Category'),
            TextColumn::make('region.name')->label('Region'),
            TextColumn::make('price')->money('UGX')->sortable(),
            TextColumn::make('status')->badge()
                ->color(fn (string $state): string => match ($state) {
                    'active'    => 'success',
                    'inactive'  => 'warning',
                    'suspended' => 'danger',
                }),
            ToggleColumn::make('is_featured')->label('Featured'),
            TextColumn::make('rating_avg')->label('Rating')->sortable(),
        ])->filters([
            SelectFilter::make('status')
                ->options([
                    'active'    => 'Active',
                    'inactive'  => 'Inactive',
                    'suspended' => 'Suspended',
                ]),
            SelectFilter::make('category_id')
                ->label('Category')
                ->options(Category::all()->pluck('name', 'id')),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListServices::route('/'),
            'create' => CreateService::route('/create'),
            'edit'   => EditService::route('/{record}/edit'),
        ];
    }
}