<?php

namespace App\Entities;

class DimensionamentoJob extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_plano_trabalho' => 'int',
        'nome' => 'string',
        'data_inicio' => 'date',
        'data_termino' => 'date',
        'horario_inicio' => '?time',
        'horario_termino' => '?time',
        'plano_diario' => 'bool',
        'status' => 'string',
    ];
}
