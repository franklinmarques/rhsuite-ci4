<?php

namespace App\Entities;

class UsuarioExamePeriodico extends AbstractEntity
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
        'data_programada' => 'date',
        'data_realizacao' => '?date',
        'data_entrega' => '?date',
        'data_entrega_copia' => '?date',
        'local_exame' => '?string',
        'observacoes' => '?string',
    ];
}
