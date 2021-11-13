<?php

namespace App\Controllers\Ei\Ordem_servico;

use App\Controllers\BaseController;

class Profissionais extends BaseController
{

    public function index()
    {
        $this->gerenciar();
    }

    //--------------------------------------------------------------------

    public function gerenciar(string $idEscola = null)
    {
        if (empty($idEscola)) {
            $idEscola = $this->uri->rsegment(3, 0);
        }

        $data = $this->db
            ->select('a2.nome AS ordemServico, e.id AS id_depto, a2.ano, a2.semestre', false)
            ->select('b.nome AS nomeEscola', false)
            ->select('c.nome AS nomeCliente', false)
            ->select('d.contrato AS nomeContrato', false)
            ->select("CONCAT(a2.ano, '/', a2.semestre) AS anoSemestre", false)
            ->join('ei_ordem_servico a2', 'a.id_ordem_servico = a2.id')
            ->join('ei_escolas b', 'b.id = a.id_escola')
            ->join('ei_diretorias c', 'c.id = b.id_diretoria')
            ->join('ei_contratos d', 'd.id_cliente = c.id')
            ->join('empresa_departamentos e', 'e.nome = c.depto', 'left')
            ->where('a.id', $idEscola)
            ->get('ei_ordem_servico_escolas a')
            ->row();

        if ($data->semestre == 2) {
            $data->nomeMes1 = 'Julho';
            $data->nomeMes2 = 'Agosto';
            $data->nomeMes3 = 'Setembro';
            $data->nomeMes4 = 'Outubro';
            $data->nomeMes5 = 'Novembro';
            $data->nomeMes6 = 'Dezembro';
        } else {
            $data->nomeMes1 = 'Janeiro';
            $data->nomeMes2 = 'Fevereiro';
            $data->nomeMes3 = 'Março';
            $data->nomeMes4 = 'Abril';
            $data->nomeMes5 = 'Maio';
            $data->nomeMes6 = 'Junho';
        }

        $funcoes = $this->getFuncoes();
        $funcoes[''] = 'selecione...';
        $data->funcoes = $funcoes;

        $supervisores = $this->db
            ->select('b.id, b.nome')
            ->join('usuarios b', 'b.id = a.id_usuario')
            ->where('b.empresa', $this->session->userdata('empresa'))
            ->where('a.ano', $data->ano)
            ->where('a.semestre', $data->semestre)
            ->where('a.is_supervisor', 1)
            ->order_by('b.nome', 'asc')
            ->get('ei_coordenacao a')
            ->result();

        $data->supervisor = ['' => 'nenhum...', '-1' => '-- manter --'] + array_column($supervisores, 'nome', 'id');

        $this->load->view('ei/ordem_servico_profissionais', $data);
    }

    //--------------------------------------------------------------------

    public function ajax_list()
    {
        $idEscola = $this->input->post('id_escola');

        $sql = "SELECT a.dia_semana,
                       c.nome AS profissional,
                       GROUP_CONCAT(DISTINCT i.nome ORDER BY i.nome SEPARATOR ', ') AS alunos,
                       a2.nome AS funcao,
                       b.valor_hora,
                       b.horas_semanais,
                       CONCAT(TIME_FORMAT(a.horario_inicio,'%H:%i'), ' às ', TIME_FORMAT(a.horario_termino,'%H:%i')) AS horario,
                       a.id,
                       b.id AS id_os_profissional,
                       CASE a.dia_semana
                            WHEN 0 THEN 'Domingo'
                            WHEN 1 THEN 'Segunda-feira'
                            WHEN 2 THEN 'Terça-feira'
                            WHEN 3 THEN 'Quarta-feira'
                            WHEN 4 THEN 'Quinta-feira'
                            WHEN 5 THEN 'Sexta-feira'
                            WHEN 6 THEN 'Sábado'
                            END AS semana,
                       e.nome AS profissional_sub1,
                       f.nome AS profissional_sub2,
                       FORMAT(b.valor_hora, 2, 'de_DE') AS valor_hora_de,
                       FORMAT(b.horas_semanais, 2, 'de_DE') AS horas_semanais_de,
                       c.status
                FROM ei_ordem_servico_profissionais b
                INNER JOIN usuarios c ON 
                           c.id = b.id_usuario
                INNER JOIN ei_ordem_servico_escolas d ON 
                           d.id = b.id_ordem_servico_escola
                LEFT JOIN ei_ordem_servico_horarios a ON 
                          a.id_os_profissional = b.id
                LEFT JOIN empresa_funcoes a2 ON 
                          a2.id = a.id_funcao
                LEFT JOIN usuarios e ON 
                          e.id = b.id_usuario_sub1
                LEFT JOIN usuarios f ON 
                          f.id = b.id_usuario_sub2
                LEFT JOIN ei_ordem_servico_turmas g ON g.id_os_horario = a.id
                LEFT JOIN ei_ordem_servico_alunos h ON h.id = g.id_os_aluno AND h.id_ordem_servico_escola = d.id
                LEFT JOIN ei_alunos i ON i.id = h.id_aluno
                WHERE c.empresa = {$this->session->userdata('empresa')} 
                      AND d.id = {$idEscola} 
                GROUP BY a.id";

        $this->load->library('dataTables');

        $output = $this->datatables->query($sql);

        $statusAtivo = [USUARIO_ATIVO => 1, USUARIO_EM_EXPERIENCIA => 1];

        $data = [];
        foreach ($output->data as $ei) {
            $row = [];
            $row[] = $ei->semana;
            $row[] = '<a>' . $ei->profissional . '</a>';
            $row[] = $ei->alunos;
            $row[] = $ei->funcao;
            $row[] = $ei->valor_hora_de;
            $row[] = $ei->horas_semanais_de;
            $row[] = $ei->horario;
            if ($ei->id) {
                $row[] = '
                      <button type="button" class="btn btn-sm btn-info" onclick="edit_profissional(' . $ei->id . ')" title="Editar programação semanal"><i class="glyphicon glyphicon-pencil"></i> </button>
                      <button type="button" class="btn btn-sm btn-danger" onclick="delete_profissional(' . $ei->id . ')" title="Excluir programação semanal"><i class="glyphicon glyphicon-trash"></i> </button>
                     ';
            } else {
                $row[] = '
                      <button type="button" class="btn btn-sm btn-info" onclick="add_profissional(' . $ei->id_os_profissional . ')" title="Editar profissional"><i class="glyphicon glyphicon-plus"></i> </button>
                      <button type="button" class="btn btn-sm btn-danger" onclick="limpar_profissional(' . $ei->id_os_profissional . ')" title="Excluir profissional"><i class="glyphicon glyphicon-minus"></i> </button>
                     ';
            }
            $row[] = $ei->id_os_profissional;
            $row[] = $ei->id;
            $row[] = $statusAtivo[$ei->status] ?? null;

            $data[] = $row;
        }

        $output->data = $data;

        echo json_encode($output);
    }

