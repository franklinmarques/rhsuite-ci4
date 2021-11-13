<?php

namespace App\Controllers\Ei\Apontamento;

use App\Controllers\BaseController;

class Mapa_atividades extends BaseController
{

    public function pdf_visitacao_macro()
    {
        $empresa = $this->session->userdata('empresa');

        $data['empresa'] = $this->db
            ->select('foto, foto_descricao')
            ->get_where('usuarios', ['id' => $this->session->userdata('empresa')])
            ->row();

        $depto = $this->input->get('depto');
        $idDiretoria = $this->input->get('diretoria');
        $idSupervisor = $this->input->get('supervisor');
        $ano = $this->input->get('ano');
        $semestre = $this->input->get('semestre');

        $alocacao = $this->db
            ->select('id, diretoria, supervisor')
            ->where('id_empresa', $empresa)
            ->where('depto', $depto)
            ->where('id_diretoria', $idDiretoria)
            ->where('id_supervisor', $idSupervisor)
            ->where('ano', $ano)
            ->where('semestre', $semestre)
            ->get('ei_alocacao')
            ->row();

        $data['departamento'] = $depto;
        $data['diretoria'] = $alocacao->diretoria;
        $data['supervisor'] = $alocacao->supervisor;
        $data['ano'] = $ano;
        $data['semestre'] = $semestre;

        $visitas = $this->db
            ->select('a.id, a.municipio, a.escola')
            ->join('ei_mapa_visitacao b', 'b.id_mapa_unidade = a.id', 'left')
            ->where('a.id_alocacao', $alocacao->id)
            ->group_by('a.id')
            ->order_by('a.municipio', 'asc')
            ->order_by('a.escola', 'asc')
            ->get('ei_mapa_unidades a')
            ->result();

        $eventos = $this->db
            ->select('a.id_mapa_unidade, a.motivo_visita AS status')
            ->select("MONTH(a.data_visita) - IF(c.semestre = 2, 6, 0) AS mes", false)
            ->select("DATE_FORMAT(MAX(a.data_visita), '%d/%m/%Y') AS data_visita", false)
            ->join('ei_mapa_unidades b', 'b.id = a.id_mapa_unidade')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->where('c.id', $alocacao->id)
            ->group_by(['b.escola', 'MONTH(a.data_visita)'])
            ->get('ei_mapa_visitacao a')
            ->result();

        $status = [
            '0' => 'warning',
            '1' => 'success',
            '2' => 'danger',
        ];

        $mesesVisitados = [];
        foreach ($eventos as $evento) {
            $mesesVisitados[$evento->id_mapa_unidade][$evento->mes] = [
                'data_visita' => $evento->data_visita,
                'status' => $status[$evento->status] ?? null
            ];
        }

        $rows = [];
        foreach ($visitas as $visita) {
            $row = [
                'municipio' => $visita->municipio,
                'escola' => $visita->escola
            ];
            for ($a = 1; $a <= 7; $a++) {
                $row['data_visita_mes' . $a] = $mesesVisitados[$visita->id][$a]['data_visita'] ?? null;
            }
            for ($b = 1; $b <= 7; $b++) {
                $row['status_mes' . $b] = $mesesVisitados[$visita->id][$b]['status'] ?? null;
            }

            $rows[] = (object)$row;
        }

        $data['rows'] = $rows;

        if ($semestre === '2') {
            $data['meses'] = [
                'Julho', 'Agosto', 'Setembro',
                'Outubro', 'Novembro', 'Dezembro', null,
            ];
        } else {
            $data['meses'] = [
                'Janeiro', 'Fevereiro', 'Março',
                'Abril', 'Maio', 'Junho', 'Julho',
            ];
        }

        $this->load->library('m_pdf');

        $stylesheet = '#table thead th { font-size: 12px; padding: 5px; background-color: #f5f5f5;} ';
        $stylesheet .= '#table tbody td { font-size: 14px; padding: 5px; vertical-align: top; } ';
        $stylesheet .= '#mapa_visitacao thead tr th { padding: 5px; text-align: center; background-color: #f5f5f5; border-color: #ddd; } ';
        $stylesheet .= '#mapa_visitacao tbody tr td { font-size: 12px; padding: 5px; } ';

        $this->m_pdf->pdf->setTopMargin(60);
        $this->m_pdf->pdf->AddPage('L');
        $this->m_pdf->pdf->writeHTML($stylesheet, 1);
        $this->m_pdf->pdf->writeHTML($this->load->view('ei/mapa_visitacao_macro_pdf', $data, true));

        $this->m_pdf->pdf->Output('Mapa de Atividades Macro.pdf', 'D');
    }

