<?php

namespace App\Entities;

class EiMedicaoMensal extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'ano' => 'int',
        'semestre' => 'bool',
        'mes' => 'int',
        'depto' => '?string',
        'id_diretoria' => '?int',
        'total_escolas' => 'int',
        'total_alunos' => 'int',
        'total_cuidadores' => 'int',
        'observacoes' => '?string',
    ];
}
