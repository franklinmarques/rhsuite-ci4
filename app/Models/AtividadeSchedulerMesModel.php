<?php

namespace App\Models;

use App\Entities\AtividadeSchedulerMes;

class AtividadeSchedulerMesModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'atividades_scheduler_meses';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = AtividadeSchedulerMes::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_atividade_scheduler',
        'janeiro',
        'fevereiro',
        'marco',
        'abril',
        'maio',
        'junho',
        'julho',
        'agosto',
        'setembro',
        'outubro',
        'novembro',
        'dezembro',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_atividade_scheduler'    => 'required|integer|max_length[11]',
        'janeiro'                   => 'integer|exact_length[1]',
        'fevereiro'                 => 'integer|exact_length[1]',
        'marco'                     => 'integer|exact_length[1]',
        'abril'                     => 'integer|exact_length[1]',
        'maio'                      => 'integer|exact_length[1]',
        'junho'                     => 'integer|exact_length[1]',
        'julho'                     => 'integer|exact_length[1]',
        'agosto'                    => 'integer|exact_length[1]',
        'setembro'                  => 'integer|exact_length[1]',
        'outubro'                   => 'integer|exact_length[1]',
        'novembro'                  => 'integer|exact_length[1]',
        'dezembro'                  => 'integer|exact_length[1]',
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
