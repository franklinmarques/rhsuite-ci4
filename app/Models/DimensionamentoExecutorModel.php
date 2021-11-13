<?php

namespace App\Models;

use App\Entities\DimensionamentoExecutor;

class DimensionamentoExecutorModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'dimensionamento_executores';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = DimensionamentoExecutor::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_crono_analise',
        'tipo',
        'id_equipe',
        'id_usuario',
        'status_apontamento_ativo',
        'tipo_apuracao',
        'nivel_apuracao',
        'id_processo_apuracao',
        'id_atividade_apuracao',
        'id_etapa_apuracao',
        'data_inicio_apuracao',
        'data_termino_apuracao',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_crono_analise'          => 'required|is_natural_no_zero|max_length[11]',
        'tipo'                      => 'required|string|max_length[1]',
        'id_equipe'                 => 'is_natural_no_zero|max_length[11]',
        'id_usuario'                => 'is_natural_no_zero|max_length[11]',
        'status_apontamento_ativo'  => 'integer|exact_length[1]',
        'tipo_apuracao'             => 'string|max_length[1]',
        'nivel_apuracao'            => 'integer|exact_length[1]',
        'id_processo_apuracao'      => 'integer|max_length[11]',
        'id_atividade_apuracao'     => 'integer|max_length[11]',
        'id_etapa_apuracao'         => 'integer|max_length[11]',
        'data_inicio_apuracao'      => 'valid_date',
        'data_termino_apuracao'     => 'valid_date',
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

    //--------------------------------------------------------------------

    public const TIPOS = [
        'E' => 'Equipes',
        'C' => 'Colaboradores',
    ];
    public const STATUS_APONTAMENTO = [
        '1' => 'Ativo',
        '0' => 'Inativo',
    ];
    public const TIPOS_APURACAO = [
        'A' => 'Automático',
        'M' => 'Manual',
    ];
    public const NIVEIS_APURACAO = [
        '1' => 'Processo',
        '2' => 'Atividade',
        '3' => 'Etapa',
    ];
}
