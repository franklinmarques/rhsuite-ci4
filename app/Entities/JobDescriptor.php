<?php

namespace App\Entities;

class JobDescriptor extends AbstractEntity
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
        'id_cargo' => 'int',
        'id_funcao' => 'int',
        'versao' => 'string',
        'data' => 'timestamp',
        'sumario' => 'bool',
        'formacao_experiencia' => 'bool',
        'condicoes_gerais_exercicio' => 'bool',
        'codigo_internacional_ciuo88' => 'bool',
        'notas' => 'bool',
        'recursos_trabalho' => 'bool',
        'atividades' => 'bool',
        'responsabilidades' => 'bool',
        'conhecimentos_habilidades' => 'bool',
        'habilidades_basicas' => 'bool',
        'habilidades_intermediarias' => 'bool',
        'habilidades_avancadas' => 'bool',
        'ambiente_trabalho' => 'bool',
        'condicoes_trabalho' => 'bool',
        'esforcos_fisicos' => 'bool',
        'grau_autonomia' => 'bool',
        'grau_complexidade' => 'bool',
        'grau_iniciativa' => 'bool',
        'competencias_tecnicas' => 'bool',
        'competencias_comportamentais' => 'bool',
        'tempo_experiencia' => 'bool',
        'formacao_minima' => 'bool',
        'formacao_plena' => 'bool',
        'esforcos_mentais' => 'bool',
        'grau_pressao' => 'bool',
        'campo_livre1' => '?string',
        'campo_livre2' => '?string',
        'campo_livre3' => '?string',
        'campo_livre4' => '?string',
        'campo_livre5' => '?string',
        'id_versao_anterior' => '?int',
        'versao_homologada' => '?bool',
    ];
}
