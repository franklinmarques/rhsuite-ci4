<?php

namespace App\Models;

use App\Entities\IcomSpTelefoneEmergencial;

class IcomSpTelefoneEmergencialModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_sp_telefones_emergenciais';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomSpTelefoneEmergencial::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'nome_servico',
        'id_estado',
        'estado',
        'id_municipio',
        'municipio',
        'localidade',
        'codigo_numerico',
        'nome_prestadora',
        'codigo_tridigito',
        'telefone_1',
        'telefone_alternativo_1',
        'telefone_alternativo_2',
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
        'nome_servico'              => 'required|string|max_length[255]',
        'id_estado'                 => 'required|is_natural_no_zero|max_length[11]',
        'estado'                    => 'string|max_length[3]',
        'id_municipio'              => 'required|is_natural_no_zero|max_length[11]',
        'municipio'                 => 'string|max_length[255]',
        'localidade'                => 'required|string|max_length[50]',
        'codigo_numerico'           => 'required|integer|max_length[2]',
        'nome_prestadora'           => 'required|string|max_length[255]',
        'codigo_tridigito'          => 'required|integer|max_length[3]',
        'telefone_1'                => 'required|string|max_length[255]',
        'telefone_alternativo_1'    => 'string|max_length[255]',
        'telefone_alternativo_2'    => 'string|max_length[255]',
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
