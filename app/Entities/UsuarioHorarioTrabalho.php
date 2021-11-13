<?php

namespace App\Entities;

class UsuarioHorarioTrabalho extends AbstractEntity
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
        'turno' => 'string',
        'domingo' => '?bool',
        'segunda_feira' => '?bool',
        'terca_feira' => '?bool',
        'quarta_feira' => '?bool',
        'quinta_feira' => '?bool',
        'sexta_feira' => '?bool',
        'sabado' => '?bool',
        'horas_dia' => 'time',
        'minutos_descanso_dia' => '?time',
        'horario_entrada' => 'time',
        'horario_intervalo' => '?time',
        'horario_retorno' => '?time',
        'horario_saida' => 'time',
        'sem_intervalo' => '?bool',
        'data_cadastro' => 'date',
        'data_edicao' => '?date',
    ];
}
