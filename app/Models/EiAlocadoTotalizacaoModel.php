<?php

namespace App\Models;

use App\Entities\EiAlocadoTotalizacao;

class EiAlocadoTotalizacaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_alocados_totalizacoes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiAlocadoTotalizacao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_alocado',
        'periodo',
        'id_cuidador',
        'cuidador',
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
        'substituicao_semestral',
        'substituicao_eventual',
        'total_dias_mes1',
        'total_dias_mes2',
        'total_dias_mes3',
        'total_dias_mes4',
        'total_dias_mes5',
        'total_dias_mes6',
        'total_dias_mes7',
        'dias_descontados_mes1',
        'dias_descontados_mes2',
        'dias_descontados_mes3',
        'dias_descontados_mes4',
        'dias_descontados_mes5',
        'dias_descontados_mes6',
        'dias_descontados_mes7',
        'total_dias_sub1',
        'total_dias_sub2',
        'total_horas_mes1',
        'total_horas_mes2',
        'total_horas_mes3',
        'total_horas_mes4',
        'total_horas_mes5',
        'total_horas_mes6',
        'total_horas_mes7',
        'total_horas_sub1',
        'total_horas_sub2',
        'horas_descontadas_mes1',
        'horas_descontadas_mes2',
        'horas_descontadas_mes3',
        'horas_descontadas_mes4',
        'horas_descontadas_mes5',
        'horas_descontadas_mes6',
        'horas_descontadas_mes7',
        'horas_descontadas_sub1',
        'horas_descontadas_sub2',
        'total_horas_faturadas_mes1',
        'total_horas_faturadas_mes2',
        'total_horas_faturadas_mes3',
        'total_horas_faturadas_mes4',
        'total_horas_faturadas_mes5',
        'total_horas_faturadas_mes6',
        'total_horas_faturadas_mes7',
        'total_horas_faturadas_sub1',
        'total_horas_faturadas_sub2',
        'total_descontos_mes1',
        'total_descontos_mes2',
        'total_descontos_mes3',
        'total_descontos_mes4',
        'total_descontos_mes5',
        'total_descontos_mes6',
        'total_descontos_mes7',
        'total_descontos_sub1',
        'total_descontos_sub2',
        'valor_pagamento_mes1',
        'valor_pagamento_mes2',
        'valor_pagamento_mes3',
        'valor_pagamento_mes4',
        'valor_pagamento_mes5',
        'valor_pagamento_mes6',
        'valor_pagamento_mes7',
        'valor_pagamento_sub1',
        'valor_pagamento_sub2',
        'valor_total_mes1',
        'valor_total_mes2',
        'valor_total_mes3',
        'valor_total_mes4',
        'valor_total_mes5',
        'valor_total_mes6',
        'valor_total_mes7',
        'valor_total_sub1',
        'valor_total_sub2',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_alocado'                    => 'required|is_natural_no_zero|max_length[11]',
        'periodo'                       => 'integer|exact_length[1]',
        'id_cuidador'                   => 'required|integer|max_length[11]',
        'cuidador'                      => 'string|max_length[255]',
        'cargo'                         => 'string|max_length[255]',
        'cargo_mes2'                    => 'string|max_length[255]',
        'cargo_mes3'                    => 'string|max_length[255]',
        'cargo_mes4'                    => 'string|max_length[255]',
        'cargo_mes5'                    => 'string|max_length[255]',
        'cargo_mes6'                    => 'string|max_length[255]',
        'cargo_mes7'                    => 'string|max_length[255]',
        'funcao'                        => 'string|max_length[255]',
        'funcao_mes2'                   => 'string|max_length[255]',
        'funcao_mes3'                   => 'string|max_length[255]',
        'funcao_mes4'                   => 'string|max_length[255]',
        'funcao_mes5'                   => 'string|max_length[255]',
        'funcao_mes6'                   => 'string|max_length[255]',
        'funcao_mes7'                   => 'string|max_length[255]',
        'substituicao_semestral'        => 'integer|exact_length[1]',
        'substituicao_eventual'         => 'integer|exact_length[1]',
        'total_dias_mes1'               => 'integer|max_length[2]',
        'total_dias_mes2'               => 'integer|max_length[2]',
        'total_dias_mes3'               => 'integer|max_length[2]',
        'total_dias_mes4'               => 'integer|max_length[2]',
        'total_dias_mes5'               => 'integer|max_length[2]',
        'total_dias_mes6'               => 'integer|max_length[2]',
        'total_dias_mes7'               => 'integer|max_length[2]',
        'dias_descontados_mes1'         => 'valid_time',
        'dias_descontados_mes2'         => 'valid_time',
        'dias_descontados_mes3'         => 'valid_time',
        'dias_descontados_mes4'         => 'valid_time',
        'dias_descontados_mes5'         => 'valid_time',
        'dias_descontados_mes6'         => 'valid_time',
        'dias_descontados_mes7'         => 'valid_time',
        'total_dias_sub1'               => 'integer|max_length[2]',
        'total_dias_sub2'               => 'integer|max_length[2]',
        'total_horas_mes1'              => 'valid_time',
        'total_horas_mes2'              => 'valid_time',
        'total_horas_mes3'              => 'valid_time',
        'total_horas_mes4'              => 'valid_time',
        'total_horas_mes5'              => 'valid_time',
        'total_horas_mes6'              => 'valid_time',
        'total_horas_mes7'              => 'valid_time',
        'total_horas_sub1'              => 'valid_time',
        'total_horas_sub2'              => 'valid_time',
        'horas_descontadas_mes1'        => 'valid_time',
        'horas_descontadas_mes2'        => 'valid_time',
        'horas_descontadas_mes3'        => 'valid_time',
        'horas_descontadas_mes4'        => 'valid_time',
        'horas_descontadas_mes5'        => 'valid_time',
        'horas_descontadas_mes6'        => 'valid_time',
        'horas_descontadas_mes7'        => 'valid_time',
        'horas_descontadas_sub1'        => 'valid_time',
        'horas_descontadas_sub2'        => 'valid_time',
        'total_horas_faturadas_mes1'    => 'valid_time',
        'total_horas_faturadas_mes2'    => 'valid_time',
        'total_horas_faturadas_mes3'    => 'valid_time',
        'total_horas_faturadas_mes4'    => 'valid_time',
        'total_horas_faturadas_mes5'    => 'valid_time',
        'total_horas_faturadas_mes6'    => 'valid_time',
        'total_horas_faturadas_mes7'    => 'valid_time',
        'total_horas_faturadas_sub1'    => 'valid_time',
        'total_horas_faturadas_sub2'    => 'valid_time',
        'total_descontos_mes1'          => 'valid_time',
        'total_descontos_mes2'          => 'valid_time',
        'total_descontos_mes3'          => 'valid_time',
        'total_descontos_mes4'          => 'valid_time',
        'total_descontos_mes5'          => 'valid_time',
        'total_descontos_mes6'          => 'valid_time',
        'total_descontos_mes7'          => 'valid_time',
        'total_descontos_sub1'          => 'valid_time',
        'total_descontos_sub2'          => 'valid_time',
        'valor_pagamento_mes1'          => 'numeric|max_length[10]',
        'valor_pagamento_mes2'          => 'numeric|max_length[10]',
        'valor_pagamento_mes3'          => 'numeric|max_length[10]',
        'valor_pagamento_mes4'          => 'numeric|max_length[10]',
        'valor_pagamento_mes5'          => 'numeric|max_length[10]',
        'valor_pagamento_mes6'          => 'numeric|max_length[10]',
        'valor_pagamento_mes7'          => 'numeric|max_length[10]',
        'valor_pagamento_sub1'          => 'numeric|max_length[10]',
        'valor_pagamento_sub2'          => 'numeric|max_length[10]',
        'valor_total_mes1'              => 'numeric|max_length[10]',
        'valor_total_mes2'              => 'numeric|max_length[10]',
        'valor_total_mes3'              => 'numeric|max_length[10]',
        'valor_total_mes4'              => 'numeric|max_length[10]',
        'valor_total_mes5'              => 'numeric|max_length[10]',
        'valor_total_mes6'              => 'numeric|max_length[10]',
        'valor_total_mes7'              => 'numeric|max_length[10]',
        'valor_total_sub1'              => 'numeric|max_length[10]',
        'valor_total_sub2'              => 'numeric|max_length[10]',
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
