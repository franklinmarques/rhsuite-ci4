<?php

namespace App\Entities;

class IcomSpTelefoneUtilServico extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_cliente' => 'int',
        'nome' => 'string',
        'telefone_1' => '?string',
        'telefone_2' => '?string',
        'url' => '?string',
        'observacoes' => '?string',
        'campo_extra' => '?string',
    ];
}
