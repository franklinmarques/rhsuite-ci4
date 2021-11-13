<?php

namespace App\Models;

use App\Entities\EiAlunoCurso;

class EiAlunoCursoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_alunos_cursos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiAlunoCurso::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_aluno',
        'id_curso',
        'id_escola',
        'qtde_semestre',
        'semestre_inicial',
        'semestre_final',
        'nota_geral',
        'status_ativo',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_aluno'          => 'required|is_natural_no_zero|max_length[11]',
        'id_curso'          => 'required|is_natural_no_zero|max_length[11]',
        'id_escola'         => 'required|is_natural_no_zero|max_length[11]',
        'qtde_semestre'     => 'required|integer|max_length[2]',
        'semestre_inicial'  => 'required|string|max_length[6]',
        'semestre_final'    => 'string|max_length[6]',
        'nota_geral'        => 'numeric|max_length[3]',
        'status_ativo'      => 'integer|max_length[1]',
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
