<?php

namespace App\Entities;

class EiLivroAta extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_usuario_frequencia' => '?int',
        'id_usuario' => 'int',
        'data' => 'date',
        'periodo' => '?bool',
        'periodo_relatorio' => '?string',
        'data_inicio_periodo' => '?date',
        'data_termino_periodo' => '?date',
        'profissional' => '?string',
        'alunos' => '?string',
        'curso' => '?string',
        'modulo' => '?string',
        'escola' => '?string',
        'atividades_realizadas' => '?string',
        'dificuldades_encontradas' => '?string',
        'sugestoes_observacoes' => '?string',
        'id_alocado' => '?int',
    ];
}
