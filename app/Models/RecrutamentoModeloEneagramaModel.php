<?php

namespace App\Models;

use App\Entities\RecrutamentoModeloEneagrama;

class RecrutamentoModeloEneagramaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'recrutamento_modelos_eneagrama';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = RecrutamentoModeloEneagrama::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'tipo_personalidade',
        'tipo_eneagramatico',
        'perfil_sensorial',
        'nivel_interacao',
        'ponto_foco',
        'agentes_positivos',
        'agentes_negativos',
        'elemento_compulsivo',
        'caracteristicas_positivas',
        'caracteristicas_negativas',
        'acao_prioritaria',
        'vicios',
        'desdobramentos_negativos',
        'areas_atuacao',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'tipo_personalidade'        => 'required|string|max_length[30]',
        'tipo_eneagramatico'        => 'required|string|max_length[50]',
        'perfil_sensorial'          => 'required|string|max_length[10]',
        'nivel_interacao'           => 'required|string|max_length[12]',
        'ponto_foco'                => 'required|string|max_length[255]',
        'agentes_positivos'         => 'required|string|max_length[255]',
        'agentes_negativos'         => 'required|string|max_length[255]',
        'elemento_compulsivo'       => 'required|string|max_length[255]',
        'caracteristicas_positivas' => 'required|string|max_length[255]',
        'caracteristicas_negativas' => 'required|string|max_length[255]',
        'acao_prioritaria'          => 'required|string',
        'vicios'                    => 'required|string',
        'desdobramentos_negativos'  => 'required|string',
        'areas_atuacao'             => 'required|string',
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
