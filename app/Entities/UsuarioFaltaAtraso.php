<?php

namespace App\Entities;

class UsuarioFaltaAtraso extends AbstractEntity
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
        'id_colaborador' => '?int',
        'id_depto' => 'int',
        'id_area' => 'int',
        'id_setor' => 'int',
        'data' => 'date',
        'falta' => '?bool',
        'horas_atraso' => '?time',
        'id_colaborador_sub' => '?int',
        'status' => 'string',
        'glosa_horas' => '?time',
        'glosa_dias' => '?int',
        'horario_entrada' => '?time',
        'horario_intervalo' => '?time',
        'horario_retorno' => '?time',
        'horario_saida' => '?time',
        'apontamento_positivo' => '?time',
        'apontamento_negativo' => '?time',
        'desconto_folha' => '?time',
        'id_detalhes' => '?int',
        'observacoes' => '?string',
    ];
}
