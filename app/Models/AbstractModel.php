<?php

namespace App\Models;

use App\Entities\EntityCollection;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;
use ReflectionException;

abstract class AbstractModel extends Model
{

    /**
     * @var array
     */
    protected $uploadConfig = [];


    public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
    {
        $this->allowedFields = array_keys($this->validationRules);
        parent::__construct($db, $validation);
    }

    protected function doFind(bool $singleton, $id = null)
    {
        $row = parent::doFind($singleton, $id);
        if ($singleton) {
            return $row;
        }
        if (!in_array($this->returnType, ['array', 'object', null]) and $this->tempReturnType == $this->returnType) {
            return new EntityCollection($row);
        }
        return $row;
    }

    protected function doFindAll(int $limit = 0, int $offset = 0)
    {
        $builder = parent::doFindAll($limit, $offset);
        if (!in_array($this->returnType, ['array', 'object', null]) and $this->tempReturnType == $this->returnType) {
            return new EntityCollection($builder);
        }
        return $builder;
    }

    public function findOne($id = null)
    {
        if (!is_numeric($id) and !is_string($id)) {
            return null;
        }
        return $this->find($id);
    }

    public function findOneOrFail($id)
    {
        $data = $this->findOne($id);
        $this->orFail($data);
        return $data;
    }

    public function findOrFail($id)
    {
        $data = $this->find($id);
        $this->orFail($data);
        return $data;
    }

    public function firstOrFail()
    {
        $data = $this->first();
        $this->orFail($data);
        return $data;
    }

    protected function orFail($data)
    {
        if (empty($data)) {
            exit(json_encode(['erro' => 'Nenhum registro encontrado.']));
        }
    }

    /**
     * @throws ReflectionException
     */
    public function insertOrFail($data = null, bool $returnID = true)
    {
        if ($result = $this->insert($data, $returnID) === false) {
            exit(json_encode(['erro' => $this->errors()]));
        }
        http_response_code(201);
        return $result ?? json_encode(['status' => true]);
    }

    /**
     * @throws ReflectionException
     */
    public function updateOrFail($id = null, $data = null)
    {
        if ($this->update($id, $data) === false) {
            exit(json_encode(['erro' => $this->errors()]));
        }
        return json_encode(['status' => true]);
    }

    /**
     * @throws ReflectionException
     */
    public function saveOrFail($data)
    {
        $id = $this->getIdValue($data);
        if (!empty($id)) {
            return $this->updateOrFail($id, $data);
        }
        return $this->insertOrFail($data);
    }

    public function deleteOrFail($id = null, bool $purge = false)
    {
        if ($this->delete($id, $purge) === false) {
            exit(json_encode(['erro' => $this->errors()]));
        }
        return json_encode(['status' => true]);
    }

    public function setUploadConfig(array $config = [])
    {
        $this->uploadConfig = $config;
    }

    public function uploadFiles()
    {

    }

    public function deleteFiles()
    {

    }

}