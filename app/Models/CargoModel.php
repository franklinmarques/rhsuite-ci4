<?php

namespace App\Models;

use App\Entities\Cargo;

class CargoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'cargos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = Cargo::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'cargo',
        'funcao',
        'peso_competencias_tecnicas',
        'peso_competencias_comportamentais',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'                        => 'required|is_natural_no_zero|max_length[11]',
        'cargo'                             => 'required|string|max_length[255]',
        'funcao'                            => 'required|string|max_length[255]',
        'peso_competencias_tecnicas'        => 'required|integer|max_length[11]',
        'peso_competencias_comportamentais' => 'required|integer|max_length[11]',
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
