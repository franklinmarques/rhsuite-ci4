<?php

namespace App\Controllers\Ei;

use App\Controllers\BaseController;

class Supervisores extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        if (!in_array($this->session->userdata('nivel'), [0, 4, 7, 8, 9, 10])) {
            redirect(site_url('home'));
        }
    }

    //--------------------------------------------------------------------

    public function index()
    {
        $empresa = $this->session->userdata('empresa');
        $id_usuario = $this->session->userdata('id');

        $data = [];

        $semestre = $this->db
            ->select("CONCAT(ano, '/', semestre) AS ano_semestre", false)
            ->order_by('ano', 'desc')
            ->order_by('semestre', 'desc')
            ->get('ei_coordenacao')
            ->result();

        $semestre = array_column($semestre, 'ano_semestre', 'ano_semestre');
        $data['busca_anoSemestre'] = ['' => 'Todos'] + $semestre;

        $supervisores = $this->db
            ->select('a.id, b.nome')
            ->join('usuarios b', 'b.id = a.id_usuario')
            ->where('b.empresa', $empresa)
            ->group_by('b.id')
            ->order_by('b.nome', 'asc')
            ->get('ei_coordenacao a')->result();

        $supervisores = array_column($supervisores, 'nome', 'id');
        $data['busca_supervisor'] = ['' => 'Todos'] + $supervisores;

        $diretorias = $this->db
            ->select('a.id, a.nome')
            ->join('ei_escolas b', 'b.id_diretoria = a.id')
            ->join('ei_supervisores c', 'c.id_escola = a.id')
            ->join('ei_coordenacao d', 'd.id = c.id_coordenacao')
            ->where('a.id_empresa', $empresa)
            ->order_by('a.nome', 'asc')
            ->get('ei_diretorias a')
            ->result();

        $diretorias = array_column($diretorias, 'nome', 'id');
        $data['busca_diretoria'] = ['' => 'Todos'] + $diretorias;

        $escolas = $this->db
            ->select('a.id, a.nome')
            ->join('ei_diretorias b', 'b.id = a.id_diretoria')
            ->join('ei_supervisores c', 'c.id_escola = a.id')
            ->join('ei_coordenacao d', 'd.id = c.id_coordenacao')
            ->where('b.id_empresa', $empresa)
            ->order_by('a.nome', 'asc')
            ->get('ei_escolas a')
            ->result();

        $escolas = array_column($escolas, 'nome', 'id');
        $data['busca_escola'] = ['' => 'Todas'] + $escolas;

        $deptos = $this->db
            ->select('id, nome')
            ->where('id_empresa', $empresa)
            ->order_by('nome', 'asc')
            ->get('empresa_departamentos')
            ->result();

        $data['deptos'] = ['' => 'selecione...'] + array_column($deptos, 'nome', 'id');

        $areas = $this->db
            ->select('a.id, a.nome')
            ->join('empresa_departamentos b', 'b.id = a.id_departamento')
            ->where('b.id_empresa', $empresa)
            ->order_by('a.nome', 'asc')
            ->get('empresa_areas a')
            ->result();

        $data['areas'] = ['' => 'selecione...'] + array_column($areas, 'nome', 'id');

        $setores = $this->db
            ->select('a.id, a.nome')
            ->join('empresa_areas b', 'b.id = a.id_area')
            ->join('empresa_departamentos c', 'c.id = b.id_departamento')
            ->where('c.id_empresa', $empresa)
            ->order_by('a.nome', 'asc')
            ->get('empresa_setores a')
            ->result();

        $data['setores'] = ['' => 'selecione...'] + array_column($setores, 'nome', 'id');

        $supervisores = $this->db
            ->select('id, nome')
            ->where('empresa', $empresa)
            ->where('tipo', 'funcionario')
            ->where('status', 1)
            ->order_by('nome', 'asc')
            ->get('usuarios')
            ->result();

        $data['supervisores'] = ['' => 'selecione...'] + array_column($supervisores, 'nome', 'id');

        $funcoes = $this->db
            ->select('a.id, a.nome')
            ->join('empresa_cargos b', 'b.id = a.id_cargo')
            ->join('usuarios c', 'c.funcao = a.nome AND c.empresa = b.id_empresa', 'left')
            ->where('b.id_empresa', $empresa)
            ->where('c.depto', 'Educação Inclusiva')
            ->group_by('a.id')
            ->order_by('a.nome', 'asc')
            ->get('empresa_funcoes a')
            ->result();

        $data['funcoes'] = array_column($funcoes, 'nome', 'id');

        $data['diretorias'] = ['' => 'selecione...'] + $diretorias;
        $data['escolas'] = [];

        $this->load->view('ei/supervisores', $data);
    }

    //--------------------------------------------------------------------

    public function atualizar_filtro()
    {
        $empresa = $this->session->userdata('empresa');
        $busca = $this->input->post('busca');
        $filtro = [];

        $qb = $this->db
            ->select('a.id, b.nome')
            ->join('usuarios b', 'b.id = a.id_usuario')
            ->where('b.empresa', $empresa);
        if ($busca['ano_semestre']) {
            $qb->where("CONCAT(a.ano, '/', a.semestre) =", $busca['ano_semestre']);
        }
        $supervisores = $qb
            ->group_by('b.id')
            ->order_by('b.nome', 'asc')
            ->get('ei_coordenacao a')
            ->result();

        $supervisores = array_column($supervisores, 'nome', 'id');
        $filtro['supervisor'] = ['' => 'Todos'] + $supervisores;

        $qb = $this->db
            ->select('a.id, a.nome')
            ->join('ei_escolas b', 'b.id_diretoria = a.id')
            ->join('ei_supervisores c', 'c.id_escola = b.id')
            ->join('ei_coordenacao d', 'd.id = c.id_coordenacao')
            ->where('a.id_empresa', $empresa);
        if ($busca['ano_semestre']) {
            $qb->where("CONCAT(d.ano, '/', d.semestre) =", $busca['ano_semestre']);
        }
        if ($busca['supervisor']) {
            $qb->where('d.id', $busca['supervisor']);
        }
        $diretorias = $qb
            ->order_by('a.nome', 'asc')
            ->get('ei_diretorias a')
            ->result();

        $diretorias = array_column($diretorias, 'nome', 'id');
        $filtro['diretoria'] = ['' => 'Todas'] + $diretorias;

        $qb = $this->db
            ->select('a.id, a.nome')
            ->join('ei_diretorias b', 'b.id = a.id_diretoria')
            ->join('ei_supervisores c', 'c.id_escola = a.id')
            ->join('ei_coordenacao d', 'd.id = c.id_coordenacao')
            ->where('b.id_empresa', $empresa);
        if ($busca['ano_semestre']) {
            $qb->where("CONCAT(d.ano, '/', d.semestre) =", $busca['ano_semestre']);
        }
        if ($busca['supervisor']) {
            $qb->where('d.id', $busca['supervisor']);
        }
        if ($busca['diretoria']) {
            $qb->where('b.id', $busca['diretoria']);
        }
        $escolas = $qb
            ->order_by('a.nome', 'asc')
            ->get('ei_escolas a')
            ->result();

        $escolas = array_column($escolas, 'nome', 'id');
        $filtro['escola'] = ['' => 'Todas'] + $escolas;

        $data['supervisor'] = form_dropdown('busca[supervisor]', $filtro['supervisor'], $busca['supervisor'], 'onchange="atualizarFiltro()" class="form-control input-sm"');
        $data['diretoria'] = form_dropdown('busca[diretoria]', $filtro['diretoria'], $busca['diretoria'], 'onchange="atualizarFiltro()" class="form-control input-sm"');
        $data['escola'] = form_dropdown('busca[escola]', $filtro['escola'], $busca['escola'], 'onchange="atualizarFiltro()" class="form-control input-sm"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function atualizar_supervisores(array $busca = [])
    {
        $empresa = $this->session->userdata('empresa');
        $retorno = count($busca);
        if (empty($busca)) {
            $busca['depto'] = $this->input->post('depto');
            $busca['area'] = $this->input->post('area');
            $busca['setor'] = $this->input->post('setor');
            $busca['supervisor'] = $this->input->post('supervisor');
        }
        $filtro = [];

        $qb = $this->db
            ->select('a.id, a.nome')
            ->join('empresa_departamentos b', 'b.id = a.id_departamento')
            ->where('b.id_empresa', $empresa);
        if ($busca['depto']) {
            $qb->where('b.id', $busca['depto']);
        }
        $areas = $qb
            ->order_by('a.nome', 'asc')
            ->get('empresa_areas a')
            ->result();

        $filtro['areas'] = ['' => 'selecione...'] + array_column($areas, 'nome', 'id');

        $qb = $this->db
            ->select('a.id, a.nome')
            ->join('empresa_areas b', 'b.id = a.id_area')
            ->join('empresa_departamentos c', 'c.id = b.id_departamento')
            ->where('c.id_empresa', $empresa);
        if ($busca['depto']) {
            $qb->where('c.id', $busca['depto']);
        }
        if ($busca['area']) {
            $qb->where('b.id', $busca['area']);
        }
        $setores = $qb
            ->order_by('a.nome', 'asc')
            ->get('empresa_setores a')
            ->result();

        $filtro['setores'] = ['' => 'selecione...'] + array_column($setores, 'nome', 'id');

        $qb = $this->db
            ->select('a.id, a.nome')
            ->where('a.empresa', $empresa)
            ->where('a.tipo', 'funcionario')
            ->where('a.status', 1);
        if ($busca['depto']) {
            $qb->join('empresa_departamentos b', 'b.nome = a.depto')
                ->where('b.id', $busca['depto']);
        }
        if ($busca['area']) {
            $qb->join('empresa_areas c', 'c.nome = a.area')
                ->where('c.id', $busca['area']);
        }
        if ($busca['setor']) {
            $qb->join('empresa_setores d', 'd.nome = a.setor')
                ->where('d.id', $busca['setor']);
        }
        $supervisores = $qb
            ->order_by('a.nome', 'asc')
            ->get('usuarios a')
            ->result();

        $filtro['supervisores'] = ['' => 'selecione...'] + array_column($supervisores, 'nome', 'id');

        $data['area'] = form_dropdown('area', $filtro['areas'], $busca['area'], 'id="area" class="form-control"');
        $data['setor'] = form_dropdown('setor', $filtro['setores'], $busca['setor'], 'id="setor" class="form-control"');
        $data['supervisor'] = form_dropdown('id_usuario', $filtro['supervisores'], $busca['supervisor'], 'id="supervisor" class="form-control"');

        if ($retorno) {
            return $data;
        }

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function atualizar_unidades()
    {
        $empresa = $this->session->userdata('empresa');
        $id_diretoria = $this->input->post('id_diretoria');
        $escolasSelecionadas = $this->input->post('id_escolas');

        $qb = $this->db
            ->select('a.id, a.nome')
            ->join('ei_diretorias b', 'b.id = a.id_diretoria')
            ->where('b.id_empresa', $empresa);
        if ($id_diretoria) {
            $qb->where('b.id', $id_diretoria);
        }
        if ($escolasSelecionadas) {
            $qb->or_where_in('a.id', $escolasSelecionadas);
        }
        $escolas = $qb
            ->order_by('a.nome', 'asc')
            ->get('ei_escolas a')
            ->result();

        $escolas = array_column($escolas, 'nome', 'id');

        $data['escolas'] = form_multiselect('id_escola[]', $escolas, $escolasSelecionadas, 'id="id_escolas" class="form-control demo2"');

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
                       s.ano_semestre,
                       s.id_supervisor,
                       s.ordem_escola,
                       s.escola
                FROM (SELECT a.id, 
                             b.nome,
                             CONCAT(a.ano, '/', a.semestre) AS ano_semestre,
                             CONCAT_WS(' - ', d.codigo, d.nome) AS escola,
                             IF(CHAR_LENGTH(d.codigo) > 0, d.codigo, CAST(d.nome AS DECIMAL)) AS ordem_escola,
                             c.id AS id_supervisor
                      FROM ei_coordenacao a 
                      INNER JOIN usuarios b ON 
                                 b.id = a.id_usuario
                      LEFT JOIN ei_supervisores c ON 
                                c.id_coordenacao = a.id
                      LEFT JOIN ei_escolas d ON 
                                d.id = c.id_escola
                      LEFT JOIN ei_diretorias e ON 
                                e.id = d.id_diretoria
                      WHERE a.is_supervisor = 1 AND 
                            b.empresa = {$this->session->userdata('empresa')}";
        if (!empty($busca['ano_semestre'])) {
            $sql .= " AND CONCAT(a.ano, '/', a.semestre) = '{$busca['ano_semestre']}'";
        }
        if (!empty($busca['supervisor'])) {
            $sql .= " AND a.id = '{$busca['supervisor']}'";
        }
        if (!empty($busca['diretoria'])) {
            $sql .= " AND e.depto = '{$busca['diretoria']}'";
        }
        if (!empty($busca['escola'])) {
            $sql .= " AND d.id = '{$busca['escola']}'";
        }
        $sql .= ') s';
        $recordsTotal = $this->db->query($sql)->num_rows();

        $columns = ['s.id', 's.nome', 's.id_supervisor', 's.escola'];
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
            $row[] = $ei->ano_semestre;
            $row[] = '
                      <button type="button" class="btn btn-sm btn-info" onclick="edit_supervisor(' . $ei->id . ')" title="Editar supervisor"><i class="glyphicon glyphicon-pencil"></i></button>
                      <button type="button" class="btn btn-sm btn-danger" onclick="delete_supervisor(' . $ei->id . ')" title="Excluir supervisor"><i class="glyphicon glyphicon-trash"></i></button>
                      <button type="button" class="btn btn-sm btn-info" onclick="vincular_unidades(' . $ei->id . ')" title="Vincular unidades">Unidades</button>
                    ';
            $row[] = $ei->escola;

            $data[] = $row;
        }

        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data,
        );

        echo json_encode($output);
    }

    //--------------------------------------------------------------------

    public function ajax_edit()
    {
        $id = $this->input->post('id');

        $data = $this->db
            ->select('*', false)
            ->select("TIME_FORMAT(carga_horaria, '%H:%i') AS carga_horaria_1", false)
            ->where('id', $id)
            ->get('ei_coordenacao')
            ->row();

        $busca = [
            'depto' => $data->depto,
            'area' => $data->area,
            'setor' => $data->setor,
            'supervisor' => $data->id_usuario,
        ];

        $campos = $this->atualizar_supervisores($busca);

        $data->area = $campos['area'];
        $data->setor = $campos['setor'];
        $data->id_usuario = $campos['supervisor'];
        $data->carga_horaria = $data->carga_horaria_1;
        unset($data->carga_horaria_1);

        $rowFuncoes = $this->db
            ->select('a.id, a.nome, d.funcao')
            ->join('empresa_cargos b', 'b.id = a.id_cargo')
            ->join('usuarios c', 'c.funcao = a.nome AND c.empresa = b.id_empresa', 'left')
            ->join('ei_funcoes_supervisionadas d', "d.funcao = a.id AND d.id_supervisor = {$id}", 'left')
            ->where('b.id_empresa', $this->session->userdata('empresa'))
            ->where('c.depto', 'Educação Inclusiva')
            ->group_by('a.id')
            ->order_by('a.nome', 'asc')
            ->get('empresa_funcoes a')
            ->result();

        $funcoes = array_column($rowFuncoes, 'nome', 'id');
        $fucoesSelecionadas = array_filter(array_column($rowFuncoes, 'funcao')) + [];

        $data->cargos = form_multiselect('funcoes[]', $funcoes, $fucoesSelecionadas, 'id="funcoes" class="form-control demo1"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_edit_escolas()
    {
        $empresa = $this->session->userdata('empresa');
        $id = $this->input->post('id');

        $data = $this->db
            ->select('a.id, b.nome AS nome_supervisor')
            ->select("CONCAT(a.ano, '/', a.semestre) AS ano_semestre", false)
            ->join('usuarios b', 'b.id = a.id_usuario')
            ->where('a.id', $id)
            ->get('ei_coordenacao a')
            ->row_array();

        $rowsEscolas = $this->db
            ->select('a.id, c.id AS id_supervisor')
            ->select(["CONCAT_WS(' - ', a.codigo, a.nome) AS nome"], false)
            ->join('ei_diretorias b', 'b.id = a.id_diretoria')
            ->join('ei_supervisores c', "c.id_escola = a.id AND c.id_coordenacao = '{$data['id']}'", 'left')
            ->where('b.id_empresa', $empresa)
            ->order_by('IF(CHAR_LENGTH(a.codigo) > 0, a.codigo, CAST(a.nome AS DECIMAL)) ASC', null, false)
            ->get('ei_escolas a')
            ->result();

        $escolas = array_column($rowsEscolas, 'nome', 'id');
        $escolasSelecionadas = array_keys(array_filter(array_column($rowsEscolas, 'id_supervisor', 'id')));

        $data['escolas'] = form_multiselect('id_escola[]', $escolas, $escolasSelecionadas, 'id="id_escolas" class="form-control demo2"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_add()
    {
        $data = $this->input->post();
        unset($data['id']);
        if (empty($data['id_usuario'])) {
            exit(json_encode(['erro' => 'O supervisor não pode ficar em branco.']));
        } else {
            $usuario = $this->db
                ->where('id', $data['id_usuario'])
                ->get('usuarios')
                ->row();

            if (empty($usuario)) {
                exit(json_encode(['erro' => 'O supervisor não foi encontrado.']));
            } elseif (in_array($usuario->status, ['1', '3']) == false) {
                exit(json_encode(['erro' => 'O supervisor não está ativo.']));
            }
        }
        if (empty($data['depto'])) {
            exit(json_encode(['erro' => 'O departamento não pode ficar em branco.']));
        }
        if (empty($data['area'])) {
            exit(json_encode(['erro' => 'A área não pode ficar em branco.']));
        }
        if (empty($data['setor'])) {
            exit(json_encode(['erro' => 'O setor não pode ficar em branco.']));
        }
        if (strlen($data['ano']) == 0) {
            exit(json_encode(['erro' => 'O ano não pode ficar em branco.']));
        } elseif (!checkdate(1, 1, $data['ano'])) {
            exit(json_encode(['erro' => 'O ano possui formato inválido.']));
        }

        $count = $this->db
            ->where('id_usuario', $data['id_usuario'])
            ->where('ano', $data['ano'])
            ->where('semestre', $data['semestre'])
            ->get('ei_coordenacao')
            ->num_rows();

        if ($count) {
            exit(json_encode(['erro' => 'O supervisor já possui cadastro no ano e semestres selecionados.']));
        }

        $funcoes = is_array($data['funcoes']) ? $data['funcoes'] : [0];

        $empresaFuncoes = $this->db
            ->select('id, id_cargo')
            ->where_in('id', $funcoes)
            ->get('empresa_funcoes')
            ->result();

        unset($data['funcoes']);

        $coordenacao = $this->db
            ->select('saldo_acumulado_horas')
            ->where('id_usuario', $data['id_usuario'])
            ->order_by('ano', 'desc')
            ->order_by('semestre', 'desc')
            ->limit(1)
            ->get('ei_coordenacao')
            ->row();

        $data['saldo_acumulado_horas'] = $coordenacao->saldo_acumulado_horas ?? null;

        $this->db->trans_start();
        $this->db->insert('ei_coordenacao', $data);
        $idSupervisor = $this->db->insert_id();

        $data2 = [];
        foreach ($empresaFuncoes as $funcao) {
            $data2[] = [
                'id_supervisor' => $idSupervisor,
                'cargo' => $funcao->id_cargo,
                'funcao' => $funcao->id,
            ];
        }

        if ($data2) {
            $this->db->insert_batch('ei_funcoes_supervisionadas', $data2);
        }

        $this->db->trans_complete();
        $status = $this->db->trans_status();

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_update()
    {
        $data = $this->input->post();
        if (empty($data['id_usuario'])) {
            exit(json_encode(['erro' => 'O supervisor não pode ficar em branco.']));
        } else {
            $usuario = $this->db
                ->where('id', $data['id_usuario'])
                ->get('usuarios')
                ->row();

            if (empty($usuario)) {
                exit(json_encode(['erro' => 'O supervisor não foi encontrado.']));
            } elseif (in_array($usuario->status, ['1', '3']) == false) {
                exit(json_encode(['erro' => 'O supervisor não está ativo.']));
            }
        }
        if (empty($data['depto'])) {
            exit(json_encode(['erro' => 'O departamento não pode ficar em branco.']));
        }
        if (empty($data['area'])) {
            exit(json_encode(['erro' => 'A área não pode ficar em branco.']));
        }
        if (empty($data['setor'])) {
            exit(json_encode(['erro' => 'O setor não pode ficar em branco.']));
        }
        if (strlen($data['ano']) == 0) {
            exit(json_encode(['erro' => 'O ano não pode ficar em branco.']));
        } elseif (!checkdate(1, 1, $data['ano'])) {
            exit(json_encode(['erro' => 'O ano possui formato inválido.']));
        }

        $count = $this->db
            ->where('id !=', $data['id'])
            ->where('id_usuario', $data['id_usuario'])
            ->where('ano', $data['ano'])
            ->where('semestre', $data['semestre'])
            ->get('ei_coordenacao')
            ->num_rows();

        if ($count) {
            exit(json_encode(['erro' => 'O supervisor já possui cadastro no ano e semestres selecionados.']));
        }

        $id = $this->input->post('id');

        $funcoesSupervisionadas = $this->db
            ->select('funcao')
            ->where('id_supervisor', $id)
            ->get('ei_funcoes_supervisionadas')
            ->result();

        $funcoesExistentes = array_column($funcoesSupervisionadas, 'funcao');

        $funcoesNovas = is_array($data['funcoes']) ? array_diff($data['funcoes'], $funcoesExistentes) + [0] : [0];
        unset($data['id'], $data['funcoes']);

        $empresaFuncoes = $this->db
            ->select('id, id_cargo')
            ->where_in('id', $funcoesNovas)
            ->get('empresa_funcoes')
            ->result();

        $this->db->trans_start();
        $this->db->update('ei_coordenacao', $data, ['id' => $id]);

        $this->db
            ->where('id_supervisor', $id)
            ->where_not_in('funcao', $funcoesExistentes + [0])
            ->delete('ei_funcoes_supervisionadas');

        $data2 = [];
        foreach ($empresaFuncoes as $funcao) {
            $data2[] = [
                'id_supervisor' => $id,
                'cargo' => $funcao->id_cargo,
                'funcao' => $funcao->id,
            ];
        }

        if ($data2) {
            $this->db->insert_batch('ei_funcoes_supervisionadas', $data2);
        }

        $this->db->trans_complete();
        $status = $this->db->trans_status();

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function salvar_escolas()
    {
        $id_coordenacao = $this->input->post('id_coordenacao');
        $id_escolas = $this->input->post('id_escolas');

        $usuario = $this->db
            ->select('b.id')
            ->join('usuarios b', 'b.id = a.id_usuario')
            ->where('a.id', $id_coordenacao)
            ->get('ei_coordenacao a')
            ->row();

        $data = [
            'id_coordenacao' => $id_coordenacao,
            'id_supervisor' => $usuario->id,
        ];

        $this->db->trans_start();

        $this->db
            ->where('id_coordenacao', $id_coordenacao)
            ->where_not_in('id_escola', $id_escolas)
            ->delete('ei_supervisores');

        $escolas = $this->db
            ->select('id, id_escola')
            ->where('id_coordenacao', $id_coordenacao)
            ->get('ei_supervisores')
            ->result();

        $escolas = array_column($escolas, 'id_escola', 'id');

        foreach ($id_escolas as $id => $id_escola) {
            $data['id_escola'] = $id_escola;
            if (in_array($id_escola, $escolas)) {
                $this->db->update('ei_supervisores', $data, ['id' => $id]);
            } else {
                $this->db->insert('ei_supervisores', $data);
            }
        }

        $this->db->trans_complete();
        $status = $this->db->trans_status();

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_delete()
    {
        $status = $this->db->delete('ei_coordenacao', ['id' => $this->input->post('id')]);
        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_delete_escola()
    {
        $status = $this->db->delete('ei_supervisores', ['id' => $this->input->post('id')]);
        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function pdf()
    {
        $empresa = $this->session->userdata('empresa');
        $this->load->library('m_pdf');

        $stylesheet = 'table { font-size: 12px; } ';
        $stylesheet .= 'table tr th, table tr td { border: 1px solid #fff; } ';
        $this->m_pdf->pdf->AddPage('L');
        $this->m_pdf->pdf->writeHTML($stylesheet, 1);

        $usuario = $this->db
            ->select('foto, foto_descricao')
            ->get_where('usuarios', ['id' => $empresa])
            ->row();

        $sql = "SELECT b.nome,
                             CONCAT(a.ano, '/', a.semestre) AS ano_semestre,
                             d.nome AS escola,
                             GROUP_CONCAT(h.nome ORDER BY h.nome SEPARATOR ', ') AS funcao
                      FROM ei_coordenacao a 
                      INNER JOIN usuarios b ON 
                                 b.id = a.id_usuario
                      LEFT JOIN ei_supervisores c ON 
                                c.id_coordenacao = a.id
                      LEFT JOIN ei_escolas d ON 
                                d.id = c.id_escola
                      LEFT JOIN ei_diretorias e ON 
                                e.id = d.id_diretoria
                      LEFT JOIN ei_funcoes_supervisionadas f ON 
                                f.id_supervisor = a.id
                      LEFT JOIN empresa_funcoes h ON h.id = f.funcao
                      WHERE a.is_supervisor = 1 AND 
                            b.empresa = {$empresa}
                      GROUP BY a.id, d.id 
                      ORDER BY b.nome ASC, a.ano ASC, a.semestre ASC";
        $data = $this->db->query($sql)->result_array();

        $cabecalho = '<table style="width: 100%;">
            <thead>
            <tr>
                <td>
                    <img src="' . base_url('imagens/usuarios/' . $usuario->foto) . '" align="left"
                         style="height: auto; width: auto; max-height: 60px; max-width:94px; vertical-align: middle; padding: 0 10px 5px 0;">
                </td>
                <td style="vertical-align: top; width: 100%;">
                    <p>
                        <img src="' . base_url('imagens/usuarios/' . $usuario->foto_descricao) . '" align="left"
                             style="height: auto; width: auto; max-height: 92px; max-width: 508px; vertical-align: middle; padding: 0 10px 5px 5px;">
                    </p>
                </td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="2" style="padding-bottom: 12px;  text-align: center; border-top: 5px solid #ddd; border-bottom: 2px solid #ddd; padding:5px;">
                    <h1 style="font-weight: bold;">VÍNCULO - SUPERVISORES x UNIDADES DE ENSINO</h1>
                </td>
            </tr>
            </tbody>
        </table>
        <br><br>';

        $table = [['Supervisor', 'Ano/semestre', 'Unidade de Ensino', 'Funções associadas ao supervisor']];
        foreach ($data as $row) {
            $table[] = $row;
        }
        $this->load->library('table');

        $html = $cabecalho . $this->table->generate($table);

        $this->m_pdf->pdf->writeHTML($html);

        $this->m_pdf->pdf->Output("EI_supervisores.pdf", 'D');
    }

}
