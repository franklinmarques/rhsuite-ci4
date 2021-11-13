<?php

namespace App\Entities;

class Evento extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'date_from' => 'datetime',
        'date_to' => 'datetime',
        'type' => 'int',
        'title' => 'string',
        'description' => 'string',
        'link' => '?string',
        'color' => '?string',
        'status' => 'int',
        'id_usuario' => '?int',
        'id_usuario_referenciado' => '?int',
    ];
}
