<?php

namespace App\Models;

use App\Entities\AnaliseSwotDimensao;

class AnaliseSwotDimensaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'analise_swot_dimensoes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = AnaliseSwotDimensao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_analise',
        'tipo_ambiente',
        'avaliacao',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_analise'    => 'required|is_natural_no_zero|max_length[11]',
        'tipo_ambiente' => 'required|string|max_length[1]',
        'avaliacao'     => 'required|string',
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

    public const TIPOS_AMBIENTE = [
        'I' => 'Interno',
        'E' => 'Externo',
    ];

}
