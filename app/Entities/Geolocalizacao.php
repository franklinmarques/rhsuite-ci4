<?php

namespace App\Entities;

class Geolocalizacao extends AbstractEntity
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
        'local' => '?string',
        'endereco' => '?string',
        'numero' => '?int',
        'bairro' => '?string',
        'id_cidade' => '?int',
        'id_estado' => '?int',
        'latitude' => '?string',
        'longitude' => '?string',
        'email' => '?string',
        'usuario' => '?string',
        'senha' => '?string',
        'ativo' => 'bool',
    ];
}
