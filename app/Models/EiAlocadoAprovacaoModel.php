<?php

namespace App\Models;

use App\Entities\EiAlocadoAprovacao;

class EiAlocadoAprovacaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_alocados_aprovacoes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiAlocadoAprovacao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_alocado',
        'cargo',
        'funcao',
        'mes_referencia',
        'data_hora_envio_solicitacao',
        'data_hora_aprovacao_escola',
        'nome_aprovador_escola',
        'status_aprovacao_escola',
        'observacoes_escola',
        'data_hora_aprovacao_cps',
        'nome_aprovador_cps',
        'status_aprovacao_cps',
        'observacoes_cps',
        'tipo_arquivo',
        'assinatura_digital',
        'arquivo_medicao',
        'id_aprovacao_coordenador',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_alocado'                    => 'required|is_natural_no_zero|max_length[11]',
        'cargo'                         => 'string|max_length[255]',
        'funcao'                        => 'string|max_length[255]',
        'mes_referencia'                => 'required|integer|max_length[2]',
        'data_hora_envio_solicitacao'   => 'valid_date',
        'data_hora_aprovacao_escola'    => 'valid_date',
        'nome_aprovador_escola'         => 'string|max_length[255]',
        'status_aprovacao_escola'       => 'integer|exact_length[1]',
        'observacoes_escola'            => 'string',
        'data_hora_aprovacao_cps'       => 'valid_date',
        'nome_aprovador_cps'            => 'string|max_length[255]',
        'status_aprovacao_cps'          => 'integer|exact_length[1]',
        'observacoes_cps'               => 'string',
        'tipo_arquivo'                  => 'string|max_length[1]',
        'assinatura_digital'            => 'string|max_length[255]',
        'arquivo_medicao'               => 'string|max_length[255]',
        'id_aprovacao_coordenador'      => 'is_natural_no_zero|max_length[11]',
    ];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = ['renomearArquivo'];
	protected $afterInsert          = [];
	protected $beforeUpdate         = ['renomearArquivo', 'validarAprovacao'];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];

    //--------------------------------------------------------------------

    protected $uploadConfig = [
        'assinatura_digital' => ['upload_path' => './arquivos/ei/assinatura_digital/', 'allowed_types' => 'gif|jpg|jpeg|png'],
        'arquivo_medicao' => ['upload_path' => './arquivos/ei/pdf/', 'allowed_types' => 'pdf'],
    ];

    public const STATUS = [
        '1' => 'Fechando medição',
        '2' => 'Validar medição',
        '3' => 'Ajustar medição',
        '4' => 'Medição validada',
    ];
    public const STATUS_APROVACAO = [
        '1' => 'Validar aprovação',
        '2' => 'Aprovação validada',
    ];

    //--------------------------------------------------------------------

    protected function renomearArquivo($data)
    {
        if (empty($data['data'])) {
            return $data;
        }

        $alocacao = $this->db
            ->select('c.ano, b.id_escola, a.id_cuidador')
            ->join('ei_alocacoes_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacoes c', 'c.id = b.id_alocacao')
            ->where('a.id', $data['data']['id_alocado'])
            ->get('ei_alocados a')
            ->row();

        if (empty($alocacao)) {
            return $data;
        }

        $nomeArquivo = "{$data['data']['mes_referencia']}-$alocacao->ano-$alocacao->id_escola-$alocacao->id_cuidador";
        if (!empty($_FILES['assinatura_digital'])) {
            $assinaturaDigital = explode('.', $_FILES['assinatura_digital']['name']);
            $this->uploadConfig['assinatura_digital']['file_name'] = $nomeArquivo . $assinaturaDigital[0];
        }
        if (!empty($_FILES['arquivo_medicao'])) {
            $arquivoMedicao = explode('.', $_FILES['arquivo_medicao']['name']);
            $this->uploadConfig['arquivo_medicao']['file_name'] = $nomeArquivo . $arquivoMedicao[0];
        }

        return $data;
    }

    //--------------------------------------------------------------------

    protected function validarAprovacao($data): array
    {
        if (empty($data['data'])) {
            return $data;
        }

        if (($data['data']['status_aprovacao_escola'] ?? null) == '3') {
            $data['data']['data_hora_aprovacao_escola'] = null;
            $data['data']['nome_aprovador_escola'] = null;
        }
        if (($data['data']['status_aprovacao_cps'] ?? null) == '1') {
            $data['data']['data_hora_aprovacao_cps'] = null;
            $data['data']['nome_aprovador_cps'] = null;
        }

        return $data;
    }
}
