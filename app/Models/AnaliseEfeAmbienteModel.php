<?php

namespace App\Models;

use App\Entities\AnaliseEfeAmbiente;

class AnaliseEfeAmbienteModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'analise_efe_ambientes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = AnaliseEfeAmbiente::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_analise',
        'status',
        'risco_oportunidade',
        'peso',
        'impacto',
        'probabilidade_ocorrencia',
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
        'id_analise'                => 'required|is_natural_no_zero|max_length[11]',
        'status'                    => 'required|integer|exact_length[1]',
        'risco_oportunidade'        => 'required|string',
        'peso'                      => 'integer|max_length[3]',
        'impacto'                   => 'integer|max_length[1]',
        'probabilidade_ocorrencia'  => 'integer|max_length[3]',
        'resultado'                 => 'numeric|max_length[5]',
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
        '0' => 'Risco',
        '1' => 'Oportunidade',
    ];
    public const IMPACTO = [
        '1' => 'Muito reduzido',
        '2' => 'Reduzido',
        '3' => 'Médio',
        '4' => 'Elevado',
        '5' => 'Muito elevado',
    ];

}
