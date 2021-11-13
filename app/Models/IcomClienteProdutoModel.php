<?php

namespace App\Models;

use App\Entities\IcomClienteProduto;

class IcomClienteProdutoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_clientes_produtos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomClienteProduto::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_cliente',
        'id_produto',
        'valor_faturamento',
        'valor_pagamento',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_cliente'        => 'required|is_natural_no_zero|max_length[11]',
        'id_produto'        => 'required|is_natural_no_zero|max_length[11]',
        'valor_faturamento' => 'required|numeric|max_length[10]',
        'valor_pagamento'   => 'required|numeric|max_length[10]',
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
