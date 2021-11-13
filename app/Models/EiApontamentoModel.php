<?php

namespace App\Models;

use App\Entities\EiApontamento;

class EiApontamentoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_apontamentos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiApontamento::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_alocado',
        'data',
        'id_horario',
        'periodo',
        'horario_inicio',
        'status',
        'id_usuario',
        'id_alocado_sub1',
        'id_alocado_sub2',
        'horario_entrada_1',
        'horario_saida_1',
        'substituto_horario_1',
        'horario_entrada_2',
        'horario_saida_2',
        'substituto_horario_2',
        'horario_entrada_3',
        'horario_saida_3',
        'substituto_horario_3',
        'desconto',
        'desconto_1',
        'desconto_2',
        'desconto_3',
        'desconto_sub1',
        'desconto_sub2',
        'observacoes',
        'ocorrencia_cuidador_aluno',
        'ocorrencia_professor',
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
        'id_alocado'                => 'required|is_natural_no_zero|max_length[11]',
        'data'                      => 'required|valid_date',
        'id_horario'                => 'is_natural_no_zero|max_length[11]',
        'periodo'                   => 'integer|exact_length[1]',
        'horario_inicio'            => 'valid_time',
        'status'                    => 'required|string|max_length[2]',
        'id_usuario'                => 'integer|max_length[11]',
        'id_alocado_sub1'           => 'is_natural_no_zero|max_length[11]',
        'id_alocado_sub2'           => 'is_natural_no_zero|max_length[11]',
        'horario_entrada_1'         => 'valid_date',
        'horario_saida_1'           => 'valid_date',
        'substituto_horario_1'      => 'integer|exact_length[4]',
        'horario_entrada_2'         => 'valid_date',
        'horario_saida_2'           => 'valid_date',
        'substituto_horario_2'      => 'integer|exact_length[4]',
        'horario_entrada_3'         => 'valid_date',
        'horario_saida_3'           => 'valid_date',
        'substituto_horario_3'      => 'integer|exact_length[4]',
        'desconto'                  => 'valid_time',
        'desconto_1'                => 'valid_time',
        'desconto_2'                => 'valid_time',
        'desconto_3'                => 'valid_time',
        'desconto_sub1'             => 'valid_time',
        'desconto_sub2'             => 'valid_time',
        'observacoes'               => 'string',
        'ocorrencia_cuidador_aluno' => 'string',
        'ocorrencia_professor'      => 'string',
        'criado_em'                 => 'valid_date',
        'atualizado_em'             => 'valid_date',
    ];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = ['setIdHorario'];
	protected $afterInsert          = ['setUsuarioFrequencia'];
	protected $beforeUpdate         = ['setIdHorario'];
	protected $afterUpdate          = ['setUsuarioFrequencia'];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = ['setUsuarioFrequencia'];
	protected $afterDelete          = [];

    //--------------------------------------------------------------------

    public const STATUS = [
        'FA' => 'Falta',
        'PV' => 'Posto vago',
        'AT' => 'Atraso',
        'SA' => 'Saída antecipada',
        'FE' => 'Feriado',
        'EM' => 'Emenda de feriado',
        'RE' => 'Recesso',
        'AF' => 'Aluno ausente',
        'EE' => 'Evento extra',
        'HE' => 'Horas de estudo',
        'SL' => 'Sábado letivo',
        'PN' => 'Presença normal',
        'SB' => 'Sábado',
        'DG' => 'Domingo',
    ];
    public const STATUS_NEGATIVOS = [
        'FA' => 'Falta',
        'PV' => 'Posto vago',
        'AT' => 'Atraso',
        'SA' => 'Saída antecipada',
    ];
    public const PERIODOS = [
        '0' => 'Madrugada',
        '1' => 'Manhã',
        '2' => 'Tarde',
        '3' => 'Noite',
    ];

    //--------------------------------------------------------------------

    protected function setIdHorario($data): array
    {
        if (!empty($data['data'])) {
            return $data;
        }

        $alocacao = $this->db
            ->select('c.*')
            ->join('ei_alocacoes_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacoes c', 'c.id = b.id_alocacao')
            ->where('a.id', $data['data']['id_alocado'])
            ->get('ei_alocados a')
            ->row();

        $semestre = $alocacao->semestre;
        $idMes = (int)date('n', strtotime($data['data']['data'])) - ($semestre > 1 ? 6 : 0);

        $horario = $this->db
            ->where('id_alocado', $data['data']['id_alocado'])
            ->where('dia_semana', date('w', strtotime($data['data']['data'])))
            ->where('periodo', $data['data']['periodo'])
            ->group_start()
            ->where('horario_inicio_mes' . $idMes, $data['data']['horario_entrada_1'])
            ->or_where('horario_inicio_mes' . $idMes, $data['data']['horario_entrada_2'])
            ->or_where('horario_inicio_mes' . $idMes, $data['data']['horario_entrada_3'])
            ->or_where('horario_termino_mes' . $idMes, $data['data']['horario_saida_1'])
            ->or_where('horario_termino_mes' . $idMes, $data['data']['horario_saida_2'])
            ->or_where('horario_termino_mes' . $idMes, $data['data']['horario_saida_3'])
            ->group_end()
            ->get('ei_alocados_horarios')
            ->row();

        $data['data']['id_horario'] = $horario->id ?? null;
        if ($horario) {
            $data['data']['periodo'] = $horario->periodo;
        } elseif (!empty($data['data']['horario_entrada_3'])) {
            $data['data']['periodo'] = 3;
        } elseif (!empty($data['data']['horario_entrada_2'])) {
            $data['data']['periodo'] = 2;
        } elseif (!empty($data['data']['horario_entrada_1'])) {
            $data['data']['periodo'] = 1;
        }

        return $data;
    }

    //--------------------------------------------------------------------

    protected function setUsuarioFrequencia($data)
    {
        if ($this->input->post('nao_salvar_medicao')) {
            return $data;
        }

        $id = $data['id'] ?? $this->insertID;
        if (!is_array($id)) {
            $id = [$id];
        }

        $apontamento = $this->db
            ->where_in('id', $id)
            ->get('ei_apontamentos')
            ->row();

        $alocado = $this->db
            ->select('a.*, b.id_escola, c.semestre', false)
            ->join('ei_alocacoes_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacoes c', 'c.id = b.id_alocacao')
            ->where('a.id', $apontamento->id_alocado)
            ->get('ei_alocados a')
            ->row();

        $periodo = $apontamento->periodo ?? $this->input->post('periodo');
        $periodoExistente = $apontamento->periodo ?? null;
        $status = !empty($data['data']) ? ['FA' => 'FT', 'PV' => 'PV', 'FE' => 'FR', 'EM' => 'EF', 'RE' => 'RE', 'EE' => 'EE', 'HE' => 'HE', 'SL' => 'SL'] : [];

        $usuarioFrequencias = $this->db
            ->select("*, horario_entrada_$periodo AS horario_entrada", false)
            ->where('id_usuario', $alocado->id_cuidador)
            ->where('data_evento', $apontamento->data)
            ->where('id_escola', $alocado->id_escola)
            ->group_start()
            ->where('(horario_entrada_1 IS NOT NULL AND horario_saida_1 IS NULL OR periodo_atual = 1)')
            ->or_where('(horario_entrada_2 IS NOT NULL AND horario_saida_2 IS NULL OR periodo_atual = 2)')
            ->or_where('(horario_entrada_3 IS NOT NULL AND horario_saida_3 IS NULL OR periodo_atual = 3)')
            ->group_end()
            ->order_by('horario_entrada_real_3', 'desc')
            ->order_by('horario_entrada_real_2', 'desc')
            ->order_by('horario_entrada_real_1', 'desc')
            ->get('ei_usuarios_frequencias')
            ->result();

        $usuarioFrequencia = $usuarioFrequencias[0] ?? null;
        foreach ($usuarioFrequencias as $row) {
            if (is_null($row->horario_entrada)) {
                $usuarioFrequencia = $row;
            }
        }

        if (isset($data['purge'])) {
            $frequenciaAtual = $this->db
                ->where("horario_entrada_real_$periodo IS NOT NULL")
                ->where("horario_saida_real_$periodo IS NOT NULL")
                ->where('id_usuario', $alocado->id_cuidador)
                ->where('data_evento', $apontamento->data)
                ->where('id_escola', $alocado->id_escola)
                ->get('ei_usuarios_frequencias')
                ->row();

            if (!empty($frequenciaAtual->id)) {
                $qb = $this->db
                    ->where('id', $frequenciaAtual->id);
                switch ($periodo) {
                    case '0':
                        $qb->where('horario_entrada_real_1 IS NULL AND horario_saida_real_1 IS NULL')
                            ->or_where('horario_entrada_real_2 IS NULL AND horario_saida_real_2 IS NULL')
                            ->or_where('horario_entrada_real_3 IS NULL AND horario_saida_real_3 IS NULL');
                        break;
                    case '1':
                        $qb->where('horario_entrada_real_2 IS NULL AND horario_saida_real_2 IS NULL')
                            ->where('horario_entrada_real_3 IS NULL AND horario_saida_real_3 IS NULL');
                        break;
                    case '2':
                        $qb->where('horario_entrada_real_1 IS NULL AND horario_saida_real_1 IS NULL')
                            ->where('horario_entrada_real_3 IS NULL AND horario_saida_real_3 IS NULL');
                        break;
                    case '3':
                        $qb->where('horario_entrada_real_1 IS NULL AND horario_saida_real_1 IS NULL')
                            ->where('horario_entrada_real_2 IS NULL AND horario_saida_real_2 IS NULL');
                        break;
                }
                $qb->delete('ei_usuarios_frequencias');
            }

            return $data;
        }

        $mes = date('m', strtotime($apontamento->data));
        $idMes = intval($mes) - (intval($alocado->semestre) > 1 ? 6 : 0);
        $diaSemana = date('w', strtotime($apontamento->data));

        $dataApontamento = $this->db
            ->select('a.id AS id_alocado_evento, g.id AS id_horario, j.*, f.minutos_tolerancia_entrada_saida', false)
            ->select("IF(j.status IN ('EE', 'HE', 'SL'), IFNULL(j.horario_entrada_$periodo, g.horario_inicio_mes$idMes), g.horario_inicio_mes$idMes) AS horario_inicio", false)
            ->select("IF(j.status IN ('EE', 'HE', 'SL'), IFNULL(j.horario_saida_$periodo, g.horario_termino_mes$idMes), g.horario_termino_mes$idMes) AS horario_termino", false)
            ->select("IF(j.status IN ('EE', 'HE', 'SL'), CONCAT(CONVERT(CASE j.status WHEN 'EE' THEN 'Evento Extra' WHEN 'HE' THEN 'Horas de Estudo' WHEN 'SL' THEN 'Sábado Letivo' END USING utf8), ' (', TIME_FORMAT(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, horario_entrada_$periodo, horario_saida_$periodo)), '%H:%i'), ')'), j.observacoes) AS observacoes_medicao", false)
            ->select(["GROUP_CONCAT(i.aluno ORDER BY i.aluno ASC SEPARATOR ', ') AS alunos"], false)
            ->join('ei_alocacoes_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacoes c', 'c.id = b.id_alocacao')
            ->join('ei_ordens_servico_escolas d', 'd.id = b.id_os_escola')
            ->join('ei_ordens_servico e', 'e.id = d.id_ordem_servico')
            ->join('ei_contratos f', 'f.id = e.id_contrato')
            ->join('ei_alocados_horarios g', "g.id_alocado = a.id AND g.dia_semana = '$diaSemana' AND g.periodo = '$periodo'", 'left')
            ->join('ei_matriculados_turmas h', 'h.id_alocado_horario = g.id', 'left')
            ->join('ei_matriculados i', 'i.id = h.id_matriculado AND i.id_alocacao_escola = b.id', 'left')
            ->join('ei_apontamentos j', "j.id_alocado = a.id AND j.data = '$apontamento->data'", 'left')
            ->where('a.id', $alocado->id)
            ->order_by('j.data', 'desc')
            ->order_by('j.periodo', 'desc')
            ->order_by("j.horario_entrada_$periodo", 'desc')
            ->order_by("j.horario_saida_$periodo", 'desc')
            ->get('ei_alocados a')
            ->row();

        if ($usuarioFrequencia) {
            $qb = $this->db
                ->set('observacoes', $dataApontamento->observacoes_medicao ?? null)
                ->set('horario_entrada_real_1', $apontamento->horario_entrada_1 ?? $data['data']['horario_entrada_1'] ?? null)
                ->set('horario_entrada_real_2', $apontamento->horario_entrada_2 ?? $data['data']['horario_entrada_2'] ?? null)
                ->set('horario_entrada_real_3', $apontamento->horario_entrada_3 ?? $data['data']['horario_entrada_3'] ?? null)
                ->set('horario_saida_real_1', $apontamento->horario_saida_1 ?? $data['data']['horario_saida_1'] ?? null)
                ->set('horario_saida_real_2', $apontamento->horario_saida_2 ?? $data['data']['horario_saida_2'] ?? null)
                ->set('horario_saida_real_3', $apontamento->horario_saida_3 ?? $data['data']['horario_saida_3'] ?? null);
            if ($periodo) {
                $qb->set('horario_entrada_' . $periodo, $dataApontamento->horario_inicio ?? null)
                    ->set('horario_saida_' . $periodo, $dataApontamento->horario_termino ?? null)
                    ->set('status_entrada_' . $periodo, $status[$apontamento->status] ?? null)
                    ->set('status_saida_' . $periodo, $status[$apontamento->status] ?? null);
            }
            $qb->set('observacoes', $dataApontamento->observacoes_medicao ?? null)
                ->where('id', $usuarioFrequencia->id)
                ->update('ei_usuarios_frequencias');
        } else {
            $dataFrequencia = [
                'id_usuario' => $alocado->id_cuidador,
                'data_evento' => $apontamento->data,
                'periodo_atual' => $periodo ?: 1,
                'id_escola' => $alocado->id_escola ?? null,
                'alunos' => $apontamento->alunos ?? null,
                'observacoes' => $apontamento->observacoes_medicao ?? null,
            ];

            $dataFrequencia['horario_entrada_real_1'] = $apontamento->horario_entrada_1 ?? $data['data']['horario_entrada_1'] ?? null;
            $dataFrequencia['horario_entrada_real_2'] = $apontamento->horario_entrada_2 ?? $data['data']['horario_entrada_2'] ?? null;
            $dataFrequencia['horario_entrada_real_3'] = $apontamento->horario_entrada_3 ?? $data['data']['horario_entrada_3'] ?? null;
            $dataFrequencia['horario_saida_real_1'] = $apontamento->horario_saida_1 ?? $data['data']['horario_saida_1'] ?? null;
            $dataFrequencia['horario_saida_real_2'] = $apontamento->horario_saida_2 ?? $data['data']['horario_saida_2'] ?? null;
            $dataFrequencia['horario_saida_real_3'] = $apontamento->horario_saida_3 ?? $data['data']['horario_saida_3'] ?? null;

            if ($dataFrequencia['horario_entrada_real_1']) {
                $dataFrequencia['status_entrada_1'] = $status[$apontamento->status] ?? null;
                $dataFrequencia['status_saida_1'] = $status[$apontamento->status] ?? null;
                $dataFrequencia['horario_entrada_1'] = $dataApontamento->horario_inicio ?? $dataFrequencia['horario_entrada_real_1'];
                $dataFrequencia['horario_saida_1'] = $dataApontamento->horario_termino ?? $dataFrequencia['horario_saida_real_1'];
            }
            if ($dataFrequencia['horario_entrada_real_2']) {
                $dataFrequencia['status_entrada_2'] = $status[$apontamento->status] ?? null;
                $dataFrequencia['status_saida_2'] = $status[$apontamento->status] ?? null;
                $dataFrequencia['horario_entrada_2'] = $dataApontamento->horario_inicio ?? $dataFrequencia['horario_entrada_real_2'];
                $dataFrequencia['horario_saida_2'] = $dataApontamento->horario_termino ?? $dataFrequencia['horario_saida_real_2'];
            }
            if ($dataFrequencia['horario_entrada_real_3']) {
                $dataFrequencia['status_entrada_3'] = $status[$apontamento->status] ?? null;
                $dataFrequencia['status_saida_3'] = $status[$apontamento->status] ?? null;
                $dataFrequencia['horario_entrada_3'] = $dataApontamento->horario_inicio ?? $dataFrequencia['horario_entrada_real_3'];
                $dataFrequencia['horario_saida_3'] = $dataApontamento->horario_termino ?? $dataFrequencia['horario_saida_real_3'];
            }

            if ($periodo) {
                $dataFrequencia['horario_entrada_' . $periodo] = $dataApontamento->horario_inicio ?? $dataFrequencia['horario_entrada_real_' . $periodo];
                $dataFrequencia['horario_saida_' . $periodo] = $dataApontamento->horario_termino ?? $dataFrequencia['horario_saida_real_' . $periodo];
                $dataFrequencia['status_entrada_' . $periodo] = $status[$apontamento->status] ?? null;
                $dataFrequencia['status_saida_' . $periodo] = $status[$apontamento->status] ?? null;
            }

            if (in_array($data['data']['status'], ['EE', 'HE', 'SL'])) {
                $labelNomeEvento = ['EE' => 'Evento Extra', 'HE' => 'Horas de Estudo', 'SL' => 'Sábado Letivo'];
                if (strlen($data['data']['desconto'] ?? '') > 0) {
                    $descontoObs = timeSimpleFormat($data['data']['desconto']);
                } else {
                    $descontoEntrada = timeToSec(explode(' ', $data['data']['horario_entrada_' . $periodo])[1] ?? null);
                    $descontoSaida = timeToSec(explode(' ', $data['data']['horario_saida_' . $periodo])[1] ?? null);
                    $descontoObs = secToTime($descontoSaida - $descontoEntrada, false);
                    if (strlen($descontoObs) == 0) {
                        $descontoObs = '00:00';
                    }
                }
                $dataFrequencia['observacoes'] = $labelNomeEvento[$data['data']['status']] . " ($descontoObs)";
            }
            $this->db->insert('ei_usuarios_frequencias', $dataFrequencia);
        }

        return $data;
    }
}
