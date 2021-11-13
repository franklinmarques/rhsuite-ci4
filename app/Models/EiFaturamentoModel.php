<?php

namespace App\Models;

use App\Entities\EiFaturamento;

class EiFaturamentoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_faturamentos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiFaturamento::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_alocacao',
        'id_escola',
        'escola',
        'cargo',
        'funcao',
        'data_envio_solicitacao_mes1',
        'data_envio_solicitacao_mes2',
        'data_envio_solicitacao_mes3',
        'data_envio_solicitacao_mes4',
        'data_envio_solicitacao_mes5',
        'data_envio_solicitacao_mes6',
        'data_envio_solicitacao_mes7',
        'data_envio_solicitacao_sub1',
        'data_envio_solicitacao_sub2',
        'data_aprovacao_mes1',
        'data_aprovacao_mes2',
        'data_aprovacao_mes3',
        'data_aprovacao_mes4',
        'data_aprovacao_mes5',
        'data_aprovacao_mes6',
        'data_aprovacao_mes7',
        'data_aprovacao_sub1',
        'data_aprovacao_sub2',
        'data_impressao_mes1',
        'data_impressao_mes2',
        'data_impressao_mes3',
        'data_impressao_mes4',
        'data_impressao_mes5',
        'data_impressao_mes6',
        'data_impressao_mes7',
        'data_impressao_sub1',
        'data_impressao_sub2',
        'horas_descontadas_mes1',
        'horas_descontadas_mes2',
        'horas_descontadas_mes3',
        'horas_descontadas_mes4',
        'horas_descontadas_mes5',
        'horas_descontadas_mes6',
        'horas_descontadas_mes7',
        'horas_descontadas_sub1',
        'horas_descontadas_sub2',
        'observacoes_mes1',
        'observacoes_mes2',
        'observacoes_mes3',
        'observacoes_mes4',
        'observacoes_mes5',
        'observacoes_mes6',
        'observacoes_mes7',
        'observacoes_sub1',
        'observacoes_sub2',
        'preservar_edicao_mes1',
        'preservar_edicao_mes2',
        'preservar_edicao_mes3',
        'preservar_edicao_mes4',
        'preservar_edicao_mes5',
        'preservar_edicao_mes6',
        'preservar_edicao_mes7',
        'preservar_edicao_sub1',
        'preservar_edicao_sub2',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_alocacao'                   => 'required|is_natural_no_zero|max_length[11]',
        'id_escola'                     => 'is_natural_no_zero|max_length[11]',
        'escola'                        => 'required|string|max_length[255]',
        'cargo'                         => 'required|string|max_length[255]',
        'funcao'                        => 'required|string|max_length[255]',
        'data_envio_solicitacao_mes1'   => 'valid_date',
        'data_envio_solicitacao_mes2'   => 'valid_date',
        'data_envio_solicitacao_mes3'   => 'valid_date',
        'data_envio_solicitacao_mes4'   => 'valid_date',
        'data_envio_solicitacao_mes5'   => 'valid_date',
        'data_envio_solicitacao_mes6'   => 'valid_date',
        'data_envio_solicitacao_mes7'   => 'valid_date',
        'data_envio_solicitacao_sub1'   => 'valid_date',
        'data_envio_solicitacao_sub2'   => 'valid_date',
        'data_aprovacao_mes1'           => 'valid_date',
        'data_aprovacao_mes2'           => 'valid_date',
        'data_aprovacao_mes3'           => 'valid_date',
        'data_aprovacao_mes4'           => 'valid_date',
        'data_aprovacao_mes5'           => 'valid_date',
        'data_aprovacao_mes6'           => 'valid_date',
        'data_aprovacao_mes7'           => 'valid_date',
        'data_aprovacao_sub1'           => 'valid_date',
        'data_aprovacao_sub2'           => 'valid_date',
        'data_impressao_mes1'           => 'valid_date',
        'data_impressao_mes2'           => 'valid_date',
        'data_impressao_mes3'           => 'valid_date',
        'data_impressao_mes4'           => 'valid_date',
        'data_impressao_mes5'           => 'valid_date',
        'data_impressao_mes6'           => 'valid_date',
        'data_impressao_mes7'           => 'valid_date',
        'data_impressao_sub1'           => 'valid_date',
        'data_impressao_sub2'           => 'valid_date',
        'horas_descontadas_mes1'        => 'valid_time',
        'horas_descontadas_mes2'        => 'valid_time',
        'horas_descontadas_mes3'        => 'valid_time',
        'horas_descontadas_mes4'        => 'valid_time',
        'horas_descontadas_mes5'        => 'valid_time',
        'horas_descontadas_mes6'        => 'valid_time',
        'horas_descontadas_mes7'        => 'valid_time',
        'horas_descontadas_sub1'        => 'valid_time',
        'horas_descontadas_sub2'        => 'valid_time',
        'observacoes_mes1'              => 'string',
        'observacoes_mes2'              => 'string',
        'observacoes_mes3'              => 'string',
        'observacoes_mes4'              => 'string',
        'observacoes_mes5'              => 'string',
        'observacoes_mes6'              => 'string',
        'observacoes_mes7'              => 'string',
        'observacoes_sub1'              => 'string',
        'observacoes_sub2'              => 'string',
        'preservar_edicao_mes1'         => 'integer|exact_length[1]',
        'preservar_edicao_mes2'         => 'integer|exact_length[1]',
        'preservar_edicao_mes3'         => 'integer|exact_length[1]',
        'preservar_edicao_mes4'         => 'integer|exact_length[1]',
        'preservar_edicao_mes5'         => 'integer|exact_length[1]',
        'preservar_edicao_mes6'         => 'integer|exact_length[1]',
        'preservar_edicao_mes7'         => 'integer|exact_length[1]',
        'preservar_edicao_sub1'         => 'integer|exact_length[1]',
        'preservar_edicao_sub2'         => 'integer|exact_length[1]',
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
