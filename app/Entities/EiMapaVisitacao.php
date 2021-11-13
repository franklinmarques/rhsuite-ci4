<?php

namespace App\Entities;

class EiMapaVisitacao extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_mapa_unidade' => 'int',
        'tipo_atividade' => '?string',
        'data_visita' => 'date',
        'data_visita_anterior' => '?date',
        'id_supervisor_visitante' => '?int',
        'supervisor_visitante' => '?string',
        'cliente' => '?int',
        'municipio' => '?string',
        'escola' => '?string',
        'unidade_visitada' => '?int',
        'prestadores_servicos_tratados' => '?string',
        'coordenador_responsavel' => '?int',
        'motivo_visita' => '?int',
        'gastos_materiais' => '?decimal',
        'sumario_visita' => '?string',
        'observacoes' => '?string',
    ];
}
