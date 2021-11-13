<?php

namespace App\Entities;

class CompraOrcamentoEmpresa extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_orcamento' => 'int',
        'id_fornecedor' => 'int',
        'empresa_contratada' => 'bool',
        'preco' => '?decimal',
        'frete' => '?decimal',
        'desconto' => '?decimal',
        'prazo_entrega' => '?date',
        'validade_proposta' => '?date',
        'contato' => '?string',
        'telefone' => '?string',
        'email' => '?string',
        'status' => '?string',
        'iq' => '?bool',
        'observacoes' => '?string',
    ];
}
