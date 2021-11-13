<?php

namespace App\Models;

use App\Entities\EiAlocacao;

class EiAlocacaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_alocacoes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiAlocacao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'depto',
        'id_diretoria',
        'diretoria',
        'id_supervisor',
        'supervisor',
        'municipio',
        'coordenador',
        'ano',
        'semestre',
        'id_ordem_servico',
        'ordem_servico',
        'congelar_mes1',
        'congelar_mes2',
        'congelar_mes3',
        'congelar_mes4',
        'congelar_mes5',
        'congelar_mes6',
        'congelar_mes7',
        'pagamento_fracionado_mes1',
        'pagamento_fracionado_mes2',
        'pagamento_fracionado_mes3',
        'pagamento_fracionado_mes4',
        'pagamento_fracionado_mes5',
        'pagamento_fracionado_mes6',
        'pagamento_fracionado_mes7',
        'medicao_liberada_mes1',
        'medicao_liberada_mes2',
        'medicao_liberada_mes3',
        'medicao_liberada_mes4',
        'medicao_liberada_mes5',
        'medicao_liberada_mes6',
        'medicao_liberada_mes7',
        'dia_fechamento_mes1',
        'dia_fechamento_mes2',
        'dia_fechamento_mes3',
        'dia_fechamento_mes4',
        'dia_fechamento_mes5',
        'dia_fechamento_mes6',
        'dia_fechamento_mes7',
        'saldo_mes1',
        'saldo_mes2',
        'saldo_mes3',
        'saldo_mes4',
        'saldo_mes5',
        'saldo_mes6',
        'saldo_mes7',
        'saldo_acumulado_mes1',
        'saldo_acumulado_mes2',
        'saldo_acumulado_mes3',
        'saldo_acumulado_mes4',
        'saldo_acumulado_mes5',
        'saldo_acumulado_mes6',
        'saldo_acumulado_mes7',
        'observacoes_mes1',
        'observacoes_mes2',
        'observacoes_mes3',
        'observacoes_mes4',
        'observacoes_mes5',
        'observacoes_mes6',
        'observacoes_mes7',
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
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'                => 'required|is_natural_no_zero|max_length[11]',
        'depto'                     => 'required|string|max_length[255]',
        'id_diretoria'              => 'required|integer|max_length[11]',
        'diretoria'                 => 'required|string|max_length[255]',
        'id_supervisor'             => 'required|integer|max_length[11]',
        'supervisor'                => 'required|string|max_length[255]',
        'municipio'                 => 'required|string|max_length[255]',
        'coordenador'               => 'required|string|max_length[255]',
        'ano'                       => 'required|int|max_length[4]',
        'semestre'                  => 'required|integer|exact_length[1]',
        'id_ordem_servico'          => 'integer|max_length[11]',
        'ordem_servico'             => 'string|max_length[255]',
        'congelar_mes1'             => 'integer|exact_length[1]',
        'congelar_mes2'             => 'integer|exact_length[1]',
        'congelar_mes3'             => 'integer|exact_length[1]',
        'congelar_mes4'             => 'integer|exact_length[1]',
        'congelar_mes5'             => 'integer|exact_length[1]',
        'congelar_mes6'             => 'integer|exact_length[1]',
        'congelar_mes7'             => 'integer|exact_length[1]',
        'pagamento_fracionado_mes1' => 'integer|exact_length[1]',
        'pagamento_fracionado_mes2' => 'integer|exact_length[1]',
        'pagamento_fracionado_mes3' => 'integer|exact_length[1]',
        'pagamento_fracionado_mes4' => 'integer|exact_length[1]',
        'pagamento_fracionado_mes5' => 'integer|exact_length[1]',
        'pagamento_fracionado_mes6' => 'integer|exact_length[1]',
        'pagamento_fracionado_mes7' => 'integer|exact_length[1]',
        'medicao_liberada_mes1'     => 'integer|exact_length[1]',
        'medicao_liberada_mes2'     => 'integer|exact_length[1]',
        'medicao_liberada_mes3'     => 'integer|exact_length[1]',
        'medicao_liberada_mes4'     => 'integer|exact_length[1]',
        'medicao_liberada_mes5'     => 'integer|exact_length[1]',
        'medicao_liberada_mes6'     => 'integer|exact_length[1]',
        'medicao_liberada_mes7'     => 'integer|exact_length[1]',
        'dia_fechamento_mes1'       => 'integer|max_length[2]',
        'dia_fechamento_mes2'       => 'integer|max_length[2]',
        'dia_fechamento_mes3'       => 'integer|max_length[2]',
        'dia_fechamento_mes4'       => 'integer|max_length[2]',
        'dia_fechamento_mes5'       => 'integer|max_length[2]',
        'dia_fechamento_mes6'       => 'integer|max_length[2]',
        'dia_fechamento_mes7'       => 'integer|max_length[2]',
        'saldo_mes1'                => 'string|max_length[10]',
        'saldo_mes2'                => 'string|max_length[10]',
        'saldo_mes3'                => 'string|max_length[10]',
        'saldo_mes4'                => 'string|max_length[10]',
        'saldo_mes5'                => 'string|max_length[10]',
        'saldo_mes6'                => 'string|max_length[10]',
        'saldo_mes7'                => 'string|max_length[10]',
        'saldo_acumulado_mes1'      => 'string|max_length[10]',
        'saldo_acumulado_mes2'      => 'string|max_length[10]',
        'saldo_acumulado_mes3'      => 'string|max_length[10]',
        'saldo_acumulado_mes4'      => 'string|max_length[10]',
        'saldo_acumulado_mes5'      => 'string|max_length[10]',
        'saldo_acumulado_mes6'      => 'string|max_length[10]',
        'saldo_acumulado_mes7'      => 'string|max_length[10]',
        'observacoes_mes1'          => 'string',
        'observacoes_mes2'          => 'string',
        'observacoes_mes3'          => 'string',
        'observacoes_mes4'          => 'string',
        'observacoes_mes5'          => 'string',
        'observacoes_mes6'          => 'string',
        'observacoes_mes7'          => 'string',
        'total_horas_mes1'          => 'string|max_length[20]',
        'total_horas_mes2'          => 'string|max_length[20]',
        'total_horas_mes3'          => 'string|max_length[20]',
        'total_horas_mes4'          => 'string|max_length[20]',
        'total_horas_mes5'          => 'string|max_length[20]',
        'total_horas_mes6'          => 'string|max_length[20]',
        'total_horas_mes7'          => 'string|max_length[20]',
        'valor_faturado_mes1'       => 'numeric|max_length[10]',
        'valor_faturado_mes2'       => 'numeric|max_length[10]',
        'valor_faturado_mes3'       => 'numeric|max_length[10]',
        'valor_faturado_mes4'       => 'numeric|max_length[10]',
        'valor_faturado_mes5'       => 'numeric|max_length[10]',
        'valor_faturado_mes6'       => 'numeric|max_length[10]',
        'valor_faturado_mes7'       => 'numeric|max_length[10]',
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
