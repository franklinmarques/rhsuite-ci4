<?php

namespace App\Models;

use App\Entities\RecrutamentoUsuario;

class RecrutamentoUsuarioModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'recrutamento_usuarios';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = RecrutamentoUsuario::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'nome',
        'data_nascimento',
        'sexo',
        'estado_civil',
        'nome_mae',
        'nome_pai',
        'cpf',
        'rg',
        'rg_orgao_emissor',
        'rg_data_emissao',
        'pis',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'escolaridade',
        'deficiencia',
        'foto',
        'telefone',
        'email',
        'senha',
        'token',
        'data_inscricao',
        'fonte_contratacao',
        'resumo_cv',
        'profissao_cargo_funcao_1',
        'profissao_cargo_funcao_2',
        'data_edicao',
        'nivel_acesso',
        'observacoes',
        'arquivo_curriculo',
        'arquivo_laudo_medico',
        'status',
        'status_aceite',
        'data_hora_aceite',
        'spa',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'                => 'required|is_natural_no_zero|max_length[11]',
        'nome'                      => 'required|string|max_length[255]',
        'data_nascimento'           => 'valid_date',
        'sexo'                      => 'in_list[]',
        'estado_civil'              => 'integer|max_length[11]',
        'nome_mae'                  => 'string|max_length[255]',
        'nome_pai'                  => 'string|max_length[255]',
        'cpf'                       => 'string|is_unique[recrutamento_usuarios.cpf,id,{id}]|max_length[14]',
        'rg'                        => 'string|max_length[13]',
        'rg_orgao_emissor'          => 'string|max_length[30]',
        'rg_data_emissao'           => 'valid_date',
        'pis'                       => 'string|max_length[14]',
        'logradouro'                => 'string|max_length[255]',
        'numero'                    => 'integer|max_length[11]',
        'complemento'               => 'string|max_length[255]',
        'bairro'                    => 'string|max_length[50]',
        'cidade'                    => 'is_natural_no_zero|max_length[11]',
        'estado'                    => 'is_natural_no_zero|max_length[2]',
        'cep'                       => 'string|max_length[9]',
        'escolaridade'              => 'is_natural_no_zero|max_length[11]',
        'deficiencia'               => 'is_natural_no_zero|max_length[11]',
        'foto'                      => 'string|max_length[255]',
        'telefone'                  => 'required|string|max_length[255]',
        'email'                     => 'string|is_unique[recrutamento_usuarios.email,id,{id}]|max_length[255]',
        'senha'                     => 'string|max_length[32]',
        'token'                     => 'required|string|max_length[255]',
        'data_inscricao'            => 'valid_date',
        'fonte_contratacao'         => 'string|max_length[30]',
        'resumo_cv'                 => 'string',
        'profissao_cargo_funcao_1'  => 'string|max_length[255]',
        'profissao_cargo_funcao_2'  => 'string|max_length[255]',
        'data_edicao'               => 'valid_date',
        'nivel_acesso'              => 'required|string|max_length[1]',
        'observacoes'               => 'string',
        'arquivo_curriculo'         => 'string|max_length[255]',
        'arquivo_laudo_medico'      => 'string|max_length[255]',
        'status'                    => 'required|string|max_length[1]',
        'status_aceite'             => 'integer|exact_length[1]',
        'data_hora_aceite'          => 'valid_date',
        'spa'                       => 'integer|exact_length[1]',
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

    public const SEXOS = [
        'M' => 'Masculino',
        'F' => 'Feminino',
    ];
    public const ESTADOS_CIVIS = [
        '1' => 'Solteiro(a)',
        '2' => 'Casado(a)',
        '3' => 'Desquitado(a)',
        '4' => 'Divorciado(a)',
        '5' => 'Viúvo(a)',
        '6' => 'Outro',
    ];
    public const STATUS = [
        'A' => 'Ativo',
        'E' => 'Excluído',
        'F' => 'Formação acadêmica pendente',
        'H' => 'Histórico profissional pendente',
        'O' => 'Objetivo profissional pendente',
        'D' => 'Documentação pendente',
    ];
    public const NIVEIS_ACESSO = [
        'C' => 'Candidato interno',
        'E' => 'Candidato externo',
    ];
}
