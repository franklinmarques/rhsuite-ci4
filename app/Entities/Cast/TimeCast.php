<?php

namespace App\Entities\Cast;

use CodeIgniter\Entity\Cast\BaseCast;
use CodeIgniter\I18n\Time;
use DateTime;
use Exception;

class TimeCast extends BaseCast
{

    /**
     * @throws Exception
     */
    public static function get($value, array $params = [])
    {
        if ($value instanceof DateTime) {
            $value = Time::createFromInstance($value);
        }
        if (is_numeric($value)) {
            $value = Time::createFromTimestamp($value);
        }
        if (is_string($value) and strpos($value, '-') !== false) {
            $value = Time::parse($value);
        }

        return $value->toLocalizedString('dd/MM/Y');
    }

    /**
     * @throws Exception
     */
    public static function set($value, array $params = [])
    {
        if (strpos($value, '/') !== false) {
            $value = preg_replace('/^(\d+):(\d+):(\d+)*/', '$1:$2:$3', $value);
        }
        return Time::parse($value);
    }

}