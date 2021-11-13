<?php

namespace App\Entities;

class Biblioteca extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_usuario' => 'int',
        'tipo' => 'int',
        'id_categoria' => 'int',
        'titulo' => 'string',
        'descricao' => 'string',
        'link' => 'string',
        'disciplina' => 'string',
        'ano_serie' => 'string',
        'tema_curricular' => 'string',
        'uso' => 'string',
        'licenca' => 'string',
        'produzido_por' => 'string',
        'tags' => 'string',
        'data_cadastro' => 'datetime',
        'data_editado' => 'datetime',
    ];
}
