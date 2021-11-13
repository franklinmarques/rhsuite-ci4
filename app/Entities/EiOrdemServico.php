<?php

namespace App\Entities;

class EiOrdemServico extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_contrato' => 'int',
        'nome' => 'string',
        'numero_empenho' => '?string',
        'ano' => 'int',
        'semestre' => 'bool',
        'escolas_nao_cadastradas' => '?string',
    ];
}
