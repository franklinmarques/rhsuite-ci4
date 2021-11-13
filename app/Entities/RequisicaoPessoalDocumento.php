<?php

namespace App\Entities;

class RequisicaoPessoalDocumento extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_candidato' => 'int',
        'nome_arquivo' => 'string',
        'tipo_arquivo' => 'string',
        'data_upload' => 'datetime',
    ];
}
