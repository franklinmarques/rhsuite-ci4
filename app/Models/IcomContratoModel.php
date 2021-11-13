<?php

namespace App\Models;

use App\Entities\IcomContrato;

class IcomContratoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_contratos';
	protected $primaryKey           = 'codigo';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomContrato::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'codigo_proposta',
        'tipo_contrato',
        'centro_custo',
        'condicoes_pagamento',
        'data_vencimento',
        'status_ativo',
        'arquivo',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'            => 'required|is_natural_no_zero|max_length[11]',
        'codigo_proposta'       => 'required|is_natural_no_zero|max_length[11]',
        'tipo_contrato'         => 'required|string|max_length[1]',
        'centro_custo'          => 'string|max_length[255]',
        'condicoes_pagamento'   => 'string|max_length[255]',
        'data_vencimento'       => 'required|valid_date',
        'status_ativo'          => 'required|integer|exact_length[1]',
        'arquivo'               => 'string|max_length[255]',
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

    protected $uploadConfig = ['arquivo' => ['upload_path' => './arquivos/icom/contratos/', 'allowed_types' => 'pdf']];

    public const TIPOS_CONTRATO = [
        'P' => 'Padrão',
        'C' => 'Customizado',
    ];
    public const STATUS = [
        '1' => 'Ativo',
        '0' => 'Inativo',
    ];
}
