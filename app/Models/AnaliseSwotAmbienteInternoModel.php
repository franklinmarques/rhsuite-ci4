<?php

namespace App\Models;

use App\Entities\AnaliseSwotAmbienteInterno;

class AnaliseSwotAmbienteInternoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'analise_swot_ambiente_interno';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = AnaliseSwotAmbienteInterno::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_analise',
        'status',
        'ponto_fraco_forte',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_analise'        => 'required|is_natural_no_zero|max_length[11]',
        'status'            => 'required|integer|exact_length[1]',
        'ponto_fraco_forte' => 'required|string',
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

    public const STATUS = [
        '0' => 'Ponto fraco',
        '1' => 'Ponto forte',
    ];

}
