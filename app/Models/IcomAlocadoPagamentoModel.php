<?php

namespace App\Models;

use App\Entities\IcomAlocadoPagamento;

class IcomAlocadoPagamentoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_alocados_pagamentos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomAlocadoPagamento::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_alocacao',
        'id_alocado',
        'nome_usuario',
        'desconto_folha',
        'qtde_horas_mes',
        'qtde_horas_pagto',
        'valor_total_pagto',
        'id_usuario_aprovador_pagto',
        'nome_aprovador_pagto',
        'data_aprovacao_pagto',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_alocacao'                   => 'required|is_natural_no_zero|max_length[11]',
        'id_alocado'                    => 'required|is_natural_no_zero|max_length[11]',
        'nome_usuario'                  => 'required|string|max_length[255]',
        'desconto_folha'                => 'valid_time',
        'qtde_horas_mes'                => 'valid_time',
        'qtde_horas_pagto'              => 'valid_time',
        'valor_total_pagto'             => 'numeric|max_length[10]',
        'id_usuario_aprovador_pagto'    => 'is_natural_no_zero|max_length[11]',
        'nome_aprovador_pagto'          => 'string|max_length[255]',
        'data_aprovacao_pagto'          => 'valid_date',
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
