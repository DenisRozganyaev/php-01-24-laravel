<?php

namespace App\Enums;

enum OrderStatusEnum: string
{
    case InProcess = 'In Process';
    case Paid = 'Paid';
    case Completed = 'Completed';
    case Canceled = 'Canceled';

    public static function findByKey(string $key)
    {
        return constant("self::$key");
    }
}
