<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Provider;
use App\Models\Service;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Tourists', User::where('role', 'tourist')->count())
                ->description('Registered tourists on the platform')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Approved Providers', Provider::where('status', 'approved')->count())
                ->description(Provider::where('status', 'pending')->count() . ' pending approval')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('warning'),

            Stat::make('Active Services', Service::where('status', 'active')->count())
                ->description('Currently listed services')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('success'),

            Stat::make('Total Bookings', Booking::count())
                ->description(
                    Booking::where('status', 'pending')->count() . ' pending · ' .
                    Booking::where('status', 'completed')->count() . ' completed'
                )
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
        ];
    }
}