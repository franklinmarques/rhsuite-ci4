<?php

namespace App\Models;

use App\Entities\CompetenciaSugestao;

class CompetenciaSugestaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'competencias_sugestoes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = CompetenciaSugestao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'nome',
        'tipo',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'nome' => 'required|string|max_length[200]',
        'tipo' => 'required|integer|max_length[11]',
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

    public const TIPO = [
        '1' => 'Técnica',
        '2' => 'Comportamental',
    ];
}
