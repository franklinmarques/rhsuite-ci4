<?php

namespace App\Entities;

class EmtuAlocacao extends AbstractEntity
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
        'id_depto' => '?int',
        'id_area' => '?int',
        'id_setor' => '?int',
        'mes' => 'int',
        'ano' => 'int',
        'dia_fechamento' => '?int',
    ];
}
