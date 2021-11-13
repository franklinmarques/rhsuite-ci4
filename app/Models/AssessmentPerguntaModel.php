<?php

namespace App\Models;

use App\Entities\AssessmentPergunta;

class AssessmentPerguntaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'assessments_perguntas';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = AssessmentPergunta::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_modelo',
        'pergunta',
        'tipo_resposta',
        'tipo_eneagrama',
        'id_competencia',
        'competencia',
        'justificativa',
        'valor_min',
        'valor_max',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_modelo'         => 'required|is_natural_no_zero|max_length[11]',
        'pergunta'          => 'required|string',
        'tipo_resposta'     => 'required|string|max_length[1]',
        'tipo_eneagrama'    => 'integer|max_length[1]',
        'id_competencia'    => 'is_natural_no_zero|max_length[11]',
        'competencia'       => 'string|max_length[255]',
        'justificativa'     => 'integer|max_length[1]',
        'valor_min'         => 'integer|max_length[11]',
        'valor_max'         => 'integer|max_length[11]',
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

    //--------------------------------------------------------------------

    public const TIPO_RESPOSTA = [
        'A' => 'Aberta',
        'N' => 'Numérica',
        'U' => 'Única escolha',
        'M' => 'Múltipla escolha',
        'V' => 'Verdadeiro/falso',
    ];

}
