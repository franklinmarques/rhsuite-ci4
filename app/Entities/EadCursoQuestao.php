<?php

namespace App\Entities;

class EadCursoQuestao extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'nome' => 'string',
        'id_pagina' => 'int',
        'tipo' => '?string',
        'conteudo' => '?string',
        'feedback_correta' => '?string',
        'feedback_incorreta' => '?string',
        'observacoes' => '?string',
        'aleatorizacao' => '?string',
        'id_biblioteca' => '?int',
        'id_copia' => '?int',
    ];
}
