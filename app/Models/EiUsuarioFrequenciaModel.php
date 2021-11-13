<?php

namespace App\Models;

use App\Entities\EiUsuariosFrequencia;

class EiUsuarioFrequenciaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_usuarios_frequencias';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiUsuariosFrequencia::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_usuario',
        'data_evento',
        'periodo_atual',
        'horario_entrada_1',
        'horario_entrada_real_1',
        'horario_saida_1',
        'horario_saida_real_1',
        'horario_entrada_2',
        'horario_entrada_real_2',
        'horario_saida_2',
        'horario_saida_real_2',
        'horario_entrada_3',
        'horario_entrada_real_3',
        'horario_saida_3',
        'horario_saida_real_3',
        'observacoes',
        'justificativa',
        'avaliacao_justificativa',
        'status_justificativa',
        'id_escola',
        'alunos',
        'status_entrada_1',
        'status_entrada_2',
        'status_entrada_3',
        'status_saida_1',
        'status_saida_2',
        'status_saida_3',
        'automatico_entrada_1',
        'automatico_saida_1',
        'automatico_entrada_2',
        'automatico_saida_2',
        'automatico_entrada_3',
        'automatico_saida_3',
        'criado_em',
        'atualizado_em',
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
        'data_evento'               => 'required|valid_date',
        'periodo_atual'             => 'required|integer|exact_length[1]',
        'horario_entrada_1'         => 'valid_time',
        'horario_entrada_real_1'    => 'valid_date',
        'horario_saida_1'           => 'valid_time',
        'horario_saida_real_1'      => 'valid_date',
        'horario_entrada_2'         => 'valid_time',
        'horario_entrada_real_2'    => 'valid_date',
        'horario_saida_2'           => 'valid_time',
        'horario_saida_real_2'      => 'valid_date',
        'horario_entrada_3'         => 'valid_time',
        'horario_entrada_real_3'    => 'valid_date',
        'horario_saida_3'           => 'valid_time',
        'horario_saida_real_3'      => 'valid_date',
        'observacoes'               => 'string',
        'justificativa'             => 'string',
        'avaliacao_justificativa'   => 'string|max_length[255]',
        'status_justificativa'      => 'integer|exact_length[1]',
        'id_escola'                 => 'is_natural_no_zero|max_length[11]',
        'alunos'                    => 'integer|max_length[11]',
        'status_entrada_1'          => 'string|max_length[2]',
        'status_entrada_2'          => 'string|max_length[2]',
        'status_entrada_3'          => 'string|max_length[2]',
        'status_saida_1'            => 'string|max_length[2]',
        'status_saida_2'            => 'string|max_length[2]',
        'status_saida_3'            => 'string|max_length[2]',
        'automatico_entrada_1'      => 'integer|exact_length[1]',
        'automatico_saida_1'        => 'integer|exact_length[1]',
        'automatico_entrada_2'      => 'integer|exact_length[1]',
        'automatico_saida_2'        => 'integer|exact_length[1]',
        'automatico_entrada_3'      => 'integer|exact_length[1]',
        'automatico_saida_3'        => 'integer|exact_length[1]',
        'criado_em'                 => 'valid_date',
        'atualizado_em'             => 'valid_date',
    ];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = [];
	protected $afterInsert          = ['inserirApontamento'];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = ['atualizarApontamento'];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = ['excluirApontamento'];
	protected $afterDelete          = [];

    //--------------------------------------------------------------------

    public const STATUS = [
        'FT' => 'Falta',
        'SA' => 'Saída antecipada',
        'AT' => 'Atraso',
        'PV' => 'Posto vago',
        'PN' => 'Presença normal',
        'FR' => 'Feriado',
        'EF' => 'Emenda de feriado',
        'RE' => 'Recesso',
        'EE' => 'Evento extra',
        'HE' => 'Evento de estudo',
        'SL' => 'Sábado letivo',
        'SB' => 'Sábado',
        'DG' => 'Domingo',
    ];

    //--------------------------------------------------------------------

    protected function inserirApontamento($data)
    {
        if (!empty($data['id']) == false) {
            return $data;
        }

        $medicoes = $this->find($data['id']);
        if (empty($medicoes)) {
            return $data;
        }

        if (!is_array($medicoes)) {
            $medicoes = [$medicoes];
        }

        foreach ($medicoes as $medicao) {
            for ($i = 1; $i <= 3; $i++) {
                $horarioEntrada = $data['data']['horario_entrada_real_' . $i];
                $horarioSaida = $data['data']['horario_saida_real_' . $i];

                if (strlen($horarioEntrada) == 0 and strlen($horarioSaida) == 0) {
                    continue;
                }

                $apontamento = $this->db
                    ->select('b.id AS id_alocado, b.id_cuidador, a.id')
                    ->join('ei_alocacoes_escolas c', 'c.id = b.id_alocacao_escola')
                    ->join('ei_apontamentos a', "a.id_alocado = b.id AND a.data = '$medicao->data_evento' AND a.periodo = '$i'", 'left', false)
                    ->where('c.id_escola', $medicao->id_escola)
                    ->where('b.id_cuidador', $medicao->id_usuario)
                    ->get('ei_alocados b')
                    ->row();

                if ($apontamento and empty($apontamento->id)) {
                    $dataApontamento = [
                        'id_alocado' => $apontamento->id_alocado,
                        'data' => $data['data']['data_evento'],
                        'periodo' => $i,
                        'status' => 'PN',
                        'id_usuario' => $apontamento->id_cuidador,
                        'horario_entrada_' . $i => $horarioEntrada,
                        'horario_saida_' . $i => $horarioSaida,
                        'criado_em' => date('Y-m-d H:i:s'),
                    ];

                    $this->db->insert('ei_apontamentos', $dataApontamento);
                }
            }
        }

        return $data;
    }

    //--------------------------------------------------------------------

    protected function atualizarApontamento($data)
    {
        if (!empty($data['id']) == false) {
            return $data;
        }

        $medicoes = $this->find($data['id']);
        if (empty($medicoes)) {
            return $data;
        }

        if (!is_array($medicoes)) {
            $medicoes = [$medicoes];
        }

        foreach ($medicoes as $medicao) {
            $apontamento = $this->db
                ->select('a.id')
                ->join('ei_alocados b', 'b.id = a.id_alocado')
                ->join('ei_alocacoes_escolas c', 'c.id = b.id_alocacao_escola')
                ->where('c.id_escola', $medicao->id_escola)
                ->where('b.id_cuidador', $medicao->id_usuario)
                ->where('a.data', $medicao->data_evento)
                ->get('ei_apontamentos a')
                ->row();

            $idApontamento = null;
            if ($apontamento) {
                if (!empty($data['data']['horario_entrada_real_1'])) {
                    $apontamento->horario_entrada_1 = $data['data']['horario_entrada_real_1'];
                }
                if (!empty($data['data']['horario_saida_real_1'])) {
                    $apontamento->horario_saida_1 = $data['data']['horario_saida_real_1'];
                }
                if (!empty($data['data']['horario_entrada_real_2'])) {
                    $apontamento->horario_entrada_2 = $data['data']['horario_entrada_real_2'];
                }
                if (!empty($data['data']['horario_saida_real_2'])) {
                    $apontamento->horario_saida_2 = $data['data']['horario_saida_real_2'];
                }
                if (!empty($data['data']['horario_entrada_real_3'])) {
                    $apontamento->horario_entrada_3 = $data['data']['horario_entrada_real_3'];
                }
                if (!empty($data['data']['horario_saida_real_3'])) {
                    $apontamento->horario_saida_3 = $data['data']['horario_saida_real_3'];
                }
                $idApontamento = $apontamento->id;
                unset($apontamento->id);
            }

            if (count((array)$apontamento) > 0) {
                $this->db
                    ->set($apontamento)
                    ->where('id', $idApontamento)
                    ->update('ei_apontamentos');
            }
        }

        return $data;
    }

    protected function excluirApontamento($data)
    {
        if (!empty($data['id']) == false) {
            return $data;
        }

        $medicoes = $this->find($data['id']);
        if (empty($medicoes)) {
            return $data;
        }

        if (!is_array($medicoes)) {
            $medicoes = [$medicoes];
        }

        foreach ($medicoes as $medicao) {
            $apontamento = $this->db
                ->select('a.id')
                ->join('ei_alocados b', 'b.id = a.id_alocado')
                ->join('ei_alocacoes_escolas c', 'c.id = b.id_alocacao_escola')
                ->where('c.id_escola', $medicao->id_escola)
                ->where('b.id_cuidador', $medicao->id_usuario)
                ->where('a.data', $medicao->data_evento)
                ->get('ei_apontamentos a')
                ->row();

            if ($apontamento) {
                $this->db->delete('ei_apontamentos', ['id' => $apontamento->id]);
            }
        }

        return $data;
    }
}
