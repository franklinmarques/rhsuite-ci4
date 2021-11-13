<?php

namespace App\Models;

use App\Entities\EiFaturamentoConsolidado;

class EiFaturamentoConsolidadoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_faturamentos_consolidados';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiFaturamentoConsolidado::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_medicao_mensal',
        'id_alocacao',
        'cargo',
        'funcao',
        'valor_hora_mes1',
        'valor_hora_mes2',
        'valor_hora_mes3',
        'valor_hora_mes4',
        'valor_hora_mes5',
        'valor_hora_mes6',
        'valor_hora_mes7',
        'total_horas_mes1',
        'total_horas_mes2',
        'total_horas_mes3',
        'total_horas_mes4',
        'total_horas_mes5',
        'total_horas_mes6',
        'total_horas_mes7',
        'valor_faturado_mes1',
        'valor_faturado_mes2',
        'valor_faturado_mes3',
        'valor_faturado_mes4',
        'valor_faturado_mes5',
        'valor_faturado_mes6',
        'valor_faturado_mes7',
        'total_escolas',
        'total_alunos',
        'total_cuidadores',
        'total_horas_projetadas',
        'total_horas_realizadas',
        'receita_projetada',
        'receita_efetuada',
        'pagamentos_efetuados',
        'resultado',
        'resultado_percentual',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_medicao_mensal'         => 'integer|max_length[11]',
        'id_alocacao'               => 'required|is_natural_no_zero|max_length[11]',
        'cargo'                     => 'required|string|max_length[255]',
        'funcao'                    => 'required|string|max_length[255]',
        'valor_hora_mes1'           => 'required|numeric|max_length[10]',
        'valor_hora_mes2'           => 'required|numeric|max_length[10]',
        'valor_hora_mes3'           => 'required|numeric|max_length[10]',
        'valor_hora_mes4'           => 'required|numeric|max_length[10]',
        'valor_hora_mes5'           => 'required|numeric|max_length[10]',
        'valor_hora_mes6'           => 'required|numeric|max_length[10]',
        'valor_hora_mes7'           => 'required|numeric|max_length[10]',
        'total_horas_mes1'          => 'required|string|max_length[20]',
        'total_horas_mes2'          => 'required|string|max_length[20]',
        'total_horas_mes3'          => 'required|string|max_length[20]',
        'total_horas_mes4'          => 'required|string|max_length[20]',
        'total_horas_mes5'          => 'required|string|max_length[20]',
        'total_horas_mes6'          => 'required|string|max_length[20]',
        'total_horas_mes7'          => 'required|string|max_length[20]',
        'valor_faturado_mes1'       => 'numeric|max_length[10]',
        'valor_faturado_mes2'       => 'numeric|max_length[10]',
        'valor_faturado_mes3'       => 'numeric|max_length[10]',
        'valor_faturado_mes4'       => 'numeric|max_length[10]',
        'valor_faturado_mes5'       => 'numeric|max_length[10]',
        'valor_faturado_mes6'       => 'numeric|max_length[10]',
        'valor_faturado_mes7'       => 'numeric|max_length[10]',
        'total_escolas'             => 'integer|max_length[11]',
        'total_alunos'              => 'integer|max_length[11]',
        'total_cuidadores'          => 'integer|max_length[11]',
        'total_horas_projetadas'    => 'string|max_length[12]',
        'total_horas_realizadas'    => 'string|max_length[12]',
        'receita_projetada'         => 'numeric|max_length[10]',
        'receita_efetuada'          => 'numeric|max_length[10]',
        'pagamentos_efetuados'      => 'numeric|max_length[10]',
        'resultado'                 => 'numeric|max_length[10]',
        'resultado_percentual'      => 'numeric|max_length[5]',
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
