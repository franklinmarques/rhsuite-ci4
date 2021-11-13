<?php

namespace App\Models\Traits;

trait ObserverTrait
{
    /**
     * @var bool
     */
    protected $allowCallbacks = true;

    /**
     * @var string[]
     */
    protected $beforeInsert = ['beforeInsert'];

    /**
     * @var string[]
     */
    protected $afterInsert = ['afterInsert'];

    /**
     * @var string[]
     */
    protected $beforeUpdate = ['beforeUpdate'];

    /**
     * @var string[]
     */
    protected $afterUpdate = ['afterUpdate'];

    /**
     * @var string[]
     */
    protected $beforeFind = ['beforeFind'];

    /**
     * @var string[]
     */
    protected $afterFind = ['afterFind'];

    /**
     * @var string[]
     */
    protected $beforeDelete = ['beforeDelete'];

    /**
     * @var string[]
     */
    protected $afterDelete = ['afterDelete'];


    /**
     * @param array $data
     * @return array
     */
    protected abstract function beforeInsert(array &$data): array;

    /**
     * @param array $data
     * @return array
     */
    protected function afterInsert(array $data): array
    {
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function beforeUpdate(array $data): array
    {
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function afterUpdate(array $data): array
    {
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function beforeFind(array $data): array
    {
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function afterFind(array $data): array
    {
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function beforeDelete(array $data): array
    {
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function afterDelete(array $data): array
    {
        return $data;
    }
}