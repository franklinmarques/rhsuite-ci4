<?php

namespace App\Entities;

class IcomPagamentoSolicitacao extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_profissional_alocado' => 'int',
        'nota_fiscal' => 'string',
        'mes_referencia' => 'int',
        'ano_referencia' => 'int',
        'nome_solicitante' => '?string',
        'cnpj' => '?string',
        'centro_custo' => '?string',
        'id_depto_prestador' => '?int',
        'tipo_pagamento' => '?string',
        'total_sessoes' => '?int',
        'total_horas' => '?decimal',
        'valor_total' => 'decimal',
        'data_validacao' => '?date',
        'assinatura_validador' => '?string',
        'data_emissao' => 'datetime',
        'assinatura' => '?string',
    ];
}
