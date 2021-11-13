<?php

namespace App\Models;

use App\Entities\EmtuAlocacaoFeriado;

class EmtuAlocacaoFeriadoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'emtu_alocacoes_feriados';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EmtuAlocacaoFeriado::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_alocacao',
        'data',
        'status',
        'qtde_novos_processos',
        'qtde_analistas',
        'qtde_processos_tratados_dia',
        'qtde_pagamentos',
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
        'data'                          => 'required|valid_date',
        'status'                        => 'required|string|max_length[2]',
        'qtde_novos_processos'          => 'integer|max_length[11]',
        'qtde_analistas'                => 'integer|max_length[11]',
        'qtde_processos_tratados_dia'   => 'integer|max_length[11]',
        'qtde_pagamentos'               => 'integer|max_length[11]',
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
