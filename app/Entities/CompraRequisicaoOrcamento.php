<?php

namespace App\Entities;

class CompraRequisicaoOrcamento extends AbstractEntity
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
        'status' => 'string',
        'prioridade' => 'int',
        'data_desejada' => '?date',
        'data_abertura' => 'date',
        'data_recebimento' => '?date',
        'data_encerramento' => '?date',
        'id_depto' => 'int',
        'id_area' => 'int',
        'id_setor' => 'int',
        'id_requisitante' => 'int',
        'itens_solicitados' => 'string',
        'sugestao_fornecedor_preferencial' => '?string',
    ];
}
