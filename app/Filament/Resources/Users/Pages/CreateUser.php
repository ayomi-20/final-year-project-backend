<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Mail\AdminCreated;
use App\Models\AppNotification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected string $plainPassword = '';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!empty($data['password'])) {
            $this->plainPassword = $data['password'];
        }
        return $data;
    }

    protected function afterCreate(): void
    {
        $user = $this->record;
        $isAdminRole = in_array($user->role, ['admin', 'super_admin']);

        if ($isAdminRole && !empty($this->plainPassword)) {
            try {
                Mail::to($user->email)->send(new AdminCreated(
                    admin: $user,
                    plainPassword: $this->plainPassword,
                    role: $user->role,
                ));
            } catch (\Exception $e) {
                Log::error('Admin creation email failed: ' . $e->getMessage());
            }

            AppNotification::create([
                'user_id'  => $user->id,
                'type'     => 'admin_account_created',
                'title'    => 'Welcome to the Admin Dashboard!',
                'message'  => 'Your ' . ucfirst(str_replace('_', ' ', $user->role)) . ' account has been created. '
                    . 'Log in to the admin dashboard <a href="' . config('app.url') . '/admin" style="color:#0F3B2E;font-weight:bold;">here</a>.',
                'is_admin' => false,
                'is_read'  => false,
            ]);
        }
    }
}