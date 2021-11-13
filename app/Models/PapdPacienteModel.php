<?php

namespace App\Models;

use App\Entities\PapdPaciente;

class PapdPacienteModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'papd_pacientes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = PapdPaciente::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'nome',
        'cpf',
        'data_nascimento',
        'sexo',
        'id_deficiencia',
        'cadastro_municipal',
        'id_hipotese_diagnostica',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'cidade_nome',
        'estado',
        'cep',
        'nome_responsavel_1',
        'telefone_fixo_1',
        'nome_responsavel_2',
        'telefone_fixo_2',
        'telefone_celular_2',
        'data_ingresso',
        'data_inativo',
        'data_fila_espera',
        'data_afastamento',
        'contratante',
        'contrato',
        'id_instituicao',
        'status',
        'telefone_celular_1',
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
        'cpf'                       => 'string|max_length[14]',
        'data_nascimento'           => 'required|valid_date',
        'sexo'                      => 'required|in_list[]',
        'id_deficiencia'            => 'is_natural_no_zero|max_length[11]',
        'cadastro_municipal'        => 'string|max_length[30]',
        'id_hipotese_diagnostica'   => 'is_natural_no_zero|max_length[11]',
        'logradouro'                => 'string|max_length[255]',
        'numero'                    => 'integer|max_length[11]',
        'complemento'               => 'string|max_length[255]',
        'bairro'                    => 'string|max_length[50]',
        'cidade'                    => 'is_natural_no_zero|max_length[11]',
        'cidade_nome'               => 'string|max_length[255]',
        'estado'                    => 'is_natural_no_zero|max_length[2]',
        'cep'                       => 'string|max_length[9]',
        'nome_responsavel_1'        => 'string|max_length[255]',
        'telefone_fixo_1'           => 'string|max_length[255]',
        'nome_responsavel_2'        => 'string|max_length[255]',
        'telefone_fixo_2'           => 'string|max_length[255]',
        'telefone_celular_2'        => 'string|max_length[255]',
        'data_ingresso'             => 'required|valid_date',
        'data_inativo'              => 'valid_date',
        'data_fila_espera'          => 'valid_date',
        'data_afastamento'          => 'valid_date',
        'contratante'               => 'string|max_length[255]',
        'contrato'                  => 'string|max_length[255]',
        'id_instituicao'            => 'required|is_natural_no_zero|max_length[11]',
        'status'                    => 'required|string|max_length[1]',
        'telefone_celular_1'        => 'string|max_length[255]',
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

    public const STATUS = [
        'A' => 'Ativo',
        'I' => 'Inativo',
        'M' => 'Em monitoramento',
        'X' => 'Afastado',
        'E' => 'Em fila de espera',
    ];
}
