<?php

namespace App\Models;

use App\Entities\AtividadeScheduler;

class AtividadeSchedulerModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'atividades_scheduler';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = AtividadeScheduler::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'id_usuario',
        'atividade',
        'dia',
        'semana',
        'mes',
        'objetivos',
        'data_cadastro',
        'data_limite',
        'envolvidos',
        'observacoes',
        'processo_roteiro',
        'documento_1',
        'documento_2',
        'documento_3',
        'lembrar',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'        => 'required|is_natural_no_zero|max_length[11]',
        'id_usuario'        => 'is_natural_no_zero|max_length[11]',
        'atividade'         => 'required|string|max_length[255]',
        'dia'               => 'integer|exact_length[2]',
        'semana'            => 'integer|exact_length[1]',
        'mes'               => 'integer|exact_length[2]',
        'objetivos'         => 'required|string',
        'data_cadastro'     => 'required|valid_date',
        'data_limite'       => 'string|max_length[255]',
        'envolvidos'        => 'required|string',
        'observacoes'       => 'string',
        'processo_roteiro'  => 'string',
        'documento_1'       => 'string|max_length[255]',
        'documento_2'       => 'string|max_length[255]',
        'documento_3'       => 'string|max_length[255]',
        'lembrar'           => 'required|integer|exact_length[1]',
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
