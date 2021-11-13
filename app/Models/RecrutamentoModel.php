<?php

namespace App\Models;

use App\Entities\Recrutamento;

class RecrutamentoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'recrutamento';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = Recrutamento::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'nome',
        'data_inicio',
        'data_termino',
        'requisitante',
        'tipo_vaga',
        'status',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'    => 'required|is_natural_no_zero|max_length[11]',
        'nome'          => 'required|string|max_length[50]',
        'data_inicio'   => 'required|valid_date',
        'data_termino'  => 'required|valid_date',
        'requisitante'  => 'required|string|max_length[50]',
        'tipo_vaga'     => 'string|max_length[1]',
        'status'        => 'string|max_length[1]',
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
