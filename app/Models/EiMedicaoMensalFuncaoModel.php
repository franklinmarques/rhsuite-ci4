<?php

namespace App\Models;

use App\Entities\EiMedicaoMensalFuncao;

class EiMedicaoMensalFuncaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_medicoes_mensais_funcoes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiMedicaoMensalFuncao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_medicao_mensal',
        'cargo',
        'funcao',
        'total_pessoas',
        'total_horas',
        'receita_efetuada',
        'pagamentos_efetuados',
        'resultado_monetario',
        'resultado_percentual',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_medicao_mensal'     => 'required|is_natural_no_zero|max_length[11]',
        'cargo'                 => 'required|string|max_length[255]',
        'funcao'                => 'required|string|max_length[255]',
        'total_pessoas'         => 'required|integer|max_length[11]',
        'total_horas'           => 'required|string|max_length[9]',
        'receita_efetuada'      => 'required|numeric|max_length[10]',
        'pagamentos_efetuados'  => 'required|numeric|max_length[10]',
        'resultado_monetario'   => 'required|numeric|max_length[10]',
        'resultado_percentual'  => 'required|numeric|max_length[4]',
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
