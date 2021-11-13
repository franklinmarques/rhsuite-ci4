<?php

namespace App\Entities;

class GestaoPessoalIndicador extends AbstractEntity
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
        'mes' => 'int',
        'ano' => 'int',
        'total_colaboradores_ativos' => '?int',
        'total_colaboradores_admitidos' => '?int',
        'total_colaboradores_demitidos' => '?int',
        'total_colaboradores_justa_causa' => '?int',
        'total_colaboradores_desligados' => '?int',
        'total_temporarios_em_6_meses' => '?int',
        'total_acidentes' => '?int',
        'total_maternidade' => '?int',
        'total_aposentadoria' => '?int',
        'total_doenca' => '?int',
        'total_faltas_st' => '?int',
        'total_faltas_cd' => '?int',
        'total_faltas_gp' => '?int',
        'total_faltas_cdh' => '?int',
        'total_faltas_icom' => '?int',
        'total_faltas_adm' => '?int',
        'total_faltas_prj' => '?int',
        'total_colaboradores' => '?int',
        'total_atrasos_4_horas' => '?int',
        'total_atrasos_8_horas' => '?int',
        'total_faltas_1_dia' => '?int',
        'total_faltas_2_dias' => '?int',
        'total_faltas_3_dias' => '?int',
    ];
}
