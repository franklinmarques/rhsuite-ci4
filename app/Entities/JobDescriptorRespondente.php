<?php

namespace App\Entities;

class JobDescriptorRespondente extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_descritor' => 'int',
        'id_usuario' => 'int',
        'sumario' => '?string',
        'formacao_experiencia' => '?string',
        'condicoes_gerais_exercicio' => '?string',
        'codigo_internacional_ciuo88' => '?string',
        'notas' => '?string',
        'recursos_trabalho' => '?string',
        'atividades' => '?string',
        'responsabilidades' => '?string',
        'conhecimentos_habilidades' => '?string',
        'habilidades_basicas' => '?string',
        'habilidades_intermediarias' => '?string',
        'habilidades_avancadas' => '?string',
        'ambiente_trabalho' => '?string',
        'condicoes_trabalho' => '?string',
        'esforcos_fisicos' => '?string',
        'grau_autonomia' => '?string',
        'grau_complexidade' => '?string',
        'grau_iniciativa' => '?string',
        'competencias_tecnicas' => '?string',
        'competencias_comportamentais' => '?string',
        'tempo_experiencia' => '?string',
        'formacao_minima' => '?string',
        'formacao_plena' => '?string',
        'esforcos_mentais' => '?string',
        'grau_pressao' => '?string',
        'campo_livre1' => '?string',
        'campo_livre2' => '?string',
        'campo_livre3' => '?string',
        'campo_livre4' => '?string',
        'campo_livre5' => '?string',
    ];
}
