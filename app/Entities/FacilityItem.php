<?php

namespace App\Entities;

class FacilityItem extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_sala' => 'int',
        'ativo' => 'bool',
        'nome' => 'string',
        'codigo' => '?string',
        'tipo' => '?string',
        'data_entrada_operacao' => '?date',
        'anos_duracao' => '?int',
        'periodicidade_vistoria' => '?string',
        'mes_vistoria_jan' => '?bool',
        'mes_vistoria_fev' => '?bool',
        'mes_vistoria_mar' => '?bool',
        'mes_vistoria_abr' => '?bool',
        'mes_vistoria_mai' => '?bool',
        'mes_vistoria_jun' => '?bool',
        'mes_vistoria_jul' => '?bool',
        'mes_vistoria_ago' => '?bool',
        'mes_vistoria_set' => '?bool',
        'mes_vistoria_out' => '?bool',
        'mes_vistoria_nov' => '?bool',
        'mes_vistoria_dez' => '?bool',
        'periodicidade_manutencao' => '?string',
        'mes_manutencao_jan' => '?bool',
        'mes_manutencao_fev' => '?bool',
        'mes_manutencao_mar' => '?bool',
        'mes_manutencao_abr' => '?bool',
        'mes_manutencao_mai' => '?bool',
        'mes_manutencao_jun' => '?bool',
        'mes_manutencao_jul' => '?bool',
        'mes_manutencao_ago' => '?bool',
        'mes_manutencao_set' => '?bool',
        'mes_manutencao_out' => '?bool',
        'mes_manutencao_nov' => '?bool',
        'mes_manutencao_dez' => '?bool',
    ];
}
