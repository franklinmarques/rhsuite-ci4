<?php

namespace App\Entities;

class EadPilulaConhecimento extends AbstractEntity
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
        'id_curso' => 'int',
        'id_pilula_conhecimento_area' => '?int',
        'publico' => 'bool',
    ];
}
