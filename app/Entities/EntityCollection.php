<?php

namespace App\Entities;

use ArrayObject;

class EntityCollection extends ArrayObject
{

    public function __call(string $function, array $params = []): EntityCollection
    {
        foreach ($this->getArrayCopy() as $entity) {
            $entity->$function($params);
        }
        return $this;
    }

    public function __get(string $key): array
    {
        $data = [];
        foreach ($this->getArrayCopy() as $entity) {
            $data[] = $entity->$key;
        }
        return $data;
    }

    public function __set(string $key, $value = null): EntityCollection
    {
        foreach ($this->getArrayCopy() as $entity) {
            $entity->$key = $value;
        }
        return $this;
    }

    public function __isset(string $key): bool
    {
        foreach ($this->getArrayCopy() as $entity) {
            if (isset($entity->attributes[$key]) == false) {
                return false;
            }
        }
        return true;
    }

    public function __unset(string $key)
    {
        foreach ($this->getArrayCopy() as $entity) {
            unset($entity->attributes[$key]);
        }
    }

    public function hasChanged(string $key = null): bool
    {
        foreach ($this->getArrayCopy() as $entity) {
            if ($entity->hasChanged($key)) {
                return true;
            }
        }
        return false;
    }

    public function toArray(bool $onlyChanged = false, bool $cast = true): array
    {
        $data = [];
        foreach ($this->getArrayCopy() as $entity) {
            $data[] = $entity->toArray($onlyChanged, $cast);
        }
        return $data;
    }

    public function toRawArray(bool $onlyChanged = false): array
    {
        $data = [];
        foreach ($this->getArrayCopy() as $entity) {
            $data[] = $entity->toRawArray($onlyChanged);
        }
        return $data;
    }

}