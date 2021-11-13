<?php

namespace App\Entities;

class EiAlocacaoEscola extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_alocacao' => 'int',
        'id_os_escola' => '?int',
        'id_escola' => '?int',
        'codigo' => '?int',
        'escola' => 'string',
        'municipio' => 'string',
        'ordem_servico' => 'string',
        'contrato' => 'string',
    ];
}
