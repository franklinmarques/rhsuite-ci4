<?php

namespace App\Models;

use App\Entities\CompetenciaResultado;

class CompetenciaResultadoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'competencias_resultados';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = CompetenciaResultado::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_avaliador',
        'cargo_dimensao',
        'nivel',
        'atitude',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_avaliador'      => 'required|is_natural_no_zero|max_length[11]',
        'cargo_dimensao'    => 'required|is_natural_no_zero|max_length[11]',
        'nivel'             => 'integer|max_length[11]',
        'atitude'           => 'integer|max_length[11]',
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

    public const NIVEL = [
        '0' => 'Nenhum conhecimento',
        '1' => 'Conhecimento básico',
        '2' => 'Conhecimento e prática básicos',
        '3' => 'Conhecimento e prática intermediários',
        '4' => 'Conhecimento e prática avancados',
        '5' => 'Especialista e multiplicador',
    ];
}
