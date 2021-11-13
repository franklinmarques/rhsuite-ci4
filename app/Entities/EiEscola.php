<?php

namespace App\Entities;

class EiEscola extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'nome' => 'string',
        'id_diretoria' => 'int',
        'codigo' => '?int',
        'endereco' => '?string',
        'numero' => '?int',
        'complemento' => '?string',
        'bairro' => '?string',
        'municipio' => 'string',
        'id_estado' => '?int',
        'telefone' => '?string',
        'telefone_contato' => '?string',
        'email' => '?string',
        'cep' => '?string',
        'geolocalizacao_1' => '?string',
        'geolocalizacao_2' => '?string',
        'pessoas_contato' => '?string',
        'periodo_manha' => '?int',
        'periodo_tarde' => '?int',
        'periodo_noite' => '?int',
        'nome_diretor' => '?string',
        'email_diretor' => '?string',
        'nome_coordenador' => '?string',
        'email_coordenador' => '?string',
        'nome_administrativo' => '?string',
        'email_administrativo' => '?string',
        'unidade_apoio_1' => '?string',
        'codigo_apoio_1' => '?int',
        'unidade_apoio_2' => '?string',
        'codigo_apoio_2' => '?int',
        'unidade_apoio_3' => '?string',
        'codigo_apoio_3' => '?int',
    ];
}
