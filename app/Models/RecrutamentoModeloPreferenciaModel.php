<?php

namespace App\Models;

use App\Entities\RecrutamentoModeloPreferencia;

class RecrutamentoModeloPreferenciaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'recrutamento_modelos_preferencias';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = RecrutamentoModeloPreferencia::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'indice',
        'descricao',
        'tipo_resultado',
        'caracteristicas_principais',
        'tracos_comportamentais',
        'pontos_fortes_titulo',
        'pontos_fortes_descricao',
        'pontos_melhoria_titulo',
        'pontos_melhoria_descricao',
        'motivacoes',
        'valores',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'indice'                        => 'required|string|max_length[1]',
        'descricao'                     => 'required|string|max_length[7]',
        'tipo_resultado'                => 'required|string|max_length[40]',
        'caracteristicas_principais'    => 'required|string|max_length[15]',
        'tracos_comportamentais'        => 'required|string',
        'pontos_fortes_titulo'          => 'required|string|max_length[11]',
        'pontos_fortes_descricao'       => 'required|string',
        'pontos_melhoria_titulo'        => 'required|string|max_length[11]',
        'pontos_melhoria_descricao'     => 'required|string',
        'motivacoes'                    => 'required|string',
        'valores'                       => 'required|string',
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
