<?php

namespace App\Models;

use App\Entities\DimensionamentoMedicao;

class DimensionamentoMedicaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'dimensionamento_medicoes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = DimensionamentoMedicao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_executor',
        'id_etapa',
        'tempo_inicio',
        'tempo_termino',
        'tempo_gasto',
        'quantidade',
        'tempo_unidade',
        'indice_mao_obra',
        'complexidade',
        'tipo_item',
        'medicao_calculada',
        'valor_min_calculado',
        'valor_medio_calculado',
        'valor_max_calculado',
        'mao_obra_min_calculada',
        'mao_obra_media_calculada',
        'mao_obra_max_calculada',
        'status',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_executor'               => 'required|is_natural_no_zero|max_length[11]',
        'id_etapa'                  => 'required|is_natural_no_zero|max_length[11]',
        'tempo_inicio'              => 'required|numeric|max_length[9]',
        'tempo_termino'             => 'required|numeric|max_length[9]',
        'tempo_gasto'               => 'numeric|max_length[9]',
        'quantidade'                => 'numeric|max_length[9]',
        'tempo_unidade'             => 'numeric|max_length[9]',
        'indice_mao_obra'           => 'numeric|max_length[9]',
        'complexidade'              => 'integer|max_length[11]',
        'tipo_item'                 => 'integer|max_length[11]',
        'medicao_calculada'         => 'required|integer|exact_length[1]',
        'valor_min_calculado'       => 'numeric|max_length[9]',
        'valor_medio_calculado'     => 'numeric|max_length[9]',
        'valor_max_calculado'       => 'numeric|max_length[9]',
        'mao_obra_min_calculada'    => 'numeric|max_length[9]',
        'mao_obra_media_calculada'  => 'numeric|max_length[9]',
        'mao_obra_max_calculada'    => 'numeric|max_length[9]',
        'status'                    => 'required|integer|exact_length[1]',
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
