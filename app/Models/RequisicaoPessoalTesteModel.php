<?php

namespace App\Models;

use App\Entities\RequisicaoPessoalTeste;

class RequisicaoPessoalTesteModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'requisicoes_pessoal_testes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = RequisicaoPessoalTeste::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_candidato',
        'tipo_teste',
        'id_modelo',
        'nome',
        'data_inicio',
        'data_termino',
        'minutos_duracao',
        'aleatorizacao',
        'data_acesso',
        'data_envio',
        'nota_aproveitamento',
        'observacoes',
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
        'id_candidato'          => 'required|is_natural_no_zero|max_length[11]',
        'tipo_teste'            => 'required|string|max_length[1]',
        'id_modelo'             => 'is_natural_no_zero|max_length[11]',
        'nome'                  => 'string|max_length[255]',
        'data_inicio'           => 'required|valid_date',
        'data_termino'          => 'valid_date',
        'minutos_duracao'       => 'integer|max_length[11]',
        'aleatorizacao'         => 'string|max_length[1]',
        'data_acesso'           => 'valid_date',
        'data_envio'            => 'valid_date',
        'nota_aproveitamento'   => 'numeric|max_length[3]',
        'observacoes'           => 'string',
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
