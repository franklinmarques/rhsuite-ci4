<?php

namespace App\Models;

use App\Entities\PesquisaAvaliador;

class PesquisaAvaliadorModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'pesquisa_avaliadores';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = PesquisaAvaliador::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_pesquisa',
        'id_avaliador',
        'id_avaliado',
        'data_acesso',
        'data_finalizacao',
        'estilo_personalidade_majoritario',
        'estilo_personalidade_secundario',
        'laudo_comportamental_padrao',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_pesquisa'                       => 'required|is_natural_no_zero|max_length[11]',
        'id_avaliador'                      => 'required|is_natural_no_zero|max_length[11]',
        'id_avaliado'                       => 'is_natural_no_zero|max_length[11]',
        'data_acesso'                       => 'valid_date',
        'data_finalizacao'                  => 'valid_date',
        'estilo_personalidade_majoritario'  => 'string',
        'estilo_personalidade_secundario'   => 'string',
        'laudo_comportamental_padrao'       => 'string',
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
