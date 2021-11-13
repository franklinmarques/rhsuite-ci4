<?php

namespace App\Controllers\Ei;

use App\Controllers\BaseController;

class Medicao_livro_ata extends BaseController
{

    public function index()
    {
        // monta campo de meses
        $data = [
            'meses' => [
                '01' => 'Janeiro',
                '02' => 'Fevereiro',
                '03' => 'Março',
                '04' => 'Abril',
                '05' => 'Maio',
                '06' => 'Junho',
                '07' => 'Julho',
                '08' => 'Agosto',
                '09' => 'Setembro',
                '10' => 'Outubro',
                '11' => 'Novembro',
                '12' => 'Dezembro',
            ],
        ];

        // monta o mes atual
        $data['mes'] = $data['meses'][date('m')];
        // monta o semestre atual
        $data['semestre'] = array_slice(array_values($data['meses']), intval(date('n')) > 6 ? 6 : 0, 7);
        if (!isset($data['semestre'][6])) {
            $data['semestre'][6] = 'Jul';
        }

        // prepara a busca dos campos de filtragem
        $where = [
            'empresa' => $this->session->userdata('empresa'),
            'ano' => date('Y'),
            'semestre' => (date('n') / 6),
        ];

        //monta o campo de departamento
        $data['depto'] = $this->getDeptos($where);

        // monta o campo de diretoria
        $data['diretoria'] = ['' => 'Todas'] + $this->getDiretorias($where);

        // monta o campo de supervisor
        $data['supervisor'] = ['' => 'Todos'] + $this->getSupervisores($where);

        // monta o campo de supervisor visitante (desnecessário)
        $data['supervisorVisitante'] = ['' => 'selecione...'] + $this->getVisitantes($where);

        // monta o departamento atual
        $data['depto_atual'] = count($data['depto']) > 0 ? '' : 'Educação Inclusiva';

        // monta a diretoria atual
        $data['diretoria_atual'] = '';

        // monta o supervisor atual
        if (in_array($this->session->userdata('nivel'), [9, 10])) {
            $data['supervisor_atual'] = $data['supervisor'][$this->session->userdata('id')] ?? '';
        } else {
            $data['supervisor_atual'] = '';
        }

        $data['id_usuario'] = $this->session->userdata('id');
        $data['nome_usuario'] = $this->session->userdata('nome');
        $timestamp = mktime(0, 0, 0, (int)date('m'), 1, (int)date('Y'));
        $data['data_inicio'] = date('d/m/Y', $timestamp);
        $data['data_termino'] = date('t/m/Y', $timestamp);

        $this->load->model('ei_livro_ata_model');
        $periodos = $this->ei_livro_ata_model::PERIODOS;
        unset($periodos['0']);
        $data['periodos'] = ['' => 'Todos'] + $periodos;

        $this->load->model('ei_usuario_frequencia_model');
        $data['status_medicao'] = ['' => '--'] + $this->ei_usuario_frequencia_model::STATUS;

        // monta a view
        $this->load->view('ei/medicao_livro_ata', $data);
    }

    //--------------------------------------------------------------------

    private function getDeptos(array $where = []): array
    {
        if ($this->session->userdata('nivel') == 10) {
            $supervisor = $this->session->userdata('id');
        } else {
            $supervisor = '';
        }

        $sql = "SELECT a.depto 
                FROM ei_diretorias a
                LEFT JOIN ei_escolas b ON b.id_diretoria = a.id
                LEFT JOIN ei_supervisores c ON c.id_escola = b.id
                LEFT JOIN ei_coordenacao d ON d.id = c.id_coordenacao
                WHERE a.id_empresa = '{$where['empresa']}'
                      AND a.depto = 'Educação Inclusiva'
                      AND (d.id_usuario = '{$supervisor}' OR '{$supervisor}' = '')
                UNION
                SELECT 'Educação Inclusiva' AS depto
                UNION
                SELECT depto 
                FROM ei_alocacao 
                WHERE id_empresa = '{$where['empresa']}' 
                       AND depto = 'Educação Inclusiva'
                       AND (id_supervisor = '{$supervisor}' OR '{$supervisor}' = '')
                       AND ano = '{$where['ano']}' AND semestre = '{$where['semestre']}'
                ORDER BY depto ASC";

        $rows = $this->db->query($sql)->result();

        return array_column($rows, 'depto', 'depto');
    }

    //--------------------------------------------------------------------

    private function getDiretorias(array $where = []): array
    {
        $depto = $where['depto'] ?? 'Educação Inclusiva';

        if ($this->session->userdata('nivel') == 10) {
            $supervisor = $this->session->userdata('id');
        } else {
            $supervisor = '';
        }

        $sql = "SELECT a.id, a.nome AS diretoria 
                FROM ei_diretorias a
                LEFT JOIN ei_escolas b ON b.id_diretoria = a.id
                LEFT JOIN ei_supervisores c ON c.id_escola = b.id
                LEFT JOIN ei_coordenacao d ON d.id = c.id_coordenacao
                WHERE a.id_empresa = '{$where['empresa']}' 
                      AND a.depto = '{$depto}'
                      AND (d.id_usuario = '{$supervisor}' OR '{$supervisor}' = '')
                UNION
                SELECT id_diretoria AS id, diretoria 
                FROM ei_alocacao 
                WHERE id_empresa = '{$where['empresa']}' 
                      AND depto = '{$depto}'
                      AND (id_supervisor = '{$supervisor}' OR '{$supervisor}' = '')
                      AND ano = '{$where['ano']}' 
                      AND semestre = '{$where['semestre']}'
                ORDER BY diretoria ASC";

        $rows = $this->db->query($sql)->result();

        return array_column($rows, 'diretoria', 'id');
    }

    //--------------------------------------------------------------------

    private function getSupervisores(array $where = []): array
    {
        $depto = $where['depto'] ?? 'Educação Inclusiva';

        $diretoria = $where['diretoria'] ?? '';

        if ($this->session->userdata('nivel') == 10) {
            $supervisor = $this->session->userdata('id');
        } else {
            $supervisor = '';
        }

        $sql = "SELECT a.id AS id, a.nome AS supervisor 
                FROM usuarios a 
                INNER JOIN ei_coordenacao b ON b.id_usuario = a.id
                INNER JOIN ei_supervisores c ON c.id_coordenacao = b.id
                INNER JOIN ei_escolas d ON d.id = c.id_escola
                INNER JOIN ei_diretorias e ON e.id = d.id_diretoria
                WHERE e.id_empresa = '{$where['empresa']}' 
                      AND e.depto = '{$depto}'
                      AND (e.id = '{$diretoria}' OR '{$diretoria}' = '')
                      AND (a.id = '{$supervisor}' OR '{$supervisor}' = '')
                UNION
                SELECT id_supervisor AS id, supervisor 
                FROM ei_alocacao 
                WHERE id_empresa = '{$where['empresa']}' 
                      AND depto = '{$depto}'
                      AND (id_diretoria = '{$diretoria}' OR '{$diretoria}' = '')
                      AND (id_supervisor = '{$supervisor}' OR '{$supervisor}' = '')
                      AND ano = '{$where['ano']}' 
                      AND semestre = '{$where['semestre']}'
                ORDER BY supervisor ASC";

        $rows = $this->db->query($sql)->result();

        return array_column($rows, 'supervisor', 'id');
    }

    //--------------------------------------------------------------------

    private function getVisitantes(array $where = []): array
    {
        $depto = $where['depto'] ?? 'Educação Inclusiva';

        $diretoria = $where['diretoria'] ?? '';

        if ($this->session->userdata('nivel') == 10) {
            $supervisor = $this->session->userdata('id');
        } else {
            $supervisor = $where['supervisor'] ?? '';
        }

        $sql = "SELECT a.id AS id, a.nome AS supervisor_visitante
                FROM usuarios a 
                INNER JOIN ei_coordenacao b ON b.id_usuario = a.id
                INNER JOIN ei_supervisores c ON c.id_coordenacao = b.id
                INNER JOIN ei_escolas d ON d.id = c.id_escola
                INNER JOIN ei_diretorias e ON e.id = d.id_diretoria
                WHERE e.id_empresa = '{$where['empresa']}' 
                      AND e.depto = '{$depto}'
                      AND (e.id = '{$diretoria}' OR '{$diretoria}' = '')
                      AND (a.id = '{$supervisor}' OR '{$supervisor}' = '')
                UNION
                SELECT DISTINCT(c.id_supervisor_visitante) AS id, c.supervisor_visitante
                FROM ei_alocacao a
                INNER JOIN ei_mapa_unidades b ON b.id_alocacao = a.id
                INNER JOIN ei_mapa_visitacao c ON c.id_mapa_unidade = b.id
                WHERE a.id_empresa = '{$where['empresa']}' 
                      AND a.depto = '{$depto}'
                      AND (a.id_diretoria = '{$diretoria}' OR '{$diretoria}' = '')
                      AND (a.id_supervisor = '{$supervisor}' OR '{$supervisor}' = '')
                      AND a.ano = '{$where['ano']}' 
                      AND a.semestre = '{$where['semestre']}'
                ORDER BY supervisor_visitante ASC";

        $rows = $this->db->query($sql)->result();

        return array_column($rows, 'supervisor_visitante', 'id');
    }

