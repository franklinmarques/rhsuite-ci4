<?php

namespace App\Models;

use App\Entities\EiValorFaturamento;

class EiValorFaturamentoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_valores_faturamentos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiValorFaturamento::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_contrato',
        'ano',
        'semestre',
        'id_cargo',
        'id_funcao',
        'qtde_horas',
        'valor',
        'valor_pagamento',
        'valor2',
        'valor_pagamento2',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_contrato'       => 'required|is_natural_no_zero|max_length[11]',
        'ano'               => 'required|int|max_length[4]',
        'semestre'          => 'required|integer|exact_length[1]',
        'id_cargo'          => 'integer|max_length[11]',
        'id_funcao'         => 'required|integer|max_length[11]',
        'qtde_horas'        => 'numeric|max_length[10]',
        'valor'             => 'numeric|max_length[10]',
        'valor_pagamento'   => 'numeric|max_length[10]',
        'valor2'            => 'numeric|max_length[10]',
        'valor_pagamento2'  => 'numeric|max_length[10]',
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
