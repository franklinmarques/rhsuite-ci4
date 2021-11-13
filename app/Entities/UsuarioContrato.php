<?php

namespace App\Entities;

class UsuarioContrato extends AbstractEntity
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
        'data_assinatura' => 'date',
        'id_depto' => 'int',
        'id_area' => 'int',
        'id_setor' => 'int',
        'id_cargo' => 'int',
        'id_funcao' => 'int',
        'contrato' => '?string',
        'valor_posto' => 'decimal',
        'conversor_dia' => '?decimal',
        'conversor_hora' => '?decimal',
    ];
}
