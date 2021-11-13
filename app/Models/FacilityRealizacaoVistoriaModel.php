<?php

namespace App\Models;

use App\Entities\FacilityRealizacaoVistoria;

class FacilityRealizacaoVistoriaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'facilities_realizacoes_vistorias';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = FacilityRealizacaoVistoria::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_realizacao',
        'id_modelo_vistoria',
        'numero_os',
        'possui_problema',
        'vistoriado',
        'nao_aplicavel',
        'descricao_problema',
        'observacoes',
        'data_abertura',
        'data_realizacao',
        'realizacao_cat',
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
        'id_realizacao'         => 'required|is_natural_no_zero|max_length[11]',
        'id_modelo_vistoria'    => 'required|is_natural_no_zero|max_length[11]',
        'numero_os'             => 'required|string|max_length[20]',
        'possui_problema'       => 'integer|exact_length[1]',
        'vistoriado'            => 'integer|exact_length[1]',
        'nao_aplicavel'         => 'required|integer|exact_length[1]',
        'descricao_problema'    => 'string',
        'observacoes'           => 'string',
        'data_abertura'         => 'valid_date',
        'data_realizacao'       => 'valid_date',
        'realizacao_cat'        => 'string|max_length[255]',
        'status'                => 'string|max_length[1]',
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
