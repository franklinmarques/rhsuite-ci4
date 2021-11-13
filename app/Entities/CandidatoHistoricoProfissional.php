<?php

namespace App\Entities;

class CandidatoHistoricoProfissional extends AbstractEntity
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
        'instituicao' => 'string',
        'data_entrada' => 'date',
        'data_saida' => '?date',
        'cargo_entrada' => 'string',
        'cargo_saida' => '?string',
        'salario_entrada' => 'decimal',
        'salario_saida' => '?decimal',
        'motivo_saida' => '?string',
        'realizacoes' => '?string',
    ];
}
