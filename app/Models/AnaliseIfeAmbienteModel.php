<?php

namespace App\Models;

use App\Entities\AnaliseIfeAmbiente;

class AnaliseIfeAmbienteModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'analise_ife_ambientes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = AnaliseIfeAmbiente::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_analise',
        'status',
        'ponto_fraco_forte',
        'peso',
        'impacto',
        'resultado',
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
        'peso'              => 'integer|max_length[3]',
        'impacto'           => 'integer|max_length[1]',
        'resultado'         => 'integer|max_length[3]',
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
        '0' => 'Fraqueza',
        '1' => 'Força',
    ];
    public const IMPACTO = [
        '1' => 'Muito reduzido',
        '2' => 'Reduzido',
        '3' => 'Médio',
        '4' => 'Elevado',
        '5' => 'Muito elevado',
    ];

}
