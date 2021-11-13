<?php

namespace App\Controllers\Ei;

use App\Controllers\BaseController;

class Diretorias extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        if (!in_array($this->session->userdata('nivel'), [0, 4, 7, 8, 9])) {
            redirect(site_url('home'));
        }
    }

    //--------------------------------------------------------------------

    public function index()
    {
        $empresa = $this->session->userdata('empresa');

        $data = [];

        $deptos_disponiveis = $this->db
            ->select('DISTINCT(depto) AS nome', false)
            ->where('empresa', $empresa)
            ->where('CHAR_LENGTH(depto) >', 0)
            ->order_by('depto', 'asc')
            ->get('usuarios')
            ->result();

        $data['deptos_disponiveis'] = ['' => 'selecione...'];
        foreach ($deptos_disponiveis as $depto_disponivel) {
            $data['deptos_disponiveis'][$depto_disponivel->nome] = $depto_disponivel->nome;
        }

        $data['cuidadores'] = '';
        $data['coordenadores'] = ['' => 'selecione...'];

        $cuidadores = $this->db
            ->select('DISTINCT(depto) AS nome', false)
            ->where('empresa', $empresa)
            ->where('depto', 'educação inclusiva')
            ->get('usuarios')
            ->row();

        if ($cuidadores) {
            $data['cuidadores'] = $cuidadores->nome;

            $usuarios = $this->db
                ->select('id, nome')
                ->where('empresa', $empresa)
                ->where('depto', $cuidadores->nome)
                ->order_by('nome', 'asc')
                ->get('usuarios')
                ->result();

            foreach ($usuarios as $usuario) {
                $data['coordenadores'][$usuario->id] = $usuario->nome;
            }
        }

        $deptos = $this->db
            ->select('DISTINCT(depto) AS nome', false)
            ->where('id_empresa', $empresa)
            ->order_by('depto', 'asc')
            ->get('ei_diretorias')
            ->result();

        $data['depto'] = ['' => 'Todos'];
        foreach ($deptos as $depto) {
            $data['depto'][$depto->nome] = $depto->nome;
        }

        $diretorias = $this->db
            ->select('DISTINCT(nome) AS nome', false)
            ->where('id_empresa', $empresa)
            ->order_by('nome', 'asc')
            ->get('ei_diretorias')
            ->result();

        $data['diretoria'] = ['' => 'Todas'];
        foreach ($diretorias as $diretoria) {
            $data['diretoria'][$diretoria->nome] = $diretoria->nome;
        }

        $coordenadores = $this->db
            ->select('a.id_coordenador AS id, b.nome', false)
            ->join('usuarios b', 'b.id = a.id_coordenador')
            ->where('a.id_empresa', $empresa)
            ->order_by('b.nome', 'asc')
            ->group_by('a.id_coordenador')
            ->get('ei_diretorias a')
            ->result();

        $data['coordenador'] = ['' => 'Todos'];
        foreach ($coordenadores as $coordenador) {
            $data['coordenador'][$coordenador->id] = $coordenador->nome;
        }

        $contratos = $this->db
            ->select('DISTINCT(a.contrato) AS nome', false)
            ->join('ei_diretorias b', 'b.id = a.id_cliente')
            ->where('b.id_empresa', $empresa)
            ->order_by('a.contrato', 'asc')
            ->get('ei_contratos a')
            ->result();

        $data['contrato'] = ['' => 'Todos'];
        foreach ($contratos as $contrato) {
            $data['contrato'][$contrato->nome] = $contrato->nome;
        }

        $this->load->view('ei/diretorias', $data);
    }

    //--------------------------------------------------------------------

    public function atualizar_filtro()
    {
        $empresa = $this->session->userdata('empresa');
        $busca = $this->input->post('busca');
        $filtro = [];

        $qb = $this->db
            ->select('DISTINCT(nome) AS nome', false)
            ->where('id_empresa', $empresa);
        if ($busca['depto']) {
            $qb->where('depto', $busca['depto']);
        }
        $diretorias = $qb
            ->order_by('nome', 'asc')
            ->get('ei_diretorias')
            ->result();

        $filtro['diretoria'] = ['' => 'Todas'];
        foreach ($diretorias as $diretoria) {
            $filtro['diretoria'][$diretoria->nome] = $diretoria->nome;
        }

        $qb = $this->db
            ->select('a.id_coordenador AS id, b.nome', false)
            ->join('usuarios b', 'b.id = a.id_coordenador')
            ->where('a.id_empresa', $empresa);
        if ($busca['depto']) {
            $qb->where('a.depto', $busca['depto']);
        }
        if ($busca['diretoria']) {
            $qb->where('a.nome', $busca['diretoria']);
        }
        $coordenadores = $qb
            ->order_by('b.nome', 'asc')
            ->group_by('a.id_coordenador')
            ->get('ei_diretorias a')
            ->result();

        $filtro['coordenador'] = ['' => 'Todos'];
        foreach ($coordenadores as $coordenador) {
            $filtro['coordenador'][$coordenador->id] = $coordenador->nome;
        }

        $qb = $this->db
            ->select('DISTINCT(a.contrato) AS nome', false)
            ->join('ei_diretorias b', 'b.id = a.id_cliente')
            ->where('b.id_empresa', $empresa);
        if ($busca['depto']) {
            $qb->where('b.depto', $busca['depto']);
        }
        if ($busca['diretoria']) {
            $qb->where('b.nome', $busca['diretoria']);
        }
        if ($busca['coordenador']) {
            $qb->where('b.id_coordenador', $busca['coordenador']);
        }
        $contratos = $qb
            ->order_by('a.contrato', 'asc')
            ->get('ei_contratos a')
            ->result();

        $filtro['contrato'] = ['' => 'Todos'];
        foreach ($contratos as $contrato) {
            $filtro['contrato'][$contrato->nome] = $contrato->nome;
        }

        $data['diretoria'] = form_dropdown('diretoria', $filtro['diretoria'], $busca['diretoria'], 'onchange="atualizarFiltro()" class="form-control input-sm"');
        $data['coordenador'] = form_dropdown('coordenador', $filtro['coordenador'], $busca['coordenador'], 'onchange="atualizarFiltro()" class="form-control input-sm"');
        $data['contrato'] = form_dropdown('contrato', $filtro['contrato'], $busca['contrato'], 'onchange="atualizarFiltro()" class="form-control input-sm"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_list()
    {
        $post = $this->input->post();
        parse_str($this->input->post('busca'), $arrBusca);
        $busca = $arrBusca['busca'] ?? [];

        $sql = "SELECT s.id,
                       s.nome,
                       s.contrato,
                       s.id_contrato,
                       s.id_valor_faturamento,
                       s.ano_semestre,
                       s.id_funcao,
                       s.funcao,
                       s.qtde_horas,
                       s.valor,
                       s.valor_pagamento,
                       s.valor2,
                       s.valor_pagamento2
                FROM (SELECT a.id,
                             a.nome,
                             d.contrato,
                             d.id AS id_contrato,
                             e.id AS id_valor_faturamento,
                             CONCAT(e.ano, '/', e.semestre) AS ano_semestre,
                             f.id AS id_funcao,
                             f.nome AS funcao,
                             FORMAT(e.qtde_horas, 2, 'de_DE') AS qtde_horas,
                             FORMAT(e.valor, 2, 'de_DE') AS valor,
                             FORMAT(e.valor_pagamento, 2, 'de_DE') AS valor_pagamento,
                             FORMAT(e.valor2, 2, 'de_DE') AS valor2,
                             FORMAT(e.valor_pagamento2, 2, 'de_DE') AS valor_pagamento2
                      FROM ei_diretorias a
                      INNER JOIN usuarios b ON 
                                 b.id = a.id_empresa 
                      LEFT JOIN usuarios c ON
                                c.id = a.id_coordenador
                      LEFT JOIN ei_contratos d ON 
                                d.id_cliente = a.id
                      LEFT JOIN ei_valores_faturamento e ON 
                                e.id_contrato = d.id
                      LEFT JOIN empresa_funcoes f ON 
                                f.id = e.id_funcao
                      WHERE a.id_empresa = {$this->session->userdata('empresa')}";
        if (!empty($busca['depto'])) {
            $sql .= " AND a.depto = '{$busca['depto']}'";
        }
        if (!empty($busca['diretoria'])) {
            $sql .= " AND a.nome = '{$busca['diretoria']}'";
        }
        if (!empty($busca['coordenador'])) {
            $sql .= " AND a.id_coordenador = '{$busca['coordenador']}'";
        }
        if (!empty($busca['contrato'])) {
            $sql .= " AND d.contrato = '{$busca['contrato']}'";
        }
        $sql .= ' GROUP BY a.id, d.id, e.id) s';
        $recordsTotal = $this->db->query($sql)->num_rows();

        $columns = ['s.id', 's.nome', 's.contrato'];
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
        foreach ($list as $ei) {
            $row = [];
            $row[] = $ei->nome;
            $row[] = '
                      <button type="button" class="btn btn-sm btn-info" onclick="edit_cliente(' . $ei->id . ')" title="Editar área/cliente"><i class="glyphicon glyphicon-pencil"></i> </button>
                      <button type="button" class="btn btn-sm btn-danger" onclick="delete_cliente(' . $ei->id . ')" title="Excluir área/cliente"><i class="glyphicon glyphicon-trash"></i> </button>
                      <button type="button" class="btn btn-sm btn-info" onclick="add_contrato(' . $ei->id . ')" title="Adicionar contrato"><i class="glyphicon glyphicon-plus"></i> Contrato</button>
                     ';
            $row[] = $ei->contrato;
            if ($ei->contrato) {
                $row[] = '
                          <button type="button" class="btn btn-sm btn-info" onclick="edit_contrato(' . $ei->id_contrato . ')" title="Editar contrato"><i class="glyphicon glyphicon-pencil"></i></button>
                          <button type="button" class="btn btn-sm btn-danger" onclick="delete_contrato(' . $ei->id_contrato . ')" title="Excluir contrato"><i class="glyphicon glyphicon-trash"></i> </button>
                          <button type="button" class="btn btn-sm btn-info" onclick="add_valor_faturamento(' . $ei->id_contrato . ')" title="Adicionar valor faturamento"><i class="glyphicon glyphicon-plus"></i> Valores</button>
                         ';
            } else {
                $row[] = '
                          <button type="button" class="btn btn-sm btn-info disabled" title="Editar contrato"><i class="glyphicon glyphicon-pencil"></i></button>
                          <button type="button" class="btn btn-sm btn-danger disabled" title="Excluir contrato"><i class="glyphicon glyphicon-trash"></i> </button>
                          <button type="button" class="btn btn-sm btn-info disabled" title="Adicionar valor faturamento"><i class="glyphicon glyphicon-plus"></i> Valores</button>
                         ';
            }
            $row[] = $ei->ano_semestre;
            $row[] = $ei->id_funcao;
            $row[] = $ei->funcao;
            $row[] = $ei->qtde_horas;
            $row[] = $ei->valor;
            $row[] = $ei->valor_pagamento;
            $row[] = $ei->valor2;
            $row[] = $ei->valor_pagamento2;
            if ($ei->id_valor_faturamento) {
                $row[] = '
                          <button type="button" class="btn btn-sm btn-info" onclick="edit_valor_faturamento(' . $ei->id_valor_faturamento . ')" title="Editar valor faturamento"><i class="glyphicon glyphicon-pencil"></i></button>
                          <button type="button" class="btn btn-sm btn-danger" onclick="delete_valor_faturamento(' . $ei->id_valor_faturamento . ')" title="Excluir valor faturamento"><i class="glyphicon glyphicon-trash"></i> </button>
                         ';
            } else {
                $row[] = '
                          <button type="button" class="btn btn-sm btn-info disabled" title="Editar valor faturamento"><i class="glyphicon glyphicon-pencil"></i></button>
                          <button type="button" class="btn btn-sm btn-danger disabled" title="Excluir valor faturamento"><i class="glyphicon glyphicon-trash"></i> </button>
                         ';
            }

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

    public function ajax_edit()
    {
        $data = $this->db
            ->get_where('ei_diretorias', ['id' => $this->input->post('id')])
            ->row();
        if (empty($data)) {
            die(json_encode(['erro' => 'Registro não encontrado.']));
        }

        if (preg_match('/^.*@$/', $data->senha_exclusao) === 1) {
            $data->senha_exclusao = substr($data->senha_exclusao, 0, strpos($data->senha_exclusao, '@') + 1);
        }

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_edit_contrato()
    {
        $id = $this->input->post('id');

        $data = $this->db
            ->select('id, id_cliente, contrato, indice_reajuste1, indice_reajuste2')
            ->select('indice_reajuste3, indice_reajuste4, indice_reajuste5')
            ->select('minutos_tolerancia_entrada_saida, horario_padrao_banda_morta')
            ->select("DATE_FORMAT(data_inicio, '%d/%m/%Y') AS data_inicio", false)
            ->select("DATE_FORMAT(data_termino, '%d/%m/%Y') AS data_termino", false)
            ->select("DATE_FORMAT(data_reajuste1, '%d/%m/%Y') AS data_reajuste1", false)
            ->select("DATE_FORMAT(data_reajuste1, '%d/%m/%Y') AS data_reajuste1", false)
            ->select("DATE_FORMAT(data_reajuste2, '%d/%m/%Y') AS data_reajuste2", false)
            ->select("DATE_FORMAT(data_reajuste3, '%d/%m/%Y') AS data_reajuste3", false)
            ->select("DATE_FORMAT(data_reajuste4, '%d/%m/%Y') AS data_reajuste4", false)
            ->select("DATE_FORMAT(data_reajuste5, '%d/%m/%Y') AS data_reajuste5", false)
            ->where('id', $id)
            ->get('ei_contratos')
            ->row();

        if ($data->indice_reajuste1) {
            $data->indice_reajuste1 = number_format($data->indice_reajuste1, 8, ',', '');
        }
        if ($data->indice_reajuste2) {
            $data->indice_reajuste2 = number_format($data->indice_reajuste2, 8, ',', '');
        }
        if ($data->indice_reajuste3) {
            $data->indice_reajuste3 = number_format($data->indice_reajuste3, 8, ',', '');
        }
        if ($data->indice_reajuste4) {
            $data->indice_reajuste4 = number_format($data->indice_reajuste4, 8, ',', '');
        }
        if ($data->indice_reajuste5) {
            $data->indice_reajuste5 = number_format($data->indice_reajuste5, 8, ',', '');
        }

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_valores()
    {
        $id = $this->input->post('id');

        $data = $this->db
            ->select('id, contrato')
            ->where('id', $id)
            ->get('ei_contratos')
            ->row();

        $rows = $this->db
            ->select('a.id, a.nome')
            ->join('empresa_cargos b', 'b.id = a.id_cargo')
            ->where('b.id_empresa', $this->session->userdata('empresa'))
            ->order_by('a.nome', 'asc')
            ->get('empresa_funcoes a')
            ->result();

        $funcoes = ['' => 'selecione...'] + array_column($rows, 'nome', 'id');

        $data->funcoes = form_dropdown('id_funcao', $funcoes, '', 'class="form-control"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_edit_valores()
    {
        $id = $this->input->post('id');

        $data = $this->db
            ->select('a.*, b.contrato', false)
            ->join('ei_contratos b', 'b.id = a.id_contrato')
            ->where('a.id', $id)
            ->get('ei_valores_faturamento a')
            ->row();

        if ($data->qtde_horas) {
            $data->qtde_horas = number_format($data->qtde_horas, 2, ',', '.');
        }
        if ($data->valor) {
            $data->valor = number_format($data->valor, 2, ',', '.');
        }
        if ($data->valor_pagamento) {
            $data->valor_pagamento = number_format($data->valor_pagamento, 2, ',', '.');
        }
        if ($data->valor2) {
            $data->valor2 = number_format($data->valor2, 2, ',', '.');
        }
        if ($data->valor_pagamento2) {
            $data->valor_pagamento2 = number_format($data->valor_pagamento2, 2, ',', '.');
        }

        $rows = $this->db
            ->select('a.id, a.nome')
            ->join('empresa_cargos b', 'b.id = a.id_cargo')
            ->where('b.id_empresa', $this->session->userdata('empresa'))
            ->order_by('a.nome', 'asc')
            ->get('empresa_funcoes a')
            ->result();

        $funcoes = ['' => 'selecione...'] + array_column($rows, 'nome', 'id');

        $data->funcoes = form_dropdown('id_funcao', $funcoes, $data->id_funcao, 'class="form-control"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_estrutura()
    {
        $depto = $this->input->post('depto');
        $id = $this->input->post('id_coordenador');

        $usuarios = $this->db
            ->select('id, nome')
            ->where('empresa', $this->session->userdata('empresa'))
            ->where('depto', $depto)
            ->order_by('nome', 'asc')
            ->get('usuarios')
            ->result();

        $coordenadores = ['' => 'selecione...'];
        foreach ($usuarios as $usuario) {
            $coordenadores[$usuario->id] = $usuario->nome;
        }

        $data['id_coordenador'] = form_dropdown('id_coordenador', $coordenadores, $id, 'id="id_coordenador" class="form-control"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_add()
    {
        $data = $this->input->post();
        $data['id_empresa'] = $this->session->userdata('empresa');
        unset($data['id']);
        if (empty($data['alias'])) {
            $data['alias'] = null;
        }
        if (strlen($data['telefone']) == 0) {
            $data['telefone'] = null;
        }
        if (strlen($data['nome_supervisor']) == 0) {
            $data['nome_supervisor'] = null;
        }
        if (strlen($data['email_supervisor']) == 0) {
            $data['email_supervisor'] = null;
        }
        if (strlen($data['nome_coordenador']) == 0) {
            $data['nome_coordenador'] = null;
        }
        if (strlen($data['email_coordenador']) == 0) {
            $data['email_coordenador'] = null;
        }
        if (strlen($data['nome_administrativo']) == 0) {
            $data['nome_administrativo'] = null;
        }
        if (strlen($data['email_administrativo']) == 0) {
            $data['email_administrativo'] = null;
        }
        if (strlen($data['depto_cliente']) == 0) {
            $data['depto_cliente'] = null;
        }
        if (strlen($data['cargo_coordenador']) == 0) {
            $data['cargo_coordenador'] = null;
        }
        if (strlen($data['cargo_supervisor']) == 0) {
            $data['cargo_supervisor'] = null;
        }
        if (strlen($data['senha_exclusao']) > 0) {
            $data['senha_exclusao'] .= '@';
        } else {
            $data['senha_exclusao'] = null;
        }
        if (isset($_FILES['assinatura_digital_coordenador']) and $_FILES['assinatura_digital_coordenador']['error'] == 0) {
            $config['upload_path'] = './arquivos/ei/assinatura_digital/';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['file_name'] = utf8_decode($_FILES['assinatura_digital_coordenador']['name']);

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('assinatura_digital_coordenador')) {
                $foto = $this->upload->data();
                $data['assinatura_digital_coordenador'] = utf8_encode($foto['file_name']);
            }
        } else {
            $data['assinatura_digital_coordenador'] = null;
        }

        $status = $this->db->insert('ei_diretorias', $data);
        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_add_contrato()
    {
        $data = $this->input->post();
        if (strlen($data['contrato']) == 0) {
            exit(json_encode(['erro' => 'Onome do contrato é obrigatório.']));
        }
        if (empty($data['data_inicio'])) {
            exit(json_encode(['erro' => 'A data de início é obrigatória.']));
        }
        if (empty($data['data_termino'])) {
            exit(json_encode(['erro' => 'A data de término é obrigatória.']));
        }
        $data['data_inicio'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['data_inicio'])));
        $data['data_termino'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['data_termino'])));
        if ($data['data_reajuste1'] and $data['indice_reajuste1']) {
            $data['data_reajuste1'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['data_reajuste1'])));
            $data['indice_reajuste1'] = str_replace(',', '.', $data['data_reajuste1']);
        } else {
            $data['data_reajuste1'] = null;
            $data['indice_reajuste1'] = null;
        }
        if ($data['data_reajuste2'] and $data['indice_reajuste2']) {
            $data['data_reajuste2'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['data_reajuste2'])));
            $data['indice_reajuste2'] = str_replace(',', '.', $data['data_reajuste2']);
        } else {
            $data['data_reajuste2'] = null;
            $data['indice_reajuste2'] = null;
        }
        if ($data['data_reajuste3'] and $data['indice_reajuste3']) {
            $data['data_reajuste3'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['data_reajuste3'])));
            $data['indice_reajuste3'] = str_replace(',', '.', $data['data_reajuste3']);
        } else {
            $data['data_reajuste3'] = null;
            $data['indice_reajuste3'] = null;
        }
        if ($data['data_reajuste4'] and $data['indice_reajuste4']) {
            $data['data_reajuste4'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['data_reajuste4'])));
            $data['indice_reajuste4'] = str_replace(',', '.', $data['data_reajuste4']);
        } else {
            $data['data_reajuste4'] = null;
            $data['indice_reajuste4'] = null;
        }
        if ($data['data_reajuste5'] and $data['indice_reajuste5']) {
            $data['data_reajuste5'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['data_reajuste5'])));
            $data['indice_reajuste5'] = str_replace(',', '.', $data['data_reajuste5']);
        } else {
            $data['data_reajuste5'] = null;
            $data['indice_reajuste5'] = null;
        }
        if (strlen($data['minutos_tolerancia_entrada_saida']) == 0) {
            $data['minutos_tolerancia_entrada_saida'] = null;
        }
        if (!empty($data['horario_padrao_banda_morta']) == false) {
            $data['horario_padrao_banda_morta'] = null;
        }
        unset($data['id']);

        $status = $this->db->insert('ei_contratos', $data);
        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_add_valores()
    {
        $data = $this->input->post();
        if (!empty($data['qtde_horas'])) {
            $data['qtde_horas'] = str_replace(['.', ','], ['', '.'], $data['qtde_horas']);
        } else {
            $data['qtde_horas'] = null;
        }
        if (!empty($data['valor'])) {
            $data['valor'] = str_replace(['.', ','], ['', '.'], $data['valor']);
        } else {
            $data['valor'] = null;
        }
        if (!empty($data['valor_pagamento'])) {
            $data['valor_pagamento'] = str_replace(['.', ','], ['', '.'], $data['valor_pagamento']);
        } else {
            $data['valor_pagamento'] = null;
        }
        if (!empty($data['valor2'])) {
            $data['valor2'] = str_replace(['.', ','], ['', '.'], $data['valor2']);
        } else {
            $data['valor2'] = null;
        }
        if (!empty($data['valor_pagamento2'])) {
            $data['valor_pagamento2'] = str_replace(['.', ','], ['', '.'], $data['valor_pagamento2']);
        } else {
            $data['valor_pagamento2'] = null;
        }

        $funcao = $this->db
            ->select('id_cargo')
            ->get_where('empresa_funcoes', ['id' => $data['id_funcao']])
            ->row();

        $data['id_cargo'] = $funcao->id_cargo ?? null;

        $status = $this->db->insert('ei_valores_faturamento', $data);
        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_update()
    {
        $data = $this->input->post();
        $data['id_empresa'] = $this->session->userdata('empresa');
        $id = $data['id'];
        unset($data['id']);
        if (empty($data['alias'])) {
            $data['alias'] = null;
        }
        if (strlen($data['telefone']) == 0) {
            $data['telefone'] = null;
        }
        if (strlen($data['nome_supervisor']) == 0) {
            $data['nome_supervisor'] = null;
        }
        if (strlen($data['email_supervisor']) == 0) {
            $data['email_supervisor'] = null;
        }
        if (strlen($data['nome_coordenador']) == 0) {
            $data['nome_coordenador'] = null;
        }
        if (strlen($data['email_coordenador']) == 0) {
            $data['email_coordenador'] = null;
        }
        if (strlen($data['nome_administrativo']) == 0) {
            $data['nome_administrativo'] = null;
        }
        if (strlen($data['email_administrativo']) == 0) {
            $data['email_administrativo'] = null;
        }
        if (strlen($data['depto_cliente']) == 0) {
            $data['depto_cliente'] = null;
        }
        if (strlen($data['cargo_coordenador']) == 0) {
            $data['cargo_coordenador'] = null;
        }
        if (strlen($data['cargo_supervisor']) == 0) {
            $data['cargo_supervisor'] = null;
        }
        if (strlen($data['senha_exclusao']) > 0) {
            $data['senha_exclusao'] .= '@';
        } else {
            $data['senha_exclusao'] = null;
        }
        $assinatiraDigitalAnterior = $data['assinatura_digital_coordenador'] ?? null;
        if (isset($_FILES['assinatura_digital_coordenador']) and $_FILES['assinatura_digital_coordenador']['error'] == 0) {
            $config['upload_path'] = './arquivos/ei/assinatura_digital/';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['file_name'] = utf8_decode($_FILES['assinatura_digital_coordenador']['name']);

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('assinatura_digital_coordenador')) {
                $foto = $this->upload->data();
                $data['assinatura_digital_coordenador'] = utf8_encode($foto['file_name']);
            }
        } else {
            $data['assinatura_digital_coordenador'] = $this->input->post('assinatura_digital_coordenador') ?: null;
        }

        $status = $this->db->update('ei_diretorias', $data, ['id' => $id]);
        if ($status and file_exists('./arquivos/ei/assinatura_digital_coordenador/' . $assinatiraDigitalAnterior)) {
            unlink('./arquivos/ei/assinatura_digital_coordenador/' . $assinatiraDigitalAnterior);
        }
        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_update_contrato()
    {
        $data = $this->input->post();
        if (strlen($data['contrato']) == 0) {
            exit(json_encode(['erro' => 'Onome do contrato é obrigatório.']));
        }
        if (empty($data['data_inicio'])) {
            exit(json_encode(['erro' => 'A data de início é obrigatória.']));
        }
        if (empty($data['data_termino'])) {
            exit(json_encode(['erro' => 'A data de término é obrigatória.']));
        }
        $data['data_inicio'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['data_inicio'])));
        $data['data_termino'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['data_termino'])));
        if ($data['data_reajuste1'] and $data['indice_reajuste1']) {
            $data['data_reajuste1'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['data_reajuste1'])));
            $data['indice_reajuste1'] = str_replace(',', '.', $data['data_reajuste1']);
        } else {
            $data['data_reajuste1'] = null;
            $data['indice_reajuste1'] = null;
        }
        if ($data['data_reajuste2'] and $data['indice_reajuste2']) {
            $data['data_reajuste2'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['data_reajuste2'])));
            $data['indice_reajuste2'] = str_replace(',', '.', $data['data_reajuste2']);
        } else {
            $data['data_reajuste2'] = null;
            $data['indice_reajuste2'] = null;
        }
        if ($data['data_reajuste3'] and $data['indice_reajuste3']) {
            $data['data_reajuste3'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['data_reajuste3'])));
            $data['indice_reajuste3'] = str_replace(',', '.', $data['data_reajuste3']);
        } else {
            $data['data_reajuste3'] = null;
            $data['indice_reajuste3'] = null;
        }
        if ($data['data_reajuste4'] and $data['indice_reajuste4']) {
            $data['data_reajuste4'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['data_reajuste4'])));
            $data['indice_reajuste4'] = str_replace(',', '.', $data['data_reajuste4']);
        } else {
            $data['data_reajuste4'] = null;
            $data['indice_reajuste4'] = null;
        }
        if ($data['data_reajuste5'] and $data['indice_reajuste5']) {
            $data['data_reajuste5'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['data_reajuste5'])));
            $data['indice_reajuste5'] = str_replace(',', '.', $data['data_reajuste5']);
        } else {
            $data['data_reajuste5'] = null;
            $data['indice_reajuste5'] = null;
        }
        if (strlen($data['minutos_tolerancia_entrada_saida']) == 0) {
            $data['minutos_tolerancia_entrada_saida'] = null;
        }
        if (!empty($data['horario_padrao_banda_morta']) == false) {
            $data['horario_padrao_banda_morta'] = null;
        }
        $id = $data['id'];
        unset($data['id']);

        $status = $this->db->update('ei_contratos', $data, ['id' => $id]);
        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_update_valores()
    {
        $data = $this->input->post();
        $id = $data['id'];
        unset($data['id']);
        if (!empty($data['qtde_horas'])) {
            $data['qtde_horas'] = str_replace(['.', ','], ['', '.'], $data['qtde_horas']);
        } else {
            $data['qtde_horas'] = null;
        }
        if (!empty($data['valor'])) {
            $data['valor'] = str_replace(['.', ','], ['', '.'], $data['valor']);
        } else {
            $data['valor'] = null;
        }
        if (!empty($data['valor_pagamento'])) {
            $data['valor_pagamento'] = str_replace(['.', ','], ['', '.'], $data['valor_pagamento']);
        } else {
            $data['valor_pagamento'] = null;
        }
        if (!empty($data['valor2'])) {
            $data['valor2'] = str_replace(['.', ','], ['', '.'], $data['valor2']);
        } else {
            $data['valor2'] = null;
        }
        if (!empty($data['valor_pagamento2'])) {
            $data['valor_pagamento2'] = str_replace(['.', ','], ['', '.'], $data['valor_pagamento2']);
        } else {
            $data['valor_pagamento2'] = null;
        }

        $funcao = $this->db
            ->select('id_cargo')
            ->get_where('empresa_funcoes', ['id' => $data['id_funcao']])
            ->row();

        $data['id_cargo'] = $funcao->id_cargo ?? null;

        $status = $this->db->update('ei_valores_faturamento', $data, ['id' => $id]);
        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_delete()
    {
        $status = $this->db->delete('ei_diretorias', ['id' => $this->input->post('id')]);

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_delete_contrato()
    {
        $status = $this->db->delete('ei_contratos', ['id' => $this->input->post('id')]);

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_delete_valores()
    {
        $status = $this->db->delete('ei_valores_faturamento', ['id' => $this->input->post('id')]);

        echo json_encode(['status' => $status !== false]);
    }

}
