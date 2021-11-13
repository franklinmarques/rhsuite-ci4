<?php

namespace App\Entities\Cast;

use CodeIgniter\Entity\Cast\BaseCast;

class DecimalBrCast extends BaseCast
{

    public static function get($value, array $params = []): string
    {
        return str_replace([',', '_'], ['.', ','], str_replace('.', '_', $value));
    }

    public static function set($value, array $params = []): float
    {
        return floatval(str_replace(',', '.', $value));
    }

}