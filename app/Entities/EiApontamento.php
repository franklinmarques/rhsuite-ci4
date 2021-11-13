<?php

namespace App\Entities;

class EiApontamento extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_alocado' => 'int',
        'data' => 'date',
        'id_horario' => '?int',
        'periodo' => '?bool',
        'horario_inicio' => '?time',
        'status' => 'string',
        'id_usuario' => '?int',
        'id_alocado_sub1' => '?int',
        'id_alocado_sub2' => '?int',
        'horario_entrada_1' => '?datetime',
        'horario_saida_1' => '?datetime',
        'substituto_horario_1' => '?bool',
        'horario_entrada_2' => '?datetime',
        'horario_saida_2' => '?datetime',
        'substituto_horario_2' => '?bool',
        'horario_entrada_3' => '?datetime',
        'horario_saida_3' => '?datetime',
        'substituto_horario_3' => '?bool',
        'desconto' => '?time',
        'desconto_1' => '?time',
        'desconto_2' => '?time',
        'desconto_3' => '?time',
        'desconto_sub1' => '?time',
        'desconto_sub2' => '?time',
        'observacoes' => '?string',
        'ocorrencia_cuidador_aluno' => '?string',
        'ocorrencia_professor' => '?string',
        'criado_em' => '?timestamp',
        'atualizado_em' => '?timestamp',
    ];
}
