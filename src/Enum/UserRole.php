<?php

namespace App\Enum;

enum UserRole: string
{
    case USER = 'ROLE_USER';
    case ADMIN = 'ROLE_ADMIN';
    case API = 'ROLE_API';

    public static function isValidRole(string $role): bool
    {
        return null !== UserRole::tryFrom($role);
    }
}
