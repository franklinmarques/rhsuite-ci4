<?php

namespace App\Entities;

class EadClienteTreinamento extends AbstractEntity
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
        'id_curso' => '?int',
        'data_cadastro' => 'datetime',
        'data_inicio' => '?datetime',
        'data_maxima' => '?datetime',
        'colaboradores_maximo' => '?int',
        'nota_aprovacao' => '?int',
        'tipo_treinamento' => '?string',
        'local_treinamento' => '?string',
        'nome' => '?string',
        'carga_horaria_presencial' => '?time',
        'avaliacao_presencial' => '?int',
        'nome_fornecedor' => '?string',
    ];
}
