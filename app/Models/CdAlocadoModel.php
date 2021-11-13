<?php

namespace App\Models;

use App\Entities\CdAlocado;

class CdAlocadoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'cd_alocados';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = CdAlocado::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_alocacao',
        'id_vinculado',
        'cuidador',
        'escola',
        'municipio',
        'supervisor',
        'turno',
        'dia_inicial',
        'dia_limite',
        'remanejado',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_alocacao'   => 'required|is_natural_no_zero|max_length[11]',
        'id_vinculado'  => 'integer|max_length[11]',
        'cuidador'      => 'string|max_length[255]',
        'escola'        => 'string|max_length[255]',
        'municipio'     => 'string|max_length[100]',
        'supervisor'    => 'string|max_length[255]',
        'turno'         => 'string|max_length[1]',
        'dia_inicial'   => 'integer|max_length[2]',
        'dia_limite'    => 'integer|max_length[2]',
        'remanejado'    => 'integer|max_length[1]',
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
