<?php

namespace App\Models;

use App\Entities\BibliotecaQuestao;

class BibliotecaQuestaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'bibliotecaquestoes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = BibliotecaQuestao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'nome',
        'tipo',
        'conteudo',
        'feedback_correta',
        'feedback_incorreta',
        'observacoes',
        'aleatorizacao',
        'id_copia',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'            => 'is_natural_no_zero|max_length[11]',
        'nome'                  => 'required|string|max_length[150]',
        'tipo'                  => 'required|string|max_length[1]',
        'conteudo'              => 'required|string',
        'feedback_correta'      => 'string',
        'feedback_incorreta'    => 'string',
        'observacoes'           => 'string',
        'aleatorizacao'         => 'string|max_length[1]',
        'id_copia'              => 'is_natural_no_zero|max_length[11]',
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
