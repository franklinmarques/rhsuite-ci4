<?php

namespace App\Entities;

class RecrutamentoFormacao extends AbstractEntity
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
        'id_escolaridade' => 'int',
        'curso' => '?string',
        'tipo' => '?string',
        'instituicao' => 'string',
        'ano_conclusao' => '?int',
        'status' => 'bool',
        'concluido' => 'int',
    ];
}
