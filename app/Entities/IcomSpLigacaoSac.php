<?php

namespace App\Entities;

class IcomSpLigacaoSac extends AbstractEntity
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
        'data' => 'date',
        'nome_empresa' => 'string',
        'protocolo' => 'string',
        'telefone' => '?string',
        'atendimento' => 'string',
        'tipo_servico' => 'string',
        'privado' => 'bool',
    ];
}
