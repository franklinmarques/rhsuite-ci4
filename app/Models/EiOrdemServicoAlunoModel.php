<?php

namespace App\Models;

use App\Entities\EiOrdemServicoAluno;

class EiOrdemServicoAlunoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_ordens_servico_alunos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiOrdemServicoAluno::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_os_escola',
        'id_aluno',
        'id_aluno_curso',
        'data_inicio',
        'data_termino',
        'modulo',
        'nota',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_os_escola'   => 'required|is_natural_no_zero|max_length[11]',
        'id_aluno'                  => 'required|is_natural_no_zero|max_length[11]',
        'id_aluno_curso'            => 'required|is_natural_no_zero|max_length[11]',
        'data_inicio'               => 'valid_date',
        'data_termino'              => 'valid_date',
        'modulo'                    => 'string|max_length[20]',
        'nota'                      => 'numeric|max_length[3]',
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
