<?php

namespace App\Entities;

class IcomFaturamentoSolicitacao extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_cliente' => 'int',
        'conta_corrente' => '?string',
        'mes_referencia' => 'int',
        'ano_referencia' => 'int',
        'cnpj' => '?string',
        'endereco' => '?string',
        'telefone' => '?string',
        'email' => '?string',
        'contato' => '?string',
        'email_secundario' => '?string',
        'contato_secundario' => '?string',
        'condicoes_pagamento' => '?string',
        'centro_custo' => '?string',
        'total_sessoes' => 'int',
        'valor_total' => 'decimal',
        'data_validacao' => '?date',
        'valor_hora_contratado' => '?decimal',
        'valor_minutos_excedidos' => '?decimal',
        'qtde_horas_contratadas' => '?decimal',
        'qtde_minutos_excedentes' => '?decimal',
        'valor_faturamento_excedente' => '?decimal',
        'assinatura_validador' => '?string',
        'telefone_validador' => '?string',
        'email_validador' => '?string',
        'data_emissao' => 'datetime',
        'assinatura' => '?string',
        'observacoes' => '?string',
        'bloqueado' => '?bool',
    ];
}
