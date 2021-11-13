<?php

namespace App\Models;

use App\Entities\ArquivoTemp;

class ArquivoTempModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'arquivos_temp';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = ArquivoTemp::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_usuario',
        'arquivo',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_usuario'    => 'integer|max_length[11]',
        'arquivo'       => 'string',
    ];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = ['configurarUsuario'];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];

    //--------------------------------------------------------------------

    protected $uploadConfig = [
        'arquivo' => ['upload_path' => './arquivos/temp/', 'allowed_types' => '*']
    ];

    //--------------------------------------------------------------------

    protected function configurarUsuario($data)
    {
        if (array_key_exists('data', $data) === false) {
            return $data;
        }

        $data['data']['id_usuario'] = session('id');

        return $data;
    }
}
