<?php

namespace App\Entities;

class StAlocacaoFeriado extends AbstractEntity
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
        'status' => '?string',
        'qtde_novos_processos' => '?int',
        'qtde_analistas' => '?int',
        'qtde_processos_analisados' => '?int',
        'qtde_pagamentos' => '?int',
        'qtde_linhas_analisadas' => '?int',
    ];
}
