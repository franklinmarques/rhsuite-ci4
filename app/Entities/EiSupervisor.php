<?php

namespace App\Entities;

class EiSupervisor extends AbstractEntity
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
        'depto' => 'int',
        'area' => 'int',
        'setor' => 'int',
        'ano' => 'int',
        'semestre' => 'bool',
        'carga_horaria' => '?time',
        'saldo_acumulado_horas' => '?string',
        'is_coordenador' => '?bool',
        'is_supervisor' => '?bool',
    ];
}
