<?php

namespace App\Entities;

class EiDiretoriaCurso extends AbstractEntity
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
        'alias' => '?string',
        'id_empresa' => 'int',
        'depto' => 'string',
        'municipio' => 'string',
        'telefone' => '?string',
        'id_coordenador' => '?int',
        'nome_supervisor' => '?string',
        'email_supervisor' => '?string',
        'nome_coordenador' => '?string',
        'email_coordenador' => '?string',
        'nome_administrativo' => '?string',
        'email_administrativo' => '?string',
        'depto_cliente' => '?string',
        'cargo_coordenador' => '?string',
        'cargo_supervisor' => '?string',
        'assinatura_digital_coordenador' => '?string',
        'senha_exclusao' => '?string',
    ];
}
