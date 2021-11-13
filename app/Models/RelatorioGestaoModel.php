<?php

namespace App\Models;

use App\Entities\RelatorioGestao;

class RelatorioGestaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'relatorios_gestao';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = RelatorioGestao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'id_usuario',
        'id_depto',
        'id_area',
        'id_setor',
        'mes_referencia',
        'ano_referencia',
        'data_fechamento',
        'indicadores',
        'riscos_oportunidades',
        'ocorrencias',
        'necessidades_investimentos',
        'objetivos_imediatos',
        'objetivos_futuros',
        'parecer_final',
        'observacoes',
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
        'id_empresa'                    => 'required|is_natural_no_zero|max_length[11]',
        'id_usuario'                    => 'required|is_natural_no_zero|max_length[11]',
        'id_depto'                      => 'is_natural_no_zero|max_length[11]',
        'id_area'                       => 'is_natural_no_zero|max_length[11]',
        'id_setor'                      => 'is_natural_no_zero|max_length[11]',
        'mes_referencia'                => 'required|integer|exact_length[2]',
        'ano_referencia'                => 'required|int|max_length[4]',
        'data_fechamento'               => 'required|valid_date',
        'indicadores'                   => 'string',
        'riscos_oportunidades'          => 'string',
        'ocorrencias'                   => 'string',
        'necessidades_investimentos'    => 'string',
        'objetivos_imediatos'           => 'string',
        'objetivos_futuros'             => 'string',
        'parecer_final'                 => 'string',
        'observacoes'                   => 'string',
        'status'                        => 'required|string|max_length[1]',
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
