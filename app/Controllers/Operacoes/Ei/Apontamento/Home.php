<?php

namespace App\Controllers\Ei\Apontamento;

use App\Controllers\BaseController;

class Home extends BaseController
{

    public function index()
    {
        $controller = $this->router->class;

        $hasBaseController = get_parent_class($controller);

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

        // monta a view
        $this->load->view('ei/apontamento', $data);
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

    private function getDeptos(array $where = []): array
    {
        if ($this->session->userdata('nivel') == 10) {
            $supervisor = $this->session->userdata('id');
        } else {
            $supervisor = '';
        }

        $empresa = $where['empresa'] ?? null;
        $ano = $where['ano'] ?? null;
        $semestre = $where['semestre'] ?? null;

        $sql = "SELECT a.depto 
                FROM ei_diretorias a
                LEFT JOIN ei_escolas b ON b.id_diretoria = a.id
                LEFT JOIN ei_supervisores c ON c.id_escola = b.id
                LEFT JOIN ei_coordenacao d ON d.id = c.id_coordenacao
                WHERE a.id_empresa = '{$empresa}'
                      AND a.depto = 'Educação Inclusiva'
                      AND (d.id_usuario = '{$supervisor}' OR '{$supervisor}' = '')
                UNION
                SELECT 'Educação Inclusiva' AS depto
                UNION
                SELECT depto 
                FROM ei_alocacao 
                WHERE id_empresa = '{$empresa}' 
                       AND depto = 'Educação Inclusiva'
                       AND (id_supervisor = '{$supervisor}' OR '{$supervisor}' = '')
                       AND ano = '{$ano}' AND semestre = '{$semestre}'
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

    public function preparar_os()
    {
        $where = $this->input->post();
        unset($where['mes']);

        $totalOS = $this->db
            ->select('a.id, a.nome')
            ->join('ei_contratos b', 'b.id = a.id_contrato')
            ->join('ei_diretorias c', 'c.id = b.id_cliente')
            ->where('c.id', $where['diretoria'])
            ->where('c.depto', $where['depto'])
            ->where('a.ano', $where['ano'])
            ->where('a.semestre', $where['semestre'])
            ->get('ei_ordem_servico a')
            ->num_rows();

        if (empty($totalOS)) {
            exit(json_encode(['erro' => 'Nenhuma Ordem de Serviço disponível para alocação.']));
        }

        $alocacao = $this->db
            ->get_where('ei_alocacao', $where)
            ->row();

        $idAlocacao = $alocacao->id ?? null;

        $sql = "SELECT a.id, a.nome
                FROM ei_ordem_servico a
                INNER JOIN ei_contratos b ON 
                           b.id = a.id_contrato
                INNER JOIN ei_diretorias c ON 
                           c.id = b.id_cliente
                INNER JOIN ei_escolas d ON 
                           d.id_diretoria = c.id
                INNER JOIN ei_supervisores e ON 
                           e.id_escola = d.id
                LEFT JOIN ei_coordenacao f ON 
                           f.id = e.id_coordenacao AND 
                           f.ano = a.ano AND 
                           f.semestre = a.semestre
                LEFT JOIN ei_ordem_servico_escolas g ON 
                          g.id_ordem_servico = a.id AND 
                          g.id_escola = d.id
                LEFT JOIN ei_ordem_servico_profissionais h ON 
                          h.id_ordem_servico_escola = g.id
                LEFT JOIN ei_ordem_servico_horarios i ON 
                          i.id_os_profissional = h.id
                LEFT JOIN ei_alocados_horarios j ON 
                          j.id_os_horario = i.id
                LEFT JOIN ei_alocados k ON 
                          k.id = j.id_alocado
                LEFT JOIN ei_alocacao_escolas l ON 
                          l.id = k.id_alocacao_escola
                LEFT JOIN ei_alocacao m ON 
                          m.id = l.id_alocacao AND 
                          m.id = '{$idAlocacao}'
                WHERE c.id = '{$where['diretoria']}'
                      AND c.depto =  '{$where['depto']}'
                      AND a.ano =  '{$where['ano']}'
                      AND a.semestre =  '{$where['semestre']}'
                      AND j.id IS NULL
                GROUP BY a.id
                ORDER BY a.nome asc";

        $os = $this->db->query($sql)->result();

        if (empty($os)) {
            exit(json_encode(['erro' => 'Este semestre já está alocado.']));
        }

        $ordem_servico = ['' => 'Todas'] + array_column($os, 'nome', 'id');

        $escolas = $this->montarOSEscola($where);
        $data['ordem_servico'] = form_dropdown('', $ordem_servico, '');

        $data['escolas'] = form_multiselect('', $escolas, '');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function filtrar_os_escolas()
    {
        $where = $this->input->post();

        $escolas = $this->montarOSEscola($where);

        $data['escolas'] = form_multiselect('', $escolas, '');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    private function montarOSEscola(array $where = []): array
    {
        $qb = $this->db
            ->select(["c.id, CONCAT_WS(' - ', c.codigo, c.nome) AS nome"], false)
            ->join('ei_supervisores b', 'b.id_coordenacao = a.id')
            ->join('ei_escolas c', 'c.id = b.id_escola')
            ->join('ei_diretorias d', 'd.id = c.id_diretoria')
            ->join('ei_ordem_servico_escolas e', 'e.id_escola = c.id', 'left')
            ->join('ei_ordem_servico f', 'f.id = e.id_ordem_servico and f.ano = a.ano AND f.semestre = a.semestre', 'left')
            ->where('d.depto', $where['depto'] ?? null)
            ->where('d.id', $where['diretoria'] ?? null);
        if (!empty($where['ordem_servico'])) {
            $qb->where('f.id', $where['ordem_servico']);
        } else {
            $qb->where('a.ano', $where['ano'])
                ->where('a.semestre', $where['semestre']);
        }
        $osEscolas = $qb
            ->group_by(['c.id', 'c.codigo', 'c.nome'])
            ->order_by('IF(CHAR_LENGTH(c.codigo) > 0, c.codigo, CAST(c.nome AS DECIMAL))', 'asc', false)
            ->get('ei_coordenacao a')
            ->result();

        return array_column($osEscolas, 'nome', 'id');
    }

    //--------------------------------------------------------------------

    public function iniciar_semestre()
    {
        $empresa = $this->session->userdata('empresa');
        $departamento = $this->input->post('depto');
        $idDiretoria = $this->input->post('diretoria');
        $idSupervisor = $this->input->post('supervisor');
        $ano = $this->input->post('ano');
        $mes = intval($this->input->post('mes'));
        $semestre = $this->input->post('semestre');
        if (empty($semestre)) {
            $semestre = $mes > 7 ? '2' : '1';
        }
        $idMes = $mes - ($semestre > 1 ? 6 : 0);
        $iniciarMapaVisitacao = $this->input->post('possui_mapa_visitacao');
        $ordemServico = $this->input->post('ordem_servico');
        $escolas = $this->input->post('escolas');

        $alocacao = $this->db
            ->where('id_empresa', $empresa)
            ->where('depto', $departamento)
            ->where('id_diretoria', $idDiretoria)
            ->where('id_supervisor', $idSupervisor)
            ->where('ano', $ano)
            ->where('semestre', $semestre)
            ->get('ei_alocacao')
            ->row();

        $this->db->trans_begin();

        if (isset($alocacao->id)) {
            $idAlocacao = $alocacao->id;
        } else {
            $diretoria = $this->db
                ->select('nome, municipio, id_coordenador')
                ->where('id_empresa', $empresa)
                ->where('id', $idDiretoria)
                ->where('depto', $departamento)
                ->get('ei_diretorias')
                ->row();

            $supervisor = $this->db
                ->select('nome')
                ->where('id', $idSupervisor)
                ->get('usuarios')
                ->row();

            $rowOrdemServico = $this->db
                ->select('id, nome')
                ->where('id', $ordemServico)
                ->get('ei_ordem_servico')
                ->row();

            $data = [
                'id_empresa' => $empresa,
                'depto' => $departamento,
                'id_diretoria' => $idDiretoria,
                'diretoria' => $diretoria->nome,
                'id_supervisor' => $idSupervisor,
                'supervisor' => $supervisor->nome,
                'municipio' => $diretoria->municipio,
                'coordenador' => $diretoria->id_coordenador,
                'ano' => $ano,
                'semestre' => $semestre,
                'id_ordem_servico' => $rowOrdemServico->id,
                'ordem_servico' => $rowOrdemServico->nome,
            ];

            $this->db->insert('ei_alocacao', $data);

            $idAlocacao = $this->db->insert_id();
        }

        $qb = $this->db
            ->select("'{$idAlocacao}' AS id_alocacao, a.id AS id_os_escola, b.id AS id_escola", false)
            ->select('b.codigo, b.nome AS escola, b.municipio, c.nome AS ordem_servico, d.contrato', false)
            ->join('ei_escolas b', 'b.id = a.id_escola')
            ->join('ei_ordem_servico c', 'c.id = a.id_ordem_servico')
            ->join('ei_contratos d', 'd.id = c.id_contrato')
            ->join('ei_diretorias e', 'e.id = d.id_cliente')
            ->join('ei_supervisores f', 'f.id_escola = b.id')
            ->join('ei_coordenacao g', 'g.id = f.id_coordenacao AND g.ano = c.ano AND g.semestre = c.semestre')
            ->join('ei_funcoes_supervisionadas h', 'h.id_supervisor = g.id')
            ->join('ei_ordem_servico_profissionais i', 'i.id_ordem_servico_escola = a.id')
            ->join('ei_ordem_servico_horarios j', 'j.id_os_profissional = i.id', 'left')
            ->where('e.id_empresa', $empresa)
            ->where('e.depto', $departamento)
            ->where('e.id', $idDiretoria)
            ->where('g.id_usuario', $idSupervisor)
            ->where('c.ano', $ano)
            ->where('c.semestre', $semestre)
            ->where('(j.id_funcao = h.funcao OR j.id_funcao IS NULL)', null, false);
        if ($ordemServico) {
            $qb->where('c.id', $ordemServico);
        }
        if ($escolas) {
            $qb->where_in('b.id', $escolas);
        }
        $alocacaoEscolas = $qb
            ->group_by('a.id')
            ->order_by('b.nome', 'asc')
            ->get('ei_ordem_servico_escolas a')
            ->result_array();

        if (!$alocacaoEscolas) {
            $this->db->trans_rollback();
            exit(json_encode(['erro' => 'Nenhuma escola encontrada.']));
        }

        $this->db->insert_batch('ei_alocacao_escolas', $alocacaoEscolas);

        $cuidadores = $this->db
            ->select('d.id AS id_alocacao_escola, a.id AS id_os_profissional, a.id_usuario AS id_cuidador, b.nome AS cuidador', false)
            ->select('a.valor_hora_operacional, a.horas_mensais_custo, a.data_inicio_contrato, a.data_termino_contrato', false)
            ->select(["ROUND((TIME_TO_SEC(a.horas_mensais_custo) / 3600) * a.valor_hora_operacional, 2) AS valor_total"], false)
            ->join('usuarios b', 'b.id = a.id_usuario')
            ->join('ei_ordem_servico_escolas c', 'c.id = a.id_ordem_servico_escola')
            ->join('ei_alocacao_escolas d', 'd.id_os_escola = c.id')
            ->join('ei_alocacao e', 'e.id = d.id_alocacao')
            ->join('ei_supervisores f', 'f.id_escola = d.id_escola')
            ->join('ei_coordenacao g', 'g.id = f.id_coordenacao AND g.id_usuario = e.id_supervisor AND g.ano = e.ano AND g.semestre = e.semestre')
            ->join('ei_funcoes_supervisionadas h', 'h.id_supervisor = g.id')
            ->join('ei_ordem_servico_horarios i', 'i.id_os_profissional = a.id', 'left')
            ->where('d.id_alocacao', $idAlocacao)
            ->where_in('b.status', [1, 3])
            ->where_in('c.id', array_column($alocacaoEscolas, 'id_os_escola'))
            ->where("(a.id_supervisor = {$idSupervisor} OR a.id_supervisor IS NULL)", null, false)
            ->where('(i.id_funcao = h.funcao OR i.id_funcao IS NULL)', null, false)
            ->group_by('a.id')
            ->get('ei_ordem_servico_profissionais a')
            ->result_array();

        if (!$cuidadores) {
            $this->db->trans_rollback();
            exit(json_encode(['erro' => 'Nenhum cuidador encontrado.']));
        }

        $this->db->insert_batch('ei_alocados', $cuidadores);

        if ($iniciarMapaVisitacao === '1') {
            $mapaVisitacao = $this->db
                ->select('a.id_alocacao, a.id_escola, a.escola, a.municipio')
                ->join('ei_alocacao b', 'b.id = a.id_alocacao')
                ->join('ei_mapa_unidades c', 'c.id_alocacao = b.id AND c.id_escola = a.id_escola', 'left')
                ->where('b.id', $idAlocacao)
                ->where('c.id', null)
                ->group_by(['a.id_escola'])
                ->get('ei_alocacao_escolas a')
                ->result_array();

            if ($mapaVisitacao) {
                $this->db->insert_batch('ei_mapa_unidades', $mapaVisitacao);
            }
        }

        $alunos = $this->db
            ->select('d.id AS id_alocacao_escola, a.id AS id_os_aluno, a.id_aluno, b.nome AS aluno', false)
            ->select('b.status, b.hipotese_diagnostica, a.modulo, a.data_inicio, a.data_termino', false)
            ->select('a.id_aluno_curso, a2.id_curso, a3.nome AS curso', false)
            ->join('ei_alunos b', 'b.id = a.id_aluno')
            ->join('ei_ordem_servico_escolas c', 'c.id = a.id_ordem_servico_escola')
            ->join('ei_alunos_cursos a2', 'a2.id = a.id_aluno_curso AND a2.id_aluno = b.id')
            ->join('ei_cursos a3', 'a3.id = a2.id_curso')
            ->join('ei_alocacao_escolas d', 'd.id_os_escola = c.id')
            ->join('ei_alocacao e', 'e.id = d.id_alocacao')
            ->join('ei_supervisores f', 'f.id_escola = d.id_escola')
            ->join('ei_coordenacao g', 'g.id = f.id_coordenacao AND g.ano = e.ano AND g.semestre = e.semestre')
            ->join('ei_funcoes_supervisionadas h', 'h.id_supervisor = g.id')
            ->join('ei_ordem_servico_profissionais i', 'i.id_ordem_servico_escola = c.id')
            ->join('ei_ordem_servico_horarios j', 'j.id_os_profissional = i.id', 'left')
            ->where('d.id_alocacao', $idAlocacao)
            ->where_in('c.id', array_column($alocacaoEscolas, 'id_os_escola'))
            ->where('(j.id_funcao = h.funcao OR j.id_funcao IS NULL)', null, false)
            ->group_by('a.id')
            ->get('ei_ordem_servico_alunos a')
            ->result_array();

        if ($alunos) {
            $this->db->insert_batch('ei_matriculados', $alunos);
        }

        $mes1 = $semestre > 1 ? '07' : '01';
        $mes2 = $semestre > 1 ? '08' : '02';
        $mes3 = $semestre > 1 ? '09' : '03';
        $mes4 = $semestre > 1 ? '10' : '04';
        $mes5 = $semestre > 1 ? '11' : '05';
        $mes6 = $semestre > 1 ? '12' : '06';
        if ($semestre === '1') {
            $mes7 = '07';
        }

        $diaIniMes1 = date('Y-m-d', strtotime("{$ano}-{$mes1}-01"));
        $diaIniMes2 = date('Y-m-d', strtotime("{$ano}-{$mes2}-01"));
        $diaIniMes3 = date('Y-m-d', strtotime("{$ano}-{$mes3}-01"));
        $diaIniMes4 = date('Y-m-d', strtotime("{$ano}-{$mes4}-01"));
        $diaIniMes5 = date('Y-m-d', strtotime("{$ano}-{$mes5}-01"));
        $diaIniMes6 = date('Y-m-d', strtotime("{$ano}-{$mes6}-01"));
        if ($semestre === '1') {
            $diaIniMes7 = date('Y-m-d', strtotime("{$ano}-{$mes7}-01"));
        }

        $diaFimMes1 = date('Y-m-t', strtotime($diaIniMes1));
        $diaFimMes2 = date('Y-m-t', strtotime($diaIniMes2));
        $diaFimMes3 = date('Y-m-t', strtotime($diaIniMes3));
        $diaFimMes4 = date('Y-m-t', strtotime($diaIniMes4));
        $diaFimMes5 = date('Y-m-t', strtotime($diaIniMes5));
        $diaFimMes6 = date('Y-m-t', strtotime($diaIniMes6));
        if ($semestre === '1') {
            $diaFimMes7 = date('Y-m-t', strtotime($diaIniMes7));
        }

        $qb = $this->db
            ->select('c.id AS id_alocado, a.id AS id_os_horario')
            ->select('f.nome AS cargo, e.nome AS funcao')
            ->select('f.nome AS cargo_mes2, e.nome AS funcao_mes2')
            ->select('f.nome AS cargo_mes3, e.nome AS funcao_mes3')
            ->select('f.nome AS cargo_mes4, e.nome AS funcao_mes4')
            ->select('f.nome AS cargo_mes5, e.nome AS funcao_mes5')
            ->select('f.nome AS cargo_mes6, e.nome AS funcao_mes6');
        if ($semestre === '1') {
            $qb->select('f.nome AS cargo_mes7, e.nome AS funcao_mes7');
        }
        $qb->select('a.dia_semana, a.periodo')
            ->select("(CASE WHEN {$mes1} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino)) THEN a.horario_inicio END) AS horario_inicio_mes1", false)
            ->select("(CASE WHEN {$mes2} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino)) THEN a.horario_inicio END) AS horario_inicio_mes2", false)
            ->select("(CASE WHEN {$mes3} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino)) THEN a.horario_inicio END) AS horario_inicio_mes3", false)
            ->select("(CASE WHEN {$mes4} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino)) THEN a.horario_inicio END) AS horario_inicio_mes4", false)
            ->select("(CASE WHEN {$mes5} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino)) THEN a.horario_inicio END) AS horario_inicio_mes5", false)
            ->select("(CASE WHEN {$mes6} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino)) THEN a.horario_inicio END) AS horario_inicio_mes6", false);
        if ($semestre === '1') {
            $qb->select("(CASE WHEN {$mes7} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino)) THEN a.horario_inicio END) AS horario_inicio_mes7", false);
        }
        $qb->select("(CASE WHEN {$mes1} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino)) THEN a.horario_termino END) AS horario_termino_mes1", false)
            ->select("(CASE WHEN {$mes2} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino)) THEN a.horario_termino END) AS horario_termino_mes2", false)
            ->select("(CASE WHEN {$mes3} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino)) THEN a.horario_termino END) AS horario_termino_mes3", false)
            ->select("(CASE WHEN {$mes4} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino)) THEN a.horario_termino END) AS horario_termino_mes4", false)
            ->select("(CASE WHEN {$mes5} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino)) THEN a.horario_termino END) AS horario_termino_mes5", false)
            ->select("(CASE WHEN {$mes6} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino)) THEN a.horario_termino END) AS horario_termino_mes6", false);
        if ($semestre === '1') {
            $qb->select("(CASE WHEN {$mes7} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino)) THEN a.horario_termino END) AS horario_termino_mes7", false);
        }
        $qb->select("(CASE WHEN {$mes1} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino)) THEN TIMEDIFF(a.horario_termino, a.horario_inicio) END) AS total_horas_mes1", false)
            ->select("(CASE WHEN {$mes2} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino)) THEN TIMEDIFF(a.horario_termino, a.horario_inicio) END) AS total_horas_mes2", false)
            ->select("(CASE WHEN {$mes3} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino)) THEN TIMEDIFF(a.horario_termino, a.horario_inicio) END) AS total_horas_mes3", false)
            ->select("(CASE WHEN {$mes4} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino)) THEN TIMEDIFF(a.horario_termino, a.horario_inicio) END) AS total_horas_mes4", false)
            ->select("(CASE WHEN {$mes5} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino)) THEN TIMEDIFF(a.horario_termino, a.horario_inicio) END) AS total_horas_mes5", false)
            ->select("(CASE WHEN {$mes6} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino)) THEN TIMEDIFF(a.horario_termino, a.horario_inicio) END) AS total_horas_mes6", false);
        if ($semestre === '1') {
            $qb->select("(CASE WHEN {$mes7} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino)) THEN TIMEDIFF(a.horario_termino, a.horario_inicio) END) AS total_horas_mes7", false);
        }
        $qb->select(['a.data_inicio_contrato, a.data_termino_contrato, a.valor_hora_operacional, a.horas_mensais_custo, l.valor AS valor_hora_funcao'], false)
            ->select(['IF(a.valor_hora_operacional > 0, a.valor_hora_operacional, l.valor_pagamento) AS valor_hora_operacional'], false)
            ->select(["IF({$mes1} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino)), WEEK(DATE_SUB(IF(MONTH(MAX(h.data_termino)) = {$mes1}, MAX(h.data_termino), '{$diaFimMes1}'), INTERVAL ((7 + DATE_FORMAT(IF(MONTH(MAX(h.data_termino)) = {$mes1}, MAX(h.data_termino), '$diaFimMes1'), '%w') - dia_semana) % 7) DAY)) - WEEK(DATE_ADD(IF(MONTH(MIN(h.data_inicio)) = {$mes1}, MIN(h.data_inicio), '{$diaIniMes1}'), INTERVAL (((7 - DATE_FORMAT(IF(MONTH(MIN(h.data_inicio)) = {$mes1}, MIN(h.data_inicio), '{$diaIniMes1}'), '%w')) + dia_semana) % 7) DAY)) + 1, 0) AS total_semanas_mes1"], false)
            ->select(["IF({$mes2} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino)), WEEK(DATE_SUB(IF(MONTH(MAX(h.data_termino)) = {$mes2}, MAX(h.data_termino), '{$diaFimMes2}'), INTERVAL ((7 + DATE_FORMAT(IF(MONTH(MAX(h.data_termino)) = {$mes2}, MAX(h.data_termino), '$diaFimMes2'), '%w') - dia_semana) % 7) DAY)) - WEEK(DATE_ADD(IF(MONTH(MIN(h.data_inicio)) = {$mes2}, MIN(h.data_inicio), '{$diaIniMes2}'), INTERVAL (((7 - DATE_FORMAT(IF(MONTH(MIN(h.data_inicio)) = {$mes2}, MIN(h.data_inicio), '{$diaIniMes2}'), '%w')) + dia_semana) % 7) DAY)) + 1, 0) AS total_semanas_mes2"], false)
            ->select(["IF({$mes3} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino)), WEEK(DATE_SUB(IF(MONTH(MAX(h.data_termino)) = {$mes3}, MAX(h.data_termino), '{$diaFimMes3}'), INTERVAL ((7 + DATE_FORMAT(IF(MONTH(MAX(h.data_termino)) = {$mes3}, MAX(h.data_termino), '$diaFimMes3'), '%w') - dia_semana) % 7) DAY)) - WEEK(DATE_ADD(IF(MONTH(MIN(h.data_inicio)) = {$mes3}, MIN(h.data_inicio), '{$diaIniMes3}'), INTERVAL (((7 - DATE_FORMAT(IF(MONTH(MIN(h.data_inicio)) = {$mes3}, MIN(h.data_inicio), '{$diaIniMes3}'), '%w')) + dia_semana) % 7) DAY)) + 1, 0) AS total_semanas_mes3"], false)
            ->select(["IF({$mes4} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino)), WEEK(DATE_SUB(IF(MONTH(MAX(h.data_termino)) = {$mes4}, MAX(h.data_termino), '{$diaFimMes4}'), INTERVAL ((7 + DATE_FORMAT(IF(MONTH(MAX(h.data_termino)) = {$mes4}, MAX(h.data_termino), '$diaFimMes4'), '%w') - dia_semana) % 7) DAY)) - WEEK(DATE_ADD(IF(MONTH(MIN(h.data_inicio)) = {$mes4}, MIN(h.data_inicio), '{$diaIniMes4}'), INTERVAL (((7 - DATE_FORMAT(IF(MONTH(MIN(h.data_inicio)) = {$mes4}, MIN(h.data_inicio), '{$diaIniMes4}'), '%w')) + dia_semana) % 7) DAY)) + 1, 0) AS total_semanas_mes4"], false)
            ->select(["IF({$mes5} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino)), WEEK(DATE_SUB(IF(MONTH(MAX(h.data_termino)) = {$mes5}, MAX(h.data_termino), '{$diaFimMes5}'), INTERVAL ((7 + DATE_FORMAT(IF(MONTH(MAX(h.data_termino)) = {$mes5}, MAX(h.data_termino), '$diaFimMes5'), '%w') - dia_semana) % 7) DAY)) - WEEK(DATE_ADD(IF(MONTH(MIN(h.data_inicio)) = {$mes5}, MIN(h.data_inicio), '{$diaIniMes5}'), INTERVAL (((7 - DATE_FORMAT(IF(MONTH(MIN(h.data_inicio)) = {$mes5}, MIN(h.data_inicio), '{$diaIniMes5}'), '%w')) + dia_semana) % 7) DAY)) + 1, 0) AS total_semanas_mes5"], false)
            ->select(["IF({$mes6} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino)), WEEK(DATE_SUB(IF(MONTH(MAX(h.data_termino)) = {$mes6}, MAX(h.data_termino), '{$diaFimMes6}'), INTERVAL ((7 + DATE_FORMAT(IF(MONTH(MAX(h.data_termino)) = {$mes6}, MAX(h.data_termino), '$diaFimMes6'), '%w') - dia_semana) % 7) DAY)) - WEEK(DATE_ADD(IF(MONTH(MIN(h.data_inicio)) = {$mes6}, MIN(h.data_inicio), '{$diaIniMes6}'), INTERVAL (((7 - DATE_FORMAT(IF(MONTH(MIN(h.data_inicio)) = {$mes6}, MIN(h.data_inicio), '{$diaIniMes6}'), '%w')) + dia_semana) % 7) DAY)) + 1, 0) AS total_semanas_mes6"], false);
        if ($semestre === '1') {
            $qb->select(["IF({$mes7} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino)), WEEK(DATE_SUB(IF(MONTH(MAX(h.data_termino)) = {$mes7}, MAX(h.data_termino), '{$diaFimMes7}'), INTERVAL ((7 + DATE_FORMAT(IF(MONTH(MAX(h.data_termino)) = {$mes7}, MAX(h.data_termino), '$diaFimMes7'), '%w') - dia_semana) % 7) DAY)) - WEEK(DATE_ADD(IF(MONTH(MIN(h.data_inicio)) = {$mes7}, MIN(h.data_inicio), '{$diaIniMes7}'), INTERVAL (((7 - DATE_FORMAT(IF(MONTH(MIN(h.data_inicio)) = {$mes7}, MIN(h.data_inicio), '{$diaIniMes7}'), '%w')) + dia_semana) % 7) DAY)) + 1, 0) AS total_semanas_mes7"], false);
        }
        $horarios = $qb
            ->join('ei_ordem_servico_profissionais b', 'b.id = a.id_os_profissional')
            ->join('ei_alocados c', 'c.id_os_profissional = b.id')
            ->join('ei_alocacao_escolas d', 'd.id = c.id_alocacao_escola')
            ->join('ei_alocacao d2', 'd2.id = d.id_alocacao')
            ->join('ei_supervisores m', 'm.id_escola = d.id_escola')
            ->join('ei_coordenacao n', 'n.id = m.id_coordenacao AND n.id_usuario = d2.id_supervisor AND n.ano = d2.ano AND n.semestre = d2.semestre')
            ->join('ei_funcoes_supervisionadas o', 'o.id_supervisor = n.id', 'left')
            ->join('empresa_funcoes e', 'e.id = a.id_funcao', 'left')
            ->join('empresa_cargos f', 'f.id = e.id_cargo', 'left')
            ->join('ei_ordem_servico_turmas g', 'g.id_os_horario = a.id', 'left')
            ->join('ei_ordem_servico_alunos h', 'h.id = g.id_os_aluno', 'left')
            ->join('ei_ordem_servico_escolas i', 'i.id = b.id_ordem_servico_escola', 'left')
            ->join('ei_ordem_servico j', 'j.id = i.id_ordem_servico', 'left')
            ->join('ei_contratos k', 'k.id = j.id_contrato', 'left')
            ->join('ei_valores_faturamento l', 'l.id_contrato = k.id AND l.ano = j.ano AND l.semestre = j.semestre AND l.id_cargo = f.id AND l.id_funcao = e.id', 'left')
            ->where('d.id_alocacao', $idAlocacao)
            ->where_in('d.id_os_escola', array_column($alocacaoEscolas, 'id_os_escola'))
            ->where_in('b.id', array_column($cuidadores, 'id_os_profissional'))
            ->where('(o.funcao = a.id_funcao OR a.id_funcao IS NULL)', null, false)
            ->group_by('a.id')
            ->get('ei_ordem_servico_horarios a')
            ->result_array();

        $this->db->insert_batch('ei_alocados_horarios', $horarios);

        $turmas = $this->db
            ->select('d.id AS id_matriculado, e.id AS id_alocado_horario')
            ->join('ei_ordem_servico_alunos b', 'b.id = a.id_os_aluno')
            ->join('ei_ordem_servico_horarios c', 'c.id = a.id_os_horario')
            ->join('ei_matriculados d', 'd.id_os_aluno = b.id')
            ->join('ei_alocados_horarios e', 'e.id_os_horario = c.id')
            ->join('ei_alocados f', 'f.id = e.id_alocado')
            ->join('ei_alocacao_escolas g', 'g.id = f.id_alocacao_escola')
            ->where('g.id_alocacao', $idAlocacao)
            ->where_in('g.id_os_escola', array_column($alocacaoEscolas, 'id_os_escola'))
            ->where_in('f.id_os_profissional', array_column($cuidadores, 'id_os_profissional'))
            ->where_in('d.id_os_aluno', array_column($alunos, 'id_os_aluno'))
            ->where_in('e.id_os_horario', array_column($horarios, 'id_os_horario'))
            ->get('ei_ordem_servico_turmas a')
            ->result_array();

        if ($turmas) {
            $this->db->insert_batch('ei_matriculados_turmas', $turmas);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            exit(json_encode(['erro' => 'Erro ao iniciar semestre.']));
        }

        $this->db->trans_commit();

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    public function montar_os_restantes()
    {
        $data = $this->input->post();
        $ano = $data['ano'];
        $mes = $data['mes'];
        $semestre = $data['semestre'] ?? '';

        if (empty($semestre)) {
            $semestre = intval($data['mes']) > 7 ? '2' : '1';
        }
        if (!empty($data['semestre_anterior'])) {
            $timestamp = mktime(0, 0, 0, (int)$mes, 1, (int)$ano);
            $dataSemestreAnterior = new DateTime(date('Y-m-d', $timestamp));
            if ($mes === '07' and $semestre === '1') {
                $dataSemestreAnterior->sub(new DateInterval('P7M'));
            } else {
                $dataSemestreAnterior->sub(new DateInterval('P6M'));
            }
            $mes = $dataSemestreAnterior->format('m');
            $ano = $dataSemestreAnterior->format('Y');
            $semestre = intval($mes) > 7 ? '2' : '1';
        }

        $data['ano_os'] = $ano;
        $data['semestre_os'] = $semestre;

        $ordemServico = $this->db
            ->select('a.id, a.nome')
            ->join('ei_contratos b', 'b.id = a.id_contrato')
            ->join('ei_diretorias c', 'c.id = b.id_cliente')
            ->join('ei_escolas d', 'd.id_diretoria = c.id')
            ->join('ei_supervisores e', 'e.id_escola = d.id')
            ->join('ei_coordenacao f', 'f.id = e.id_coordenacao')
            ->where('c.id_empresa', $this->session->userdata('empresa'))
            ->where('c.depto', $data['depto'])
            ->where('c.id', $data['diretoria'])
            ->where('f.id_usuario', $data['supervisor'])
            ->where('a.ano', $ano)
            ->where('a.semestre', $semestre)
            ->group_by(['a.id', 'd.id'])
            ->order_by('a.nome', 'ASC')
            ->get('ei_ordem_servico a')
            ->result();

        $os = ['' => 'selecione...'] + array_column($ordemServico, 'nome', 'id');

        $data['ordens_servico'] = form_dropdown('ordem_servico', $os, '', 'class="form-control"');
        $data['escolas'] = form_dropdown('', ['' => 'selecione...'], '');
        $data['alunos'] = form_dropdown('', ['' => 'selecione...'], '');
        $data['dias_semana'] = form_dropdown('', ['' => 'Todos'], '');
        $data['periodos'] = form_dropdown('', ['' => 'Todos'], '');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function montar_escolas_restantes()
    {
        $os = $this->input->post('ordem_servico');

        $subquery = $this->db
            ->select('a.id')
            ->select(["CONCAT_WS(' - ', b.codigo, b.nome) AS nome"], false)
            ->select(["IF(CHAR_LENGTH(b.codigo) > 0, b.codigo, CAST(b.nome AS DECIMAL)) AS ordem"], false)
            ->join('ei_escolas b', 'b.id = a.id_escola')
            ->where('a.id_ordem_servico', $os)
            ->group_by('b.id')
            ->order_by('b.nome', 'ASC')
            ->get_compiled_select('ei_ordem_servico_escolas a');

        $escolasNaoAlocadas = $this->db
            ->from("({$subquery}) t")
            ->order_by('t.ordem', 'asc')
            ->get()
            ->result();

        $escolas = ['' => 'selecione...'] + array_column($escolasNaoAlocadas, 'nome', 'id');

        $data['escolas'] = form_dropdown('escola', $escolas, '', 'class="form-control"');
        $data['alunos'] = form_dropdown('', ['' => 'selecione...'], '');
        $data['dias_semana'] = form_dropdown('', ['' => 'Todos'], '');
        $data['periodos'] = form_dropdown('', ['' => 'Todos'], '');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function montar_alunos_restantes()
    {
        $escola = $this->input->post('escola');

        $alunosNaoAlocados = $this->db
            ->select('a.id, b.nome')
            ->join('ei_alunos b', 'b.id = a.id_aluno')
            ->where('a.id_ordem_servico_escola', $escola)
            ->get('ei_ordem_servico_alunos a')
            ->result();

        $alunos = ['' => 'selecione...'] + array_column($alunosNaoAlocados, 'nome', 'id');

        $data['alunos'] = form_dropdown('aluno', $alunos, '', 'class="form-control"');
        $data['dias_semana'] = form_dropdown('', ['' => 'Todos'], '');
        $data['periodos'] = form_dropdown('', ['' => 'Todos'], '');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function montar_dias_semana_restantes()
    {
        $idAluno = $this->input->post('aluno');

        $diasSemanaNaoAlocados = $this->db
            ->select('a.dia_semana, a.periodo')
            ->join('ei_ordem_servico_turmas b', 'b.id_os_horario = a.id')
            ->join('ei_ordem_servico_alunos c', 'c.id = b.id_os_aluno')
            ->where('c.id', $idAluno)
            ->order_by('a.dia_semana', 'asc')
            ->get('ei_ordem_servico_horarios a')
            ->result();

        $diasSemana = [
            '0' => 'Domingo',
            '1' => 'Segunda',
            '2' => 'Terça',
            '3' => 'Quarta',
            '4' => 'Quinta',
            '5' => 'Sexta',
            '6' => 'Sábado',
        ];

        $periodos = [
            '0' => 'Madrugada',
            '1' => 'Manhã',
            '2' => 'Tarde',
            '3' => 'Noite',
        ];

        $diasSemana = ['' => 'Todos'] + array_intersect_key($diasSemana, array_column($diasSemanaNaoAlocados, 'dia_semana', 'dia_semana'));
        $data['dias_semana'] = form_dropdown('dia_semana', $diasSemana, '', 'class="form-control"');

        $periodos = ['' => 'Todos'] + array_intersect_key($periodos, array_column($diasSemanaNaoAlocados, 'periodo', 'periodo'));
        $data['periodos'] = form_dropdown('periodo', $periodos, '', 'class="form-control"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function montar_periodos_restantes()
    {
        $idAluno = $this->input->post('aluno');
        $diaSemana = $this->input->post('dia_semana');

        $periodosNaoAlocados = $this->db
            ->select('a.periodo')
            ->join('ei_ordem_servico_turmas b', 'b.id_os_horario = a.id')
            ->join('ei_ordem_servico_alunos c', 'c.id = b.id_os_aluno')
            ->where('c.id', $idAluno)
            ->group_start()
            ->where('a.dia_semana', $diaSemana)
            ->or_where("CHAR_LENGTH('{$diaSemana}') =", 0)
            ->group_end()
            ->order_by('a.periodo', 'asc')
            ->get('ei_ordem_servico_horarios a')
            ->result();

        $periodos = [
            '0' => 'Madrugada',
            '1' => 'Manhã',
            '2' => 'Tarde',
            '3' => 'Noite',
        ];

        $periodos = ['' => 'Todos'] + array_intersect_key($periodos, array_column($periodosNaoAlocados, 'periodo', 'periodo'));

        $data['periodos'] = form_dropdown('periodo', $periodos, '', 'class="form-control"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function adicionar_os_individual()
    {
        // verifica se há ordem de serviço
        $ordemServico = $this->input->post('ordem_servico');
        if (empty($ordemServico)) {
            exit(json_encode(['erro' => 'Selecione uma Ordem de Serviço.']));
        }

        // Gera as variáveis locais necessárias
        $empresa = $this->session->userdata('empresa');
        $departamento = $this->input->post('depto');
        $idDiretoria = $this->input->post('diretoria');
        $idSupervisor = $this->input->post('supervisor');
        $ano = $this->input->post('ano_os');
        $anoReal = $this->input->post('ano');
        $mes = $this->input->post('mes_os');
        $mesReal = $this->input->post('mes');
        $semestre = $this->input->post('semestre_os');
        if (empty($semestre)) {
            $semestre = $mes > 7 ? '2' : '1';
        }
        $semestreReal = $this->input->post('semestre');
        if (empty($semestreReal)) {
            $semestreReal = $mesReal > 7 ? '2' : '1';
        }

        // Verifica se existe o mês alocado
        $alocacao = $this->db
            ->where('id_empresa', $empresa)
            ->where('depto', $departamento)
            ->where('id_diretoria', $idDiretoria)
            ->where('id_supervisor', $idSupervisor)
            ->where('ano', $anoReal)
            ->where('semestre', $semestreReal)
            ->get('ei_alocacao')
            ->row();

        if (empty($alocacao)) {
            exit(json_encode(['erro' => 'O semestre não foi iniciado.']));
        }

        // Inicia a transação
        $this->db->trans_begin();

        $idAlocacao = $alocacao->id;

        // gera as demais variáveis locais
        $osEscola = $this->input->post('escola');
        $osAluno = $this->input->post('aluno');
        $osDiaSemana = $this->input->post('dia_semana');
        $osPeriodo = $this->input->post('periodo');
        $semestreAnterior = $this->input->post('semestre_anterior');
        $ordemServico = $this->input->post('ordem_servico');

        // Recupera as escolas já alocadoas
        $qb = $this->db
            ->select('id, id_os_escola');
        if ($osEscola) {
            $qb->where('id_os_escola', $osEscola);
        }
        $alocacaoEscolasExistentes = $qb
            ->where('id_alocacao', $idAlocacao)
            ->get('ei_alocacao_escolas')
            ->result_array();

        // Seleciona as novas escolas
        $qb = $this->db
            ->select("'{$idAlocacao}' AS id_alocacao, a.id AS id_os_escola, b.id AS id_escola", false)
            ->select('b.codigo, b.nome AS escola, b.municipio, c.nome AS ordem_servico, d.contrato', false)
            ->join('ei_escolas b', 'b.id = a.id_escola')
            ->join('ei_ordem_servico c', 'c.id = a.id_ordem_servico')
            ->join('ei_contratos d', 'd.id = c.id_contrato')
            ->join('ei_diretorias e', 'e.id = d.id_cliente')
            ->join('ei_supervisores f', 'f.id_escola = b.id')
            ->join('ei_coordenacao g', 'g.id = f.id_coordenacao AND g.ano = c.ano AND g.semestre = c.semestre')
            ->join('ei_funcoes_supervisionadas h', 'h.id_supervisor = g.id')
            ->join('ei_ordem_servico_profissionais i', 'i.id_ordem_servico_escola = a.id')
            ->join('ei_ordem_servico_horarios j', 'j.id_os_profissional = i.id', 'left')
            ->join('ei_alocacao_escolas k', "k.id_alocacao = '{$idAlocacao}' AND k.id_os_escola = a.id", 'left')
            ->where('e.id_empresa', $empresa)
            ->where('e.depto', $departamento)
            ->where('e.id', $idDiretoria)
            ->where('g.id_usuario', $idSupervisor)
            ->where('c.ano', $ano)
            ->where('c.semestre', $semestre)
            ->where('(j.id_funcao = h.funcao OR j.id_funcao IS NULL)', null, false)
            ->where('c.id', $ordemServico)
            ->where('k.id', null);
        if ($osEscola) {
            $qb->where('a.id', $osEscola);
        }
        if (strlen($osDiaSemana) > 0) {
            $qb->where('j.dia_semana', $osDiaSemana);
        }
        if (strlen($osPeriodo) > 0) {
            $qb->where('j.periodo', $osPeriodo);
        }
        $alocacaoEscolas = $qb
            ->group_by(['a.id', 'b.nome'])
            ->order_by('b.nome', 'asc')
            ->get('ei_ordem_servico_escolas a')
            ->result_array();

        // Aloca as novas escolas
        if ($alocacaoEscolas) {
            $this->db->insert_batch('ei_alocacao_escolas', $alocacaoEscolas);
        }

        // mescla as escolas recém cadastradas à lista de escolas já alocadas
        $escolasExistentes = array_merge(array_column($alocacaoEscolasExistentes, 'id_os_escola'), array_column($alocacaoEscolas, 'id_os_escola'));

        // Verifica se a lista de escolas está vazia
        if (empty($escolasExistentes)) {
            $this->db->trans_rollback();
            exit(json_encode(['erro' => 'Nenhuma escola encontrada.']));
        }

        // recupera os cuidadores já alocados
        $alocadosExistentes = $this->db
            ->select('a.id, a.id_os_profissional')
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->where_in('b.id_os_escola', $escolasExistentes)
            ->get('ei_alocados a')
            ->result_array();

        // seleciona os novos cuidadores
        $qb = $this->db
            ->select('d.id AS id_alocacao_escola, a.id AS id_os_profissional, a.id_usuario AS id_cuidador, b.nome AS cuidador', false)
            ->select('a.valor_hora_operacional, a.horas_mensais_custo, a.data_inicio_contrato, a.data_termino_contrato', false)
            ->select(["ROUND((TIME_TO_SEC(a.horas_mensais_custo) / 3600) * a.valor_hora_operacional, 2) AS valor_total"], false)
            ->join('usuarios b', 'b.id = a.id_usuario')
            ->join('ei_ordem_servico_escolas c', 'c.id = a.id_ordem_servico_escola')
            ->join('ei_alocacao_escolas d', 'd.id_os_escola = c.id')
            ->join('ei_alocacao e', 'e.id = d.id_alocacao')
            ->join('ei_supervisores f', 'f.id_escola = d.id_escola')
            ->join('ei_coordenacao g', 'g.id = f.id_coordenacao AND g.id_usuario = e.id_supervisor AND g.ano = e.ano AND g.semestre = e.semestre')
            ->join('ei_funcoes_supervisionadas h', 'h.id_supervisor = g.id')
            ->join('ei_ordem_servico_horarios i', 'i.id_os_profissional = a.id', 'left')
            ->join('ei_alocados j', 'j.id_alocacao_escola = d.id AND j.id_os_profissional = a.id', 'left')
            ->join('ei_ordem_servico_turmas k', 'k.id_os_horario = i.id', 'left')
            ->join('ei_ordem_servico_alunos l', 'l.id = k.id_os_aluno AND l.id_ordem_servico_escola = c.id', 'left')
            ->where('d.id_alocacao', $idAlocacao)
            ->where_in('c.id', $escolasExistentes)
            ->where("(a.id_supervisor = {$idSupervisor} OR a.id_supervisor IS NULL)", null, false)
            ->where('(i.id_funcao = h.funcao OR i.id_funcao IS NULL)', null, false)
            ->where('j.id', null);
        if ($osAluno) {
            $qb->where('l.id', $osAluno);
        }
        if (strlen($osDiaSemana) > 0) {
            $qb->where('i.dia_semana', $osDiaSemana);
        }
        if (strlen($osPeriodo) > 0) {
            $qb->where('i.periodo', $osPeriodo);
        }
        $cuidadores = $qb
            ->group_by('a.id')
            ->get('ei_ordem_servico_profissionais a')
            ->result_array();

        // aloca os novos cuidadores
        if ($cuidadores) {
            $this->db->insert_batch('ei_alocados', $cuidadores);
        }

        // mescla os novos cuidadores á lista de cuidadores já alocados
        $cuidadoresExistentes = array_merge(array_column($alocadosExistentes, 'id_os_profissional'), array_column($cuidadores, 'id_os_profissional'));

        // verifica se a lista de cuidadores está vazia
        if (!$cuidadoresExistentes) {
            $this->db->trans_rollback();
            exit(json_encode(['erro' => 'Nenhum cuidador encontrado.']));
        }

        // Recupera os alunos já alocados
        $matriculadosExistentes = $this->db
            ->select('a.id, a.id_os_aluno')
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->where_in('b.id_os_escola', $escolasExistentes)
            ->get('ei_matriculados a')
            ->result_array();

        // seleciona os novos alunos
        $qb = $this->db
            ->select('d.id AS id_alocacao_escola, a.id AS id_os_aluno, a.id_aluno, b.nome AS aluno', false)
            ->select('b.status, b.hipotese_diagnostica, a.modulo, a.data_inicio, a.data_termino', false)
            ->select('a.id_aluno_curso, a2.id_curso, a3.nome AS curso', false)
            ->join('ei_alunos b', 'b.id = a.id_aluno')
            ->join('ei_ordem_servico_escolas c', 'c.id = a.id_ordem_servico_escola')
            ->join('ei_alunos_cursos a2', 'a2.id = a.id_aluno_curso AND a2.id_aluno = b.id')
            ->join('ei_cursos a3', 'a3.id = a2.id_curso')
            ->join('ei_alocacao_escolas d', 'd.id_os_escola = c.id')
            ->join('ei_alocacao e', 'e.id = d.id_alocacao')
            ->join('ei_supervisores f', 'f.id_escola = d.id_escola')
            ->join('ei_coordenacao g', 'g.id = f.id_coordenacao AND g.ano = e.ano AND g.semestre = e.semestre')
            ->join('ei_funcoes_supervisionadas h', 'h.id_supervisor = g.id')
            ->join('ei_ordem_servico_profissionais i', 'i.id_ordem_servico_escola = c.id')
            ->join('ei_ordem_servico_horarios j', 'j.id_os_profissional = i.id', 'left')
            ->join('ei_matriculados k', 'k.id_alocacao_escola = d.id AND k.id_os_aluno = a.id', 'left')
            ->where('d.id_alocacao', $idAlocacao);
        if ($osEscola) {
            $qb->where('c.id', $osEscola);
        } else {
            $qb->where_in('c.id', $escolasExistentes);
        }
        if ($semestreAnterior) {
            $qb->where('a.id IS NOT NULL');
        }
        if ($osAluno) {
            $qb->where('a.id', $osAluno);
        } elseif ($semestreAnterior == '0') {
            $qb->where('(j.id_funcao = h.funcao OR j.id_funcao IS NULL)', null, false)
                ->where('k.id', null);
        }
        if (strlen($osDiaSemana) > 0) {
            $qb->where('j.dia_semana', $osDiaSemana);
        }
        if (strlen($osPeriodo) > 0) {
            $qb->where('j.periodo', $osPeriodo);
        }
        $alunos = $qb
            ->group_by(['a.id', 'b.id'])
            ->get('ei_ordem_servico_alunos a')
            ->result_array();

        // aloca os novos alunos
        if ($alunos) {
            $this->db->insert_batch('ei_matriculados', $alunos);
        }

        // mescla os novos alunos à lista de alunos já alocados
        $alunosExistentes = array_merge(array_column($matriculadosExistentes, 'id_os_aluno'), array_column($alunos, 'id_os_aluno'));

        $mes1 = $semestre > 1 ? '07' : '01';
        $mes2 = $semestre > 1 ? '08' : '02';
        $mes3 = $semestre > 1 ? '09' : '03';
        $mes4 = $semestre > 1 ? '10' : '04';
        $mes5 = $semestre > 1 ? '11' : '05';
        $mes6 = $semestre > 1 ? '12' : '06';
        if ($semestre === '1') {
            $mes7 = '07';
        }

        $diaIniMes1 = date('Y-m-d', strtotime("{$ano}-{$mes1}-01"));
        $diaIniMes2 = date('Y-m-d', strtotime("{$ano}-{$mes2}-01"));
        $diaIniMes3 = date('Y-m-d', strtotime("{$ano}-{$mes3}-01"));
        $diaIniMes4 = date('Y-m-d', strtotime("{$ano}-{$mes4}-01"));
        $diaIniMes5 = date('Y-m-d', strtotime("{$ano}-{$mes5}-01"));
        $diaIniMes6 = date('Y-m-d', strtotime("{$ano}-{$mes6}-01"));
        if ($semestre === '1') {
            $diaIniMes7 = date('Y-m-d', strtotime("{$ano}-{$mes7}-01"));
        }

        $diaFimMes1 = date('Y-m-t', strtotime($diaIniMes1));
        $diaFimMes2 = date('Y-m-t', strtotime($diaIniMes2));
        $diaFimMes3 = date('Y-m-t', strtotime($diaIniMes3));
        $diaFimMes4 = date('Y-m-t', strtotime($diaIniMes4));
        $diaFimMes5 = date('Y-m-t', strtotime($diaIniMes5));
        $diaFimMes6 = date('Y-m-t', strtotime($diaIniMes6));
        if ($semestre === '1') {
            $diaFimMes7 = date('Y-m-t', strtotime($diaIniMes7));
        }

        $qb = $this->db
            ->select('c.id AS id_alocado, a.id AS id_os_horario')
            ->select('f.nome AS cargo, e.nome AS funcao')
            ->select('f.nome AS cargo_mes2, e.nome AS funcao_mes2')
            ->select('f.nome AS cargo_mes3, e.nome AS funcao_mes3')
            ->select('f.nome AS cargo_mes4, e.nome AS funcao_mes4')
            ->select('f.nome AS cargo_mes5, e.nome AS funcao_mes5')
            ->select('f.nome AS cargo_mes6, e.nome AS funcao_mes6');
        if ($semestre === '1') {
            $qb->select('f.nome AS cargo_mes7, e.nome AS funcao_mes7');
        }
        $qb->select('a.dia_semana, a.periodo')
            ->select("(CASE WHEN '{$semestreAnterior}' = 1 OR ({$mes1} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino))) THEN a.horario_inicio END) AS horario_inicio_mes1", false)
            ->select("(CASE WHEN '{$semestreAnterior}' = 1 OR ({$mes2} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino))) THEN a.horario_inicio END) AS horario_inicio_mes2", false)
            ->select("(CASE WHEN '{$semestreAnterior}' = 1 OR ({$mes3} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino))) THEN a.horario_inicio END) AS horario_inicio_mes3", false)
            ->select("(CASE WHEN '{$semestreAnterior}' = 1 OR ({$mes4} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino))) THEN a.horario_inicio END) AS horario_inicio_mes4", false)
            ->select("(CASE WHEN '{$semestreAnterior}' = 1 OR ({$mes5} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino))) THEN a.horario_inicio END) AS horario_inicio_mes5", false)
            ->select("(CASE WHEN '{$semestreAnterior}' = 1 OR ({$mes6} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino))) THEN a.horario_inicio END) AS horario_inicio_mes6", false);
        if ($semestre === '1') {
            $qb->select("(CASE WHEN '{$semestreAnterior}' = 1 OR ({$mes7} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino))) THEN a.horario_inicio END) AS horario_inicio_mes7", false);
        }
        $qb->select("(CASE WHEN '{$semestreAnterior}' = 1 OR ({$mes1} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino))) THEN a.horario_termino END) AS horario_termino_mes1", false)
            ->select("(CASE WHEN '{$semestreAnterior}' = 1 OR ({$mes2} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino))) THEN a.horario_termino END) AS horario_termino_mes2", false)
            ->select("(CASE WHEN '{$semestreAnterior}' = 1 OR ({$mes3} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino))) THEN a.horario_termino END) AS horario_termino_mes3", false)
            ->select("(CASE WHEN '{$semestreAnterior}' = 1 OR ({$mes4} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino))) THEN a.horario_termino END) AS horario_termino_mes4", false)
            ->select("(CASE WHEN '{$semestreAnterior}' = 1 OR ({$mes5} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino))) THEN a.horario_termino END) AS horario_termino_mes5", false)
            ->select("(CASE WHEN '{$semestreAnterior}' = 1 OR ({$mes6} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino))) THEN a.horario_termino END) AS horario_termino_mes6", false);
        if ($semestre === '1') {
            $qb->select("(CASE WHEN '{$semestreAnterior}' = 1 OR ({$mes7} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino))) THEN a.horario_termino END) AS horario_termino_mes7", false);
        }
        $qb->select("(CASE WHEN '{$semestreAnterior}' = 1 OR ({$mes1} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino))) THEN TIMEDIFF(a.horario_termino, a.horario_inicio) END) AS total_horas_mes1", false)
            ->select("(CASE WHEN '{$semestreAnterior}' = 1 OR ({$mes2} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino))) THEN TIMEDIFF(a.horario_termino, a.horario_inicio) END) AS total_horas_mes2", false)
            ->select("(CASE WHEN '{$semestreAnterior}' = 1 OR ({$mes3} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino))) THEN TIMEDIFF(a.horario_termino, a.horario_inicio) END) AS total_horas_mes3", false)
            ->select("(CASE WHEN '{$semestreAnterior}' = 1 OR ({$mes4} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino))) THEN TIMEDIFF(a.horario_termino, a.horario_inicio) END) AS total_horas_mes4", false)
            ->select("(CASE WHEN '{$semestreAnterior}' = 1 OR ({$mes5} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino))) THEN TIMEDIFF(a.horario_termino, a.horario_inicio) END) AS total_horas_mes5", false)
            ->select("(CASE WHEN '{$semestreAnterior}' = 1 OR ({$mes6} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino))) THEN TIMEDIFF(a.horario_termino, a.horario_inicio) END) AS total_horas_mes6", false);
        if ($semestre === '1') {
            $qb->select("(CASE WHEN '{$semestreAnterior}' = 1 OR ({$mes7} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino))) THEN TIMEDIFF(a.horario_termino, a.horario_inicio) END) AS total_horas_mes7", false);
        }
        $qb->select(['a.data_inicio_contrato, a.data_termino_contrato, a.valor_hora_operacional, a.horas_mensais_custo, l.valor AS valor_hora_funcao'], false)
            ->select(['IF(a.valor_hora_operacional > 0, a.valor_hora_operacional, l.valor_pagamento) AS valor_hora_operacional'], false)
            ->select(["IF('{$semestreAnterior}' = 1 OR ({$mes1} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino))), WEEK(DATE_SUB(IF(MONTH(MAX(h.data_termino)) = {$mes1}, MAX(h.data_termino), '{$diaFimMes1}'), INTERVAL ((7 + DATE_FORMAT(IF(MONTH(MAX(h.data_termino)) = {$mes1}, MAX(h.data_termino), '$diaFimMes1'), '%w') - a.dia_semana) % 7) DAY)) - WEEK(DATE_ADD(IF(MONTH(MIN(h.data_inicio)) = {$mes1}, MIN(h.data_inicio), '{$diaIniMes1}'), INTERVAL (((7 - DATE_FORMAT(IF(MONTH(MIN(h.data_inicio)) = {$mes1}, MIN(h.data_inicio), '{$diaIniMes1}'), '%w')) + a.dia_semana) % 7) DAY)) + 1, 0) AS total_semanas_mes1"], false)
            ->select(["IF('{$semestreAnterior}' = 1 OR ({$mes2} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino))), WEEK(DATE_SUB(IF(MONTH(MAX(h.data_termino)) = {$mes2}, MAX(h.data_termino), '{$diaFimMes2}'), INTERVAL ((7 + DATE_FORMAT(IF(MONTH(MAX(h.data_termino)) = {$mes2}, MAX(h.data_termino), '$diaFimMes2'), '%w') - a.dia_semana) % 7) DAY)) - WEEK(DATE_ADD(IF(MONTH(MIN(h.data_inicio)) = {$mes2}, MIN(h.data_inicio), '{$diaIniMes2}'), INTERVAL (((7 - DATE_FORMAT(IF(MONTH(MIN(h.data_inicio)) = {$mes2}, MIN(h.data_inicio), '{$diaIniMes2}'), '%w')) + a.dia_semana) % 7) DAY)) + 1, 0) AS total_semanas_mes2"], false)
            ->select(["IF('{$semestreAnterior}' = 1 OR ({$mes3} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino))), WEEK(DATE_SUB(IF(MONTH(MAX(h.data_termino)) = {$mes3}, MAX(h.data_termino), '{$diaFimMes3}'), INTERVAL ((7 + DATE_FORMAT(IF(MONTH(MAX(h.data_termino)) = {$mes3}, MAX(h.data_termino), '$diaFimMes3'), '%w') - a.dia_semana) % 7) DAY)) - WEEK(DATE_ADD(IF(MONTH(MIN(h.data_inicio)) = {$mes3}, MIN(h.data_inicio), '{$diaIniMes3}'), INTERVAL (((7 - DATE_FORMAT(IF(MONTH(MIN(h.data_inicio)) = {$mes3}, MIN(h.data_inicio), '{$diaIniMes3}'), '%w')) + a.dia_semana) % 7) DAY)) + 1, 0) AS total_semanas_mes3"], false)
            ->select(["IF('{$semestreAnterior}' = 1 OR ({$mes4} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino))), WEEK(DATE_SUB(IF(MONTH(MAX(h.data_termino)) = {$mes4}, MAX(h.data_termino), '{$diaFimMes4}'), INTERVAL ((7 + DATE_FORMAT(IF(MONTH(MAX(h.data_termino)) = {$mes4}, MAX(h.data_termino), '$diaFimMes4'), '%w') - a.dia_semana) % 7) DAY)) - WEEK(DATE_ADD(IF(MONTH(MIN(h.data_inicio)) = {$mes4}, MIN(h.data_inicio), '{$diaIniMes4}'), INTERVAL (((7 - DATE_FORMAT(IF(MONTH(MIN(h.data_inicio)) = {$mes4}, MIN(h.data_inicio), '{$diaIniMes4}'), '%w')) + a.dia_semana) % 7) DAY)) + 1, 0) AS total_semanas_mes4"], false)
            ->select(["IF('{$semestreAnterior}' = 1 OR ({$mes5} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino))), WEEK(DATE_SUB(IF(MONTH(MAX(h.data_termino)) = {$mes5}, MAX(h.data_termino), '{$diaFimMes5}'), INTERVAL ((7 + DATE_FORMAT(IF(MONTH(MAX(h.data_termino)) = {$mes5}, MAX(h.data_termino), '$diaFimMes5'), '%w') - a.dia_semana) % 7) DAY)) - WEEK(DATE_ADD(IF(MONTH(MIN(h.data_inicio)) = {$mes5}, MIN(h.data_inicio), '{$diaIniMes5}'), INTERVAL (((7 - DATE_FORMAT(IF(MONTH(MIN(h.data_inicio)) = {$mes5}, MIN(h.data_inicio), '{$diaIniMes5}'), '%w')) + a.dia_semana) % 7) DAY)) + 1, 0) AS total_semanas_mes5"], false)
            ->select(["IF('{$semestreAnterior}' = 1 OR ({$mes6} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino))), WEEK(DATE_SUB(IF(MONTH(MAX(h.data_termino)) = {$mes6}, MAX(h.data_termino), '{$diaFimMes6}'), INTERVAL ((7 + DATE_FORMAT(IF(MONTH(MAX(h.data_termino)) = {$mes6}, MAX(h.data_termino), '$diaFimMes6'), '%w') - a.dia_semana) % 7) DAY)) - WEEK(DATE_ADD(IF(MONTH(MIN(h.data_inicio)) = {$mes6}, MIN(h.data_inicio), '{$diaIniMes6}'), INTERVAL (((7 - DATE_FORMAT(IF(MONTH(MIN(h.data_inicio)) = {$mes6}, MIN(h.data_inicio), '{$diaIniMes6}'), '%w')) + a.dia_semana) % 7) DAY)) + 1, 0) AS total_semanas_mes6"], false);
        if ($semestre === '1') {
            $qb->select(["IF('{$semestreAnterior}' = 1 OR ({$mes7} BETWEEN MONTH(MIN(h.data_inicio)) AND MONTH(MAX(h.data_termino))), WEEK(DATE_SUB(IF(MONTH(MAX(h.data_termino)) = {$mes7}, MAX(h.data_termino), '{$diaFimMes7}'), INTERVAL ((7 + DATE_FORMAT(IF(MONTH(MAX(h.data_termino)) = {$mes7}, MAX(h.data_termino), '$diaFimMes7'), '%w') - a.dia_semana) % 7) DAY)) - WEEK(DATE_ADD(IF(MONTH(MIN(h.data_inicio)) = {$mes7}, MIN(h.data_inicio), '{$diaIniMes7}'), INTERVAL (((7 - DATE_FORMAT(IF(MONTH(MIN(h.data_inicio)) = {$mes7}, MIN(h.data_inicio), '{$diaIniMes7}'), '%w')) + a.dia_semana) % 7) DAY)) + 1, 0) AS total_semanas_mes7"], false);
        }
        $qb->join('ei_ordem_servico_profissionais b', 'b.id = a.id_os_profissional')
            ->join('ei_ordem_servico_escolas i', 'i.id = b.id_ordem_servico_escola')
            ->join('ei_ordem_servico j', 'j.id = i.id_ordem_servico')
            ->join('ei_contratos k', 'k.id = j.id_contrato')
            ->join('ei_alocados c', 'c.id_os_profissional = b.id')
            ->join('ei_alocacao_escolas d', 'd.id = c.id_alocacao_escola')
            ->join('ei_alocacao d2', 'd2.id = d.id_alocacao')
            ->join('ei_supervisores m', 'm.id_escola = d.id_escola')
            ->join('ei_coordenacao n', 'n.id = m.id_coordenacao AND n.id_usuario = d2.id_supervisor AND n.ano = d2.ano AND n.semestre = d2.semestre')
            ->join('ei_funcoes_supervisionadas o', 'o.id_supervisor = n.id', 'left')
            ->join('empresa_funcoes e', 'e.id = a.id_funcao', 'left')
            ->join('empresa_cargos f', 'f.id = e.id_cargo', 'left')
            ->join('ei_ordem_servico_turmas g', 'g.id_os_horario = a.id', 'left')
            ->join('ei_ordem_servico_alunos h', 'h.id = g.id_os_aluno AND h.id_ordem_servico_escola = i.id', 'left')
            ->join('ei_valores_faturamento l', 'l.id_contrato = k.id AND l.ano = j.ano AND l.semestre = j.semestre AND l.id_cargo = f.id AND l.id_funcao = e.id', 'left')
            ->join('ei_alocados_horarios m2', 'm2.id_alocado = c.id AND m2.id_os_horario = a.id', 'left')
            ->where('d.id_alocacao', $idAlocacao);
        if (strlen($osDiaSemana) > 0) {
            $qb->where('a.dia_semana', $osDiaSemana);
        }
        if (strlen($osPeriodo) > 0) {
            $qb->where('a.periodo', $osPeriodo);
        }
        $horarios = $qb
            ->where_in('d.id_os_escola', $escolasExistentes)
            ->where_in('b.id', $cuidadoresExistentes)
            ->where('(o.funcao = a.id_funcao OR a.id_funcao IS NULL)', null, false)
            ->where('m2.id', null)
            ->group_by('a.id')
            ->get('ei_ordem_servico_horarios a')
            ->result_array();

//        exit(json_encode(['erro' => $horarios]));

        if ($horarios) {
            $this->db->insert_batch('ei_alocados_horarios', $horarios);
        }

        $horariosExistentes = $this->db
            ->select('a.id_os_horario')
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->where_in('c.id_os_escola', $escolasExistentes)
            ->get('ei_alocados_horarios a')
            ->result_array();

        $qb = $this->db
            ->select('d.id AS id_matriculado, e.id AS id_alocado_horario')
            ->join('ei_ordem_servico_alunos b', 'b.id = a.id_os_aluno')
            ->join('ei_ordem_servico_horarios c', 'c.id = a.id_os_horario')
            ->join('ei_matriculados d', 'd.id_os_aluno = b.id')
            ->join('ei_alocados_horarios e', 'e.id_os_horario = c.id')
            ->join('ei_alocados f', 'f.id = e.id_alocado')
            ->join('ei_alocacao_escolas g', 'g.id = f.id_alocacao_escola')
            ->join('ei_matriculados_turmas h', 'h.id_matriculado = d.id AND h.id_alocado_horario = e.id', 'left')
            ->where('g.id_alocacao', $idAlocacao)
            ->where_in('g.id_os_escola', $escolasExistentes)
            ->where_in('f.id_os_profissional', $cuidadoresExistentes)
            ->where_in('d.id_os_aluno', $alunosExistentes);
        if (strlen($osDiaSemana) > 0) {
            $qb->where('c.dia_semana', $osDiaSemana);
        }
        if (strlen($osPeriodo) > 0) {
            $qb->where('c.periodo', $osPeriodo);
        }
        $turmas = $qb->where_in('e.id_os_horario', array_column($horariosExistentes, 'id_os_horario'))
            ->where('(h.id_matriculado IS NULL OR h.id_alocado_horario IS NULL)')
            ->get('ei_ordem_servico_turmas a')
            ->result_array();

        if ($turmas) {
            $this->db->insert_batch('ei_matriculados_turmas', $turmas);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            exit(json_encode(['erro' => 'Erro ao iniciar semestre.']));
        }

        $this->db->trans_commit();

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    public function preparar_exclusao_semestre()
    {
        $data = $this->input->post();
        if (empty($data['semestre'])) {
            $data['semestre'] = intval($data['mes']) > 7 ? '2' : '1';
        }

        unset($data['mes']);

        $sql = "SELECT a.id,
                       a.nome,
                       d.id AS id_escola,
                       d.nome AS escola,
                       m.id AS id_aluno,
                       m.nome AS aluno
                FROM ei_ordem_servico a
                INNER JOIN ei_contratos b 
                           ON b.id = a.id_contrato
                INNER JOIN ei_diretorias c
                           ON c.id = b.id_cliente
                INNER JOIN ei_escolas d 
                           ON d.id_diretoria = c.id
                INNER JOIN ei_supervisores e 
                           ON e.id_escola = d.id
                INNER JOIN ei_coordenacao f 
                           ON f.id = e.id_coordenacao
                INNER JOIN ei_ordem_servico_escolas g 
                           ON g.id_ordem_servico = a.id
                LEFT JOIN ei_ordem_servico_profissionais h 
                           ON h.id_ordem_servico_escola = g.id
                LEFT JOIN ei_funcoes_supervisionadas i
                           ON i.id_supervisor = f.id 
                           AND i.cargo = h.id_cargo 
                           AND i.funcao = h.id_funcao                          
                LEFT JOIN ei_ordem_servico_horarios j
                           ON j.id_os_profissional = h.id                          
                LEFT JOIN ei_ordem_servico_alunos k
                           ON k.id_ordem_servico_escola = g.id
                LEFT JOIN ei_ordem_servico_turmas l
                           ON l.id_os_horario = j.id 
                           AND l.id_os_aluno = k.id
                INNER JOIN ei_alunos m ON m.id = k.id_aluno
                WHERE c.id_empresa = {$this->session->userdata('empresa')}
                      AND c.depto = '{$data['depto']}'
                      AND c.id = {$data['diretoria']}
                      AND f.id_usuario = {$data['supervisor']}
                      AND a.ano = {$data['ano']}
                      AND a.semestre = {$data['semestre']}
                GROUP BY a.id, d.id, m.id
                ORDER BY a.nome ASC";
        $ordemServico = $this->db->query($sql)->result();

        $options = ['' => 'selecione...'] + array_column($ordemServico, 'nome', 'id');
        $escolas = array_column($ordemServico, 'escola', 'id_escola');
        $alunos = array_column($ordemServico, 'aluno', 'id_aluno');
        asort($escolas);
        asort($alunos);

        $data['ordem_servico'] = form_dropdown('ordem_servico', $options, '', 'class="form-control"');
        $data['escola'] = form_dropdown('escola', ['' => 'selecione...'] + $escolas, '', 'class="form-control"');
        $data['aluno'] = form_dropdown('aluno', ['' => 'selecione...'] + $alunos, '', 'class="form-control"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function limpar_semestre()
    {
        $data = $this->input->post();

        $rows = $this->db
            ->select('id')
            ->where('id_empresa', $this->session->userdata('empresa'))
            ->where('depto', $data['depto'])
            ->where('id_diretoria', $data['diretoria'])
            ->where('id_supervisor', $data['supervisor'])
            ->where('ano', $data['ano'])
            ->where('semestre', $data['semestre'])
            ->get('ei_alocacao')
            ->result();

        if (!$rows) {
            exit(json_encode(['erro' => 'Este semestre já está vazio.']));
        }

        if ($data['possui_mapa_visitacao'] === '2') {
            $status = $this->db
                ->where_in('id_alocacao', array_column($rows, 'id'))
                ->delete('ei_mapa_unidades');
        } elseif ($data['possui_mapa_visitacao'] === '1') {
            $status = $this->db
                ->where_in('id', array_column($rows, 'id'))
                ->delete('ei_alocacao');
        } else {
            $status = $this->db
                ->where_in('id_alocacao', array_column($rows, 'id'))
                ->delete(['ei_alocacao_escolas', 'ei_faturamento', 'ei_faturamento_consolidado', 'ei_pagamento_prestador']);
        }

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function editar_opcoes_mes()
    {
        $data = $this->input->post();
        $idMes = $this->getIdMes($data['mes'], $data['semestre']);

        $alocacao = $this->db
            ->select('id, congelar_mes' . $idMes . ' AS congelar_mes')
            ->select('pagamento_fracionado_mes' . $idMes . ' AS pagamento_fracionado')
            ->select('medicao_liberada_mes' . $idMes . ' AS medicao_liberada')
            ->select('dia_fechamento_mes' . $idMes . ' AS dia_fechamento')
            ->where('depto', $data['depto'])
            ->where('id_diretoria', $data['diretoria'])
            ->where('id_supervisor', $data['supervisor'])
            ->where('ano', $data['ano'])
            ->where('semestre', $data['semestre'])
            ->get('ei_alocacao')
            ->row();

        if (empty($alocacao)) {
            exit(json_encode(['erro' => 'Mês não alocado']));
        }

        $alocacao->mes = $data['mes'];

        echo json_encode($alocacao);
    }

    //--------------------------------------------------------------------

    public function salvar_opcoes_mes()
    {
        $data = $this->input->post();
        $congelarMes = !empty($data['congelar_mes']) ? 1 : null;
        $pagamentoFracionado = !empty($data['pagamento_fracionado']) ? 1 : null;
        $medicaoLiberada = !empty($data['medicao_liberada']) ? 1 : null;
        $diaFechamento = $data['dia_fechamento'] ?? null;

        $alocacao = $this->db
            ->where('id', $data['id'])
            ->get('ei_alocacao')
            ->row_array();

        if (empty($alocacao)) {
            exit(json_encode(['erro' => 'Mês alocado não encontrado.']));
        }

        $idMes = $this->getIdMes($data['mes'], $alocacao['semestre']);

        $this->db->trans_start();

        $this->db
            ->set('congelar_mes' . $idMes, $congelarMes)
            ->set('pagamento_fracionado_mes' . $idMes, $pagamentoFracionado)
            ->set('medicao_liberada_mes' . $idMes, $medicaoLiberada)
            ->set('dia_fechamento_mes' . $idMes, $diaFechamento)
            ->where('id', $data['id'])
            ->update('ei_alocacao');

        $this->db->trans_complete();

        if ($this->db->trans_status() == false) {
            exit(json_encode(['erro' => 'Não foi possível salvar as opções do mês.']));
        }

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    private function getIdMes(?string $mes, ?int $semestre): int
    {
        $semestre = intval($mes) > 7 ? 2 : (intval($mes) < 7 ? 1 : $semestre);
        return $mes - ($semestre > 1 ? 6 : 0);
    }

    //--------------------------------------------------------------------

    public function ajax_edit_total_semanas_mes()
    {
        $idAlocado = $this->input->post('id_alocado');
        $periodo = $this->input->post('periodo');
        $cargo = $this->input->post('cargo');
        $funcao = $this->input->post('funcao');

        $idMes = $this->getIdMes($this->input->post('mes'), $this->input->post('semestre'));
        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';

        $data = $this->db
            ->select("DATE_FORMAT(IFNULL(data_inicio_real, MIN(a.data_inicio)), '%d/%m/%Y') AS data_inicio_real", false)
            ->select("IFNULL(DATE_FORMAT(data_termino_real, '%d/%m/%Y'), '00/00/0000') AS data_termino_real", false)
            ->join('ei_matriculados_turmas b', 'b.id_matriculado = a.id')
            ->join('ei_alocados_horarios c', 'c.id = b.id_alocado_horario')
            ->join('ei_alocados d', 'd.id = c.id_alocado AND d.id_alocacao_escola = a.id_alocacao_escola')
            ->where('d.id', $idAlocado)
            ->where('c.periodo', $periodo)
            ->group_start()
            ->where("(c.cargo{$mesCargoFuncao} = '{$cargo}' AND c.funcao{$mesCargoFuncao} = '{$funcao}')")
            ->or_where("(c.cargo_sub1 = '{$cargo}' AND c.funcao_sub1 = '{$funcao}')")
            ->or_where("(c.cargo_sub2 = '{$cargo}' AND c.funcao_sub2 = '{$funcao}')")
            ->group_end()
            ->group_by('d.id')
            ->get('ei_matriculados a')
            ->row();

        if (empty($data)) {
            exit(json_encode(['erro' => 'Nenhum aluno alocado.']));
        }

        echo json_encode($data);
    }

}
