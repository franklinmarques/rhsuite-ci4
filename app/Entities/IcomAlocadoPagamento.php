<?php

namespace App\Entities;

class IcomAlocadoPagamento extends AbstractEntity
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
        'id_alocado' => 'int',
        'nome_usuario' => 'string',
        'desconto_folha' => '?time',
        'qtde_horas_mes' => '?time',
        'qtde_horas_pagto' => '?time',
        'valor_total_pagto' => '?decimal',
        'id_usuario_aprovador_pagto' => '?int',
        'nome_aprovador_pagto' => '?string',
        'data_aprovacao_pagto' => '?datetime',
    ];
}
