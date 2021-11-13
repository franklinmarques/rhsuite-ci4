<?php

namespace App\Entities;

class KanbanColuna extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_quadro' => 'int',
        'nome' => 'string',
        'cor' => '?string',
    ];
}
