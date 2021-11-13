<?php

namespace App\Models;

use App\Entities\AnalisePercepcaoPontuacao;

class AnalisePercepcaoPontuacaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'analise_percepcao_pontuacoes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = AnalisePercepcaoPontuacao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_atributo',
        'id_concorrente',
        'id_grupo',
        'pontuacao',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_atributo'       => 'required|is_natural_no_zero|max_length[11]',
        'id_concorrente'    => 'is_natural_no_zero|max_length[11]',
        'id_grupo'          => 'is_natural_no_zero|max_length[11]',
        'pontuacao'         => 'required|integer|max_length[2]',
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
