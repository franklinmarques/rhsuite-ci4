<?php

namespace App\Models;

use App\Entities\Funcionario;
use CodeIgniter\Model;

class FuncionarioModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'funcionarios';
    protected $primaryKey           = 'id_usuario';
    protected $useAutoIncrement     = false;
    protected $insertID             = 0;
    protected $returnType           = Funcionario::class;
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = [
        'id_usuario',
        'nome',
        'data_nascimento',
        'sexo',
        'foto',
        'assinatura_digital',
        'nome_mae',
        'nome_pai',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'id_cidade',
        'id_estado',
        'cep',
        'telefones',
        'endereco_ip1',
        'endereco_ip2',
        'geolocalizacao_1',
        'geolocalizacao_2',
        'municipio_trabalho',
        'local_trabalho',
        'id_depto',
        'id_area',
        'id_setor',
        'id_cargo',
        'id_funcao',
        'tipo_vinculo',
        'rg',
        'cpf',
        'pis',
        'cnpj',
        'razao_social_cnpj',
        'nome_fantasia_cnpj',
        'codigo_pj',
        'matricula',
        'contrato',
        'centro_custo',
        'nome_banco',
        'agencia_bancaria',
        'conta_bancaria',
        'tipo_conta_bancaria',
        'operacao_conta_bancaria',
        'pessoa_conta_bancaria',
        'nome_cartao_vt',
        'valor_vt',
        'data_admissao',
        'data_demissao',
        'tipo_demissao',
        'observacoes_demissao',
        'nivel_acesso',
        'hash_acesso',
        'observacoes_historico',
        'status',
        'pagina_inicial',
        'recolher_menu',
        'possui_apontamento_horas',
        'faz_somente_apontamento',
        'banco_horas_icom',
        'banco_horas_icom_2',
    ];

    // Dates
    protected $useTimestamps        = false;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'id_usuario'                => 'required|is_natural_no_zero|max_length[11]',
        'nome'                      => 'required|string|max_length[255]',
        'data_nascimento'           => 'valid_date',
        'sexo'                      => 'in_list[M,F]',
        'foto'                      => 'string|max_length[255]',
        'assinatura_digital'        => 'string',
        'nome_mae'                  => 'string|max_length[255]',
        'nome_pai'                  => 'string|max_length[255]',
        'endereco'                  => 'string|max_length[255]',
        'numero'                    => 'integer|max_length[11]',
        'complemento'               => 'string|max_length[255]',
        'bairro'                    => 'string|max_length[255]',
        'id_cidade'                 => 'integer|max_length[11]',
        'id_estado'                 => 'integer|max_length[11]',
        'cep'                       => 'string|max_length[9]',
        'telefones'                 => 'string|max_length[255]',
        'endereco_ip1'              => 'string|max_length[255]',
        'endereco_ip2'              => 'string|max_length[255]',
        'geolocalizacao_1'          => 'string|max_length[30]',
        'geolocalizacao_2'          => 'string|max_length[30]',
        'municipio_trabalho'        => 'string|max_length[255]',
        'local_trabalho'            => 'string|max_length[255]',
        'id_depto'                  => 'is_natural_no_zero|max_length[11]',
        'id_area'                   => 'is_natural_no_zero|max_length[11]',
        'id_setor'                  => 'is_natural_no_zero|max_length[11]',
        'id_cargo'                  => 'is_natural_no_zero|max_length[11]',
        'id_funcao'                 => 'is_natural_no_zero|max_length[11]',
        'tipo_vinculo'              => 'integer|max_length[1]',
        'rg'                        => 'string|max_length[13]',
        'cpf'                       => 'string|max_length[14]',
        'pis'                       => 'string|max_length[14]',
        'cnpj'                      => 'string|max_length[18]',
        'razao_social_cnpj'         => 'string|max_length[255]',
        'nome_fantasia_cnpj'        => 'string|max_length[255]',
        'codigo_pj'                 => 'string|max_length[10]',
        'matricula'                 => 'string|max_length[255]',
        'contrato'                  => 'string|max_length[255]',
        'centro_custo'              => 'string|max_length[255]',
        'nome_banco'                => 'string|max_length[255]',
        'agencia_bancaria'          => 'string|max_length[255]',
        'conta_bancaria'            => 'string|max_length[255]',
        'tipo_conta_bancaria'       => 'string|max_length[1]',
        'operacao_conta_bancaria'   => 'string|max_length[200]',
        'pessoa_conta_bancaria'     => 'string|max_length[1]',
        'nome_cartao_vt'            => 'string|max_length[200]',
        'valor_vt'                  => 'string|max_length[200]',
        'data_admissao'             => 'valid_date',
        'data_demissao'             => 'valid_date',
        'tipo_demissao'             => 'integer|max_length[11]',
        'observacoes_demissao'      => 'string',
        'nivel_acesso'              => 'required|integer|max_length[11]',
        'hash_acesso'               => 'string',
        'observacoes_historico'     => 'string',
        'status'                    => 'integer|max_length[2]',
        'pagina_inicial'            => 'string|max_length[255]',
        'recolher_menu'             => 'integer|exact_length[1]',
        'possui_apontamento_horas'  => 'integer|exact_length[1]',
        'faz_somente_apontamento'   => 'integer|exact_length[1]',
        'banco_horas_icom'          => 'string|max_length[10]',
        'banco_horas_icom_2'        => 'string|max_length[10]',
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

    protected $uploadConfig = [
        'foto' => ['upload_path' => './imagens/usuarios/', 'allowed_types' => 'gif|jpg|png'],
        'assinatura_digital' => ['upload_path' => './imagens/usuarios/', 'allowed_types' => 'gif|jpg|png']
    ];

    public const TIPOS = [
        'administrador' => 'administrador',
        'empresa' => 'empresa',
        'funcionario' => 'funcionario',
        'selecionador' => 'selecionador',
    ];
    public const SEXOS = [
        'M' => 'Masculino',
        'F' => 'Feminino',
    ];
    public const TIPOS_TELA_INICIAL = [
        '1' => 'Imagem padrão',
        '2' => 'Vídeo padrão',
        '3' => 'Imagem personalizada',
        '4' => 'Vídeo personalizado',
    ];
    public const TIPOS_CONTA_BANCARIA = [
        'C' => 'Corrente',
        'P' => 'Poupança',
    ];
    public const TIPOS_PESSOA_CONTA_BANCARIA = [
        'F' => 'Pessoa física',
        'J' => 'Pessoa jurídica',
    ];
    public const TIPOS_VINCULO = [
        '1' => 'CLT',
        '2' => 'MEI',
        '7' => 'MEI - Esporádico',
        '3' => 'PJ',
        '4' => 'Autônomo',
        '5' => 'ME',
        '6' => 'LTDA',
        '8' => 'Temporário',
        '9' => 'Cliente',
    ];
    public const TIPOS_DEMISSAO = [
        '1' => 'Demissão sem justa causa',
        '2' => 'Demissão por justa causa',
        '3' => 'Pedido de demissão',
        '4' => 'Término do contrato',
        '5' => 'Rescisão antecipada pelo empregado',
        '6' => 'Rescisão antecipada pelo empregador',
        '7' => 'Desistiu da vaga',
        '8' => 'Rescisão estagiário',
        '9' => 'Rescisão por acordo',
        '10' => 'Distrato temporário',
        '11' => 'Distrato',
        '12' => 'Falecimento',
    ];
    public const NIVEIS_ACESSO = [
        NIVEL_ACESSO_ADMINISTRADOR => 'Administrador',
        NIVEL_ACESSO_PRESIDENTE => 'Presidente',
        NIVEL_ACESSO_DIRETOR => 'Diretor',
        NIVEL_ACESSO_GERENTE => 'Gerente',
        NIVEL_ACESSO_COORDENADOR => 'Coordenador',
        NIVEL_ACESSO_REPRESENTANTE => 'Representante',
        NIVEL_ACESSO_SUPERVISOR => 'Supervisor',
        NIVEL_ACESSO_SUPERVISOR_REQUISITANTE => 'Supervisor requisitante',
        NIVEL_ACESSO_ENCARREGADO => 'Encarregado',
        NIVEL_ACESSO_LIDER => 'Líder',
        NIVEL_ACESSO_COLABORADOR_CLT => 'Colaborador CLT',
        NIVEL_ACESSO_COLABORADOR_MEI => 'Colaborador MEI',
        NIVEL_ACESSO_COLABORADOR_ME => 'Colaborador ME',
        NIVEL_ACESSO_COLABORADOR_PJ => 'Colaborador PJ',
        NIVEL_ACESSO_COLABORADOR_LTDA => 'Colaborador LTDA',
        NIVEL_ACESSO_CUIDADOR_COMUNITARIO => 'Cuidador Comunitário',
        NIVEL_ACESSO_GESTOR => 'Gestor',
        NIVEL_ACESSO_MULTIPLICADOR => 'Multiplicador',
        NIVEL_ACESSO_SELECIONADOR => 'Selecionador',
        NIVEL_ACESSO_CLIENTE_NIVEL_0 => 'Cliente Nível 0',
        NIVEL_ACESSO_CLIENTE_NIVEL_1 => 'Cliente Nível 1',
        NIVEL_ACESSO_CLIENTE_NIVEL_2 => 'Cliente Nível 2',
        NIVEL_ACESSO_VISTORIADOR => 'Vistoriador',
        NIVEL_ACESSO_AUTONOMO => 'Autônomo',
        NIVEL_ACESSO_FORNECEDOR => 'Fornecedor',
    ];
    public const STATUS = [
        USUARIO_ATIVO => 'Ativo',
        USUARIO_ATIVAR => 'Ativar',
        USUARIO_INATIVO => 'Inativo',
        USUARIO_EM_EXPERIENCIA => 'Em experiência',
        USUARIO_EM_DESLIGAMENTO => 'Em desligamento',
        USUARIO_DESLIGADO => 'Desligado',
        USUARIO_AFASTADO_MATERNIDADE => 'Afastado (maternidade)',
        USUARIO_AFASTADO_APOSENTADORIA => 'Afastado (aposentadoria/invalidez)',
        USUARIO_AFASTADO_INSS => 'Afastado (auxílio doença - INSS)',
        USUARIO_AFASTADO_ATESTADO => 'Afastado (auxílio doença - atestado)',
        USUARIO_AFASTADO_ACIDENTE => 'Afastado (acidente)',
        USUARIO_DESISTIU_VAGA => 'Desistiu da vaga',
        USUARIO_DISTRATO_TEMPORARIO => 'Distrato temporário',
        USUARIO_DISTRATO => 'Distrato',
    ];

    public function insert($data = null, bool $returnID = true)
    {
        $this->setValidationRule('id_usuario', 'required|is_natural_no_zero|max_length[11]|is_unique[empresa.id_usuario,id_usuario,{id_usuario}]');
        return parent::insert($data, $returnID); // TODO: Change the autogenerated stub
    }

    public function insertBatch(?array $set = null, ?bool $escape = null, int $batchSize = 100, bool $testing = false)
    {
        $this->setValidationRule('id_usuario', 'required|is_natural_no_zero|max_length[11]|is_unique[empresa.id_usuario,id_usuario,{id_usuario}]');
        return parent::insertBatch($set, $escape, $batchSize, $testing); // TODO: Change the autogenerated stub
    }
}
