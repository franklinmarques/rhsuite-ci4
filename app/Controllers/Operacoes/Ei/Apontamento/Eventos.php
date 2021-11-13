<?php

namespace App\Controllers\Ei\Apontamento;

use App\Controllers\BaseController;

class Eventos extends BaseController
{

    public function limpar_mes()
    {
        $ano = $this->input->post('ano');
        $mes = $this->input->post('mes');
        $idEscola = $this->input->post('id_escola');
        $idCuidador = $this->input->post('id_cuidador');
        $periodo = $this->input->post('periodo');

        $alocacao = $this->db
            ->where('id_empresa', $this->session->userdata('empresa'))
            ->where('depto', $this->input->post('depto'))
            ->where('id_diretoria', $this->input->post('diretoria'))
            ->where('id_supervisor', $this->input->post('supervisor'))
            ->where('ano', $ano)
            ->where('semestre', $this->input->post('semestre'))
            ->get('ei_alocacao')
            ->row();

        if (empty($alocacao)) {
            exit(json_encode(['erro' => 'Nenhuma alocação encontrada.']));
        }

        $qb = $this->db
            ->select('d.id, c.id AS id_alocado, b.id_alocacao, c.id_cuidador, c.id_alocacao_escola', false)
            ->join('ei_alocacao_escolas b', 'b.id_alocacao = a.id', 'left')
            ->join('ei_alocados c', 'c.id_alocacao_escola = b.id', 'left')
            ->join('ei_alocados_horarios e', 'e.id_alocado = c.id', 'left')
            ->join('ei_apontamento d', "d.id_alocado = c.id AND MONTH(d.data) = '{$mes}' AND (d.periodo = e.periodo OR d.periodo IS NULL)", 'left', false)
            ->where('a.id', $alocacao->id);
        if ($idEscola) {
            $qb->where('b.id_escola', $idEscola);
        }
        if ($idCuidador) {
            $qb->where('c.id_cuidador', $idCuidador);
        }
        if ($periodo) {
            $qb->where('e.periodo', $periodo);
        }
        $eventos = $qb
            ->group_by('d.id')
            ->get('ei_alocacao a')
            ->result_array();

        $qtdeEscolas = array_filter(array_unique(array_column($eventos, 'id_alocacao')));
        $idAlocados = array_filter(array_unique(array_column($eventos, 'id_alocado')));

        if (empty($qtdeEscolas)) {
            exit(json_encode(['erro' => 'Nenhuma escola alocada.']));
        }
        if (empty($idAlocados)) {
            exit(json_encode(['erro' => 'Nenhum cuidador alocado.']));
        }

        $mes = $this->input->post('mes');

        $this->db->trans_start();

        $this->db
            ->where_in('id_alocado', $idAlocados + [0])
            ->group_start()
            ->where('periodo', $periodo)
            ->or_where("CHAR_LENGTH('{$periodo}') =", 0)
            ->group_end()
            ->where('MONTH(data)', $mes)
            ->delete('ei_apontamento');

        $qb = $this->db;
        if ($periodo) {
            $qb->group_start()
                ->where("horario_entrada_{$periodo} !=")
                ->or_where("horario_entrada_real_{$periodo} !=")
                ->or_where("horario_saida_{$periodo} !=")
                ->or_where("horario_saida_real_{$periodo} !=")
                ->or_where("status_entrada_{$periodo} !=")
                ->or_where("status_saida_{$periodo} !=")
                ->group_end();
            foreach (array_diff(['1', '2', '3'], [$periodo]) as $p) {
                $qb->where("horario_entrada_{$p}")
                    ->where("horario_entrada_real_{$p}")
                    ->where("horario_saida_{$p}")
                    ->where("horario_saida_real_{$p}")
                    ->where("status_entrada_{$p}")
                    ->where("status_saida_{$p}");
            }
        }
        if ($idCuidador) {
            $qb->where('id_usuario', $idCuidador);
        }
        if ($idEscola) {
            $qb->where('id_escola', $idEscola);
        }
        $qb->where('YEAR(data_evento)', $ano)
            ->where('MONTH(data_evento)', $mes)
            ->delete('ei_usuarios_frequencias');

        if ($periodo) {
            $qb = $this->db
                ->set('horario_entrada_' . $periodo, null)
                ->set('horario_saida_' . $periodo, null)
                ->set('horario_entrada_real_' . $periodo, null)
                ->set('horario_saida_real_' . $periodo, null)
                ->set('status_entrada_' . $periodo, null)
                ->set('status_saida_' . $periodo, null)
                ->set('automatico_entrada_' . $periodo, null)
                ->set('automatico_saida_' . $periodo, null);
            if ($idCuidador) {
                $qb->where('id_usuario', $idCuidador);
            }
            if ($idEscola) {
                $qb->where('id_escola', $idEscola);
            }
            $qb->where('YEAR(data_evento)', $ano)
                ->where('MONTH(data_evento)', $mes)
                ->update('ei_usuarios_frequencias');

            $qb = $this->db
                ->set('periodo_atual', "(CASE WHEN horario_entrada_3 IS NOT NULL OR status_entrada_3 IS NOT NULL THEN 3
                                              WHEN horario_entrada_2 IS NOT NULL OR status_entrada_2 IS NOT NULL THEN 2
                                              WHEN horario_entrada_1 IS NOT NULL OR status_entrada_1 IS NOT NULL THEN 1 
                                              ELSE 0 END)", false);
            if ($idCuidador) {
                $qb->where('id_usuario', $idCuidador);
            }
            if ($idEscola) {
                $qb->where('id_escola', $idEscola);
            }
            $qb->where('YEAR(data_evento)', $ano)
                ->where('MONTH(data_evento)', $mes)
                ->update('ei_usuarios_frequencias');
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() == false) {
            exit(json_encode(['erro' => 'Não foi possível limpar os eventos do mês']));
        }

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    public function notificar_colaboradores_sem_evento_atual()
    {
        parse_str($this->input->post('busca'), $busca);
        $periodo = $this->input->post('periodo');
        $semestre = $busca['semestre'] ?? null;
        if (empty($semestre)) {
            $semestre = intval($busca['mes']) > 7 ? '2' : '1';
        }

        $alocacao = $this->db
            ->where('id_empresa', $this->session->userdata('empresa'))
            ->where('depto', $busca['depto'])
            ->where('id_diretoria', $busca['diretoria'])
            ->where('id_supervisor', $busca['supervisor'])
            ->where('ano', $busca['ano'])
            ->where('semestre', $semestre)
            ->get('ei_alocacao')
            ->row();

        if (empty($alocacao)) {
            exit(json_encode(['erro' => 'Alocação semestral não encontrada.']));
        }

        $alocados = $this->db
            ->select('c.email')
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('usuarios c', 'c.id = a.id_cuidador')
            ->join('ei_alocados_horarios d', "d.id_alocado = a.id AND d.periodo = '{$periodo}'")
            ->join('ei_apontamento e', 'e.id_alocado = a.id AND e.data = NOW() AND (e.periodo = d.periodo OR e.periodo IS NULL)', 'left')
            ->where('b.id_alocacao', $alocacao->id)
            ->where('e.id', null)
            ->group_by('a.id')
            ->get('ei_alocados a')
            ->result();

        if (empty($alocados)) {
            exit(json_encode(['erro' => 'Nenhum colaborador sem evento foi encontrado.']));
        }

        $notificacoesBemSucedidas = 0;
        $emailRemetente = $this->session->userdata('email');
        $nomeRemetente = $this->session->userdata('nome');
        $titulo = 'Ausência de evento de apontamento';

        switch ($periodo) {
            case '3':
                $nomePeriodo = 'noite';
                break;
            case '2':
                $nomePeriodo = 'tarde';
                break;
            case '1':
                $nomePeriodo = 'manhã';
        }

        $this->load->library('email');

        $totalAlocados = count((array)$alocados);

        foreach ($alocados as $k => $alocado) {
            if ($k == ($totalAlocados - 1)) {
                $this->email->cc('mhffortes@hotmail.com');
            }
            $status = $this->email
                ->from($emailRemetente, $nomeRemetente)
                ->to($alocado->email)
                ->subject($titulo)
                ->set_mailtype('html')
                ->message($this->load->view('ei/colaboradores_sem_evento_atual_email', ['nome_periodo' => $nomePeriodo], true))
                ->send();

            if ($status) {
                $notificacoesBemSucedidas++;
            }
        }

        if ($notificacoesBemSucedidas == 0) {
            exit(json_encode(['erro' => 'Nenhum colaborador sem evento pôde ser notificado.']));
        } elseif ($notificacoesBemSucedidas != $totalAlocados) {
            exit(json_encode(['erro' => 'Alguns colaboradores sem evento não puderam ser notificados.']));
        }

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    public function save_frequencias_fim_semana()
    {
        $busca = $this->input->post();
        $semestre = $busca['semestre'] ?? null;
        if (empty($semestre)) {
            $semestre = intval($busca['mes']) > 7 ? '2' : '1';
        }

        $domingos = [];
        $sabados = [];
        $finaisDeSemana = [];
        $timestampFimDoMes = strtotime(date('Y-m-t', mktime(0, 0, 0, (int)$busca['mes'], 1, (int)$busca['ano'])));
        $timestampDataAtual = strtotime(date('Y-m-d'));
        if (floatval(date('Y.m', $timestampFimDoMes)) > floatval(date('Y.m', $timestampDataAtual))) {
            exit(json_encode(['erro' => 'Mês/ano inválidos para cadastro de faltas.']));
        }
        $ultimoDiaValidoDoMes = (int)date('d', min($timestampFimDoMes, $timestampDataAtual));
        for ($dia = 1; $dia <= $ultimoDiaValidoDoMes; $dia++) {
            $semana = date('w', mktime(0, 0, 0, (int)$busca['mes'], $dia, (int)$busca['ano']));
            if (in_array($semana, ['0', '6'])) {
                $finaisDeSemana[] = $dia;
                if ($semana === '6') {
                    $sabados[] = $dia;
                } else {
                    $domingos[] = $dia;
                }
            }
        }

        $this->db->trans_start();

        $alocacao = $this->db
            ->where('id_empresa', $this->session->userdata('empresa'))
            ->where('depto', $busca['depto'])
            ->where('id_diretoria', $busca['diretoria'])
            ->where('id_supervisor', $busca['supervisor'])
            ->where('ano', $busca['ano'])
            ->where('semestre', $semestre)
            ->get('ei_alocacao')
            ->row();

        $alocados = $this->db
            ->select('a.id')
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->where('b.id_alocacao', $alocacao->id)
            ->group_by('a.id')
            ->get('ei_alocados a')
            ->result();

        $apontamentos = [];
        $medicoes = [];
        foreach ($alocados as $alocado) {
            foreach ($finaisDeSemana as $finalDeSemana) {
                $status = in_array($finalDeSemana, $domingos) ? 'DG' : 'SB';
                $finalDeSemanaAnterior = $finalDeSemana - ($status == 'DG' ? 1 : 0);

                $resultApontamentos = $this->db
                    ->select('a.id_alocado, a.periodo')
                    ->select("STR_TO_DATE('{$busca['ano']}-{$busca['mes']}-{$finalDeSemana}', '%Y-%m-%e') AS data", false)
                    ->select("'{$status}' AS status", false)
                    ->join('ei_alocados b', 'b.id = a.id_alocado')
                    ->join('ei_apontamento c', "c.id_alocado = b.id AND MONTH(c.data) = '{$busca['mes']}' AND DAY(c.data) = '{$finalDeSemana}' AND (c.periodo = a.periodo OR c.periodo IS NULL)", 'left')
                    ->where('b.id', $alocado->id)
                    ->where_not_in('a.dia_semana', [0, 6])
                    ->where('c.id IS NULL')
                    ->group_by(['b.id', 'a.periodo'])
                    ->get('ei_alocados_horarios a')
                    ->result_array();

                foreach ($resultApontamentos as $resultApontamento) {
                    $apontamentos[] = $resultApontamento;
                }

                $subquery = $this->db
                    ->select('a.id AS id_alocado, a.id_cuidador AS id_usuario, b.id_escola')
                    ->select(["STR_TO_DATE('{$busca['ano']}-{$busca['mes']}-{$finalDeSemana}', '%Y-%m-%e') AS data_evento"], false)
                    ->select("(SELECT COUNT(d2.id) FROM ei_alocados_horarios d2 WHERE d2.id_alocado = a.id AND d2.periodo = 1) AS periodo_manha", false)
                    ->select("(SELECT COUNT(d3.id) FROM ei_alocados_horarios d3 WHERE d3.id_alocado = a.id AND d3.periodo = 2) AS periodo_tarde", false)
                    ->select("(SELECT COUNT(d4.id) FROM ei_alocados_horarios d4 WHERE d4.id_alocado = a.id AND d4.periodo = 3) AS periodo_noite", false)
                    ->select("'{$status}' AS status", false)
                    ->select(["IF(e.status IS NULL OR e.status = 'SB', 1, 0) AS status_anterior_periodo1"], false)
                    ->select(["IF(f.status IS NULL OR f.status = 'SB', 1, 0) AS status_anterior_periodo2"], false)
                    ->select(["IF(g.status IS NULL OR g.status = 'SB', 1, 0) AS status_anterior_periodo3"], false)
                    ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
                    ->join('ei_apontamento d', "d.id_alocado = a.id AND MONTH(d.data) = '{$busca['mes']}' AND DAY(d.data) = '{$finalDeSemana}'", 'left', false)
                    ->join('ei_apontamento e', "e.id_alocado = a.id AND MONTH(e.data) = '{$busca['mes']}' AND DAY(e.data) = '{$finalDeSemanaAnterior}' AND (e.periodo = 1 OR e.periodo IS NULL)", 'left', false)
                    ->join('ei_apontamento f', "f.id_alocado = a.id AND MONTH(f.data) = '{$busca['mes']}' AND DAY(f.data) = '{$finalDeSemanaAnterior}' AND (f.periodo = 2 OR f.periodo IS NULL)", 'left', false)
                    ->join('ei_apontamento g', "g.id_alocado = a.id AND MONTH(g.data) = '{$busca['mes']}' AND DAY(g.data) = '{$finalDeSemanaAnterior}' AND (g.periodo = 3 OR g.periodo IS NULL)", 'left', false)
                    ->where('a.id', $alocado->id)
//                    ->where('d.id IS NULL')
                    ->group_by(['a.id_cuidador', 'd.data'])
                    ->get_compiled_select('ei_alocados a');

                $resultMedicao = $this->db
                    ->select('t.id, s.id_usuario, s.data_evento, s.id_escola')
                    ->select('t.status_entrada_1, t.status_entrada_2, t.status_entrada_3')
                    ->select('t.status_saida_1, t.status_saida_2, t.status_saida_3')
                    ->select(['IF(s.periodo_manha > 0 AND status_anterior_periodo1, s.status, NULL) AS status_evento_entrada_1'], false)
                    ->select(['IF(s.periodo_manha > 0 AND status_anterior_periodo1, s.status, NULL) AS status_evento_saida_1'], false)
                    ->select(['IF(s.periodo_tarde > 0 AND status_anterior_periodo2, s.status, NULL) AS status_evento_entrada_2'], false)
                    ->select(['IF(s.periodo_tarde > 0 AND status_anterior_periodo2, s.status, NULL) AS status_evento_saida_2'], false)
                    ->select(['IF(s.periodo_noite > 0 AND status_anterior_periodo3, s.status, NULL) AS status_evento_entrada_3'], false)
                    ->select(['IF(s.periodo_noite > 0 AND status_anterior_periodo3, s.status, NULL) AS status_evento_saida_3'], false)
                    ->from("({$subquery}) s")
                    ->join('ei_usuarios_frequencias t', 't.id_usuario = s.id_usuario AND t.data_evento = s.data_evento AND s.id_escola = t.id_escola', 'left')
//                    ->where('t.id IS NULL')
                    ->get()
                    ->row_array();

                if ($resultMedicao) {
                    $resultMedicao['status_entrada_1'] = $resultMedicao['status_entrada_1'] ?? $resultMedicao['status_evento_entrada_1'];
                    $resultMedicao['status_entrada_2'] = $resultMedicao['status_entrada_2'] ?? $resultMedicao['status_evento_entrada_2'];
                    $resultMedicao['status_entrada_3'] = $resultMedicao['status_entrada_3'] ?? $resultMedicao['status_evento_entrada_3'];
                    $resultMedicao['status_saida_1'] = $resultMedicao['status_saida_1'] ?? $resultMedicao['status_evento_saida_1'];
                    $resultMedicao['status_saida_2'] = $resultMedicao['status_saida_2'] ?? $resultMedicao['status_evento_saida_2'];
                    $resultMedicao['status_saida_3'] = $resultMedicao['status_saida_3'] ?? $resultMedicao['status_evento_saida_3'];
                    unset($resultMedicao['status_evento_entrada_1']);
                    unset($resultMedicao['status_evento_entrada_2']);
                    unset($resultMedicao['status_evento_entrada_3']);
                    unset($resultMedicao['status_evento_saida_1']);
                    unset($resultMedicao['status_evento_saida_2']);
                    unset($resultMedicao['status_evento_saida_3']);
                    $medicoes[] = $resultMedicao;
                }
            }
        }

        foreach ($apontamentos as $apontamento) {
            $this->db->insert('ei_apontamento', $apontamento);
        }

        foreach ($medicoes as $medicao) {
            if ($medicao['id']) {
                $this->db->update('ei_usuarios_frequencias', $medicao, ['id' => $medicao['id']]);
            } else {
                $this->db->insert('ei_usuarios_frequencias', $medicao);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() == false) {
            exit(json_encode(['erro' => 'Erro ao criar eventos de medição']));
        }

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    public function preparar_faltas()
    {
        $mes = $this->input->post('mes');
        $ano = $this->input->post('ano');

        $alocacao = $this->db
            ->select('id')
            ->where('id_empresa', $this->session->userdata('empresa'))
            ->where('depto', $this->input->post('depto'))
            ->where('id_diretoria', $this->input->post('diretoria'))
            ->where('id_supervisor', $this->input->post('supervisor'))
            ->where('ano', $ano)
            ->where('semestre', $this->input->post('semestre'))
            ->get('ei_alocacao')
            ->row();

        if (empty($alocacao)) {
            exit(json_encode(['erro' => 'Nenhuma alocação encontrada.']));
        }

        $timestampFimDoMes = strtotime(date('Y-m-t', mktime(0, 0, 0, (int)$mes, 1, (int)$ano)));
        $timestampDataAtual = strtotime(date('Y-m-d'));
        if (floatval(date('Y.m', $timestampFimDoMes)) > floatval(date('Y.m', $timestampDataAtual))) {
            exit(json_encode(['erro' => 'Mês/ano inválidos para cadastro de faltas.']));
        }
        $qtdeDiasValidosMes = date('d', min($timestampFimDoMes, $timestampDataAtual));
        $dias = [];
        for ($i = 1; $i <= $qtdeDiasValidosMes; $i++) {
            $dia = str_pad($i, 2, '0', STR_PAD_LEFT);
            $dias[$dia] = $dia;
        }
        $data = [
            'id_alocacao' => $alocacao->id,
            'mes' => $mes,
            'dias' => form_dropdown('', $dias, '01'),
        ];

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function salvar_faltas()
    {
        $idAlocacao = $this->input->post('id_alocacao');
        $diasUteis = $this->input->post('dias_uteis');
        $dia = $this->input->post('dia');
        $mes = $this->input->post('mes');

        $alocacao = $this->db
            ->where('id', $idAlocacao)
            ->get('ei_alocacao')
            ->row();

        $ano = $alocacao->ano;

        $dias = [];
        if ($diasUteis) {
            $timestampFimDoMes = strtotime(date('Y-m-t', mktime(0, 0, 0, (int)$mes, 1, (int)$ano)));
            $timestampDataAtual = strtotime(date('Y-m-d'));
            if (floatval(date('Y.m', $timestampFimDoMes)) > floatval(date('Y.m', $timestampDataAtual))) {
                exit(json_encode(['erro' => 'Mês/ano inválidos para cadastro de faltas.']));
            }
            $ultimoDiaValidoDoMes = (int)date('d', min($timestampFimDoMes, $timestampDataAtual));
            for ($diaMes = 1; $diaMes <= $ultimoDiaValidoDoMes; $diaMes++) {
                $semana = date('w', mktime(0, 0, 0, (int)$mes, $diaMes, (int)$ano));
                if (in_array($semana, ['0', '6']) == false) {
                    $dias[] = $diaMes;
                }
            }
        } else {
            $dias = [$dia];
        }

        $rowsGroup = [];
        foreach ($dias as $diaNovo) {
            $data = date('Y-m-d', mktime(0, 0, 0, (int)$mes, (int)$diaNovo, (int)$ano));
            $diaSemana = date('w', strtotime($data));
            $rows = $this->db
                ->select("a.id AS id_alocado, '{$data}' AS data, 'FA' AS status", false)
                ->select('d.periodo, a.id_cuidador AS id_usuario')
                ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
                ->join('ei_alocacao c', 'c.id = b.id_alocacao')
                ->join('ei_alocados_horarios d', 'd.id_alocado = a.id')
                ->join('ei_apontamento e', "e.id_alocado = a.id AND e.data = '{$data}' AND (e.periodo = d.periodo OR e.periodo IS NULL)", 'left')
                ->where('c.id', $idAlocacao)
                ->where('d.dia_semana', $diaSemana)
                ->where('e.id', null)
                ->group_by(['a.id', 'd.periodo'])
                ->get('ei_alocados a')
                ->result_array();

            foreach ($rows as $row) {
                $rowsGroup[] = $row;
            }
        }

        if (empty($rowsGroup)) {
            exit(json_encode(['erro' => 'Todos os colaboradores já possuem evento no dia selecionado.']));
        }

        $this->load->model('ei_apontamento_model', 'apontamento');

        $this->db->trans_start();
        foreach ($rowsGroup as $rowGroup) {
            $this->apontamento->insert($rowGroup) or exit(json_encode(['erro' => $this->apontamento->errors()]));
        }
        $this->db->trans_complete();

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    public function ajax_list()
    {
        parse_str($this->input->post('busca'), $busca);
        $semestre = $busca['semestre'] ?? null;
        $funcao = $this->input->post('funcao');
        if (empty($semestre)) {
            $semestre = intval($busca['mes']) > 7 ? '2' : '1';
        }
        $dataInicioMes = "{$busca['ano']}-{$busca['mes']}-01";
        $dataTerminoMes = date('Y-m-t', strtotime($dataInicioMes));

        $idMes = $this->getIdMes($busca['mes'], $semestre);
        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';

        $query = $this->db
            ->select('a.id, b.municipio, b.escola, b.id_escola, b.ordem_servico, a.id_cuidador, d.periodo')
            ->select(["CASE WHEN MONTH(d.data_substituicao1) < '{$busca['mes']}' || MONTH(d.data_substituicao2) < '{$busca['mes']}' THEN NULL ELSE a.cuidador END AS cuidador"], false)
            ->select("IFNULL(GROUP_CONCAT(DISTINCT h.aluno ORDER BY h.aluno SEPARATOR ';<br>'), '') AS aluno", false)
            ->select("GROUP_CONCAT(DISTINCT h.id_aluno ORDER BY h.id_aluno SEPARATOR ',') AS id_alunos", false)
            ->select("(CASE d.periodo WHEN 0 THEN 'Madrugada' WHEN 1 THEN 'Manhã' WHEN 2 THEN 'Tarde' WHEN 3 THEN 'Noite' END) AS nome_periodo", false)
            ->select(["CASE WHEN MONTH(d.data_substituicao1) <= '{$busca['mes']}' THEN e.nome ELSE NULL END AS cuidador_sub1"], false)
            ->select(["CASE WHEN MONTH(d.data_substituicao2) <= '{$busca['mes']}' THEN f.nome ELSE NULL END AS cuidador_sub2"], false)
            ->select("h.id AS id_aluno_matriculado, COUNT(DISTINCT(h.aluno)) AS total_alunos", false)
            ->select('c2.status, e.status AS status_sub1, f.status AS status_sub2', false)
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->join('usuarios c2', 'c2.id = a.id_cuidador', 'left')
            ->join('ei_alocados_horarios d', "d.id_alocado = a.id", 'left')
            ->join('usuarios e', 'e.id = d.id_cuidador_sub1', 'left')
            ->join('usuarios f', 'f.id = d.id_cuidador_sub2', 'left')
            ->join('ei_matriculados_turmas g', "g.id_alocado_horario = d.id", 'left')
            ->join('ei_matriculados h', 'h.id = g.id_matriculado AND h.id_alocacao_escola = b.id', 'left')
            ->where('c.id_empresa', $this->session->userdata('empresa'))
            ->where('c.depto', $busca['depto'])
            ->where('c.id_diretoria', $busca['diretoria'])
            ->where('c.id_supervisor', $busca['supervisor'])
            ->where('c.ano', $busca['ano'])
            ->where('c.semestre', $semestre)
            ->group_start()
            ->where('d.funcao' . $mesCargoFuncao, $funcao)
            ->or_where("CHAR_LENGTH('{$funcao}') =", 0)
            ->group_end()
            ->group_start()
            ->where('d.data_inicio_real <=', $dataTerminoMes)
            ->or_where('d.data_inicio_real', null)
            ->group_end()
            ->group_start()
            ->where('d.data_termino_real >=', $dataInicioMes)
            ->or_where('d.data_termino_real', null)
            ->group_end()
            ->group_by(['a.id', 'a.cuidador', 'd.periodo', 'd.cargo' . $mesCargoFuncao, 'd.funcao' . $mesCargoFuncao])
            ->order_by('a.id')
            ->order_by('a.cuidador')
            ->order_by('d.periodo')
            ->order_by('d.cargo' . $mesCargoFuncao)
            ->order_by('d.funcao' . $mesCargoFuncao)
            ->get('ei_alocados a');

        $options = [
            'search' => ['municipio', 'escola', 'cuidador'],
            'order' => ['municipio', 'cuidador', 'aluno', 'escola', 'ordem_servico'],
        ];
        $this->load->library('dataTables', $options);

        $output = $this->datatables->generate($query);

        $alocados = $output->data;
        $output->totalFuncionarios = count(array_filter(array_column($alocados, 'id_cuidador')));
        $output->totalAlunos = array_sum(array_column($alocados, 'total_alunos'));

        $rowsEventos = $this->db
            ->select('b.id_cuidador, c.id_escola')
            ->select("b.id, DATE_FORMAT(a.data, '%d') AS dia", false)
            ->select("IFNULL(a.periodo, '') AS periodo, a.status", false)
            ->select(["IFNULL((SELECT GROUP_CONCAT(DISTINCT m.aluno ORDER BY m.aluno SEPARATOR ';<br>') 
                        FROM ei_matriculados m
                        LEFT JOIN ei_matriculados_turmas n ON n.id_matriculado = m.id
                        LEFT JOIN ei_alocados_horarios h ON h.id = n.id_alocado_horario
                        WHERE m.id_alocacao_escola = c.id AND 
                              h.id_alocado = b.id AND 
                              h.periodo = a.periodo), '') AS aluno"], false)
            ->select("TIME_FORMAT(a.desconto, '%H:%i') AS desconto", false)
            ->select("IF(DATE_FORMAT(a.data, '%w') IN (0, 6) AND a.status NOT IN ('EE', 'HE', 'SL'), 0, 1) AS dia_util", false)
            ->select("(CASE a.periodo WHEN 3 THEN a.horario_entrada_3 
                                      WHEN 2 THEN a.horario_entrada_2 
                                      WHEN 1 THEN a.horario_entrada_1 
                                      END) AS horario_entrada", false)
            ->select("(CASE a.periodo WHEN 3 THEN a.horario_saida_3 
                                      WHEN 2 THEN a.horario_saida_2 
                                      WHEN 1 THEN a.horario_saida_1 
                                      END) AS horario_saida", false)
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
//            ->join('ei_alocados_horarios e', 'e.id_alocado = b.id', 'left')
            ->where('d.ano', $busca['ano'])
            ->where('d.semestre', $semestre)
            ->where('YEAR(a.data)', $busca['ano'])
            ->where('MONTH(a.data)', $busca['mes'])
            ->where_in('b.id', $alocados ? array_unique(array_column($alocados, 'id')) : [0])
            ->group_by(['b.id', 'a.periodo', 'aluno', 'a.data'])
            ->order_by('b.id', 'asc')
            ->order_by('a.periodo', 'asc')
            ->order_by('a.data', 'asc')
//            ->group_by('a.id')
            ->get('ei_apontamento a')
            ->result();

        $apontamento = [];
        $colaboradorAtivo = [USUARIO_ATIVO => 1, USUARIO_EM_EXPERIENCIA => 1];
        $nomeDoStatus = [
            'FA' => 'Falta',
            'PV' => 'Posto vago',
            'AT' => 'Atraso',
            'SA' => 'Saída antecipada',
            'FE' => 'Feriado',
            'EM' => 'Emenda de feriado',
            'RE' => 'Recesso',
            'AF' => 'Aluno ausente',
            'EE' => 'Evento Extra',
            'HE' => 'Horas de Estudo',
            'SL' => 'Sábado Letivo',
            'AP' => 'Apontamento positivo',
            'AN' => 'Apontamento negativo',
            'PN' => 'Presença Normal',
            'SB' => 'Sábado',
            'DG' => 'Domingo',
        ];

        foreach ($rowsEventos as $rowEvento) {
            $statusEvento = $nomeDoStatus[$rowEvento->status] ?? null;
            $apontamento[$rowEvento->id][$rowEvento->periodo][$rowEvento->aluno][intval($rowEvento->dia)] = [
                'id' => $rowEvento->id,
                'periodo' => $rowEvento->periodo,
                'dia' => $rowEvento->dia,
                'status' => $rowEvento->status,
                'tipo' => $statusEvento,
                'desconto' => $rowEvento->desconto,
                'entrada' => $rowEvento->dia_util ? $rowEvento->horario_entrada : 1,
                'saida' => $rowEvento->dia_util ? $rowEvento->horario_saida : 1,
            ];
        }
//print_r($apontamento);exit;
        $data = [];

        foreach ($alocados as $alocado) {
            $row = [
                $alocado->id,
                "<strong>Municipio:</strong> {$alocado->municipio}&emsp;
                <strong>Escola:</strong> {$alocado->escola}<br>
                <strong>Ordem de serviço:</strong> {$alocado->ordem_servico}",
                implode(';<br>', array_unique(array_filter([$alocado->cuidador, $alocado->cuidador_sub1, $alocado->cuidador_sub2]))),
                (strlen($alocado->nome_periodo) and $alocado->aluno) ? $alocado->aluno . ' - ' . $alocado->nome_periodo : null,
            ];
            for ($i = 1; $i <= 31; $i++) {
                $row[] = $apontamento[$alocado->id][$alocado->periodo][$alocado->aluno][$i] ?? $apontamento[$alocado->id][$alocado->periodo][''][$i] ?? $apontamento[$alocado->id][''][''][$i] ?? [];
            }
            $row[] = $alocado->periodo;
            $row[] = $alocado->id_aluno_matriculado;
            $row[] = $colaboradorAtivo[$alocado->status_sub2] ?? $colaboradorAtivo[$alocado->status_sub1] ?? $colaboradorAtivo[$alocado->status] ?? null;
            $row[] = $alocado->id_alunos;

            $data[] = $row;
        }

        $output->data = $data;

        $funcoes = $this->db
            ->select("a.cargo{$mesCargoFuncao} AS cargo, a.funcao{$mesCargoFuncao} AS funcao", false)
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->where('d.id_empresa', $this->session->userdata('empresa'))
            ->where('d.depto', $busca['depto'])
            ->where('d.id_diretoria', $busca['diretoria'])
            ->where('d.id_supervisor', $busca['supervisor'])
            ->where('d.ano', $busca['ano'])
            ->where('d.semestre', $semestre)
            ->group_start()
            ->where('a.data_inicio_real <=', $dataTerminoMes)
            ->or_where('a.data_inicio_real', null)
            ->group_end()
            ->group_start()
            ->where('a.data_termino_real >=', $dataInicioMes)
            ->or_where('a.data_termino_real', null)
            ->group_end()
            ->group_by(['a.cargo' . $mesCargoFuncao, 'a.funcao' . $mesCargoFuncao])
            ->order_by('a.cargo' . $mesCargoFuncao, 'asc')
            ->order_by('a.funcao' . $mesCargoFuncao, 'asc')
            ->get('ei_alocados_horarios a')
            ->result_array();

        $funcoes = ['' => 'Todas'] + array_column($funcoes, 'funcao', 'funcao');
        $output->funcoes = form_dropdown('', $funcoes, $funcao);

        $this->load->library('Calendar');
        $dias_semana = $this->calendar->get_day_names('long');
        $semana = [];
        for ($i = 1; $i <= 7; $i++) {
            $semana[$i] = $dias_semana[date('w', mktime(0, 0, 0, $busca['mes'], $i, $busca['ano']))];
        }

        $nomeSemestre = '';
        if ($busca['mes'] == 7) {
            $nomeSemestre = " - {$semestre}&ordm; semestre";
        }

        $output->calendar = [
            'mes' => $busca['mes'],
            'ano' => $busca['ano'],
            'mes_ano' => $this->calendar->get_month_name($busca['mes']) . ' ' . $busca['ano'] . $nomeSemestre,
            'qtde_dias' => date('t', mktime(0, 0, 0, $busca['mes'], 1, $busca['ano'])),
            'semana' => $semana,
        ];

        echo json_encode($output);
    }

    //--------------------------------------------------------------------

    private function getIdMes(?string $mes, ?int $semestre): int
    {
        $semestre = intval($mes) > 7 ? 2 : (intval($mes) < 7 ? 1 : $semestre);
        return $mes - ($semestre > 1 ? 6 : 0);
    }

    //--------------------------------------------------------------------

    public function replicar_status_dia()
    {
        parse_str($this->input->post('eventos'), $eventos);
        parse_str($this->input->post('busca'), $busca);

        $data = $this->db
            ->select(["a.id AS id_alocado, '{$eventos['data']}' AS data, '{$eventos['status']}' AS status"], false)
            ->select(['NULL AS desconto, NULL AS desconto_sub1, NULL AS desconto_sub2'], false)
            ->select(["IFNULL((SELECT IF(COUNT(IF(x.data_substituicao1 <= '{$eventos['data']}', 0, 1)) > 0, NULL, x.id_cuidador_sub1)
							   FROM ei_alocados_horarios x 
							   WHERE x.id_alocado = a.id
							   GROUP BY x.id_alocado), a.id_cuidador) AS id_usuario"], false)
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->join('ei_apontamento d', "d.id_alocado = a.id AND d.data = '{$eventos['data']}'", 'left')
            ->where('c.id_empresa', $this->session->userdata('empresa'))
            ->where('c.depto', $busca['depto'])
            ->where('c.id_diretoria', $busca['diretoria'])
            ->where('c.id_supervisor', $busca['supervisor'])
            ->where('c.ano', $busca['ano'])
            ->where('c.semestre', $busca['semestre'])
            ->where('d.data', null)
            ->group_by('a.id')
            ->get('ei_alocados a')
            ->result_array();

        $this->load->model('ei_apontamento_model', 'apontamento');

        $this->db->trans_start();
        foreach ($data as $row) {
            $this->apontamento->insert($row);
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() == false) {
            exit(json_encode(['erro' => 'Não foi possível replicar os eventos.']));
        }

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    public function limpar_status_dia()
    {
        parse_str($this->input->post('eventos'), $eventos);
        parse_str($this->input->post('busca'), $busca);
        $busca['id_diretoria'] = $busca['diretoria'];
        $busca['id_supervisor'] = $busca['supervisor'];
        unset($busca['diretoria'], $busca['supervisor'], $busca['mes']);

        $data = $this->db
            ->select('a.id')
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->where($busca)
            ->where('a.data', $eventos['data'])
            ->where('a.status', $eventos['status'])
            ->get('ei_apontamento a')
            ->result();

        $this->db->trans_start();
        foreach ($data as $row) {
            $this->apontamento->delete($row->id);
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() == false) {
            exit(json_encode(['erro' => 'Não foi possível limpar os eventos.']));
        }

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    public function ajax_edit_cuidador()
    {
        $idAlocado = $this->input->post('id_alocado');

        $alocacao = $this->db
            ->select('c.id, c.depto, c.id_diretoria, c.ano, c.semestre, a.id_cuidador, a.cuidador, d.telefone, d.sexo')
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->join('usuarios d', 'd.id = a.id_cuidador')
            ->where('a.id', $idAlocado)
            ->get('ei_alocados a')
            ->row();

        $sql = "SELECT a.id, a.nome, CONCAT(a.cargo, '/', a.funcao) AS cargo, a.funcao 
                FROM usuarios a
                INNER JOIN ei_ordem_servico_profissionais b ON b.id_usuario = a.id
                INNER JOIN ei_ordem_servico_escolas c ON c.id = b.id_ordem_servico_escola
                INNER JOIN ei_ordem_servico d ON d.id = c.id_ordem_servico
                INNER JOIN ei_contratos e ON e.id = d.id_contrato
                INNER JOIN ei_diretorias f ON f.id = e.id_cliente
                WHERE f.id = '{$alocacao->id_diretoria}' AND 
                      f.depto = '{$alocacao->depto}' AND 
                      d.ano = '{$alocacao->ano}' AND 
                      d.semestre = '{$alocacao->semestre}' AND 
                      a.id NOT IN (SELECT x.id_cuidador
                                   FROM ei_alocados x 
                                   INNER JOIN ei_alocacao_escolas y 
                                              ON y.id = x.id_alocacao_escola
                                   WHERE y.id_alocacao = '{$alocacao->id}')
                ORDER BY a.nome ASC";
        $rows = $this->db->query($sql)->result();
        $idProfissionais = ['' => 'selecione...'] + array_column($rows, 'nome', 'id');
        $cargoFuncao = ['' => 'Todos'] + array_column($rows, 'cargo', 'funcao');
        $municipio = ['' => 'Todos'] + array_column($rows, 'municipio', 'municipio');

        $data = ['cuidador_antigo' => $alocacao->cuidador];

        $data['cargo_funcao'] = form_dropdown('', $cargoFuncao, '');

        $data['municipio'] = form_dropdown('', $municipio, '');

        $data['id_cuidador'] = form_dropdown('', $idProfissionais, $alocacao->id_cuidador);

        $data['telefone'] = '+55 ' . str_replace(['+55 ', '+55'], '', $alocacao->telefone);

        if ($alocacao->sexo == 'M') {
            $data['mensagem_notificacao'] = 'Caro colaborador, por gentileza verifique seus apontamentos de frequência!';
        } elseif ($alocacao->sexo == 'F') {
            $data['mensagem_notificacao'] = 'Cara colaboradora, por gentileza verifique seus apontamentos de frequência!';
        } else {
            $data['mensagem_notificacao'] = 'Caro(a) colaborador(a), por gentileza verifique seus apontamentos de frequência!';
        }

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_filtrar_cuidador()
    {
        $idAlocado = $this->input->post('id');
        $cargoFuncao = $this->input->post('cargo_funcao');
        $municipio = $this->input->post('municipio');

        $alocacao = $this->db
            ->select('c.id, c.depto, c.id_diretoria, c.ano, c.semestre, a.id_cuidador, a.cuidador')
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->where('a.id', $idAlocado)
            ->get('ei_alocados a')
            ->row();

        $sql = "SELECT a.id, a.nome
                FROM usuarios a
                INNER JOIN ei_ordem_servico_profissionais b ON b.id_usuario = a.id
                INNER JOIN ei_ordem_servico_escolas c ON c.id = b.id_ordem_servico_escola
                INNER JOIN ei_ordem_servico d ON d.id = c.id_ordem_servico
                INNER JOIN ei_contratos e ON e.id = d.id_contrato
                INNER JOIN ei_diretorias f ON f.id = e.id_cliente
                WHERE f.id = '{$alocacao->id_diretoria}' AND 
                      f.depto = '{$alocacao->depto}' AND 
                      d.ano = '{$alocacao->ano}' AND 
                      d.semestre = '{$alocacao->semestre}' AND 
                      a.id NOT IN (SELECT x.id_cuidador 
                                   FROM ei_alocados x 
                                   INNER JOIN ei_alocacao_escolas y 
                                              ON y.id = x.id_alocacao_escola
                                   WHERE y.id_alocacao = '{$alocacao->id}') AND 
                      (a.funcao = '{$cargoFuncao}' OR CHAR_LENGTH('{$cargoFuncao}') = 0) AND
                      (a.municipio = '{$municipio}' OR CHAR_LENGTH('{$municipio}') = 0)
                ORDER BY a.nome ASC";

        $rows = $this->db->query($sql)->result();

        $idProfissionais = ['' => 'selecione...'] + array_column($rows, 'nome', 'id');

        $data['id_cuidador'] = form_dropdown('', $idProfissionais, $alocacao->id_cuidador);

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_save_cuidador()
    {
        $id = $this->input->post('id');

        $usuario = $this->db
            ->select('a.id, a.nome, a.cargo, a.funcao, a.municipio')
            ->select('b.id AS id_depto, c.id AS id_area, d.id AS id_setor')
            ->select('e.id AS id_cargo, f.id AS id_funcao')
            ->join('empresa_departamentos b', 'b.nome = a.depto', 'left')
            ->join('empresa_areas c', 'c.nome = a.area', 'left')
            ->join('empresa_setores d', 'd.nome = a.setor', 'left')
            ->join('empresa_cargos e', 'e.nome = a.cargo', 'left')
            ->join('empresa_funcoes f', 'f.nome = a.funcao', 'left')
            ->where('a.id', $this->input->post('id_cuidador'))
            ->get('usuarios a')
            ->row();

        $data = [
            'id_cuidador' => $usuario->id,
            'cuidador' => $usuario->nome,
        ];

        $this->db->trans_start();

        $this->db->update('ei_alocados', $data, ['id' => $id]);

        $data2 = [
            'id_usuario' => $usuario->id,
            'id_departamento' => $usuario->id_depto,
            'id_area' => $usuario->id_area,
            'id_setor' => $usuario->id_setor,
            'id_cargo' => $usuario->id_cargo,
            'id_funcao' => $usuario->id_funcao,
            'municipio' => $usuario->municipio,
        ];

        $alocado = $this->db
            ->select('id_os_profissional')
            ->where('id', $id)
            ->get('ei_alocados')
            ->row();

        $this->db->update('ei_ordem_servico_profissionais', $data2, ['id' => $alocado->id_os_profissional]);

        $this->db->trans_complete();
        $status = $this->db->trans_status();

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_edit_disciplina_aluno()
    {
        $data = $this->db
            ->select('id, id_curso, id_disciplina, media_semestral')
            ->select(["CONCAT(aluno, '<br>', modulo) AS dados"], false)
            ->where('id', $this->input->post('id_matriculado'))
            ->get('ei_matriculados')
            ->row();

        if (empty($data)) {
            exit(json_encode(['erro' => 'Aluno matriculado não encontrado.']));
        }

        $arrDisciplinas = $this->db
            ->select('id, nome')
            ->where('id_curso', $data->id_curso)
            ->get('ei_disciplinas')
            ->result();

        $disciplinas = ['' => 'selecione...'] + array_column($arrDisciplinas, 'nome', 'id');
        $data->disciplinas = form_dropdown('', $disciplinas, $data->id_curso);

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_save_disciplina_aluno()
    {
        $data = $this->input->post();

        $matriculado = $this->db
            ->select('id, id_os_aluno')
            ->where('id', $data['id'])
            ->get('ei_matriculados')
            ->row();

        if (empty($matriculado)) {
            exit(json_encode(['erro' => 'Aluno matriculado não encontrado.']));
        }

        unset($data['id']);

        $this->db->trans_start();

        $this->db->update('ei_matriculados', $data, ['id' => $matriculado->id]);
        $this->db->update('ei_ordem_servico_alunos', ['nota' => $data['media_semestral']], ['id' => $matriculado->id_os_aluno]);

        $this->db->trans_complete();

        if ($this->db->trans_status() == false) {
            exit(json_encode(['erro' => 'Não foi possível salvar o cadastro de notas.']));
        }

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    public function ajax_edit()
    {
        $date = $this->input->post('data');
        $idAlocado = $this->input->post('id_alocado');
        $periodo = $this->input->post('periodo');
        $idAlunos = $this->input->post('alunos');

        $usuario = $this->db
            ->select('b.id_cuidador, b.cuidador')
            ->select(["IF(a.data_substituicao2 IS NOT NULL AND a.data_substituicao2 <= '{$date}', a.id_cuidador_sub2, IF(a.data_substituicao1 IS NOT NULL AND a.data_substituicao1 <= '{$date}', a.id_cuidador_sub1, b.id_cuidador)) AS id"], false)
            ->select(["IF(a.data_substituicao2 IS NOT NULL AND a.data_substituicao2 <= '{$date}', d.nome, IF(a.data_substituicao1 IS NOT NULL AND a.data_substituicao1 <= '{$date}', c.nome, b.cuidador)) AS nome"], false)
            ->join('ei_alocados_horarios a', 'b.id = a.id_alocado', 'left')
            ->join('usuarios c', 'c.id = a.id_cuidador_sub1', 'left')
            ->join('usuarios d', 'd.id = a.id_cuidador_sub2', 'left')
            ->where('b.id', $idAlocado)
            ->where('a.periodo', $periodo)
//			->where("a.dia_semana = DATE_FORMAT('{$date}', '%w') OR a.dia_semana IS NULL")
            ->group_by('b.id')
            ->get('ei_alocados b')
            ->row();

        $alunos = $this->db
            ->where_in('id_aluno', explode(',', $idAlunos))
            ->get('ei_matriculados');

        $this->db->start_cache();

        $this->db->select('a.id AS id_alocado, b.escola, b.municipio, b.ordem_servico')
            ->select('c.horario_entrada_1, c.horario_saida_1, c.substituto_horario_1')
            ->select('c.horario_entrada_2, c.horario_saida_2, c.substituto_horario_2')
            ->select('c.horario_entrada_3, c.horario_saida_3, c.substituto_horario_3')
            ->select('a.id_cuidador, a.cuidador, c.id_usuario, c.id_alocado_sub1, c.id_alocado_sub2')
            ->select('c.id, c.periodo, c.status, c.observacoes, c.ocorrencia_cuidador_aluno, c.ocorrencia_professor')
            ->select("DATE_FORMAT(c.data, '%d/%m/%Y') AS data", false)
            ->select("TIME_FORMAT(c.desconto, '%H:%i') AS desconto", false)
            ->select("TIME_FORMAT(c.desconto_sub1, '%H:%i') AS desconto_sub1", false)
            ->select("TIME_FORMAT(c.desconto_sub2, '%H:%i') AS desconto_sub2", false)
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->where('a.id', $idAlocado);

        $this->db->stop_cache();

        $data = $this->db
            ->join('ei_alocados_horarios d', "d.id_alocado = a.id AND d.periodo = '{$periodo}' AND d.dia_semana = DATE_FORMAT('{$date}', '%w')", 'left', false)
            ->join('ei_matriculados_turmas e', 'e.id_alocado_horario = d.id', 'left')
            ->join('ei_matriculados f', "f.id = e.id_matriculado AND f.id_alocacao_escola = b.id AND f.id_aluno IN ({$idAlunos})", 'left', false)
            ->join('ei_apontamento c', "c.id_alocado = a.id AND c.data = '{$date}' AND (c.periodo = '{$periodo}' OR c.periodo IS NULL)", 'left', false)
            ->group_by('c.id')
            ->get('ei_alocados a')
            ->row();

        if (!isset($data->id)) {
            $data = $this->db
                ->join('ei_apontamento c', "c.id_alocado = a.id AND c.data = '{$date}' AND c.periodo IS NULL", 'left')
                ->get('ei_alocados a')
                ->row();
        }

        if (empty($data->data)) {
            $data->data = date('d/m/Y', strtotime(str_replace('-', '/', $date)));
        }

        $this->db->flush_cache();

        $cuidadoresSub = $this->db
            ->select('a.id, a.nome')
            ->join('empresa_departamentos b', 'b.id = a.id_depto OR b.nome = a.depto', 'left')
            ->where('a.empresa', $this->session->userdata('empresa'))
            ->where('a.tipo', 'funcionario')
            ->where("a.depto = 'Educação Inclusiva'")
            ->where('a.status', 1)
            ->order_by('a.nome', 'asc')
            ->get('usuarios a')
            ->result();

        $cuidadores = ['' => $data->cuidador] + array_column($cuidadoresSub, 'nome', 'id');

        $data->id_usuarios = form_dropdown('', [$usuario->id => $usuario->nome], $usuario->id);
        $cuidadores[''] = 'selecione...';
        $data->id_alocado_sub1 = form_dropdown('', $cuidadores, $data->id_alocado_sub1);
        $data->id_alocado_sub2 = form_dropdown('', $cuidadores, $data->id_alocado_sub2);

        $this->load->helper('time');

        $data->horario_entrada_1 = timeSimpleFormat($data->horario_entrada_1);
        $data->horario_saida_1 = timeSimpleFormat($data->horario_saida_1);
        $data->horario_entrada_2 = timeSimpleFormat($data->horario_entrada_2);
        $data->horario_saida_2 = timeSimpleFormat($data->horario_saida_2);
        $data->horario_entrada_3 = timeSimpleFormat($data->horario_entrada_3);
        $data->horario_saida_3 = timeSimpleFormat($data->horario_saida_3);

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_save()
    {
        $post = $this->input->post();
        $id = $this->input->post('id');

        if (!empty($post['status']) == false) {
            exit(json_encode(['erro' => 'O status é obrigatório.']));
        }

        $dias = $post['dias'];
        $definirHorariosPadrao = $post['horarios_padrao'] ?? null;
        unset($post['id']);
        unset($post['dias']);
        unset($post['horarios_padrao']);
        unset($post['nao_salvar_medicao']);

        $alocacao = $this->db
            ->select('c.semestre')
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->where('a.id', $post['id_alocado'])
            ->get('ei_alocados a')
            ->row();

        $mes = date('m', strtotime($post['data']));
        $diaSemana = date('w', strtotime($post['data']));
        $idMes = $mes - ($alocacao->semestre > 1 ? 6 : 0);
        $horariosPadrao = [];
        if ($definirHorariosPadrao) {
            $horarios = $this->db
                ->select('dia_semana')
                ->select("horario_inicio_mes{$idMes} AS horario_inicio", false)
                ->select("horario_termino_mes{$idMes} AS horario_termino", false)
                ->where('id_alocado', $post['id_alocado'])
                ->where('periodo', $post['periodo'])
                ->get('ei_alocados_horarios')
                ->result();

            foreach ($horarios as $horario) {
                $horariosPadrao[$horario->dia_semana] = [
                    'horario_inicio' => $horario->horario_inicio,
                    'horario_termino' => $horario->horario_termino,
                ];
            }
            $post['horario_entrada_1'] = '';
            $post['horario_saida_1'] = '';
            $post['horario_entrada_2'] = '';
            $post['horario_saida_2'] = '';
            $post['horario_entrada_3'] = '';
            $post['horario_saida_3'] = '';
            $post['horario_entrada_' . $post['periodo']] = $horariosPadrao[$diaSemana]['horario_inicio'] ?? '';
            $post['horario_saida_' . $post['periodo']] = $horariosPadrao[$diaSemana]['horario_inicio'] ?? '';
        }

        $this->load->model('ei_apontamento_model', 'apontamento');

        $dataGroup = [$post];
//        if (empty($id)) {
        $diaAtual = date('d', strtotime($post['data']));
        $ano = date('Y', strtotime($post['data']));
        foreach ($dias as $k => $dia) {
            if ($dia == $diaAtual) {
                continue;
            }
            $dataDia = date('Y-m-d', mktime(0, 0, 0, (int)$mes, (int)$dia, (int)$ano));
            $oldData = $this->apontamento->where(['id !=' => $id, 'id_alocado', $post['id_alocado'], 'data' => $dataDia])->first();
            if (!empty($oldData)) {
                $post = $oldData;
            }
            $post['data'] = $dataDia;
            if ($definirHorariosPadrao) {
                $diaSemana = date('w', strtotime($post['data']));
                $post['horario_entrada_' . $post['periodo']] = $horariosPadrao[$diaSemana]['horario_inicio'] ?? '';
                $post['horario_saida_' . $post['periodo']] = $horariosPadrao[$diaSemana]['horario_inicio'] ?? '';
            }
            $dataGroup[] = $post;
        }
//        }

        $this->load->helper('time');

        $this->db->trans_begin();

        foreach ($dataGroup as $data) {

            if (in_array($data['status'], ['EM', 'RE'])) {
//                $data['periodo'] = null;
            }

            if (strlen($data['id_alocado_sub1']) == 0) {
                $data['id_alocado_sub1'] = null;
            }
            if (strlen($data['id_alocado_sub2']) == 0) {
                $data['id_alocado_sub2'] = null;
            }

            if (strlen($data['horario_entrada_1'] ?? null) > 0) {
                $data['horario_entrada_1'] = $data['data'] . ' ' . $data['horario_entrada_1'];
            } else {
                $data['horario_entrada_1'] = null;
            }
            if (strlen($data['horario_entrada_2'] ?? null) > 0) {
                $data['horario_entrada_2'] = $data['data'] . ' ' . $data['horario_entrada_2'];
            } else {
                $data['horario_entrada_2'] = null;
            }
            if (strlen($data['horario_entrada_3'] ?? null) > 0) {
                $data['horario_entrada_3'] = $data['data'] . ' ' . $data['horario_entrada_3'];
            } else {
                $data['horario_entrada_3'] = null;
            }

            if (strlen($data['horario_saida_1'] ?? null) > 0) {
                $data['horario_saida_1'] = $data['data'] . ' ' . $data['horario_saida_1'];
            } else {
                $data['horario_saida_1'] = null;
            }
            if (strlen($data['horario_saida_2'] ?? null) > 0) {
                $data['horario_saida_2'] = $data['data'] . ' ' . $data['horario_saida_2'];
            } else {
                $data['horario_saida_2'] = null;
            }
            if (strlen($data['horario_saida_3'] ?? null) > 0) {
                $data['horario_saida_3'] = $data['data'] . ' ' . $data['horario_saida_3'];
            } else {
                $data['horario_saida_3'] = null;
            }

            if (strlen($data['substituto_horario_1'] ?? null) == 0) {
                $data['substituto_horario_1'] = null;
            }
            if (strlen($data['substituto_horario_2'] ?? null) == 0) {
                $data['substituto_horario_2'] = null;
            }
            if (strlen($data['substituto_horario_3'] ?? null) == 0) {
                $data['substituto_horario_3'] = null;
            }

            $desconto = timeToSec($data['desconto'] ?? null);
            $descontoSub1 = timeToSec($data['desconto_sub1'] ?? null);
            $descontoSub2 = timeToSec($data['desconto_sub2'] ?? null);

            if (in_array($data['status'], ['FA', 'PV', 'AT', 'SA'])) {
                $data['desconto'] = strlen($desconto) ? secToTime($desconto * ($desconto < 0 ? 1 : -1)) : null;
                $data['desconto_sub1'] = strlen($descontoSub1) ? secToTime($descontoSub1 * ($descontoSub1 < 0 ? -1 : 1)) : null;
                $data['desconto_sub2'] = strlen($descontoSub2) ? secToTime($descontoSub2 * ($descontoSub2 < 0 ? -1 : 1)) : null;
            } else {
                $data['desconto'] = strlen($desconto) ? secToTime($desconto * ($desconto < 0 ? -1 : 1)) : null;
                $data['desconto_sub1'] = strlen($descontoSub1) ? secToTime($descontoSub1 * ($descontoSub1 < 0 ? 1 : -1)) : null;
                $data['desconto_sub2'] = strlen($descontoSub2) ? secToTime($descontoSub2 * ($descontoSub2 < 0 ? 1 : -1)) : null;
            }

            $desconto_old = null;
            $desconto_sub1_old = null;
            $desconto_sub2_old = null;

            if ($id) {
                $row = $this->db
                    ->select('TIME_TO_SEC(desconto) AS desconto')
                    ->select('TIME_TO_SEC(desconto_sub1) AS desconto_sub1')
                    ->select('TIME_TO_SEC(desconto_sub2) AS desconto_sub2')
                    ->where('id', $id)
                    ->get('ei_apontamento')
                    ->row();

                if ($row) {
                    $desconto_old = $row->desconto / 3600;
                    $desconto_sub1_old = $row->desconto_sub1 / 3600;
                    $desconto_sub2_old = $row->desconto_sub2 / 3600;
                }

                $this->apontamento->update($id, $data);
            } else {
                $this->apontamento->insert($data);
            }


            if ($this->db->trans_status()) {
                $mes = intval(date('m', strtotime($data['data'])));
                $semestre = $mes > 6 ? 2 : 1;
                if ($mes > 6) {
                    $mes -= 6;
                }

                $osProfissional = $this->db
                    ->select("a.id, a.desconto_mensal_{$mes} AS desconto", false)
                    ->select("a.desconto_mensal_sub1_{$mes} AS desconto_sub1", false)
                    ->select("a.desconto_mensal_sub2_{$mes} AS desconto_sub2", false)
                    ->join('ei_ordem_servico_escolas b', 'b.id = a.id_ordem_servico_escola')
                    ->join('ei_ordem_servico c', 'c.id = b.id_ordem_servico')
                    ->join('ei_alocados d', 'd.id_os_profissional = a.id', 'left')
                    ->where('d.id', $data['id_alocado'])
                    ->where('c.ano', date('Y', strtotime($data['data'])))
                    ->where('c.semestre', $semestre)
                    ->get('ei_ordem_servico_profissionais a')
                    ->row();

                if ($osProfissional) {
                    if (!isset($data['desconto'])) {
                        $data['desconto'] = $this->input->post('desconto');
                    }
                    if (!isset($data['desconto_sub1'])) {
                        $data['desconto_sub1'] = $this->input->post('desconto_sub1');
                    }
                    if (!isset($data['desconto_sub2'])) {
                        $data['desconto_sub2'] = $this->input->post('desconto_sub2');
                    }

                    $desconto = $this->db->query("SELECT TIME_TO_SEC('{$data['desconto']}') AS desconto")->row_array()['desconto'];
                    $desconto_sub1 = $this->db->query("SELECT TIME_TO_SEC('{$data['desconto_sub1']}') AS desconto_sub1")->row_array()['desconto_sub1'];
                    $desconto_sub2 = $this->db->query("SELECT TIME_TO_SEC('{$data['desconto_sub2']}') AS desconto_sub2")->row_array()['desconto_sub2'];

                    $data2 = [
                        'desconto_mensal_' . $mes => $osProfissional->desconto - $desconto_old + ($desconto / 3600),
                        'desconto_mensal_sub1_' . $mes => $osProfissional->desconto_sub1 - $desconto_sub1_old + ($desconto_sub1 / 3600),
                        'desconto_mensal_sub2_' . $mes => $osProfissional->desconto_sub2 - $desconto_sub2_old + ($desconto_sub2 / 3600),
                    ];
                    $this->db->update('ei_ordem_servico_profissionais', $data2, ['id' => $osProfissional->id]);
                }
            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            exit(json_encode(['erro' => 'Erro ao iniciar semestre.']));
        }

        $this->db->trans_commit();

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    public function ajax_delete()
    {
        $id = $this->input->post('id');

        $apontamento = $this->db
            ->select('a.id, a.data, b.id_cuidador')
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->where('a.id', $id)
            ->get('ei_apontamento a')
            ->row();

        if (empty($apontamento)) {
            exit(json_encode(['erro' => 'Evento não encontrado.']));
        }

        $this->db->trans_start();

        $status = $this->db->delete('ei_apontamento', ['id' => $apontamento->id]);

        $this->db
            ->where('id_usuario', $apontamento->id_cuidador)
            ->where('data_evento', $apontamento->data)
            ->delete('ei_usuarios_frequencias');

        $this->db->trans_complete();

        if ($this->db->trans_status() == false) {
            exit(json_encode(['erro' => 'Erro ao excluir o evento.']));
        }

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function preparar_recupeacao_medicao()
    {
        $cuidadores = $this->db
            ->select('b.id, b.nome')
            ->join('usuarios b', 'b.id = a.id_cuidador')
            ->join('ei_alocacao_escolas c', 'c.id = a.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->where('d.id_empresa', $this->session->userdata('empresa'))
            ->where('d.depto', $this->input->post('depto'))
            ->where('d.id_diretoria', $this->input->post('diretoria'))
            ->where('d.id_supervisor', $this->input->post('supervisor'))
            ->where('d.ano', $this->input->post('ano'))
            ->where('d.semestre', $this->input->post('semestre'))
            ->where_in('b.status', [USUARIO_ATIVO, USUARIO_EM_EXPERIENCIA])
            ->group_by(['b.id', 'b.nome'])
            ->order_by('TRIM(b.nome)', 'asc')
            ->get('ei_alocados a')
            ->result_array();

        $data['cuidadores'] = form_dropdown('', array_column($cuidadores, 'nome', 'id'));

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function recuperar_medicao()
    {
        $dataInicio = strToDate($this->input->post('data_inicio'));
        $dataTermino = strToDate($this->input->post('data_termino'));
        $idCuidadores = $this->input->post('id_cuidadores');
        if (is_array($idCuidadores) == false) {
            $idCuidadores = [0];
        }

        $this->load->library('form_validation');

        $this->form_validation->set_rules('data_inicio', 'Data Início', 'required|valid_date');
        $this->form_validation->set_rules('data_termino', 'Data Término', 'required|valid_date');
        $this->form_validation->set_rules('id_cuidadores[]', 'Colaboradores', 'required');
        if ($this->form_validation->run() == false) {
            exit(json_encode(['erro' => $this->form_validation->error_string(' ', ' ')]));
        }

        $apontamentos = $this->db
            ->select('a.*', false)
            ->select('b.id_cuidador, c.id_escola, d.semestre')
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->where('d.id_empresa', $this->session->userdata('empresa'))
            ->where('d.depto', $this->input->post('depto'))
            ->where('d.id_diretoria', $this->input->post('diretoria'))
            ->where('d.id_supervisor', $this->input->post('supervisor'))
            ->where('d.ano', $this->input->post('ano'))
            ->where('d.semestre', $this->input->post('semestre'))
            ->where_in('b.id_cuidador', $idCuidadores)
            ->where("a.data BETWEEN '{$dataInicio}' AND '{$dataTermino}'", null, false)
            ->get('ei_apontamento a')
            ->result();

        $status = ['FA' => 'FT', 'PV' => 'PV', 'FE' => 'FR', 'EM' => 'EF', 'RE' => 'RE', 'EE' => 'EE', 'HE' => 'HE', 'SL' => 'SL'];

        $totalDeMedicoesCadastradas = 0;

        foreach ($apontamentos as $apontamento) {
            $mes = date('m', strtotime($apontamento->data));
            $idMes = intval($mes) - (intval($apontamento->semestre) > 1 ? 6 : 0);
            $diaSemana = date('w', strtotime($apontamento->data));
            $periodo = $apontamento->periodo ?? 1;

            $alocadoHorario = $this->db
                ->select("a.horario_inicio_mes{$idMes} AS horario_inicio", false)
                ->select("a.horario_termino_mes{$idMes} AS horario_termino", false)
                ->select(["GROUP_CONCAT(e.aluno ORDER BY e.aluno ASC SEPARATOR ', ') AS alunos"], false)
                ->join('ei_alocados b', 'b.id = a.id_alocado')
                ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
                ->join('ei_matriculados_turmas d', 'd.id_alocado_horario = a.id', 'left')
                ->join('ei_matriculados e', 'e.id = d.id_matriculado AND e.id_alocacao_escola = c.id', 'left')
                ->join('ei_usuarios_frequencias f', 'e.id = d.id_matriculado AND e.id_alocacao_escola = c.id', 'left')
                ->where('a.id_alocado', $apontamento->id_alocado)
                ->where('a.dia_semana', $diaSemana)
                ->where('a.periodo', $periodo)
                ->group_start()
                ->where('a.id', $apontamento->id_horario)
                ->or_where("CHAR_LENGTH('{$apontamento->id_horario}') =", 0)
                ->group_end()
                ->group_by('a.id')
                ->get('ei_alocados_horarios a')
                ->row();

            $alunos = explode(', ', $alocadoHorario->alunos ?? null) + explode(', ', $usuarioFrequencia->alunos ?? null);
            $alunos = count($alunos) > 0 ? implode(', ', array_filter(array_unique($alunos))) : null;

            $dataFrequencia = [
                'id_usuario' => $apontamento->id_cuidador,
                'data_evento' => $apontamento->data,
                'periodo_atual' => $periodo,
                'id_escola' => $apontamento->id_escola,
                'alunos' => $alunos,
                'observacoes' => $apontamento->observacoes ?? null,
            ];
            if ($periodo) {
                $dataFrequencia['horario_entrada_' . $periodo] = $alocadoHorario->horario_inicio ?? null;
                $dataFrequencia['horario_saida_' . $periodo] = $alocadoHorario->horario_termino ?? null;
                $dataFrequencia['horario_entrada_real_' . $periodo] = $apontamento->{'horario_entrada_' . $periodo} ?? null;
                $dataFrequencia['horario_saida_real_' . $periodo] = $apontamento->{'horario_saida_' . $periodo} ?? null;
                $dataFrequencia['status_entrada_' . $periodo] = $status[$apontamento->status] ?? null;
                $dataFrequencia['status_saida_' . $periodo] = $status[$apontamento->status] ?? null;
            } else {
                $dataFrequencia['status_entrada_1'] = $status[$apontamento->status] ?? null;
                $dataFrequencia['status_saida_1'] = $status[$apontamento->status] ?? null;
                $dataFrequencia['status_entrada_2'] = $status[$apontamento->status] ?? null;
                $dataFrequencia['status_saida_2'] = $status[$apontamento->status] ?? null;
                $dataFrequencia['status_entrada_3'] = $status[$apontamento->status] ?? null;
                $dataFrequencia['status_saida_3'] = $status[$apontamento->status] ?? null;
            }

            $usuarioFrequencias = $this->db
                ->select("id, horario_entrada_{$periodo} AS horario_entrada", false)
                ->where('id_usuario', $apontamento->id_cuidador)
                ->where('data_evento', $apontamento->data)
                ->where('id_escola', $apontamento->id_escola)
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

            if ($usuarioFrequencia) {
                $this->db->update('ei_usuarios_frequencias', $dataFrequencia, ['id' => $usuarioFrequencia->id]);
            } else {
                $this->db->insert('ei_usuarios_frequencias', $dataFrequencia);
            }
            $totalDeMedicoesCadastradas++;
        }

        if ($totalDeMedicoesCadastradas === 1) {
            $msg = $totalDeMedicoesCadastradas . ' medição nova foi salva.';
        } else {
            $msg = $totalDeMedicoesCadastradas . ' medições novas foram salvas.';
        }
        echo json_encode(['total' => $totalDeMedicoesCadastradas, 'msg' => $msg]);
    }

    //--------------------------------------------------------------------

    public function preparar_exclusao_eventos_mes()
    {
        $escolas = $this->db
            ->select('a.id_escola, a.escola')
            ->join('ei_alocacao b', 'b.id = a.id_alocacao')
            ->where('b.id_empresa', $this->session->userdata('empresa'))
            ->where('b.depto', $this->input->post('depto'))
            ->where('b.id_diretoria', $this->input->post('diretoria'))
            ->where('b.id_supervisor', $this->input->post('supervisor'))
            ->where('b.ano', $this->input->post('ano'))
            ->where('b.semestre', $this->input->post('semestre'))
            ->order_by('a.escola', 'asc')
            ->get('ei_alocacao_escolas a')
            ->result_array();

        $escolas = ['' => 'Todas'] + array_column($escolas, 'escola', 'id_escola');
        $data['escolas'] = form_dropdown('', $escolas);
        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function filtrar_exclusao_eventos_mes()
    {
        $cuidadores = $this->db
            ->select('a.id_cuidador, a.cuidador')
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocados_horarios c', 'c.id_alocado = a.id')
            ->where('b.id_escola', $this->input->post('id_escola'))
            ->order_by('a.cuidador', 'asc')
            ->get('ei_alocados a')
            ->result_array();

        $cuidadores = ['' => 'Todos'] + array_column($cuidadores, 'cuidador', 'id_cuidador');

        $periodos = $this->db
            ->select('a.periodo')
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->where('b.id_cuidador', $this->input->post('id_cuidador'))
            ->where('c.id_escola', $this->input->post('id_escola'))
            ->get('ei_alocados_horarios a')
            ->result_array();

        $periodosPadrao = [
            '0' => 'Madrugada',
            '1' => 'Manhã',
            '2' => 'Tarde',
            '3' => 'Noite',
        ];

        $periodos = ['' => 'Todos'] + array_intersect_key($periodosPadrao, array_column($periodos, 'periodo', 'periodo'));

        $data = [
            'cuidadores' => form_dropdown('', $cuidadores, $this->input->post('id_cuidador')),
            'periodos' => form_dropdown('', $periodos, $this->input->post('periodo')),
        ];

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function notificar_cuidador()
    {
        $id = $this->input->post('id');
        $mes = $this->input->post('mes');
        $mensagem = $this->input->post('mensagem');
        if (strlen($mensagem) == 0) {
            exit(json_encode(['erro' => 'O corpo da mensagem é obrigatório.']));
        }

        $alocado = $this->db
            ->select('a.id_cuidador, b.nome, b.email, b.telefone, d.ano')
            ->join('usuarios b', 'b.id = a.id_cuidador')
            ->join('ei_alocacao_escolas c', 'c.id = a.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->where('a.id', $id)
            ->get('ei_alocados a')
            ->row();

        $usuario = $this->db
            ->select('nome, email, sexo')
            ->where('id', $alocado->id_cuidador)
            ->get('usuarios')
            ->row();

        if (empty($usuario)) {
            exit(json_encode(['erro' => 'Profissional não encontrado.']));
        }

        $this->load->library('email');
        $this->load->helper('time');
        $this->load->library('calendar');

        $data = [
            'logoEmpresa' => 'imagens/usuarios/' . $this->session->userdata('logomarca'),
            'usuario' => $usuario->nome,
            'sexo' => $usuario->sexo,
            'nomeMes' => strtolower($this->calendar->get_month_name($mes)),
            'ano' => $alocado->ano,
            'mensagem' => $this->input->post('mensagem')
        ];

        $this->email
            ->set_mailtype('html')
            ->from('contato@rhsuite.com.br', 'RhSuite')
            ->to($usuario->email)
//            ->cc('apoio.icom@ame-sp.org.br')
            ->subject($usuario->nome . ' - Notificação de Falta de Educação Inclusiva')
            ->message($this->load->view('ei/email_notificacao_falta', $data, true));

        if ($this->email->send() == false) {
            exit(json_encode(['erro' => 'Não foi possível enviar o e-mail, tente novamente.']));
        }

        echo json_encode(['status' => true]);
    }

}
