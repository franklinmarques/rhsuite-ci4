<?php

namespace App\Models;

use App\Entities\GestaoVaga;

class GestaoVagaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'gestao_vagas';
	protected $primaryKey           = 'codigo';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = GestaoVaga::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'data_abertura',
        'status',
        'id_requisicao_pessoal',
        'id_cargo',
        'id_funcao',
        'cargo_funcao_alternativo',
        'formacao_minima',
        'formacao_especifica_minima',
        'perfil_profissional_desejado',
        'quantidade',
        'estado_vaga',
        'cidade_vaga',
        'bairro_vaga',
        'tipo_vinculo',
        'remuneracao',
        'beneficios',
        'horario_trabalho',
        'contato_selecionador',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'                    => 'required|is_natural_no_zero|max_length[11]',
        'data_abertura'                 => 'required|valid_date',
        'status'                        => 'required|integer|exact_length[1]',
        'id_requisicao_pessoal'         => 'required|is_natural_no_zero|max_length[11]',
        'id_cargo'                      => 'is_natural_no_zero|max_length[11]',
        'id_funcao'                     => 'is_natural_no_zero|max_length[11]',
        'cargo_funcao_alternativo'      => 'string|max_length[255]',
        'formacao_minima'               => 'integer|max_length[2]',
        'formacao_especifica_minima'    => 'string',
        'perfil_profissional_desejado'  => 'string',
        'quantidade'                    => 'required|integer|max_length[11]',
        'estado_vaga'                   => 'string|max_length[2]',
        'cidade_vaga'                   => 'string|max_length[100]',
        'bairro_vaga'                   => 'string|max_length[255]',
        'tipo_vinculo'                  => 'required|integer|exact_length[1]',
        'remuneracao'                   => 'required|numeric|max_length[10]',
        'beneficios'                    => 'string',
        'horario_trabalho'              => 'string',
        'contato_selecionador'          => 'string',
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