    //--------------------------------------------------------------------

    public function atualizar_filtro()
    {
        $diretoria = $this->input->post('diretoria');

        $supervisor = $this->input->post('supervisor');

        $where = [
            'empresa' => $this->session->userdata('empresa'),
            'depto' => $this->input->post('depto'),
            'diretoria' => $diretoria,
            'ano' => $this->input->post('ano'),
            'semestre' => $this->input->post('semestre'),
        ];

        $filtro['diretoria'] = ['' => 'Todas'] + $this->getDiretorias($where);

        $filtro['supervisor'] = ['' => 'Todos'] + $this->getSupervisores($where);

        if (isset($filtro['supervisor'][$supervisor])) {
            $where['supervisor'] = $supervisor;
            $filtro['supervisor_visitante'] = $this->getVisitantes($where);
        } else {
            $filtro['supervisor_visitante'] = ['' => 'selecione...'] + $this->getVisitantes($where);
        }

        $data['diretoria'] = form_dropdown('diretoria', $filtro['diretoria'], $diretoria, 'onchange="atualizarFiltro()" class="form-control input-sm"');

        $data['supervisor'] = form_dropdown('supervisor', $filtro['supervisor'], $supervisor, 'class="form-control input-sm"');

        $data['supervisor_visitante'] = form_dropdown('supervisor_visitante', $filtro['supervisor_visitante'], $supervisor, 'class="form-control"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function atualizar_filtros_visitas()
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

        $filtro = $this->montarFiltrosVisita($busca);

        $data['prestadores_servicos_tratados'] = $filtro['prestadores_servicos_tratados'];

        $data['cliente'] = form_dropdown('cliente', $filtro['clientes'], $cliente, 'onchange="atualizarFiltrosVisitas()" class="form-control"');

        $data['municipio'] = form_dropdown('municipio', $filtro['municipios'], $municipio, 'onchange="atualizarFiltrosVisitas()" class="form-control"');

        $data['unidade_visitada'] = form_dropdown('unidade_visitada', $filtro['unidades_visitadas'], $unidadeVisitada, 'onchange="atualizarFiltrosVisitas()" class="form-control"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_list_medicao()
    {
        parse_str($this->input->post('filtro'), $filtro);
        $mes = $this->input->post('mes');
        $semestre = $this->input->post('semestre');
        $ano = $this->input->post('ano');

        $timestamp = mktime(0, 0, 0, (int)$mes, 1, (int)$ano);
        $dataInicioMes = date('Y-m-d', $timestamp);
        $dataTerminoMes = date('Y-m-t', $timestamp);

        $qb = $this->db
            ->select('a.data_evento')
            ->select(["IF(a.status_entrada_1 IN ('FT', 'FR', 'EF', 'RE', 'EE', 'HE', 'SB', 'DG'), a.status_entrada_1, TIME_FORMAT(a.horario_entrada_1, '%H:%i')) AS horario_entrada_1"], false)
            ->select(["IF(a.status_entrada_1 IN ('FT', 'FR', 'EF', 'RE', 'EE', 'HE', 'SB', 'DG'), a.status_entrada_1, TIME_FORMAT(a.horario_entrada_real_1, '%H:%i')) AS horario_entrada_real_1"], false)
            ->select(["IF(a.status_entrada_1 IN ('FT', 'FR', 'EF', 'RE', 'EE', 'HE', 'SB', 'DG'), NULL, TIME_FORMAT(TIMEDIFF(a.horario_entrada_1, TIME(a.horario_entrada_real_1)), '%H:%i')) AS horario_entrada_dif_1"], false)
            ->select(["IF(a.status_saida_1 IN ('FT', 'FR', 'EF', 'RE', 'EE', 'HE', 'SB', 'DG'), a.status_saida_1, TIME_FORMAT(a.horario_saida_1, '%H:%i')) AS horario_saida_1"], false)
            ->select(["IF(a.status_saida_1 IN ('FT', 'FR', 'EF', 'RE', 'EE', 'HE', 'SB', 'DG'), a.status_saida_1, TIME_FORMAT(a.horario_saida_real_1, '%H:%i')) AS horario_saida_real_1"], false)
            ->select(["IF(a.status_saida_1 IN ('FT', 'FR', 'EF', 'RE', 'EE', 'HE', 'SB', 'DG'), NULL, TIME_FORMAT(TIMEDIFF(TIME(a.horario_saida_real_1), a.horario_saida_1), '%H:%i')) AS horario_saida_dif_1"], false)
            ->select(["IF(a.status_entrada_2 IN ('FT', 'FR', 'EF', 'RE', 'EE', 'HE', 'SB', 'DG'), a.status_entrada_2, TIME_FORMAT(a.horario_entrada_2, '%H:%i')) AS horario_entrada_2"], false)
            ->select(["IF(a.status_entrada_2 IN ('FT', 'FR', 'EF', 'RE', 'EE', 'HE', 'SB', 'DG'), a.status_entrada_2, TIME_FORMAT(a.horario_entrada_real_2, '%H:%i')) AS horario_entrada_real_2"], false)
            ->select(["IF(a.status_entrada_2 IN ('FT', 'FR', 'EF', 'RE', 'EE', 'HE', 'SB', 'DG'), NULL, TIME_FORMAT(TIMEDIFF(a.horario_entrada_2, TIME(a.horario_entrada_real_2)), '%H:%i')) AS horario_entrada_dif_2"], false)
            ->select(["IF(a.status_saida_2 IN ('FT', 'FR', 'EF', 'RE', 'EE', 'HE', 'SB', 'DG'), a.status_saida_2, TIME_FORMAT(a.horario_saida_2, '%H:%i')) AS horario_saida_2"], false)
            ->select(["IF(a.status_saida_2 IN ('FT', 'FR', 'EF', 'RE', 'EE', 'HE', 'SB', 'DG'), a.status_saida_2, TIME_FORMAT(a.horario_saida_real_2, '%H:%i')) AS horario_saida_real_2"], false)
            ->select(["IF(a.status_saida_2 IN ('FT', 'FR', 'EF', 'RE', 'EE', 'HE', 'SB', 'DG'), NULL, TIME_FORMAT(TIMEDIFF(TIME(a.horario_saida_real_2), a.horario_saida_2), '%H:%i')) AS horario_saida_dif_2"], false)
            ->select(["IF(a.status_entrada_3 IN ('FT', 'FR', 'EF', 'RE', 'EE', 'HE', 'SB', 'DG'), a.status_entrada_3, TIME_FORMAT(a.horario_entrada_3, '%H:%i')) AS horario_entrada_3"], false)
            ->select(["IF(a.status_entrada_3 IN ('FT', 'FR', 'EF', 'RE', 'EE', 'HE', 'SB', 'DG'), a.status_entrada_3, TIME_FORMAT(a.horario_entrada_real_3, '%H:%i')) AS horario_entrada_real_3"], false)
            ->select(["IF(a.status_entrada_3 IN ('FT', 'FR', 'EF', 'RE', 'EE', 'HE', 'SB', 'DG'), NULL, TIME_FORMAT(TIMEDIFF(a.horario_entrada_3, TIME(a.horario_entrada_real_3)), '%H:%i')) AS horario_entrada_dif_3"], false)
            ->select(["IF(a.status_saida_3 IN ('FT', 'FR', 'EF', 'RE', 'EE', 'HE', 'SB', 'DG'), a.status_saida_3, TIME_FORMAT(a.horario_saida_3, '%H:%i')) AS horario_saida_3"], false)
            ->select(["IF(a.status_saida_3 IN ('FT', 'FR', 'EF', 'RE', 'EE', 'HE', 'SB', 'DG'), a.status_saida_3, TIME_FORMAT(a.horario_saida_real_3, '%H:%i')) AS horario_saida_real_3"], false)
            ->select(["IF(a.status_saida_3 IN ('FT', 'FR', 'EF', 'RE', 'EE', 'HE', 'SB', 'DG'), NULL, TIME_FORMAT(TIMEDIFF(TIME(a.horario_saida_real_3), a.horario_saida_3), '%H:%i')) AS horario_saida_dif_3"], false)
            ->select('a.observacoes, a.justificativa, a.avaliacao_justificativa, a.id')
            ->select(["DATE_FORMAT(a.data_evento, '%d') AS data_evento_de"], false)
            ->join('usuarios b', 'b.id = a.id_usuario')
            ->where('b.empresa', $this->session->userdata('empresa'))
            ->where('a.id_usuario', $this->session->userdata('id'));
        if (!empty($filtro['escola'])) {
            $qb->where('a.id_escola', $filtro['escola']);
        }
        if (!empty($filtro['data_inicio'])) {
            $qb->where('a.data_evento >=', date('y-m-d', strtotime(str_replace('/', '-', $filtro['data_inicio']))));
        } else {
            $qb->where('a.data_evento >=', $dataInicioMes);
        }
        if (!empty($filtro['data_termino'])) {
            $qb->where('a.data_evento <=', date('y-m-d', strtotime(str_replace('/', '-', $filtro['data_termino']))));
        } else {
            $qb->where('a.data_evento <=', $dataTerminoMes);
        }
        $sql = $qb->get_compiled_select('ei_usuarios_frequencias a');

        $this->load->library('dataTables');

        $output = $this->datatables->query($sql);

        $data = [];

        foreach ($output->data as $row) {
            $data[] = [
                $row->data_evento_de,
                $row->horario_entrada_1,
                $row->horario_entrada_real_1,
                $row->horario_entrada_dif_1,
                $row->horario_saida_1,
                $row->horario_saida_real_1,
                $row->horario_saida_dif_1,
                $row->horario_entrada_2,
                $row->horario_entrada_real_2,
                $row->horario_entrada_dif_2,
                $row->horario_saida_2,
                $row->horario_saida_real_2,
                $row->horario_saida_dif_2,
                $row->horario_entrada_3,
                $row->horario_entrada_real_3,
                $row->horario_entrada_dif_3,
                $row->horario_saida_3,
                $row->horario_saida_real_3,
                $row->horario_saida_dif_3,
                $row->observacoes,
                $row->justificativa,
                $row->avaliacao_justificativa,
                '<button class="btn btn-sm btn-danger" onclick="preparar_exclusao_medicao(' . $row->id . ');" title="Excluir medição"><i class="glyphicon glyphicon-trash"></i></button>',
                /*'<button class="btn btn-sm btn-info" onclick="edit_medicao(' . $row->id . ');" title="Editar medição"><i class="glyphicon glyphicon-pencil"></i></button>
                 <button class="btn btn-sm btn-danger" onclick="preparar_exclusao_medicao(' . $row->id . ');" title="Excluir medição"><i class="glyphicon glyphicon-trash"></i></button>',*/
                $row->id,
            ];
        }

        $output->data = $data;

        $livroATA2 = $this->db
            ->select("a.id_escola, b.codigo, b.nome")
            ->join('ei_escolas b', 'b.id = a.id_escola')
            ->join('ei_diretorias c', 'c.id = b.id_diretoria')
            ->where('c.id_empresa', $this->session->userdata('empresa'))
            ->where('YEAR(a.data_evento)', $ano)
            ->where('MONTH(a.data_evento)', $mes)
            ->where('a.id_usuario', $this->session->userdata('id'))
            ->group_by('a.id_escola')
            ->order_by('b.codigo', 'asc')
            ->order_by('b.nome', 'asc')
            ->get('ei_usuarios_frequencias a')
            ->result_array();

        $escolas = ['' => 'Todas'];
        foreach ($livroATA2 as $row) {
            $escolas[$row['id_escola']] = implode(' - ', [$row['codigo'], $row['nome']]);
        }

        $output->escolas = form_dropdown('', $escolas, $filtro['escola']);

        $qtdeDiasMes = date('t', mktime(0, 0, 0, (int)$mes, 1, (int)$ano));
        for ($i = 1; $i <= $qtdeDiasMes; $i++) {
            $dia = str_pad($i, 2, '0', STR_PAD_LEFT);
            $dias[$dia] = $dia;
        }
        $output->dias = form_dropdown('', $dias, '01');

        echo json_encode($output);
    }

    //--------------------------------------------------------------------

    public function ajax_list_livro_ata()
    {
        parse_str($this->input->post('filtro'), $filtro);
        $mes = $this->input->post('mes');
        $semestre = $this->input->post('semestre');
        $ano = $this->input->post('ano');

        $dataInicio = strToDate($filtro['data_inicio'] ?? null);
        $dataTermino = strToDate($filtro['data_termino'] ?? null);
        $timestamp = mktime(0, 0, 0, (int)$mes, 1, (int)$ano);
        $dataInicioMes = date('Y-m-d', $timestamp);
        $dataTerminoMes = date('Y-m-t', $timestamp);

        $qb = $this->db
            ->select('a.data, a.data_inicio_periodo, a.data_termino_periodo, a.alunos')
            ->select('a.curso, a.modulo, a.escola, a.atividades_realizadas')
            ->select('a.dificuldades_encontradas, a.sugestoes_observacoes')
            ->select(["a.id, DATE_FORMAT(a.data, '%d/%m/%Y') AS data_de"], false)
            ->select(["DATE_FORMAT(a.data_inicio_periodo, '%d/%m/%Y') AS data_inicio_periodo_de"], false)
            ->select(["DATE_FORMAT(a.data_termino_periodo, '%d/%m/%Y') AS data_termino_periodo_de"], false)
            ->join('usuarios b', 'b.id = a.id_usuario')
            ->where('b.empresa', $this->session->userdata('empresa'))
            ->where('MONTH(a.data)', $mes)
            ->where('YEAR(a.data)', $ano)
            ->where('b.id', $this->session->userdata('id'));
        if (!empty($filtro['escola'])) {
            $qb->where('a.escola', $filtro['escola']);
        }
        if ($dataInicio) {
            $qb->where('a.data >=', $dataInicio);
        } else {
            $qb->where('a.data >=', $dataInicioMes);
        }
        if ($dataTermino) {
            $qb->where('a.data <=', $dataTermino);
        } else {
            $qb->where('a.data <=', $dataTerminoMes);
        }
        $sql = $qb->get_compiled_select('ei_livro_ata a');

        $this->load->library('dataTables');

        $output = $this->datatables->query($sql);

        $data = [];

        foreach ($output->data as $row) {
            $data[] = [
                $row->data_de,
                $row->data_inicio_periodo_de,
                $row->data_termino_periodo_de,
                $row->alunos,
                $row->curso,
                $row->modulo,
                $row->escola,
                $row->atividades_realizadas,
                $row->dificuldades_encontradas,
                $row->sugestoes_observacoes,
                '<button class="btn btn-sm btn-info" onclick="edit_livro_ata(' . $row->id . ');" title="Editar Livro ATA"><i class="glyphicon glyphicon-pencil"></i></button>
                 <button class="btn btn-sm btn-danger" onclick="delete_livro_ata(' . $row->id . ');" title="Excluir Livro ATA"><i class="glyphicon glyphicon-trash"></i></button>',
            ];
        }

        $output->data = $data;

        $livroATA2 = $this->db
            ->select('escola')
            ->where('id_usuario', $this->session->userdata('id'))
            ->where('CHAR_LENGTH(escola) >', 0)
            ->where('MONTH(data)', $mes)
            ->where('YEAR(data)', $ano)
            ->group_by('escola')
            ->order_by('escola', 'asc')
            ->get('ei_livro_ata')
            ->result_array();

        $escolas = ['' => 'Todas'] + array_column($livroATA2, 'escola', 'escola');

        $output->escolas = form_dropdown('', $escolas, $filtro['escola']);

        echo json_encode($output);
    }

    //--------------------------------------------------------------------

    public function ajax_list_notas_fiscais()
    {
        parse_str($this->input->post('filtro'), $filtro);
        $ano = $this->input->post('ano');
        $semestre = $this->input->post('semestre');
        $mes = $this->input->post('mes');
        $idMes = intval($mes) - ($semestre > 1 ? 6 : 0);

        $this->load->library('calendar');

        $nomeMes = $this->calendar->get_month_name($mes);

        $qb = $this->db
            ->select("'{$nomeMes}' AS mes")
            ->select("IF(a.nota_complementar IS NULL, SUM(e.valor_total_mes{$idMes}), NULL) AS valor_total_mes")
            ->select("a.nota_fiscal_mes{$idMes} AS nota_fiscal")
            ->select("a.data_emissao_mes{$idMes} AS data_emissao", false)
            ->select("a.codigo_alfa_mes{$idMes} AS codigo_alfa")
            ->select("a.status_mes{$idMes} AS status, a.id")
            ->select(["DATE_FORMAT(a.data_emissao_mes{$idMes}, '%d/%m/%Y') AS data_emissao_de"], false)
            ->select(["IF(COUNT(f.id) < 2, 1, IF(COUNT(g.id) < 2, 2, 0)) AS indice_complementar"], false)
            ->join('ei_alocacao b', 'b.id = a.id_alocacao')
            ->join('ei_alocacao_escolas c', 'c.id_alocacao = b.id')
            ->join('ei_alocados d', 'd.id_alocacao_escola = c.id AND d.id_cuidador = a.id_cuidador')
            ->join('ei_alocados_totalizacao e', 'e.id_alocado = d.id AND e.id_cuidador = d.id_cuidador', 'left')
            ->join('ei_pagamento_prestador f', 'f.id_cuidador = a.id_cuidador AND f.id = a.id_complementar_1', 'left')
            ->join('ei_pagamento_prestador g', 'g.id_cuidador = a.id_cuidador AND g.id = a.id_complementar_2', 'left')
            ->where('b.id_empresa', $this->session->userdata('empresa'))
            ->where('d.id_cuidador', $this->session->userdata('id'))
            ->where('b.ano', $ano)
            ->where('b.semestre', $semestre)
            ->group_by('a.id');
        if (!empty($filtro['profissional'])) {
            $qb->where('a.id_cuidador', $this->session->userdata('id'));
        }
        $sql = $qb->get_compiled_select('ei_pagamento_prestador a');

        $config = [
            'order' => ['id_alocacao', 'nota_complementar', 'mes', 'valor_total_mes', 'nota_fiscal', 'data_emissao', 'codigo_alfa', 'status'],
        ];
        $this->load->library('dataTables', $config);

        $output = $this->datatables->query($sql);

        $this->load->model('ei_pagamento_prestador_model', 'pagamento');

        $statusPagamento = $this->pagamento::STATUS;

        $data = [];

        foreach ($output->data as $row) {
            if ($row->indice_complementar) {
                $btn = '<button class="btn btn-sm btn-info" onclick="add_nota_fiscal_complementar(' . $row->id . ', ' . $idMes . ', ' . $row->indice_complementar . ')" title="Adicionar nota fiscal complementar"><i class="glyphicon glyphicon-plus"></i> N. F. complementar</button>';
            } else {
                $btn = '<button class="btn btn-sm btn-info" disabled title="Adicionar nota fiscal complementar"><i class="glyphicon glyphicon-plus"></i> N. F. complementar</button>';
            }
            $data[] = [
                $row->mes,
                number_format($row->valor_total_mes, 2, ',', '.'),
                $row->nota_fiscal,
                $row->data_emissao_de,
                $row->codigo_alfa,
                $statusPagamento[$row->status] ?? null,
                '<button class="btn btn-sm btn-info" onclick="edit_nota_fiscal(' . $row->id . ', ' . $idMes . ')" title="Editar nota fiscal"><i class="glyphicon glyphicon-pencil"></i></button>',
            ];
        }

        $output->data = $data;

        echo json_encode($output);
    }

    //--------------------------------------------------------------------

    public function ajax_new_nota_fiscal()
    {
        $semestre = $this->input->post('semestre');
        $ano = $this->input->post('ano');

        $alocacao = $this->db
            ->select('c.id AS id_alocacao, NULL as id, 0 AS indice_complementar', false)
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->where('c.ano', $ano)
            ->where('c.semestre', $semestre)
            ->where('a.id_cuidador', $this->session->userdata('id'))
            ->get('ei_alocados a')
            ->row();

        if (empty($alocacao)) {
            exit(json_encode(['erro' => 'Alocação semestral não encontrada.']));
        }

        $pagamentos = $this->db
            ->where('id_alocacao', $alocacao->id_alocacao)
            ->where('id_cuidador', $this->session->userdata('id'))
            ->order_by('id', 'asc')
            ->get('ei_pagamento_prestador')
            ->result();

        if (count($pagamentos) > 2) {
            exit(json_encode(['erro' => 'O limite de notas fiscais complementares foi atingido.']));
        }

        foreach ($pagamentos as $pagamento) {
            $alocacao->id = $pagamento->id;
            if (is_null($pagamento->id_complementar_1)) {
                $alocacao->indice_complementar = 1;
                break;
            }
            if (is_null($pagamento->id_complementar_2)) {
                $alocacao->indice_complementar = 2;
                break;
            }
        }

        $alocacao->id_mes = (int)$this->input->post('mes') - ($semestre > 1 ? 6 : 0);
        if ($semestre == '2') {
            $mesesCompetencia = [
                '1' => 'Julho',
                '2' => 'Agosto',
                '3' => 'Setembro',
                '4' => 'Outubro',
                '5' => 'Novembro',
                '6' => 'Dezembro',
            ];
        } else {
            $mesesCompetencia = [
                '1' => 'Janeiro',
                '2' => 'Fevereiro',
                '3' => 'Março',
                '4' => 'Abril',
                '5' => 'Maio',
                '6' => 'Junho',
                '7' => 'Julho',
            ];
        }
        $alocacao->meses_competencia = form_dropdown('', ['' => 'selecione...'] + $mesesCompetencia, $alocacao->id_mes);

        echo json_encode($alocacao);
    }

    //--------------------------------------------------------------------

    public function ajax_edit_nota_fiscal()
    {
        $id = $this->input->post('id');
        $idMes = $this->input->post('mes');

        $qb = $this->db
            ->select('a.id, b.semestre');
        if ($idMes) {
            $qb->select("id_alocacao, nota_fiscal_mes{$idMes} AS numero_nota_fiscal")
                ->select(["DATE_FORMAT(data_emissao_mes{$idMes}, '%d/%m/%Y') AS data_emissao"], false)
                ->select(["DATE_FORMAT(data_criacao_mes{$idMes}, '%d/%m/%Y %H:%i') AS data_criacao"], false)
                ->select("codigo_alfa_mes{$idMes} AS codigo_alfa")
                ->select("mes_competencia_{$idMes} AS mes_competencia")
                ->select("arquivo_nota_fiscal_mes{$idMes} AS arquivo_nota_fiscal")
                ->select("status_mes{$idMes} AS status");
        } else {
            $qb->select("id_alocacao, nota_fiscal_sub AS numero_nota_fiscal")
                ->select(["DATE_FORMAT(data_emissao_sub, '%d/%m/%Y') AS data_emissao"], false)
                ->select(["DATE_FORMAT(data_criacao_sub, '%d/%m/%Y %H:%i') AS data_criacao"], false)
                ->select("codigo_alfa_sub AS codigo_alfa")
                ->select("mes_competencia_sub AS mes_competencia")
                ->select('arquivo_nota_fiscal_sub AS arquivo_nota_fiscal')
                ->select("status_sub AS status");
        }
        $data = $qb
            ->join('ei_alocacao b', 'b.id = a.id_alocacao')
            ->where('a.id', $id)
            ->get('ei_pagamento_prestador a')
            ->row();

        if (empty($data)) {
            exit(json_encode(['erro' => 'Nota fiscal não encontrada.']));
        }

        $data->id_mes = $idMes;
        if ($data->semestre == '2') {
            $mesesCompetencia = [
                '1' => 'Julho',
                '2' => 'Agosto',
                '3' => 'Setembro',
                '4' => 'Outubro',
                '5' => 'Novembro',
                '6' => 'Dezembro',
            ];
        } else {
            $mesesCompetencia = [
                '1' => 'Janeiro',
                '2' => 'Fevereiro',
                '3' => 'Março',
                '4' => 'Abril',
                '5' => 'Maio',
                '6' => 'Junho',
                '7' => 'Julho',
            ];
        }
        $data->meses_competencia = form_dropdown('', ['' => 'selecione...'] + $mesesCompetencia, $data->mes_competencia);
        if (empty($data->data_criacao)) {
            $data->data_criacao = date('d/m/Y H:i');
        }

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_add_nota_fiscal_complementar()
    {
        $post = $this->input->post();
        $id = $post['id'];

        $this->load->model('ei_pagamento_prestador_model', 'pagamento');

        $oldData = $this->pagamento->findOne($id);
        if (empty($oldData)) {
            exit(json_encode(['erro' => 'Registro a ser complementado não encontrado.']));
        }

        $indiceComplementar = $post['indice_complementar'];
        unset($post['id']);
        unset($post['indice_complementar']);

        $idMes = $post['mes_competencia'] ?: $post['id_mes'];
        $post['data_emissao'] = strToDate($post['data_emissao']);
        $dataCriacao = datetimeFormat($oldData->{'data_criacao_mes' . $idMes}, true, true);
        $post['data_criacao'] = strToDatetime($dataCriacao ?: $post['data_criacao'] . ' ' . date('H:i:s'));
        unset($post['id_mes']);

        $post["arquivo_nota_fiscal_mes{$idMes}"] = $this->input->post('arquivo_nota_fiscal');
        $post["data_criacao_mes{$idMes}"] = $this->input->post('data_criacao');
        unset($post['arquivo_nota_fiscal']);
        unset($post['mes_referencia']);
        if (isset($_FILES['arquivo_nota_fiscal'])) {
            $_FILES["arquivo_nota_fiscal_mes{$idMes}"] = $_FILES['arquivo_nota_fiscal'];
            unset($_FILES['arquivo_nota_fiscal']);
        }

        $this->load->library('entities');
        $preData = $this->entities->create('EiPagamentoPrestador', $post);
        if (strlen($preData->status) == 0) {
            $preData->status = null;
        }

        $this->pagamento->setValidationRule('numero_nota_fiscal', 'max_length[255]');
        $this->pagamento->setValidationRule('data_emissao', 'valid_date');
        $this->pagamento->setValidationRule('codigo_alfa', 'max_length[100]');
        $this->pagamento->setValidationRule("arquivo_nota_fiscal_mes{$idMes}", 'uploaded[arquivo_nota_fiscal]|mime_in[arquivo_nota_fiscal.pdf]|max_length[255]');

        $this->pagamento->setValidationLabel('numero_nota_fiscal', 'Número Nota Fiscal');
        $this->pagamento->setValidationLabel('data_emissao', 'Data Emissão da Nota Fiscal');
        $this->pagamento->setValidationLabel('codigo_alfa', 'Código de Verificação/Validação');
        $this->pagamento->setValidationLabel('status', 'Status');
        $this->pagamento->setValidationLabel("arquivo_nota_fiscal_mes{$idMes}", 'Arquivo Nota Fiscal');

        if ($this->pagamento->validate($preData) == false) {
            exit(json_encode(['erro' => $this->pagamento->errors()]));
        }

        $this->pagamento->skipValidation();

        if ($idMes) {
            $data = [
                'id_alocacao' => $preData->id_alocacao,
                'id_cuidador' => $this->session->userdata('id'),
                'nota_complementar' => 1,
                "nota_fiscal_mes{$idMes}" => $preData->numero_nota_fiscal,
                "data_emissao_mes{$idMes}" => $preData->data_emissao,
                "codigo_alfa_mes{$idMes}" => $preData->codigo_alfa,
                "status_mes{$idMes}" => $preData->status,
                "mes_competencia_{$idMes}" => $preData->mes_competencia,
                "data_criacao_mes{$idMes}" => $preData->data_criacao,
            ];
            if (isset($_FILES["arquivo_nota_fiscal_mes{$idMes}"])) {
                $data["arquivo_nota_fiscal_mes{$idMes}"] = $preData->arquivo_nota_fiscal;
            }
        } else {
            $data = [
                'id_alocacao' => $preData->id_alocacao,
                'id_cuidador' => $this->session->userdata('id'),
                'nota_complementar' => 1,
                'nota_fiscal_sub' => $preData->numero_nota_fiscal,
                'data_emissao_sub' => $preData->data_emissao,
                'codigo_alfa_sub' => $preData->codigo_alfa,
                'status_sub' => $preData->status,
                'mes_competencia_sub' => $preData->mes_competencia,
                'data_criacao_sub' => $preData->data_criacao,
            ];
            if (isset($_FILES["arquivo_nota_fiscal_mes{$idMes}"])) {
                $data['arquivo_nota_fiscal_sub'] = $preData->arquivo_nota_fiscal;
            }
        }

        $this->db->trans_start();
        $this->pagamento->insert($data);
        $this->db
            ->set('id_complementar_' . $indiceComplementar, $this->pagamento->getInsertID())
            ->where('id', $id)
            ->update($this->pagamento->getTable());
        $this->db->trans_complete();

        if ($this->db->trans_status() == false) {
            exit(json_encode(['erro' => $this->pagamento->errors()]));
        }

        echo json_encode(['status' => true]);

    }

    //--------------------------------------------------------------------

    public function ajax_update_nota_fiscal()
    {
        $post = $this->input->post();
        $id = $post['id'];

        $this->load->model('ei_pagamento_prestador_model', 'pagamento');

        $oldData = $this->pagamento->findOne($id);
        if (empty($oldData)) {
            exit(json_encode(['erro' => 'Registro não encontrado.']));
        }

        $idMes = $post['mes_competencia'] ?: $post['id_mes'];
        $post['data_emissao'] = strToDate($post['data_emissao']);
        $dataCriacao = datetimeFormat($oldData->{'data_criacao_mes' . $idMes}, true, true);
        $post['data_criacao'] = strToDatetime($dataCriacao ?: $post['data_criacao'] . ':' . date('s'));
        unset($post['id_mes']);

        $post["arquivo_nota_fiscal_mes{$idMes}"] = $this->input->post('arquivo_nota_fiscal');
        $post["data_criacao_mes{$idMes}"] = $this->input->post('data_criacao');
        unset($post['arquivo_nota_fiscal']);
        unset($post['mes_referencia']);
        if (isset($_FILES['arquivo_nota_fiscal'])) {
            $_FILES["arquivo_nota_fiscal_mes{$idMes}"] = $_FILES['arquivo_nota_fiscal'];
            unset($_FILES['arquivo_nota_fiscal']);
        }

        $this->load->library('entities');
        $preData = $this->entities->create('EiPagamentoPrestador', $post);
        if (strlen($preData->status) == 0) {
            $preData->status = null;
        }

        $this->pagamento->setValidationRule('numero_nota_fiscal', 'max_length[255]');
        $this->pagamento->setValidationRule('data_emissao', 'valid_date');
        $this->pagamento->setValidationRule('codigo_alfa', 'max_length[100]');
        $this->pagamento->setValidationRule("arquivo_nota_fiscal_mes{$idMes}", 'uploaded[arquivo_nota_fiscal]|mime_in[arquivo_nota_fiscal.pdf]|max_length[255]');

        $this->pagamento->setValidationLabel('numero_nota_fiscal', 'Número Nota Fiscal');
        $this->pagamento->setValidationLabel('data_emissao', 'Data Emissão da Nota Fiscal');
        $this->pagamento->setValidationLabel('codigo_alfa', 'Código de Verificação/Validação');
        $this->pagamento->setValidationLabel('status', 'Status');
        $this->pagamento->setValidationLabel("arquivo_nota_fiscal_mes{$idMes}", 'Arquivo Nota Fiscal');

        if ($this->pagamento->validate($preData) == false) {
            exit(json_encode(['erro' => $this->pagamento->errors()]));
        }

        $this->pagamento->skipValidation();

        if ($idMes) {
            $data = [
                'id_alocacao' => $preData->id_alocacao,
                "nota_fiscal_mes{$idMes}" => $preData->numero_nota_fiscal,
                "data_emissao_mes{$idMes}" => $preData->data_emissao,
                "codigo_alfa_mes{$idMes}" => $preData->codigo_alfa,
                "status_mes{$idMes}" => $preData->status,
                "mes_competencia_{$idMes}" => $preData->mes_competencia,
                "data_criacao_mes{$idMes}" => $preData->data_criacao,
            ];
            if (isset($_FILES["arquivo_nota_fiscal_mes{$idMes}"])) {
                $data["arquivo_nota_fiscal_mes{$idMes}"] = $preData->arquivo_nota_fiscal;
            }
        } else {
            $data = [
                'id_alocacao' => $preData->id_alocacao,
                'nota_fiscal_sub' => $preData->numero_nota_fiscal,
                'data_emissao_sub' => $preData->data_emissao,
                'codigo_alfa_sub' => $preData->codigo_alfa,
                'status_sub' => $preData->status,
                'mes_competencia_sub' => $preData->mes_competencia,
                'data_criacao_sub' => $preData->data_criacao,
            ];
            if (isset($_FILES["arquivo_nota_fiscal_mes{$idMes}"])) {
                $data['arquivo_nota_fiscal_sub'] = $preData->arquivo_nota_fiscal;
            }
        }

        $this->pagamento->update($id, $data) or exit(json_encode(['erro' => $this->pagamento->errors()]));

        echo json_encode(['status' => true]);

    }

    //--------------------------------------------------------------------

    public function novo_apontamento_horas()
    {
        $ano = $this->input->post('ano');
        $semestre = $this->input->post('semestre');

        $alocacoes = $this->db
            ->select("b.id_escola, CONCAT_WS(' - ',b.codigo, b.escola) AS nome", false)
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->where('a.id_cuidador', $this->session->userdata('id'))
            ->where('c.ano', $ano)
            ->where('c.semestre', $semestre)
            ->group_by(['b.codigo', 'b.id_escola', 'b.escola'])
            ->order_by('b.codigo')
            ->order_by('b.escola')
            ->get('ei_alocados a')
            ->result_array();

        $escolas = array_column($alocacoes, 'nome', 'id_escola');
        $data['escolas'] = form_dropdown('', $escolas ?: ['' => 'selecione...']);

        echo json_encode($data);

    }

    //--------------------------------------------------------------------

    public function add_apontamento_horas()
    {
        $dia = $this->input->post('dia');
        $mes = $this->input->post('mes');
        $semestre = $this->input->post('semestre');
        $tipoEvento = $this->input->post('tipo_evento');
        $ano = $this->input->post('ano');
        $hora = $this->input->post('hora');
        $data = $ano . '-' . $mes . '-' . $dia;
        $dataHora = $data . ' ' . $hora . ':00';
        $timestamp = strtotime($dataHora);
        $diaFinal = date('t', $timestamp);
        if (empty($diaFinal)) {
            $diaFinal = '31';
        }

        $this->load->library('form_validation');

        $this->form_validation->set_rules('dia', 'Dia', "required|greater_than[0]|less_than_equal_to[{$diaFinal}]");
        if (in_array($tipoEvento, ['E', 'S'])) {
            $this->form_validation->set_rules('hora', 'Hora', 'required|valid_time');
            $this->form_validation->set_rules('qtde_horas', 'Qtde. Horas', 'valid_time');
        }
        if (in_array($tipoEvento, ['EE', 'HE', 'SL'])) {
            $this->form_validation->set_rules('horario_entrada', "Entrada ({$tipoEvento})", 'valid_time');
            $this->form_validation->set_rules('horario_saida', "Saída ({$tipoEvento})", 'valid_time');
        }
        $this->form_validation->set_rules('periodo', 'Período', 'required');
        $this->form_validation->set_rules('tipo_evento', 'Tipo de Evento', 'required');
        $this->form_validation->set_rules('justificativa', 'Justificativa', 'max_length[65535]');
        $this->form_validation->set_rules('observacoes', 'Observações', 'max_length[65535]');

        if ($this->form_validation->run() == false) {
            exit(json_encode(['erro' => $this->form_validation->error_string(' ', ' ')]));
        }

        $idUsuario = $this->session->userdata('id');

        $diaSemana = date('w', $timestamp);
        $periodo = $this->input->post('periodo');

        $this->db->trans_start();

        $alocacoes = $this->db
            ->select('c.id, c.semestre, b.id_escola')
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->join('ei_alocados_horarios d', "d.id_alocado = a.id AND d.periodo = '{$periodo}' AND '{$diaSemana}' IN (0, 6, d.dia_semana)", 'left', false)
            ->group_start()
            ->where('a.id_cuidador', $idUsuario)
            ->or_where('d.id_cuidador_sub1', $idUsuario)
            ->or_where('d.id_cuidador_sub2', $idUsuario)
            ->group_end()
            ->where('c.ano', $ano)
            ->where('c.semestre', $semestre)
            ->where("IF(d.periodo IS NULL, d.periodo = '{$periodo}', d.periodo = '{$periodo}')")
            ->group_by(['a.id', 'c.semestre'])
            ->order_by('c.semestre', 'desc')
            ->get('ei_alocados a')
            ->result();

        if (empty($alocacoes)) {
            exit(json_encode(['erro' => 'Usuário não alocado no mês ou período selecionado.']));
        }

        $alocacao = $alocacoes[0];
        if (empty($idEscola = $this->input->post('id_escola'))) {
            $idEscola = $alocacao->id_escola;
        }

        $frequenciasExistentes = $this->db
            ->where('id_usuario', $idUsuario)
            ->where('data_evento', $data)
            ->where('id_escola', $idEscola)
            ->group_start()
            ->where('(horario_entrada_1 IS NOT NULL AND horario_saida_1 IS NULL OR periodo_atual = 1)')
            ->or_where('(horario_entrada_2 IS NOT NULL AND horario_saida_2 IS NULL OR periodo_atual = 2)')
            ->or_where('(horario_entrada_3 IS NOT NULL AND horario_saida_3 IS NULL OR periodo_atual = 3)')
            ->group_end()
            ->order_by('data_evento', 'desc')
            ->get('ei_usuarios_frequencias')
            ->result();

        $frequenciaExistente = $frequenciasExistentes[count($alocacoes) - 1] ?? null;

//        if ($tipoEvento === 'E' and (isset($frequenciaExistente->periodo_atual) and $frequenciaExistente->periodo_atual == $periodo)) {
//            $frequenciaExistente = null;
//        }
        if (($tipoEvento === 'E' and !empty($frequenciaExistente->{'horario_entrada_real_' . $periodo})) or
            ($tipoEvento === 'S' and !empty($frequenciaExistente->{'horario_saida_real_' . $periodo}))) {
            $frequenciaExistente = null;
        }

        $idMes = intval($mes) - ($semestre == 2 ? 6 : 0);
        $qtdeHoras = $this->input->post('qtde_horas');
        if (strlen($qtdeHoras) > 0) {
            $qtdeHoras .= ':00';
        } else {
            $qtdeHoras = null;
        }
        $justificativa = $this->input->post('justificativa');
        $observacoes = $this->input->post('observacoes');
        if (in_array($tipoEvento, ['EE', 'HE', 'SL'])) {
            $tipoEventoEspecial = [
                'EE' => 'Evento Extra', 'HE' => 'Horas de Estudo', 'SL' => 'Sábado Letivo',
            ];
            $observacoes = implode(' - ', array_filter([$observacoes, $tipoEventoEspecial[$tipoEvento] . (strlen($qtdeHoras) > 0 ? ' (' . $qtdeHoras . ')' : '')]));
        }
        if (strlen($observacoes) == 0) {
            $observacoes = null;
        }

        $periodoAtual = $periodo;
        if ($tipoEvento === 'S' and !empty($frequenciaExistente->periodo_atual)) {
            $periodoAtual = $frequenciaExistente->periodo_atual;
        }
        $dataFrequencia = ['id_usuario' => $idUsuario,
            'data_evento' => $data,
            'periodo_atual' => $periodoAtual,
            'justificativa' => strlen($justificativa) > 0 ? $justificativa : null,
            'id_escola' => $idEscola ?? null,
        ];

        $dataFrequenciaAnterior = $this->db
            ->select('observacoes')
            ->select("DATE_FORMAT(horario_entrada_{$periodo}, '%H:%i') AS entrada_programada")
            ->select("DATE_FORMAT(horario_saida_{$periodo}, '%H:%i') AS saida_programada")
            ->select("DATE_FORMAT(horario_entrada_real_{$periodo}, '%H:%i') AS entrada_real")
            ->select("DATE_FORMAT(horario_saida_real_{$periodo}, '%H:%i') AS saida_real")
            ->where('id_usuario', $idUsuario)
            ->where("'{$tipoEvento}' = 'E'", null, false)
            ->where('data_evento', $data)
            ->where('periodo_atual', $periodo)
            ->where('id_escola', $idEscola ?? null)
            ->order_by('id', 'asc')
            ->get('ei_usuarios_frequencias')
            ->row();

        $dataApontamentos = $this->db
            ->select('a.id AS id_alocado_evento', false)
            ->select("g.horario_inicio_mes{$idMes} AS horario_inicio_mes", false)
            ->select("g.horario_termino_mes{$idMes} AS horario_termino_mes", false)
            ->select('j.*, f.minutos_tolerancia_entrada_saida, g.id AS id_alocado_horario', false)
            ->select(["GROUP_CONCAT(i.aluno ORDER BY i.aluno ASC SEPARATOR ', ') AS alunos"], false)
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->join('ei_ordem_servico_escolas d', 'd.id = b.id_os_escola')
            ->join('ei_ordem_servico e', 'e.id = d.id_ordem_servico')
            ->join('ei_contratos f', 'f.id = e.id_contrato')
            ->join('ei_alocados_horarios g', "g.id_alocado = a.id AND g.periodo = '{$periodoAtual}' AND ('{$diaSemana}' IN (0, 6, g.dia_semana) OR '{$tipoEvento}' IN ('FE', 'EM', 'RE', 'EE', 'HE', 'SL'))", 'left')
            ->join('ei_matriculados_turmas h', 'h.id_alocado_horario = g.id', 'left')
            ->join('ei_matriculados i', 'i.id = h.id_matriculado AND i.id_alocacao_escola = b.id', 'left')
            ->join('ei_apontamento j', "j.id_alocado = a.id AND j.data = '{$data}' AND IF(j.id_horario IS NOT NULL, j.id_horario = g.id, j.periodo = g.periodo OR j.periodo IS NULL)", 'left', false)
            ->where('c.id', $alocacao->id)
            ->where('a.id_cuidador', $idUsuario)
            ->where('g.id IS NOT NULL')
            ->group_by(['a.id', 'g.id', 'j.data', 'j.periodo', "j.horario_entrada_{$periodo}", "j.horario_saida_{$periodo}"])
            ->order_by('j.data', 'desc')
            ->order_by('j.periodo', 'desc')
            ->order_by("j.horario_entrada_{$periodo}", 'desc')
            ->order_by("j.horario_saida_{$periodo}", 'desc')
            ->get('ei_alocados a')
            ->result_array();

        if (empty($dataApontamentos)) {
            exit(json_encode(['erro' => 'Usuário não alocado no período ou mês selecionado.']));
        }

        $dataApontamento = [];

        foreach ($dataApontamentos as $rowApontamento) {
            if (empty($rowApontamento["horario_entrada_{$periodo}"]) or empty($rowApontamento["horario_saida_{$periodo}"])) {
                $dataApontamento = $rowApontamento;
                break;
            }
        }
        if (empty($dataApontamento)) {
            $dataApontamento = $dataApontamentos[0];
        }

        $this->load->helper('time');

        $apontamentoExistente = !empty($dataApontamento);
        $horarioInicioApontamento = $dataApontamento['horario_inicio_mes'] ?? null;
        $horarioTerminoApontamento = $dataApontamento['horario_termino_mes'] ?? null;
        $minutosToleranciaEntradaSaida = strlen($dataApontamento['minutos_tolerancia_entrada_saida']) > 0 ? $dataApontamento['minutos_tolerancia_entrada_saida'] * 60 : 0;
        if ($tipoEvento === 'S' and timeToSec($hora) < (timeToSec($horarioTerminoApontamento) - $minutosToleranciaEntradaSaida)) {
            $dataApontamento['status'] = 'SA';
            $dataApontamento['desconto'] = secToTime(timeToSec($dataApontamento['desconto']) - (timeToSec($horarioTerminoApontamento) - timeToSec($hora)));
        } elseif ($tipoEvento === 'E' and timeToSec($hora) > (timeToSec($horarioInicioApontamento) + $minutosToleranciaEntradaSaida)) {
            $dataApontamento['status'] = 'AT';
            $dataApontamento['desconto'] = secToTime(timeToSec($dataApontamento['desconto']) - (timeToSec($hora) - timeToSec($horarioInicioApontamento)));
        } elseif (in_array($tipoEvento, ['FE', 'EM', 'RE', 'EE', 'HE', 'SL'])) {
            $dataApontamento['status'] = $tipoEvento;
            if (in_array($dataApontamento['status'], ['EE', 'HE', 'SL'])) {
                $dataApontamento['desconto'] = $qtdeHoras;
            }
        }
        if ($apontamentoExistente and !empty($dataApontamento['status']) == false) {
            $dataApontamento['status'] = 'PN';
        }
        $dataFrequencia['alunos'] = $dataApontamento['alunos'] ?? null;
        $dataApontamento['observacoes'] = $observacoes;
        $dataApontamento['id_horario'] = $dataApontamento['id_alocado_horario'];
        unset($dataApontamento['id_alocado_horario']);
        unset($dataApontamento['horario_inicio_mes']);
        unset($dataApontamento['horario_termino_mes']);
        unset($dataApontamento['minutos_tolerancia_entrada_saida']);
        unset($dataApontamento['alunos']);

        $statusFrequencia = [
            'SA' => 'SA',
            'AT' => 'AT',
            'FE' => 'FR',
            'EM' => 'EF',
            'RE' => 'RE',
            'EE' => 'EE',
            'HE' => 'HE',
            'SL' => 'SL',
            'PN' => 'PN',
            'FA' => 'FT',
        ];

        if ($periodo == '3') {
            if ($tipoEvento != 'E') {
                $dataApontamento['horario_saida_3'] = $dataHora;
                $dataFrequencia['status_saida_3'] = $statusFrequencia[$dataApontamento['status']];
                $dataFrequencia['horario_saida_3'] = $horarioTerminoApontamento;
                $dataFrequencia['horario_saida_real_3'] = $dataHora;
            }
            if ($tipoEvento != 'S') {
                $dataApontamento['horario_entrada_3'] = $dataHora;
                $dataFrequencia['status_entrada_3'] = $statusFrequencia[$dataApontamento['status']];
                $dataFrequencia['horario_entrada_3'] = $horarioInicioApontamento;
                $dataFrequencia['horario_entrada_real_3'] = $dataHora;
            }
        } elseif
        ($periodo == '2') {
            if ($tipoEvento != 'E') {
                $dataApontamento['horario_saida_2'] = $dataHora;
                $dataFrequencia['status_saida_2'] = $statusFrequencia[$dataApontamento['status']];
                $dataFrequencia['horario_saida_2'] = $horarioTerminoApontamento;
                $dataFrequencia['horario_saida_real_2'] = $dataHora;
            }
            if ($tipoEvento != 'S') {
                $dataApontamento['horario_entrada_2'] = $dataHora;
                $dataFrequencia['status_entrada_2'] = $statusFrequencia[$dataApontamento['status']];
                $dataFrequencia['horario_entrada_2'] = $horarioInicioApontamento;
                $dataFrequencia['horario_entrada_real_2'] = $dataHora;
            }
        } elseif ($periodo == '1') {
            if ($tipoEvento != 'E') {
                $dataApontamento['horario_saida_1'] = $dataHora;
                $dataFrequencia['status_saida_1'] = $statusFrequencia[$dataApontamento['status']];
                $dataFrequencia['horario_saida_1'] = $horarioTerminoApontamento;
                $dataFrequencia['horario_saida_real_1'] = $dataHora;
            }
            if ($tipoEvento != 'S') {
                $dataApontamento['horario_entrada_1'] = $dataHora;
                $dataFrequencia['status_entrada_1'] = $statusFrequencia[$dataApontamento['status']];
                $dataFrequencia['horario_entrada_1'] = $horarioInicioApontamento;
                $dataFrequencia['horario_entrada_real_1'] = $dataHora;
            }
        }

        if (in_array($tipoEvento, ['EE', 'HE', 'SL'])) {
            $horarioEntradaEventoExtra = $this->input->post('horario_entrada');
            $horarioSaidaEventoExtra = $this->input->post('horario_saida');
            if (strlen($horarioEntradaEventoExtra) > 0) {
                $horarioEntradaEventoExtra = $data . ' ' . $this->input->post('horario_entrada') . ':00';
            }
            if (strlen($horarioSaidaEventoExtra) > 0) {
                $horarioSaidaEventoExtra = $data . ' ' . $this->input->post('horario_saida') . ':00';
            }

            $dataApontamento['horario_entrada_' . $periodo] = $horarioEntradaEventoExtra ? $data . ' ' . $horarioEntradaEventoExtra : null;
            $dataApontamento['horario_saida_' . $periodo] = $horarioSaidaEventoExtra ? $data . ' ' . $horarioSaidaEventoExtra : null;
            $dataFrequencia['horario_entrada_real_' . $periodo] = $dataApontamento['horario_entrada_' . $periodo];
            $dataFrequencia['horario_saida_real_' . $periodo] = $dataApontamento['horario_saida_' . $periodo];
            $dataFrequencia['horario_entrada_' . $periodo] = $horarioInicioApontamento ?: $horarioEntradaEventoExtra;
            $dataFrequencia['horario_saida_' . $periodo] = $horarioTerminoApontamento ?: $horarioSaidaEventoExtra;

            $descontoEntrada = timeToSec(explode(' ', $dataApontamento['horario_entrada_' . $periodo])[1] ?? null);
            $descontoSaida = timeToSec(explode(' ', $dataApontamento['horario_saida_' . $periodo])[1] ?? null);
            $descontoObs = secToTime($descontoSaida - $descontoEntrada, false);
            $dataFrequencia['observacoes'] = "Evento Extra ({$descontoObs})";


            $descontoEntrada = $this->input->post('horario_entrada');
            if (strlen($descontoEntrada) == 0) {
                $descontoEntrada = explode(' ', $dataApontamento['horario_entrada_' . $periodo])[1] ?? null;
            }
            $descontoSaida = $this->input->post('horario_saida');
            if (strlen($descontoSaida) == 0) {
                $descontoSaida = explode(' ', $dataApontamento['horario_saida_' . $periodo])[1] ?? null;
            }
            $dataApontamento['horario_entrada_' . $periodo] = $data . ' ' . $descontoEntrada;
            $dataApontamento['horario_saida_' . $periodo] = $data . ' ' . $descontoSaida;
            $descontoObs = secToTime(timeToSec($descontoSaida) - timeToSec($descontoEntrada), false);
            $dataFrequencia['observacoes'] = "Evento Extra ({$descontoObs})";
        }

        if ($frequenciaExistente) {
            $this->db->update('ei_usuarios_frequencias', $dataFrequencia, ['id' => $frequenciaExistente->id]);
        } else {
            $this->db->insert('ei_usuarios_frequencias', $dataFrequencia);
        }

        if ($apontamentoExistente) {
            if (!empty($dataApontamento['id'])) {
                unset($dataApontamento['id_alocado_evento']);
                $this->db->update('ei_apontamento', $dataApontamento, ['id' => $dataApontamento['id']]);
            } else {
                $dataApontamento['data'] = $dataFrequencia['data_evento'];
                $dataApontamento['periodo'] = $periodo;
                $dataApontamento['id_alocado'] = $dataApontamento['id_alocado_evento'];
                unset($dataApontamento['id_alocado_evento']);
                $this->db->insert('ei_apontamento', $dataApontamento);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() == false) {
            exit(json_encode(['erro' => 'Não foi possível cadastrar o apontamento de horas']));
        }

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    public function preparar_exclusao_apontamento_horas()
    {
        $data = $this->db
            ->select('a.data_evento, b.nome')
            ->join('usuarios b', 'b.id = a.id_usuario')
            ->where('a.id', $this->input->post('id'))
            ->get('ei_usuarios_frequencias a')
            ->row();
        if (empty($data)) {
            exit(json_encode(['erro' => 'Erro ao editar a medição.']));
        }
        $data->data_evento = dateFormat($data->data_evento);
        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function remove_apontamento_horas()
    {
        $id = $this->input->post('id');
        $periodoExcusao = $this->input->post('periodo_exclusao');
        $manterApontamento = $this->input->post('manter_apontamento');

        $medicao = $this->db
            ->where('id', $id)
            ->get('ei_usuarios_frequencias')
            ->row();

        $this->db->trans_start();

        if ($periodoExcusao) {
            $oldHorariosEntrada = [
                '1' => $medicao->horario_entrada_1,
                '2' => $medicao->horario_entrada_2,
                '3' => $medicao->horario_entrada_3,
            ];
            $oldPeriodos = array_filter(array_diff_key($oldHorariosEntrada, [$periodoExcusao => null]));
            $peridoAtual = array_key_last($oldPeriodos) ?? null;
        } else {
            $peridoAtual = null;
        }

        if ($periodoExcusao and $peridoAtual) {
            $this->db
                ->set('horario_entrada_' . $periodoExcusao, null)
                ->set('horario_entrada_real_' . $periodoExcusao, null)
                ->set('horario_saida_' . $periodoExcusao, null)
                ->set('horario_saida_real_' . $periodoExcusao, null)
                ->set('status_entrada_' . $periodoExcusao, null)
                ->set('status_saida_' . $periodoExcusao, null)
                ->set('automatico_entrada_' . $periodoExcusao, null)
                ->set('automatico_saida_' . $periodoExcusao, null)
                ->set('periodo_atual', $peridoAtual)
                ->where('id', $id)
                ->update('ei_usuarios_frequencias');
        } else {
            $this->db->delete('ei_usuarios_frequencias', ['id' => $id]);
        }

        if (empty($manterApontamento)) {
            $qb = $this->db
                ->select('a.id')
                ->join('ei_alocados b', 'b.id = a.id_alocado')
                ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
                ->where('c.id_escola', $medicao->id_escola)
                ->where('b.id_cuidador', $medicao->id_usuario)
                ->where('a.data', $medicao->data_evento);
            if ($periodoExcusao) {
                $qb->group_start()
                    ->where('periodo', $periodoExcusao)
                    ->or_where('periodo', null)
                    ->group_end();
            }
            $apontamento = $qb
                ->get('ei_apontamento a')
                ->row();

            if ($apontamento) {
                $this->db->delete('ei_apontamento', ['id' => $apontamento->id]);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() == false) {
            die(json_encode(['erro' => 'Não foi possível excluir o evento.']));
        }

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    private function validarApontamentoHoras($data)
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('dia', 'Dia', "required|greater_than[0]|less_than_equal_to[{$data['dia_final']}]");
        if (in_array($data['tipo_vento'], ['E', 'S'])) {
            $this->form_validation->set_rules('hora', 'Hora', 'required|valid_time');
            $this->form_validation->set_rules('qtde_horas', 'Qtde. Horas', 'valid_time');
        }
        $this->form_validation->set_rules('periodo', 'Período', 'required');
        $this->form_validation->set_rules('tipo_evento', 'Tipo de Evento', 'required');
        $this->form_validation->set_rules('justificativa', 'Justificativa', 'max_length[65535]');
        $this->form_validation->set_rules('observacoes', 'Observações', 'max_length[65535]');

        if ($this->form_validation->run() == false) {
            exit(json_encode(['erro' => $this->form_validation->error_string(' ', ' ')]));
        }
    }

    //--------------------------------------------------------------------

    public function ajax_preencher_livro_ata()
    {
        $idUsuario = $this->session->userdata('id');
        $dataEvento = strToDate($this->input->post('data_evento'));
        $ano = date('Y', strtotime($dataEvento));
        $diaSemana = date('w', strtotime($dataEvento));
        $periodo = $this->input->post('periodo');

        $dataApontamento = $this->db
            ->select(['b.escola, IFNULL(k.profissional, a.cuidador) AS profissional, g.id AS id_horario'], false)
            ->select(["DATE_FORMAT(k.data_inicio_periodo, '%d/%m/%Y') AS data_inicio_periodo"], false)
            ->select(["DATE_FORMAT(k.data_termino_periodo, '%d/%m/%Y') AS data_termino_periodo"], false)
            ->select(["GROUP_CONCAT(DISTINCT i.aluno ORDER BY i.aluno ASC SEPARATOR ', ') AS alunos"], false)
            ->select(["GROUP_CONCAT(DISTINCT i.modulo ORDER BY i.modulo ASC SEPARATOR ', ') AS modulo"], false)
            ->select(["GROUP_CONCAT(DISTINCT i.curso ORDER BY i.curso ASC SEPARATOR ', ') AS curso"], false)
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->join('ei_ordem_servico_escolas d', 'd.id = b.id_os_escola')
            ->join('ei_ordem_servico e', 'e.id = d.id_ordem_servico')
            ->join('ei_contratos f', 'f.id = e.id_contrato')
            ->join('ei_alocados_horarios g', "g.id_alocado = a.id AND g.dia_semana = '{$diaSemana}' AND (g.periodo = '{$periodo}' OR CHAR_LENGTH('{$periodo}') = 0)", 'left')
            ->join('ei_matriculados_turmas h', 'h.id_alocado_horario = g.id', 'left')
            ->join('ei_matriculados i', 'i.id = h.id_matriculado AND i.id_alocacao_escola = b.id', 'left')
            ->join('ei_apontamento j', "j.id_alocado = a.id AND j.data = '{$dataEvento}' AND j.periodo = '{$periodo}'", 'left')
            ->join('ei_livro_ata k', "k.id_usuario = a.id_cuidador AND k.data = '{$dataEvento}'", 'left')
            ->where('c.ano', $ano)
            ->where('a.id_cuidador', $idUsuario)
            ->where('g.periodo IS NOT NULL')
            ->group_by(['a.id', 'g.periodo'])
            ->get('ei_alocados a')
            ->row();

        if (empty($dataApontamento->id_horario)) {
            $this->load->model('ei_alocado_horario_model');
            $nomePeriodo = $this->ei_alocado_horario_model::PERIODOS;
            if (!empty($nomePeriodo[$periodo])) {
                exit(json_encode(['erro' => "O profissional não está alocado(a) no período da {$nomePeriodo[$periodo]}."]));
            }
            exit(json_encode(['erro' => "O profissional não está alocado(a) no período selecionado."]));
        }

        $data = [
            'data_inicio_periodo' => $dataApontamento->data_inicio_periodo ?? null,
            'data_termino_periodo' => $dataApontamento->data_termino_periodo ?? null,
            'profissional' => $dataApontamento->profissional ?? null,
            'alunos' => $dataApontamento->alunos ?? null,
            'curso' => $dataApontamento->curso ?? null,
            'modulo' => $dataApontamento->modulo ?? null,
            'escola' => $dataApontamento->escola ?? null,
            'atividades_realizadas' => $dataApontamento->atividades_realizadas ?? null,
            'dificuldades_encontradas' => $dataApontamento->dificuldades_encontradas ?? null,
            'sugestoes_observacoes' => $dataApontamento->sugestoes_observacoes ?? null,
        ];

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function salvar_livro_ata()
    {
        $this->load->library('entities');
        $data = $this->entities->create('EiLivroAta', $this->input->post());
        $data->id_usuario = $this->session->userdata('id');

        $this->load->model('ei_livro_ata_model', 'livro_ata');

        $this->livro_ata->setValidationRule('data_inicio_periodo', 'required|valid_date');
        $this->livro_ata->setValidationRule('data_termino_periodo', 'required|valid_date|after_or_equal_date[data_inicio_periodo]');
        $this->livro_ata->setValidationRule('atividades_realizadas', 'required|max_length[4294967295]');

        $this->livro_ata->setValidationLabel('data', 'Data Evento');
        $this->livro_ata->setValidationLabel('periodo', 'Período');
        $this->livro_ata->setValidationLabel('data_inicio_periodo', 'Início Período ATA');
        $this->livro_ata->setValidationLabel('data_termino_periodo', 'Término Período ATA');
        $this->livro_ata->setValidationLabel('atividades_realizadas', 'Atividades Realizadas');
        $this->livro_ata->setValidationLabel('dificuldades_encontradas', 'Dificuldades Encontradas');
        $this->livro_ata->setValidationLabel('sugestoes_observacoes', 'Sugestões Para Dificuldades/Observações');

        if ($this->livro_ata->save($data) == false) {
            exit(json_encode(['erro' => $this->livro_ata->errors()]));
        }

        if (empty($data->id)) {
            $usuario = $this->db
                ->select('a.sexo, b.foto')
                ->join('usuarios b', 'b.id = a.empresa')
                ->where('a.id', $data->id_usuario)
                ->get('usuarios a')
                ->row();

            $nomesPeriodo = $this->livro_ata::PERIODOS;
            $data2 = [
                'logoEmpresa' => 'imagens/usuarios/' . $usuario->foto,
                'sexo' => $usuario->sexo,
                'data' => dateFormat($data->data),
                'periodo' => strtolower($nomesPeriodo[$data->periodo] ?? ''),
            ];

            $this->load->library('email');

            $this->email
                ->set_mailtype('html')
                ->from('contato@rhsuite.com.br', 'RhSuite')
                ->to($this->session->userdata('email'))
                ->subject('Registro de Livro ATA')
                ->message($this->load->view('ei/email_notificacao_livro_ata', $data2, true))
                ->send();
        }

        echo json_encode(['status' => true]);
    }

}
