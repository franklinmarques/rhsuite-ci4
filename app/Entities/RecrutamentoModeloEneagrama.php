<?php

namespace App\Entities;

class RecrutamentoModeloEneagrama extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'tipo_personalidade' => 'string',
        'tipo_eneagramatico' => 'string',
        'perfil_sensorial' => 'string',
        'nivel_interacao' => 'string',
        'ponto_foco' => 'string',
        'agentes_positivos' => 'string',
        'agentes_negativos' => 'string',
        'elemento_compulsivo' => 'string',
        'caracteristicas_positivas' => 'string',
        'caracteristicas_negativas' => 'string',
        'acao_prioritaria' => 'string',
        'vicios' => 'string',
        'desdobramentos_negativos' => 'string',
        'areas_atuacao' => 'string',
    ];
}
