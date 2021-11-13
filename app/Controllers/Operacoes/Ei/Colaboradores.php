<?php

namespace App\Controllers\Ei;

use App\Controllers\BaseController;

class Colaboradores extends BaseController
{

    public function index()
    {
        $this->load->model('usuario_model', 'usuario');

        $filtro = $this->db
            ->select('depto')
            ->like('depto', 'Educação Inclusiva')
            ->get('usuarios')
            ->row();

        $data = $this->get_filtros_usuarios($filtro->depto);
        $data['status'] = ['' => 'Todos'] + $this->usuario::STATUS;
        $data['depto'] = ['Educação Inclusiva' => 'Educação Inclusiva'];

        $qb = $this->db;
        if ($this->session->userdata('tipo') == 'funcionario' and in_array($this->session->userdata('nivel'), [9, 10, 11])) {
            if (in_array($this->session->userdata('nivel'), [9, 10])) {
                $qb->select("depto, '' AS area, '' AS setor", false);
            } else {
                $qb->select('depto, area, setor');
            }
            $qb->where('id', $this->session->userdata('id'));
        } else {
            $qb->select("depto, '' AS area, '' AS setor", false)
                ->like('depto', 'Educação Inclusiva');
        }
        $status = $qb
            ->get('usuarios')
            ->row();

        $data['depto_atual'] = $status->depto;
        $data['area_atual'] = $status->area;
        $data['setor_atual'] = $status->setor;

        $contratos = $this->db
            ->select('DISTINCT(contrato) AS nome', false)
            ->where('empresa', $this->session->userdata('empresa'))
            ->where('CHAR_LENGTH(contrato) >', 0)
            ->get('usuarios')
            ->result();

        $data['contrato'] = ['' => 'selecione...'];
        foreach ($contratos as $contrato) {
            $data['contrato'][$contrato->nome] = $contrato->nome;
        }

        $this->load->view('ei/colaboradores', $data);
    }

    //--------------------------------------------------------------------

