<?php

namespace App\Entities;

use App\Entities\Cast\DateBrCast;
use App\Entities\Cast\DateTimeBrCast;
use App\Entities\Cast\DecimalBrCast;
use App\Entities\Cast\FloatBrCast;
use App\Entities\Cast\Time24hCast;
use App\Entities\Cast\TimeCast;
use App\Entities\Cast\TimestampBrCast;
use App\Models\AbstractModel;
use CodeIgniter\Entity\Entity;

abstract class AbstractEntity extends Entity
{
    protected $castHandlers = [
        'timestamp' => TimestampBrCast::class,
        'datetime' => DateTimeBrCast::class,
        'date' => DateBrCast::class,
        'decimal' => DecimalBrCast::class,
        'double' => FloatBrCast::class,
        'float' => FloatBrCast::class,
        'time' => TimeCast::class,
        'time24h' => Time24hCast::class,
    ];

    public function __get(string $key)
    {
        if (in_array($key, $this->dates, true) and array_key_exists($key, $this->casts)) {
            $this->dates = array_diff($this->dates, [$key]);
        }
        return parent::__get($key);
    }

    public function __set(string $key, $value = null)
    {
        if (in_array($key, $this->dates, true) and array_key_exists($key, $this->casts)) {
            $this->dates = array_diff($this->dates, [$key]);
        }
        return parent::__set($key, $value);
    }

    protected function castAs($value, string $attribute, string $method = 'get')
    {
//        $value = $this->nullifyTrimmedEmptyString($value);
        return parent::castAs($value, $attribute, $method);
    }

    private function nullifyTrimmedEmptyString($value)
    {
        if (is_string($value)) {
            $value = trim($value);
            if (strlen($value) == 0) {
                return null;
            }
        }
        return $value;
    }

    public function raw(): AbstractEntity
    {
        $this->castHandlers = [
            'date' => DateBrCast::class,
            'time' => TimeCast::class,
            'time24h' => Time24hCast::class,
        ];
        return $this;
    }

    protected function hasOne($related, $foreignKey = null, $localKey = null)
    {
        db_connect()
            ->table($related)
            ->join()
            ->where()->getResult();
    }

    protected function hasMany($related, $foreignKey = null, $localKey = null)
    {
        $model = $this->getModel($related);
    }

    protected function belongsTo($related, $foreignKey = null, $ownerKey = null, $relation = null)
    {
        if (is_null($foreignKey)) {
            $foreignKey = 'id_' . strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', explode('\\', $related)[2]));
        }
        $selfModel = $this->getModel(static::class);
        $parentModel = $this->getModel($related);
        if (is_null($ownerKey)) {
            $ownerKey = $parentModel->primaryKey;
        }
        dd($parentModel
            ->join($selfModel->getTable(), "{$selfModel->getTable()}.$foreignKey = {$parentModel->getTable()}.$ownerKey")
            ->where("{$parentModel->getTable()}.$ownerKey", $this->attributes[$foreignKey])
            ->getCompiledSelect());
        return $parentModel
            ->join($selfModel->getTable(), "{$selfModel->getTable()}.$foreignKey = {$parentModel->getTable()}.$ownerKey")
            ->where("{$parentModel->getTable()}.$ownerKey", $this->attributes[$foreignKey])
            ->first();
    }

    protected function belongsToMany($related, $table = null, $foreignPivotKey = null, $relatedPivotKey = null, $parentKey = null, $relatedKey = null, $relation = null)
    {

    }

    private function getModel($entity)
    {
        $model = str_replace('Entities', 'Models', $entity) . 'Model';
        return new $model;
    }

    private function getForeignKey(?string $foreignKey): string
    {
        return is_null($foreignKey) ? 'id_' . strtolower($foreignKey) : $foreignKey;
    }

    /*private function getPrimaryKey($model)
    {
        return new ($entity . 'Model');
    }*/
}
