<?php

namespace App\Models;

use App\Entities\IcomModeloProposta;

class IcomModeloPropostaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_modelos_propostas';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomModeloProposta::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_produto',
        'nome',
        'descricao_abertura',
        'descricao_objeto',
        'descricao_complemento',
        'descricao_condicoes_pagamento',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_produto'                    => 'required|is_natural_no_zero|max_length[11]',
        'nome'                          => 'required|string|max_length[255]',
        'descricao_abertura'            => 'string',
        'descricao_objeto'              => 'string',
        'descricao_complemento'         => 'string',
        'descricao_condicoes_pagamento' => 'string',
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
