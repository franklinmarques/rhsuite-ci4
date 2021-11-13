<?php

namespace App\Entities;

class RecrutamentoModeloPreferencia extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'indice' => 'string',
        'descricao' => 'string',
        'tipo_resultado' => 'string',
        'caracteristicas_principais' => 'string',
        'tracos_comportamentais' => 'string',
        'pontos_fortes_titulo' => 'string',
        'pontos_fortes_descricao' => 'string',
        'pontos_melhoria_titulo' => 'string',
        'pontos_melhoria_descricao' => 'string',
        'motivacoes' => 'string',
        'valores' => 'string',
    ];
}
