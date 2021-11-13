<?php

namespace App\Entities;

class RequisicaoPessoalEstagio extends AbstractEntity
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
        'nome' => 'string',
        'destino_email' => 'string',
        'email_responsavel' => 'string',
        'mensagem' => '?string',
    ];
}
