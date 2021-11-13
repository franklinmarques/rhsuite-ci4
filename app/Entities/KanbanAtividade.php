<?php

namespace App\Entities;

class KanbanAtividade extends AbstractEntity
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
        'id_quadro' => 'int',
        'id_usuario_responsavel' => 'int',
        'nome' => 'string',
        'descricao' => '?string',
        'ordem' => 'int',
        'status' => 'string',
        'id_etapa_atual' => 'int',
        'data_limite' => '?datetime',
        'tempo_estimado' => '?int',
        'tempo_gasto' => '?time',
        'data_criacao' => 'datetime',
        'data_fechamento' => '?datetime',
    ];
}
