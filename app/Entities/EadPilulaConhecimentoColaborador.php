<?php

namespace App\Entities;

class EadPilulaConhecimentoColaborador extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_pilula_conhecimento' => 'int',
        'id_usuario' => 'int',
    ];
}
