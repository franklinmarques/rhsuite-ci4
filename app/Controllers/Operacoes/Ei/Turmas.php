<?php

namespace App\Controllers\Ei;

use App\Controllers\BaseController;

class Turmas extends BaseController
{

    public function index()
    {
        $this->gerenciar($this->uri->rsegment(3, 0));
    }

    //--------------------------------------------------------------------

    public function gerenciar(string $idSemestre = null)
    {
        $empresa = $this->session->userdata('empresa');

        $diretorias = $this->db
            ->select('id, nome')
            ->where('id_empresa', $empresa)
            ->get('ei_diretorias')
            ->result();

        $data['diretorias'] = ['' => 'Todos'] + array_column($diretorias, 'nome', 'id');

        $contratos = $this->db
            ->select('a.id, a.contrato')
            ->join('ei_diretorias b', 'b.id = a.id_cliente')
            ->where('b.id_empresa', $empresa)
            ->get('ei_contratos a')
            ->result();

        $data['contratos'] = ['' => 'Todos'] + array_column($contratos, 'nome', 'id');

        $escolas = $this->db
            ->select('a.id, a.nome')
            ->join('ei_diretorias b', 'b.id = a.id_diretoria')
            ->where('b.id_empresa', $empresa)
            ->get('ei_escolas a')
            ->result();

        $data['cuidadores'] = ['' => 'Todos'] + array_column($escolas, 'nome', 'id');

        $cursos = $this->db
            ->select('id, nome')
            ->where('id_empresa', $empresa)
            ->get('ei_cursos')
            ->result();

        $data['disciplinas'] = ['' => 'Todos'] + array_column($cursos, 'nome', 'id');

        $alunosCursos = $this->db
            ->select('a.id, a.status_ativo, b.nome AS escola, c.nome AS diretoria, d.nome AS curso', false)
            ->join('ei_escolas b', 'b.id = a.id_escola')
            ->join('ei_diretorias c', 'c.id = b.id_diretoria')
            ->join('ei_cursos d', 'd.id = a.id_curso')
            ->get('ei_alunos_cursos a')
            ->row();

        $data['nomeCliente'] = $alunosCursos->diretoria;
        $data['nomeEscola'] = $alunosCursos->escola;
        $data['nomeCurso'] = $alunosCursos->curso;
        $data['cursoAtivo'] = $alunosCursos->status_ativo;
        $data['idAlunoCurso'] = $alunosCursos->id;

        $this->load->view('ei/turmas', $data);
    }

    //--------------------------------------------------------------------

