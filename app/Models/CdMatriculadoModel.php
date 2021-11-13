<?php

namespace App\Models;

use App\Entities\CdMatriculado;

class CdMatriculadoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'cd_matriculados';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = CdMatriculado::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_alocacao',
        'id_aluno',
        'aluno',
        'escola',
        'supervisor',
        'hipotese_diagnostica',
        'turno',
        'status',
        'dia_inicial',
        'dia_limite',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_alocacao'           => 'required|is_natural_no_zero|max_length[11]',
        'id_aluno'              => 'integer|max_length[11]',
        'aluno'                 => 'required|string|max_length[255]',
        'escola'                => 'required|string|max_length[255]',
        'supervisor'            => 'required|string|max_length[255]',
        'hipotese_diagnostica'  => 'required|string|max_length[255]',
        'turno'                 => 'required|string|max_length[1]',
        'status'                => 'required|string|max_length[1]',
        'dia_inicial'           => 'integer|max_length[2]',
        'dia_limite'            => 'integer|max_length[2]',
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
