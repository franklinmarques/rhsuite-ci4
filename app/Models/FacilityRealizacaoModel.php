<?php

namespace App\Models;

use App\Entities\FacilityRealizacao;

class FacilityRealizacaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'facilities_realizacoes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = FacilityRealizacao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'id_modelo',
        'mes',
        'ano',
        'pendencias',
        'id_usuario_vistoriador',
        'tipo_executor',
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
        'id_empresa'                => 'required|is_natural_no_zero|max_length[11]',
        'id_modelo'                 => 'required|is_natural_no_zero|max_length[11]',
        'mes'                       => 'required|integer|max_length[2]',
        'ano'                       => 'required|int|max_length[4]',
        'pendencias'                => 'required|integer|exact_length[1]',
        'id_usuario_vistoriador'    => 'is_natural_no_zero|max_length[11]',
        'tipo_executor'             => 'string|max_length[1]',
        'status'                    => 'required|string|max_length[1]',
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
