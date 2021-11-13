<?php

namespace App\Models;

use App\Entities\PesquisaLifoComportamento;

class PesquisaLifoComportamentoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'pesquisa_lifo_comportamentos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = PesquisaLifoComportamento::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_estilo',
        'situacao_comportamental',
        'nome',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_estilo'                 => 'required|is_natural_no_zero|max_length[11]',
        'situacao_comportamental'   => 'required|string|max_length[1]',
        'nome'                      => 'required|string|max_length[100]',
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