    //--------------------------------------------------------------------

    public function ajax_edit()
    {
        $idEscola = $this->input->post('id_escola');

        $data = $this->db
            ->select('id AS id_ordem_servico_escola')
            ->get_where('ei_ordem_servico_escolas', ['id' => $idEscola])
            ->row_array();

        $deptos = $this->getDepartamentos();
        $areas = $this->getAreas();
        $setores = $this->getSetores();
        $cargos = $this->getCargos();
        $funcoes = $this->getFuncoes();
        $municipios = $this->getMunicipios();
        $usuarios = $this->getUsuarios();

        $rows = $this->db
            ->select('id_usuario')
            ->where('id_ordem_servico_escola', $idEscola)
            ->get('ei_ordem_servico_profissionais')
            ->result();

        $usuariosSelecionados = array_column($rows, 'id_usuario');

        $supervisores = $this->db
            ->where('id_ordem_servico_escola', $data['id_ordem_servico_escola'])
            ->get('ei_ordem_servico_profissionais')
            ->result_array();

        if (count($supervisores) === 1) {
            $data['supervisores'] = $supervisores[0];
        } else {
            $data['supervisores'] = $supervisores ? '-1' : '';
        }

        $data['depto'] = form_dropdown('id_departamento', $deptos, '', 'id="depto" class="form-control filtro"');
        $data['area'] = form_dropdown('id_area', $areas, '', 'id="area" class="form-control"');
        $data['setor'] = form_dropdown('id_setor', $setores, '', 'id="setor" class="form-control"');
        $data['cargo'] = form_dropdown('id_cargo', $cargos, '', 'id="cargo" class="form-control"');
        $data['funcao'] = form_dropdown('id_funcao', $funcoes, '', 'id="funcao" class="form-control"');
        $data['municipio'] = form_dropdown('municipio', $municipios, '', 'id="municipio" class="form-control filtro"');
        $data['id_usuarios'] = form_multiselect('id_usuario[]', $usuarios, $usuariosSelecionados, 'id="id_usuarios" class="demo1" size="8"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function atualizar_filtros()
    {
        parse_str($this->input->post('busca'), $busca);
        $buscaIdUsuarios = $this->input->post('id_usuarios');

        $areas = $this->getAreas($busca);
        $setores = $this->getSetores($busca);
        $cargos = $this->getCargos($busca);
        $funcoes = $this->getFuncoes($busca);
        $municipios = $this->getMunicipios($busca);
        $idUsuarios = $this->getUsuarios($busca);

        $data['area'] = form_dropdown('id_area', $areas, $busca['id_area'], 'id="area" class="form-control"');
        $data['setor'] = form_dropdown('id_setor', $setores, $busca['id_setor'], 'id="setor" class="form-control"');
        $data['cargo'] = form_dropdown('id_cargo', $cargos, $busca['id_cargo'], 'id="cargo" class="form-control"');
        $data['funcao'] = form_dropdown('id_funcao', $funcoes, $busca['id_funcao'], 'id="funcao" class="form-control"');
        $data['municipio'] = form_dropdown('municipio', $municipios, $busca['municipio'], 'id="municipio" class="form-control filtro"');
        $data['id_usuarios'] = form_multiselect('id_usuario[]', $idUsuarios, $buscaIdUsuarios, 'id="id_usuarios" class="demo1" size="8"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_edit_horario()
    {
        $id = $this->input->post('id');
        $idEscola = $this->input->post('id_escola');
        $idProfissional = $this->input->post('id_profissional');

        $qb = $this->db
            ->select('c.id, c.dia_semana, b.id AS id_os_profissional, c.id_funcao')
            ->select("TIME_FORMAT(c.horario_inicio, '%H:%i') AS horario_inicio", false)
            ->select("TIME_FORMAT(c.horario_termino, '%H:%i') AS horario_termino", false)
            ->join('ei_ordem_servico_profissionais b', 'b.id_ordem_servico_escola = a.id', 'left');
        if ($id) {
            $qb->join('ei_ordem_servico_horarios c', 'c.id_os_profissional = b.id', 'left')
                ->where('a.id', $idEscola)
                ->where('c.id', $id);
        } else {
            $qb->join('ei_ordem_servico_horarios c', "c.id_os_profissional = b.id AND c.id IS NULL", 'left')
                ->where('a.id', $idEscola)
                ->where('b.id', $idProfissional)
                ->group_by('a.id');
        }
        $data = $qb
            ->get('ei_ordem_servico_escolas a')
            ->row_array();

        $profissionais = $this->db
            ->select('a.id, b.nome')
            ->join('usuarios b', 'b.id = a.id_usuario')
            ->where('b.empresa', $this->session->userdata('empresa'))
            ->where('a.id_ordem_servico_escola', $idEscola)
            ->order_by('b.nome', 'asc')
            ->get('ei_ordem_servico_profissionais a')
            ->result();

        $profissionais = ['' => 'selecione...'] + array_column($profissionais, 'nome', 'id');

        $idOSProfissional = $data['id_os_profissional'] ?? '';
        $data['id_os_profissional'] = form_dropdown('id_os_profissional', $profissionais, $idOSProfissional, 'class="form-control"');

        $funcoes = $this->getFuncoes();
        $funcoes[''] = 'selecione...';
        $idFuncao = $data['id_funcao'] ?? '';
        $data['id_funcao'] = form_dropdown('id_funcao', $funcoes, $idFuncao, 'class="form-control"');

        $arrAlunos = $this->db
            ->select('a.id, b.nome, f.id_os_aluno')
            ->join('ei_alunos b', 'b.id = a.id_aluno')
            ->join('ei_ordem_servico_escolas c', 'c.id = a.id_ordem_servico_escola')
            ->join('ei_ordem_servico_profissionais d', 'd.id_ordem_servico_escola = c.id', 'left')
            ->join('ei_ordem_servico_horarios e', "e.id_os_profissional = d.id AND e.id = '{$id}'", 'left')
            ->join('ei_ordem_servico_turmas f', 'f.id_os_aluno = a.id AND f.id_os_horario = e.id', 'left')
            ->where('c.id', $idEscola)
            ->get('ei_ordem_servico_alunos a')
            ->result();

        $alunos = array_column($arrAlunos, 'nome', 'id');
        $alunosSelecionados = array_column($arrAlunos, 'id_os_aluno');

        $data['alunos'] = form_multiselect('alunos[]', $alunos, $alunosSelecionados, 'id="alunos" class="demo2" size="8"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_edit_dados()
    {
        $id = $this->input->post('id');
        $idOSProfissional = $this->input->post('id_os_profissional');

        $data = $this->db
            ->select('a.*, b.faturamento_semestral_projetado, e.ano, e.semestre', false)
            ->select('b.id_usuario, b.id_supervisor, b.valor_hora')
            ->select('b.id_funcao, b.id_funcao_2m, b.id_funcao_3m')
            ->select('b.id_funcao_1t, b.id_funcao_2t, b.id_funcao_3t')
            ->select('b.id_funcao_1n, b.id_funcao_2n, b.id_funcao_3n')
            ->select('b.valor_hora_operacional, b.valor_hora_operacional_2, b.valor_hora_operacional_3')
            ->select('b.valor_hora_operacional_1t, b.valor_hora_operacional_2t, b.valor_hora_operacional_3t')
            ->select('b.valor_hora_operacional_1n, b.valor_hora_operacional_2n, b.valor_hora_operacional_3n')
            ->select('b.horas_mensais_custo, b.horas_mensais_custo_2, b.horas_mensais_custo_3')
            ->select('b.horas_mensais_custo_1t, b.horas_mensais_custo_2t, b.horas_mensais_custo_3t')
            ->select('b.horas_mensais_custo_1n, b.horas_mensais_custo_2n, b.horas_mensais_custo_3n')
            ->select('b.id_ordem_servico_escola, c.nome AS nome_usuario, b.pagamento_inicio, b.pagamento_reajuste', false)
            ->select("(CASE a.dia_semana WHEN 0 THEN 'Domingo' WHEN 1 THEN 'Segunda-feira' WHEN 2 THEN 'Terça-feira' WHEN 3 THEN 'Quarta-feira' WHEN 4 THEN 'Quinta-feira' WHEN 5 THEN 'Sexta-feira' WHEN 6 THEN 'Sábado' END) AS nome_semana", false)
            ->select("(CASE a.periodo WHEN 0 THEN 'Madrugada' WHEN 1 THEN 'Manhã' WHEN 2 THEN 'Tarde' WHEN 3 THEN 'Noite' END) AS nome_periodo", false)
            ->join('ei_ordem_servico_profissionais b', 'b.id = a.id_os_profissional')
            ->join('usuarios c', 'c.id = b.id_usuario')
            ->join('ei_ordem_servico_escolas d', 'd.id = b.id_ordem_servico_escola')
            ->join('ei_ordem_servico e', 'e.id = d.id_ordem_servico')
            ->where('a.id', $id)
            ->where('b.id', $idOSProfissional)
            ->get('ei_ordem_servico_horarios a')
            ->row();

        if (empty($data)) {
            $data = $this->db
                ->select(["a.*, d.ano, d.semestre, b.nome AS nome_usuario, 'Integral' AS nome_periodo, 'Sem cadastro' AS nome_semana"], false)
                ->join('usuarios b', 'b.id = a.id_usuario')
                ->join('ei_ordem_servico_escolas c', 'c.id = a.id_ordem_servico_escola')
                ->join('ei_ordem_servico d', 'd.id = c.id_ordem_servico')
                ->where('a.id', $idOSProfissional)
                ->get('ei_ordem_servico_profissionais a')
                ->row();
        }

        if ($data) {
            if ($data->valor_hora) {
                $data->valor_hora = number_format($data->valor_hora, 2, ',', '.');
            }
            if ($data->qtde_dias) {
                $data->qtde_dias = number_format($data->qtde_dias, 2, ',', '');
            }
            if ($data->faturamento_semestral_projetado) {
                $data->faturamento_semestral_projetado = number_format($data->faturamento_semestral_projetado, 2, ',', '');
            }
            if ($data->horas_diarias) {
                $data->horas_diarias = number_format($data->horas_diarias, 2, ',', '');
            }
            if ($data->horas_semanais) {
                $data->horas_semanais = number_format($data->horas_semanais, 2, ',', '');
            }
            if ($data->horas_semestre) {
                $data->horas_semestre = number_format($data->horas_semestre, 2, ',', '');
            }
            if ($data->horas_mensais) {
                $data->horas_mensais = number_format($data->horas_mensais, 2, ',', '');
            }
            if ($data->valor_hora_mensal) {
                $data->valor_hora_mensal = number_format($data->valor_hora_mensal, 2, ',', '.');
            }
            if ($data->valor_hora_operacional) {
                $data->valor_hora_operacional = number_format($data->valor_hora_operacional, 2, ',', '.');
            }
            if ($data->valor_hora_operacional_2) {
                $data->valor_hora_operacional_2 = number_format($data->valor_hora_operacional_2, 2, ',', '.');
            }
            if ($data->valor_hora_operacional_3) {
                $data->valor_hora_operacional_3 = number_format($data->valor_hora_operacional_3, 2, ',', '.');
            }
            if ($data->valor_hora_operacional_1t) {
                $data->valor_hora_operacional_1t = number_format($data->valor_hora_operacional_1t, 2, ',', '.');
            }
            if ($data->valor_hora_operacional_2t) {
                $data->valor_hora_operacional_2t = number_format($data->valor_hora_operacional_2t, 2, ',', '.');
            }
            if ($data->valor_hora_operacional_3t) {
                $data->valor_hora_operacional_3t = number_format($data->valor_hora_operacional_3t, 2, ',', '.');
            }
            if ($data->valor_hora_operacional_1n) {
                $data->valor_hora_operacional_1n = number_format($data->valor_hora_operacional_1n, 2, ',', '.');
            }
            if ($data->valor_hora_operacional_2n) {
                $data->valor_hora_operacional_2n = number_format($data->valor_hora_operacional_2n, 2, ',', '.');
            }
            if ($data->valor_hora_operacional_3n) {
                $data->valor_hora_operacional_3n = number_format($data->valor_hora_operacional_3n, 2, ',', '.');
            }

            $data->desconto_mensal_1 = number_format($data->desconto_mensal_1, 2, ',', '');
            $data->desconto_mensal_2 = number_format($data->desconto_mensal_2, 2, ',', '');
            $data->desconto_mensal_3 = number_format($data->desconto_mensal_3, 2, ',', '');
            $data->desconto_mensal_4 = number_format($data->desconto_mensal_4, 2, ',', '');
            $data->desconto_mensal_5 = number_format($data->desconto_mensal_5, 2, ',', '');
            $data->desconto_mensal_6 = number_format($data->desconto_mensal_6, 2, ',', '');

            if ($data->valor_mensal_1) {
                $data->valor_mensal_1 = number_format($data->valor_mensal_1, 2, ',', '.');
            }
            if ($data->valor_mensal_2) {
                $data->valor_mensal_2 = number_format($data->valor_mensal_2, 2, ',', '.');
            }
            if ($data->valor_mensal_3) {
                $data->valor_mensal_3 = number_format($data->valor_mensal_3, 2, ',', '.');
            }
            if ($data->valor_mensal_4) {
                $data->valor_mensal_4 = number_format($data->valor_mensal_4, 2, ',', '.');
            }
            if ($data->valor_mensal_5) {
                $data->valor_mensal_5 = number_format($data->valor_mensal_5, 2, ',', '.');
            }
            if ($data->valor_mensal_6) {
                $data->valor_mensal_6 = number_format($data->valor_mensal_6, 2, ',', '.');
            }
            if ($data->horas_mensais_custo) {
                $data->horas_mensais_custo = preg_replace('/(\d+):(\d+):(\d+)/', '$1:$2', $data->horas_mensais_custo);
            }
            if ($data->horas_mensais_custo_2) {
                $data->horas_mensais_custo_2 = preg_replace('/(\d+):(\d+):(\d+)/', '$1:$2', $data->horas_mensais_custo_2);
            }
            if ($data->horas_mensais_custo_3) {
                $data->horas_mensais_custo_3 = preg_replace('/(\d+):(\d+):(\d+)/', '$1:$2', $data->horas_mensais_custo_3);
            }
            if ($data->horas_mensais_custo_1t) {
                $data->horas_mensais_custo_1t = preg_replace('/(\d+):(\d+):(\d+)/', '$1:$2', $data->horas_mensais_custo_1t);
            }
            if ($data->horas_mensais_custo_2t) {
                $data->horas_mensais_custo_2t = preg_replace('/(\d+):(\d+):(\d+)/', '$1:$2', $data->horas_mensais_custo_2t);
            }
            if ($data->horas_mensais_custo_3t) {
                $data->horas_mensais_custo_3t = preg_replace('/(\d+):(\d+):(\d+)/', '$1:$2', $data->horas_mensais_custo_3t);
            }
            if ($data->horas_mensais_custo_1n) {
                $data->horas_mensais_custo_1n = preg_replace('/(\d+):(\d+):(\d+)/', '$1:$2', $data->horas_mensais_custo_1n);
            }
            if ($data->horas_mensais_custo_2n) {
                $data->horas_mensais_custo_2n = preg_replace('/(\d+):(\d+):(\d+)/', '$1:$2', $data->horas_mensais_custo_2n);
            }
            if ($data->horas_mensais_custo_3n) {
                $data->horas_mensais_custo_3n = preg_replace('/(\d+):(\d+):(\d+)/', '$1:$2', $data->horas_mensais_custo_3n);
            }
            if ($data->data_inicio_contrato) {
                $data->data_inicio_contrato = date('d/m/Y', strtotime($data->data_inicio_contrato));
            }
            if ($data->data_termino_contrato) {
                $data->data_termino_contrato = date('d/m/Y', strtotime($data->data_termino_contrato));
            }
            if ($data->pagamento_inicio) {
                $data->pagamento_inicio = number_format($data->pagamento_inicio, 2, ',', '.');
            }
            if ($data->pagamento_reajuste) {
                $data->pagamento_reajuste = number_format($data->pagamento_reajuste, 2, ',', '.');
            }

        } else {
            $fields = $this->db->list_fields('ei_ordem_servico_profissionais');
            $data = array_combine(array_flip($fields), array_pad([], count($fields), null));
            $data['id'] = $id;
        }

        $sql = "SELECT s.id, s.nome
                FROM(SELECT b.id, b.nome 
                     FROM ei_ordem_servico_profissionais a 
                     INNER JOIN usuarios b ON b.id = a.id_supervisor
                     WHERE a.id = '{$data->id}'
                     UNION 
                     SELECT d.id, d.nome 
                     FROM ei_coordenacao c
                     INNER JOIN usuarios d ON d.id = c.id_usuario
                     WHERE d.empresa = '{$this->session->userdata('empresa')}' AND 
                           c.ano = '{$data->ano}' AND 
                           c.semestre = '{$data->semestre}' AND 
                           c.is_supervisor = 1) s 
                ORDER BY s.nome ASC";
        $supervisores = $this->db->query($sql)->result_array();

        $nomeUsuario = $data->nome_usuario;
        $nomeSemana = $data->nome_semana;
        $nomePeriodo = $data->nome_periodo;
        $idSupervisor = $data->id_supervisor;

        unset($data->nome_usuario, $data->nome_semana, $data->nome_periodo, $data->id_supervisor);

        $retorno = [
            'data' => $data,
            'input' => [
                'nome_usuario' => $nomeUsuario,
                'nome_semana' => $nomeSemana,
                'nome_periodo' => $nomePeriodo,
            ],
        ];

        $retorno['input']['supervisores'] = form_dropdown('', ['' => 'selecione...'] + array_column($supervisores, 'nome', 'id'), $idSupervisor);

        $funcoes = $this->getFuncoes();
        $funcoes[''] = 'selecione...';

        $retorno['funcoes'] = form_dropdown('', $funcoes, $data->id_funcao);
        $retorno['funcoes_2m'] = form_dropdown('', $funcoes, $data->id_funcao_2m);
        $retorno['funcoes_3m'] = form_dropdown('', $funcoes, $data->id_funcao_3m);

        $retorno['funcoes_1t'] = form_dropdown('', $funcoes, $data->id_funcao_1t);
        $retorno['funcoes_2t'] = form_dropdown('', $funcoes, $data->id_funcao_2t);
        $retorno['funcoes_3t'] = form_dropdown('', $funcoes, $data->id_funcao_3t);

        $retorno['funcoes_1n'] = form_dropdown('', $funcoes, $data->id_funcao_1n);
        $retorno['funcoes_2n'] = form_dropdown('', $funcoes, $data->id_funcao_2n);
        $retorno['funcoes_3n'] = form_dropdown('', $funcoes, $data->id_funcao_3n);

        echo json_encode($retorno);
    }

    //--------------------------------------------------------------------

    public function ajax_edit_substituto1()
    {
        $id = $this->input->post('id');

        $data = $this->db
            ->select('id, id_usuario_sub1, id_ordem_servico_escola')
            ->select(["DATE_FORMAT(data_substituicao1, '%d/%m/%Y') AS data_substituicao1"], false)
            ->where('id', $id)
            ->get('ei_ordem_servico_profissionais')
            ->row();

        $municipios = $this->getMunicipios();
        $usuarios = ['' => 'selecione...'] + $this->getUsuarios();

        $data->municipio = form_dropdown('municipio', $municipios, '', 'id="municipio_sub1" class="form-control"');
        $data->id_usuario_sub1 = form_dropdown('id_usuario_sub1', $usuarios, $data->id_usuario_sub1);

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_edit_substituto2()
    {
        $id = $this->input->post('id');

        $data = $this->db
            ->select('id, id_usuario_sub2, id_ordem_servico_escola')
            ->select(["DATE_FORMAT(data_substituicao2, '%d/%m/%Y') AS data_substituicao2"], false)
            ->where('id', $id)
            ->get('ei_ordem_servico_profissionais')
            ->row();

        $municipios = $this->getMunicipios();
        $usuarios = ['' => 'selecione...'] + $this->getUsuarios();

        $data->municipio = form_dropdown('municipio', $municipios, '', 'id="municipio_sub2" class="form-control"');
        $data->id_usuario_sub2 = form_dropdown('id_usuario_sub1', $usuarios, $data->id_usuario_sub2);

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function atualizar_substituto()
    {
        $municipio = $this->input->post('municipio');
        $idUsuario = $this->input->post('id_usuario');

        $where = ['id !=', $idUsuario];
        if ($municipio) {
            $where['municipio'] = $municipio;
        }
        $usuarios = ['' => 'selecione...'] + $this->getUsuarios($where);

        $data['usuario'] = form_dropdown('usuario', $usuarios, '');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_save()
    {
        $idOSEscola = $this->input->post('id_ordem_servico_escola');
        $idUsuarios = $this->input->post('id_usuario');
        if (empty($idUsuarios)) {
            $idUsuarios = [];
        }
        $idSupervisor = $this->input->post('id_supervisor');
        if (empty($idSupervisor)) {
            exit(json_encode(['erro' => 'O supervisor é obrigatório.']));
        }

        $this->db->trans_start();

        $this->db
            ->where('id_ordem_servico_escola', $idOSEscola)
            ->where_not_in('id_usuario', $idUsuarios + [0])
            ->delete('ei_ordem_servico_profissionais');

        $profissionais = $this->db
            ->select('id_usuario')
            ->where('id_ordem_servico_escola', $idOSEscola)
            ->get('ei_ordem_servico_profissionais')
            ->result();

        $profissionais = array_column($profissionais, 'id_usuario');

        $idNovosUsuarios = array_diff($idUsuarios, $profissionais);

        foreach ($idNovosUsuarios as $idNovoUsuario) {
            $data = $this->db
                ->select('a.municipio, b.id AS id_departamento, c.id AS id_area')
                ->select('d.id AS id_setor, e.id AS id_cargo, f.id AS id_funcao')
                ->join('empresa_departamentos b', 'b.nome = a.depto', 'left')
                ->join('empresa_areas c', 'c.nome = a.area', 'left')
                ->join('empresa_setores d', 'd.nome = a.setor', 'left')
                ->join('empresa_cargos e', 'e.nome = a.cargo', 'left')
                ->join('empresa_funcoes f', 'f.nome = a.funcao', 'left')
                ->where('a.id', $idNovoUsuario)
                ->get('usuarios a')
                ->row_array();

            $data['id_ordem_servico_escola'] = $idOSEscola;
            $data['id_usuario'] = $idNovoUsuario;

            $this->db->insert('ei_ordem_servico_profissionais', $data);
        }

        if ($idSupervisor != '-1') {
            $this->db
                ->set('id_supervisor', $idSupervisor)
                ->where('id_ordem_servico_escola', $idOSEscola)
                ->where_in('id_usuario', $idUsuarios + [0])
                ->update('ei_ordem_servico_profissionais');
        }

        $this->db->trans_complete();
        $status = $this->db->trans_status();

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_add_horarios()
    {
        $isOSProfissioanl = $this->input->post('id_os_profissional');
        if (empty($isOSProfissioanl)) {
            exit(json_encode(['erro' => 'O cuidador não pode ficar em branco']));
        }

        $diasSemana = $this->input->post('dia_semana');
        if (empty($diasSemana)) {
            $diasSemana = [];
        }
        $horarioInicio = $this->input->post('horario_inicio');
        if (empty($horarioInicio)) {
            $horarioInicio = [];
        }
        $horarioTermino = $this->input->post('horario_termino');
        if (empty($horarioTermino)) {
            $horarioTermino = [];
        }

        $this->db->trans_start();

        $arrHorarios = [];
        foreach ($diasSemana as $k => $diaSemana) {
            if (strlen($diaSemana) > 0 and $horarioInicio[$k] and $horarioTermino[$k]) {
                $totalDiasMes = $this->contarSemanasDoMes($isOSProfissioanl, $diaSemana);
                $periodo = strstr($horarioInicio[$k], ':', true);
                if (strlen($periodo) > 0) {
                    $periodo = floor(intval($periodo) / 6);
                }
                $data = [
                    'id_os_profissional' => $isOSProfissioanl,
                    'id_funcao' => $this->input->post('id_funcao'),
                    'dia_semana' => $diaSemana,
                    'periodo' => $periodo,
                    'horario_inicio' => $horarioInicio[$k],
                    'horario_termino' => $horarioTermino[$k],
                    'total_dias_mes1' => $totalDiasMes[0],
                    'total_dias_mes2' => $totalDiasMes[1],
                    'total_dias_mes3' => $totalDiasMes[2],
                    'total_dias_mes4' => $totalDiasMes[3],
                    'total_dias_mes5' => $totalDiasMes[4],
                    'total_dias_mes6' => $totalDiasMes[5],
                ];

                $this->db->insert('ei_ordem_servico_horarios', $data);
                $arrHorarios[] = $this->db->insert_id();
            }
        }

        $arrAlunos = $this->input->post('alunos');
        if (empty($arrAlunos)) {
            $arrAlunos = [];
        }

        $data2 = [];
        foreach ($arrAlunos as $idOSAluno) {
            foreach ($arrHorarios as $idOSHorario) {
                $data2[] = [
                    'id_os_aluno' => $idOSAluno,
                    'id_os_horario' => $idOSHorario,
                ];
            }
        }

        if ($data2) {
            $this->db->insert_batch('ei_ordem_servico_turmas', $data2);
        }

        $this->db->trans_complete();
        $status = $this->db->trans_status();

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_update_horario()
    {
        $id = $this->input->post('id');
        $data = $this->input->post();
        $arrAlunos = $this->input->post('alunos');
        if (empty($arrAlunos)) {
            $arrAlunos = [];
        }
        unset($data['id'], $data['alunos']);
        if (empty($data['id_os_profissional'])) {
            exit(json_encode(['erro' => 'O cuidador não pode ficar em branco']));
        }
        $data['dia_semana'] = $data['dia_semana'][0] ?? null;
        $data['horario_inicio'] = $data['horario_inicio'][0] ?? null;
        $data['horario_termino'] = $data['horario_termino'][0] ?? null;
        $periodo = strstr($data['horario_inicio'], ':', true);
        if (strlen($periodo) > 0) {
            $data['periodo'] = floor(intval($periodo) / 6);
        } else {
            $data['periodo'] = null;
        }

        $this->db->trans_start();

        $count = $this->db
            ->select('dia_semana, horario_inicio, horario_termino')
            ->where('id !=', $id)
            ->where('id_os_profissional', $data['id_os_profissional'])
            ->where('dia_semana', $data['dia_semana'])
            ->where('horario_inicio', $data['horario_inicio'])
            ->where('horario_termino', $data['horario_termino'])
            ->get('ei_ordem_servico_horarios')
            ->num_rows();

        if ($count) {
            exit(json_encode(['erro' => 'O dia e horários já foram cadastrados para este cuidador.']));
        }

        $totalDiasMes = $this->contarSemanasDoMes($data['id_os_profissional'], $data['dia_semana']);

        if ($totalDiasMes) {
            $data['total_dias_mes1'] = $totalDiasMes[0];
            $data['total_dias_mes2'] = $totalDiasMes[1];
            $data['total_dias_mes3'] = $totalDiasMes[2];
            $data['total_dias_mes4'] = $totalDiasMes[3];
            $data['total_dias_mes5'] = $totalDiasMes[4];
            $data['total_dias_mes6'] = $totalDiasMes[5];
        } else {
            $data['total_dias_mes1'] = null;
            $data['total_dias_mes2'] = null;
            $data['total_dias_mes3'] = null;
            $data['total_dias_mes4'] = null;
            $data['total_dias_mes5'] = null;
            $data['total_dias_mes6'] = null;
        }

        $this->db->update('ei_ordem_servico_horarios', $data, ['id' => $id]);

        $data2 = [];
        foreach ($arrAlunos as $idOSAluno) {
            $data2[] = [
                'id_os_aluno' => $idOSAluno,
                'id_os_horario' => $id,
            ];
        }

        $this->db->delete('ei_ordem_servico_turmas', ['id_os_horario' => $id]);

        if ($data2) {
            $this->db->insert_batch('ei_ordem_servico_turmas', $data2);
        }

        $this->db->trans_complete();
        $status = $this->db->trans_status();

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_delete()
    {
        $status = $this->db->delete('ei_ordem_servico_profissionais', ['id' => $this->input->post('id')]);

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_delete_horario()
    {
        $status = $this->db->delete('ei_ordem_servico_horarios', ['id' => $this->input->post('id')]);

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_save_dados()
    {
        $data = $this->input->post();
        $id = $this->input->post('id');
        $idOSProfissional = $this->input->post('id_os_profissional');
        $idFuncao2m = $this->input->post('id_funcao_2m');
        $idFuncao3m = $this->input->post('id_funcao_3m');
        $idFuncao1t = $this->input->post('id_funcao_1t');
        $idFuncao2t = $this->input->post('id_funcao_2t');
        $idFuncao3t = $this->input->post('id_funcao_3t');
        $idFuncao1n = $this->input->post('id_funcao_1n');
        $idFuncao2n = $this->input->post('id_funcao_2n');
        $idFuncao3n = $this->input->post('id_funcao_3n');

        $valorHoraOperacional2 = $this->input->post('valor_hora_operacional_2');
        $valorHoraOperacional3 = $this->input->post('valor_hora_operacional_3');
        $valorHoraOperacional1t = $this->input->post('valor_hora_operacional_1t');
        $valorHoraOperacional2t = $this->input->post('valor_hora_operacional_2t');
        $valorHoraOperacional3t = $this->input->post('valor_hora_operacional_3t');
        $valorHoraOperacional1n = $this->input->post('valor_hora_operacional_1n');
        $valorHoraOperacional2n = $this->input->post('valor_hora_operacional_2n');
        $valorHoraOperacional3n = $this->input->post('valor_hora_operacional_3n');

        $horasMensaisCusto2 = $this->input->post('horas_mensais_custo_2');
        $horasMensaisCusto3 = $this->input->post('horas_mensais_custo_3');
        $horasMensaisCusto1t = $this->input->post('horas_mensais_custo_1t');
        $horasMensaisCusto2t = $this->input->post('horas_mensais_custo_2t');
        $horasMensaisCusto3t = $this->input->post('horas_mensais_custo_3t');
        $horasMensaisCusto1n = $this->input->post('horas_mensais_custo_1n');
        $horasMensaisCusto2n = $this->input->post('horas_mensais_custo_2n');
        $horasMensaisCusto3n = $this->input->post('horas_mensais_custo_3n');

        unset($data['id_funcao_2m']);
        unset($data['id_funcao_3m']);
        unset($data['id_funcao_1t']);
        unset($data['id_funcao_2t']);
        unset($data['id_funcao_3t']);
        unset($data['id_funcao_1n']);
        unset($data['id_funcao_2n']);
        unset($data['id_funcao_3n']);

        unset($data['valor_hora_operacional_2']);
        unset($data['valor_hora_operacional_3']);
        unset($data['valor_hora_operacional_1t']);
        unset($data['valor_hora_operacional_2t']);
        unset($data['valor_hora_operacional_3t']);
        unset($data['valor_hora_operacional_1n']);
        unset($data['valor_hora_operacional_2n']);
        unset($data['valor_hora_operacional_3n']);

        unset($data['horas_mensais_custo_2']);
        unset($data['horas_mensais_custo_3']);
        unset($data['horas_mensais_custo_1t']);
        unset($data['horas_mensais_custo_2t']);
        unset($data['horas_mensais_custo_3t']);
        unset($data['horas_mensais_custo_1n']);
        unset($data['horas_mensais_custo_2n']);
        unset($data['horas_mensais_custo_3n']);

        if (empty($data['id_supervisor'])) {
            $data['id_supervisor'] = null;
        }

        if ($id) {
            $idSupervisor = $data['id_supervisor'];
            unset($data['id'], $data['id_usuario'], $data['id_ordem_servico_escola'], $data['id_supervisor']);
            $tipo = array_column($this->db->field_data('ei_ordem_servico_profissionais'), 'type', 'name');
            $tipo['pagamento_inicio'] = 'decimal';
            $tipo['pagamento_reajuste'] = 'decimal';
        } else {
            unset($data['id'], $data['id_os_profissional']);
            $tipo = array_column($this->db->field_data('ei_ordem_servico_profissionais'), 'type', 'name');
        }

        foreach ($data as $campo => $valor) {
            if (isset($tipo[$campo])) {
                if ($tipo[$campo] == 'decimal') {
                    $data[$campo] = str_replace(['.', ','], ['', '.'], $valor);
                } elseif ($tipo[$campo] == 'date') {
                    if (strlen($data[$campo])) {
                        $data[$campo] = date('Y-m-d', strtotime(str_replace('/', '-', $data[$campo])));
                    } else {
                        $data[$campo] = null;
                    }
                }
            }
        }


        if ($id) {
            $horario = $this->db
                ->get_where('ei_ordem_servico_horarios', ['id' => $id])
                ->row();

            $data2 = $data;
            unset($data2['pagamento_inicio'], $data2['pagamento_reajuste'], $data2['faturamento_semestral_projetado']);
            unset($data2['valor_hora_operacional_2'], $data2['valor_hora_operacional_3']);
            unset($data2['horas_mensais_custo_2'], $data2['horas_mensais_custo_3']);

            $status = $this->db
                ->where('id_os_profissional', $idOSProfissional)
                ->where('periodo', $horario->periodo)
                ->update('ei_ordem_servico_horarios', $data2);

            $dataProfissionais = [
                'id_supervisor' => $idSupervisor,
                'pagamento_inicio' => $data['pagamento_inicio'],
                'valor_hora_operacional' => $data['valor_hora_operacional'],
                'valor_hora_operacional_2' => str_replace(['.', ','], ['', '.'], $valorHoraOperacional2),
                'valor_hora_operacional_3' => str_replace(['.', ','], ['', '.'], $valorHoraOperacional3),
                'valor_hora_operacional_1t' => str_replace(['.', ','], ['', '.'], $valorHoraOperacional1t),
                'valor_hora_operacional_2t' => str_replace(['.', ','], ['', '.'], $valorHoraOperacional2t),
                'valor_hora_operacional_3t' => str_replace(['.', ','], ['', '.'], $valorHoraOperacional3t),
                'valor_hora_operacional_1n' => str_replace(['.', ','], ['', '.'], $valorHoraOperacional1n),
                'valor_hora_operacional_2n' => str_replace(['.', ','], ['', '.'], $valorHoraOperacional2n),
                'valor_hora_operacional_3n' => str_replace(['.', ','], ['', '.'], $valorHoraOperacional3n),
                'horas_mensais_custo' => $data['horas_mensais_custo'],
                'horas_mensais_custo_2' => $horasMensaisCusto2,
                'horas_mensais_custo_3' => $horasMensaisCusto3,
                'horas_mensais_custo_1t' => $horasMensaisCusto1t,
                'horas_mensais_custo_2t' => $horasMensaisCusto2t,
                'horas_mensais_custo_3t' => $horasMensaisCusto3t,
                'horas_mensais_custo_1n' => $horasMensaisCusto1n,
                'horas_mensais_custo_2n' => $horasMensaisCusto2n,
                'horas_mensais_custo_3n' => $horasMensaisCusto3n,
                'pagamento_reajuste' => $data['pagamento_reajuste'],
                'id_funcao' => $data['id_funcao'],
                'id_funcao_2m' => $idFuncao2m,
                'id_funcao_3m' => $idFuncao3m,
                'id_funcao_1t' => $idFuncao1t,
                'id_funcao_2t' => $idFuncao2t,
                'id_funcao_3t' => $idFuncao3t,
                'id_funcao_1n' => $idFuncao1n,
                'id_funcao_2n' => $idFuncao2n,
                'id_funcao_3n' => $idFuncao3n,
            ];

            $this->db->update('ei_ordem_servico_profissionais', $dataProfissionais, ['id' => $idOSProfissional]);
        } else {
            if ($idOSProfissional) {
                if (!$this->db->get_where('ei_ordem_servico_profissionais', ['id' => $idOSProfissional])->num_rows()) {
                    exit(json_encode(['erro' => 'Não foi possível atualizar os dados do profissional.']));
                }
                $status = $this->db->update('ei_ordem_servico_profissionais', $data, ['id' => $idOSProfissional]);
            } else {
                $status = $this->db->insert('ei_ordem_servico_profissionais', $data);
            }
        }

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_save_substituto1()
    {
        $data = $this->input->post();
        $id = $this->input->post('id');
        if (empty($data['id_usuario_sub1'])) {
            exit(json_encode(['erro' => 'O profissional substituto é obrigatório']));
        }
        if (strlen($data['data_substituicao1']) == 0) {
            exit(json_encode(['erro' => 'A data de início é obrigatória']));
        } elseif ($data['data_substituicao1'] != date('d/m/Y', strtotime(str_replace('/', '-', $data['data_substituicao1'])))) {
            exit(json_encode(['erro' => 'A data de início é inválida']));
        }
        unset($data['id']);
        $data['data_substituicao1'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['data_substituicao1'])));

        $status = $this->db->update('ei_ordem_servico_profissionais', $data, ['id' => $id]);

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_save_substituto2()
    {
        $data = $this->input->post();
        $id = $this->input->post('id');
        if (empty($data['id_usuario_sub2'])) {
            exit(json_encode(['erro' => 'O profissional substituto é obrigatório']));
        }
        if (strlen($data['data_substituicao2']) == 0) {
            exit(json_encode(['erro' => 'A data de início é obrigatória']));
        } elseif ($data['data_substituicao2'] != date('d/m/Y', strtotime(str_replace('/', '-', $data['data_substituicao2'])))) {
            exit(json_encode(['erro' => 'A data de início é inválida']));
        }
        unset($data['id']);
        $data['data_substituicao2'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['data_substituicao2'])));

        $status = $this->db->update('ei_ordem_servico_profissionais', $data, ['id' => $id]);

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    private function getDepartamentos(): array
    {
        $rows = $this->db
            ->select('id, nome')
            ->where('id_empresa', $this->session->userdata('empresa'))
            ->where('nome', 'Educação Inclusiva')
            ->get('empresa_departamentos')
            ->result();

        return array_column($rows, 'nome', 'id');
    }

    //--------------------------------------------------------------------

    private function getAreas(array $where = []): array
    {
        $qb = $this->db
            ->select('a.id, a.nome')
            ->join('empresa_departamentos b', 'b.id = a.id_departamento')
            ->where('b.id_empresa', $this->session->userdata('empresa'))
            ->where('b.nome', 'Educação Inclusiva');
        if (!empty($where['id_depto'])) {
            $qb->where('b.id', $where['id_depto']);
        }
        $rows = $qb
            ->order_by('a.nome', 'asc')
            ->get('empresa_areas a')
            ->result();

        return ['' => 'Todas'] + array_column($rows, 'nome', 'id');
    }

    //--------------------------------------------------------------------

    private function getSetores(array $where = []): array
    {
        $qb = $this->db
            ->select('a.id, a.nome')
            ->join('empresa_areas b', 'b.id = a.id_area')
            ->join('empresa_departamentos c', 'c.id = b.id_departamento')
            ->where('c.id_empresa', $this->session->userdata('empresa'))
            ->where('c.nome', 'Educação Inclusiva');
        if (!empty($where['id_depto'])) {
            $qb->where('c.id', $where['id_depto']);
        }
        if (!empty($where['id_area'])) {
            $qb->where('b.id', $where['id_area']);
        }
        $rows = $qb
            ->order_by('a.nome', 'asc')
            ->get('empresa_setores a')
            ->result();

        return ['' => 'Todos'] + array_column($rows, 'nome', 'id');
    }

    //--------------------------------------------------------------------

    private function getCargos(array $where = []): array
    {
        $qb = $this->db->select('a.id, a.nome')
            ->join('usuarios b', 'b.cargo = a.nome')
            ->join('empresa_departamentos c', 'c.nome = b.depto', 'left')
            ->join('empresa_areas d', 'd.nome = b.area', 'left')
            ->join('empresa_setores e', 'e.nome = b.setor', 'left')
            ->where('a.id_empresa', $this->session->userdata('empresa'))
            ->where('c.nome', 'Educação Inclusiva');
        if (!empty($where['id_area'])) {
            $qb->where('d.id', $where['id_area']);
        }
        if (!empty($where['id_setor'])) {
            $qb->where('e.id', $where['id_setor']);
        }
        $rows = $qb
            ->group_by('a.id')
            ->order_by('a.nome', 'asc')
            ->get('empresa_cargos a')
            ->result();

        return ['' => 'Todos'] + array_column($rows, 'nome', 'id');
    }

    //--------------------------------------------------------------------

    private function getFuncoes(array $where = []): array
    {
        $qb = $this->db
            ->select('a.id, a.nome')
            ->join('empresa_cargos b', 'b.id = a.id_cargo')
            ->join('usuarios c', 'c.funcao = a.nome')
            ->join('empresa_departamentos d', 'd.nome = c.depto', 'left')
            ->join('empresa_areas e', 'e.nome = c.area', 'left')
            ->join('empresa_setores f', 'f.nome = c.setor', 'left')
            ->where('b.id_empresa', $this->session->userdata('empresa'))
            ->where('d.nome', 'Educação Inclusiva');
        if (!empty($where['id_area'])) {
            $qb->where('e.id', $where['id_area']);
        }
        if (!empty($where['id_setor'])) {
            $qb->where('f.id', $where['id_setor']);
        }
        if (!empty($where['id_cargo'])) {
            $qb->where('b.id', $where['id_cargo']);
        }
        $rows = $qb
            ->group_by('a.id')
            ->order_by('a.nome', 'asc')
            ->get('empresa_funcoes a')
            ->result();

        return ['' => 'Todas'] + array_column($rows, 'nome', 'id');
    }

    //--------------------------------------------------------------------

    private function getMunicipios(array $where = []): array
    {
        $qb = $this->db
            ->select('a.municipio')
            ->join('empresa_areas b', 'b.nome = a.area')
            ->join('empresa_setores c', 'c.nome = a.setor')
            ->join('empresa_cargos d', 'd.nome = a.cargo')
            ->join('empresa_funcoes e', 'e.nome = a.funcao')
            ->where('a.empresa', $this->session->userdata('empresa'))
            ->where('CHAR_LENGTH(a.municipio) >', 0)
            ->where('a.depto', 'Educação Inclusiva');
        if (!empty($where['id_area'])) {
            $qb->where('b.id', $where['id_area']);
        }
        if (!empty($where['id_setor'])) {
            $qb->where('c.id', $where['id_setor']);
        }
        if (!empty($where['id_cargo'])) {
            $qb->where('d.id', $where['id_cargo']);
        }
        if (!empty($where['id_funcao'])) {
            $qb->where('e.id', $where['id_funcao']);
        }
        $rows = $qb
            ->group_by('a.municipio')
            ->order_by('a.municipio', 'asc')
            ->get('usuarios a')
            ->result();

        return ['' => 'Todos'] + array_column($rows, 'municipio', 'municipio');
    }

    //--------------------------------------------------------------------

    private function getUsuarios(array $where = []): array
    {
        $qb = $this->db
            ->select('a.id, a.nome')
            ->join('empresa_areas b', 'b.nome = a.area')
            ->join('empresa_setores c', 'c.nome = a.setor')
            ->join('empresa_cargos d', 'd.nome = a.cargo')
            ->join('empresa_funcoes e', 'e.nome = a.funcao')
            ->where('a.empresa', $this->session->userdata('empresa'))
            ->where('a.tipo', 'funcionario')
            ->where_in('a.status', [1, 3])
            ->where('a.depto', 'Educação Inclusiva');
        if (!empty($where['id_area'])) {
            $qb->where('b.id', $where['id_area']);
        }
        if (!empty($where['id_setor'])) {
            $qb->where('c.id', $where['id_setor']);
        }
        if (!empty($where['id_cargo'])) {
            $qb->where('d.id', $where['id_cargo']);
        }
        if (!empty($where['id_funcao'])) {
            $qb->where('e.id', $where['id_funcao']);
        }
        if (!empty($where['municipio'])) {
            $qb->where('a.municipio', $where['municipio']);
        }
        $rows = $qb
            ->order_by('a.nome', 'asc')
            ->get('usuarios a')
            ->result();

        return array_column($rows, 'nome', 'id');
    }

    //--------------------------------------------------------------------

    /**
     * Calcula o total de dias de uma semana para cada mês de um semestre
     */
    private function contarSemanasDoMes(?int $idOSProfissional, ?int $diaDaSemana): array
    {
        switch ($diaDaSemana) {
            case 0:
                $semana = 'sun';
                break;
            case 1:
                $semana = 'mon';
                break;
            case 2:
                $semana = 'tue';
                break;
            case 3:
                $semana = 'wed';
                break;
            case 4:
                $semana = 'thu';
                break;
            case 5:
                $semana = 'fri';
                break;
            case 6:
                $semana = 'sat';
                break;
            default:
                return [];
        }

        $row = $this->db
            ->select('c.ano, c.semestre')
            ->select("DATE_FORMAT(MIN(f.data_inicio), '%M %Y') AS mes_inicial", false)
            ->select("DATE_FORMAT(MAX(f.data_termino), '%M %Y') AS mes_final", false)
            ->select('MIN(f.data_inicio) AS data_inicio', false)
            ->select('MAX(f.data_termino) AS data_termino', false)
            ->join('ei_ordem_servico_escolas b', 'b.id = a.id_ordem_servico_escola')
            ->join('ei_ordem_servico c', 'c.id = b.id_ordem_servico')
            ->join('ei_ordem_servico_horarios d', 'd.id_os_profissional = a.id', 'left')
            ->join('ei_ordem_servico_turmas e', 'e.id_os_horario = d.id', 'left')
            ->join('ei_ordem_servico_alunos f', 'f.id = e.id_os_aluno', 'left')
            ->where('a.id', $idOSProfissional)
            ->group_by('a.id')
            ->get('ei_ordem_servico_profissionais a')
            ->row();

        $mesInicial = intval($row->semestre) == 2 ? 7 : 1;
        $mesFinal = $mesInicial + 5;
        $mesAno = [];
        for ($i = $mesInicial; $i <= $mesFinal; $i++) {
            $mesAno[] = date('F Y', strtotime('01-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-' . $row->ano));
        }

        $data = [];
        foreach ($mesAno as $mes) {
            if ($mes == $row->mes_inicial and $row->data_inicio) {
                $semanaInicial = date('W', strtotime("{$semana} {$row->data_inicio}"));
            } else {
                $semanaInicial = date('W', strtotime("first {$semana} of {$mes}"));
            }
            if ($mes == $row->mes_final and $row->data_termino) {
                $semanaFinal = date('W', strtotime($semana, strtotime("{$row->data_termino} -1 week +1 day"))) + 1;
            } else {
                $semanaFinal = date('W', strtotime("last {$semana} of {$mes} -1 week")) + 1;
            }

            $data[] = $semanaFinal - ($semanaInicial - 1);
        }

        return $data;
    }

}
