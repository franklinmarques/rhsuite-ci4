<?php

namespace App\Models;

use App\Entities\StApontamento;

class StApontamentoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'st_apontamentos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = StApontamento::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_alocado',
        'data',
        'hora_entrada',
        'hora_intervalo',
        'hora_retorno',
        'hora_saida',
        'qtde_dias',
        'hora_atraso',
        'qtde_req',
        'qtde_rev',
        'apontamento_extra',
        'apontamento_desc',
        'apontamento_saldo',
        'apontamento_saldo_old',
        'hora_glosa',
        'id_detalhe_evento',
        'observacoes',
        'status',
        'id_usuario_alocado_bck',
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
        'data'                      => 'required|valid_date',
        'hora_entrada'              => 'valid_date',
        'hora_intervalo'            => 'valid_date',
        'hora_retorno'              => 'valid_date',
        'hora_saida'                => 'valid_date',
        'qtde_dias'                 => 'integer|max_length[2]',
        'hora_atraso'               => 'valid_time',
        'qtde_req'                  => 'integer|max_length[11]',
        'qtde_rev'                  => 'integer|max_length[11]',
        'apontamento_extra'         => 'valid_time',
        'apontamento_desc'          => 'valid_time',
        'apontamento_saldo'         => 'valid_time',
        'hora_glosa'                => 'valid_time',
        'id_detalhe_evento'         => 'is_natural_no_zero|max_length[11]',
        'observacoes'               => 'string',
        'status'                    => 'required|string|max_length[2]',
        'id_usuario_alocado_bck'    => 'is_natural_no_zero|max_length[11]',
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
