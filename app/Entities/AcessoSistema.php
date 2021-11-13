<?php

namespace App\Entities;

class AcessoSistema extends AbstractEntity
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
        'tipo' => '?string',
        'data_acesso' => 'timestamp',
        'data_atualizacao' => '?timestamp',
        'data_saida' => '?timestamp',
        'endereco_ip' => '?string',
        'agente_usuario' => '?string',
        'id_sessao' => '?string',
    ];
}
