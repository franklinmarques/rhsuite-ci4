<?php

namespace App\Entities;

class RequisicaoPessoalEmail extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_empresa' => '?int',
        'colaborador' => 'string',
        'email' => 'string',
        'tipo_usuario' => 'int',
        'tipo_email' => '?int',
    ];
}
