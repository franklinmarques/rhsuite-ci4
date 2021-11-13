<?php

namespace App\Models;

use App\Entities\EiMatriculado;

class EiMatriculadoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_matriculados';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiMatriculado::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_alocacao_escola',
        'id_os_aluno',
        'id_aluno',
        'aluno',
        'id_aluno_curso',
        'id_curso',
        'curso',
        'id_disciplina',
        'disciplina',
        'hipotese_diagnostica',
        'modulo',
        'status',
        'data_inicio',
        'data_termino',
        'data_recesso',
        'media_semestral',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_alocacao_escola'    => 'required|is_natural_no_zero|max_length[11]',
        'id_os_aluno'           => 'is_natural_no_zero|max_length[11]',
        'id_aluno'              => 'is_natural_no_zero|max_length[11]',
        'aluno'                 => 'required|string|max_length[255]',
        'id_aluno_curso'        => 'integer|max_length[11]',
        'id_curso'              => 'integer|max_length[11]',
        'curso'                 => 'string|max_length[255]',
        'id_disciplina'         => 'integer|max_length[11]',
        'disciplina'            => 'string|max_length[255]',
        'hipotese_diagnostica'  => 'string|max_length[255]',
        'modulo'                => 'required|string|max_length[20]',
        'status'                => 'required|string|max_length[1]',
        'data_inicio'           => 'valid_date',
        'data_termino'          => 'valid_date',
        'data_recesso'          => 'valid_date',
        'media_semestral'       => 'numeric|max_length[3]',
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
