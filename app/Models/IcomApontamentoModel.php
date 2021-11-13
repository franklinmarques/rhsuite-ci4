<?php

namespace App\Models;

use App\Entities\IcomApontamento;

class IcomApontamentoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_apontamentos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomApontamento::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_alocado',
        'data',
        'tipo_evento',
        'modo_acesso',
        'tipo_evento_entrada',
        'tipo_evento_saida',
        'folga',
        'horario_especial',
        'horario_fracionado',
        'horario_entrada_especial',
        'horario_saida_especial',
        'qtde_horas_diarias',
        'qtde_minutos_folga',
        'horario_entrada_especial_2',
        'horario_saida_especial_2',
        'qtde_horas_diarias_2',
        'qtde_minutos_folga_2',
        'horario_entrada_especial_3',
        'horario_saida_especial_3',
        'qtde_horas_diarias_3',
        'qtde_minutos_folga_3',
        'horario_entrada',
        'horario_intervalo',
        'horario_retorno',
        'horario_saida',
        'hora_extra',
        'desconto_folha',
        'desconto_folha_saida',
        'credito_folha',
        'banco_horas',
        'saldo_banco_horas',
        'saida_banco_horas',
        'observacoes',
        'tipo_evento_2',
        'modo_acesso_2',
        'tipo_evento_entrada_2',
        'tipo_evento_saida_2',
        'horario_entrada_2',
        'horario_intervalo_2',
        'horario_retorno_2',
        'horario_saida_2',
        'desconto_folha_2',
        'desconto_folha_saida_2',
        'credito_folha_2',
        'saldo_banco_horas_2',
        'saida_banco_horas_2',
        'observacoes_2',
        'tipo_evento_3',
        'modo_acesso_3',
        'tipo_evento_entrada_3',
        'tipo_evento_saida_3',
        'horario_entrada_3',
        'horario_intervalo_3',
        'horario_retorno_3',
        'horario_saida_3',
        'desconto_folha_3',
        'desconto_folha_saida_3',
        'credito_folha_3',
        'saldo_banco_horas_3',
        'saida_banco_horas_3',
        'observacoes_3',
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
        'data'                          => 'required|valid_date',
        'tipo_evento'                   => 'required|string|max_length[2]',
        'modo_acesso'                   => 'string|max_length[1]',
        'tipo_evento_entrada'           => 'string|max_length[2]',
        'tipo_evento_saida'             => 'string|max_length[2]',
        'folga'                         => 'integer|exact_length[1]',
        'horario_especial'              => 'integer|exact_length[1]',
        'horario_fracionado'            => 'integer|exact_length[1]',
        'horario_entrada_especial'      => 'valid_time',
        'horario_saida_especial'        => 'valid_time',
        'qtde_horas_diarias'            => 'valid_time',
        'qtde_minutos_folga'            => 'valid_time',
        'horario_entrada_especial_2'    => 'valid_time',
        'horario_saida_especial_2'      => 'valid_time',
        'qtde_horas_diarias_2'          => 'valid_time',
        'qtde_minutos_folga_2'          => 'valid_time',
        'horario_entrada_especial_3'    => 'valid_time',
        'horario_saida_especial_3'      => 'valid_time',
        'qtde_horas_diarias_3'          => 'valid_time',
        'qtde_minutos_folga_3'          => 'valid_time',
        'horario_entrada'               => 'valid_time',
        'horario_intervalo'             => 'valid_time',
        'horario_retorno'               => 'valid_time',
        'horario_saida'                 => 'valid_time',
        'hora_extra'                    => 'valid_time',
        'desconto_folha'                => 'valid_time',
        'desconto_folha_saida'          => 'valid_time',
        'credito_folha'                 => 'valid_time',
        'banco_horas'                   => 'valid_time',
        'saldo_banco_horas'             => 'valid_time',
        'saida_banco_horas'             => 'valid_time',
        'observacoes'                   => 'string',
        'tipo_evento_2'                 => 'string|max_length[2]',
        'modo_acesso_2'                 => 'string|max_length[1]',
        'tipo_evento_entrada_2'         => 'string|max_length[2]',
        'tipo_evento_saida_2'           => 'string|max_length[2]',
        'horario_entrada_2'             => 'valid_time',
        'horario_intervalo_2'           => 'valid_time',
        'horario_retorno_2'             => 'valid_time',
        'horario_saida_2'               => 'valid_time',
        'desconto_folha_2'              => 'valid_time',
        'desconto_folha_saida_2'        => 'valid_time',
        'credito_folha_2'               => 'valid_time',
        'saldo_banco_horas_2'           => 'valid_time',
        'saida_banco_horas_2'           => 'valid_time',
        'observacoes_2'                 => 'string',
        'tipo_evento_3'                 => 'string|max_length[2]',
        'modo_acesso_3'                 => 'string|max_length[1]',
        'tipo_evento_entrada_3'         => 'string|max_length[2]',
        'tipo_evento_saida_3'           => 'string|max_length[2]',
        'horario_entrada_3'             => 'valid_time',
        'horario_intervalo_3'           => 'valid_time',
        'horario_retorno_3'             => 'valid_time',
        'horario_saida_3'               => 'valid_time',
        'desconto_folha_3'              => 'valid_time',
        'desconto_folha_saida_3'        => 'valid_time',
        'credito_folha_3'               => 'valid_time',
        'saldo_banco_horas_3'           => 'valid_time',
        'saida_banco_horas_3'           => 'valid_time',
        'observacoes_3'                 => 'string',
    ];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = ['desvincularSaldoHorasAnterior', 'calcularSaldoHorasAtual'];
	protected $afterInsert          = ['atualizarDescontoFolhaMensal', 'vincularSaldoHorasAtual', 'salvarApontamentoWeb', 'insertEvento'];
	protected $beforeUpdate         = ['desvincularSaldoHorasAnterior', 'calcularSaldoHorasAtual'];
	protected $afterUpdate          = ['atualizarDescontoFolhaMensal', 'vincularSaldoHorasAtual', 'salvarApontamentoWeb', 'updateEvento'];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = ['desvincularSaldoHorasAnterior', 'excluirApontamentoWeb', 'limparDescontoFolhaMensal', 'deleteEvento'];
	protected $afterDelete          = ['vincularSaldoHorasAtual', 'salvarApontamentoWeb'];

    //--------------------------------------------------------------------

    public $alert;

    public const TIPOS_EVENTO = [
        'FO' => 'Folga',
        'HE' => 'Horário especial',
        'FR' => 'Férias',
        'DL' => 'Desligamento',
        'HF' => 'Horário fracionado',
        'PN' => 'Presença normal',
        'FJ' => 'Falta com atestado próprio',
        'FN' => 'Falta sem atestado',
        'FC' => 'Falta a compensar',
        'FA' => 'Falta abonada',
        'BH' => 'Compensação banco de horas',
        'AC' => 'Atraso/saída antecipada a compensar',
        'AJ' => 'Atraso com atestado próprio',
        'AS' => 'Atraso sem atestado',
        'SP' => 'Saída pós-horário',
        'CO' => 'Compensação (Trabalho dia de folga - MEI)',
        'EA' => 'Entrada antecipada',
        'SJ' => 'Saída antecipada com atestado próprio',
        'SN' => 'Saída antecipada sem atestado',
        'EX' => 'Hora extra (Trabalho dia de feriado - CLT)',
    ];
    public const PERIODOS = [
        '0' => 'Madrugada',
        '1' => 'Manhã',
        '2' => 'Tarde',
        '3' => 'Noite',
    ];

    private $flagSaldoBancoHoras;

    private static $eventosBancoHoras = ['EA', 'SP', 'AC', 'FC', 'CO', 'BH', 'AS', 'SN'];
    private static $eventosApontamentoWeb = ['PN', 'FO', 'FR', 'DL', 'HE', 'HF', 'EA', 'AC', 'AJ', 'AS', 'SP', 'SJ', 'SN', 'FJ', 'FN', 'FA', 'FC', 'CO', 'BH', 'EX'];
    private static $eventosCompensacao = ['CO', 'BH'];
    private static $eventosPresenciais = ['FJ', 'FN', 'FC', 'FA'];
    private static $eventosEntrada = ['EA', 'AJ', 'AS'];
    private static $eventosSaida = ['SP', 'SJ', 'SN'];
    private static $eventosFolga = ['FO', 'HE', 'FR', 'HF'];

    //--------------------------------------------------------------------

    protected function desvincularSaldoHorasAnterior($data)
    {
        if (!empty($data[$this->primaryKey][0])) {
            $apontamento = $this
                ->where($this->primaryKey, $data[$this->primaryKey][0])
                ->first();

            $this->flagIdAlocado = [$apontamento->id_alocado];
        } else {
            if (!empty($data['data']['id_alocado'])) {
                $this->flagIdAlocado = $data['data']['id_alocado'];
            }

            return $data;
        }

        if (!empty($data['data']['horario_fracionado'])) {
            return $data;
        }

        $row = $this->db
            ->select('a.id_alocado, a.saldo_banco_horas, a.saida_banco_horas, b.id_usuario, c.banco_horas_icom, c.banco_horas_icom_2, a.tipo_evento')
            ->select('a.saldo_banco_horas_2, a.saida_banco_horas_2, a.tipo_evento_2')
            ->select('a.saldo_banco_horas_3, a.saida_banco_horas_3, a.tipo_evento_3')
            ->join('icom_alocados b', 'b.id = a.id_alocado')
            ->join('usuarios c', 'c.id = b.id_usuario')
            ->where_in('a.id', $data[$this->primaryKey])
            ->group_start()
            ->where('(a.saldo_banco_horas IS NOT NULL OR a.saida_banco_horas IS NOT NULL)', null, false)
            ->or_where("(a.tipo_evento = 'EX' AND a.credito_folha IS NOT NULL)", null, false)
            ->or_where('(a.saldo_banco_horas_2 IS NOT NULL OR a.saida_banco_horas_2 IS NOT NULL)', null, false)
            ->or_where('(a.saldo_banco_horas_3 IS NOT NULL OR a.saida_banco_horas_3 IS NOT NULL)', null, false)
            ->group_end()
            ->get('icom_apontamentos a')
            ->row();

        if (empty($row)) {
            return $data;
        } elseif (!in_array($row->tipo_evento, ['EA', 'SP', 'AS', 'SN', 'FC', 'CO', 'BH', 'EX'])) {
            return $data;
        }

        $this->load->helper('time');

        $bancoHoras = timeToSec($row->banco_horas_icom) - (timeToSec($row->saldo_banco_horas) + timeToSec($row->saida_banco_horas) + timeToSec($row->saldo_banco_horas_2 ?? 0) + timeToSec($row->saida_banco_horas_2 ?? 0) + timeToSec($row->saldo_banco_horas_3 ?? 0) + timeToSec($row->saida_banco_horas_3 ?? 0));

        $this->db->update('usuarios', ['banco_horas_icom' => secToTime($bancoHoras)], ['id' => $row->id_usuario]);

        // --------------------------------------------

        $row = $this->db
            ->select('b.id_usuario, a.data, DAY(a.data) AS dia', false)
            ->select('MONTH(a.data) AS mes, YEAR(a.data) AS ano', false)
            ->join('icom_alocados b', 'b.id = a.id_alocado')
            ->where_in('a.id', $data[$this->primaryKey])
            ->get($this->table . ' a')
            ->row();

        $apontamentoWeb2 = $this->db
            ->select('a.banco_horas_icom_2')
            ->select('SUM(IFNULL(TIME_TO_SEC(b.saldo_horas_2), 0)) AS saldo_horas_2', false)
            ->join('usuarios_apontamentos_horas_2 b', "b.id_usuario = a.id AND DATE_FORMAT(b.data_hora, '%Y-%m-%d') = '$row->data'", 'left')
            ->where('a.id', $row->id_usuario)
            ->group_by('a.id')
            ->get('usuarios a')
            ->row();

        $this->load->helper('time');

        $horas_icom_2 = secToTime(timeToSec($apontamentoWeb2->banco_horas_icom_2) - ($apontamentoWeb2->saldo_horas_2 ?? 0));

        $this->db
            ->set('banco_horas_icom_2', $horas_icom_2)
            ->where('id', $row->id_usuario)
            ->update('usuarios');

        return $data;
    }

    //--------------------------------------------------------------------

    protected function excluirApontamentoWeb($data)
    {
        if (!empty($data['id']) == false) {
            return $data;
        }

        $row = $this->db
            ->select('b.id_usuario, DAY(a.data) AS dia', false)
            ->select('MONTH(a.data) AS mes, YEAR(a.data) AS ano', false)
            ->join('icom_alocados b', 'b.id = a.id_alocado')
            ->where_in('a.id', $data['id'])
            ->get($this->table . ' a')
            ->row();

        if ($row) {
            $this->db
                ->where('id_usuario', $row->id_usuario)
                ->where('DAY(data_hora)', $row->dia)
                ->where('MONTH(data_hora)', $row->mes)
                ->where('YEAR(data_hora)', $row->ano)
                ->delete('usuarios_apontamentos_horas');

            // -----------------------------------------------

            $this->db
                ->where('id_usuario', $row->id_usuario)
                ->where('DAY(data_hora)', $row->dia)
                ->where('MONTH(data_hora)', $row->mes)
                ->where('YEAR(data_hora)', $row->ano)
                ->delete('usuarios_apontamentos_horas_2');
        }

        return $data;
    }

    //--------------------------------------------------------------------

    protected function calcularSaldoHorasAtual($data): array
    {
        if (!empty($data['data']) == false) {
            return $data;
        }

        $id = $data['data']['id'] ?? null;

        $row = $this->db
            ->select('a.id, c.categoria, a.horas_dia, a.minutos_descanso_dia, a.qtde_horas_dia_mei, a.qtde_horas_dia_clt, b.banco_horas_icom, e.tipo_evento')
            ->select('a.horario_entrada, a.horario_intervalo, a.horario_retorno, a.horario_saida, e.horario_fracionado')
            ->select('e.horario_entrada_2, e.horario_saida_2, e.horario_entrada_3, e.horario_saida_3')
            ->select('e.horario_entrada_especial, e.horario_saida_especial, e.desconto_folha, e.desconto_folha_saida')
            ->select('e.horario_entrada_especial_2, e.horario_saida_especial_2, e.desconto_folha_2, e.desconto_folha_saida_2')
            ->select('e.horario_entrada_especial_3, e.horario_saida_especial_3, e.desconto_folha_3, e.desconto_folha_saida_3')
            ->join('usuarios b', 'b.id = a.id_usuario')
            ->join('icom_alocados c', 'c.id_usuario = b.id')
            ->join('icom_alocacoes d', 'd.id = c.id_alocacao AND d.id_setor = a.id_setor')
            ->join('icom_apontamentos e', "e.id_alocado = c.id AND e.id = '$id'", 'left')
            ->where('c.id', $data['data']['id_alocado'])
            ->get('icom_postos a')
            ->row();

        if (empty($row) and ($data['data']['tipo_evento'] !== 'BH' and $data['data']['tipo_evento_2'] !== 'BH' and $data['data']['tipo_evento_3'] !== 'BH')) {
            return $data;
        }

        if (!empty($data['data']['tipo_evento']) == false and !empty($data['data']['folga'])) {
            $data['data']['tipo_evento'] = 'FO';
        }
        if (!empty($data['data']['tipo_evento_2']) == false and !empty($data['data']['folga'])) {
            $data['data']['tipo_evento_2'] = 'FO';
        }
        if (!empty($data['data']['tipo_evento_3']) == false and !empty($data['data']['folga'])) {
            $data['data']['tipo_evento_3'] = 'FO';
        }

        if (!empty($data['data']['tipo_evento']) == false and !empty($data['data']['horario_especial'])) {
            $data['data']['tipo_evento'] = 'HE';
        }
        if (!empty($data['data']['tipo_evento_2']) == false and !empty($data['data']['horario_especial'])) {
            $data['data']['tipo_evento_2'] = 'HE';
        }
        if (!empty($data['data']['tipo_evento_3']) == false and !empty($data['data']['horario_especial'])) {
            $data['data']['tipo_evento_3'] = 'HE';
        }

        if (!empty($data['data']['tipo_evento']) == false and !empty($data['data']['horario_fracionado'])) {
            $data['data']['tipo_evento'] = 'HF';
        }
        if (!empty($data['data']['tipo_evento_2']) == false and !empty($data['data']['horario_fracionado'])) {
            $data['data']['tipo_evento_2'] = 'HF';
        }
        if (!empty($data['data']['tipo_evento_3']) == false and !empty($data['data']['horario_fracionado'])) {
            $data['data']['tipo_evento_3'] = 'HF';
        }

        $this->load->helper('time');

        if ($row->tipo_evento == 'HF' or !empty($data['data']['horario_fracionado'])) {
            $row->horario_entrada = $data['data']['horario_entrada_especial'] ?? $row->horario_entrada;
            $row->horario_saida = $data['data']['horario_saida_especial'] ?? $row->horario_saida;
            $row->horas_dia = $data['data']['qtde_horas_diarias'] ?? $row->horas_dia;
            $row->minutos_descanso_dia = $data['data']['qtde_minutos_folga'] ?? $row->minutos_descanso_dia;
        } elseif ($row->tipo_evento == 'HE' and $data['data']['tipo_evento'] == 'HE') {
            $row->horario_entrada = $row->horario_entrada_especial;
            $row->horario_saida = $row->horario_saida_especial;
        } elseif ($row->tipo_evento == 'FO' or $data['data']['tipo_evento'] == 'FO') {
            $row->horario_entrada = $row->horario_entrada_especial;
            $row->horario_saida = $row->horario_saida_especial;
        }

        $horarioEntrada = $data['data']['horario_entrada'] ?? null;
        $horarioIntervalo = $data['data']['horario_intervalo'] ?? null;
        $horarioRetorno = $data['data']['horario_retorno'] ?? null;
        $horarioSaida = $data['data']['horario_saida'] ?? null;
        $descontoEntrada = 0;
        $descontoSaida = 0;

        switch ($data['data']['tipo_evento']) {
            case 'DL':
            case 'FN':
                $descontoFolha = timeToSec($row->horas_dia) - timeToSec($row->minutos_descanso_dia ?? 0);
                $data['data']['desconto_folha'] = secToTime($descontoFolha * ($descontoFolha < 0 ? 1 : -1));
                $data['data']['desconto_folha_saida'] = null;
                $data['data']['saldo_banco_horas'] = null;
                $data['data']['saida_banco_horas'] = null;
                break;
            case 'FJ':
                $descontoFolha = $row->categoria == 'MEI' ? timeToSec($row->horas_dia) - timeToSec($row->minutos_descanso_dia ?? 0) : 0;
                $data['data']['desconto_folha'] = secToTime($descontoFolha * ($descontoFolha < 0 ? 1 : -1));
                $data['data']['desconto_folha_saida'] = null;
                $data['data']['saldo_banco_horas'] = null;
                $data['data']['saida_banco_horas'] = null;
                break;
            case 'FC':
                $descontoFolha = timeToSec($row->horas_dia) - timeToSec($row->minutos_descanso_dia ?? 0);
                $descontoEntrada = $descontoFolha * ($descontoFolha < 0 ? 1 : -1);
                $data['data']['desconto_folha'] = null;
                $data['data']['desconto_folha_saida'] = null;
                $data['data']['saldo_banco_horas'] = secToTime($descontoEntrada);
                $data['data']['saida_banco_horas'] = null;
                break;
            case 'CO':
                $descontoEntrada = timeToSec($row->horas_dia) - timeToSec($row->minutos_descanso_dia ?? 0);
                $data['data']['saldo_banco_horas'] = secToTime($descontoEntrada);
                break;
            case 'AJ':
            case 'AS':
            case 'EA':
                if (!empty($data['data']['folga']) or !empty($data['data']['horario_especial']) or !empty($data['data']['horario_fracionado'])) {
                    $row->horario_entrada = $row->horario_entrada_especial;
                    $row->horario_retorno = $row->horario_entrada_especial;
                }
                $data['data']['tipo_evento_entrada'] = $data['data']['tipo_evento'];
                $descontoSaida = timeToSec($data['data']['saida_banco_horas']);
                if ($data['data']['tipo_evento'] === 'EA') {
                    $timestampEntrada = $this->calcularHorasDescontadas($row->horario_entrada, $horarioEntrada);
                    $timestampRetorno = $this->calcularHorasDescontadas($row->horario_retorno, $horarioRetorno);
                    $descontoEntrada = max($timestampEntrada, 0) + max($timestampRetorno, 0);
                    $data['data']['desconto_folha'] = null;
                    $data['data']['saldo_banco_horas'] = secToTime($descontoEntrada);
                } else {
                    if ($data['data']['tipo_evento'] === 'AS') {
                        $timestampEntrada = $this->calcularHorasDescontadas($row->horario_entrada, $horarioEntrada, true);
                        $timestampRetorno = $this->calcularHorasDescontadas($row->horario_retorno, $horarioRetorno, true);
                        $descontoEntrada = (max($timestampEntrada, 0) + max($timestampRetorno, 0)) * (-1);
                        $data['data']['desconto_folha'] = null;
                        $data['data']['saldo_banco_horas'] = secToTime($descontoEntrada);
                    } else {
                        $data['data']['desconto_folha'] = null;
                        $data['data']['saldo_banco_horas'] = null;
                    }
                }
                break;
            case 'EX':
                if (!empty($data['data']['credito_folha'])) {
                    $descontoEntrada = timeToSec($data['data']['credito_folha']);
                } else {
                    $horaExtra = timeToSec($row->horas_dia) - timeToSec($row->minutos_descanso_dia ?? 0);
                    $descontoEntrada = $horaExtra * ($horaExtra < 0 ? -1 : 1);
                }
                $descontoEntrada = $descontoEntrada + timeToSec($data['data']['desconto_folha']);
                break;
            case 'SJ':
            case 'SP':
            case 'SN':
                if (!empty($data['data']['folga']) or !empty($data['data']['horario_especial']) or !empty($data['data']['horario_fracionado'])) {
                    $row->horario_intervalo = $row->horario_saida_especial;
                    $row->horario_saida = $row->horario_saida_especial;
                }
                $data['data']['tipo_evento_saida'] = $data['data']['tipo_evento'];
                $descontoEntrada = timeToSec($data['data']['saldo_banco_horas']);
                if ($data['data']['tipo_evento'] === 'SP') {
                    $timestampIntervalo = $this->calcularHorasDescontadas($row->horario_intervalo, $horarioIntervalo, true, $row->horario_entrada);
                    $timestampSaida = $this->calcularHorasDescontadas($row->horario_saida, $horarioSaida, true, $row->horario_entrada);
                    $descontoSaida = (max($timestampIntervalo, 0) + max($timestampSaida, 0));
                    $data['data']['desconto_folha_saida'] = null;
                    $data['data']['saida_banco_horas'] = secToTime($descontoSaida);
                } else {
                    if ($data['data']['tipo_evento'] === 'SN') {
                        $timestampIntervalo = $this->calcularHorasDescontadas($row->horario_intervalo, $horarioIntervalo, false, $row->horario_entrada);
                        $timestampSaida = $this->calcularHorasDescontadas($row->horario_saida, $horarioSaida, false, $row->horario_entrada);
                        $descontoSaida = (max($timestampIntervalo, 0) + max($timestampSaida, 0)) * (-1);
                        $data['data']['desconto_folha_saida'] = null;
                        $data['data']['saida_banco_horas'] = secToTime($descontoSaida);
                    } else {
                        $data['data']['desconto_folha_saida'] = null;
                        $data['data']['saida_banco_horas'] = null;
                    }
                }
                break;
            case 'AC':
                $timestampEntrada = $this->calcularHorasDescontadas($row->horario_entrada, $horarioEntrada, true);
                $timestampIntervalo = $this->calcularHorasDescontadas($row->horario_intervalo, $horarioIntervalo);
                $timestampRetorno = $this->calcularHorasDescontadas($row->horario_retorno, $horarioRetorno, true);
                $timestampSaida = $this->calcularHorasDescontadas($row->horario_saida, $horarioSaida);
                $descontoEntrada = (max($timestampEntrada, 0) + max($timestampRetorno, 0)) * (-1);
                $descontoSaida = (max($timestampIntervalo, 0) + max($timestampSaida, 0)) * (-1);
                $data['data']['saldo_banco_horas'] = secToTime($descontoEntrada);
                $data['data']['saida_banco_horas'] = secToTime($descontoSaida);
                $data['data']['desconto_folha'] = null;
                $data['data']['desconto_folha_saida'] = null;
                break;
            case 'BH':
                $descontoEntrada = timeToSec($data['data']['saldo_banco_horas'] ?? 0);
                break;
        }

        if ($row->tipo_evento == 'HF' or !empty($data['data']['horario_fracionado'])) {
            $row->horario_entrada_2 = $data['data']['horario_entrada_especial_2'] ?? $row->horario_entrada_2;
            $row->horario_saida_2 = $data['data']['horario_saida_especial_2'] ?? $row->horario_saida_2;
            $row->horas_dia = $data['data']['qtde_horas_diarias_2'] ?? $row->horas_dia;
            $row->minutos_descanso_dia = $data['data']['qtde_minutos_folga_2'] ?? $row->minutos_descanso_dia;
        } elseif ($row->tipo_evento == 'HE' and $data['data']['tipo_evento_2'] == 'HE') {
            $row->horario_entrada_2 = $row->horario_entrada_especial_2;
            $row->horario_saida_2 = $row->horario_saida_especial_2;
        } elseif ($row->tipo_evento == 'FO' or $data['data']['tipo_evento_2'] == 'FO') {
            $row->horario_entrada_2 = $row->horario_entrada_especial_2;
            $row->horario_saida_2 = $row->horario_saida_especial_2;
        }

        $horarioEntrada2 = $data['data']['horario_entrada_2'] ?? null;
        $horarioIntervalo2 = $data['data']['horario_intervalo_2'] ?? null;
        $horarioRetorno2 = $data['data']['horario_retorno_2'] ?? null;
        $horarioSaida2 = $data['data']['horario_saida_2'] ?? null;
        $descontoEntrada2 = 0;
        $descontoSaida2 = 0;


        switch ($data['data']['tipo_evento_2']) {
            case 'FN':
                $descontoFolha2 = timeToSec($row->horas_dia) - timeToSec($row->minutos_descanso_dia ?? 0);
                $data['data']['desconto_folha_2'] = secToTime($descontoFolha2 * ($descontoFolha2 < 0 ? 1 : -1));
                $data['data']['desconto_folha_saida_2'] = null;
                $data['data']['saldo_banco_horas_2'] = null;
                $data['data']['saida_banco_horas_2'] = null;
                break;
            case 'FJ':
                $descontoFolha2 = $row->categoria == 'MEI' ? timeToSec($row->horas_dia) - timeToSec($row->minutos_descanso_dia ?? 0) : 0;
                $data['data']['desconto_folha_2'] = secToTime($descontoFolha2 * ($descontoFolha2 < 0 ? 1 : -1));
                $data['data']['desconto_folha_saida_2'] = null;
                $data['data']['saldo_banco_horas_2'] = null;
                $data['data']['saida_banco_horas_2'] = null;
                break;
            case 'FC':
                $descontoFolha2 = timeToSec($row->horas_dia) - timeToSec($row->minutos_descanso_dia ?? 0);
                $descontoEntrada2 = $descontoFolha2 * ($descontoFolha2 < 0 ? 1 : -1);
                $data['data']['desconto_folha_2'] = null;
                $data['data']['desconto_folha_saida_2'] = null;
                $data['data']['saldo_banco_horas_2'] = secToTime($descontoEntrada2);
                $data['data']['saida_banco_horas_2'] = null;
                break;
            case 'CO':
                $descontoEntrada2 = timeToSec($row->horas_dia) - timeToSec($row->minutos_descanso_dia ?? 0);
                $data['data']['saldo_banco_horas_2'] = secToTime($descontoEntrada2);
                break;
            case 'AJ':
            case 'AS':
            case 'EA':
                if (!empty($data['data']['folga']) or !empty($data['data']['horario_especial']) or !empty($data['data']['horario_fracionado'])) {
                    $row->horario_entrada_2 = $row->horario_entrada_especial_2;
                    $row->horario_retorno_2 = $row->horario_entrada_especial_2;
                }
                $data['data']['tipo_evento_entrada_2'] = $data['data']['tipo_evento_2'];
                $descontoSaida2 = timeToSec($data['data']['saida_banco_horas_2']);
                if ($data['data']['tipo_evento_2'] === 'EA') {
                    $timestampEntrada2 = $this->calcularHorasDescontadas($row->horario_entrada_2, $horarioEntrada2);
                    $timestampRetorno2 = $this->calcularHorasDescontadas($row->horario_retorno_2, $horarioRetorno2);
                    $descontoEntrada2 = max($timestampEntrada2, 0) + max($timestampRetorno2, 0);
                    $data['data']['desconto_folha_2'] = null;
                    $data['data']['saldo_banco_horas_2'] = secToTime($descontoEntrada2);
                } else {
                    if ($data['data']['tipo_evento_2'] === 'AS') {
                        $timestampEntrada2 = $this->calcularHorasDescontadas($row->horario_entrada_2, $horarioEntrada2, true);
                        $timestampRetorno2 = $this->calcularHorasDescontadas($row->horario_retorno_2, $horarioRetorno2, true);
                        $descontoEntrada2 = (max($timestampEntrada2, 0) + max($timestampRetorno2, 0)) * (-1);
                        $data['data']['desconto_folha_2'] = null;
                        $data['data']['saldo_banco_horas_2'] = secToTime($descontoEntrada2);
                    } else {
                        $data['data']['desconto_folha_2'] = null;
                        $data['data']['saldo_banco_horas_2'] = null;
                    }
                }
                break;
            case 'EX':
                if (!empty($data['data']['credito_folha_2'])) {
                    $descontoEntrada2 = timeToSec($data['data']['credito_folha_2']);
                } else {
                    $horaExtra2 = timeToSec($row->horas_dia) - timeToSec($row->minutos_descanso_dia ?? 0);
                    $descontoEntrada2 = $horaExtra2 * ($horaExtra2 < 0 ? -1 : 1);
                }
                $descontoEntrada2 = $descontoEntrada2 + timeToSec($data['data']['desconto_folha_2']);
                break;
            case 'SJ':
            case 'SP':
            case 'SN':
                if (!empty($data['data']['folga']) or !empty($data['data']['horario_especial']) or !empty($data['data']['horario_fracionado'])) {
                    $row->horario_intervalo_2 = $row->horario_saida_especial_2;
                    $row->horario_saida_2 = $row->horario_saida_especial_2;
                }
                $data['data']['tipo_evento_saida_2'] = $data['data']['tipo_evento_2'];
                $descontoEntrada2 = timeToSec($data['data']['saldo_banco_horas_2']);
                if ($data['data']['tipo_evento_2'] === 'SP') {
                    $timestampIntervalo2 = $this->calcularHorasDescontadas($row->horario_intervalo_2, $horarioIntervalo2, true, $row->horario_entrada_2);
                    $timestampSaida2 = $this->calcularHorasDescontadas($row->horario_saida_2, $horarioSaida2, true, $row->horario_entrada_2);
                    $descontoSaida2 = (max($timestampIntervalo2, 0) + max($timestampSaida2, 0));
                    $data['data']['desconto_folha_saida_2'] = null;
                    $data['data']['saida_banco_horas_2'] = secToTime($descontoSaida2);
                } else {
                    if ($data['data']['tipo_evento_2'] === 'SN') {
                        $timestampIntervalo2 = $this->calcularHorasDescontadas($row->horario_intervalo_2, $horarioIntervalo2, false, $row->horario_entrada_2);
                        $timestampSaida2 = $this->calcularHorasDescontadas($row->horario_saida_2, $horarioSaida2, false, $row->horario_entrada_2);
                        $descontoSaida2 = (max($timestampIntervalo2, 0) + max($timestampSaida2, 0)) * (-1);
                        $data['data']['desconto_folha_saida_2'] = null;
                        $data['data']['saida_banco_horas_2'] = secToTime($descontoSaida2);
                    } else {
                        $data['data']['desconto_folha_saida_2'] = null;
                        $data['data']['saida_banco_horas_2'] = null;
                    }
                }
                break;
            case 'AC':
                $timestampEntrada2 = $this->calcularHorasDescontadas($row->horario_entrada_2, $horarioEntrada2, true);
                $timestampIntervalo2 = $this->calcularHorasDescontadas($row->horario_intervalo_2, $horarioIntervalo2);
                $timestampRetorno2 = $this->calcularHorasDescontadas($row->horario_retorno_2, $horarioRetorno2, true);
                $timestampSaida2 = $this->calcularHorasDescontadas($row->horario_saida_2, $horarioSaida2);
                $descontoEntrada2 = (max($timestampEntrada2, 0) + max($timestampRetorno2, 0)) * (-1);
                $descontoSaida2 = (max($timestampIntervalo2, 0) + max($timestampSaida2, 0)) * (-1);
                $data['data']['saldo_banco_horas_2'] = secToTime($descontoEntrada2);
                $data['data']['saida_banco_horas_2'] = secToTime($descontoSaida2);
                $data['data']['desconto_folha_2'] = null;
                $data['data']['desconto_folha_saida_2'] = null;
                break;
            case 'BH':
                $descontoEntrada2 = timeToSec($data['data']['saldo_banco_horas_2'] ?? 0);
                break;
        }

        if ($row->tipo_evento == 'HF' or !empty($data['data']['horario_fracionado'])) {
            $row->horario_entrada_3 = $data['data']['horario_entrada_especial_3'] ?? $row->horario_entrada_3;
            $row->horario_saida_3 = $data['data']['horario_saida_especial_3'] ?? $row->horario_saida_3;
            $row->horas_dia = $data['data']['qtde_horas_diarias_3'] ?? $row->horas_dia;
            $row->minutos_descanso_dia = $data['data']['qtde_minutos_folga_3'] ?? $row->minutos_descanso_dia;
        } elseif ($row->tipo_evento == 'HE' or $data['data']['tipo_evento_3'] == 'HE') {
            $row->horario_entrada_3 = $row->horario_entrada_especial_3;
            $row->horario_saida_3 = $row->horario_saida_especial_3;
        } elseif ($row->tipo_evento == 'FO' and $data['data']['tipo_evento_3'] == 'FO') {
            $row->horario_entrada_3 = $row->horario_entrada_especial_3;
            $row->horario_saida_3 = $row->horario_saida_especial_3;
        }

        $horarioEntrada3 = $data['data']['horario_entrada_3'] ?? null;
        $horarioIntervalo3 = $data['data']['horario_intervalo_3'] ?? null;
        $horarioRetorno3 = $data['data']['horario_retorno_3'] ?? null;
        $horarioSaida3 = $data['data']['horario_saida_3'] ?? null;
        $descontoEntrada3 = 0;
        $descontoSaida3 = 0;

        switch ($data['data']['tipo_evento_3']) {
            case 'FN':
                $descontoFolha3 = timeToSec($row->horas_dia) - timeToSec($row->minutos_descanso_dia ?? 0);
                $data['data']['desconto_folha_3'] = secToTime($descontoFolha3 * ($descontoFolha3 < 0 ? 1 : -1));
                $data['data']['desconto_folha_saida_3'] = null;
                $data['data']['saldo_banco_horas_3'] = null;
                $data['data']['saida_banco_horas_3'] = null;
                break;
            case 'FJ':
                $descontoFolha3 = $row->categoria == 'MEI' ? timeToSec($row->horas_dia) - timeToSec($row->minutos_descanso_dia ?? 0) : 0;
                $data['data']['desconto_folha_3'] = secToTime($descontoFolha3 * ($descontoFolha3 < 0 ? 1 : -1));
                $data['data']['desconto_folha_saida_3'] = null;
                $data['data']['saldo_banco_horas_3'] = null;
                $data['data']['saida_banco_horas_3'] = null;
                break;
            case 'FC':
                $descontoFolha3 = timeToSec($row->horas_dia) - timeToSec($row->minutos_descanso_dia ?? 0);
                $descontoEntrada3 = $descontoFolha3 * ($descontoFolha3 < 0 ? 1 : -1);
                $data['data']['desconto_folha_3'] = null;
                $data['data']['desconto_folha_saida_3'] = null;
                $data['data']['saldo_banco_horas_3'] = secToTime($descontoEntrada3);
                $data['data']['saida_banco_horas_3'] = null;
                break;
            case 'CO':
                $descontoEntrada3 = timeToSec($row->horas_dia) - timeToSec($row->minutos_descanso_dia ?? 0);
                $data['data']['saldo_banco_horas_3'] = secToTime($descontoEntrada3);
                break;
            case 'AJ':
            case 'AS':
            case 'EA':
                if (!empty($data['data']['folga']) or !empty($data['data']['horario_especial']) or !empty($data['data']['horario_fracionado'])) {
                    $row->horario_entrada_3 = $row->horario_entrada_especial_3;
                    $row->horario_retorno_3 = $row->horario_entrada_especial_3;
                }
                $data['data']['tipo_evento_entrada_3'] = $data['data']['tipo_evento_3'];
                $descontoSaida3 = timeToSec($data['data']['saida_banco_horas_3']);
                if ($data['data']['tipo_evento_3'] === 'EA') {
                    $timestampEntrada3 = $this->calcularHorasDescontadas($row->horario_entrada_3, $horarioEntrada3);
                    $timestampRetorno3 = $this->calcularHorasDescontadas($row->horario_retorno_3, $horarioRetorno3);
                    $descontoEntrada3 = max($timestampEntrada3, 0) + max($timestampRetorno3, 0);
                    $data['data']['desconto_folha_3'] = null;
                    $data['data']['saldo_banco_horas_3'] = secToTime($descontoEntrada3);
                } else {
                    if ($data['data']['tipo_evento_3'] === 'AS') {
                        $timestampEntrada3 = $this->calcularHorasDescontadas($row->horario_entrada_3, $horarioEntrada3, true);
                        $timestampRetorno3 = $this->calcularHorasDescontadas($row->horario_retorno_3, $horarioRetorno3, true);
                        $descontoEntrada3 = (max($timestampEntrada3, 0) + max($timestampRetorno3, 0)) * (-1);
                        $data['data']['desconto_folha_3'] = null;
                        $data['data']['saldo_banco_horas_3'] = secToTime($descontoEntrada3);
                    } else {
                        $data['data']['desconto_folha_3'] = null;
                        $data['data']['saldo_banco_horas_3'] = null;
                    }
                }
                break;
            case 'EX':
                if (!empty($data['data']['credito_folha_3'])) {
                    $descontoEntrada3 = timeToSec($data['data']['credito_folha_3']);
                } else {
                    $horaExtra3 = timeToSec($row->horas_dia) - timeToSec($row->minutos_descanso_dia ?? 0);
                    $descontoEntrada3 = $horaExtra3 * ($horaExtra3 < 0 ? -1 : 1);
                }
                $descontoEntrada3 = $descontoEntrada3 + timeToSec($data['data']['desconto_folha_3']);
                break;
            case 'SJ':
            case 'SP':
            case 'SN':
                if (!empty($data['data']['folga']) or !empty($data['data']['horario_especial']) or !empty($data['data']['horario_fracionado'])) {
                    $row->horario_intervalo_3 = $row->horario_saida_especial_3;
                    $row->horario_saida_3 = $row->horario_saida_especial_3;
                }
                $data['data']['tipo_evento_saida_3'] = $data['data']['tipo_evento_3'];
                $descontoEntrada3 = timeToSec($data['data']['saldo_banco_horas_3']);
                if ($data['data']['tipo_evento_3'] === 'SP') {
                    $timestampIntervalo3 = $this->calcularHorasDescontadas($row->horario_intervalo_3, $horarioIntervalo3, true, $row->horario_entrada_3);
                    $timestampSaida3 = $this->calcularHorasDescontadas($row->horario_saida_3, $horarioSaida3, true, $row->horario_entrada_3);
                    $descontoSaida3 = (max($timestampIntervalo3, 0) + max($timestampSaida3, 0));
                    $data['data']['desconto_folha_saida_3'] = null;
                    $data['data']['saida_banco_horas_3'] = secToTime($descontoSaida3);
                } else {
                    if ($data['data']['tipo_evento_3'] === 'SN') {
                        $timestampIntervalo3 = $this->calcularHorasDescontadas($row->horario_intervalo_3, $horarioIntervalo3, false, $row->horario_entrada_3);
                        $timestampSaida3 = $this->calcularHorasDescontadas($row->horario_saida_3, $horarioSaida3, false, $row->horario_entrada_3);
                        $descontoSaida3 = (max($timestampIntervalo3, 0) + max($timestampSaida3, 0)) * (-1);
                        $data['data']['desconto_folha_saida_3'] = null;
                        $data['data']['saida_banco_horas_3'] = secToTime($descontoSaida3);
                    } else {
                        $data['data']['desconto_folha_saida_3'] = null;
                        $data['data']['saida_banco_horas_3'] = null;
                    }
                }
                break;
            case 'AC':
                $timestampEntrada3 = $this->calcularHorasDescontadas($row->horario_entrada_3, $horarioEntrada3, true);
                $timestampIntervalo3 = $this->calcularHorasDescontadas($row->horario_intervalo_3, $horarioIntervalo3);
                $timestampRetorno3 = $this->calcularHorasDescontadas($row->horario_retorno_3, $horarioRetorno3, true);
                $timestampSaida3 = $this->calcularHorasDescontadas($row->horario_saida_3, $horarioSaida3);
                $descontoEntrada3 = (max($timestampEntrada3, 0) + max($timestampRetorno3, 0)) * (-1);
                $descontoSaida3 = (max($timestampIntervalo3, 0) + max($timestampSaida3, 0)) * (-1);
                $data['data']['saldo_banco_horas_3'] = secToTime($descontoEntrada3);
                $data['data']['saida_banco_horas_3'] = secToTime($descontoSaida3);
                $data['data']['desconto_folha_3'] = null;
                $data['data']['desconto_folha_saida_3'] = null;
                break;
            case 'BH':
                $descontoEntrada3 = timeToSec($data['data']['saldo_banco_horas_3'] ?? 0);
                break;
        }

        if (!empty($data['data']['folga']) and $descontoEntrada < 0) {
            $descontoEntrada = 0;
        }
        if (!empty($data['data']['folga']) and $descontoSaida < 0) {
            $descontoSaida = 0;
        }
        if (!empty($data['data']['folga']) and $descontoEntrada2 < 0) {
            $descontoEntrada2 = 0;
        }
        if (!empty($data['data']['folga']) and $descontoSaida2 < 0) {
            $descontoSaida2 = 0;
        }
        if (!empty($data['data']['folga']) and $descontoEntrada3 < 0) {
            $descontoEntrada3 = 0;
        }
        if (!empty($data['data']['folga']) and $descontoSaida3 < 0) {
            $descontoSaida3 = 0;
        }
        if (!empty($data['data']['folga']) and !empty($data['data']['horario_saida'])) {
            $this->flagSaldoBancoHoras = timeToSec($data['data']['horario_saida']) - timeToSec($data['data']['horario_entrada']);
            if (!empty($data['data']['tipo_evento'])) {
                $data['data']['saldo_banco_horas'] = secToTime($this->flagSaldoBancoHoras);
                $data['data']['saida_banco_horas'] = null;
            }
            if (!empty($data['data']['tipo_evento_2']) and !empty($data['data']['horario_saida_2'])) {
                $data['data']['saldo_banco_horas_2'] = secToTime($data['data']['horario_saida_2']) - timeToSec($data['data']['horario_entrada_2']);
                $data['data']['saida_banco_horas_2'] = null;
            }
            if (!empty($data['data']['tipo_evento_3']) and !empty($data['data']['horario_saida_3'])) {
                $data['data']['saldo_banco_horas_3'] = secToTime($data['data']['horario_saida_3']) - timeToSec($data['data']['horario_entrada_3']);
                $data['data']['saida_banco_horas_3'] = null;
            }
        } else {
            $this->flagSaldoBancoHoras = $descontoEntrada + $descontoSaida + $descontoEntrada2 + $descontoSaida2 + $descontoEntrada3 + $descontoSaida3;
        }

        return $data;
    }

    //--------------------------------------------------------------------

    private function calcularHorasDescontadas($horarioSalvo, $novoHorario = null, $inverter = false, $horarioAnterior = null): int
    {
        $segundosSalvos = timeToSec($horarioSalvo);
        $segundosNovos = timeToSec($novoHorario ?? $horarioSalvo);
        if ($segundosNovos < $segundosSalvos and $segundosNovos < timeToSec($horarioAnterior)) {
            $segundosNovos += 86400;
        }

        if ($inverter) {
            return $segundosNovos - $segundosSalvos;
        }

        return $segundosSalvos - $segundosNovos;
    }

    //--------------------------------------------------------------------

    protected function atualizarDescontoFolhaMensal($data)
    {
        if ($data['result']) {
            $row = $this->db
                ->select('a.id_alocado, a.desconto_folha, b.desconto_folha AS desconto_folha_mes', false)
                ->join('icom_alocados b', 'b.id = a.id_alocado')
                ->where_in('a.id', $data['id'])
                ->where_in('a.tipo_evento', ['DL', 'FN', 'FJ', 'FC', 'FA', 'AJ', 'AS', 'EA', 'EX1', 'SJ', 'SN', 'SP', 'AC'])
                ->group_by('a.id')
                ->get('icom_apontamentos a')
                ->row();

            if ($row) {
                $this->load->helper('time');

                $descontoAnterior = timeToSec($row->desconto_folha_mes ?? 0) - timeToSec($data['data']['desconto_folha'] ?? 0) + timeToSec($row->desconto_folha ?? 0);

                $this->db->update('icom_alocados', ['desconto_folha' => secToTime($descontoAnterior)], ['id' => $row->id_alocado]);
            }
        }

        return $data;
    }

    //--------------------------------------------------------------------

    protected function limparDescontoFolhaMensal($data)
    {
        if (!empty($data['id'])) {
            $row = $this->db
                ->select('a.id_alocado, b.id_usuario, a.data, COUNT(c.id) AS total_eventos, a.desconto_folha, b.desconto_folha AS desconto_folha_mes', false)
                ->join('icom_alocados b', 'b.id = a.id_alocado')
                ->join('icom_apontamentos c', 'c.id_alocado = b.id AND c.id != a.id', 'left')
                ->where_in('a.id', $data['id'])
                ->where_in('a.tipo_evento', ['DL', 'FN', 'FJ', 'FC', 'FA', 'AJ', 'AS', 'EA', 'EX1', 'SJ', 'SN', 'SP', 'AC'])
                ->group_by('a.id')
                ->get('icom_apontamentos a')
                ->row();

            if ($row) {
                $this->load->helper('time');

                $descontoAnterior = timeToSec($row->desconto_folha_mes ?? 0) - timeToSec($row->desconto_folha ?? 0);

                $this->db->update('icom_alocados', ['desconto_folha' => secToTime($descontoAnterior)], ['id' => $row->id_alocado]);

                $apontamentoWebAnterior = $this->db
                    ->select('a.*, b.flag_ultimo_apontamento, b.data_hora_ultimo_apontamento', false)
                    ->join('usuarios b', 'b.id = a.id_usuario')
                    ->where('a.id_usuario', $row->id_usuario)
                    ->where("DATE_FORMAT(a.data_hora, '%Y-%m-%d') <=", $row->data)
                    ->order_by('a.data_hora', 'desc')
                    ->get('usuarios_apontamentos_horas_2 a')
                    ->row();

                if (!empty($apontamentoWebAnterior->data_hora_entrada) and !empty($apontamentoWebAnterior->data_hora_saida) == false) {
                    $tipoEvento = $apontamentoWebAnterior->tipo_evento_entrada ?? null;
                    $dataHora = $apontamentoWebAnterior->data_hora_entrada ?? null;
                } else {
                    $tipoEvento = $apontamentoWebAnterior->tipo_evento_saida ?? 'S';
                    $dataHora = $apontamentoWebAnterior->data_hora_saida ?? null;
                }

                $qb = $this->db
                    ->set('flag_ultimo_apontamento', $tipoEvento);
                if ($dataHora) {
                    $qb->set('data_hora_ultimo_apontamento', $dataHora);
                }
                $qb->where('id', $row->id_usuario)
                    ->update('usuarios');
            }
        }

        return $data;
    }

    //--------------------------------------------------------------------

    protected function vincularSaldoHorasAtual($data)
    {
        if ($data['result'] and in_array($data['data']['tipo_evento'] ?? '', self::$eventosBancoHoras)) {
            $id = $data['id'];
            if (!is_array($id)) {
                $id = [$id];
            }

            $row = $this->db
                ->select('b.id_usuario, c.banco_horas_icom, c.banco_horas_icom_2', false)
                ->join('icom_alocados b', 'b.id = a.id_alocado')
                ->join('usuarios c', 'c.id = b.id_usuario')
                ->where_in('a.id', $id)
                ->where_in('tipo_evento', self::$eventosBancoHoras)
                ->get('icom_apontamentos a')
                ->row();

            if ($row) {
                $this->load->helper('time');

                $bancoHoras = timeToSec($row->banco_horas_icom) + ($this->flagSaldoBancoHoras ?? 0);

                $this->db->update('usuarios', ['banco_horas_icom' => secToTime($bancoHoras)], ['id' => $row->id_usuario]);
            }
        }

        return $data;
    }

    //--------------------------------------------------------------------

    protected function salvarApontamentoWeb($data)
    {
        if ($data['result'] and !empty($data['data'])) {
            $id = $data['id'];
            if (!is_array($id)) {
                $id = [$id];
            }

            $row = $this->db
                ->select('b.id_usuario, c.banco_horas_icom AS banco_horas', false)
                ->select('a.modo_acesso AS modo_cadastramento', false)
                ->select('a.tipo_evento AS turno_evento, d.horas_dia, d.minutos_descanso_dia, d.categoria', false)
                ->select("b.desconto_folha AS descontos_folha", false)
                ->select("a.horario_entrada, a.horario_saida, a.horario_fracionado", false)
                ->select("a.horario_entrada_especial, a.horario_saida_especial", false)
                ->select("a.qtde_horas_diarias, a.qtde_minutos_folga", false)
                ->select("a.horario_entrada_especial_2, a.horario_saida_especial_2", false)
                ->select("a.qtde_horas_diarias_2, a.qtde_minutos_folga_2", false)
                ->select("a.horario_entrada_especial_3, a.horario_saida_especial_3", false)
                ->select("a.qtde_horas_diarias_3, a.qtde_minutos_folga_3", false)
                ->select("a.desconto_folha, a.desconto_folha_saida", false)
                ->select("a.saldo_banco_horas, a.saida_banco_horas", false)
                ->select("(CASE WHEN a.tipo_evento IN ('FO', 'FR', 'DL') THEN NULL 
								WHEN a.tipo_evento IN ('EA', 'AS', 'CO', 'BH', 'PN') THEN a.saldo_banco_horas 
								WHEN a.tipo_evento IN ('SP','SN') THEN a.saida_banco_horas 
								WHEN a.tipo_evento = 'SJ' THEN  a.desconto_folha_saida 
								ELSE a.desconto_folha END) AS saldo_horas", false)
                ->select('c.id_depto, c.id_area, c.id_setor', false)
                ->join('icom_alocados b', 'b.id = a.id_alocado')
                ->join('usuarios c', 'c.id = b.id_usuario')
                ->join('icom_postos d', 'd.id_usuario = b.id_usuario', 'left')
                ->where_in('a.id', $id)
                ->where_in('a.tipo_evento', self::$eventosApontamentoWeb)
                ->group_by('a.id')
                ->get($this->table . ' a')
                ->row_array();

            if (empty($row)) {
                return $data;
            }

            $categoriaPosto = null;
            if (!empty($data['data']['horario_fracionado']) and ($data['data']['horario_entrada_3'] or $data['data']['horario_saida_3'])) {
                $horasDia = $data['data']['qtde_horas_diarias_3'] ?? null;
                $minutosDescansoDia = $data['data']['qtde_minutos_folga_3'] ?? null;
            } elseif (!empty($data['data']['horario_fracionado']) and ($data['data']['horario_entrada_2'] or $data['data']['horario_saida_2'])) {
                $horasDia = $data['data']['qtde_horas_diarias_2'] ?? null;
                $minutosDescansoDia = $data['data']['qtde_minutos_folga_2'] ?? null;
            } elseif (!empty($data['data']['horario_fracionado']) and ($data['data']['horario_entrada'] or $data['data']['horario_saida'])) {
                $horasDia = $data['data']['qtde_horas_diarias'] ?? null;
                $minutosDescansoDia = $data['data']['qtde_minutos_folga'] ?? null;
            } else {
                $categoriaPosto = $row['categoria'];
                $horasDia = $row['horas_dia'];
                $minutosDescansoDia = $row['minutos_descanso_dia'];
            }

            unset($row['horas_dia']);
            unset($row['minutos_descanso_dia']);
            unset($row['categoria']);

            $apontamentoWebAnterior = $this->db
                ->select('id, turno_evento, modo_cadastramento, justificativa')
                ->where('id_usuario', $row['id_usuario'])
                ->where("DATE_FORMAT(data_hora, '%Y-%m-%d') <=", $data['data']['data'])
                ->order_by('data_hora', 'desc')
                ->get('usuarios_apontamentos_horas')
                ->row();

            $row['modo_cadastramento'] = $data['data']['modo_acesso'];
            if (!empty($apontamentoWebAnterior->justificativa) == false) {
                $row['justificativa'] = $data['data']['observacoes'];
            }
            if (isset($apontamentoWebAnterior->turno_evento) and $row['turno_evento'] == 'AC') {
                $row['turno_evento'] = $apontamentoWebAnterior->turno_evento;
            }
            $turno_evento = $this->input->post('turno_evento');
            $tipo_horario_especial = $this->input->post('tipo_horario_especial');
            $tipo_evento_entrada = null;
            if ($data['data']['modo_acesso'] == 'A' or $turno_evento) {
                if ($turno_evento) {
                    if ($tipo_horario_especial == 'HE') {
                        $tipo_evento_entrada = in_array($turno_evento, self::$eventosSaida) ? 'SE' : 'EE';
                    } elseif ($tipo_horario_especial == 'EX') {
                        $tipo_evento_entrada = in_array($turno_evento, self::$eventosSaida) ? 'SX' : 'E';
                    } elseif ($tipo_horario_especial == 'HF') {
                        $tipo_evento_entrada = in_array($turno_evento, self::$eventosSaida) ? 'SF' : 'EF';
                    }
                    $row['turno_evento'] = $turno_evento;
                } elseif ($row['turno_evento'] == 'EX' or $turno_evento == 'EX') {
                    $row['turno_evento'] = 'X';
                } elseif ($row['turno_evento'] == 'FO') {
                    $row['turno_evento'] = 'FO';
                } elseif ($row['turno_evento'] == 'FR') {
                    $row['turno_evento'] = 'FR';
                } elseif ($row['turno_evento'] == 'DL') {
                    $row['turno_evento'] = 'DL';
                } elseif ($row['turno_evento'] == 'PN') {
                    $row['turno_evento'] = 'E';
                } elseif (in_array($row['turno_evento'], self::$eventosCompensacao)) {
                    $row['turno_evento'] = 'C';
                } elseif (in_array($row['turno_evento'], self::$eventosPresenciais)) {
                    $row['turno_evento'] = 'F';
                } elseif (in_array($row['turno_evento'], self::$eventosEntrada)) {
                    $row['turno_evento'] = 'E';
                } elseif (in_array($row['turno_evento'], self::$eventosSaida)) {
                    $row['turno_evento'] = 'S';
                } else {
                    if ($row['horario_saida'] or $row['desconto_folha'] or $row['desconto_folha_saida']) {
                        $row['turno_evento'] = 'S';
                    } elseif ($row['saldo_banco_horas'] or $row['saida_banco_horas']) {
                        $row['turno_evento'] = 'E';
                    } else {
                        $row['turno_evento'] = 'E';
                    }
                }
            } else {
                if (!empty($data['data']['horario_fracionado']) and ($data['data']['horario_entrada_3'] or $data['data']['horario_saida_3'])) {
                    $row['turno_evento'] = in_array($data['data']['tipo_evento_3'], self::$eventosSaida) ? 'S' : 'E';
                } elseif (!empty($data['data']['horario_fracionado']) and ($data['data']['horario_entrada_2'] or $data['data']['horario_saida_2'])) {
                    $row['turno_evento'] = in_array($data['data']['tipo_evento_2'], self::$eventosSaida) ? 'S' : 'E';
                } elseif (!empty($data['data']['horario_fracionado']) and ($data['data']['horario_entrada'] or $data['data']['horario_saida'])) {
                    $row['turno_evento'] = in_array($data['data']['tipo_evento'], self::$eventosSaida) ? 'S' : 'E';
                } elseif ($data['data']['tipo_evento'] == 'FO') {
                    $row['turno_evento'] = 'FO';
                } elseif ($data['data']['tipo_evento'] == 'FR') {
                    $row['turno_evento'] = 'FR';
                } elseif ($data['data']['tipo_evento'] == 'DL') {
                    $row['turno_evento'] = 'DL';
                } elseif ($data['data']['tipo_evento'] == 'FN') {
                    $row['turno_evento'] = 'F';
                } elseif ($data['data']['tipo_evento'] == 'FA') {
                    $row['turno_evento'] = 'B';
                } elseif ($data['data']['tipo_evento'] == 'FJ') {
                    $row['turno_evento'] = 'J';
                } elseif ($data['data']['tipo_evento'] == 'FC') {
                    $row['turno_evento'] = 'A';
                } else {
                    if ($tipo_horario_especial == 'HE') {
                        $tipo_evento_entrada = in_array($data['data']['tipo_evento'], self::$eventosSaida) ? 'SE' : 'EE';
                    } elseif ($tipo_horario_especial == 'EX') {
                        $tipo_evento_entrada = in_array($data['data']['tipo_evento'], self::$eventosSaida) ? 'SX' : 'E';
                    } elseif ($tipo_horario_especial == 'HF') {
                        $tipo_evento_entrada = in_array($data['data']['tipo_evento'], self::$eventosSaida) ? 'SF' : 'EF';
                    }
                    $row['turno_evento'] = in_array($data['data']['tipo_evento'], self::$eventosSaida) ? 'S' : 'E';
                }
            }
            $turno_evento = $row['turno_evento'] ?? $turno_evento;

            if (!empty($data['data']['data'])) {
                if ($data['data']['modo_acesso'] == 'M') {

                    if (!empty($data['data']['horario_fracionado']) and ($data['data']['horario_entrada_3'] or $data['data']['horario_saida_3'])) {
                        $hora = in_array($data['data']['tipo_evento_3'], self::$eventosSaida) ? $data['data']['horario_saida_3'] : $data['data']['horario_entrada_3'];
                    } elseif (!empty($data['data']['horario_fracionado']) and ($data['data']['horario_entrada_2'] or $data['data']['horario_saida_2'])) {
                        $hora = in_array($data['data']['tipo_evento_2'], self::$eventosSaida) ? $data['data']['horario_saida_2'] : $data['data']['horario_entrada_2'];
                    } elseif (!empty($data['data']['horario_fracionado']) and ($data['data']['horario_entrada'] or $data['data']['horario_saida'])) {
                        $hora = in_array($data['data']['tipo_evento'], self::$eventosSaida) ? $data['data']['horario_saida'] : $data['data']['horario_entrada'];
                    } elseif (($data['data']['tipo_evento'] === 'FO' and $row['turno_evento'] == 'FO') or
                        ($data['data']['tipo_evento'] === 'FR' and $row['turno_evento'] == 'FR') or
                        ($data['data']['tipo_evento'] === 'DL' and $row['turno_evento'] == 'DL')) {
                        $row['saldo_horas'] = null;
                        $row['banco_horas'] = null;
                        $hora = in_array($data['data']['tipo_evento'], self::$eventosSaida) ? $data['data']['horario_saida'] : $data['data']['horario_entrada'];
                    } else {
                        $hora = in_array($data['data']['tipo_evento'], self::$eventosSaida) ? $data['data']['horario_saida'] : $data['data']['horario_entrada'] ?? '00:00:00';
                    }
                } else {
                    $hora = date('H:i:s');
                }
                $row['data_hora'] = $data['data']['data'] . ' ' . $hora;
            } else {
                $row['data_hora'] = date('Y-m-d H:i:s');
            }

            unset($row['horario_fracionado']);
            unset($row['horario_entrada']);
            unset($row['horario_entrada_especial']);
            unset($row['horario_entrada_especial_2']);
            unset($row['horario_entrada_especial_3']);
            unset($row['horario_saida']);
            unset($row['horario_saida_especial']);
            unset($row['horario_saida_especial_2']);
            unset($row['horario_saida_especial_3']);
            unset($row['qtde_horas_diarias']);
            unset($row['qtde_horas_diarias_2']);
            unset($row['qtde_horas_diarias_3']);
            unset($row['qtde_minutos_folga']);
            unset($row['qtde_minutos_folga_2']);
            unset($row['qtde_minutos_folga_3']);
            unset($row['desconto_folha']);
            unset($row['desconto_folha_saida']);
            unset($row['saldo_banco_horas']);
            unset($row['saida_banco_horas']);

            $update2 = false;
            if (!empty($apontamentoWebAnterior->id) and
                ($apontamentoWebAnterior->id_usuario ?? null) == $row['id_usuario'] and
                ($apontamentoWebAnterior->data_hora ?? null) == $row['data_hora'] and
                ($apontamentoWebAnterior->turno_evento ?? null) == $row['turno_evento']) {
                $update2 = true;
                $idRetorno = $apontamentoWebAnterior->id;
            } else {
                $idRetorno = $this->db->insert_id();
            }

            // -------------------------------------------------------

            if ($data['data']['horario_entrada_3'] or $data['data']['horario_saida_3']) {
                $numeroTurno = 3;
                $horarioSaida = $data['data']['horario_saida_3'];
            } elseif ($data['data']['horario_entrada_2'] or $data['data']['horario_saida_2']) {
                $numeroTurno = 2;
                $horarioSaida = $data['data']['horario_saida_2'];
            } elseif ($data['data']['horario_entrada'] or $data['data']['horario_saida']) {
                $numeroTurno = 1;
                $horarioSaida = $data['data']['horario_saida'];
            } else {
                $numeroTurno = null;
                $horarioSaida = $data['data']['horario_saida'];
            }

            $qb = $this->db
                ->where('id_usuario', $row['id_usuario'])
                ->where('data_hora IS NOT NULL');
            if (in_array($turno_evento, ['E', 'S'])) {
                $qb->where('DATE(data_hora) <=', $data['data']['data']);
            } else {
                $qb->where('DATE(data_hora) =', $data['data']['data']);
            }
            $apontamentoWebAnterior2 = $qb
                ->order_by('data_hora', 'desc')
                ->get('usuarios_apontamentos_horas_2')
                ->row();

            $idApontamentoWebAnterior2 = null;
            if (isset($apontamentoWebAnterior2) and in_array($turno_evento, ['S', 'F', 'J', 'A', 'B', 'FO', 'FR', 'DL'])) {
                if ($apontamentoWebAnterior2->numero_turno == $numeroTurno) {
                    $idApontamentoWebAnterior2 = $apontamentoWebAnterior2->id;
                }
            }

            $apontamentoWeb2 = $this->db
                ->select('a.id AS id_original, a.banco_horas_icom_2, b.*', false)
                ->join('usuarios_apontamentos_horas_2 b', "b.id_usuario = a.id AND b.id = '$idApontamentoWebAnterior2'", 'left')
                ->where('a.id', $row['id_usuario'])
                ->order_by('b.data_hora', 'desc')
                ->get('usuarios a')
                ->row_array();

            $bancoHorasIcom2 = $apontamentoWeb2['banco_horas_icom_2'];
            $bancoHorasIcom2Anterior = $bancoHorasIcom2;
            unset($apontamentoWeb2['id_original']);
            unset($apontamentoWeb2['banco_horas_icom_2']);

            $possuiTurnoEvento = $this->input->post('turno_evento');

            if (empty($apontamentoWeb2['id'])) {
                $apontamentoWeb2 = [
                    'id' => null,
                    'id_usuario' => $row['id_usuario'],
                    'id_old' => $idRetorno,
                    'turno_evento' => $turno_evento,
                    'numero_turno' => $numeroTurno,
                    'data_hora' => $row['data_hora'],
                    'data_hora_entrada' => $row['data_hora'],
                    'modo_automatico' => ($possuiTurnoEvento ? $this->input->post('modo_cadastramento') !== 'M' : $row['modo_cadastramento'] === 'A') ? 1 : null,
                    'descontos_folha' => $row['descontos_folha'],
                    'id_depto' => $row['id_depto'],
                    'id_area' => $row['id_area'],
                    'id_setor' => $row['id_setor'],
                    'justificativa' => $row['justificativa'],
                    'saldo_horas' => null,
                    'saldo_horas_2' => null,
                    'banco_horas' => null,
                ];
            }

            $segundosTolerancia = 360;

            $horaExtraSaida = $turno_evento == 'S' && $data['data']['tipo_evento'] === 'EX';

            if ($turno_evento == 'S' or $horaExtraSaida) {
                $apontamentoWeb2['saida_automatica'] = ($possuiTurnoEvento ? $this->input->post('modo_cadastramento') !== 'M' : $row['modo_cadastramento'] === 'A') ? 1 : null;
                $apontamentoWeb2['data_hora_saida'] = $data['data']['data'] . ' ' . $horarioSaida;//159
                $apontamentoWeb2['tipo_evento_saida'] = $turno_evento;
                if ($apontamentoWeb2['id']) {
                    $minutosDescansoDia = date('H:i:s', strtotime($minutosDescansoDia ?? 0));

                    if (!empty($data['data']['horario_fracionado'])) {
                        $apontamentoAnterior = $this
                            ->where('id', $data['id'])
                            ->first();

                        $saldoHoras2 = 0;
                        switch ($this->input->post('numero_turno')) {
                            case '3':
                                $saldoHoras2 -= timeToSec($apontamentoAnterior->saldo_banco_horas_3 ?? 0) + timeToSec($apontamentoAnterior->saida_banco_horas_3 ?? 0);
                                $saldoHoras2 += timeToSec($data['data']['saldo_banco_horas_3']) + timeToSec($data['data']['saida_banco_horas_3']);
                                break;
                            case '2':
                                $saldoHoras2 -= timeToSec($apontamentoAnterior->saldo_banco_horas_2 ?? 0) + timeToSec($apontamentoAnterior->saida_banco_horas_2 ?? 0);
                                $saldoHoras2 += timeToSec($data['data']['saldo_banco_horas_2']) + timeToSec($data['data']['saida_banco_horas_2']);
                                break;
                            default:
                                $saldoHoras2 -= timeToSec($apontamentoAnterior->saldo_banco_horas ?? 0) + timeToSec($apontamentoAnterior->saida_banco_horas ?? 0);
                                $saldoHoras2 += timeToSec($data['data']['saldo_banco_horas']) + timeToSec($data['data']['saida_banco_horas']);
                        }
                    } else {

                        if (!empty($apontamentoWeb2['data_hora_entrada']) and !empty($apontamentoWeb2['data_hora_saida'])) {
                            $sqlSaldoHoras2 = $this->db
                                ->query("SELECT TIMESTAMPDIFF(SECOND, '{$apontamentoWeb2['data_hora_entrada']}', '{$apontamentoWeb2['data_hora_saida']}') AS secs")
                                ->row();

                            $saldoHoras2 = $sqlSaldoHoras2->secs;
                        } else {
                            $saldoHoras2 = timeToSec($apontamentoWeb2['data_hora_saida'] ?? 0) - timeToSec($apontamentoWeb2['data_hora_entrada'] ?? 0);
                        }
                        $saldoHoras2 = $saldoHoras2 - timeToSec($horasDia);
                    }

                    if (empty($saldoHoras2) or ($saldoHoras2 < $segundosTolerancia and $saldoHoras2 > ($segundosTolerancia * (-1)))) {
                        $saldoHoras2 = 0;
                    }

                    if ($data['data']['tipo_evento'] === 'EX') {
                        $apontamentoWeb2['tipo_evento_saida'] = 'SX';
                        $saldoHoras2 = $sqlSaldoHoras2->secs ?? (($apontamentoWeb2['data_hora_saida'] ?? 0) - ($apontamentoWeb2['data_hora_entrada'] ?? 0));
                    }
                    $bancoHorasIcom2 = secToTime(timeToSec($bancoHorasIcom2) + $saldoHoras2); // Se hora extra incluir horarios do posto

                    $apontamentoWeb2['saldo_horas'] = secToTime($saldoHoras2 - timeToSec($minutosDescansoDia));
                    $apontamentoWeb2['saldo_horas_2'] = secToTime($saldoHoras2);
                    $apontamentoWeb2['banco_horas'] = $bancoHorasIcom2;

                    unset($apontamentoWeb2['data_hora']);
                    unset($apontamentoWeb2['data_hora_entrada']);
                    unset($apontamentoWeb2['numero_turno']);
                    unset($apontamentoWeb2['modo_automatico']);
                    if (in_array($data['data']['tipo_evento'], ['FO', 'FR', 'DL'])) {
                        $apontamentoWeb2['data_hora_saida'] = $data['data']['data'] . ' 0:00:00';
                        $apontamentoWeb2['tipo_evento_saida'] = $data['data']['tipo_evento'];
                        $apontamentoWeb2['saldo_horas'] = '0:00:00';
                        $apontamentoWeb2['saldo_horas_2'] = '0:00:00';
                        $apontamentoWeb2['banco_horas'] = $bancoHorasIcom2Anterior;
                        $apontamentoWeb2['descontos_folha'] = $data['data']['tipo_evento'] == 'DL' ? $row['descontos_folha'] : '0:00:00';
                    } elseif ($data['data']['tipo_evento'] !== 'EX') {
                        unset($apontamentoWeb2['turno_evento']);
                    }
                }
            } elseif (in_array($turno_evento, ['F', 'J', 'A', 'B'])) {
                $saldoHoras2 = (timeToSec($horasDia) - timeToSec($minutosDescansoDia)) * (-1);
                if (empty($saldoHoras2) or ($saldoHoras2 < $segundosTolerancia and $saldoHoras2 > ($segundosTolerancia * (-1)))) {
                    $saldoHoras2 = 0;
                }
                if ($turno_evento === 'A') {
                    $bancoHorasIcom2 = secToTime(timeToSec($bancoHorasIcom2) + $saldoHoras2);
                }
                if ($turno_evento === 'B') {
                    $apontamentoWeb2['saldo_horas_2'] = secToTime(0);
                } else {
                    $apontamentoWeb2['saldo_horas_2'] = secToTime($saldoHoras2);
                }

                $apontamentoWeb2['tipo_evento_entrada'] = $tipo_evento_entrada ?? $turno_evento;
                $apontamentoWeb2['saldo_horas'] = $data['data']['saldo_banco_horas'];
                $apontamentoWeb2['banco_horas'] = $bancoHorasIcom2;

                if (!($turno_evento === 'F' or ($turno_evento === 'J' and $categoriaPosto === 'MEI'))) {
                    $apontamentoWeb2['descontos_folha'] = secToTime(0);
                }
            } else {
                $apontamentoWeb2['data_hora_entrada'] = $row['data_hora'];
                $apontamentoWeb2['entrada_automatica'] = $row['modo_cadastramento'] === 'A' ? 1 : null;
                $apontamentoWeb2['tipo_evento_entrada'] = $tipo_evento_entrada ?? $turno_evento;
                if ($data['data']['tipo_evento'] === 'EX') {
                    $apontamentoWeb2['turno_evento'] = 'EX';
                    $apontamentoWeb2['tipo_evento_entrada'] = 'EX';
                } else if (in_array($data['data']['tipo_evento'], ['FO', 'FR', 'DL'])) {
                    $apontamentoWeb2['turno_evento'] = $data['data']['tipo_evento'];
                    $apontamentoWeb2['data_hora_entrada'] = $data['data']['data'] . ' 0:00:00';
                    $apontamentoWeb2['data_hora_saida'] = $data['data']['data'] . ' 0:00:00';
                    $apontamentoWeb2['tipo_evento_entrada'] = $data['data']['tipo_evento'];
                    $apontamentoWeb2['tipo_evento_saida'] = $data['data']['tipo_evento'];
                    $apontamentoWeb2['saldo_horas'] = '0:00:00';
                    $apontamentoWeb2['saldo_horas_2'] = '0:00:00';
                    $apontamentoWeb2['banco_horas'] = $bancoHorasIcom2Anterior;
                    $apontamentoWeb2['descontos_folha'] = $data['data']['tipo_evento'] == 'DL' ? $row['descontos_folha'] : '0:00:00';
                }
            }

            if ($apontamentoWeb2['id']) {
                $this->db->update('usuarios_apontamentos_horas_2', $apontamentoWeb2, ['id' => $apontamentoWeb2['id']]);
            } elseif (in_array($turno_evento, ['F', 'J', 'A', 'B', 'X', 'FO', 'FR', 'DL']) or $numeroTurno) {
                $this->db->insert('usuarios_apontamentos_horas_2', $apontamentoWeb2);
            }

            if (in_array($turno_evento, ['S', 'F', 'J', 'A', 'B', 'X'])) {
                $this->db->update('usuarios', ['banco_horas_icom_2' => $bancoHorasIcom2], ['id' => $apontamentoWeb2['id_usuario']]);
            }

            $this->db->update('icom_postos', ['tipo_ultimo_evento' => $turno_evento], ['id_usuario' => $apontamentoWeb2['id_usuario']]);
        }

        $this->flagSaldoBancoHoras = null;
        $this->flagIdAlocado = null;

        return $data;
    }

    //--------------------------------------------------------------------

    public function deleteEntrada($id = null, $turno = '')
    {
        if (!empty($id) && is_numeric($id)) {
            $id = [$id];
        }

        $this->load->helper('time');

        $this->db->trans_start();

        switch ($turno) {
            case 'N':
                $apontamento = $this->db
                    ->select('a.id_alocado, a.data, a.desconto_folha_3 AS desconto_entrada_3, a.horario_entrada_3')
                    ->select('b.id_usuario, b.desconto_folha, a.tipo_evento_saida_3')
                    ->select('a.saldo_banco_horas_3, c.banco_horas_icom')
                    ->join('icom_alocados b', 'b.id = a.id_alocado')
                    ->join('usuarios c', 'c.id = b.id_usuario')
                    ->where_in('a.id', $id)
                    ->get('icom_apontamentos a')
                    ->row();

                $this->db
                    ->set('tipo_evento_3', $apontamento->tipo_evento_saida_3 ?? 'PN')
                    ->set('tipo_evento_entrada_3', null)
                    ->set('horario_entrada_3', null)
                    ->set('horario_intervalo_3', null)
                    ->set('desconto_folha_3', null)
                    ->set('saldo_banco_horas_3', null)
                    ->where_in('id', $id)
                    ->update('icom_apontamentos');

                $horarioEntrada = $apontamento->horario_entrada_3;
                $descontoAtual = secToTime(timeToSec($apontamento->desconto_folha) - timeToSec($apontamento->desconto_entrada_3));
                $saldoBancoHoras = timeToSec($apontamento->saldo_banco_horas_3);
                break;
            case 'T':
                $apontamento = $this->db
                    ->select('a.id_alocado, a.data, a.desconto_folha_2 AS desconto_entrada_2, a.horario_entrada_2')
                    ->select('b.id_usuario, b.desconto_folha, a.tipo_evento_saida_2')
                    ->select('a.saldo_banco_horas_2, c.banco_horas_icom')
                    ->join('icom_alocados b', 'b.id = a.id_alocado')
                    ->join('usuarios c', 'c.id = b.id_usuario')
                    ->where_in('a.id', $id)
                    ->get('icom_apontamentos a')
                    ->row();

                $this->db
                    ->set('tipo_evento_2', $apontamento->tipo_evento_saida_2 ?? 'PN')
                    ->set('tipo_evento_entrada_2', null)
                    ->set('horario_entrada_2', null)
                    ->set('horario_intervalo_2', null)
                    ->set('desconto_folha_2', null)
                    ->set('saldo_banco_horas_2', null)
                    ->where_in('id', $id)
                    ->update('icom_apontamentos');

                $horarioEntrada = $apontamento->horario_entrada_2;
                $descontoAtual = secToTime(timeToSec($apontamento->desconto_folha) - timeToSec($apontamento->desconto_entrada_2));
                $saldoBancoHoras = timeToSec($apontamento->saldo_banco_horas_2);
                break;
            default:
                $apontamento = $this->db
                    ->select('a.id_alocado, a.data, a.desconto_folha AS desconto_entrada')
                    ->select('b.id_usuario, b.desconto_folha, a.tipo_evento_saida, a.horario_entrada, a.horario_saida')
                    ->select('a.saldo_banco_horas, c.banco_horas_icom, a.folga')
                    ->join('icom_alocados b', 'b.id = a.id_alocado')
                    ->join('usuarios c', 'c.id = b.id_usuario')
                    ->where_in('a.id', $id)
                    ->get('icom_apontamentos a')
                    ->row();

                $this->db
                    ->set('tipo_evento', !empty($apontamento->horario_saida) ? 'HF' : 'PN')
                    ->set('tipo_evento_entrada', null)
                    ->set('horario_entrada', null)
                    ->set('horario_intervalo', null)
                    ->set('desconto_folha', null)
                    ->set('saldo_banco_horas', null)
                    ->where_in('id', $id)
                    ->update('icom_apontamentos');

                $horarioEntrada = $apontamento->horario_entrada;
                $descontoAtual = secToTime(timeToSec($apontamento->desconto_folha) - timeToSec($apontamento->desconto_entrada));
                $saldoBancoHoras = timeToSec($apontamento->saldo_banco_horas);
        }

        $this->db
            ->set('desconto_folha', $descontoAtual)
            ->where('id', $apontamento->id_alocado)
            ->update('icom_alocados');

        if ($saldoBancoHoras != 0) {
            $bancoHorasIcom = timeToSec($apontamento->banco_horas_icom);
            $this->db
                ->set('banco_horas_icom_2', secToTime($bancoHorasIcom - $saldoBancoHoras))
                ->where('id', $apontamento->id_usuario)
                ->update('usuarios');
        }

        $this->db
            ->where('id_usuario', $apontamento->id_usuario)
            ->where("DATE_FORMAT(data_hora, '%Y-%m-%d') =", $apontamento->data)
            ->where("DATE_FORMAT(data_hora, '%H:%i') =", timeSimpleFormat($horarioEntrada))
            ->where('turno_evento', 'E')
            ->delete('usuarios_apontamentos_horas');

        return $this->db->trans_status();
    }

    //--------------------------------------------------------------------

    public function deleteSaida($id = null, $turno = ''): bool
    {
        if (!empty($id) && is_numeric($id)) {
            $id = [$id];
        }

        $this->load->helper('time');

        $this->db->trans_start();

        switch ($turno) {
            case 'N':
                $apontamento = $this->db
                    ->select('a.id_alocado, a.data, a.desconto_folha_3 AS desconto_folha_entrada, a.desconto_folha_saida_3')
                    ->select('b.id_usuario, b.desconto_folha, a.tipo_evento_entrada_3, a.horario_saida_3')
                    ->select('a.saldo_banco_horas_3 AS saldo_banco_horas, a.saida_banco_horas_3, c.banco_horas_icom')
                    ->join('icom_alocados b', 'b.id = a.id_alocado')
                    ->join('usuarios c', 'c.id = b.id_usuario')
                    ->where_in('a.id', $id)
                    ->get('icom_apontamentos a')
                    ->row();

                $this->db
                    ->set('tipo_evento_3', $apontamento->tipo_evento_entrada_3 ?? 'PN')
                    ->set('tipo_evento_saida_3', null)
                    ->set('horario_retorno_3', null)
                    ->set('horario_saida_3', null)
                    ->set('desconto_folha_saida_3', null)
                    ->set('saida_banco_horas_3', null)
                    ->where_in('id', $id)
                    ->update('icom_apontamentos');

                $horarioSaida = $apontamento->horario_saida_3;
                $descontoAtual = secToTime(timeToSec($apontamento->desconto_folha) - timeToSec($apontamento->desconto_folha_saida_3));
                $saidaBancoHoras = timeToSec($apontamento->saida_banco_horas_3);
                break;
            case 'T':
                $apontamento = $this->db
                    ->select('a.id_alocado, a.data, a.desconto_folha_2 AS desconto_folha_entrada, a.desconto_folha_saida_2')
                    ->select('b.id_usuario, b.desconto_folha, a.tipo_evento_entrada_2, a.horario_saida_2')
                    ->select('a.saldo_banco_horas_2 AS saldo_banco_horas, a.saida_banco_horas_2, c.banco_horas_icom')
                    ->join('icom_alocados b', 'b.id = a.id_alocado')
                    ->join('usuarios c', 'c.id = b.id_usuario')
                    ->where_in('a.id', $id)
                    ->get('icom_apontamentos a')
                    ->row();

                $this->db
                    ->set('tipo_evento_2', $apontamento->tipo_evento_entrada_2 ?? 'PN')
                    ->set('tipo_evento_saida_2', null)
                    ->set('horario_retorno_2', null)
                    ->set('horario_saida_2', null)
                    ->set('desconto_folha_saida_2', null)
                    ->set('saida_banco_horas_2', null)
                    ->where_in('id', $id)
                    ->update('icom_apontamentos');

                $horarioSaida = $apontamento->horario_saida_2;
                $descontoAtual = secToTime(timeToSec($apontamento->desconto_folha) - timeToSec($apontamento->desconto_folha_saida_2));
                $saidaBancoHoras = timeToSec($apontamento->saida_banco_horas_2);
                break;
            default:
                $apontamento = $this->db
                    ->select('a.id_alocado, a.data, a.desconto_folha AS desconto_folha_entrada, a.desconto_folha_saida')
                    ->select('b.id_usuario, b.desconto_folha, a.tipo_evento_entrada, a.horario_saida')
                    ->select('a.saldo_banco_horas, a.saida_banco_horas, c.banco_horas_icom')
                    ->join('icom_alocados b', 'b.id = a.id_alocado')
                    ->join('usuarios c', 'c.id = b.id_usuario')
                    ->where_in('a.id', $id)
                    ->get('icom_apontamentos a')
                    ->row();

                $this->db
                    ->set('tipo_evento', $apontamento->tipo_evento_entrada ?? 'PN')
                    ->set('tipo_evento_saida', null)
                    ->set('horario_retorno', null)
                    ->set('horario_saida', null)
                    ->set('desconto_folha_saida', null)
                    ->set('saida_banco_horas', null)
                    ->where_in('id', $id)
                    ->update('icom_apontamentos');

                $horarioSaida = $apontamento->horario_saida;
                $descontoAtual = secToTime(timeToSec($apontamento->desconto_folha) - timeToSec($apontamento->desconto_folha_saida));
                $saidaBancoHoras = timeToSec($apontamento->saida_banco_horas);
        }

        $this->db
            ->set('desconto_folha', $descontoAtual)
            ->where('id', $apontamento->id_alocado)
            ->update('icom_alocados');

        if ($saidaBancoHoras != 0) {
            $bancoHorasIcom = timeToSec($apontamento->banco_horas_icom);
            $this->db
                ->set('banco_horas_icom_2', secToTime($bancoHorasIcom - $saidaBancoHoras))
                ->where('id', $apontamento->id_usuario)
                ->update('usuarios');
        }

        $this->db
            ->where('id_usuario', $apontamento->id_usuario)
            ->where("DATE_FORMAT(data_hora, '%Y-%m-%d') =", $apontamento->data)
            ->where("DATE_FORMAT(data_hora, '%H:%i') =", timeSimpleFormat($horarioSaida))
            ->where('turno_evento', 'S')
            ->delete('usuarios_apontamentos_horas');

        $this->db->trans_complete();

        return true;
    }

    //--------------------------------------------------------------------

    protected function insertEvento($data)
    {
        $this->load->model('icom_evento_model', 'evento');

        $this->evento->insertByOld($data['id'], $data['data']);

        return $data;
    }

    //--------------------------------------------------------------------

    protected function updateEvento($data)
    {
        $this->load->model('icom_evento_model', 'evento');

        $this->evento->updateByOld($data['id'], $data['data']);

        return $data;
    }

    //--------------------------------------------------------------------

    protected function deleteEvento($data)
    {
        $this->load->model('icom_evento_model', 'evento');

        $this->evento->deleteByOld($data['id']);

        return $data;
    }

    //--------------------------------------------------------------------

    protected function insertApontamentoWeb2($data)
    {
        $this->load->model('usuario_apontamento_horas_2_model', 'apontamentoWeb2');

        $this->apontamentoWeb2->insertByOld($data['id'], $data['data']);

        return $data;
    }

    //--------------------------------------------------------------------

    protected function updateApontamentoWeb2($data)
    {
        $this->load->model('usuario_apontamento_horas_2_model', 'apontamentoWeb2');

        $this->apontamentoWeb2->updateByOld($data['id'], $data['data']);

        return $data;
    }

    //--------------------------------------------------------------------

    protected function deleteApontamentoWeb2($data)
    {
        $this->load->model('usuario_apontamento_horas_2_model', 'apontamentoWeb2');

        $this->apontamentoWeb2->deleteByOld($data['id']);

        return $data;
    }
}
