<?php

namespace App\Entities;

class CdApontamento extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_alocado' => 'int',
        'data' => 'date',
        'data_afastamento' => '?date',
        'id_cuidador_sub' => '?int',
        'status' => 'string',
        'qtde_dias' => '?int',
        'apontamento_asc' => '?time',
        'apontamento_desc' => '?time',
        'saldo' => '?int',
        'observacoes' => '?string',
    ];
}