    //--------------------------------------------------------------------

    public function pdf_visitacao_tabular()
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

        $alocacao = $this->db
            ->select('id, diretoria, supervisor')
            ->where('id_empresa', $empresa)
            ->where('depto', $depto)
            ->where('id_diretoria', $idDiretoria)
            ->where('id_supervisor', $idSupervisor)
            ->where('ano', $ano)
            ->where('semestre', $semestre)
            ->get('ei_alocacao')
            ->row();

        $data['departamento'] = $depto;
        $data['diretoria'] = $alocacao->diretoria;
        $data['supervisor'] = $alocacao->supervisor;
        $data['mes'] = $mes;
        $data['ano'] = $ano;
        $data['semestre'] = $semestre;

        $eventos = $this->db
            ->select('a.id, tipo_atividade, a.supervisor_visitante, d.nome AS nome_cliente')
            ->select('a.municipio AS atividade_municipio, e.nome AS nome_unidade_visitada')
            ->select('a.prestadores_servicos_tratados, f.nome AS nome_coordenador_responsavel')
            ->select('a.motivo_visita, a.sumario_visita, a.observacoes')
            ->select(["DATE_FORMAT(a.data_visita, '%d/%m/%Y') AS data_visita"], false)
            ->select(["DATE_FORMAT(a.data_visita, '%d/%m/%Y') AS data_visita_anterior"], false)
            ->select(["FORMAT(a.gastos_materiais, 2,  'de_DE') AS gastos_materiais"], false)
            ->select('b.municipio AS nome_municipio, b.escola AS nome_escola')
            ->join('ei_mapa_unidades b', 'b.id = a.id_mapa_unidade')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->join('ei_diretorias d', 'd.id = a.cliente', 'left')
            ->join('ei_escolas e', 'e.id = a.unidade_visitada', 'left')
            ->join('usuarios f', 'f.id = a.coordenador_responsavel', 'left')
            ->where('c.id', $alocacao->id)
            ->where('MONTH(a.data_visita)', $mes)
            ->order_by('b.municipio', 'asc')
            ->order_by('b.escola', 'asc')
            ->order_by('a.data_visita', 'asc')
            ->order_by('a.data_visita_anterior', 'asc')
            ->get('ei_mapa_visitacao a')
            ->result();

        $rows = [];

        foreach ($eventos as $evento) {
            $rows[$evento->nome_municipio][$evento->nome_escola][] = $evento;
        }

        $data['rows'] = $rows;

        $this->load->model('ei_mapa_visitacao_model', 'atividade');

        $data['tiposAtividades'] = $this->atividade::TIPOS_ATIVIDADES;
        $data['$motivosAtividades'] = $this->atividade::MOTIVOS_ATIVIDADES;

        $this->load->library('m_pdf');

        $stylesheet = '#table thead th { font-size: 12px; padding: 5px; background-color: #f5f5f5;} ';
        $stylesheet .= '#table tbody td { font-size: 12px; padding: 5px; vertical-align: top; } ';
        $stylesheet .= '#mapa_visitacao thead tr th { padding: 5px; text-align: center; background-color: #f5f5f5; border-color: #ddd; } ';
        $stylesheet .= '#mapa_visitacao tbody tr td { font-size: 10px; padding: 5px; } ';

