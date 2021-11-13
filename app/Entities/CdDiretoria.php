<?php

namespace App\Entities;

class CdDiretoria extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'nome' => 'string',
        'alias' => '?string',
        'id_empresa' => 'int',
        'depto' => 'string',
        'municipio' => 'string',
        'contrato' => 'string',
        'id_coordenador' => '?int',
    ];
}
