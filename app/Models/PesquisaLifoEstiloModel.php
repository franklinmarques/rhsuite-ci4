<?php

namespace App\Models;

use App\Entities\PesquisaLifoEstilo;

class PesquisaLifoEstiloModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'pesquisa_lifo_estilos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = PesquisaLifoEstilo::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'nome',
        'indice_resposta',
        'estilo_personalidade_majoritario',
        'estilo_personalidade_secundario',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'                        => 'required|is_natural_no_zero|max_length[11]',
        'nome'                              => 'required|string|max_length[20]',
        'indice_resposta'                   => 'required|integer|max_length[1]',
        'estilo_personalidade_majoritario'  => 'string',
        'estilo_personalidade_secundario'   => 'string',
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
