<?php

namespace App\Models;

use App\Entities\AnaliseCfeFatorChaveSucesso;

class AnaliseCfeFatorChaveSucessoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'analise_cfe_fatores_chave_sucesso';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = AnaliseCfeFatorChaveSucesso::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_analise',
        'fator_chave',
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
        'id_analise'    => 'required|is_natural_no_zero|max_length[11]',
        'fator_chave'   => 'required|string',
        'peso'          => 'integer|max_length[3]',
        'impacto'       => 'integer|max_length[1]',
        'resultado'     => 'integer|max_length[3]',
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

    public const IMPACTO = [
        '1' => 'Muito reduzido',
        '2' => 'Reduzido',
        '3' => 'Médio',
        '4' => 'Elevado',
        '5' => 'Muito elevado',
    ];

}
