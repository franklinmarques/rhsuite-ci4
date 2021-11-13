<?php

namespace App\Entities;

class IcomCliente extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_empresa' => 'int',
        'nome' => 'string',
        'nome_fantasia' => '?string',
        'cnpj' => '?string',
        'data_vencimento_contrato' => '?date',
        'tipo' => '?string',
        'centro_custo' => '?string',
        'condicoes_pagamento' => '?string',
        'valor_contratual_mensal' => '?decimal',
        'valor_minutos_excedidos' => '?decimal',
        'qtde_horas_contratadas' => '?decimal',
        'endereco' => '?string',
        'observacoes' => '?string',
        'contato_principal' => '?string',
        'telefone_contato_principal' => '?string',
        'email_contato_principal' => '?string',
        'cargo_contato_principal' => '?string',
        'contato_secundario' => '?string',
        'telefone_contato_secundario' => '?string',
        'email_contato_secundario' => '?string',
        'cargo_contato_secundario' => '?string',
    ];
}
