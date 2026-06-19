<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'tourists' => Tab::make('Tourists')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'tourist')),
            'providers' => Tab::make('Providers')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'provider')),
            'admins' => Tab::make('Admins')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('role', ['admin', 'super_admin'])),
            'revoked' => Tab::make('Revoked')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'revoked')),
        ];
    }
}