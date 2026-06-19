<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Provider;
use App\Models\Review;
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
            // 👥 Users summary (instead of splitting too much)
            Stat::make('Total Users', User::whereIn('role', [
                    'tourist', 'provider', 'admin', 'super_admin'
                ])->count())
                ->description('Tourists, providers & admins')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Tourists', User::where('role', 'tourist')->count())
                ->description('Registered travelers')
                ->descriptionIcon('heroicon-m-user')
                ->color('success'),

            // 🏢 Providers (merged pending + approved insight)
            Stat::make('Providers', Provider::count())
                ->description(
                    Provider::where('status', 'approved')->count() . ' approved · ' .
                    Provider::where('status', 'pending')->count() . ' pending'
                )
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('info'),

            // 🧭 Services (clean single stat)
            Stat::make('Active Services', Service::where('status', 'active')->count())
                ->description('Currently available services')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('success'),

            // 📦 Bookings (combined insight instead of separate cards)
            Stat::make('Total Bookings', Booking::count())
                ->description(
                    Booking::where('status', 'pending')->count() . ' pending · ' .
                    Booking::where('status', 'completed')->count() . ' completed'
                )
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('warning'),

            // ⭐ Reviews (kept because it adds real admin insight)
            Stat::make('Total Reviews', Review::count())
                ->description(
                    Review::where('is_hidden', true)->count() . ' hidden'
                )
                ->descriptionIcon('heroicon-m-star')
                ->color('gray'),
        ];
    }
}