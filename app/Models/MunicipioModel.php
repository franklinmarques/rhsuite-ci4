<?php

namespace App\Models;

use App\Entities\Municipio;

class MunicipioModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'municipios';
	protected $primaryKey           = 'cod_mun';
	protected $useAutoIncrement     = false;
	protected $insertID             = 0;
	protected $returnType           = Municipio::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'cod_mun',
        'cod_uf',
        'municipio',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'cod_mun'   => 'required|integer|max_length[11]',
        'cod_uf'    => 'required|integer|max_length[11]',
        'municipio' => 'required|string|max_length[40]',
    ];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = [];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];
}
