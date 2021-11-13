<?php

namespace App\Entities;

class UsuarioAfastamento extends AbstractEntity
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
        'id_empresa' => 'int',
        'data_afastamento' => 'date',
        'motivo_afastamento' => '?int',
        'motivo_afastamento_bck' => '?string',
        'data_pericia_medica' => '?date',
        'data_limite_beneficio' => '?date',
        'data_retorno' => '?date',
        'historico_afastamento' => '?string',
    ];
}
