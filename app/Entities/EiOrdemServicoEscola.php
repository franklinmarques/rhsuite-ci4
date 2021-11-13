<?php

namespace App\Entities;

class EiOrdemServicoEscola extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_ordem_servico' => 'int',
        'id_escola' => 'int',
    ];
}
