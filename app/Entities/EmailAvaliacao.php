<?php

namespace App\Entities;

class EmailAvaliacao extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_avaliacao' => 'int',
        'texto_inicio' => 'string',
        'texto_cobranca' => 'string',
        'texto_fim' => 'string',
    ];
}
