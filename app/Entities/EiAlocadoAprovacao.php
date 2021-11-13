<?php

namespace App\Entities;

class EiAlocadoAprovacao extends AbstractEntity
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
        'cargo' => '?string',
        'funcao' => '?string',
        'mes_referencia' => 'int',
        'data_hora_envio_solicitacao' => '?datetime',
        'data_hora_aprovacao_escola' => '?datetime',
        'nome_aprovador_escola' => '?string',
        'status_aprovacao_escola' => '?bool',
        'observacoes_escola' => '?string',
        'data_hora_aprovacao_cps' => '?datetime',
        'nome_aprovador_cps' => '?string',
        'status_aprovacao_cps' => '?bool',
        'observacoes_cps' => '?string',
        'tipo_arquivo' => '?string',
        'assinatura_digital' => '?string',
        'arquivo_medicao' => '?string',
        'id_aprovacao_coordenador' => '?int',
    ];
}
