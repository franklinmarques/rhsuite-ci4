<?php

namespace App\Entities;

class UsuarioIntegracao extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_usuario' => 'int',
        'data_inicio' => 'date',
        'data_termino' => 'date',
        'atividades_desenvolvidas' => 'string',
        'realizadores' => 'string',
        'observacoes' => '?string',
    ];
}
