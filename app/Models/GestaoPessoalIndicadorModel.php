<?php

namespace App\Models;

use App\Entities\GestaoPessoalIndicador;

class GestaoPessoalIndicadorModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'gestao_pessoal_indicadores';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = GestaoPessoalIndicador::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'mes',
        'ano',
        'total_colaboradores_ativos',
        'total_colaboradores_admitidos',
        'total_colaboradores_demitidos',
        'total_colaboradores_justa_causa',
        'total_colaboradores_desligados',
        'total_temporarios_em_6_meses',
        'total_acidentes',
        'total_maternidade',
        'total_aposentadoria',
        'total_doenca',
        'total_faltas_st',
        'total_faltas_cd',
        'total_faltas_gp',
        'total_faltas_cdh',
        'total_faltas_icom',
        'total_faltas_adm',
        'total_faltas_prj',
        'total_colaboradores',
        'total_atrasos_4_horas',
        'total_atrasos_8_horas',
        'total_faltas_1_dia',
        'total_faltas_2_dias',
        'total_faltas_3_dias',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'                        => 'required|is_natural_no_zero|max_length[11]',
        'mes'                               => 'required|integer|max_length[2]',
        'ano'                               => 'required|int|max_length[4]',
        'total_colaboradores_ativos'        => 'integer|max_length[11]',
        'total_colaboradores_admitidos'     => 'integer|max_length[11]',
        'total_colaboradores_demitidos'     => 'integer|max_length[11]',
        'total_colaboradores_justa_causa'   => 'integer|max_length[11]',
        'total_colaboradores_desligados'    => 'integer|max_length[11]',
        'total_temporarios_em_6_meses'      => 'integer|max_length[11]',
        'total_acidentes'                   => 'integer|max_length[11]',
        'total_maternidade'                 => 'integer|max_length[11]',
        'total_aposentadoria'               => 'integer|max_length[11]',
        'total_doenca'                      => 'integer|max_length[11]',
        'total_faltas_st'                   => 'integer|max_length[11]',
        'total_faltas_cd'                   => 'integer|max_length[11]',
        'total_faltas_gp'                   => 'integer|max_length[11]',
        'total_faltas_cdh'                  => 'integer|max_length[11]',
        'total_faltas_icom'                 => 'integer|max_length[11]',
        'total_faltas_adm'                  => 'integer|max_length[11]',
        'total_faltas_prj'                  => 'integer|max_length[11]',
        'total_colaboradores'               => 'integer|max_length[11]',
        'total_atrasos_4_horas'             => 'integer|max_length[11]',
        'total_atrasos_8_horas'             => 'integer|max_length[11]',
        'total_faltas_1_dia'                => 'integer|max_length[11]',
        'total_faltas_2_dias'               => 'integer|max_length[11]',
        'total_faltas_3_dias'               => 'integer|max_length[11]',
    ];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = [];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];
}
