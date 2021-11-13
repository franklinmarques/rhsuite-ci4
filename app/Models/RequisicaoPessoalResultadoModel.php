<?php

namespace App\Models;

use App\Entities\RequisicaoPessoalResultado;

class RequisicaoPessoalResultadoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'requisicoes_pessoal_resultados';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = RequisicaoPessoalResultado::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_teste',
        'id_pergunta',
        'peso_max',
        'id_alternativa',
        'valor',
        'resposta',
        'nota',
        'data_avaliacao',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_teste'          => 'required|is_natural_no_zero|max_length[11]',
        'id_pergunta'       => 'required|is_natural_no_zero|max_length[11]',
        'peso_max'          => 'integer|max_length[3]',
        'id_alternativa'    => 'is_natural_no_zero|max_length[11]',
        'valor'             => 'integer|max_length[11]',
        'resposta'          => 'string',
        'nota'              => 'integer|max_length[2]',
        'data_avaliacao'    => 'valid_date',
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
