<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case CASHIER = 'cashier';

    /**
     * Get all role values as array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all role names as array.
     *
     * @return array<string>
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }
}
