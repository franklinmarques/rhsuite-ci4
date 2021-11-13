<?php

namespace App\Entities;

class FacilityRealizacao extends AbstractEntity
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
        'id_modelo' => 'int',
        'mes' => 'int',
        'ano' => 'int',
        'pendencias' => 'bool',
        'id_usuario_vistoriador' => '?int',
        'tipo_executor' => '?string',
        'status' => 'string',
    ];
}
