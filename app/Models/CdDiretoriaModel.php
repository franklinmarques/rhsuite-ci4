<?php

namespace App\Models;

use App\Entities\CdDiretoria;

class CdDiretoriaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'cd_diretorias';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = CdDiretoria::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'nome',
        'alias',
        'id_empresa',
        'depto',
        'municipio',
        'contrato',
        'id_coordenador',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'nome'              => 'required|string|max_length[100]',
        'alias'             => 'string|max_length[100]',
        'id_empresa'        => 'required|is_natural_no_zero|max_length[11]',
        'depto'             => 'required|string|max_length[255]',
        'municipio'         => 'required|string|max_length[100]',
        'contrato'          => 'required|string|max_length[30]',
        'id_coordenador'    => 'is_natural_no_zero|max_length[11]',
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
