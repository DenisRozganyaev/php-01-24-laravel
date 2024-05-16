<?php

namespace App\Enums\Permissions;

enum Product: string
{
    case PUBLISH = 'publish product';
    case EDIT = 'edit product';
    case DELETE = 'delete product';

    public static function values(): array
    {
        $values = [];

        foreach (self::cases() as $props) {
            $values[] = $props->value;
        }

        return $values;
    }
}
