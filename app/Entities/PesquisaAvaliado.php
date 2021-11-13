<?php

namespace App\Entities;

class PesquisaAvaliado extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_pesquisa' => 'int',
        'id_avaliado' => 'int',
    ];
}
