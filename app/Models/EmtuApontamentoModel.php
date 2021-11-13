<?php

namespace App\Models;

use App\Entities\EmtuApontamento;

class EmtuApontamentoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'emtu_apontamentos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EmtuApontamento::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_alocado',
        'data',
        'horario_entrada',
        'horario_intervalo',
        'horario_retorno',
        'horario_saida',
        'qtde_dias',
        'hora_atraso',
        'hora_extra',
        'desconto_folha',
        'saldo_banco_horas',
        'hora_glosa',
        'observacoes',
        'status',
        'id_alocado_bck',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_alocado'        => 'required|is_natural_no_zero|max_length[11]',
        'data'              => 'required|valid_date',
        'horario_entrada'   => 'valid_date',
        'horario_intervalo' => 'valid_date',
        'horario_retorno'   => 'valid_date',
        'horario_saida'     => 'valid_date',
        'qtde_dias'         => 'integer|max_length[2]',
        'hora_atraso'       => 'valid_time',
        'hora_extra'        => 'valid_time',
        'desconto_folha'    => 'valid_time',
        'saldo_banco_horas' => 'valid_time',
        'hora_glosa'        => 'valid_time',
        'observacoes'       => 'string',
        'status'            => 'required|string|max_length[2]',
        'id_alocado_bck'    => 'is_natural_no_zero|max_length[11]',
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
