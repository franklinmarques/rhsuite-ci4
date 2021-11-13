<?php

namespace App\Models;

use App\Entities\FacilityRealizacaoLaudo;

class FacilityRealizacaoLaudoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'facilities_realizacoes_laudos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = FacilityRealizacaoLaudo::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_realizacao',
        'id_item',
        'arquivo',
        'tipo_mime',
        'data_cadastro',
        'local_armazem',
        'sala_box',
        'arquivo_fisico',
        'pasta_caixa',
        'codigo_localizador',
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
        'id_item'               => 'required|is_natural_no_zero|max_length[11]',
        'arquivo'               => 'required|string|max_length[255]',
        'tipo_mime'             => 'required|string|max_length[50]',
        'data_cadastro'         => 'required|valid_date',
        'local_armazem'         => 'string|max_length[255]',
        'sala_box'              => 'string|max_length[255]',
        'arquivo_fisico'        => 'string|max_length[255]',
        'pasta_caixa'           => 'string|max_length[255]',
        'codigo_localizador'    => 'string|max_length[32]',
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
