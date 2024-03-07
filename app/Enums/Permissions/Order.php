<?php

namespace App\Enums\Permissions;

enum Order: string
{
    case EDIT = 'edit order';
    case DELETE = 'delete order';

    public static function values(): array
    {
        $values = [];

        foreach (self::cases() as $props) {
            $values[] = $props->value;
        }

        return $values;
    }
}
