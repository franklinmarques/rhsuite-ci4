<?php

namespace App\Models;

use App\Entities\EiAlunoTurma;

class EiAlunoTurmaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_alunos_turmas';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiAlunoTurma::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_semestre',
        'id_disciplina',
        'id_cuidador',
        'dia_semana',
        'hora_inicio',
        'hora_termino',
        'periodo',
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
        'id_semestre'   => 'required|integer|max_length[11]',
        'id_disciplina' => 'required|is_natural_no_zero|max_length[11]',
        'id_cuidador'   => 'integer|max_length[11]',
        'dia_semana'    => 'integer|max_length[1]',
        'hora_inicio'   => 'valid_time',
        'hora_termino'  => 'valid_time',
        'periodo'       => 'string|max_length[1]',
        'nota'          => 'numeric|max_length[3]',
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
