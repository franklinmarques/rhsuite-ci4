<?php

namespace App\Entities;

class CdEscola extends AbstractEntity
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
        'id_diretoria' => 'int',
        'endereco' => '?string',
        'numero' => '?int',
        'complemento' => '?string',
        'bairro' => '?string',
        'municipio' => 'string',
        'telefone' => '?string',
        'telefone_contato' => '?string',
        'email' => '?string',
        'cep' => '?string',
        'periodo_manha' => '?int',
        'periodo_tarde' => '?int',
        'periodo_noite' => '?int',
    ];
}
