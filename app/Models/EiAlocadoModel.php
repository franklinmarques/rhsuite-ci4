<?php

namespace App\Models;

use App\Entities\EiAlocado;

class EiAlocadoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_alocados';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiAlocado::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_alocacao_escola',
        'id_os_profissional',
        'id_cuidador',
        'cuidador',
        'valor_hora',
        'valor_hora_operacional',
        'valor_hora_pagamento',
        'horas_diarias',
        'horas_semanais',
        'qtde_dias',
        'horas_semestre',
        'total_dias_letivos',
        'data_inicio_contrato',
        'data_termino_contrato',
        'horas_mensais_custo',
        'valor_total',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_alocacao_escola'        => 'required|is_natural_no_zero|max_length[11]',
        'id_os_profissional'        => 'is_natural_no_zero|max_length[11]',
        'id_cuidador'               => 'is_natural_no_zero|max_length[11]',
        'cuidador'                  => 'string|max_length[255]',
        'valor_hora'                => 'numeric|max_length[10]',
        'valor_hora_operacional'    => 'numeric|max_length[10]',
        'valor_hora_pagamento'      => 'numeric|max_length[10]',
        'horas_diarias'             => 'numeric|max_length[5]',
        'horas_semanais'            => 'numeric|max_length[5]',
        'qtde_dias'                 => 'numeric|max_length[4]',
        'horas_semestre'            => 'numeric|max_length[6]',
        'total_dias_letivos'        => 'required|integer|max_length[3]',
        'data_inicio_contrato'      => 'valid_date',
        'data_termino_contrato'     => 'valid_date',
        'horas_mensais_custo'       => 'valid_time',
        'valor_total'               => 'numeric|max_length[10]',
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
