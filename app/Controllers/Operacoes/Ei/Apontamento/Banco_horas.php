<?php

namespace App\Controllers\Ei\Apontamento;

use App\Controllers\BaseController;

class Banco_horas extends BaseController
{

    public function ajax_list()
    {
        parse_str($this->input->post('busca'), $busca);

        $idMes = intval($busca['mes']) - ($busca['semestre'] === '2' ? 6 : 0);

        $alocacao = $this->db
            ->select('id', false)
            ->where('id_usuario', $busca['supervisor'])
            ->where('ano', $busca['ano'])
            ->where('semestre', $busca['semestre'])
            ->get('ei_coordenacao')
            ->row();

        $bancoHoras = $this->db
            ->select("id, saldo_mes{$idMes} AS saldo_mes", false)
            ->select('saldo_mes1, saldo_mes2, saldo_mes3, saldo_mes4')
            ->select('saldo_mes5, saldo_mes6, saldo_mes7')
            ->where('id_supervisao', $alocacao->id ?? '')
            ->get('ei_saldo_banco_horas')
            ->row();

        $query = $this->db
            ->select('a.data')
            ->select(["TIME_FORMAT(a.horario_entrada, '%H:%i') AS horario_entrada"], false)
            ->select(["TIME_FORMAT(a.horario_saida, '%H:%i') AS horario_saida"], false)
            ->select(["TIME_FORMAT(a.horario_entrada_1, '%H:%i') AS horario_entrada_1"], false)
            ->select(["TIME_FORMAT(a.horario_saida_1, '%H:%i') AS horario_saida_1"], false)
            ->select(["TIME_FORMAT(a.total, '%H:%i') AS total"], false)
            ->select(["TIME_FORMAT(a.saldo_dia, '%H:%i') AS saldo_dia"], false)
            ->select('a.observacoes, a.id')
            ->select(["DATE_FORMAT(a.data, '%d/%m/%Y') AS data_de"], false)
            ->join('ei_coordenacao b', 'b.id = a.id_supervisao')
            ->where('b.id', $alocacao->id ?? null)
            ->where('MONTH(a.data)', $busca['mes'])
            ->where('YEAR(a.data)', $busca['ano'])
            ->get('ei_carga_horaria a');

        $this->load->helper('time');

        $this->load->library('dataTables');

        $output = $this->datatables->generate($query);

        $data = [];

        foreach ($output->data as $row) {
            $data[] = [
                $row->data_de,
                $row->horario_entrada,
                $row->horario_saida,
                $row->horario_entrada_1,
                $row->horario_saida_1,
                $row->total,
                $row->saldo_dia,
                nl2br($row->observacoes),
                '<button class="btn btn-sm btn-info" onclick="edit_banco_hora(' . $row->id . ');" title="Editar evento"><i class="glyphicon glyphicon-pencil"></i></button>
                 <button class="btn btn-sm btn-danger" onclick="delete_banco_hora(' . $row->id . ');" title="Excluir evento"><i class="glyphicon glyphicon-trash"></i></button>',
            ];
        }

        $coordenacao = $this->db
            ->select(["TIME_FORMAT(a.carga_horaria, '%H:%i') AS carga_horaria"], false)
            ->select('b.banco_horas_icom AS saldo_acumulado_horas', false)
            ->join('usuarios b', 'b.id = a.id_usuario')
            ->where('b.id', $busca['supervisor'])
            ->where('a.ano', $busca['ano'])
            ->where('a.semestre', $busca['semestre'])
            ->get('ei_coordenacao a')
            ->row();

        $saldoAcumulado = timeToSec($coordenacao->saldo_acumulado_horas ?? '');
        $saldoMes = timeToSec($bancoHoras->saldo_mes ?? '');

        $output->saldo_mes = secToTime($saldoMes, false);
        $output->saldo_acumulado = secToTime($saldoAcumulado, false);

        $output->carga_horaria = $coordenacao->carga_horaria ?? '';

        $dias = array_map(function ($d) {
            return str_pad($d, 2, '0', 0);
        }, range(1, date('t', mktime(0, 0, 0, $busca['mes'], 1, $busca['ano']))));

        $output->dias = form_dropdown('', array_combine($dias, $dias), 1);

        $output->data = $data;

        echo json_encode($output);
    }

    //--------------------------------------------------------------------

