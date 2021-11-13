<?php

namespace App\Models;

use App\Entities\AnaliseAdl;

class AnaliseAdlModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'analise_adl';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = AnaliseAdl::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'nome',
        'data',
        'tipo',
        'descricao',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'    => 'required|is_natural_no_zero|max_length[11]',
        'nome'          => 'required|string|max_length[255]',
        'data'          => 'required|valid_date',
        'tipo'          => 'required|string|max_length[1]',
        'descricao'     => 'string',
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
        'P' => 'Produto',
        'U' => 'Portifólio',
    ];
    public const TIPOS_POR_EXTENSO = [
        'P' => 'Produtos',
        'U' => 'Unidade estratégica de negócios',
    ];

}
