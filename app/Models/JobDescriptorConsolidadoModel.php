<?php

namespace App\Models;

use App\Entities\JobDescriptorConsolidado;

class JobDescriptorConsolidadoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'job_descriptor_consolidados';
	protected $primaryKey           = 'id_descritor';
	protected $useAutoIncrement     = false;
	protected $insertID             = 0;
	protected $returnType           = JobDescriptorConsolidado::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_descritor',
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
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_descritor'                  => 'required|integer|max_length[11]',
        'sumario'                       => 'string',
        'formacao_experiencia'          => 'string',
        'condicoes_gerais_exercicio'    => 'string',
        'codigo_internacional_ciuo88'   => 'string',
        'notas'                         => 'string',
        'recursos_trabalho'             => 'string',
        'atividades'                    => 'string',
        'responsabilidades'             => 'string',
        'conhecimentos_habilidades'     => 'string',
        'habilidades_basicas'           => 'string',
        'habilidades_intermediarias'    => 'string',
        'habilidades_avancadas'         => 'string',
        'ambiente_trabalho'             => 'string',
        'condicoes_trabalho'            => 'string',
        'esforcos_fisicos'              => 'string',
        'grau_autonomia'                => 'string',
        'grau_complexidade'             => 'string',
        'grau_iniciativa'               => 'string',
        'competencias_tecnicas'         => 'string',
        'competencias_comportamentais'  => 'string',
        'tempo_experiencia'             => 'string',
        'formacao_minima'               => 'string',
        'formacao_plena'                => 'string',
        'esforcos_mentais'              => 'string',
        'grau_pressao'                  => 'string',
        'campo_livre1'                  => 'string',
        'campo_livre2'                  => 'string',
        'campo_livre3'                  => 'string',
        'campo_livre4'                  => 'string',
        'campo_livre5'                  => 'string',
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