    public function atualizar_filtro()
    {
        $empresa = $this->session->userdata('empresa');
        $busca = $this->input->post('busca');
        $filtro = [];

        $qb = $this->db
            ->select('a.id, a.nome')
            ->join('ei_contratos b', 'b.id = a.id_contrato')
            ->join('ei_diretorias c', 'c.id = b.id_cliente')
            ->where('c.id_empresa', $empresa);
        if ($busca['diretoria']) {
            $qb->where('c.id', $busca['diretoria']);
        }
        $escolas_disponiveis = $qb
            ->group_by('a.id')
            ->order_by('a.nome', 'asc')
            ->get('ei_escolas a')
            ->result();

        $filtro['escola'] = ['' => 'Todas'];
        foreach ($escolas_disponiveis as $escola_disponivel) {
            $filtro['escola'][$escola_disponivel->id] = $escola_disponivel->nome;
        }

        $data['escola'] = form_dropdown('escola', $filtro['escola'], $busca['escola'], 'onchange="atualizarFiltro()" class="form-control input-sm"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_list()
    {
        $post = $this->input->post();
        parse_str($this->input->post('busca'), $arrBusca);
        $busca = $arrBusca['busca'] ?? [];
        $idAlunoCurso = $this->input->post('id_aluno_curso');

        $sql = "SELECT s.id, 
                       s.dia_semana,
                       s.disciplina,
                       s.cuidador,
                       s.horario,
                       s.nota,
                       s.id_semestre, 
                       s.ano_semestre,
                       s.data_inicio,
                       s.data_termino,
                       s.modulo
                FROM (SELECT b.id,
                             a.id AS id_semestre,
                             CONCAT(a.ano, '/', a.semestre) AS ano_semestre, 
                             DATE_FORMAT(a.data_inicio, '%d/%m/%Y') AS data_inicio,
                             DATE_FORMAT(a.data_termino, '%d/%m/%Y') AS data_termino,
                             a.modulo, 
                             CASE b.dia_semana
                                  WHEN 0 THEN 'Domingo'
                                  WHEN 1 THEN 'Segunda'
                                  WHEN 2 THEN 'Terça'
                                  WHEN 3 THEN 'Quarta'
                                  WHEN 4 THEN 'Quinta'
                                  WHEN 5 THEN 'Sexta'
                                  WHEN 6 THEN 'Sábado'
                                  END AS dia_semana,
                             c.nome AS disciplina,
                             d.nome AS cuidador,
                             CONCAT(TIME_FORMAT(b.hora_inicio, '%h:%i'), ' às ', TIME_FORMAT(b.hora_termino, '%h:%i')) AS horario,
                             b.nota
                       FROM ei_semestres a 
                       INNER JOIN ei_alunos_cursos x ON x.id = a.id_aluno_curso
                       LEFT JOIN ei_alunos_turmas b ON b.id_semestre = a.id
                       LEFT JOIN ei_disciplinas c ON c.id = b.id_disciplina
                       LEFT JOIN usuarios d ON d.id = b.id_cuidador
                       WHERE x.id = '{$idAlunoCurso}'
                       ORDER BY a.ano DESC,
                                a.semestre DESC,
                                b.dia_semana ASC,
                                b.hora_inicio ASC,
                                b.hora_termino ASC) s";

        $recordsTotal = $this->db->query($sql)->num_rows();

        $columns = [
            's.id',
            's.dia_semana',
            's.disciplina',
            's.cuidador',
            's.horario',
            's.nota',
        ];

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
            $row[] = $ei->ano_semestre;
            $row[] = $ei->dia_semana;
            $row[] = $ei->disciplina;
            $row[] = $ei->cuidador;
            $row[] = $ei->horario;
            $row[] = '
                      <button type="button" class="btn btn-sm btn-info" onclick="edit_turma(' . $ei->id . ')" title="Editar turma"><i class="glyphicon glyphicon-pencil"></i> </button>
                      <button type="button" class="btn btn-sm btn-danger" onclick="delete_turma(' . $ei->id . ')" title="Excluir turma"><i class="glyphicon glyphicon-trash"></i> </button>
                      <a class="btn btn-sm btn-primary" href="' . site_url('ei/alunos/vincular/' . $ei->id) . '" title="Vincular alunos">Vincular alunos</button>
                     ';
            $row[] = $ei->data_inicio;
            $row[] = $ei->data_termino;
            $row[] = $ei->modulo;
            $row[] = '
                      <button type="button" class="btn btn-sm btn-info" onclick="edit_semestre(' . $ei->id_semestre . ')" title="Editar semestre"><i class="glyphicon glyphicon-pencil"></i> </button>
                      <button type="button" class="btn btn-sm btn-danger" onclick="delete_semestre(' . $ei->id_semestre . ')" title="Excluir semestre"><i class="glyphicon glyphicon-trash"></i> </button>
                      <button type="button" class="btn btn-sm btn-info" onclick="add_turma(' . $ei->id_semestre . ')" title="Editar semestre"><i class="glyphicon glyphicon-plus"></i> Turma</button>
                     ';

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
        $data = $this->db
            ->select('id, ano, semestre, modulo')
            ->select("DATE_FORMAT(data_inicio, '%d/%m/%Y') AS data_inicio", false)
            ->select("DATE_FORMAT(data_termino, '%d/%m/%Y') AS data_termino", false)
            ->get_where('ei_semestres', ['id' => $this->input->post('id')])
            ->row();

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_edit_turma()
    {
        $data = $this->db
            ->select('id, id_semestre, id_disciplina, id_cuidador, dia_semana, nota')
            ->select("TIME_FORMAT(hora_inicio, '%H:%i') AS hora_inicio", false)
            ->select("TIME_FORMAT(hora_termino, '%H:%i') AS hora_termino", false)
            ->get_where('ei_alunos_turmas', ['id' => $this->input->post('id')])
            ->row();

        if (!empty($data->nota)) {
            $data->nota = number_format($data->nota, 1, ',', '');
        }

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function atualizar_escolas()
    {
        $id_diretoria = $this->input->post('id_diretoria');
        $id_escola = $this->input->post('id_escola');

        $rows = $this->db
            ->select('id, nome')
            ->get_where('ei_escolas', ['id_diretoria' => $id_diretoria])
            ->result();

        $escolas = ['' => 'selecione...'];
        foreach ($rows as $row) {
            $escolas[$row->id] = $row->nome;
        }

        $selected = array_key_exists($id_escola, $escolas) ? $id_escola : '';
        $data = form_dropdown('id_escola', $escolas, $selected, 'id="id_escola" class="form-control"');

        echo $data;
    }

    //--------------------------------------------------------------------

    public function atualizar_periodos()
    {
        $data = $this->db
            ->select('periodo_manha, periodo_tarde, periodo_noite')
            ->get_where('ei_escolas', ['id' => $this->input->post('id')])
            ->row();

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_add()
    {
        $data = $this->input->post();

        if (empty($data['ano'])) {
            exit('O campo Ano é obrigatório');
        }
        if (empty($data['semestre'])) {
            exit('O campo Semestre é obrigatório');
        }

        foreach ($data as $key => $value) {
            if ($value === '') {
                $data[$key] = null;
            }
        }
        if ($data['data_inicio']) {
            $data['data_inicio'] = date("Y-m-d", strtotime(str_replace('/', '-', $data['data_inicio'])));
        }
        if ($data['data_termino']) {
            $data['data_termino'] = date("Y-m-d", strtotime(str_replace('/', '-', $data['data_termino'])));
        }

        $status = $this->db->insert('ei_semestres', $data);

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_add_turma()
    {
        $data = $this->input->post();

        if (empty($data['id_disciplina'])) {
            exit('O campo Disciplina é obrigatório');
        }

        foreach ($data as $key => $value) {
            if ($value === '') {
                $data[$key] = null;
            }
        }
        if ($data['nota']) {
            $data['nota'] = str_replace(',', '.', $data['nota']);
        }

        $status = $this->db->insert('ei_alunos_turmas', $data);

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_update()
    {
        $data = $this->input->post();
        $id = $data['id'];
        unset($data['id']);

        if (empty($data['ano'])) {
            exit('O campo Ano é obrigatório');
        }
        if (empty($data['semestre'])) {
            exit('O campo Semestre é obrigatório');
        }

        foreach ($data as $key => $value) {
            if ($value === '') {
                $data[$key] = null;
            }
        }
        if ($data['data_inicio']) {
            $data['data_inicio'] = date("Y-m-d", strtotime(str_replace('/', '-', $data['data_inicio'])));
        }
        if ($data['data_termino']) {
            $data['data_termino'] = date("Y-m-d", strtotime(str_replace('/', '-', $data['data_termino'])));
        }

        $status = $this->db->update('ei_semestres', $data, ['id' => $id]);

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_update_turma()
    {
        $data = $this->input->post();
        $id = $data['id'];
        unset($data['id']);

        if (empty($data['id_disciplina'])) {
            exit('O campo Disciplina é obrigatório');
        }

        foreach ($data as $key => $value) {
            if ($value === '') {
                $data[$key] = null;
            }
        }
        if ($data['nota']) {
            $data['nota'] = str_replace(',', '.', $data['nota']);
        }

        $status = $this->db->update('ei_alunos_turmas', $data, ['id' => $id]);

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_delete()
    {
        $status = $this->db->delete('ei_semestres', ['id' => $this->input->post('id')]);
        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_delete_turma()
    {
        $status = $this->db->delete('ei_alunos_turmas', ['id' => $this->input->post('id')]);
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
            [
                'field' => 'periodos',
                'label' => $label[14],
                'rules' => 'required',
            ],
        ];

        $this->form_validation->set_rules($config);

        return $this->form_validation->run();
    }

}
