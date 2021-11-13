<?php

namespace App\Models;

use App\Entities\IcomPagamentoConsolidado;

class IcomPagamentoConsolidadoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_pagamentos_consolidados';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomPagamentoConsolidado::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_aprovacao',
        'id_usuario_prestador',
        'total_horas',
        'valor_total',
        'data_validacao',
        'assinatura_validador',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_aprovacao'          => 'required|is_natural_no_zero|max_length[11]',
        'id_usuario_prestador'  => 'required|is_natural_no_zero|max_length[11]',
        'total_horas'           => 'numeric|max_length[10]',
        'valor_total'           => 'numeric|max_length[10]',
        'data_validacao'        => 'valid_date',
        'assinatura_validador'  => 'string|max_length[255]',
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
