<?php

namespace App\Entities;

class PapdAtendimento extends AbstractEntity
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
        'id_paciente' => 'int',
        'id_atividade' => 'int',
        'data_atendimento' => 'datetime',
    ];
}
