<?php

namespace App\Entities;

class KanbanEtapa extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_atividade' => 'int',
        'id_coluna' => 'int',
        'status' => 'string',
        'data_inicio' => 'datetime',
        'data_termino' => '?datetime',
    ];
}
