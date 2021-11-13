<?php

namespace App\Models;

use App\Entities\RecrutamentoModelo;

class RecrutamentoModeloModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'recrutamento_modelos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = RecrutamentoModelo::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'nome',
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
        'id_empresa'    => 'required|is_natural_no_zero|max_length[11]',
        'nome'          => 'required|string|max_length[50]',
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

    public const TIPOS = [
        'M' => 'Matemática',
        'R' => 'Raciocínio Lógico',
        'P' => 'Português',
        'C' => 'Personalidade - Eneagrama',
        'J' => 'Personalidade - Jung',
        'L' => 'Liderança',
        'D' => 'Digitação',
        'I' => 'Interpretação',
        'T' => 'Conhecimento técnico',
        'A' => 'Conhecimento comportamental',
        'E' => 'Questões dissertativas',
        'F' => 'Preferência Cerebral & Perfil Comportamental',
    ];
}
