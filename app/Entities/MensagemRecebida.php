<?php

namespace App\Entities;

class MensagemRecebida extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_remetente' => 'int',
        'id_destinatario' => 'int',
        'titulo' => '?string',
        'mensagem' => 'string',
        'anexo' => '?string',
        'data_cadastro' => 'datetime',
        'status' => 'int',
    ];
}
