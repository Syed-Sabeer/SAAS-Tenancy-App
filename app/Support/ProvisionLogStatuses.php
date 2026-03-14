<?php

namespace App\Support;

final class ProvisionLogStatuses
{
    const SUCCESS = 'success';
    const FAILED = 'failed';

    public static function all(): array
    {
        return [
            self::SUCCESS,
            self::FAILED,
        ];
    }
}