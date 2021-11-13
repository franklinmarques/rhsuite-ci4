<?php

namespace App\Entities;

class IcomProposta extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'codigo' => 'int',
        'id_cliente' => 'int',
        'id_setor' => '?int',
        'codigo_alfa' => '?string',
        'descricao' => 'string',
        'tipo' => '?string',
        'id_produto' => '?int',
        'id_modelo_proposta' => '?int',
        'descricao_abertura' => '?string',
        'descricao_objeto' => '?string',
        'descricao_complemento' => '?string',
        'descricao_condicoes_pagamento' => '?string',
        'quantidade_horas' => '?decimal',
        'data_evento' => '?date',
        'local_evento' => '?string',
        'data_entrega' => 'date',
        'probabilidade_fechamento' => '?int',
        'valor' => 'decimal',
        'status' => 'string',
        'custo_produto_servico' => '?decimal',
        'custo_administrativo' => '?decimal',
        'impostos' => '?decimal',
        'margem_liquida' => '?decimal',
        'margem_liquida_percentual' => '?int',
        'detalhes' => '?string',
        'arquivo' => '?string',
    ];
}
