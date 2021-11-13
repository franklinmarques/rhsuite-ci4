<?php

namespace App\Models;

use App\Entities\EiAlocacaoEscola;

class EiAlocacaoEscolaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_alocacoes_escolas';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiAlocacaoEscola::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_alocacao',
        'id_os_escola',
        'id_escola',
        'codigo',
        'escola',
        'municipio',
        'ordem_servico',
        'contrato',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_alocacao'   => 'required|is_natural_no_zero|max_length[11]',
        'id_os_escola'  => 'is_natural_no_zero|max_length[11]',
        'id_escola'     => 'is_natural_no_zero|max_length[11]',
        'codigo'        => 'integer|max_length[4]',
        'escola'        => 'required|string|max_length[255]',
        'municipio'     => 'required|string|max_length[255]',
        'ordem_servico' => 'required|string|max_length[255]',
        'contrato'      => 'required|string|max_length[30]',
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
