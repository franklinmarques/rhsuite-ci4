<?php

namespace App\Entities;

class EmtuAlocacaoFeriado extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_alocacao' => 'int',
        'data' => 'date',
        'status' => 'string',
        'qtde_novos_processos' => '?int',
        'qtde_analistas' => '?int',
        'qtde_processos_tratados_dia' => '?int',
        'qtde_pagamentos' => '?int',
    ];
}
