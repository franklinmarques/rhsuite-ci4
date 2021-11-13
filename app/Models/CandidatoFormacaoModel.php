<?php

namespace App\Models;

use App\Entities\CandidatoFormacao;

class CandidatoFormacaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'candidatos_formacoes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = CandidatoFormacao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_candidato',
        'id_escolaridade',
        'curso',
        'tipo',
        'instituicao',
        'ano_conclusao',
        'concluido',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_candidato'      => 'required|is_natural_no_zero|max_length[11]',
        'id_escolaridade'   => 'required|is_natural_no_zero|max_length[11]',
        'curso'             => 'string|max_length[255]',
        'tipo'              => 'string|max_length[1]',
        'instituicao'       => 'required|string|max_length[255]',
        'ano_conclusao'     => 'int|max_length[4]',
        'concluido'         => 'required|integer|exact_length[1]',
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
