<?php

namespace App\Entities;

class DimensionamentoEquipeMembro extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_equipe' => 'int',
        'id_usuario' => 'int',
    ];
}
