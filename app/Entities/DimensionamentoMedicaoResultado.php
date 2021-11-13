<?php

namespace App\Entities;

class DimensionamentoMedicaoResultado extends AbstractEntity
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
        'id_usuario' => 'int',
        'id_crono_analise' => 'int',
        'id_executor' => 'int',
        'id_processo' => '?int',
        'id_atividade' => '?int',
        'id_etapa' => '?int',
        'grau_complexidade' => '?bool',
        'tamanho_item' => '?bool',
        'soma_menor' => 'decimal',
        'soma_media' => 'decimal',
        'soma_maior' => 'decimal',
        'mao_obra_menor' => 'decimal',
        'mao_obra_media' => 'decimal',
        'mao_obra_maior' => 'decimal',
        'data_cadastro' => 'datetime',
    ];
}
