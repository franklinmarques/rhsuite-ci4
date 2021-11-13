<?php

namespace App\Models;

use App\Entities\AvaliacaoExpModelo;

class AvaliacaoExpModeloModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'avaliacao_exp_modelos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = AvaliacaoExpModelo::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'nome',
        'tipo',
        'observacao',
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
        'id_empresa'    => 'required|is_natural_no_zero|max_length[11]',
        'nome'          => 'required|string|max_length[255]',
        'tipo'          => 'required|string|max_length[1]',
        'observacao'    => 'string',
        'id_copia'      => 'is_natural_no_zero|max_length[11]',
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

    //--------------------------------------------------------------------

    public const TIPOS = [
        'A' => 'Avaliação periódica (múltiplas alternativas)',
        'D' => 'Avaliação de desempenho',
        'P' => 'Período de experiência',
        'M' => 'Avaliação de experiência (respostas mistas)',
    ];

}
