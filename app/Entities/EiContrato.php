<?php

namespace App\Entities;

class EiContrato extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_cliente' => 'int',
        'contrato' => 'string',
        'data_inicio' => 'date',
        'data_termino' => 'date',
        'data_reajuste1' => '?date',
        'indice_reajuste1' => '?decimal',
        'data_reajuste2' => '?date',
        'indice_reajuste2' => '?decimal',
        'data_reajuste3' => '?date',
        'indice_reajuste3' => '?decimal',
        'data_reajuste4' => '?date',
        'indice_reajuste4' => '?decimal',
        'data_reajuste5' => '?date',
        'indice_reajuste5' => '?decimal',
        'minutos_tolerancia_entrada_saida' => '?int',
        'horario_padrao_banda_morta' => '?bool',
    ];
}