    public function atualizar_filtro()
    {
        $depto = $this->input->post('depto');
        $area = $this->input->post('area');
        $setor = $this->input->post('setor');
        $cargo = $this->input->post('cargo');
        $funcao = $this->input->post('funcao');

        $filtro = $this->get_filtros_usuarios($depto, $area, $setor, $cargo, $funcao);

        $data['area'] = form_dropdown('area', $filtro['area'], $area, 'class="form-control input-sm"');
        $data['setor'] = form_dropdown('setor', $filtro['setor'], $setor, 'class="form-control input-sm"');
        $data['cargo'] = form_dropdown('cargo', $filtro['cargo'], $cargo, 'class="form-control input-sm"');
        $data['funcao'] = form_dropdown('funcao', $filtro['funcao'], $funcao, 'class="form-control input-sm"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function atualizar_filtro2()
    {
        $depto = $this->input->post('depto');
        $area = $this->input->post('area');
        $setor = $this->input->post('setor');
        $cargo = $this->input->post('cargo');
        $funcao = $this->input->post('funcao');

        $filtro = $this->get_filtros_usuarios($depto, $area, $setor, $cargo, $funcao);
        if ($this->session->userdata('tipo') == 'funcionario') {
            if (!in_array($this->session->userdata('nivel'), [9, 10])) {
                unset($filtro['area'][''], $filtro['setor']['']);
            }
            unset($filtro['depto']['']);
        }

        $data['area'] = form_dropdown('area', $filtro['area'], $area, 'onchange="atualizarFiltro()" class="form-control input-sm"');
        $data['setor'] = form_dropdown('setor', $filtro['setor'], $setor, 'onchange="atualizarFiltro()" class="form-control input-sm"');
        $data['cargo'] = form_dropdown('cargo', $filtro['cargo'], $cargo, 'onchange="atualizarFiltro()" class="form-control input-sm"');
        $data['funcao'] = form_dropdown('funcao', $filtro['funcao'], $funcao, 'onchange="atualizarFiltro()" class="form-control input-sm"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function novo()
    {
        $empresa = $this->session->userdata('empresa');
        $post = $this->input->post();
        $mes = empty($post['mes']) ? date('m') : $post['mes'];
        $ano = empty($post['ano']) ? date('Y') : $post['ano'];

        $num_rows = $this->db
            ->where('id_empresa', $empresa)
            ->where('data', date('Y-m-t', mktime(0, 0, 0, $mes, 1, $ano)))
            ->where('depto', $post['depto'])
            ->where('area', $post['area'])
            ->where('setor', $post['setor'])
            ->get('alocacao')
            ->num_rows();

        if ($num_rows) {
            exit;
        }

        $data = [
            'id_empresa' => $empresa,
            'data' => date('Y-m-t', mktime(0, 0, 0, $mes, 1, $ano)),
            'depto' => $post['depto'],
            'area' => $post['area'],
            'setor' => $post['setor'],
        ];

        $this->db->trans_start();

        $this->db->insert('alocacao', $data);
        $id_alocacao = $this->db->insert_id();

        $data2 = $this->db
            ->select("'{$id_alocacao}' AS id_alocacao, a.id AS id_usuario", false)
            ->select("'I' AS tipo_horario, 'P' AS nivel", false)
            ->where('a.depto', $post['depto'])
            ->where('a.area', $post['area'])
            ->where('a.setor', $post['setor'])
            ->get('usuarios a, (SELECT @rownum:=0) b')
            ->result_array();

        $this->db->insert_batch('alocacao_usuarios', $data2);

        $this->db->trans_complete();
        $status = $this->db->trans_status();

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function get_colaboradores()
    {
        parse_str($this->input->post('busca'), $busca);

        $qb = $this->db
            ->select('id, nome, status')
            ->select("CONCAT_WS('/', depto, area, setor) AS estrutura", false)
            ->select("CONCAT(cargo, '/', funcao) AS cargo_funcao", false)
            ->where('empresa', $this->session->userdata('empresa'))
            ->where_in('tipo', ['funcionario', 'selecionador']);
        if (!empty($busca['busca'])) {
            $qb->group_start()
                ->like('nome', $busca['busca'])
                ->or_like('email', $busca['busca'])
                ->group_end();
        }
        if (!empty($busca['pdi'])) {
            $qb->where("id IN (SELECT id_usuario FROM pdi WHERE status = '{$busca['pdi']}')", null, false);
        }
        if (!empty($busca['status'])) {
            $qb->where('status', $busca['status']);
        }
        if (!empty($busca['depto'])) {
            $qb->where('depto', $busca['depto']);
        }
        if (!empty($busca['area'])) {
            $qb->where('area', $busca['area']);
        }
        if (!empty($busca['setor'])) {
            $qb->where('setor', $busca['setor']);
        }
        if (!empty($busca['cargo'])) {
            $qb->where('cargo', $busca['cargo']);
        }
        if (!empty($busca['funcao'])) {
            $qb->where('funcao', $busca['funcao']);
        }
        if (!empty($busca['contrato'])) {
            $qb->where('contrato', $busca['contrato']);
        }
        $sql = $qb->get_compiled_select('usuarios');

        $config = [
            'select' => ['id', 'nome', 'status', 'estrutura', 'cargo_funcao'],
            'search' => ['nome', 'email'],
        ];

        $this->load->library('dataTables', $config);

        $output = $this->datatables->query($sql);

        $this->load->model('usuario_model');
        $status = $this->usuario_model::STATUS;

        $data = [];
        foreach ($output->data as $row) {
            $data[] = [
                $row->id,
                $row->nome,
                $status[$row->status] ?? null,
                implode('/', array_filter(explode('/', $row->estrutura))),
                implode('/', array_filter(explode('/', $row->cargo_funcao))),
                '<a class="btn btn-primary btn-xs" href="' . site_url('ei/colaboradores/editar_perfil/' . $row->id) . '"><i class="fa fa-edit"></i> Edição rápida</a>
                 <a class="btn btn-success btn-xs" href="' . site_url('ead/cursos_funcionario/index/' . $row->id) . '"><i class="fa fa-graduation-cap"></i> Treinamentos</a>
                 <a class="btn btn-magenta btn-xs" href="' . site_url('pdi/gerenciar/' . $row->id) . '" style="background-color: #A0511D; color: #FFF;"><i class="fa fa-briefcase"></i> PDIs</a>
                 <button type="button" class="btn btn-xs btn-info" onclick="gerenciar_contratos(' . $row->id . ');" title="Gerenciar contratosr"><i class="glyphicon glyphicon-plus"></i> Contratos</button>',
            ];
        }
        $output->data = $data;

        $filtro = $this->get_filtros_usuarios($busca['depto'], $busca['area'], $busca['setor'], $busca['cargo'], $busca['funcao']);
        if ($this->session->userdata('tipo') == 'funcionario') {
            if (!in_array($this->session->userdata('nivel'), [9, 10])) {
                unset($filtro['area'][''], $filtro['setor']['']);
            }
            unset($filtro['depto']['']);
        }

        $output->areas = form_dropdown('', $filtro['area'], $busca['area']);
        $output->setores = form_dropdown('', $filtro['setor'], $busca['setor']);
        $output->cargos = form_dropdown('', $filtro['cargo'], $busca['cargo']);
        $output->funcoes = form_dropdown('', $filtro['funcao'], $busca['funcao']);

        echo json_encode($output);
    }

    //--------------------------------------------------------------------

    public function ajax_list3()
    {
        $post = $this->input->post();
        parse_str($this->input->post('busca'), $busca);

        $sql = "SELECT s.id, 
                       s.nome,
                       s.estrutura,
                       s.cargo_funcao,
                       s.depto,
                       s.area,
                       s.setor,
                       s.cargo, 
                       s.funcao,
                       s.contrato
                FROM (SELECT c.id, 
                             c.nome,
                             CONCAT_WS('/', c.depto, c.area, c.setor) AS estrutura,
                             CONCAT_WS('/', c.cargo, c.funcao) AS cargo_funcao,
                             c.depto,
                             c.area,
                             c.setor,
                             c.cargo, 
                             c.funcao,
                             c.contrato
                      FROM alocacao_usuarios a
                      INNER JOIN alocacao b ON 
                                 b.id = a.id_alocacao 
                      INNER JOIN usuarios c ON 
                                 c.id = a.id_usuario 
                      WHERE b.id_empresa = {$this->session->userdata('empresa')} GROUP BY c.id) s";
        $recordsTotal = $this->db->query($sql)->num_rows();

        $columns = ['s.id', 's.nome'];
        if ($post['search']['value']) {
            foreach ($columns as $key => $column) {
                if ($key > 1) {
                    $sql .= " OR
                         {$column} LIKE '%{$post['search']['value']}%'";
                } elseif ($key == 1) {
                    $sql .= " 
                        WHERE {$column} LIKE '%{$post['search']['value']}%'";
                }
            }
        }
        $recordsFiltered = $this->db->query($sql)->num_rows();

        if (isset($post['order'])) {
            $orderBy = [];
            foreach ($post['order'] as $order) {
                $orderBy[] = ($order['column'] + 1) . ' ' . $order['dir'];
            }
            $sql .= ' 
                    ORDER BY ' . implode(', ', $orderBy);
        }
        $sql .= " 
                LIMIT {$post['start']}, {$post['length']}";
        $list = $this->db->query($sql)->result();

        $data = [];
        foreach ($list as $apontamento) {
            $row = [];
            $row[] = $apontamento->nome;
            $row[] = $apontamento->estrutura;
            $row[] = $apontamento->cargo_funcao;
            $row[] = '
                      <a class="btn btn-sm btn-primary" href="' . site_url('apontamento_colaboradores/editar_perfil/' . $apontamento->id) . '" title="Edição rápida"><i class="glyphicon glyphicon-pencil"></i> </a>
                      <a class="btn btn-sm btn-success" href="' . site_url('ead/cursos_funcionario/index/' . $apontamento->id) . '" target="_blank" title="Treinamentos"><i class="glyphicon glyphicon-plus"></i> Treinamentos</a>
                      <a class="btn btn-sm btn-warning" href="' . site_url('pdi/gerenciar/' . $apontamento->id) . '" target="_blank" title="PDIs"><i class="glyphicon glyphicon-plus"></i> PDIs</a>
                     ';
            $data[] = $row;
        }

        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    //--------------------------------------------------------------------

    public function ajax_list()
    {
        $post = $this->input->post();
        parse_str($this->input->post('busca'), $busca);

        $sql = "SELECT s.id_usuario,
                       s.nome,
                       s.estrutura,
                       s.cargo_funcao,
                       s.depto,
                       s.area,
                       s.setor,
                       s.cargo, 
                       s.funcao,
                       s.contrato,
                       s.id
                FROM (SELECT a.id, 
                             c.id AS id_usuario,
                             c.nome,
                             CONCAT_WS('/', c.depto, c.area, c.setor) AS estrutura,
                             CONCAT_WS('/', c.cargo, c.funcao) AS cargo_funcao,
                             c.depto,
                             c.area,
                             c.setor,
                             c.cargo, 
                             c.funcao,
                             c.contrato
                      FROM alocacao_usuarios a
                      INNER JOIN alocacao b ON 
                                 b.id = a.id_alocacao 
                      INNER JOIN usuarios c ON 
                                 c.id = a.id_usuario 
                      WHERE b.id_empresa = {$this->session->userdata('empresa')} AND 
                            DATE_FORMAT(b.data, '%Y-%m') = '{$busca['ano']}-{$busca['mes']}'";
        if ($busca['depto']) {
            $sql .= " AND c.depto = '{$busca['depto']}'";
        }
        if ($busca['area']) {
            $sql .= " AND c.area = '{$busca['area']}'";
        }
        if ($busca['setor']) {
            $sql .= " AND c.setor = '{$busca['setor']}'";
        }
        if ($busca['cargo']) {
            $sql .= " AND c.cargo = '{$busca['cargo']}'";
        }
        if ($busca['funcao']) {
            $sql .= " AND c.funcao = '{$busca['funcao']}'";
        }
        $sql .= ' GROUP BY a.id) s';
        $recordsTotal = $this->db->query($sql)->num_rows();

        $columns = ['s.id', 's.nome'];
        if ($post['search']['value']) {
            foreach ($columns as $key => $column) {
                if ($key > 1) {
                    $sql .= " OR
                         {$column} LIKE '%{$post['search']['value']}%'";
                } elseif ($key == 1) {
                    $sql .= " 
                        WHERE {$column} LIKE '%{$post['search']['value']}%'";
                }
            }
        }
        $recordsFiltered = $this->db->query($sql)->num_rows();

        if (isset($post['order'])) {
            $orderBy = [];
            foreach ($post['order'] as $order) {
                $orderBy[] = ($order['column'] + 1) . ' ' . $order['dir'];
            }
            $sql .= ' 
                    ORDER BY ' . implode(', ', $orderBy);
        }
        $sql .= " 
                LIMIT {$post['start']}, {$post['length']}";
        $list = $this->db->query($sql)->result();

        $data = [];
        foreach ($list as $apontamento) {
            $row = [];
            $row[] = $apontamento->nome;
            $row[] = $apontamento->depto;
            $row[] = $apontamento->area;
            $row[] = $apontamento->setor;
            $row[] = $apontamento->funcao;
            $row[] = '
                      <a class="btn btn-xs btn-success" href="' . site_url('ead/cursos_funcionario/index/' . $apontamento->id_usuario) . '" target="_blank" title="Treinamentos"><i class="glyphicon glyphicon-plus"></i> Treinamentos</a>
                      <a class="btn btn-xs btn-warning" href="' . site_url('pdi/gerenciar/' . $apontamento->id_usuario) . '" target="_blank" title="PDIs"><i class="glyphicon glyphicon-plus"></i> PDIs</a>
                      <button type="button" class="btn btn-xs btn-danger" onclick="delete_colaborador(' . $apontamento->id . ')" title="Excluir alocação de colaborador"><i class="glyphicon glyphicon-trash"></i> </button>
                     ';
            $data[] = $row;
        }

        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    //--------------------------------------------------------------------

    public function ajax_colaboradores()
    {
        parse_str($this->input->post('busca'), $busca);

        $qb = $this->db
            ->select('a.id, b.nome')
            ->join('usuarios b', 'b.id = a.id_usuario')
            ->join('alocacao c', 'c.id = a.id_alocacao')
            ->where('c.id_empresa', $this->session->userdata('empresa'))
            ->where("DATE_FORMAT(c.data, '%Y-%m') =", "{$busca['ano']}-{$busca['mes']}");
        if ($this->session->userdata('tipo') == 'funcionario') {
            $qb->where('c.depto', $busca['depto']);
        }
        $rows = $qb
            ->order_by('b.nome', 'asc')
            ->get('alocacao_usuarios a')
            ->result();

        $options = ['' => 'selecione...'];
        foreach ($rows as $row) {
            $options[$row->id] = $row->nome;
        }

        $data['id_bck'] = form_dropdown('id_bck', $options, '', 'class="form-control"');
        $data['id_usuario_sub1'] = form_dropdown('id_usuario_sub1', $options, '', 'class="form-control"');
        $data['id_usuario_sub2'] = form_dropdown('id_usuario_sub2', $options, '', 'class="form-control"');
        $data['id_alocado_bck'] = form_dropdown('id_alocado_bck', $options, '', 'class="form-control"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_setores()
    {
        $area = $this->input->post('area');
        $setor = $this->input->post('setor');

        $rows = $this->db
            ->select('DISTINCT(setor) AS nome', false)
            ->where('empresa', $this->session->userdata('empresa'))
            ->where('area', $area)
            ->where('CHAR_LENGTH(setor) >', 0)
            ->get('usuarios')
            ->result();

        $options = ['' => 'selecione...'];
        foreach ($rows as $row) {
            $options[$row->nome] = $row->nome;
        }

        echo form_dropdown('setor', $options, $setor, 'id="setor" class="combobox form-control"');
    }

    //--------------------------------------------------------------------

    public function editar_perfil()
    {
        $this->db->where('id', $this->uri->rsegment(3, 0));
        $funcionario = $this->db->get('usuarios')->row();

        if (empty($funcionario)) {
            redirect(site_url('ei/colaboradores'));
        }

        if (!$funcionario->hash_acesso) {
            $funcionario->hash_acesso = 'null';
        }

        $funcionario->data_admissao = datetimeFormat($funcionario->data_admissao);
        $data['row'] = $funcionario;

        $this->load->model('usuario_model', 'usuario');
        $data['status'] = $this->usuario::STATUS;
        $data['tipo_conta_bancaria'] = $this->usuario::TIPOS_CONTA_BANCARIA;
        $data['pessoa_conta_bancaria'] = $this->usuario::TIPOS_PESSOA_CONTA_BANCARIA;

        $areas = $this->db
            ->select('DISTINCT(area) AS nome')
            ->where('empresa', $this->session->userdata('empresa'))
            ->where('depto', $funcionario->depto)
            ->where('CHAR_LENGTH(area) >', 0)
            ->get('usuarios')
            ->result();

        $data['area'] = ['' => 'digite ou selecione...'];
        foreach ($areas as $area) {
            $data['area'][$area->nome] = $area->nome;
        }

        $setores = $this->db
            ->select('DISTINCT(setor) AS nome')
            ->where('empresa', $this->session->userdata('empresa'))
            ->where('depto', $funcionario->depto)
            ->where('area', $funcionario->area)
            ->where('CHAR_LENGTH(setor) >', 0)
            ->get('usuarios')
            ->result();

        $data['setor'] = ['' => 'digite ou selecione...'];
        foreach ($setores as $setor) {
            $data['setor'][$setor->nome] = $setor->nome;
        }

        $contratos = $this->db
            ->select('DISTINCT(contrato) AS nome')
            ->where('empresa', $this->session->userdata('empresa'))
            ->where('CHAR_LENGTH(contrato) >', 0)
            ->get('usuarios')
            ->result();

        $data['contrato'] = ['' => 'digite ou selecione...'];
        foreach ($contratos as $contrato) {
            $data['contrato'][$contrato->nome] = $contrato->nome;
        }

        $this->load->view('ei/coladorador', $data);
    }

    //--------------------------------------------------------------------

    public function ajax_save()
    {
        $post = $this->input->post();
        $mes_ano = strtotime($post['ano'] . '-' . $post['mes']);
        $post['data'] = date('Y-m-d', $mes_ano);
        unset($post['id'], $post['mes'], $post['ano']);

        $row = $this->db
            ->select('depto, area, setor, cargo, funcao, contrato')
            ->where('id', $post['id_usuario'])
            ->get('usuarios')
            ->row_array();

        $data = array_merge($post, $row);
        $sql = "SELECT s.* 
                FROM (SELECT a.* 
                      FROM alocacao_postos a 
                      WHERE a.id_usuario = '{$data['id_usuario']}' 
                      ORDER BY a.data DESC 
                      LIMIT 1) s 
                WHERE s.depto = '{$data['depto']}' AND 
                      s.area = '{$data['area']}' AND 
                      s.setor = '{$data['setor']}' AND 
                      s.cargo = '{$data['cargo']}' AND 
                      s.funcao = '{$data['funcao']}' AND 
                      s.contrato = '{$data['contrato']}' AND 
                      s.total_dias_mensais = '{$data['total_dias_mensais']}' AND 
                      s.total_horas_diarias = '{$data['total_horas_diarias']}' AND 
                      s.valor_posto = '{$data['valor_posto']}' AND 
                      s.valor_dia = '{$data['valor_dia']}' AND 
                      s.valor_hora = '{$data['valor_hora']}'";
        $count = $this->db->query($sql)->num_rows();

        $row2 = $this->db
            ->select('id')
            ->where('id_empresa', $this->session->userdata('empresa'))
            ->where("DATE_FORMAT(data, '%Y-%m') =", date('Y-m', $mes_ano))
            ->get('alocacao')
            ->row();

        $data2 = [
            'id_alocacao' => $row2->id,
            'id_usuario' => $post['id_usuario'],
            'tipo_horario' => 'I',
            'nivel' => 'P',
        ];

        $this->db->trans_start();
        if ($count == 0) {
            $this->db->query($this->db->insert_string('alocacao_postos', $data));
        }
        $this->db->query($this->db->insert_string('alocacao_usuarios', $data2));
        $this->db->trans_complete();

        $status = $this->db->trans_status();

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function save_perfil()
    {
        header('Content-type: text/json');
        $this->load->helper(['date']);

        $funcionario = $this->db
            ->where('id', $this->uri->rsegment(3, 0))
            ->where_in('tipo', ['funcionario', 'selecionador'])
            ->get('usuarios')
            ->row();

        if ($funcionario->empresa != $this->session->userdata('empresa')) {
            exit(json_encode(['retorno' => 0, 'aviso' => 'Você não tem acesso a essa página!']));
        }

        $novaSenha = $this->input->post('nova_senha');
        $confirmarNovaSenha = $this->input->post('confirmar_nova_senha');

        if (strlen($novaSenha) > 0 or strlen($confirmarNovaSenha) > 0) {
            if (strlen($novaSenha) == 0) {
                exit(json_encode(['retorno' => 0, 'aviso' => 'O campo Senha é obrigatório se o campo Confirmar Senha estiver preenchido!']));
            } elseif (strlen($confirmarNovaSenha) == 0) {
                exit(json_encode(['retorno' => 0, 'aviso' => 'O campo Confirmar Senha é obrigatório se o campo Senha estiver preenchido!']));
            } else if ($novaSenha !== $confirmarNovaSenha) {
                exit(json_encode(['retorno' => 0, 'aviso' => 'Os campos Senha e Confirmar Senha não conferem!']));
            }
            $this->load->library('auth');
            $data['senha'] = $this->auth->encryptPassword($novaSenha);
        }
        $possui_apontamento_horas = $this->input->post('possui_apontamento_horas');
        if (strlen($possui_apontamento_horas) == 0) {
            $possui_apontamento_horas = null;
        }

        $data['nome'] = trim($this->input->post('nome'));
        if (strlen($data['nome']) == 0) {
            exit(json_encode(['retorno' => 0, 'aviso' => 'O nome é obrigatório.']));
        }
        $data['email'] = trim($this->input->post('email'));
        if (strlen($data['email']) == 0) {
            exit(json_encode(['retorno' => 0, 'aviso' => 'O email é obrigatório.']));
        }
        $data['area'] = $this->input->post('area');
        $data['setor'] = $this->input->post('setor');
        $data['cnpj'] = $this->input->post('cnpj');
        $data['telefone'] = $this->input->post('telefone');
        $data['contrato'] = $this->input->post('contrato');
        $data['dataeditado'] = mdate("%Y-%m-%d %H:%i:%s");
        $data['possui_apontamento_horas'] = $possui_apontamento_horas;
        $data['status'] = $this->input->post('status');
        $data['data_admissao'] = strToDate($this->input->post('data_admissao'));
        $data['nome_cartao'] = $this->input->post('nome_cartao');
        $data['valor_vt'] = $this->input->post('valor_vt');
        $data['nome_banco'] = $this->input->post('nome_banco');
        $data['agencia_bancaria'] = $this->input->post('agencia_bancaria');
        $data['conta_bancaria'] = $this->input->post('conta_bancaria');
        $data['tipo_conta_bancaria'] = $this->input->post('tipo_conta_bancaria');
        if (strlen($data['tipo_conta_bancaria']) == 0) {
            $data['tipo_conta_bancaria'] = null;
        }
        $data['operacao_conta_bancaria'] = $this->input->post('operacao_conta_bancaria');
        if (strlen($data['operacao_conta_bancaria']) == 0) {
            $data['operacao_conta_bancaria'] = null;
        }
        $data['pessoa_conta_bancaria'] = $this->input->post('pessoa_conta_bancaria');
        if (strlen($data['pessoa_conta_bancaria']) == 0) {
            $data['pessoa_conta_bancaria'] = null;
        }
        $data['pagina_inicial'] = $this->input->post('pagina_inicial');
        if (strlen($data['pagina_inicial']) == 0) {
            $data['pagina_inicial'] = null;
        }
        $data['recolher_menu'] = $this->input->post('recolher_menu');
        if (strlen($data['recolher_menu']) == 0) {
            $data['recolher_menu'] = null;
        }

        if (!empty($_FILES['assinatura_digital'])) {
            $config['upload_path'] = './imagens/usuarios/';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['file_name'] = utf8_decode($_FILES['assinatura_digital']['name']);

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('assinatura_digital')) {
                $assinatura_digital = $this->upload->data();
                $data['assinatura_digital'] = utf8_encode($assinatura_digital['file_name']);
                if ($funcionario->assinatura_digital != "avatar.jpg" && file_exists('./imagens/usuarios/' . $funcionario->assinatura_digital) && $funcionario->assinatura_digital != $data['assinatura_digital']) {
                    @unlink('./imagens/usuarios/' . $funcionario->assinatura_digital);
                }
            } else {
                exit(json_encode(['retorno' => 0, 'aviso' => $this->upload->display_errors(), 'redireciona' => 0, 'pagina' => '']));
            }
        }

        if (!$this->db->where('id', $funcionario->id)->update('usuarios', $data)) {
            exit(json_encode(['retorno' => 0, 'aviso' => 'Erro ao editar funcionário, tente novamente, se o erro persistir entre em contato com o administrador']));
        }

        echo json_encode(['retorno' => 1, 'aviso' => 'Funcionário editado com sucesso', 'redireciona' => 1, 'pagina' => site_url('ei/colaboradores')]);
    }

    //--------------------------------------------------------------------

    public function ajax_edit()
    {
        $data = $this->db
            ->select('id, nome, area, setor, contrato, status')
            ->where('empresa', $this->session->userdata('empresa'))
            ->where('id', $this->input->post('id'))
            ->get('usuarios')
            ->row();

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_delete()
    {
        $status = $this->db->delete('alocacao_usuarios', ['id' => $this->input->post('id')]);

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function pdf()
    {
        $data['empresa'] = $this->db
            ->select('foto, foto_descricao')
            ->where('id', $this->session->userdata('empresa'))
            ->get('usuarios')
            ->row();

        $get = $this->input->get();

        $qb = $this->db
            ->select("a.id, a.matricula, a.nome, DATE_FORMAT(a.data_admissao, '%d/%m/%Y') AS data_admissao", false)
            ->select("CONCAT_WS('/', a.cargo, a.funcao) AS cargo_funcao", false)
            ->where('a.empresa', $this->session->userdata('empresa'));
        if (isset($get['busca'])) {
            $qb->like('a.nome', $get['busca'])
                ->or_like('a.email', $get['busca']);
        }
        if (isset($get['pdi'])) {
            $qb->where_in('a.id', "(SELECT d.id_usuario FROM pdi d WHERE d.status = {$get['pdi']})");
        }
        if (isset($get['status'])) {
            $qb->where('a.status', $get['status']);
        }
        if (isset($get['depto'])) {
            $qb->where('a.depto', $get['depto']);
        }
        if (isset($get['area'])) {
            $qb->where('a.area', $get['area']);
        }
        if (isset($get['setor'])) {
            $qb->where('a.setor', $get['setor']);
        }
        if (isset($get['cargo'])) {
            $qb->where('a.cargo', $get['cargo']);
        }
        if (isset($get['funcao'])) {
            $qb->where('a.funcao', $get['funcao']);
        }
        if (isset($get['contrato'])) {
            $qb->where('a.contrato', $get['contrato']);
        }
        $data['colaboradores'] = $qb
            ->order_by('a.nome', 'asc')
            ->get('usuarios a')->result();

        $this->load->library('m_pdf');

        $stylesheet = '#table thead th { font-size: 12px; padding: 4px; background-color: #f5f5f5;} ';
        $stylesheet .= '#table tbody td { font-size: 12px; padding: 4px; vertical-align: top; } ';

        $this->m_pdf->pdf->writeHTML($stylesheet, 1);
        $this->m_pdf->pdf->writeHTML($this->load->view('ei/colaboradores_pdf', $data, true));

        $this->m_pdf->pdf->Output('Colaboradores.pdf', 'D');
    }

    //--------------------------------------------------------------------

    public function gerenciar_contratos()
    {
        $idUsuario = $this->input->post('id_usuario');

        $rowsCurriculos = $this->db
            ->select('arquivo, descricao')
            ->where('colaborador', $idUsuario)
            ->where('tipo', 15)
            ->order_by('descricao', 'asc')
            ->get('documentos')
            ->result();

        $curriculos = ['' => 'selecione...'];

        foreach ($rowsCurriculos as $rowCurriculo) {
            $curriculos[convert_accented_characters($rowCurriculo->arquivo)] = $rowCurriculo->descricao;
        }

        $rowsContratos = $this->db
            ->select('arquivo, descricao')
            ->where('colaborador', $idUsuario)
            ->where('tipo', 16)
            ->order_by('descricao', 'asc')
            ->get('documentos')
            ->result();

        $contratos = ['' => 'selecione...'];

        foreach ($rowsContratos as $rowContrato) {
            $contratos[convert_accented_characters($rowContrato->arquivo)] = $rowContrato->descricao;
        }

        $data = [
            'curriculos' => form_dropdown('', $curriculos, ''),
            'contratos' => form_dropdown('', $contratos, ''),
        ];

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function salvar_contrato()
    {
        if ($this->session->userdata('tipo') != 'empresa') {
            redirect(site_url('ei/colaboradores'));
        }

        $data = $this->input->post();

        $data['datacadastro'] = date('Y-m-d H:i:s');
        $data['usuario'] = $this->session->userdata('id');

        if ($data['colaborador'] < 1) {
            exit(json_encode(['retorno' => 0, 'aviso' => 'Erro ao salvar arquivo, id do colaborador não identificado ', 'redireciona' => 0]));
        }

        if (!empty($_FILES['arquivo'])) {
            $config['upload_path'] = './arquivos/documentos/colaborador/';
            $config['allowed_types'] = 'pdf';
            $config['max_size'] = '102400';

            $this->load->library('upload', $config);
            $_FILES['arquivo']['name'] = utf8_encode($_FILES['arquivo']['name']);

            if ($this->upload->do_upload('arquivo') == false) {
                exit(json_encode(['erro' => 'Erro no upload do arquivo: ' . $this->upload->display_errors()]));
            }

            $foto = $this->upload->data();
            $data['arquivo'] = $foto['file_name'];

            if ($foto['file_ext'] === '.doc' || $foto['file_ext'] === '.docx') {
                shell_exec("unoconv -f pdf " . $config['upload_path'] . $foto['file_name']);
                $data['arquivo'] = $foto['raw_name'] . ".pdf";
                unlink($config['upload_path'] . $foto['file_name']);
            }

            $this->db->trans_start();
            $this->db->insert('documentos', $data);
            $this->db->trans_complete();

            if ($this->db->trans_status() == false) {
                exit(json_encode(['erro' => 'Erro ao salvar o arquivo.']));
            }
        }

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    public function excluir_contrato()
    {
        $urlArquivo = './arquivos/documentos/colaborador/' . $this->input->post('arquivo');

        if (!is_file($urlArquivo)) {
            exit(json_encode(['erro' => 'Arquivo não encontrado.']));
        }

        if (!unlink($urlArquivo)) {
            exit(json_encode(['erro' => 'Não foi possível excluir o arquivo.']));
        }

        echo json_encode(['status' => true]);
    }

}
