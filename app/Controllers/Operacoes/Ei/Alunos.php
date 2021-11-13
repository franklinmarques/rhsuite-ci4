<?php

namespace App\Controllers\Ei;

use App\Controllers\BaseController;

class Alunos extends BaseController
{

    public function index()
    {
        $this->gerenciar();
    }

    //--------------------------------------------------------------------

    public function gerenciar(string $idEscola = null)
    {
        $qtde_escolas = $this->db
            ->get_where('ei_escolas', ['id' => $idEscola])
            ->num_rows();

        if ($idEscola and !$qtde_escolas) {
            redirect(site_url('home'));
        }

        $empresa = $this->session->userdata('empresa');
        $id_usuario = $this->session->userdata('id');

        $data = [];

        $data['status'] = ['' => 'Todos'];
        $sqlStatus = "SELECT DISTINCT(a.status) AS id,
                             CASE a.status
                                  WHEN 'A' THEN 'Ativos'
                                  WHEN 'I' THEN 'Inativos'
                                  WHEN 'N' THEN 'Não frequentes'
                                  WHEN 'F' THEN 'Afastados'
                                  END AS nome
                       FROM ei_alunos a
                       INNER JOIN ei_escolas b ON b.id = a.id_escola
                       INNER JOIN ei_diretorias d ON d.id = b.id_diretoria
                       WHERE d.id_empresa = '{$empresa}'";
        if ($idEscola) {
            $sqlStatus .= " AND a.id_escola = {$idEscola}";
        }
        $statusGroup = $this->db->query($sqlStatus)->result();
        foreach ($statusGroup as $status) {
            $data['status'][$status->id] = $status->nome;
        }
        $data['cursos'] = ['' => 'Todos'];
        $data['depto'] = [];

        $qb = $this->db
            ->select('DISTINCT(a.depto) AS nome', false)
            ->join('ei_escolas b', 'b.id_diretoria = a.id', 'left')
            ->join('ei_supervisores c', 'c.id_escola = b.id', 'left')
            ->where('a.id_empresa', $empresa);
        if ($idEscola) {
            $qb->where('b.id', $idEscola);
        } elseif (in_array($this->session->userdata('nivel'), [4, 11])) {
            $qb->where('c.id_supervisor', $id_usuario);
        } else {
            $data['depto'] = ['' => 'Todos'];
        }
        $deptos = $qb
            ->group_by('a.id')
            ->order_by('a.depto', 'asc')
            ->get('ei_diretorias a')
            ->result();

        foreach ($deptos as $depto) {
            $data['depto'][$depto->nome] = $depto->nome;
        }

        $data['diretoria'] = [];
        $data['id_diretoria'] = [];

        $qb = $this->db
            ->select('a.id, a.nome')
            ->join('ei_escolas b', 'b.id_diretoria = a.id', 'left')
            ->join('ei_supervisores c', 'c.id_escola = b.id', 'left')
            ->where('a.id_empresa', $empresa);
        if ($idEscola) {
            $qb->where('b.id', $idEscola);
        } elseif (in_array($this->session->userdata('nivel'), [4, 11])) {
            $qb->where('c.id_supervisor', $id_usuario);
        }
        $diretorias_disponiveis = $qb
            ->group_by('a.id')
            ->order_by('a.nome', 'asc')
            ->get('ei_diretorias a')
            ->result();

        $data['diretoria'] = ['' => 'Todas'];
        $data['id_diretoria'] = ['' => 'selecione...'];
        foreach ($diretorias_disponiveis as $diretoria_disponivel) {
            $data['diretoria'][$diretoria_disponivel->id] = $diretoria_disponivel->nome;
            $data['id_diretoria'][$diretoria_disponivel->id] = $diretoria_disponivel->nome;
        }

        $data['escola'] = [];
        $data['id_escola'] = [];

        $qb = $this->db
            ->select('a.id, a.nome, a.municipio')
            ->join('ei_diretorias b', 'b.id = a.id_diretoria')
            ->join('ei_supervisores c', 'c.id_escola = a.id', 'left')
            ->where('b.id_empresa', $empresa);
        if (in_array($this->session->userdata('nivel'), [4, 11])) {
            $qb->where('c.id_supervisor', $id_usuario);
        }
        if ($idEscola) {
            $qb->where('a.id', $idEscola);
        } else {
            $data['escola'] = ['' => 'Todas'];
            $data['id_escola'] = ['' => 'selecione...'];
        }
        $escolas_disponiveis = $qb
            ->group_by('a.id')
            ->order_by('a.nome', 'asc')
            ->get('ei_escolas a')
            ->result();

        foreach ($escolas_disponiveis as $escola_disponivel) {
            $data['escola'][$escola_disponivel->id] = $escola_disponivel->nome;
            $data['id_escola'][$escola_disponivel->id] = $escola_disponivel->nome;
        }
        $data['municipio'] = ['' => 'selecione...'] + array_filter(array_column($escolas_disponiveis, 'municipio', 'municipio'));

        $cursos = $this->db
            ->select('id, nome')
            ->order_by('nome', 'asc')
            ->get('ei_cursos')
            ->result();

        $cursos = array_column($cursos, 'nome', 'id');
        $data['curso'] = ['' => 'Todos'] + $cursos;
        $data['cursos'] = ['' => 'selecione...'] + $cursos;

        $alunos = $this->db
            ->select('nome')
            ->order_by('nome', 'asc')
            ->get('ei_alunos')
            ->result();

        $data['alunos'] = ['' => 'Digite ou selecione...'] + array_column($alunos, 'nome', 'nome');

        $this->load->view('ei/alunos', $data);
    }

