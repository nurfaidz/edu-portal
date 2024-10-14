<?php

namespace App\Filament\Pages;

use App\Enums\Roles\Role;
use Filament\Pages\Auth\Login as AuthLogin;
use Illuminate\Database\Eloquent\Builder;

class Login extends AuthLogin
{
    /**
     * {@inheritDoc}
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'email' => $data['email'],
            'password' => $data['password'],
            fn (Builder $query) => $query->whereRelation('roles', function ($query) {
                return $query->whereIn('name', [Role::Superadmin->value, Role::Admin->value, Role::Lecturer->value]);
            }),
        ];
    }
}