        $this->m_pdf->pdf->setTopMargin(55);
        $this->m_pdf->pdf->AddPage('P');
        $this->m_pdf->pdf->writeHTML($stylesheet, 1);
        $this->m_pdf->pdf->writeHTML($this->load->view('ei/mapa_visitacao_tabular_pdf', $data, true));

        $this->m_pdf->pdf->Output('Mapa de Atividades Tabular.pdf', 'D');
    }

    //--------------------------------------------------------------------

    public function ajax_list()
    {
        $post = $this->input->post();

        parse_str($this->input->post('busca'), $busca);

        $semestre = intval($busca['semestre']);

        $this->load->library('Calendar');
        $this->calendar->month_type = 'short';
        $meses = [];
        $nomeMeses = [];
        $mesInicial = $semestre === 2 ? 7 : 1;
        $mesFinal = $semestre === 2 ? 12 : 7;
        for ($i = $mesInicial; $i <= $mesFinal; $i++) {
            $mes = str_pad($i, 2, '0', STR_PAD_LEFT);
            $meses[] = $mes;
            $nomeMeses[] = ucfirst($this->calendar->get_month_name($mes));
        }

        $output = [
            'draw' => intval($this->input->post('draw')),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'meses' => $meses,
            'semestre' => $nomeMeses,
            'data' => [],
        ];

        $alocacao = $this->db
            ->select('id')
            ->where('id_empresa', $this->session->userdata('empresa'))
            ->where('depto', $busca['depto'])
            ->where('id_diretoria', $busca['diretoria'])
            ->where('id_supervisor', $busca['supervisor'])
            ->where('ano', $busca['ano'])
            ->where('semestre', $semestre)
            ->get('ei_alocacao')
            ->row();

        if (empty($alocacao)) {
            echo json_encode($output);
            return;
        }

        $recordsTotal = $this->db
            ->select('a.id, a.municipio, a.escola')
            ->join('ei_mapa_visitacao b', 'b.id_mapa_unidade = a.id', 'left')
            ->where('a.id_alocacao', $alocacao->id)
            ->group_by('a.id')
            ->get('ei_mapa_unidades a')
            ->num_rows();

        $sql = "SELECT s.* FROM ({$this->db->last_query()}) s";

        if ($post['search']['value']) {
            $sql .= " WHERE s.municipio LIKE '%{$post['search']['value']}%' OR 
                            s.escola LIKE '%{$post['search']['value']}%'";
            $recordsFiltered = $this->db->query($sql)->num_rows();
        } else {
            $recordsFiltered = $recordsTotal;
        }

        if ($post['order']) {
            $orderBy = [];
            foreach ($post['order'] as $order) {
                $orderBy[] = intval($order['column'] + 2) . ' ' . $order['dir'];
            }
            $sql .= ' ORDER BY ' . implode(', ', $orderBy);
            if ($post['length'] > 0) {
                $sql .= " LIMIT {$post['start']}, {$post['length']}";
            }
        }
        $visitas = $this->db->query($sql)->result();

        $eventos = $this->db
            ->select('b.id, a.id_mapa_unidade')
            ->select("COUNT(a.data_visita) AS total_visitas", false)
            ->select("SUM(IF(a.motivo_visita IN (5, 6, 7), 1, 0)) AS total_ocorrencias", false)
            ->select("MONTH(a.data_visita) - IF(c.semestre = 2, 6, 0) AS mes", false)
            ->select("MAX(a.data_visita) AS data_visita", false)
            ->select("SUM(IF(a.motivo_visita = 2, 1, 0)) AS visita_programada", false)
            ->select('a.motivo_visita', false)
            ->join('ei_mapa_unidades b', 'b.id = a.id_mapa_unidade')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->where('c.id', $alocacao->id)
            ->group_by(['b.escola', 'MONTH(a.data_visita)'])
            ->get('ei_mapa_visitacao a')
            ->result();

        $mesesVisitados = [];
        foreach ($eventos as $evento) {
            $mesesVisitados[$evento->id_mapa_unidade][$evento->mes] = [
                'total_visitas' => $evento->total_visitas,
                'total_ocorrencias' => $evento->total_ocorrencias,
                'data_visita' => $evento->data_visita,
                'motivo_visita' => $evento->motivo_visita,
            ];
        }

        $data = [];
        foreach ($visitas as $visita) {
            $row = [
                $visita->municipio,
                $visita->escola
            ];
            for ($i = 1; $i <= 7; $i++) {
                $row[] = $mesesVisitados[$visita->id][$i]['total_visitas'] ?? null;
            }
            $row[] = $visita->id;
            for ($a = 1; $a <= 7; $a++) {
                $row[] = $mesesVisitados[$visita->id][$a]['total_ocorrencias'] ?? null;
            }
            for ($b = 1; $b <= 7; $b++) {
                $row[] = $mesesVisitados[$visita->id][$b]['data_visita'] ?? null;
            }
            for ($c = 1; $c <= 7; $c++) {
                $row[] = $mesesVisitados[$visita->id][$c]['motivo_visita'] ?? null;
            }

            $data[] = $row;
        }

        $output['recordsTotal'] = intval($recordsTotal);
        $output['recordsFiltered'] = intval($recordsFiltered);
        $output['data'] = $data;

        echo json_encode($output);
    }

    //--------------------------------------------------------------------

    public function ajax_unidade_visitada()
    {
        $data = $this->db
            ->select('a.id, b.ano, a.escola')
            ->join('ei_alocacao b', 'b.id = a.id_alocacao')
            ->where('a.id', $this->input->post('id'))
            ->get('ei_mapa_unidades a')
            ->row();

        if (empty($data)) {
            exit(json_encode(['erro' => 'Unidade não encontrada.']));
        }

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function unidade_visitada($isPdf = false)
    {
        $empresa = $this->session->userdata('empresa');

        $data['empresa'] = $this->db
            ->select('foto, foto_descricao')
            ->get_where('usuarios', ['id' => $empresa])
            ->row();

        $id = $this->input->get('id_mapa_unidade');

        $mapaUnidade = $this->db
            ->select('a.*, b.ano', false)
            ->join('ei_alocacao b', 'b.id = a.id_alocacao')
            ->where('a.id', $id)
            ->get('ei_mapa_unidades a')
            ->row();

        $mes = $this->input->get('mes');
        $ano = $this->input->get('ano');
        if (empty($ano)) {
            $ano = $mapaUnidade->ano;
        }

        $sql = "SELECT DATE_FORMAT(a.data_visita, '%d/%m/%Y') AS data_visita, a.escola, 
                       a.supervisor_visitante, 
                       a.prestadores_servicos_tratados, 
                       (CASE a.motivo_visita
                             WHEN 1 THEN 'Visita de rotina'
                             WHEN 2 THEN 'Visita programada'
                             WHEN 3 THEN 'Solicitação da unidade'
                             WHEN 4 THEN 'Solicitação de materiais'
                             WHEN 5 THEN 'Processo seletivo'
                             WHEN 6 THEN 'Ocorrência com aluno'
                             WHEN 7 THEN 'Ocorrência com funcionário'
                             WHEN 8 THEN 'Ocorrência na escola'
                             END) AS motivo_visita,                             
                       FORMAT(a.gastos_materiais, 2, 'de_DE') AS gastos_materiais, 
                       a.sumario_visita, 
                       a.observacoes
                FROM ei_mapa_visitacao a
                INNER JOIN ei_mapa_unidades b ON b.id = a.id_mapa_unidade
                INNER JOIN ei_alocacao c ON c.id = b.id_alocacao
                WHERE c.id_empresa = '{$empresa}'
                      AND (b.id = '{$mapaUnidade->id}'
                      OR (c.ano = '{$ano}' AND b.escola = '{$mapaUnidade->id_escola}' AND b.escola = '{$mapaUnidade->escola}'))
                ORDER BY a.data_visita ASC, a.escola ASC, a.supervisor_visitante ASC";

        $data['visitas'] = $this->db->query($sql)->result();

        $data['id'] = $id;
        $data['mes'] = $mes;
        $data['ano'] = $ano;
        $data['mes_ano'] = implode('/', array_filter([$mes, $ano]));

        $data['query_string'] = http_build_query(['id_mapa_unidade' => $id, 'ano' => $ano, 'mes' => $mes]);
        $data['is_pdf'] = $isPdf === true;

        if ($data['is_pdf']) {
            return $this->load->view('ei/relatorio_visitas', $data, true);
        }

        $this->load->view('ei/relatorio_visitas', $data);
    }

    //--------------------------------------------------------------------

    public function pdf_unidade_visitada()
    {
        $this->load->library('m_pdf');

        $stylesheet = '#table thead tr th { border-top: 4px solid #ddd; padding-top: 8px; } ';
        $stylesheet .= 'table.unidades_visitadas {  border: 1px solid #333; margin-bottom: 0px; } ';
        $stylesheet .= 'table.unidades_visitadas thead tr th { font-size: 12px; padding: 4px; background-color: #f5f5f5; border: 1px solid #333;  } ';
        $stylesheet .= 'table.unidades_visitadas tbody tr td { font-size: 11px; padding: 4px; vertical-align: top; border: 1px solid #333;  } ';

        $this->m_pdf->pdf->setTopMargin(45);
        $this->m_pdf->pdf->AddPage('L');
        $this->m_pdf->pdf->writeHTML($stylesheet, 1);
        $this->m_pdf->pdf->writeHTML($this->unidade_visitada(true));

        $mes = $this->input->get('mes');
        $ano = $this->input->get('ano');

        $this->load->library('Calendar');
        $this->calendar->month_type = 'short';
        $nome = 'Relatório de Visitas - ' . implode('/', array_filter([$mes, $ano]));

        $this->m_pdf->pdf->Output($nome . '.pdf', 'D');
    }

    //--------------------------------------------------------------------

    public function ajax_atividades()
    {
        $idMapaUnidade = $this->input->post('id_mapa_unidade');
        $idMes = $this->input->post('id_mes');

        $rowsId = $this->db
            ->select(["a.id, CONCAT(DATE_FORMAT(a.data_visita, '%d/%m/%Y'), ' - ', b.escola) AS nome"], false)
            ->join('ei_mapa_unidades b', 'b.id = a.id_mapa_unidade')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->where('MONTH(a.data_visita)', intval($idMes))
            ->where('b.id', $idMapaUnidade)
            ->order_by('a.data_visita', 'asc')
            ->order_by('a.id', 'asc')
            ->get('ei_mapa_visitacao a')
            ->result();

        $id = ['' => '-- Nova atividade --'] + array_column($rowsId, 'nome', 'id');

        $data = $this->db
            ->select("d.*, a.id AS id_mapa_visitacao, DATE_FORMAT(d.data_visita, '%m') AS mes", false)
            ->select('b.id AS id_alocacao, b.ano, b.id_diretoria, b.id_supervisor', false)
            ->select('a.id_escola, a.escola AS unidade, a.municipio AS nome_municipio', false)
            ->join('ei_alocacao b', 'b.id = a.id_alocacao')
            ->join('ei_escolas c', 'c.id = a.id_escola', 'left')
            ->join('ei_mapa_visitacao d', "d.id_mapa_unidade = a.id AND DATE_FORMAT(d.data_visita, '%m') = '{$idMes}'", 'left')
            ->where('a.id', $idMapaUnidade)
            ->order_by('d.data_visita', 'desc')
            ->order_by('d.id', 'desc')
            ->get('ei_mapa_unidades a')
            ->row();

        if (empty($data->id_mapa_unidade)) {
            $data->id_mapa_unidade = $data->id_mapa_visitacao;
        }
        if (empty($data->id_supervisor_visitante)) {
            $data->id_supervisor_visitante = $data->id_supervisor;
        }
        if (empty($data->cliente)) {
            $data->cliente = $data->id_diretoria;
        }
        if (empty($data->municipio)) {
            $data->municipio = $data->nome_municipio;
        }
        if (empty($data->unidade_visitada)) {
            $data->unidade_visitada = $data->id_escola;
        }
        if (empty($data->escola)) {
            $data->escola = $data->unidade;
        }
        if (empty($data->mes)) {
            $data->mes = $idMes;
        }
        if ($data->data_visita) {
            $data->data_visita = date('d/m/Y', strtotime($data->data_visita));
        } else {
            $data->data_visita = date('d/m/Y', mktime(0, 0, 0, $idMes, 1, $data->ano));
        }
        if ($data->data_visita_anterior) {
            $data->data_visita_anterior = date('d/m/Y', strtotime($data->data_visita_anterior));
        } else {
            $data->data_visita_anterior = null;
        }
        $data->gastos_materiais = number_format($data->gastos_materiais, 2, ',', '.');

        $busca = [
            'escola' => $data->escola,
            'cliente' => $data->cliente,
            'municipio' => $data->municipio,
            'unidade_visitada' => $data->id_escola,
            'mes' => $data->mes,
            'ano' => $data->ano,
        ];

        $filtrosVisita = $this->montarFiltros($busca);
        $supervisoresVisitantes = $filtrosVisita['supervisores_visitantes'];
        $clientes = $filtrosVisita['clientes'];
        $municipios = $filtrosVisita['municipios'];
        $unidadesVisitadas = $filtrosVisita['unidades_visitadas'];

        if (empty($data->id)) {
            $data->prestadores_servicos_tratados = $filtrosVisita['prestadores_servicos_tratados'];
        }

        $data->id_selecionado = $data->id;
        $data->id = form_dropdown('id', $id, $data->id, 'class="form-control"');
        $data->supervisor_visitante = form_dropdown('supervisor_visitante', $supervisoresVisitantes, $data->id_supervisor_visitante, 'class="form-control"');
        $data->cliente = form_dropdown('cliente', $clientes, $data->cliente, 'onchange="atualizarFiltrosVisitas()" class="form-control"');
        $data->municipio = form_dropdown('municipio', $municipios, $data->municipio, 'onchange="atualizarFiltrosVisitas()" class="form-control"');
        $data->unidade_visitada = form_dropdown('unidade_visitada', $unidadesVisitadas, $data->unidade_visitada, 'onchange="atualizarFiltrosVisitas()" class="form-control"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_edit()
    {
        $id = $this->input->post('id');
        $idMapaUnidade = $this->input->post('id_mapa_unidade');
        $idMes = $this->input->post('id_mes');

        $data = $this->db
            ->select("c.*, a.municipio AS nome_municipio, a.id_escola, a.escola AS unidade, b.id_diretoria, b.id_supervisor, b.ano", false)
            ->join('ei_alocacao b', 'b.id = a.id_alocacao')
            ->join('ei_mapa_visitacao c', "c.id_mapa_unidade = a.id AND c.id = '{$id}'", 'left')
            ->where('a.id', $idMapaUnidade)
            ->get('ei_mapa_unidades a')
            ->row();

        if (empty($data->escola)) {
            $data->escola = $data->unidade;
        }
        if (empty($data->mes)) {
            $data->mes = $idMes;
        }
        if ($data->data_visita) {
            $data->data_visita = date('d/m/Y', strtotime($data->data_visita));
        } elseif (empty($data->id)) {
            $data->data_visita = date('d/m/Y', mktime(0, 0, 0, $data->mes, 1, $data->ano));
        }
        if ($data->data_visita_anterior) {
            $data->data_visita_anterior = date('d/m/Y', strtotime($data->data_visita_anterior));
        }
        $data->gastos_materiais = number_format($data->gastos_materiais, 2, ',', '.');

        $busca = [
            'escola' => $data->escola,
            'cliente' => $data->cliente,
            'municipio' => $data->municipio,
            'unidade_visitada' => $data->id_escola,
            'mes' => $data->mes,
            'ano' => $data->ano,
        ];

        $filtrosVisita = $this->montarFiltros($busca);
        $supervisoresVisitantes = $filtrosVisita['supervisores_visitantes'];
        $clientes = $filtrosVisita['clientes'];
        $municipios = $filtrosVisita['municipios'];
        $unidadesVisitadas = $filtrosVisita['unidades_visitadas'];

        if ($data->id) {
            $data->prestadores_servicos_tratados = $filtrosVisita['prestadores_servicos_tratados'];
        }

        $data->supervisor_visitante = form_dropdown('supervisor_visitante', $supervisoresVisitantes, $data->id_supervisor_visitante, 'class="form-control"');
        $data->cliente = form_dropdown('cliente', $clientes, $data->cliente, 'onchange="atualizarFiltrosVisitas()" class="form-control"');
        $data->municipio = form_dropdown('municipio', $municipios, $data->municipio, 'onchange="atualizarFiltrosVisitas()" class="form-control"');
        $data->unidade_visitada = form_dropdown('unidade_visitada', $unidadesVisitadas, $data->unidade_visitada, 'onchange="atualizarFiltrosVisitas()" class="form-control"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function atualizar_filtros()
    {
        $id = $this->input->post('id');

        $row = $this->db
            ->select('cliente, municipio, unidade_visitada')
            ->where('id', $id)
            ->get('ei_mapa_visitacao')
            ->row();

        if ($row) {
            $cliente = $row->cliente;
            $municipio = $row->municipio;
            $unidadeVisitada = $row->unidade_visitada;
        } else {
            $cliente = $this->input->post('cliente');
            $municipio = $this->input->post('municipio');
            $unidadeVisitada = $this->input->post('unidade_visitada');
        }

        $escola = $this->db
            ->where('id', $unidadeVisitada)
            ->get('ei_escolas')
            ->row();

        $busca = [
            'cliente' => $cliente,
            'municipio' => $municipio,
            'unidade_visitada' => $unidadeVisitada,
            'escola' => ($escola->nome ?? ''),
            'mes' => $this->input->post('mes'),
            'ano' => $this->input->post('ano'),
        ];

        $filtro = $this->montarFiltros($busca);

        $data['prestadores_servicos_tratados'] = $filtro['prestadores_servicos_tratados'];

        $data['cliente'] = form_dropdown('cliente', $filtro['clientes'], $cliente, 'onchange="atualizarFiltrosVisitas()" class="form-control"');

        $data['municipio'] = form_dropdown('municipio', $filtro['municipios'], $municipio, 'onchange="atualizarFiltrosVisitas()" class="form-control"');

        $data['unidade_visitada'] = form_dropdown('unidade_visitada', $filtro['unidades_visitadas'], $unidadeVisitada, 'onchange="atualizarFiltrosVisitas()" class="form-control"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    private function montarFiltros(array $busca = []): array
    {
        $supervisores = $this->db
            ->select('d.id, d.nome')
            ->join('ei_escolas b', 'b.id = a.id_escola')
            ->join('ei_coordenacao c', 'c.id = a.id_coordenacao')
            ->join('usuarios d', 'd.id = c.id_usuario')
            ->where('b.id', $busca['unidade_visitada'] ?? null)
            ->order_by('d.nome', 'asc')
            ->get('ei_supervisores a')
            ->result();

        $supervisores = array_column($supervisores, 'nome', 'id');

        $clientes = $this->db
            ->select('id, nome')
            ->order_by('nome', 'asc')
            ->get('ei_diretorias')
            ->result();

        $clientes = array_column($clientes, 'nome', 'id');

        $qb = $this->db
            ->select('a.municipio')
            ->join('ei_diretorias b', 'b.id = a.id_diretoria');
        if ($busca['cliente']) {
            $qb->where('b.id', $busca['cliente'] ?? null);
        }
        $municipios = $qb
            ->group_by('a.municipio')
            ->order_by('a.municipio', 'asc')
            ->get('ei_escolas a')
            ->result();

        $municipios = array_column($municipios, 'municipio', 'municipio');

        $qb = $this->db
            ->select('a.id, a.nome')
            ->join('ei_diretorias b', 'b.id = a.id_diretoria');
        if ($busca['cliente']) {
            $qb->where('b.id', $busca['cliente'] ?? null);
        }
        if ($busca['municipio']) {
            $qb->where('a.municipio', $busca['municipio'] ?? null);
        }
        $unidades_visitadas = $this->db
            ->order_by('a.nome', 'asc')
            ->get('ei_escolas a')
            ->result();

        $unidades_visitadas = array_column($unidades_visitadas, 'nome', 'id');

        $data = [
            'supervisores_visitantes' => ['' => 'selecione...'] + $supervisores,
            'clientes' => ['' => 'selecione...'] + $clientes,
            'municipios' => ['' => 'selecione...'] + $municipios,
            'unidades_visitadas' => ['' => 'selecione...'] + $unidades_visitadas,
        ];

        if (!empty($unidades_visitadas[$busca['unidade_visitada']])) {
            $data['prestadores_servicos_tratados'] = $this->db
                    ->select("GROUP_CONCAT(DISTINCT a.cuidador ORDER BY a.cuidador SEPARATOR ', ') AS cuidador", false)
                    ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
                    ->join('ei_alocacao c', 'c.id = b.id_alocacao')
                    ->where('c.ano', $busca['ano'] ?? null)
                    ->where('c.semestre', !empty($busca['mes']) ? (intval($busca['mes']) > 6 ? 2 : 1) : null)
                    ->where('b.escola', $busca['escola'] ?? null)
                    ->get('ei_alocados a')
                    ->row()
                    ->cuidador ?? null;
        } else {
            $data['prestadores_servicos_tratados'] = null;
        }

        return $data;
    }

    //--------------------------------------------------------------------

    public function ajax_add()
    {
        $data = $this->input->post();

        $escola = $this->db
            ->select('nome')
            ->where('id', $data['unidade_visitada'])
            ->get('ei_escolas')
            ->row();

        $supervisor = $this->db
            ->select('nome')
            ->where('id', $data['id_supervisor_visitante'])
            ->get('usuarios')
            ->row();

        $data['escola'] = $escola->nome ?? null;
        $data['supervisor_visitante'] = $supervisor->nome ?? null;
        $id = $data['id'];
        unset($data['id']);

        if ($data['data_visita']) {
            $data['data_visita'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['data_visita'])));
        }
        if ($data['data_visita_anterior']) {
            $data['data_visita_anterior'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['data_visita_anterior'])));
        } else {
            $data['data_visita_anterior'] = null;
        }
        $data['gastos_materiais'] = str_replace(['.', ','], ['', '.'], $data['gastos_materiais']);

        $status = $this->db->insert('ei_mapa_visitacao', $data);

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_update()
    {
        $data = $this->input->post();

        $escola = $this->db
            ->select('nome')
            ->where('id', $data['unidade_visitada'])
            ->get('ei_escolas')
            ->row();

        $supervisor = $this->db
            ->select('nome')
            ->where('id', $data['id_supervisor_visitante'])
            ->get('usuarios')
            ->row();

        $data['escola'] = $escola->nome ?? null;
        $data['supervisor_visitante'] = $supervisor->nome ?? null;
        $id = $data['id'];
        unset($data['id']);

        if ($data['data_visita']) {
            $data['data_visita'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['data_visita'])));
        } else {
            $data['data_visita'] = null;
        }

        if ($data['data_visita_anterior']) {
            $data['data_visita_anterior'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['data_visita_anterior'])));
        } else {
            $data['data_visita_anterior'] = null;
        }
        $data['gastos_materiais'] = str_replace(['.', ','], ['', '.'], $data['gastos_materiais']);

        $status = $this->db->update('ei_mapa_visitacao', $data, ['id' => $id]);

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_delete()
    {
        $status = $this->db->delete('ei_mapa_visitacao', ['id' => $this->input->post('id')]);

        echo json_encode(['status' => $status !== false]);
    }

}
