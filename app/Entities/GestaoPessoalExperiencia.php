<?php

namespace App\Entities;

class GestaoPessoalExperiencia extends AbstractEntity
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
        'id_depto' => 'int',
        'ano' => 'int',
        'mes' => 'int',
        'total_avaliados' => 'int',
    ];
}
