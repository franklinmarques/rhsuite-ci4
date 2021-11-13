<?php

namespace App\Entities;

class RequisicaoPessoalAprovador extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id_usuario' => 'int',
    ];
}
