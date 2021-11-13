<?php

namespace App\Models;

use App\Entities\CompraRequisicaoOrcamento;

class CompraRequisicaoOrcamentoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'compras_requisicoes_orcamentos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = CompraRequisicaoOrcamento::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'status',
        'prioridade',
        'data_desejada',
        'data_abertura',
        'data_recebimento',
        'data_encerramento',
        'id_depto',
        'id_area',
        'id_setor',
        'id_requisitante',
        'itens_solicitados',
        'sugestao_fornecedor_preferencial',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'                        => 'required|is_natural_no_zero|max_length[11]',
        'status'                            => 'required|string|max_length[4]',
        'prioridade'                        => 'required|integer|max_length[11]',
        'data_desejada'                     => 'valid_date',
        'data_abertura'                     => 'required|valid_date',
        'data_recebimento'                  => 'valid_date',
        'data_encerramento'                 => 'valid_date',
        'id_depto'                          => 'required|is_natural_no_zero|max_length[11]',
        'id_area'                           => 'required|is_natural_no_zero|max_length[11]',
        'id_setor'                          => 'required|is_natural_no_zero|max_length[11]',
        'id_requisitante'                   => 'required|is_natural_no_zero|max_length[11]',
        'itens_solicitados'                 => 'required|string',
        'sugestao_fornecedor_preferencial'  => 'string',
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
        'RCA' => 'Requisição de compra aberta',
        'RCST' => 'Requisição de compra sendo tratada',
        'OS' => 'Orçamento solicitado',
        'OSA' => 'Orçamento sendo analisado',
        'PCE' => 'Pedido de compra emitido - Aguardando entrega',
        'PE' => 'Produto entregue',
        'SSE' => 'Serviço sendo realizado',
        'SF' => 'Serviço finalizado',
    ];
    public const PRIORIDADES = [
        '1' => 'Baixa',
        '2' => 'Média',
        '3' => 'Alta',
    ];
}
