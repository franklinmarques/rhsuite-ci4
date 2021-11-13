<?php

namespace App\Entities;

class PesquisaJungEstilo extends AbstractEntity
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
        'nome' => 'string',
        'laudo_comportamental_padrao' => '?string',
        'perfil_preponderante' => 'string',
        'atitude_primaria' => 'string',
        'atitude_secundaria' => 'string',
    ];
}
