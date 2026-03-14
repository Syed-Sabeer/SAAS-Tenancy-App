<?php

namespace App\Support;

final class TenantUserRoles
{
    const COMPANY_ADMIN = 'company_admin';
    const COMPANY_USER = 'company_user';

    public static function all(): array
    {
        return [
            self::COMPANY_ADMIN,
            self::COMPANY_USER,
        ];
    }
}