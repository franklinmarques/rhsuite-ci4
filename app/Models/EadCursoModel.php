<?php

namespace App\Models;

use App\Entities\EadCurso;

class EadCursoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ead_cursos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EadCurso::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'nome',
        'id_empresa',
        'publico',
        'gratuito',
        'descricao',
        'data_cadastro',
        'data_editado',
        'horas_duracao',
        'objetivos',
        'competencias_genericas',
        'competencias_especificas',
        'competencias_comportamentais',
        'categoria',
        'id_categoria',
        'area_conhecimento',
        'id_area',
        'consultor',
        'foto_consultor',
        'curriculo',
        'foto_treinamento',
        'pre_requisitos',
        'progressao_linear',
        'status',
        'id_copia',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'nome'                          => 'required|string|max_length[255]',
        'id_empresa'                    => 'required|is_natural_no_zero|max_length[11]',
        'publico'                       => 'required|integer|max_length[1]',
        'gratuito'                      => 'required|integer|max_length[1]',
        'descricao'                     => 'string',
        'data_cadastro'                 => 'required|valid_date',
        'data_editado'                  => 'valid_date',
        'horas_duracao'                 => 'required|integer|max_length[11]',
        'objetivos'                     => 'string',
        'competencias_genericas'        => 'string|max_length[100]',
        'competencias_especificas'      => 'string|max_length[100]',
        'competencias_comportamentais'  => 'string|max_length[100]',
        'categoria'                     => 'string|max_length[100]',
        'id_categoria'                  => 'is_natural_no_zero|max_length[11]',
        'area_conhecimento'             => 'string|max_length[100]',
        'id_area'                       => 'is_natural_no_zero|max_length[11]',
        'consultor'                     => 'string|max_length[100]',
        'foto_consultor'                => 'string|max_length[255]',
        'curriculo'                     => 'string',
        'foto_treinamento'              => 'string|max_length[255]',
        'pre_requisitos'                => 'string|max_length[100]',
        'progressao_linear'             => 'required|integer|max_length[1]',
        'status'                        => 'required|integer|max_length[1]',
        'id_copia'                      => 'is_natural_no_zero|max_length[11]',
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
