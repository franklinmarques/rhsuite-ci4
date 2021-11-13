<?php

namespace App\Models;

use App\Entities\PesquisaPergunta;

class PesquisaPerguntaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'pesquisa_perguntas';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = PesquisaPergunta::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_modelo',
        'id_categoria',
        'pergunta',
        'tipo_resposta',
        'tipo_eneagrama',
        'prefixo_resposta',
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
        'id_categoria'      => 'is_natural_no_zero|max_length[11]',
        'pergunta'          => 'required|string',
        'tipo_resposta'     => 'required|string|max_length[1]',
        'tipo_eneagrama'    => 'integer|max_length[1]',
        'prefixo_resposta'  => 'string|max_length[30]',
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
}
