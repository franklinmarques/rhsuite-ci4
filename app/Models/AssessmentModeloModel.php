<?php

namespace App\Models;

use App\Entities\AssessmentModelo;

class AssessmentModeloModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'assessments_modelos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = AssessmentModelo::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'nome',
        'id_empresa',
        'tipo',
        'observacoes',
        'instrucoes',
        'aleatorizacao',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'nome'          => 'required|string|max_length[50]',
        'id_empresa'    => 'required|is_natural_no_zero|max_length[11]',
        'tipo'          => 'required|string|max_length[1]',
        'observacoes'   => 'string',
        'instrucoes'    => 'string',
        'aleatorizacao' => 'string|max_length[1]',
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

    public const TIPO = [
        'C' => 'Pesquisa de Clima Organizacional',
        'P' => 'Pesquisa de Perfil Profissional (uma única resposta)',
        'M' => 'Pesquisa de Perfil Profissional (múltiplas respostas)',
        'E' => 'Avaliação de Personalidade (Eneagrama)',
        'Q' => 'Avaliação de Personalidade (Tipologia Junguiana)',
        'O' => 'Avaliação de Personalidade (Orientações de Vida)',
        'N' => 'Avaliação de Potencial (NineBox)',
    ];

}
