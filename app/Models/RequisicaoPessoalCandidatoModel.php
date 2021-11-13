<?php

namespace App\Models;

use App\Entities\RequisicaoPessoalCandidato;

class RequisicaoPessoalCandidatoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'requisicoes_pessoal_candidatos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = RequisicaoPessoalCandidato::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_requisicao',
        'id_usuario',
        'id_usuario_banco',
        'data_inscricao',
        'status',
        'data_selecao',
        'resultado_selecao',
        'data_requisitante',
        'resultado_requisitante',
        'antecedentes_criminais',
        'restricoes_financeiras',
        'data_exame_admissional',
        'endereco_exame_admissional',
        'resultado_exame_admissional',
        'aprovado',
        'aprovado_indicacao',
        'data_admissao',
        'observacoes',
        'desempenho_perfil',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_requisicao'                 => 'required|is_natural_no_zero|max_length[11]',
        'id_usuario'                    => 'is_natural_no_zero|max_length[11]',
        'id_usuario_banco'              => 'is_natural_no_zero|max_length[11]',
        'data_inscricao'                => 'valid_date',
        'status'                        => 'string|max_length[1]',
        'data_selecao'                  => 'valid_date',
        'resultado_selecao'             => 'string|max_length[1]',
        'data_requisitante'             => 'valid_date',
        'resultado_requisitante'        => 'string|max_length[1]',
        'antecedentes_criminais'        => 'integer|exact_length[1]',
        'restricoes_financeiras'        => 'integer|exact_length[1]',
        'data_exame_admissional'        => 'valid_date',
        'endereco_exame_admissional'    => 'string',
        'resultado_exame_admissional'   => 'integer|exact_length[1]',
        'aprovado'                      => 'integer|exact_length[1]',
        'aprovado_indicacao'            => 'integer|exact_length[1]',
        'data_admissao'                 => 'valid_date',
        'observacoes'                   => 'string',
        'desempenho_perfil'             => 'string|max_length[1]',
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
