<?php

namespace App\Filament\Pages;

use App\Models\AppNotification;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Contracts\Support\Htmlable;

class AdminNotifications extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bell';
    protected static ?string $navigationLabel = 'Notifications';
    protected static ?int $navigationSort = 6;
    protected string $view = 'filament.pages.admin-notifications';

    public string $filter = 'all';

    public function getTitle(): string|Htmlable
    {
        return 'Admin Notifications';
    }

    

    public function getNotifications(): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = AppNotification::where('is_admin', true)
            ->latest();

        if ($this->filter === 'read') {
            $query->where('is_read', true);
        } elseif ($this->filter === 'unread') {
            $query->where('is_read', false);
        }

        return $query->paginate(20);
    }

    public function getUnreadCount(): int
    {
        return AppNotification::where('is_admin', true)
            ->where('is_read', false)
            ->count();
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
    }

    public function markRead(int $id): void
    {
        AppNotification::where('id', $id)
            ->where('is_admin', true)
            ->update(['is_read' => true]);
    }

    public function markAllRead(): void
    {
        AppNotification::where('is_admin', true)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function deleteNotification(int $id): void
    {
        AppNotification::where('id', $id)
            ->where('is_admin', true)
            ->delete();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('markAllRead')
                ->label('Mark All Read')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->action('markAllRead'),
        ];
    }
}