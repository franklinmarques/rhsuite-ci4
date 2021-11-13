<?php

namespace App\Entities;

class EiUsuariosFrequencia extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_usuario' => 'int',
        'data_evento' => 'date',
        'periodo_atual' => 'bool',
        'horario_entrada_1' => '?time',
        'horario_entrada_real_1' => '?datetime',
        'horario_saida_1' => '?time',
        'horario_saida_real_1' => '?datetime',
        'horario_entrada_2' => '?time',
        'horario_entrada_real_2' => '?datetime',
        'horario_saida_2' => '?time',
        'horario_saida_real_2' => '?datetime',
        'horario_entrada_3' => '?time',
        'horario_entrada_real_3' => '?datetime',
        'horario_saida_3' => '?time',
        'horario_saida_real_3' => '?datetime',
        'observacoes' => '?string',
        'justificativa' => '?string',
        'avaliacao_justificativa' => '?string',
        'status_justificativa' => '?bool',
        'id_escola' => '?int',
        'alunos' => '?int',
        'status_entrada_1' => '?string',
        'status_entrada_2' => '?string',
        'status_entrada_3' => '?string',
        'status_saida_1' => '?string',
        'status_saida_2' => '?string',
        'status_saida_3' => '?string',
        'automatico_entrada_1' => '?bool',
        'automatico_saida_1' => '?bool',
        'automatico_entrada_2' => '?bool',
        'automatico_saida_2' => '?bool',
        'automatico_entrada_3' => '?bool',
        'automatico_saida_3' => '?bool',
        'criado_em' => '?timestamp',
        'atualizado_em' => '?timestamp',
    ];
}
