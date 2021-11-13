<?php

namespace App\Entities;

class RequisicaoPessoalFaltaAtraso extends AbstractEntity
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
        'total_faltas' => 'int',
        'total_atrasos' => 'int',
        'tempo_total_atraso' => '?time',
    ];
}
