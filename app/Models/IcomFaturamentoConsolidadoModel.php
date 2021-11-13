<?php

namespace App\Models;

use App\Entities\IcomFaturamentoConsolidado;

class IcomFaturamentoConsolidadoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_faturamentos_consolidados';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomFaturamentoConsolidado::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_aprovacao',
        'id_cliente',
        'total_horas',
        'valor_total',
        'data_validacao',
        'data_nova_validacao',
        'data_aprovacao',
        'data_faturado',
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
        'id_cliente'            => 'required|is_natural_no_zero|max_length[11]',
        'total_horas'           => 'numeric|max_length[10]',
        'valor_total'           => 'numeric|max_length[10]',
        'data_validacao'        => 'valid_date',
        'data_nova_validacao'   => 'valid_date',
        'data_aprovacao'        => 'valid_date',
        'data_faturado'         => 'valid_date',
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
