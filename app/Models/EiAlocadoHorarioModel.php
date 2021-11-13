<?php

namespace App\Models;

use App\Entities\EiAlocadoHorario;

class EiAlocadoHorarioModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_alocados_horarios';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiAlocadoHorario::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_alocado',
        'id_os_horario',
        'dia_semana',
        'periodo',
        'cargo',
        'cargo_mes2',
        'cargo_mes3',
        'cargo_mes4',
        'cargo_mes5',
        'cargo_mes6',
        'cargo_mes7',
        'funcao',
        'funcao_mes2',
        'funcao_mes3',
        'funcao_mes4',
        'funcao_mes5',
        'funcao_mes6',
        'funcao_mes7',
        'horario_inicio_mes1',
        'horario_inicio_mes2',
        'horario_inicio_mes3',
        'horario_inicio_mes4',
        'horario_inicio_mes5',
        'horario_inicio_mes6',
        'horario_inicio_mes7',
        'horario_termino_mes1',
        'horario_termino_mes2',
        'horario_termino_mes3',
        'horario_termino_mes4',
        'horario_termino_mes5',
        'horario_termino_mes6',
        'horario_termino_mes7',
        'total_horas_mes1',
        'total_horas_mes2',
        'total_horas_mes3',
        'total_horas_mes4',
        'total_horas_mes5',
        'total_horas_mes6',
        'total_horas_mes7',
        'total_semanas_mes1',
        'total_semanas_mes2',
        'total_semanas_mes3',
        'total_semanas_mes4',
        'total_semanas_mes5',
        'total_semanas_mes6',
        'total_semanas_mes7',
        'total_semanas_sub1',
        'total_semanas_sub2',
        'desconto_mes1',
        'desconto_mes2',
        'desconto_mes3',
        'desconto_mes4',
        'desconto_mes5',
        'desconto_mes6',
        'desconto_mes7',
        'desconto_sub1',
        'desconto_sub2',
        'endosso_mes1',
        'endosso_mes2',
        'endosso_mes3',
        'endosso_mes4',
        'endosso_mes5',
        'endosso_mes6',
        'endosso_mes7',
        'endosso_sub1',
        'endosso_sub2',
        'total_mes1',
        'total_mes2',
        'total_mes3',
        'total_mes4',
        'total_mes5',
        'total_mes6',
        'total_mes7',
        'total_sub1',
        'total_sub2',
        'total_endossado_mes1',
        'total_endossado_mes2',
        'total_endossado_mes3',
        'total_endossado_mes4',
        'total_endossado_mes5',
        'total_endossado_mes6',
        'total_endossado_mes7',
        'total_endossado_sub1',
        'total_endossado_sub2',
        'id_cuidador_sub1',
        'cargo_sub1',
        'funcao_sub1',
        'data_substituicao1',
        'id_cuidador_sub2',
        'cargo_sub2',
        'funcao_sub2',
        'data_substituicao2',
        'data_inicio_contrato',
        'data_termino_contrato',
        'valor_hora_operacional',
        'horas_mensais_custo',
        'valor_hora_funcao',
        'data_inicio_real',
        'data_termino_real',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_alocado'                => 'required|is_natural_no_zero|max_length[11]',
        'id_os_horario'             => 'is_natural_no_zero|max_length[11]',
        'dia_semana'                => 'required|integer|max_length[1]',
        'periodo'                   => 'integer|exact_length[1]',
        'cargo'                     => 'string|max_length[255]',
        'cargo_mes2'                => 'string|max_length[255]',
        'cargo_mes3'                => 'string|max_length[255]',
        'cargo_mes4'                => 'string|max_length[255]',
        'cargo_mes5'                => 'string|max_length[255]',
        'cargo_mes6'                => 'string|max_length[255]',
        'cargo_mes7'                => 'string|max_length[255]',
        'funcao'                    => 'string|max_length[255]',
        'funcao_mes2'               => 'string|max_length[255]',
        'funcao_mes3'               => 'string|max_length[255]',
        'funcao_mes4'               => 'string|max_length[255]',
        'funcao_mes5'               => 'string|max_length[255]',
        'funcao_mes6'               => 'string|max_length[255]',
        'funcao_mes7'               => 'string|max_length[255]',
        'horario_inicio_mes1'       => 'valid_time',
        'horario_inicio_mes2'       => 'valid_time',
        'horario_inicio_mes3'       => 'valid_time',
        'horario_inicio_mes4'       => 'valid_time',
        'horario_inicio_mes5'       => 'valid_time',
        'horario_inicio_mes6'       => 'valid_time',
        'horario_inicio_mes7'       => 'valid_time',
        'horario_termino_mes1'      => 'valid_time',
        'horario_termino_mes2'      => 'valid_time',
        'horario_termino_mes3'      => 'valid_time',
        'horario_termino_mes4'      => 'valid_time',
        'horario_termino_mes5'      => 'valid_time',
        'horario_termino_mes6'      => 'valid_time',
        'horario_termino_mes7'      => 'valid_time',
        'total_horas_mes1'          => 'valid_time',
        'total_horas_mes2'          => 'valid_time',
        'total_horas_mes3'          => 'valid_time',
        'total_horas_mes4'          => 'valid_time',
        'total_horas_mes5'          => 'valid_time',
        'total_horas_mes6'          => 'valid_time',
        'total_horas_mes7'          => 'valid_time',
        'total_semanas_mes1'        => 'required|integer|max_length[1]',
        'total_semanas_mes2'        => 'required|integer|max_length[1]',
        'total_semanas_mes3'        => 'required|integer|max_length[1]',
        'total_semanas_mes4'        => 'required|integer|max_length[1]',
        'total_semanas_mes5'        => 'required|integer|max_length[1]',
        'total_semanas_mes6'        => 'required|integer|max_length[1]',
        'total_semanas_mes7'        => 'required|integer|max_length[1]',
        'total_semanas_sub1'        => 'integer|max_length[1]',
        'total_semanas_sub2'        => 'integer|max_length[1]',
        'desconto_mes1'             => 'numeric|max_length[5]',
        'desconto_mes2'             => 'numeric|max_length[5]',
        'desconto_mes3'             => 'numeric|max_length[5]',
        'desconto_mes4'             => 'numeric|max_length[5]',
        'desconto_mes5'             => 'numeric|max_length[5]',
        'desconto_mes6'             => 'numeric|max_length[5]',
        'desconto_mes7'             => 'numeric|max_length[5]',
        'desconto_sub1'             => 'numeric|max_length[5]',
        'desconto_sub2'             => 'numeric|max_length[5]',
        'endosso_mes1'              => 'numeric|max_length[5]',
        'endosso_mes2'              => 'numeric|max_length[5]',
        'endosso_mes3'              => 'numeric|max_length[5]',
        'endosso_mes4'              => 'numeric|max_length[5]',
        'endosso_mes5'              => 'numeric|max_length[5]',
        'endosso_mes6'              => 'numeric|max_length[5]',
        'endosso_mes7'              => 'numeric|max_length[5]',
        'endosso_sub1'              => 'numeric|max_length[5]',
        'endosso_sub2'              => 'numeric|max_length[5]',
        'total_mes1'                => 'valid_time',
        'total_mes2'                => 'valid_time',
        'total_mes3'                => 'valid_time',
        'total_mes4'                => 'valid_time',
        'total_mes5'                => 'valid_time',
        'total_mes6'                => 'valid_time',
        'total_mes7'                => 'valid_time',
        'total_sub1'                => 'valid_time',
        'total_sub2'                => 'valid_time',
        'total_endossado_mes1'      => 'valid_time',
        'total_endossado_mes2'      => 'valid_time',
        'total_endossado_mes3'      => 'valid_time',
        'total_endossado_mes4'      => 'valid_time',
        'total_endossado_mes5'      => 'valid_time',
        'total_endossado_mes6'      => 'valid_time',
        'total_endossado_mes7'      => 'valid_time',
        'total_endossado_sub1'      => 'valid_time',
        'total_endossado_sub2'      => 'valid_time',
        'id_cuidador_sub1'          => 'is_natural_no_zero|max_length[11]',
        'cargo_sub1'                => 'string|max_length[255]',
        'funcao_sub1'               => 'string|max_length[255]',
        'data_substituicao1'        => 'valid_date',
        'id_cuidador_sub2'          => 'is_natural_no_zero|max_length[11]',
        'cargo_sub2'                => 'string|max_length[255]',
        'funcao_sub2'               => 'string|max_length[255]',
        'data_substituicao2'        => 'valid_date',
        'data_inicio_contrato'      => 'valid_date',
        'data_termino_contrato'     => 'valid_date',
        'valor_hora_operacional'    => 'numeric|max_length[10]',
        'horas_mensais_custo'       => 'valid_time',
        'valor_hora_funcao'         => 'numeric|max_length[10]',
        'data_inicio_real'          => 'valid_date',
        'data_termino_real'         => 'valid_date',
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

    //--------------------------------------------------------------------

    public const DIAS_SEMANA = [
        '0' => 'Domingo',
        '1' => 'Segunda',
        '2' => 'Terça',
        '3' => 'Quarta',
        '4' => 'Quinta',
        '5' => 'Sexta',
        '6' => 'Sábado',
    ];
    public const DIAS_SEMANA_POR_EXTENSO = [
        '0' => 'Domingo',
        '1' => 'Segunda-feira',
        '2' => 'Terça-feira',
        '3' => 'Quarta-feira',
        '4' => 'Quinta-feira',
        '5' => 'Sexta-feira',
        '6' => 'Sábado',
    ];
    public const PERIODOS = [
        '0' => 'Madrugada',
        '1' => 'Manhã',
        '2' => 'Tarde',
        '3' => 'Noite',
    ];
}
