<?php

namespace App\Models;

use App\Entities\EiOrdemServicoProfissional;

class EiOrdemServicoProfissionalModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_ordens_servico_profissionais';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiOrdemServicoProfissional::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_os_escola',
        'id_usuario',
        'id_supervisor',
        'id_usuario_sub1',
        'data_substituicao1',
        'id_usuario_sub2',
        'data_substituicao2',
        'id_departamento',
        'id_area',
        'id_setor',
        'id_cargo',
        'id_funcao',
        'id_funcao_2m',
        'id_funcao_3m',
        'id_funcao_1t',
        'id_funcao_2t',
        'id_funcao_3t',
        'id_funcao_1n',
        'id_funcao_2n',
        'id_funcao_3n',
        'municipio',
        'valor_hora',
        'qtde_dias',
        'qtde_semanas',
        'horas_diarias',
        'horas_semanais',
        'horas_mensais',
        'horas_semestre',
        'faturamento_semestral_projetado',
        'desconto_mensal_1',
        'desconto_mensal_2',
        'desconto_mensal_3',
        'desconto_mensal_4',
        'desconto_mensal_5',
        'valor_hora_operacional',
        'valor_hora_operacional_2',
        'valor_hora_operacional_3',
        'valor_hora_operacional_1t',
        'valor_hora_operacional_2t',
        'valor_hora_operacional_3t',
        'valor_hora_operacional_1n',
        'valor_hora_operacional_2n',
        'valor_hora_operacional_3n',
        'valor_mensal_1',
        'valor_mensal_2',
        'valor_mensal_3',
        'valor_mensal_4',
        'valor_mensal_5',
        'valor_mensal_6',
        'desconto_mensal_6',
        'valor_hora_mensal',
        'desconto_mensal_sub1_1',
        'desconto_mensal_sub1_2',
        'desconto_mensal_sub1_3',
        'desconto_mensal_sub1_4',
        'desconto_mensal_sub1_5',
        'desconto_mensal_sub1_6',
        'desconto_mensal_sub2_1',
        'desconto_mensal_sub2_2',
        'desconto_mensal_sub2_3',
        'desconto_mensal_sub2_4',
        'desconto_mensal_sub2_5',
        'desconto_mensal_sub2_6',
        'horas_mensais_custo',
        'horas_mensais_custo_2',
        'horas_mensais_custo_3',
        'horas_mensais_custo_1t',
        'horas_mensais_custo_2t',
        'horas_mensais_custo_3t',
        'horas_mensais_custo_1n',
        'horas_mensais_custo_2n',
        'horas_mensais_custo_3n',
        'data_inicio_contrato',
        'data_termino_contrato',
        'pagamento_inicio',
        'pagamento_reajuste',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_os_escola'           => 'required|is_natural_no_zero|max_length[11]',
        'id_usuario'                        => 'required|is_natural_no_zero|max_length[11]',
        'id_supervisor'                     => 'is_natural_no_zero|max_length[11]',
        'id_usuario_sub1'                   => 'integer|max_length[11]',
        'data_substituicao1'                => 'valid_date',
        'id_usuario_sub2'                   => 'integer|max_length[11]',
        'data_substituicao2'                => 'valid_date',
        'id_departamento'                   => 'integer|max_length[11]',
        'id_area'                           => 'integer|max_length[11]',
        'id_setor'                          => 'integer|max_length[11]',
        'id_cargo'                          => 'integer|max_length[11]',
        'id_funcao'                         => 'integer|max_length[11]',
        'id_funcao_2m'                      => 'integer|max_length[11]',
        'id_funcao_3m'                      => 'integer|max_length[11]',
        'id_funcao_1t'                      => 'integer|max_length[11]',
        'id_funcao_2t'                      => 'integer|max_length[11]',
        'id_funcao_3t'                      => 'integer|max_length[11]',
        'id_funcao_1n'                      => 'integer|max_length[11]',
        'id_funcao_2n'                      => 'integer|max_length[11]',
        'id_funcao_3n'                      => 'integer|max_length[11]',
        'municipio'                         => 'string|max_length[255]',
        'valor_hora'                        => 'numeric|max_length[10]',
        'qtde_dias'                         => 'integer|max_length[4]',
        'qtde_semanas'                      => 'integer|max_length[1]',
        'horas_diarias'                     => 'numeric|max_length[5]',
        'horas_semanais'                    => 'numeric|max_length[5]',
        'horas_mensais'                     => 'numeric|max_length[6]',
        'horas_semestre'                    => 'numeric|max_length[10]',
        'faturamento_semestral_projetado'   => 'numeric|max_length[10]',
        'desconto_mensal_1'                 => 'required|numeric|max_length[6]',
        'desconto_mensal_2'                 => 'required|numeric|max_length[6]',
        'desconto_mensal_3'                 => 'required|numeric|max_length[6]',
        'desconto_mensal_4'                 => 'required|numeric|max_length[6]',
        'desconto_mensal_5'                 => 'required|numeric|max_length[6]',
        'valor_hora_operacional'            => 'numeric|max_length[10]',
        'valor_hora_operacional_2'          => 'numeric|max_length[10]',
        'valor_hora_operacional_3'          => 'numeric|max_length[10]',
        'valor_hora_operacional_1t'         => 'numeric|max_length[10]',
        'valor_hora_operacional_2t'         => 'numeric|max_length[10]',
        'valor_hora_operacional_3t'         => 'numeric|max_length[10]',
        'valor_hora_operacional_1n'         => 'numeric|max_length[10]',
        'valor_hora_operacional_2n'         => 'numeric|max_length[10]',
        'valor_hora_operacional_3n'         => 'numeric|max_length[10]',
        'valor_mensal_1'                    => 'numeric|max_length[10]',
        'valor_mensal_2'                    => 'numeric|max_length[10]',
        'valor_mensal_3'                    => 'numeric|max_length[10]',
        'valor_mensal_4'                    => 'numeric|max_length[10]',
        'valor_mensal_5'                    => 'numeric|max_length[10]',
        'valor_mensal_6'                    => 'numeric|max_length[10]',
        'desconto_mensal_6'                 => 'required|numeric|max_length[6]',
        'valor_hora_mensal'                 => 'numeric|max_length[10]',
        'desconto_mensal_sub1_1'            => 'required|numeric|max_length[10]',
        'desconto_mensal_sub1_2'            => 'required|numeric|max_length[10]',
        'desconto_mensal_sub1_3'            => 'required|numeric|max_length[10]',
        'desconto_mensal_sub1_4'            => 'required|numeric|max_length[10]',
        'desconto_mensal_sub1_5'            => 'required|numeric|max_length[10]',
        'desconto_mensal_sub1_6'            => 'required|numeric|max_length[10]',
        'desconto_mensal_sub2_1'            => 'required|numeric|max_length[10]',
        'desconto_mensal_sub2_2'            => 'required|numeric|max_length[10]',
        'desconto_mensal_sub2_3'            => 'required|numeric|max_length[10]',
        'desconto_mensal_sub2_4'            => 'required|numeric|max_length[10]',
        'desconto_mensal_sub2_5'            => 'required|numeric|max_length[10]',
        'desconto_mensal_sub2_6'            => 'required|numeric|max_length[10]',
        'horas_mensais_custo'               => 'valid_time',
        'horas_mensais_custo_2'             => 'valid_time',
        'horas_mensais_custo_3'             => 'valid_time',
        'horas_mensais_custo_1t'            => 'valid_time',
        'horas_mensais_custo_2t'            => 'valid_time',
        'horas_mensais_custo_3t'            => 'valid_time',
        'horas_mensais_custo_1n'            => 'valid_time',
        'horas_mensais_custo_2n'            => 'valid_time',
        'horas_mensais_custo_3n'            => 'valid_time',
        'data_inicio_contrato'              => 'valid_date',
        'data_termino_contrato'             => 'valid_date',
        'pagamento_inicio'                  => 'numeric|max_length[10]',
        'pagamento_reajuste'                => 'numeric|max_length[10]',
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
