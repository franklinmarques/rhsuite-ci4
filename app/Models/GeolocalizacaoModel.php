<?php

namespace App\Models;

use App\Entities\Geolocalizacao;

class GeolocalizacaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'geolocalizacoes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = Geolocalizacao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'local',
        'endereco',
        'numero',
        'bairro',
        'id_cidade',
        'id_estado',
        'latitude',
        'longitude',
        'email',
        'usuario',
        'senha',
        'ativo',
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
        'local'         => 'string|max_length[255]',
        'endereco'      => 'string|max_length[255]',
        'numero'        => 'integer|max_length[11]',
        'bairro'        => 'string|max_length[255]',
        'id_cidade'     => 'is_natural_no_zero|max_length[11]',
        'id_estado'     => 'is_natural_no_zero|max_length[11]',
        'latitude'      => 'string|max_length[10]',
        'longitude'     => 'string|max_length[10]',
        'email'         => 'string|max_length[255]',
        'usuario'       => 'string|max_length[255]',
        'senha'         => 'string|max_length[255]',
        'ativo'         => 'required|integer|exact_length[1]',
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
