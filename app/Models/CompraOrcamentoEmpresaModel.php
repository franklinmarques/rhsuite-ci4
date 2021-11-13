<?php

namespace App\Models;

use App\Entities\CompraOrcamentoEmpresa;

class CompraOrcamentoEmpresaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'compras_orcamentos_empresas';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = CompraOrcamentoEmpresa::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_orcamento',
        'id_fornecedor',
        'empresa_contratada',
        'preco',
        'frete',
        'desconto',
        'prazo_entrega',
        'validade_proposta',
        'contato',
        'telefone',
        'email',
        'status',
        'iq',
        'observacoes',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_orcamento'          => 'required|is_natural_no_zero|max_length[11]',
        'id_fornecedor'         => 'required|is_natural_no_zero|max_length[11]',
        'empresa_contratada'    => 'required|integer|exact_length[1]',
        'preco'                 => 'numeric|max_length[10]',
        'frete'                 => 'numeric|max_length[10]',
        'desconto'              => 'numeric|max_length[4]',
        'prazo_entrega'         => 'valid_date',
        'validade_proposta'     => 'valid_date',
        'contato'               => 'string|max_length[255]',
        'telefone'              => 'string|max_length[255]',
        'email'                 => 'string|max_length[255]',
        'status'                => 'string|max_length[2]',
        'iq'                    => 'integer|exact_length[1]',
        'observacoes'           => 'string',
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

    public const STATUS = [
        'AO' => 'Aguardando orçamento',
        'AI' => 'Aguardando aprovação interna',
        'AP' => 'Aguardando entrega do produto',
        'AS' => 'Aguardando execução do serviço',
        'PE' => 'Produto entregue',
        'SE' => 'Serviço entregue',
    ];
    public const IQ = [
        '0' => 'Péssimo',
        '1' => 'Ruim',
        '2' => 'Médio',
        '3' => 'Bom',
        '4' => 'Ótimo',
    ];
}
