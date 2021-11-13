<?php

namespace App\Models;

use App\Entities\EiEscola;

class EiEscolaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_escolas';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiEscola::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'nome',
        'id_diretoria',
        'codigo',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'municipio',
        'id_estado',
        'telefone',
        'telefone_contato',
        'email',
        'cep',
        'geolocalizacao_1',
        'geolocalizacao_2',
        'pessoas_contato',
        'periodo_manha',
        'periodo_tarde',
        'periodo_noite',
        'nome_diretor',
        'email_diretor',
        'nome_coordenador',
        'email_coordenador',
        'nome_administrativo',
        'email_administrativo',
        'unidade_apoio_1',
        'codigo_apoio_1',
        'unidade_apoio_2',
        'codigo_apoio_2',
        'unidade_apoio_3',
        'codigo_apoio_3',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'nome'                  => 'required|string|max_length[100]',
        'id_diretoria'          => 'required|is_natural_no_zero|max_length[11]',
        'codigo'                => 'integer|max_length[4]',
        'endereco'              => 'string|max_length[255]',
        'numero'                => 'integer|max_length[11]',
        'complemento'           => 'string|max_length[255]',
        'bairro'                => 'string|max_length[50]',
        'municipio'             => 'required|string|max_length[100]',
        'id_estado'             => 'integer|max_length[11]',
        'telefone'              => 'string|max_length[30]',
        'telefone_contato'      => 'string|max_length[30]',
        'email'                 => 'string|max_length[255]',
        'cep'                   => 'string|max_length[20]',
        'geolocalizacao_1'      => 'string|max_length[30]',
        'geolocalizacao_2'      => 'string|max_length[30]',
        'pessoas_contato'       => 'string',
        'periodo_manha'         => 'integer|max_length[1]',
        'periodo_tarde'         => 'integer|max_length[1]',
        'periodo_noite'         => 'integer|max_length[1]',
        'nome_diretor'          => 'string|max_length[255]',
        'email_diretor'         => 'string|max_length[255]',
        'nome_coordenador'      => 'string|max_length[255]',
        'email_coordenador'     => 'string|max_length[255]',
        'nome_administrativo'   => 'string|max_length[255]',
        'email_administrativo'  => 'string|max_length[255]',
        'unidade_apoio_1'       => 'string|max_length[100]',
        'codigo_apoio_1'        => 'integer|max_length[4]',
        'unidade_apoio_2'       => 'string|max_length[100]',
        'codigo_apoio_2'        => 'integer|max_length[4]',
        'unidade_apoio_3'       => 'string|max_length[100]',
        'codigo_apoio_3'        => 'integer|max_length[4]',
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
