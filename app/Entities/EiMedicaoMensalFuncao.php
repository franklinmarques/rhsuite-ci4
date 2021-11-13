<?php

namespace App\Entities;

class EiMedicaoMensalFuncao extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_medicao_mensal' => 'int',
        'cargo' => 'string',
        'funcao' => 'string',
        'total_pessoas' => 'int',
        'total_horas' => 'string',
        'receita_efetuada' => 'decimal',
        'pagamentos_efetuados' => 'decimal',
        'resultado_monetario' => 'decimal',
        'resultado_percentual' => 'decimal',
    ];
}
