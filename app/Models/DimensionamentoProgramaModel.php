<?php

namespace App\Models;

use App\Entities\DimensionamentoPrograma;

class DimensionamentoProgramaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'dimensionamento_programas';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = DimensionamentoPrograma::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_job',
        'id_executor',
        'volume_trabalho',
        'qtde_horas_disponiveis',
        'tipo_valor',
        'tipo_mao_obra',
        'unidades',
        'mao_obra',
        'carga_horaria_necessaria',
        'horario_inicio_projetado',
        'horario_termino_projetado',
        'horario_inicio_real',
        'horario_termino_real',
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
        'id_job'                    => 'required|is_natural_no_zero|max_length[11]',
        'id_executor'               => 'required|is_natural_no_zero|max_length[11]',
        'volume_trabalho'           => 'numeric|max_length[9]',
        'qtde_horas_disponiveis'    => 'numeric|max_length[9]',
        'tipo_valor'                => 'string|max_length[1]',
        'tipo_mao_obra'             => 'string|max_length[1]',
        'unidades'                  => 'string|max_length[10]',
        'mao_obra'                  => 'string|max_length[10]',
        'carga_horaria_necessaria'  => 'numeric|max_length[9]',
        'horario_inicio_projetado'  => 'valid_time',
        'horario_termino_projetado' => 'valid_time',
        'horario_inicio_real'       => 'valid_time',
        'horario_termino_real'      => 'valid_time',
        'status'                    => 'required|string|max_length[1]',
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