    public function ajax_edit()
    {
        $data = $this->db
            ->where('id', $this->input->post('id'))
            ->get('ei_carga_horaria')
            ->row();

        if (empty($data)) {
            exit(json_encode(['erro' => 'Carga horária não encontrada ou excluída recentemente.']));
        }

        $data->dia = date('d', strtotime($data->data));
        if ($data->horario_entrada) {
            $data->horario_entrada = date('H:i', strtotime($data->horario_entrada));
        }
        if ($data->horario_saida) {
            $data->horario_saida = date('H:i', strtotime($data->horario_saida));
        }
        if ($data->horario_entrada_1) {
            $data->horario_entrada_1 = date('H:i', strtotime($data->horario_entrada_1));
        }
        if ($data->horario_saida_1) {
            $data->horario_saida_1 = date('H:i', strtotime($data->horario_saida_1));
        }
        if ($data->carga_horaria) {
            $data->carga_horaria = date('H:i', strtotime($data->carga_horaria));
        }
        if ($data->saldo_dia) {
            $data->saldo_dia = date('H:i', strtotime($data->saldo_dia));
        }

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_add()
    {
        $data = $this->input->post();

        $supervisao = $this->db
            ->select('a.id')
            ->join('usuarios b', 'b.id = a.id_usuario')
            ->join('ei_supervisores c', 'c.id_coordenacao = a.id OR c.id_supervisor = a.id_usuario')
            ->join('ei_escolas d', 'd.id = c.id_escola')
            ->join('ei_diretorias e', 'e.id = d.id_diretoria')
            ->where('e.depto', $data['depto'])
            ->where('e.id', $data['diretoria'])
            ->where('a.id_usuario', $data['supervisor'])
            ->where('a.ano <=', $data['ano'])
            ->where('a.semestre', $data['semestre'])
            ->group_by(['a.id', 'a.ano', 'a.semestre'])
            ->order_by('a.ano DESC, a.semestre DESC')
            ->limit(1)
            ->get('ei_coordenacao a')
            ->row();

        if (empty($supervisao)) {
            exit(json_encode(['erro' => 'O Supervisor não foi encontrado.']));
        }

        $data['data'] = date('Y-m-d', mktime(0, 0, 0, $data['mes'], $data['dia'], $data['ano']));

        $mes = $data['mes'];
        $semestre = $data['semestre'];
        unset($data['depto'], $data['diretoria'], $data['supervisor'], $data['dia'], $data['mes'], $data['ano'], $data['semestre']);

        $data['id_supervisao'] = $supervisao->id;

        foreach ($data as &$row) {
            if (strlen($row) == 0) {
                $row = null;
            }
        }

        $this->load->helper('time');

        $data['total'] = secToTime(
            (timeToSec($data['horario_saida']) - timeToSec($data['horario_entrada'])) +
            (timeToSec($data['horario_saida_1']) - timeToSec($data['horario_entrada_1'])), false);

        $data['saldo_dia'] = secToTime(timeToSec($data['total']) - timeToSec($data['carga_horaria']));

        $this->db->trans_start();
        $this->db->insert('ei_carga_horaria', $data);
        $this->updateSaldoAcumulado($supervisao->id, $mes, $semestre);
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            exit(json_encode(['erro' => 'Erro ao cadastrar o Banco de Horas.']));
        }

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    public function ajax_update()
    {
        $data = $this->input->post();

        $data['data'] = date('Y-m-d', mktime(0, 0, 0, $data['mes'], $data['dia'], $data['ano']));

        if (empty($data['id_supervisao'])) {
            exit(json_encode(['erro' => 'O Supervisor não foi encontrado.']));
        }

        $mes = $data['mes'];
        $semestre = $data['semestre'];
        unset($data['depto'], $data['diretoria'], $data['supervisor'], $data['dia'], $data['mes'], $data['ano'], $data['semestre']);

        foreach ($data as &$row) {
            if (strlen($row) == 0) {
                $row = null;
            }
        }

        $this->load->helper('time');

        $data['total'] = secToTime(
            (timeToSec($data['horario_saida']) - timeToSec($data['horario_entrada'])) +
            (timeToSec($data['horario_saida_1']) - timeToSec($data['horario_entrada_1'])), false);

        $data['saldo_dia'] = secToTime(timeToSec($data['total']) - timeToSec($data['carga_horaria']));

        $this->db->trans_start();
        $this->db->update('ei_carga_horaria', $data, ['id' => $data['id']]);
        $this->updateSaldoAcumulado($data['id_supervisao'], $mes, $semestre);
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            exit(json_encode(['erro' => 'Erro ao cadastrar o Banco de Horas.']));
        }

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    public function ajax_delete()
    {
        $cargaHoraria = $this->db
            ->select('a.id, a.id_supervisao, MONTH(a.data) AS mes, b.semestre', false)
            ->join('ei_coordenacao b', 'b.id = a.id_supervisao')
            ->where('a.id', $this->input->post('id'))
            ->get('ei_carga_horaria a')
            ->row();

        if (empty($cargaHoraria)) {
            exit(json_encode(['erro' => 'O Supervisor não foi encontrado.']));
        }

        $this->db->trans_start();
        $this->db->update('ei_carga_horaria', ['saldo_dia' => 0], ['id' => $cargaHoraria->id]);
        $this->updateSaldoAcumulado($cargaHoraria->id_supervisao, $cargaHoraria->mes, $cargaHoraria->semestre);
        $this->db->delete('ei_carga_horaria', ['id' => $cargaHoraria->id]);
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            exit(json_encode(['erro' => 'Não foi possível excluir a carga horária.']));
        }

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    private function updateSaldoAcumulado(int $idSupervisao, string $mes, int $semestre): void
    {
        $idMes = intval($mes) - ($semestre > 1 ? 6 : 0);

        $cargaHoraria = $this->db
            ->select('b.id_usuario, b.ano, b.semestre')
            ->select(['SUM(IFNULL(TIME_TO_SEC(a.saldo_dia), 0)) AS novo_saldo_segundos_mes'], false)
            ->join('ei_coordenacao b', 'b.id = a.id_supervisao')
            ->where('a.id_supervisao', $idSupervisao)
            ->where('MONTH(a.data)', $mes)
            ->get('ei_carga_horaria a')
            ->row();

        $bancoHoras = $this->db
            ->select("id, saldo_mes{$idMes} AS antigo_saldo_mes", false)
            ->where('id_supervisao', $idSupervisao)
            ->get('ei_saldo_banco_horas')
            ->row();

        if ($this->load->is_loaded('time') === false) {
            $this->load->helper('time');
        }

        if (isset($cargaHoraria->novo_saldo_segundos_mes)) {
            $data = ['saldo_mes' . $idMes => secToTime($cargaHoraria->novo_saldo_segundos_mes)];
            if (isset($bancoHoras->id)) {
                $this->db->update('ei_saldo_banco_horas', $data, ['id_supervisao' => $idSupervisao]);
            } else {
                $data['id_supervisao'] = $idSupervisao;
                $this->db->insert('ei_saldo_banco_horas', $data);
            }
        } else {
            $this->db->delete('ei_saldo_banco_horas', ['id_supervisao' => $idSupervisao]);
        }

        $coordenacao = $this->db
//			->select('a.id, a.saldo_acumulado_horas AS saldo_acumulado')
            ->select('b.id, b.banco_horas_icom AS saldo_acumulado')
            ->join('usuarios b', 'b.id = a.id_usuario')
            ->where('b.id', $cargaHoraria->id_usuario)
            ->where("CONCAT(a.ano, '.', a.semestre) >= '{$cargaHoraria->ano}.{$cargaHoraria->semestre}'")
            ->where('a.is_supervisor', 1)
            ->get('ei_coordenacao a')
            ->result();

        foreach ($coordenacao as $row) {
            $saldoAcumulado = timeToSec($row->saldo_acumulado);
            $diferenca = 0;
            if (!empty($bancoHoras->antigo_saldo_mes)) {
                $diferenca -= timeToSec($bancoHoras->antigo_saldo_mes);
            }
            if (!empty($cargaHoraria->novo_saldo_segundos_mes)) {
                $diferenca += $cargaHoraria->novo_saldo_segundos_mes;
            }
            $saldoAcumulado += $diferenca;
            $this->db
                ->set('banco_horas_icom', secToTime($saldoAcumulado))
                ->where('id', $row->id)
                ->update('usuarios');
            /*$this->db
				->set('saldo_acumulado_horas', secToTime($saldoAcumulado))
				->where('id', $row->id)
				->update('ei_coordenacao');*/
        }
    }

    //--------------------------------------------------------------------

    public function pdf()
    {
        $empresa = $this->session->userdata('empresa');

        $data['empresa'] = $this->db
            ->select('foto, foto_descricao')
            ->get_where('usuarios', ['id' => $this->session->userdata('empresa')])
            ->row();

        $depto = $this->input->get('depto');
        $idDiretoria = $this->input->get('diretoria');
        $idSupervisor = $this->input->get('supervisor');
        $mes = $this->input->get('mes');
        $ano = $this->input->get('ano');
        $semestre = $this->input->get('semestre');

        $saldoMes = $this->input->post('saldo_mes');
        $saldoAcumulado = $this->input->post('saldo_acumulado');

        $diretoria = $this->db->select('nome')->where('id', $idDiretoria)->get('ei_diretorias')->row();
        $supervisor = $this->db->select('nome')->where('id', $idSupervisor)->get('usuarios')->row();

        $this->load->library('calendar');

        $data['departamento'] = $depto;
        $data['diretoria'] = $diretoria->nome;
        $data['supervisor'] = $supervisor->nome;
        $data['mes'] = $this->calendar->get_month_name($mes);
        $data['ano'] = $ano;
        $data['semestre'] = $semestre;

        $coordenacao = $this->db
            ->select('id, saldo_acumulado_horas')
            ->where('id_usuario', $idSupervisor)
            ->where('ano', $ano)
            ->where('semestre', $semestre)
            ->get('ei_coordenacao')
            ->row();

        $this->load->helper('time');

        $data['saldo_acumulado_horas'] = timeSimpleFormat($coordenacao->saldo_acumulado_horas ?? '');

        $qb = $this->db
            ->select(["DATE_FORMAT(a.data, '%d/%m/%Y') AS data"], false)
            ->select(["TIME_FORMAT(a.horario_entrada, '%H:%i') AS horario_entrada"], false)
            ->select(["TIME_FORMAT(a.horario_saida, '%H:%i') AS horario_saida"], false)
            ->select(["TIME_FORMAT(a.horario_entrada_1, '%H:%i') AS horario_entrada_1"], false)
            ->select(["TIME_FORMAT(a.horario_saida_1, '%H:%i') AS horario_saida_1"], false)
            ->select(["TIME_FORMAT(a.total, '%H:%i') AS total"], false)
            ->select(["TIME_FORMAT(a.saldo_dia, '%H:%i') AS saldo_dia"], false)
            ->select('a.observacoes')
            ->join('ei_coordenacao b', 'b.id = a.id_supervisao')
            ->where('b.id', $coordenacao->id ?? null)
            ->where('MONTH(data)', $mes)
            ->where('YEAR(data)', $ano);
        if ($saldoMes) {
            $qb->where("TIME_FORMAT(a.saldo_dia, '%H:%i') = '{$saldoMes}'", null, false);
        }
        if ($saldoAcumulado) {
            $qb->where("TIME_FORMAT(a.saldo_dia, '%H:%i') = '{$saldoAcumulado}'", null, false);
        }
        $data['rows'] = $qb
            ->group_by('a.id')
            ->get('ei_carga_horaria a')
            ->result();

        $this->load->library('m_pdf');

        $stylesheet = '#table thead th { font-size: 12px; padding: 5px; background-color: #f5f5f5;} ';
        $stylesheet .= '#table tbody td { font-size: 14px; padding: 5px; vertical-align: top; } ';
        $stylesheet .= '#banco_horas thead tr th { padding: 5px; text-align: center; background-color: #f5f5f5; border-color: #ddd; } ';
        $stylesheet .= '#banco_horas tbody tr td { font-size: 12px; padding: 5px; } ';

        $this->m_pdf->pdf->setTopMargin(68);
        $this->m_pdf->pdf->AddPage('L');
        $this->m_pdf->pdf->writeHTML($stylesheet, 1);
        $this->m_pdf->pdf->writeHTML($this->load->view('ei/banco_horas_pdf', $data, true));

        $this->calendar->month_type = 'short';

        $this->m_pdf->pdf->Output('Banco de Horas - ' . $this->calendar->get_month_name($mes) . '_' . $data['ano'] . '.pdf', 'D');
    }

    //--------------------------------------------------------------------

    private function validarCargaHoraria(): void
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('data', '"Data"', 'valid_date');
        $this->form_validation->set_rules('data_1', '"Data"', 'valid_date');
        $this->form_validation->set_rules('horario_entrada', '"Horário entrada"', 'valid_time');
        $this->form_validation->set_rules('horario_saida', '"Horário saída"', 'valid_time');
        $this->form_validation->set_rules('horario_entrada_1', '"Horário entrada"', 'valid_time');
        $this->form_validation->set_rules('horario_saida_1', '"Horário saída"', 'valid_time');

        if ($this->form_validation->run() == false) {
            exit(json_encode(['erro' => $this->form_validation->error_string(' ', ' ')]));
        }
    }

    //--------------------------------------------------------------------

    public function salvar_carga_horaria_acumulada()
    {
        $alocacao = $this->db
            ->select('id, saldo_acumulado')
            ->where('empresa', $this->session->userdata('empresa'))
            ->get('ei_alocacac')
            ->row();

        $saldoMes = $this->input->post('saldo_mes');
        $saldoAcumulado = $this->input->post('saldo_acumulado');
        $data = [
            'saldo_mensal' => $saldoMes,
            'saldo_acumulado' => secToTime(timeToSec($alocacao->saldo_acumulado) + timeToSec($saldoAcumulado)),
        ];

        $this->db->update('ei_alocacao', $data, ['id' => $alocacao->id]);

        echo json_encode(['status' => true]);
    }

}
