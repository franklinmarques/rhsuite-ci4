<?php

namespace App\Entities;

class IcomAlocadoFeedback extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_usuario' => '?int',
        'id_alocacao' => '?int',
        'id_alocado' => '?int',
        'id_usuario_orientador' => '?int',
        'nome_usuario_orientador' => 'string',
        'data' => 'date',
        'descricao' => '?string',
        'avaliacao_desempenho' => '?string',
        'plano_desenvolimento_melhorias' => '?string',
        'resultado' => '?string',
    ];
}
