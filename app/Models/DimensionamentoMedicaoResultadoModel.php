<?php

namespace App\Models;

use App\Entities\DimensionamentoMedicaoResultado;

class DimensionamentoMedicaoResultadoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'dimensionamento_medicoes_resultados';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = DimensionamentoMedicaoResultado::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'id_usuario',
        'id_crono_analise',
        'id_executor',
        'id_processo',
        'id_atividade',
        'id_etapa',
        'grau_complexidade',
        'tamanho_item',
        'soma_menor',
        'soma_media',
        'soma_maior',
        'mao_obra_menor',
        'mao_obra_media',
        'mao_obra_maior',
        'data_cadastro',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'        => 'required|is_natural_no_zero|max_length[11]',
        'id_usuario'        => 'required|is_natural_no_zero|max_length[11]',
        'id_crono_analise'  => 'required|is_natural_no_zero|max_length[11]',
        'id_executor'       => 'required|is_natural_no_zero|max_length[11]',
        'id_processo'       => 'is_natural_no_zero|max_length[11]',
        'id_atividade'      => 'is_natural_no_zero|max_length[11]',
        'id_etapa'          => 'is_natural_no_zero|max_length[11]',
        'grau_complexidade' => 'integer|exact_length[1]',
        'tamanho_item'      => 'integer|exact_length[1]',
        'soma_menor'        => 'required|numeric|max_length[9]',
        'soma_media'        => 'required|numeric|max_length[9]',
        'soma_maior'        => 'required|numeric|max_length[9]',
        'mao_obra_menor'    => 'required|numeric|max_length[9]',
        'mao_obra_media'    => 'required|numeric|max_length[9]',
        'mao_obra_maior'    => 'required|numeric|max_length[9]',
        'data_cadastro'     => 'required|valid_date',
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
