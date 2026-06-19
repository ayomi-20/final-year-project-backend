<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;

class Logout extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-left-on-rectangle';

    protected static ?string $navigationLabel = 'Logout';

    protected static ?int $navigationSort = 99;

    protected string $view = 'filament.pages.logout';

    public function mount(): void
    {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->redirect('/admin/login');
    }
}