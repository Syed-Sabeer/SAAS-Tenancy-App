<?php

namespace App\Support;

final class CompanyStatuses
{
    const DRAFT = 'draft';
    const PROVISIONING = 'provisioning';
    const ACTIVE = 'active';
    const SUSPENDED = 'suspended';
    const CANCELLED = 'cancelled';

    public static function all(): array
    {
        return [
            self::DRAFT,
            self::PROVISIONING,
            self::ACTIVE,
            self::SUSPENDED,
            self::CANCELLED,
        ];
    }
}