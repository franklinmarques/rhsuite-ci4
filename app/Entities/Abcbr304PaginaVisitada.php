<?php

namespace App\Entities;

class Abcbr304PaginaVisitada extends AbstractEntity
{
	protected $datamap = [];

	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
    
	protected $casts   = [
        'id' => 'int',
        'id_empresa' => '?int',
        'id_usuario' => 'int',
        'nome_usuario' => 'string',
        'tipo_usuario' => 'string',
        'url_pagina' => 'string',
        'data_hora_acesso' => 'timestamp',
        'data_hora_atualizacao' => '?timestamp',
    ];
}
