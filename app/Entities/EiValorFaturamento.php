<?php

namespace App\Entities;

class EiValorFaturamento extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_contrato' => 'int',
        'ano' => 'int',
        'semestre' => 'bool',
        'id_cargo' => '?int',
        'id_funcao' => 'int',
        'qtde_horas' => '?decimal',
        'valor' => '?decimal',
        'valor_pagamento' => '?decimal',
        'valor2' => '?decimal',
        'valor_pagamento2' => '?decimal',
    ];
}
