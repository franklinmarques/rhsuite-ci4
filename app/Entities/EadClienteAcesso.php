<?php

namespace App\Entities;

class EadClienteAcesso extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_curso_usuario' => 'int',
        'id_pagina' => 'int',
        'data_acesso' => 'datetime',
        'data_atualizacao' => '?datetime',
        'tempo_estudo' => '?time',
        'data_finalizacao' => '?datetime',
        'status' => 'int',
    ];
}
