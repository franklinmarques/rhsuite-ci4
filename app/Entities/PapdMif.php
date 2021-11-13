<?php

namespace App\Entities;

class PapdMif extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_paciente' => 'int',
        'avaliador' => 'string',
        'data_avaliacao' => 'date',
        'mif' => '?int',
        'observacoes' => '?string',
        'alimentacao' => '?bool',
        'arrumacao' => '?bool',
        'banho' => '?bool',
        'vestimenta_superior' => '?bool',
        'vestimenta_inferior' => '?bool',
        'higiene_pessoal' => '?bool',
        'controle_vesical' => '?bool',
        'controle_intestinal' => '?bool',
        'leito_cadeira' => '?bool',
        'sanitario' => '?bool',
        'banheiro_chuveiro' => '?bool',
        'marcha' => '?bool',
        'cadeira_rodas' => '?bool',
        'escadas' => '?bool',
        'compreensao_ambas' => '?bool',
        'compreensao_visual' => '?bool',
        'expressao_verbal' => '?bool',
        'expressao_nao_verbal' => '?bool',
        'interacao_social' => '?bool',
        'resolucao_problemas' => '?bool',
        'memoria' => '?bool',
    ];
}
