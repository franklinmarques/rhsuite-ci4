<?php

namespace App\Entities;

class IcomAlocacaoMediaDesempenho extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id_alocacao' => 'int',
        'comprometimento' => '?decimal',
        'pontualidade' => '?decimal',
        'script' => '?decimal',
        'simpatia' => '?decimal',
        'empatia' => '?decimal',
        'postura' => '?decimal',
        'ferramenta' => '?decimal',
        'tradutorio' => '?decimal',
        'linguistico' => '?decimal',
        'neutralidade' => '?decimal',
        'discricao' => '?decimal',
        'fidelidade' => '?decimal',
        'tempo_medio' => '?time',
        'qtde_atendidas' => '?decimal',
        'qtde_recusadas' => '?decimal',
        'taxa_ocupacao' => '?decimal',
        'taxa_absenteismo' => '?decimal',
        'qtde_reclamacoes' => '?decimal',
        'extra_1' => '?decimal',
        'extra_2' => '?decimal',
        'extra_3' => '?decimal',
        'total_comportamento_performance' => '?decimal',
        'total_monitoria_qualidade' => '?decimal',
        'total_desempenho_quantitativo' => '?decimal',
        'total' => '?decimal',
    ];
}
