<?php

namespace App\Models;

use App\Entities\JobDescriptor;

class JobDescriptorModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'job_descriptor';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = JobDescriptor::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'id_cargo',
        'id_funcao',
        'versao',
        'data',
        'sumario',
        'formacao_experiencia',
        'condicoes_gerais_exercicio',
        'codigo_internacional_ciuo88',
        'notas',
        'recursos_trabalho',
        'atividades',
        'responsabilidades',
        'conhecimentos_habilidades',
        'habilidades_basicas',
        'habilidades_intermediarias',
        'habilidades_avancadas',
        'ambiente_trabalho',
        'condicoes_trabalho',
        'esforcos_fisicos',
        'grau_autonomia',
        'grau_complexidade',
        'grau_iniciativa',
        'competencias_tecnicas',
        'competencias_comportamentais',
        'tempo_experiencia',
        'formacao_minima',
        'formacao_plena',
        'esforcos_mentais',
        'grau_pressao',
        'campo_livre1',
        'campo_livre2',
        'campo_livre3',
        'campo_livre4',
        'campo_livre5',
        'id_versao_anterior',
        'versao_homologada'
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
        'id_cargo'                      => 'required|is_natural_no_zero|max_length[11]',
        'id_funcao'                     => 'required|is_natural_no_zero|max_length[11]',
        'versao'                        => 'required|string|max_length[255]',
        'data'                          => 'required|valid_date',
        'sumario'                       => 'required|integer|exact_length[1]',
        'formacao_experiencia'          => 'required|integer|exact_length[1]',
        'condicoes_gerais_exercicio'    => 'required|integer|exact_length[1]',
        'codigo_internacional_ciuo88'   => 'required|integer|exact_length[1]',
        'notas'                         => 'required|integer|exact_length[1]',
        'recursos_trabalho'             => 'required|integer|exact_length[1]',
        'atividades'                    => 'required|integer|exact_length[1]',
        'responsabilidades'             => 'required|integer|exact_length[1]',
        'conhecimentos_habilidades'     => 'required|integer|exact_length[1]',
        'habilidades_basicas'           => 'required|integer|exact_length[1]',
        'habilidades_intermediarias'    => 'required|integer|exact_length[1]',
        'habilidades_avancadas'         => 'required|integer|exact_length[1]',
        'ambiente_trabalho'             => 'required|integer|exact_length[1]',
        'condicoes_trabalho'            => 'required|integer|exact_length[1]',
        'esforcos_fisicos'              => 'required|integer|exact_length[1]',
        'grau_autonomia'                => 'required|integer|exact_length[1]',
        'grau_complexidade'             => 'required|integer|exact_length[1]',
        'grau_iniciativa'               => 'required|integer|exact_length[1]',
        'competencias_tecnicas'         => 'required|integer|exact_length[1]',
        'competencias_comportamentais'  => 'required|integer|exact_length[1]',
        'tempo_experiencia'             => 'required|integer|exact_length[1]',
        'formacao_minima'               => 'required|integer|exact_length[1]',
        'formacao_plena'                => 'required|integer|exact_length[1]',
        'esforcos_mentais'              => 'required|integer|exact_length[1]',
        'grau_pressao'                  => 'required|integer|exact_length[1]',
        'campo_livre1'                  => 'string|max_length[255]',
        'campo_livre2'                  => 'string|max_length[255]',
        'campo_livre3'                  => 'string|max_length[255]',
        'campo_livre4'                  => 'string|max_length[255]',
        'campo_livre5'                  => 'string|max_length[255]',
        'id_versao_anterior'            => 'integer|max_length[11]',
        'versao_homologada'             => 'integer|exact_length[1]',
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
