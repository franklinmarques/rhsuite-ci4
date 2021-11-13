<?php

namespace App\Models;

use App\Entities\IcomEvento;

class IcomEventoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_eventos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomEvento::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_alocado',
        'id_old',
        'tipo_entrada',
        'entrada_automatica',
        'data_entrada',
        'hora_entrada',
        'horario_especial_entrada',
        'desconto_folha_entrada',
        'saldo_horas_entrada',
        'tipo_saida',
        'saida_automatica',
        'data_saida',
        'hora_saida',
        'horario_especial_saida',
        'desconto_folha_saida',
        'saldo_horas_saida',
        'horas_diarias',
        'minutos_folga',
        'acrescimo_folha',
        'observacoes',
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
        'tipo_entrada'              => 'required|string|max_length[2]',
        'entrada_automatica'        => 'integer|exact_length[1]',
        'data_entrada'              => 'required|valid_date',
        'hora_entrada'              => 'valid_time',
        'horario_especial_entrada'  => 'valid_time',
        'desconto_folha_entrada'    => 'valid_time',
        'saldo_horas_entrada'       => 'valid_time',
        'tipo_saida'                => 'string|max_length[2]',
        'saida_automatica'          => 'integer|exact_length[1]',
        'data_saida'                => 'valid_date',
        'hora_saida'                => 'valid_time',
        'horario_especial_saida'    => 'valid_time',
        'desconto_folha_saida'      => 'valid_time',
        'saldo_horas_saida'         => 'valid_time',
        'horas_diarias'             => 'valid_time',
        'minutos_folga'             => 'valid_time',
        'acrescimo_folha'           => 'valid_time',
        'observacoes'               => 'string',
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

    public const TIPOS = [
        'FO' => 'Folga',
        'HE' => 'Horário especial',
        'FR' => 'Férias',
        'DL' => 'Desligamento',
        'HF' => 'Horário fracionado',
        'PN' => 'Presença normal',
        'FJ' => 'Falta com atestado próprio',
        'FN' => 'Falta sem atestado',
        'FC' => 'Falta a compensar',
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
    public const TIPOS_ENTRADA = [
        'FO' => 'Folga',
        'HE' => 'Horário especial',
        'FR' => 'Férias',
        'DL' => 'Desligamento',
        'HF' => 'Horário fracionado',
        'PN' => 'Presença normal',
        'FJ' => 'Falta com atestado próprio',
        'FN' => 'Falta sem atestado',
        'FC' => 'Falta a compensar',
        'BH' => 'Compensação banco de horas',
        'AC' => 'Atraso/saída antecipada a compensar',
        'AJ' => 'Atraso com atestado próprio',
        'AS' => 'Atraso sem atestado',
        'CO' => 'Compensação (Trabalho dia de folga - MEI)',
        'EA' => 'Entrada antecipada',
        'EX' => 'Hora extra (Trabalho dia de feriado - CLT)',
    ];
    public const TIPOS_SAIDA = [
        'HE' => 'Horário especial',
        'BH' => 'Compensação banco de horas',
        'SP' => 'Saída pós-horário',
        'CO' => 'Compensação (Trabalho dia de folga - MEI)',
        'EA' => 'Entrada antecipada',
        'SJ' => 'Saída antecipada com atestado próprio',
        'SN' => 'Saída antecipada sem atestado',
    ];

    //--------------------------------------------------------------------

    protected function calculaSaldoHoras($data)
    {
        if (!empty($data['data']) == false) {
            return $data;
        }

        $flagEntradaSaida = '';
        $flagMultiplicadorSaldo = 0;
        $possuiDescontoFolha = false;
        $possuiSaldoBancoHoras = false;

        if (in_array($data['data']['turno_evento'], [])) {
            $flagEntradaSaida = 'E';
        } elseif (in_array($data['data']['turno_evento'], [])) {
            $flagEntradaSaida = 'S';
        }

        if (in_array($data['data']['turno_evento'], [])) {
            $flagMultiplicadorSaldo = 1;
        } elseif (in_array($data['data']['turno_evento'], [])) {
            $flagMultiplicadorSaldo = (-1);
        }

        if (in_array($data['data']['turno_evento'], [])) {
            $possuiDescontoFolha = true;
        }
        if (in_array($data['data']['turno_evento'], [])) {
            $possuiSaldoBancoHoras = true;
        }

        $row = $this->db
            ->select('a.id_alocado, a.desconto_folha_entrada', false)
            ->select('a.desconto_folha_saida b.desconto_folha', false)
            ->join('icom_alocados b', 'b.id = a.id_alocado')
            ->where_in('a.id', $data['id'])
            ->group_start()
            ->where_in('a.tipo_entrada', ['DL', 'FN', 'FJ', 'FC', 'AJ', 'AS', 'EA', 'EX', 'AC'])
            ->or_where_in('a.tipo_saida', ['SJ', 'SN', 'SP', 'AC'])
            ->group_end()
            ->group_by('a.id')
            ->get('icom_eventos a')
            ->row();

        if ($row) {
            $this->load->helper('time');

            $descontoAnterior = timeToSec($row->desconto_folha ?? 0) - timeToSec($data['data']['desconto_folha_entrada'] ?? 0) + timeToSec($row->desconto_folha_entrada ?? 0);

            $this->db->update('icom_alocados', ['desconto_folha' => secToTime($descontoAnterior)], ['id' => $row->id_alocado]);
        }

        return $data;
    }

    //--------------------------------------------------------------------

    protected function setDescontoFolha($data)
    {
        if (!empty($data['data']) == false) {
            return $data;
        }

        $row = $this->db
            ->select('a.id_alocado, a.desconto_folha_entrada', false)
            ->select('a.desconto_folha_saida b.desconto_folha', false)
            ->join('icom_alocados b', 'b.id = a.id_alocado')
            ->where_in('a.id', $data['id'])
            ->group_start()
            ->where_in('a.tipo_entrada', ['DL', 'FN', 'FJ', 'FC', 'AJ', 'AS', 'EA', 'EX', 'AC'])
            ->or_where_in('a.tipo_saida', ['SJ', 'SN', 'SP', 'AC'])
            ->group_end()
            ->group_by('a.id')
            ->get('icom_eventos a')
            ->row();

        if ($row) {
            $this->load->helper('time');

            $descontoAnterior = timeToSec($row->desconto_folha ?? 0) - timeToSec($data['data']['desconto_folha_entrada'] ?? 0) + timeToSec($row->desconto_folha_entrada ?? 0);

            $this->db->update('icom_alocados', ['desconto_folha' => secToTime($descontoAnterior)], ['id' => $row->id_alocado]);
        }

        return $data;
    }

    //--------------------------------------------------------------------

    protected function unsetDescontoFolha($data)
    {
        if (!empty($data['data']) == false) {
            return $data;
        }

        $row = $this->db
            ->select('a.id_alocado, a.desconto_folha_entrada', false)
            ->select('a.desconto_folha_saida b.desconto_folha', false)
            ->join('icom_alocados b', 'b.id = a.id_alocado')
            ->where_in('a.id', $data['id'])
            ->group_start()
            ->where_in('a.tipo_entrada', ['DL', 'FN', 'FJ', 'FC', 'AJ', 'AS', 'EA', 'EX', 'AC'])
            ->or_where_in('a.tipo_saida', ['SJ', 'SN', 'SP', 'AC'])
            ->group_end()
            ->group_by('a.id')
            ->get('icom_eventos a')
            ->row();

        if ($row) {
            $this->load->helper('time');

            $descontoAnterior = timeToSec($row->desconto_folha ?? 0) - timeToSec($data['data']['desconto_folha_entrada'] ?? 0) + timeToSec($row->desconto_folha_entrada ?? 0);

            $this->db->update('icom_alocados', ['desconto_folha' => secToTime($descontoAnterior)], ['id' => $row->id_alocado]);
        }

        return $data;
    }

    //--------------------------------------------------------------------

    protected function setBancoHoras($data)
    {
        if (!empty($data['data']) == false) {
            return $data;
        }

        if ($data['result'] and in_array($data['data']['tipo_evento'] ?? '', self::TIPOS)) {
            $id = $data['id'];
            if (!is_array($id)) {
                $id = [$id];
            }

            $row = $this->db
                ->select('b.id_usuario, c.banco_horas_icom', false)
                ->join('icom_alocados b', 'b.id = a.id_alocado')
                ->join('usuarios c', 'c.id = b.id_usuario')
                ->where_in('a.id', $id)
                ->where_in('tipo_evento', self::TIPOS)
                ->get('icom_eventos a')
                ->row();

            if ($row) {
                $this->load->helper('time');

                $bancoHoras = timeToSec($row->banco_horas_icom) + ($this->flagSaldoBancoHoras ?? 0);

                $this->db->update('usuarios', ['banco_horas_icom_2' => secToTime($bancoHoras)], ['id' => $row->id_usuario]);
            }
        }

        return $data;
    }

    //--------------------------------------------------------------------

    protected function unsetBancoHoras($data)
    {
        if (!empty($data[$this->primaryKey][0])) {
            $apontamento = $this->where($this->primaryKey, $data[$this->primaryKey][0])->first();
            $this->flagIdAlocado = [$apontamento->id_alocado];
        } else {
            if (!empty($data['data']['id_alocado'])) {
                $this->flagIdAlocado = $data['data']['id_alocado'];
            }

            return $data;
        }

        $row = $this->db
            ->select('a.id_alocado, a.saldo_banco_horas, a.saida_banco_horas, b.id_usuario, c.banco_horas_icom, a.tipo_evento')
            ->select('a.saldo_banco_horas_2, a.saida_banco_horas_2, a.tipo_evento_2')
            ->select('a.saldo_banco_horas_3, a.saida_banco_horas_3, a.tipo_evento_3')
            ->join('icom_alocados b', 'b.id = a.id_alocado')
            ->join('usuarios c', 'c.id = b.id_usuario')
            ->where_in('a.id', $data[$this->primaryKey])
            ->group_start()
            ->where('(a.saldo_banco_horas IS NOT NULL OR a.saida_banco_horas IS NOT NULL)', null, false)
            ->or_where('(a.saldo_banco_horas_2 IS NOT NULL OR a.saida_banco_horas_2 IS NOT NULL)', null, false)
            ->or_where('(a.saldo_banco_horas_3 IS NOT NULL OR a.saida_banco_horas_3 IS NOT NULL)', null, false)
            ->group_end()
            ->get('icom_apontamentos a')
            ->row();

        if (empty($row)) {
            return $data;
        } elseif (!in_array($row->tipo_evento, ['EA', 'SP', 'AS', 'SN', 'FC', 'CO', 'BH'])) {
            return $data;
        }

        $this->load->helper('time');

        $bancoHoras = timeToSec($row->banco_horas_icom) - (timeToSec($row->saldo_banco_horas) + timeToSec($row->saida_banco_horas) + timeToSec($row->saldo_banco_horas_2 ?? 0) + timeToSec($row->saida_banco_horas_2 ?? 0) + timeToSec($row->saldo_banco_horas_3 ?? 0) + timeToSec($row->saida_banco_horas_3 ?? 0));

        $this->db->update('usuarios', ['banco_horas_icom' => secToTime($bancoHoras)], ['id' => $row->id_usuario]);

        return $data;
    }

    //--------------------------------------------------------------------

    protected function setApontamentoWeb($data)
    {
        return $data;
    }

    //--------------------------------------------------------------------

    protected function unsetApontamentoWeb($data)
    {
        if (!empty($data['id']) == false) {
            return $data;
        }

        $row = $this->db
            ->select('b.id_usuario, DAY(a.data_entrada) AS dia', false)
            ->select('MONTH(a.data_entrada) AS mes, YEAR(a.data_entrada) AS ano', false)
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
                ->delete('usuarios_apontamentos_horas_2');
        }

        return $data;
    }

    //--------------------------------------------------------------------

    public function insertByOld($ids, $post)
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        $data = [
            'id_alocado' => $post['id_alocado'],
            'tipo_entrada' => $post['tipo_evento_entrada'] ?? $post['tipo_evento'],
            'entrada_automatica' => $post['modo_acesso'] === 'A' ? 1 : null,
            'data_entrada' => $post['data'],
            'hora_entrada' => $post['horario_entrada'] ?? null,
            'horario_especial_entrada' => $post['horario_entrada_especial'] ?? null,
            'desconto_folha_entrada' => $post['desconto_folha'] ?? null,
            'saldo_horas_entrada' => $post['saldo_banco_horas'] ?? null,
            'tipo_saida' => $post['tipo_evento_saida'] ?? null,
            'saida_automatica' => $post['modo_acesso'] === 'A' ? 1 : null,
            'data_saida' => !empty($post['hora_saida']) ? $post['data'] : null,
            'hora_saida' => $post['hora_saida'] ?? null,
            'horario_especial_saida' => $post['horario_saida_especial'] ?? null,
            'desconto_folha_saida' => $post['desconto_folha_saida'] ?? null,
            'saldo_horas_saida' => $post['saida_banco_horas'] ?? null,
            'horas_diarias' => $post['qtde_horas_diarias'] ?? null,
            'minutos_folga' => $post['qtde_minutos_folga'] ?? null,
            'acrescimo_folha' => $post['hora_extra'] ?? null,
            'observacoes' => $post['observaoces'] ?? null,
        ];
        foreach ($ids as $id) {
            $data['id_old'] = $id;

            $this->db->insert('icom_eventos', $data);
        }
    }

    //--------------------------------------------------------------------

    public function updateByOld($id, $post)
    {
        if (!is_array($id)) {
            $id = [$id];
        }
        $data = [
            'id_alocado' => $post['id_alocado'],
            'tipo_entrada' => $post['tipo_evento_entrada'] ?? $post['tipo_evento'],
            'entrada_automatica' => $post['modo_acesso'] === 'A' ? 1 : null,
            'data_entrada' => $post['data'],
            'hora_entrada' => $post['horario_entrada'] ?? null,
            'horario_especial_entrada' => $post['horario_entrada_especial'] ?? null,
            'desconto_folha_entrada' => $post['desconto_folha'] ?? null,
            'saldo_horas_entrada' => $post['saldo_banco_horas'] ?? null,
            'tipo_saida' => $post['tipo_evento_saida'] ?? null,
            'saida_automatica' => $post['modo_acesso'] === 'A' ? 1 : null,
            'data_saida' => !empty($post['hora_saida']) ? $post['data'] : null,
            'hora_saida' => $post['hora_saida'] ?? null,
            'horario_especial_saida' => $post['horario_saida_especial'] ?? null,
            'desconto_folha_saida' => $post['desconto_folha_saida'] ?? null,
            'saldo_horas_saida' => $post['saida_banco_horas'] ?? null,
            'horas_diarias' => $post['qtde_horas_diarias'] ?? null,
            'minutos_folga' => $post['qtde_minutos_folga'] ?? null,
            'acrescimo_folha' => $post['hora_extra'] ?? null,
            'observacoes' => $post['observaoces'] ?? null
        ];

        $this->db
            ->set($data)
            ->where_in('id_old', $id)
            ->update('icom_eventos');
    }

    //--------------------------------------------------------------------

    public function deleteByOld($id)
    {
        if (!is_array($id)) {
            $id = [$id];
        }

        $this->db
            ->where_in('id_old', $id)
            ->delete('icom_eventos');
    }
}
