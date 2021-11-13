<?php

namespace App\Entities;

class IcomSpTelefoneEmergencial extends AbstractEntity
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
        'nome_servico' => 'string',
        'id_estado' => 'int',
        'estado' => '?string',
        'id_municipio' => 'int',
        'municipio' => '?string',
        'localidade' => 'string',
        'codigo_numerico' => 'int',
        'nome_prestadora' => 'string',
        'codigo_tridigito' => 'int',
        'telefone_1' => 'string',
        'telefone_alternativo_1' => '?string',
        'telefone_alternativo_2' => '?string',
    ];
}
