<?php

namespace App\Models;

use App\Entities\EiOrdemServicoHorario;

class EiOrdemServicoHorarioModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_ordens_servico_horarios';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiOrdemServicoHorario::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_os_profissional',
        'id_funcao',
        'id_os_profissional_sub1',
        'id_funcao_sub1',
        'data_substituicao1',
        'id_os_profissional_sub2',
        'id_funcao_sub2',
        'data_substituicao2',
        'dia_semana',
        'periodo',
        'horario_inicio',
        'horario_termino',
        'total_dias_mes1',
        'total_dias_mes2',
        'total_dias_mes3',
        'total_dias_mes4',
        'total_dias_mes5',
        'total_dias_mes6',
        'valor_hora',
        'horas_diarias',
        'qtde_dias',
        'horas_semanais',
        'qtde_semanas',
        'horas_mensais',
        'horas_semestre',
        'valor_hora_mensal',
        'valor_hora_operacional',
        'horas_mensais_custo',
        'data_inicio_contrato',
        'data_termino_contrato',
        'desconto_mensal_1',
        'desconto_mensal_2',
        'desconto_mensal_3',
        'desconto_mensal_4',
        'desconto_mensal_5',
        'desconto_mensal_6',
        'valor_mensal_1',
        'valor_mensal_2',
        'valor_mensal_3',
        'valor_mensal_4',
        'valor_mensal_5',
        'valor_mensal_6',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_os_profissional'        => 'required|is_natural_no_zero|max_length[11]',
        'id_funcao'                 => 'is_natural_no_zero|max_length[11]',
        'id_os_profissional_sub1'   => 'integer|max_length[11]',
        'id_funcao_sub1'            => 'integer|max_length[11]',
        'data_substituicao1'        => 'valid_date',
        'id_os_profissional_sub2'   => 'integer|max_length[11]',
        'id_funcao_sub2'            => 'integer|max_length[11]',
        'data_substituicao2'        => 'valid_date',
        'dia_semana'                => 'integer|max_length[1]',
        'periodo'                   => 'integer|exact_length[1]',
        'horario_inicio'            => 'valid_time',
        'horario_termino'           => 'valid_time',
        'total_dias_mes1'           => 'integer|max_length[1]',
        'total_dias_mes2'           => 'integer|max_length[1]',
        'total_dias_mes3'           => 'integer|max_length[1]',
        'total_dias_mes4'           => 'integer|max_length[1]',
        'total_dias_mes5'           => 'integer|max_length[1]',
        'total_dias_mes6'           => 'integer|max_length[1]',
        'valor_hora'                => 'numeric|max_length[10]',
        'horas_diarias'             => 'numeric|max_length[5]',
        'qtde_dias'                 => 'integer|max_length[4]',
        'horas_semanais'            => 'numeric|max_length[5]',
        'qtde_semanas'              => 'integer|max_length[1]',
        'horas_mensais'             => 'numeric|max_length[6]',
        'horas_semestre'            => 'numeric|max_length[10]',
        'valor_hora_mensal'         => 'numeric|max_length[10]',
        'valor_hora_operacional'    => 'numeric|max_length[10]',
        'horas_mensais_custo'       => 'valid_time',
        'data_inicio_contrato'      => 'valid_date',
        'data_termino_contrato'     => 'valid_date',
        'desconto_mensal_1'         => 'numeric|max_length[6]',
        'desconto_mensal_2'         => 'numeric|max_length[6]',
        'desconto_mensal_3'         => 'numeric|max_length[6]',
        'desconto_mensal_4'         => 'numeric|max_length[6]',
        'desconto_mensal_5'         => 'numeric|max_length[6]',
        'desconto_mensal_6'         => 'numeric|max_length[6]',
        'valor_mensal_1'            => 'numeric|max_length[10]',
        'valor_mensal_2'            => 'numeric|max_length[10]',
        'valor_mensal_3'            => 'numeric|max_length[10]',
        'valor_mensal_4'            => 'numeric|max_length[10]',
        'valor_mensal_5'            => 'numeric|max_length[10]',
        'valor_mensal_6'            => 'numeric|max_length[10]',
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
