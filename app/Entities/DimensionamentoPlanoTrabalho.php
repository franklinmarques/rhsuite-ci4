<?php

namespace App\Entities;

class DimensionamentoPlanoTrabalho extends AbstractEntity
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
        'data_inicio' => 'date',
        'data_termino' => 'date',
        'plano_diario' => 'bool',
        'status' => 'string',
    ];
}
