<?php

namespace App\Entities;

class BibliotecaQuestao extends AbstractEntity
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
        'nome' => 'string',
        'tipo' => 'string',
        'conteudo' => 'string',
        'feedback_correta' => '?string',
        'feedback_incorreta' => '?string',
        'observacoes' => '?string',
        'aleatorizacao' => '?string',
        'id_copia' => '?int',
    ];
}