    //--------------------------------------------------------------------

    public function atualizar_filtro()
    {
        $empresa = $this->session->userdata('empresa');
        $busca = $this->input->post('busca');
        $filtro = [];

        $qb = $this->db
            ->select('a.id, a.nome')
            ->join('ei_diretorias c', 'c.id = a.id_diretoria')
            ->join('ei_contratos b', 'b.id_cliente = c.id')
            ->where('c.id_empresa', $empresa);
        if ($busca['diretoria']) {
            $qb->where('c.id', $busca['diretoria']);
        }
        $escolas = $qb
            ->group_by('a.id')
            ->order_by('a.nome', 'asc')
            ->get('ei_escolas a')
            ->result();

        $filtro['escola'] = ['' => 'Todas'] + array_column($escolas, 'nome', 'id');;

        $sqlStatus = "SELECT a.status,
                             CASE a.status 
                                  WHEN 'A' THEN 'Ativo'
                                  WHEN 'I' THEN 'Inativo'
                                  WHEN 'N' THEN 'Não frequentando'
                                  WHEN 'F' THEN 'Afastado' END AS nome_status
                      FROM ei_alunos a
                      INNER JOIN ei_escolas b
                                 ON b.id = a.id_escola
                      INNER JOIN ei_diretorias c
                                 ON c.id = b.id_diretoria
                      WHERE (b.id = '{$busca['escola']}' OR CHAR_LENGTH('{$busca['escola']}') = 0)
                            AND (c.id = '{$busca['diretoria']}' OR CHAR_LENGTH('{$busca['diretoria']}') = 0)
                      GROUP BY a.status";
        $status = $this->db->query($sqlStatus)->result();
        $filtro['status'] = ['' => 'Todos'] + array_column($status, 'nome_status', 'status');

        $qb = $this->db
            ->select('a.id, a.nome')
            ->join('ei_alunos_cursos b', 'b.id_curso = a.id')
            ->join('ei_alunos c', 'c.id = b.id_aluno')
            ->join('ei_escolas d', 'd.id = c.id_escola')
            ->join('ei_diretorias e', 'e.id = d.id_diretoria');
        if ($busca['diretoria']) {
            $qb->where('e.id', $busca['diretoria']);
        }
        if ($busca['escola']) {
            $qb->where('d.id', $busca['escola']);
        }
        if ($busca['status']) {
            $qb->where('c.id', $busca['status']);
        }
        $cursos = $qb
            ->get('ei_cursos a')
            ->result();

        $filtro['curso'] = ['' => 'Todos'] + array_column($cursos, 'nome', 'id');

        $data['escola'] = form_dropdown('busca[escola]', $filtro['escola'], $busca['escola'], 'onchange="atualizarFiltro()" class="form-control input-sm filtro"');
        $data['status'] = form_dropdown('busca[status]', $filtro['status'], $busca['status'], 'onchange="atualizarFiltro()" class="form-control input-sm filtro"');
        $data['curso'] = form_dropdown('busca[curso]', $filtro['curso'], $busca['curso'], 'onchange="atualizarFiltro()" class="form-control input-sm filtro"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_list()
    {
        $post = $this->input->post();
        parse_str($this->input->post('busca'), $arrBusca);
        $busca = $arrBusca['busca'] ?? [];
        $id_escola = $this->input->post('id_escola');

        $sql = "SELECT s.id, 
                       s.nome,
                       s.status,
                       s.id_curso,
                       s.curso,
                       s.status_curso,
                       s.diretoria,
                       s.codigo,
                       s.escola,
                       s.id_aluno_curso
                FROM (SELECT a.id, 
                             c.nome AS diretoria,
                             b.id AS id_escola,
                             b.codigo,
                             b.nome AS escola,
                             a.nome,
                             (CASE a.status 
                                  WHEN 'A' THEN 'Ativo'
                                  WHEN 'I' THEN 'Inativo'
                                  WHEN 'N' THEN 'Não frequentando'
                                  WHEN 'F' THEN 'Afastado' END) AS status,
                             d.id AS id_aluno_curso,
                             e.id AS id_curso,
                             e.nome AS curso,
                             (CASE d.status_ativo 
                                  WHEN 1 THEN 'Ativo'
                                  WHEN 0 THEN 'Inativo' 
                                  ELSE NULL END) AS status_curso
                      FROM ei_alunos a
                      LEFT JOIN ei_alunos_cursos d ON 
                                d.id_aluno = a.id
                      LEFT JOIN ei_cursos e ON 
                                e.id = d.id_curso
                      LEFT JOIN ei_escolas b ON
                                 b.id = d.id_escola
                      LEFT JOIN ei_diretorias c ON 
                                c.id = b.id_diretoria AND 
                                c.id_empresa = {$this->session->userdata('empresa')}
                      WHERE 1";
        if ($id_escola) {
            $sql .= " AND b.id = {$id_escola}";
        }
        if (!empty($busca['diretoria'])) {
            $sql .= " AND c.id = '{$busca['diretoria']}'";
        }
        if (!empty($busca['escola'])) {
            $sql .= " AND b.id = '{$busca['escola']}'";
        }
        if (!empty($busca['status'])) {
            $sql .= " AND a.status = '{$busca['status']}'";
        }
        if (!empty($busca['curso'])) {
            $sql .= " AND e.id = '{$busca['curso']}'";
        }
        /*if (!empty($busca['curso'])) {
            $sql .= " AND c.id = '{$busca['curso']}'";
        }*/
        $sql .= ' GROUP BY a.id, d.id) s';
        $recordsTotal = $this->db->query($sql)->num_rows();

        $columns = ['s.id', 's.escola', 's.nome', 's.curso'];
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

            $row[] = $ei->id;
            $row[] = $ei->nome;
            $row[] = $ei->status;
            $row[] = '
                      <button type="button" class="btn btn-sm btn-info" onclick="edit_aluno(' . $ei->id . ')" title="Editar aluno"><i class="glyphicon glyphicon-pencil"></i> </button>
                      <button type="button" class="btn btn-sm btn-danger" onclick="delete_aluno(' . $ei->id . ')" title="Excluir aluno"><i class="glyphicon glyphicon-trash"></i> </button>
                      <button type="button" class="btn btn-sm btn-info" onclick="add_curso(' . $ei->id . ')" title="Adicionar curso"><i class="glyphicon glyphicon-plus"></i> Curso</button>
                     ';
            $row[] = $ei->id_curso;
            $row[] = $ei->curso;
            $row[] = $ei->codigo;
            $row[] = $ei->escola;
            $row[] = $ei->status_curso;
            if ($ei->id_curso) {
                $row[] = '
                          <button type="button" class="btn btn-sm btn-info" onclick="edit_curso(' . $ei->id_aluno_curso . ')" title="Editar curso"><i class="glyphicon glyphicon-pencil"></i></button>
                          <button type="button" class="btn btn-sm btn-danger" onclick="delete_curso(' . $ei->id_aluno_curso . ')" title="Excluir curso"><i class="glyphicon glyphicon-trash"></i> </button>
                         ';
            } else {
                $row[] = '
                          <button type="button" class="btn btn-sm btn-info disabled" title="Editar curso"><i class="glyphicon glyphicon-pencil"></i></button>
                          <button type="button" class="btn btn-sm btn-danger disabled" title="Excluir curso"><i class="glyphicon glyphicon-trash"></i> </button>
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

        echo json_encode($output);
    }

    //--------------------------------------------------------------------

    public function ajax_edit()
    {
        $id = $this->input->post('id');

        $data = $this->db
            ->get_where('ei_alunos', ['id' => $id])
            ->row();

        /*$cursos = $this->db
            ->get_where('ei_alunos_cursos', ['id_aluno' => $data->id])
            ->result();

        foreach ($cursos as $curso) {
            $data->{'id_curso[' . $curso->ordem . ']'} = $curso->id;
            $data->{'curso[' . $curso->ordem . ']'} = $curso->nome;
            $data->{'qtde_semestre[' . $curso->ordem . ']'} = $curso->qtde_semestre;
            $data->{'semestre_inicial[' . $curso->ordem . ']'} = $curso->semestre_inicial;
            $data->{'semestre_ativo[' . $curso->ordem . ']'} = $curso->semestre_atual;
            $data->{'status_curso[' . $curso->ordem . ']'} = $curso->status_ativo;
        }*/

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_edit_curso()
    {
        $id = $this->input->post('id');

        $data = $this->db
            ->select('a.*, b.id_diretoria, b.municipio', false)
            ->join('ei_escolas b', 'b.id = a.id_escola')
            ->get_where('ei_alunos_cursos a', ['a.id' => $id])
            ->row();

        $data->nota_geral = str_replace('.', ',', $data->nota_geral);

        $qb = $this->db
            ->select('municipio');
        if ($data->id_diretoria) {
            $qb->where('id_diretoria', $data->id_diretoria);
        }
        $rowsMunicipios = $qb
            ->where('municipio IS NOT NULL')
            ->order_by('municipio', 'asc')
            ->get('ei_escolas')
            ->result();

        $municipios = ['' => 'selecione...'] + array_column($rowsMunicipios, 'municipio', 'municipio');

        $qb = $this->db
            ->select('id, nome');
        if ($data->id_diretoria) {
            $qb->where('id_diretoria', $data->id_diretoria);
        }
        if ($data->municipio) {
            $qb->where('municipio', $data->municipio);
        }
        $rowsEscolas = $qb
            ->order_by('nome', 'asc')
            ->get('ei_escolas')
            ->result();

        $escolas = ['' => 'selecione...'] + array_column($rowsEscolas, 'nome', 'id');

        $data->municipios = form_dropdown('', $municipios, $data->municipio);
        $data->escolas = form_dropdown('', $escolas, $data->id_escola);

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function atualizar_escolas()
    {
        $municipio = $this->input->post('municipio');
        $id_diretoria = $this->input->post('id_diretoria');
        $id_escola = $this->input->post('id_escola');

        $qb = $this->db
            ->select('municipio');
        if ($id_diretoria) {
            $qb->where('id_diretoria', $id_diretoria);
        }
        $rows1 = $qb
            ->where('municipio IS NOT NULL')
            ->order_by('municipio', 'asc')
            ->get('ei_escolas')
            ->result();

        $municipios = ['' => 'selecione...'] + array_column($rows1, 'municipio', 'municipio');

        $qb = $this->db
            ->select('id, nome, municipio');
        if ($id_diretoria) {
            $qb->where('id_diretoria', $id_diretoria);
        }
        if ($municipio) {
            $qb->where('municipio', $municipio);
        }
        $rows2 = $qb
            ->order_by('nome', 'asc')
            ->get('ei_escolas')
            ->result();

        $escolas = ['' => 'selecione...'] + array_column($rows2, 'nome', 'id');

        $selected = array_key_exists($id_escola, $escolas) ? $id_escola : '';
        $data['municipios'] = form_dropdown('', $municipios, $municipio, 'id="municipio" class="form-control"');
        $data['escolas'] = form_dropdown('id_escola', $escolas, $selected, 'id="id_escola" class="form-control"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function atualizar_periodos()
    {
        $id = $this->input->post('id');

        $cursos = $this->db
            ->select('b.id, b.nome')
            ->join('ei_cursos b', 'b.id = a.id_curso')
            ->where('a.id_escola', $id)
            ->get('ei_escolas_cursos a')
            ->result();

        $cursos = ['' => 'selecione...'] + array_column($cursos, 'nome', 'id');

        $data = form_dropdown('id_curso', $cursos, '', 'id="id_curso" class="form-control"');

        echo json_encode(['cursos' => $data]);
    }

    //--------------------------------------------------------------------

    public function ajax_add()
    {
        $data = $this->input->post();

        if (empty($data['nome'])) {
            exit('O nome do(a) aluno(a) é obrigatório');
        }
        /*if (empty($data['id_escola'])) {
            exit('O campo Unidade de Ensino é obrigatório');
        }*/
        /*if (empty($data['hipotese_diagnostica'])) {
            exit('O campo Hipótese Diagnóstica é obrigatório');
        }*/

        $cursos = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $cursos[$key] = $value;
                unset($data[$key]);
            }
            if ($value === '') {
                $data[$key] = null;
            }
        }
        /*if ($data['data_matricula']) {
            $data['data_matricula'] = date("d/m/Y", strtotime(str_replace('-', '/', $data['data_matricula'])));
        }
        if ($data['data_afastamento']) {
            $data['data_afastamento'] = date("d/m/Y", strtotime(str_replace('-', '/', $data['data_afastamento'])));
        }
        if ($data['data_desligamento']) {
            $data['data_desligamento'] = date("d/m/Y", strtotime(str_replace('-', '/', $data['data_desligamento'])));
        }*/

        $status = $this->db->insert('ei_alunos', $data);

        /*if ($status) {
            $id_aluno = $this->db->insert_id();

            $data2 = [];
            foreach ($cursos as $ordem => $curso) {
                $cursos['id_aluno'] = array_fill(1, 5, $id_aluno);
                $cursos['nome'] = $cursos['curso'];
                $cursos['ordem'] = array_combine(array_keys($cursos['id_curso']), array_keys($cursos['id_curso']));
                $cursos['semestre_atual'] = $cursos['semestre_ativo'];
                for ($i = 1; $i <= 5; $i++) {
                    $cursos['status_ativo'][$i] = $cursos['status_curso'][$i] ?? null;
                }
                unset($cursos['id_curso'], $cursos['curso'], $cursos['semestre_ativo'], $cursos['status_curso']);

                $arrCursos = [];
                foreach ($cursos as $key => $values) {
                    foreach ($values as $ordem => $value) {
                        $arrCursos[$ordem][$key] = $value;
                    }
                }

                $data2 = [];
                foreach ($arrCursos as $arrCurso) {
                    if (!empty($arrCurso['nome'])) {
                        $data2[] = $arrCurso;
                    }
                }
            }
            $status = $this->db->insert_batch('ei_alunos_cursos', $data2);
        }*/

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_add_curso()
    {
        $data = $this->input->post();
        unset($data['id']);

        if (empty($data['id_escola'])) {
            exit('O campo Unidade de Ensino é obrigatório');
        }
        if (empty($data['id_curso'])) {
            exit('O campo Curso é obrigatório');
        }

        $status = $this->db->insert('ei_alunos_cursos', $data);

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_update()
    {
        $data = $this->input->post();

        $erro = '';
        if (empty($data['nome'])) {
            $erro .= "O nome do(a) aluno(a) é obrigatório\n";
        }
        /*if (empty($data['hipotese_diagnostica'])) {
            $erro .= "O campo Hipótese Diagnóstica é obrigatório\n";
        }*/
        if ($erro) {
            exit($erro);
        }

        $id = $data['id'];
        unset($data['id']);
        $cursos = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $cursos[$key] = $value;
                unset($data[$key]);
            }
            if (empty($value)) {
                $data[$key] = null;
            }
        }
        $status = $this->db->update('ei_alunos', $data, ['id' => $id]);

        /*if ($status) {
            $rowsCursos = $this->db->get_where('ei_alunos_cursos', ['id_aluno' => $id])->result();
            $cursosAluno = [];
            foreach ($rowsCursos as $rowCurso) {
                $cursosAluno[$rowCurso->ordem] = $rowCurso;
            }

            $cursos['id'] = $cursos['id_curso'];
            $cursos['id_aluno'] = array_fill(1, 5, $id);
            $cursos['nome'] = $cursos['curso'];
            $cursos['ordem'] = array_combine(array_keys($cursos['id_curso']), array_keys($cursos['id_curso']));
            $cursos['semestre_atual'] = $cursos['semestre_ativo'];
            for ($i = 1; $i <= 5; $i++) {
                $cursos['status_ativo'][$i] = $cursos['status_curso'][$i] ?? null;
            }
            unset($cursos['id_curso'], $cursos['curso'], $cursos['semestre_ativo'], $cursos['status_curso']);

            $arrCursos = [];
            foreach ($cursos as $key => $values) {
                foreach ($values as $ordem => $value) {
                    $arrCursos[$ordem][$key] = $value;
                }
            }

            foreach ($arrCursos as $ordem => $data2) {
                if (!($status !== false)) {
                    break;
                }
                if (!empty($data2['nome'])) {
                    if (!empty($cursosAluno[$ordem])) {
                        $status = $this->db->update('ei_alunos_cursos', $data2, ['id' => $cursosAluno[$ordem]->id]);
                    } else {
                        $status = $this->db->insert('ei_alunos_cursos', $data2);
                    }
                } elseif (!empty($cursosAluno[$ordem])) {
                    $status = $this->db->delete('ei_alunos_cursos', ['id' => $cursosAluno[$ordem]->id]);
                }
            }
        }*/

        /*$matriculados = $this->db
            ->select('a.id, a.id_aluno, a.escola, a.id_alocacao, a.turno')
            ->join('ei_alocacao b', "b.id = a.id_alocacao AND DATE_FORMAT(b.data, '%Y-%m') = '" . date('Y-m') . "'")
            ->where('a.id_aluno', $id)
            ->or_where('a.aluno', $data['nome'])
            ->limit(1)
            ->get('ei_matriculados a')
            ->result();

        foreach ($matriculados as $matriculado) {

            if ($status !== false) {
                $escola = $this->db
                    ->select('nome')
                    ->get_where('ei_escolas', ['id' => $data['id_escola']])
                    ->row();

                $data2 = [
                    'id_alocacao' => $matriculado->id_alocacao,
                    'id_aluno' => $id ?? $matriculado->id_aluno,
                    'aluno' => $data['nome'],
                    'escola' => $escola->nome ?? $matriculado->escola,
                    'status' => $data['status'],
                ];

                $status = $this->db->update('ei_matriculados a', $data2, ['a.id' => $matriculado->id]);
            }

        }*/

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_update_curso()
    {
        $data = $this->input->post();
        $id = $data['id'];
        unset($data['id']);

        if ((!empty($data['status_ativo'])) == false) {
            $data['status_ativo'] = null;
        }
        if (empty($data['id_escola'])) {
            exit('O campo Unidade de Ensino é obrigatório');
        }
        if (empty($data['id_curso'])) {
            exit('O campo Curso é obrigatório');
        }
        if (strlen($data['nota_geral'])) {
            $data['nota_geral'] = str_replace(',', '.', $data['nota_geral']);
        } else {
            $data['nota_geral'] = null;
        }

        $status = $this->db->update('ei_alunos_cursos', $data, ['id' => $id]);

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_delete()
    {
        $status = $this->db->delete('ei_alunos', ['id' => $this->input->post('id')]);
        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_delete_curso()
    {
        $status = $this->db->delete('ei_alunos_cursos', ['id' => $this->input->post('id')]);
        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function importar()
    {
        $this->load->view('ei/importar_alunos');
    }

    //--------------------------------------------------------------------

    public function importar_csv()
    {
        header('Content-type: text/json; charset=UTF-8');
        $this->load->helper(['date']);

        $empresa = $this->session->userdata('empresa');

        // Verifica se o arquivo foi enviado
        if (!(isset($_FILES) && !empty($_FILES) and $_FILES['arquivo']['error'] == 0)) {
            //Mensagem de erro
            exit(json_encode(['retorno' => 0, 'aviso' => 'Erro no envio do arquivo. Por favor, tente mais tarde', 'redireciona' => 0, 'pagina' => '']));
        }

        $config['upload_path'] = './arquivos/csv/';
        $config['file_name'] = utf8_decode($_FILES['arquivo']['name']);
        $config['allowed_types'] = '*';
        $config['overwrite'] = true;

        //Upload do csv
        $html = '';
        $this->load->library('upload', $config);
        if ($this->upload->do_upload('arquivo') == false) {
            exit(json_encode(['retorno' => 0, 'aviso' => $this->upload->display_errors(), 'redireciona' => 0, 'pagina' => '']));
        }
        $csv = $this->upload->data();

        //Importar o arquivo transferido para o banco de dados
        $handle = fopen($config['upload_path'] . $csv['file_name'], "r");

        $x = 0;
        $validacao = true;
        $label = ['Aluno', 'Endereço', 'Número', 'Complemento', 'Município', 'Telefone',
            'Contato', 'E-mail', 'CEP', 'Nome responsável', 'Hipótese diagnóstica',
            'Observações', 'Escola', 'Data matrícula', 'Períodos'];
        $data = [];

        $this->db->trans_begin();

        while (($row = fgetcsv($handle, 1850, ";")) !== false) {
            $x++;

            if ($x == 1) {
                if (count(array_filter($row)) == 15) {
                    $label = $row;
                }
                continue;
            }

            $row = array_pad($row, 15, '');
            if (count(array_filter($row)) == 0) {
                $html .= "Linha $x: registro n&atilde;o encontrado.<br>";
                continue;
            }

            $data['nome'] = utf8_encode($row[0]);
            $data['endereco'] = utf8_encode($row[1]);
            $data['numero'] = utf8_encode($row[2]);
            $data['complemento'] = utf8_encode($row[3]);
            $data['municipio'] = utf8_encode($row[4]);

            $telefones = explode('/', $row[5]);
            foreach ($telefones as $k => $telefone) {
                $telefones[$k] = trim($telefone);
            }
            $data['telefone'] = utf8_encode(implode('/', $telefones));
            $data['contato'] = utf8_encode($row[6]);

            $data['email'] = utf8_encode($row[7]);
            $data['cep'] = utf8_encode($row[8]);
            $data['nome_responsavel'] = utf8_encode($row[9]);
            $data['hipotese_diagnostica'] = utf8_encode($row[10]);
            $data['observacoes'] = utf8_encode($row[11]);

            $escola = $this->db
                ->select('a.id')
                ->join('ei_diretorias b', 'b.id = a.id_diretoria')
                ->where('b.id_empresa', $empresa)
                ->where('a.nome', utf8_encode($row[12]))
                ->get('ei_escolas a')
                ->row();

            if (!isset($escola->id)) {
                $html .= "Linha $x: escola \"" . utf8_encode($row[12]) . "\" n&atilde;o encontrada.<br>";
                continue;
            }
            $data['id_escola'] = $escola->id;

            $data['data_matricula'] = utf8_encode($row[13]);
            $data['periodos'] = utf8_encode($row[14]);

            $_POST = $data;

            if ($this->validaCsv($label)) {
                $data['data_matricula'] = date("Y-m-d", strtotime(str_replace('/', '-', $data['data_matricula'])));
                $data['periodo_manha'] = preg_match('/(M|I)/i', $row[14]);
                $data['periodo_tarde'] = preg_match('/(T|I)/i', $row[14]);
                $data['periodo_noite'] = preg_match('/(N|I)/i', $row[14]);
                unset($data['periodos']);
                $data['status'] = 'A';

                //Inserir informação no banco
                if ($this->db->get_where('ei_alunos', ['nome' => $data['nome'], 'id_escola' => $data['id_escola']])->num_rows() == 0) {
                    $this->db->query($this->db->insert_string('ei_alunos', $data));
                }
            } else {
                $html .= $this->form_validation->error_string("Linha $x: ");
                $validacao = false;
            }
        }

        fclose($handle);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }

        if (!($validacao and empty($html))) {
            //Mensagem de erro
            exit(json_encode(['retorno' => 0, 'aviso' => utf8_encode("Erro no registro de alguns arquivos: <br> {$html}"), 'redireciona' => 0, 'pagina' => '']));
        }

        //Mensagem de confirmação
        echo json_encode(['retorno' => 1, 'aviso' => 'Importação de alunos efetuada com sucesso', 'redireciona' => 1, 'pagina' => site_url('ei/alunos/importar')]);
    }

    //--------------------------------------------------------------------

    private function validaCsv(array $label): bool
    {
        $this->load->library('form_validation');
        $lang = [
            'required' => "A coluna %s &eacute; obrigat&oacute;ria.",
            'integer' => "A coluna %s deve conter um valor num&eacute;rico.",
            'max_length' => 'A coluna %s n&atilde;o deve conter mais de %s caracteres.',
            'valid_email' => 'A coluna %s deve conter um endereco de e-mail v&aacute;lido.',
            'is_unique' => 'A coluna %s contem dado j&aacute; cadastrado em outro aluno.',
            'is_date' => 'A coluna %s deve conter uma data v&aacue;lida.',
            'regex_match' => 'A coluna %s n&aacute;o est&aacute; no formato correto.',
        ];
        $this->form_validation->set_message($lang);

        $config = [
            [
                'field' => 'nome',
                'label' => $label[0],
                'rules' => 'required|max_length[255]',
            ],
            [
                'field' => 'endereco',
                'label' => $label[1],
                'rules' => 'max_length[255]',
            ],
            [
                'field' => 'numero',
                'label' => $label[2],
                'rules' => 'max_length[11]',
            ],
            [
                'field' => 'complemento',
                'label' => $label[3],
                'rules' => 'max_length[255]',
            ],
            [
                'field' => 'municipio',
                'label' => $label[4],
                'rules' => 'max_length[100]',
            ],
            [
                'field' => 'telefone',
                'label' => $label[5],
                'rules' => 'max_length[50]',
            ],
            [
                'field' => 'contato',
                'label' => $label[6],
                'rules' => 'max_length[255]',
            ],
            // [
            //     'field' => 'email',
            //     'label' => $label[7],
            //     'rules' => 'valid_email|is_unique[ei_alunos.email]|max_length[255]',
            // ],
            [
                'field' => 'cep',
                'label' => $label[8],
                'rules' => 'max_length[20]',
            ],
            [
                'field' => 'nome_responsavel',
                'label' => $label[9],
                'rules' => 'max_length[255]',
            ],
            [
                'field' => 'hipotese_diagnostica',
                'label' => $label[10],
                'rules' => 'required|max_length[255]',
            ],
            [
                'field' => 'id_escola',
                'label' => $label[12],
                'rules' => 'required|integer|max_length[11]',
            ],
            // [
            //     'field' => 'data_matricula',
            //     'label' => $label[13],
            //     'rules' => 'is_date',
            // ],
            [
                'field' => 'periodos',
                'label' => $label[14],
                'rules' => 'required',
            ],
        ];

        $this->form_validation->set_rules($config);

        return $this->form_validation->run();
    }

    //--------------------------------------------------------------------

    public function pdf()
    {
        $empresa = $this->session->userdata('empresa');
        $this->load->library('m_pdf');

        $stylesheet = 'table { font-size: 12px; } ';
        $stylesheet .= 'table tr th, table tr td { border: 1px solid #fff; } ';
//        $this->m_pdf->pdf->setTopMargin(60);
        $this->m_pdf->pdf->AddPage('L');
        $this->m_pdf->pdf->writeHTML($stylesheet, 1);

        $usuario = $this->db
            ->select('foto, foto_descricao')
            ->get_where('usuarios', ['id' => $empresa])
            ->row();

        $sql = "SELECT s.codigo_nome,
					   s.status,
					   s.codigo_curso,
					   s.codigo_escola,
					   s.status_curso
                FROM (SELECT CONCAT_WS(' - ', a.id, a.nome) AS codigo_nome,
                             (CASE a.status 
                                  WHEN 'A' THEN 'Ativo'
                                  WHEN 'I' THEN 'Inativo'
                                  WHEN 'N' THEN 'Não frequentando'
                                  WHEN 'F' THEN 'Afastado' END) AS status,
                             CONCAT_WS(' - ', e.id, e.nome) AS codigo_curso,
                             CONCAT_WS(' - ', b.codigo, b.nome) AS codigo_escola,
                             (CASE d.status_ativo 
                                  WHEN 1 THEN 'Ativo'
                                  WHEN 0 THEN 'Inativo' 
                                  ELSE NULL END) AS status_curso,
                             a.nome,
                             e.nome AS curso,
                             b.nome AS escola
                      FROM ei_alunos a
                      LEFT JOIN ei_alunos_cursos d ON 
                                d.id_aluno = a.id
                      LEFT JOIN ei_cursos e ON 
                                e.id = d.id_curso
                      LEFT JOIN ei_escolas b ON
                                 b.id = d.id_escola
                      LEFT JOIN ei_diretorias c ON 
                                c.id = b.id_diretoria AND 
                                c.id_empresa = {$empresa}
                      GROUP BY a.id, d.id) s 
                ORDER BY s.nome ASC, s.status ASC, s.curso ASC, s.escola ASC, s.status_curso ASC";
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
                    <h1 style="font-weight: bold;">RELAÇÃO ALUNOS x CURSOS x ESCOLAS</h1>
                </td>
            </tr>
            </tbody>
        </table>
        <br><br>';

        $table = [['Aluno', 'Status', 'Curso', 'Escola', 'Status do curso']];
        foreach ($data as $row) {
            $table[] = $row;
        }
        $this->load->library('table');

        $html = $cabecalho . $this->table->generate($table);

        $this->m_pdf->pdf->writeHTML($html);

        $this->m_pdf->pdf->Output("EI_alunos.pdf", 'D');
    }

}
