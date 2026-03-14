<?php

namespace App\Support;

final class EnterpriseAdminStatuses
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';

    public static function all(): array
    {
        return [
            self::ACTIVE,
            self::INACTIVE,
        ];
    }
}