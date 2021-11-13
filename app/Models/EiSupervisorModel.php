<?php

namespace App\Models;

use App\Entities\EiSupervisor;

class EiSupervisorModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_supervisores';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiSupervisor::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_usuario',
        'depto',
        'area',
        'setor',
        'ano',
        'semestre',
        'carga_horaria',
        'saldo_acumulado_horas',
        'is_coordenador',
        'is_supervisor',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_usuario'            => 'required|is_natural_no_zero|max_length[11]',
        'depto'                 => 'required|integer|max_length[11]',
        'area'                  => 'required|integer|max_length[11]',
        'setor'                 => 'required|integer|max_length[11]',
        'ano'                   => 'required|int|max_length[4]',
        'semestre'              => 'required|integer|exact_length[1]',
        'carga_horaria'         => 'valid_time',
        'saldo_acumulado_horas' => 'string|max_length[10]',
        'is_coordenador'        => 'integer|exact_length[1]',
        'is_supervisor'         => 'integer|exact_length[1]',
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
