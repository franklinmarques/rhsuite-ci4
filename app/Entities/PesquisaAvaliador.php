<?php

namespace App\Entities;

class PesquisaAvaliador extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_pesquisa' => 'int',
        'id_avaliador' => 'int',
        'id_avaliado' => '?int',
        'data_acesso' => '?datetime',
        'data_finalizacao' => '?datetime',
        'estilo_personalidade_majoritario' => '?string',
        'estilo_personalidade_secundario' => '?string',
        'laudo_comportamental_padrao' => '?string',
    ];
}
