<?php

namespace App\Models;

use App\Entities\DimensionamentoCronoAnalise;

class DimensionamentoCronoAnaliseModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'dimensionamento_crono_analises';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = DimensionamentoCronoAnalise::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'nome',
        'id_processo',
        'data_inicio',
        'data_termino',
        'status',
        'base_tempo',
        'unidade_producao',
        'data_inicio_apuracao',
        'data_termino_apuracao',
        'tipo_apuracao',
        'nivel_apuracao',
        'id_processo_apuracao',
        'id_atividade_apuracao',
        'id_etapa_apuracao',
        'status_apontamento',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'            => 'required|is_natural_no_zero|max_length[11]',
        'nome'                  => 'required|string|max_length[255]',
        'id_processo'           => 'is_natural_no_zero|max_length[11]',
        'data_inicio'           => 'required|valid_date',
        'data_termino'          => 'required|valid_date',
        'status'                => 'string|max_length[1]',
        'base_tempo'            => 'string|max_length[1]',
        'unidade_producao'      => 'string|max_length[30]',
        'data_inicio_apuracao'  => 'valid_date',
        'data_termino_apuracao' => 'valid_date',
        'tipo_apuracao'         => 'string|max_length[1]',
        'nivel_apuracao'        => 'integer|exact_length[1]',
        'id_processo_apuracao'  => 'integer|max_length[11]',
        'id_atividade_apuracao' => 'integer|max_length[11]',
        'id_etapa_apuracao'     => 'integer|max_length[11]',
        'status_apontamento'    => 'integer|exact_length[1]',
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
