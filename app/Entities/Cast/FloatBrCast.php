<?php

namespace App\Entities\Cast;

use CodeIgniter\Entity\Cast\BaseCast;

class FloatBrCast extends BaseCast
{

    public static function get($value, array $params = []): string
    {
        return str_replace('.', ',', $value);
    }

    public static function set($value, array $params = []): float
    {
        return floatval(str_replace(',', '.', $value));
    }

}