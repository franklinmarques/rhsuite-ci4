<?php

namespace App\Entities;

class PapdMedicao extends AbstractEntity
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
        'ano' => 'int',
        'mes' => 'int',
        'total_pacientes_cadastrados' => 'int',
        'total_pacientes_inativos' => 'int',
        'total_pacientes_monitorados' => 'int',
    ];
}
