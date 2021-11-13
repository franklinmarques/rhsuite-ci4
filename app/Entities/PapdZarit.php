<?php

namespace App\Entities;

class PapdZarit extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_paciente' => 'int',
        'avaliador' => 'string',
        'pessoa_pesquisada' => '?string',
        'data_avaliacao' => 'date',
        'zarit' => '?int',
        'observacoes' => '?string',
        'assistencia_excessiva' => '?bool',
        'tempo_desperdicado' => '?bool',
        'estresse_cotidiano' => '?bool',
        'constrangimento_alheio' => '?bool',
        'influencia_negativa' => '?bool',
        'futuro_receoso' => '?bool',
        'dependencia' => '?bool',
        'impacto_saude' => '?bool',
        'perda_privacidade' => '?bool',
        'perda_vida_social' => '?bool',
        'dependencia_exclusiva' => '?bool',
        'tempo_desgaste' => '?bool',
        'perda_controle' => '?bool',
        'duvida_prestatividade' => '?bool',
        'expectativa_qualidade' => '?bool',
        'sobrecarga' => '?bool',
    ];
}
