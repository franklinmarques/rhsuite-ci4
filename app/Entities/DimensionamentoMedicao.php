<?php

namespace App\Entities;

class DimensionamentoMedicao extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_executor' => 'int',
        'id_etapa' => 'int',
        'tempo_inicio' => 'decimal',
        'tempo_termino' => 'decimal',
        'tempo_gasto' => '?decimal',
        'quantidade' => '?decimal',
        'tempo_unidade' => '?decimal',
        'indice_mao_obra' => '?decimal',
        'complexidade' => '?int',
        'tipo_item' => '?int',
        'medicao_calculada' => 'bool',
        'valor_min_calculado' => '?decimal',
        'valor_medio_calculado' => '?decimal',
        'valor_max_calculado' => '?decimal',
        'mao_obra_min_calculada' => '?decimal',
        'mao_obra_media_calculada' => '?decimal',
        'mao_obra_max_calculada' => '?decimal',
        'status' => 'bool',
    ];
}
