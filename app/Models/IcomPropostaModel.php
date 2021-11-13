<?php

namespace App\Models;

use App\Entities\IcomProposta;

class IcomPropostaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_propostas';
	protected $primaryKey           = 'codigo';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomProposta::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_cliente',
        'id_setor',
        'codigo_alfa',
        'descricao',
        'tipo',
        'id_produto',
        'id_modelo_proposta',
        'descricao_abertura',
        'descricao_objeto',
        'descricao_complemento',
        'descricao_condicoes_pagamento',
        'quantidade_horas',
        'data_evento',
        'local_evento',
        'data_entrega',
        'probabilidade_fechamento',
        'valor',
        'status',
        'custo_produto_servico',
        'custo_administrativo',
        'impostos',
        'margem_liquida',
        'margem_liquida_percentual',
        'detalhes',
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
        'id_cliente'                    => 'required|is_natural_no_zero|max_length[11]',
        'id_setor'                      => 'is_natural_no_zero|max_length[11]',
        'codigo_alfa'                   => 'string|max_length[25]',
        'descricao'                     => 'required|string|max_length[255]',
        'tipo'                          => 'string|max_length[1]',
        'id_produto'                    => 'is_natural_no_zero|max_length[11]',
        'id_modelo_proposta'            => 'is_natural_no_zero|max_length[11]',
        'descricao_abertura'            => 'string',
        'descricao_objeto'              => 'string',
        'descricao_complemento'         => 'string',
        'descricao_condicoes_pagamento' => 'string',
        'quantidade_horas'              => 'numeric|max_length[10]',
        'data_evento'                   => 'valid_date',
        'local_evento'                  => 'string|max_length[255]',
        'data_entrega'                  => 'required|valid_date',
        'probabilidade_fechamento'      => 'integer|max_length[3]',
        'valor'                         => 'required|numeric|max_length[10]',
        'status'                        => 'required|string|max_length[1]',
        'custo_produto_servico'         => 'numeric|max_length[10]',
        'custo_administrativo'          => 'numeric|max_length[10]',
        'impostos'                      => 'numeric|max_length[10]',
        'margem_liquida'                => 'numeric|max_length[10]',
        'margem_liquida_percentual'     => 'integer|max_length[3]',
        'detalhes'                      => 'string',
        'arquivo'                       => 'string|max_length[255]',
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

    protected $uploadConfig = ['arquivo' => ['upload_path' => './arquivos/icom/propostas/', 'allowed_types' => 'pdf']];

    public const TIPOS = [
        'C' => 'Customizada',
        'P' => 'Padrão',
    ];
    public const STATUS = [
        'A' => 'Aberta',
        'G' => 'Ganha',
        'P' => 'Perdida',
        'C' => 'Cancelada',
    ];
}
