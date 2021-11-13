<?php

namespace App\Entities;

class StContratoServico extends AbstractEntity
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
        'tipo' => 'int',
        'descricao' => 'string',
        'data_reajuste' => '?date',
        'valor' => 'decimal',
    ];
}
