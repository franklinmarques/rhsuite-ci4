<?php

namespace App\Entities;

class EadCurso extends AbstractEntity
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
        'id_empresa' => 'int',
        'publico' => 'int',
        'gratuito' => 'int',
        'descricao' => '?string',
        'data_cadastro' => 'datetime',
        'data_editado' => '?datetime',
        'horas_duracao' => 'int',
        'objetivos' => '?string',
        'competencias_genericas' => '?string',
        'competencias_especificas' => '?string',
        'competencias_comportamentais' => '?string',
        'categoria' => '?string',
        'id_categoria' => '?int',
        'area_conhecimento' => '?string',
        'id_area' => '?int',
        'consultor' => '?string',
        'foto_consultor' => '?string',
        'curriculo' => '?string',
        'foto_treinamento' => '?string',
        'pre_requisitos' => '?string',
        'progressao_linear' => 'int',
        'status' => 'int',
        'id_copia' => '?int',
    ];
}
