<?php

namespace App\Controllers\Ei;

use App\Controllers\BaseController;

class Cursos_disciplinas extends BaseController
{

    /**
     * Abre a tela de curso/disciplina
     *
     * @access public
     * @uses ..\views\ei\cursos_disciplinas.php View
     */
    public function index()
    {
        $this->cursos();
    }

    //--------------------------------------------------------------------

    /**
     * Abre a tela de curso/disciplina na prmeira aba
     *
     * @access public
     * @uses ..\views\ei\cursos_disciplinas.php View
     */
    public function cursos()
    {
        $data = $this->input->get();
        $data['empresa'] = $this->session->userdata('empresa');
        $data['indice'] = 0;

        $clientes = $this->db
            ->select('a.id, a.nome')
            ->order_by('a.nome', 'asc')
            ->get('ei_diretorias a')
            ->result();

        $data['clientes'] = array_column($clientes, 'nome', 'id');

        $this->load->view('ei/cursos_disciplinas', $data);
    }

    //--------------------------------------------------------------------

    /**
     * Abre a tela de curso/disciplina na segunda aba
     *
     * @access public
     * @uses ..\views\ei\cursos_disciplinas.php View
     */
    public function disciplinas()
    {
        $data = $this->input->get();
        $data['empresa'] = $this->session->userdata('empresa');
        $data['indice'] = 1;
        $this->load->view('ei/cursos_disciplinas', $data);
    }

    //--------------------------------------------------------------------

    /**
     * Retorna lista de cursos existentes
     *
     * @access public
     */
    public function ajax_cursos()
    {
        $post = $this->input->post();

        $sql = "SELECT s.diretoria,
                       s.nome,
                       s.escola,
                       s.id
                FROM (SELECT a.id, 
                             CONCAT(a.id, ' - ', a.nome) AS nome,
                             a2.nome AS diretoria,
                             CONCAT(c.codigo, ' - ', c.nome) AS escola
                      FROM ei_cursos a
                      INNER JOIN ei_diretorias a2 ON 
                                 a2.id = a.id_diretoria
                      LEFT JOIN ei_escolas_cursos b ON 
                                b.id_curso = a.id
                      LEFT JOIN ei_escolas c ON 
                                c.id = b.id_escola
                      WHERE a.id_empresa = {$this->session->userdata('empresa')}
                ORDER BY a.nome ASC) s";

        $recordsTotal = $this->db->query($sql)->num_rows();

        $columns = ['s.id', 's.diretoria', 's.nome', 's.escola'];
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
        if ($post['length'] > 0) {
            $sql .= " LIMIT {$post['start']}, {$post['length']}";
        }
        $list = $this->db->query($sql)->result();

        $data = [];
        foreach ($list as $curso) {
            $row = [];
            $row[] = $curso->diretoria;
            $row[] = $curso->nome;
            $row[] = $curso->escola;
            $row[] = '
                      <button class="btn btn-sm btn-info" onclick="edit_curso(' . $curso->id . ')" title="Editar curso"><i class="glyphicon glyphicon-pencil"></i></button>
                      <button class="btn btn-sm btn-danger" onclick="delete_curso(' . $curso->id . ')" title="Excluir curso"><i class="glyphicon glyphicon-trash"></i></button>
                      <button class="btn btn-sm btn-primary" onclick="nextDisciplina(' . $curso->id . ')" title="Disciplinas"><i class="glyphicon glyphicon-list"></i> Disciplinas</button>
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

    /**
     * Retorna lista de funções existentes
     *
     * @access public
     */
    public function ajax_disciplinas()
    {
        $post = $this->input->post();

        $sql = "SELECT s.id, 
                       s.nome_curso,
                       s.nome,
                       s.qtde_semestres
                FROM (SELECT a.id, 
                             b.nome AS nome_curso,
                             a.nome,
                             a.qtde_semestres
                      FROM ei_cursos b
                      LEFT JOIN ei_disciplinas a
                                ON b.id = a.id_curso
                      WHERE b.id_empresa = {$this->session->userdata('empresa')}
                            AND (b.id = '{$post['id_curso']}' OR CHAR_LENGTH('{$post['id_curso']}') = 0)
                      ORDER BY a.nome ASC) s";

        $recordsTotal = $this->db->query($sql)->num_rows();

        $columns = ['s.id', 's.nome_curso', 's.nome'];
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
        if ($post['length'] > 0) {
            $sql .= " LIMIT {$post['start']}, {$post['length']}";
        }
        $list = $this->db->query($sql)->result();

        $data = [];
        foreach ($list as $disciplina) {
            $row = [];
            $row[] = $disciplina->nome_curso;
            if ($disciplina->id) {
                $row[] = $disciplina->nome;
                $row[] = $disciplina->qtde_semestres;
                $row[] = '
                          <button class="btn btn-sm btn-info" onclick="edit_disciplina(' . $disciplina->id . ')" title="Editar disciplina"><i class="glyphicon glyphicon-pencil"></i></button>
                          <button class="btn btn-sm btn-danger" onclick="delete_disciplina(' . $disciplina->id . ')" title="Excluir disciplina"><i class="glyphicon glyphicon-trash"></i></button>
                         ';
            } else {
                $row[] = '<span class="text-muted">Nenhuma disciplina encontrada</span>';
                $row[] = null;
                $row[] = '
                          <button class="btn btn-sm btn-info disabled" title="Editar disciplina"><i class="glyphicon glyphicon-pencil"></i></button>
                          <button class="btn btn-sm btn-danger disabled" title="Excluir disciplina"><i class="glyphicon glyphicon-trash"></i></button>
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

    /**
     * Retorna dados para edição de um curso
     *
     * @access public
     */
    public function atualizar_escolas()
    {
        $id = $this->input->post('id');

        $escolas = $this->db
            ->select("a.id, CONCAT(a.codigo, ' - ', a.nome) AS nome", false)
            ->join('ei_diretorias b', 'b.id = a.id_diretoria', 'left')
            ->where('b.id', $id)
            ->order_by('a.codigo', 'asc')
            ->order_by('a.nome', 'asc')
            ->get('ei_escolas a')
            ->result();

        $escolas = array_column($escolas, 'nome', 'id');

        $data['escolas'] = form_multiselect('id_escola[]', $escolas, [], 'id="id_escola" class="demo2" size="8"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    /**
     * Retorna dados para edição de um curso
     *
     * @access public
     */
    public function ajax_edit_curso()
    {
        $id = $this->input->post('id');

        $data = $this->db
            ->get_where('ei_cursos', ['id' => $id])
            ->row();

        $escolas = $this->db
            ->select('id, nome')
            ->where('id_diretoria', $data->id_diretoria)
            ->get('ei_escolas')
            ->result();

        $escolas = array_column($escolas, 'nome', 'id');

        $idEscolas = $this->db
            ->select('id_escola')
            ->where('id_curso', $id)
            ->get('ei_escolas_cursos')
            ->result_array();

        $escolasVinculadas = array_column($idEscolas, 'id_escola');

        $data->escolas = form_multiselect('id_escola[]', $escolas, $escolasVinculadas, 'id="id_escola" class="demo2" size="8"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    /**
     * Retorna dados para edição de uma disciplina
     *
     * @access public
     */
    public function ajax_edit_disciplina()
    {
        $data = $this->db
            ->get_where('ei_disciplinas', ['id' => $this->input->post('id')])
            ->row();

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    /**
     * Cadastra um novo curso
     *
     * @access public
     */
    public function ajax_add_curso()
    {
        $data = $this->input->post();

        $qtdeCursos = $this->db
            ->where('id_diretoria', $data['id_diretoria'])
            ->where('nome', $data['nome'])
            ->get('ei_cursos')
            ->num_rows();

        if ($qtdeCursos) {
            exit(json_encode(['erro' => "Este curso já existe e se encontra cadastrado em outras unidades. \nPara cadastrá-lo em uma nova unidade, basta editar o curso e relacionar a nova unidade desejada."]));
        }

        $idEscolas = $this->input->post('id_escola');
        if (empty($idEscolas)) {
            $idEscolas = [];
        }
        unset($data['id_escola']);
        $status = $this->db->insert('ei_cursos', $data);

        if ($status) {
            $idCurso = $this->db->insert_id();
            $data2 = [];
            foreach ($idEscolas as $idEscola) {
                $data2[] = ['id_escola' => $idEscola, 'id_curso' => $idCurso];
            }
            if ($data2) {
                $this->db->insert_batch('ei_escolas_cursos', $data2);
            }
        }

        echo json_encode(['status' => $status]);
    }

    //--------------------------------------------------------------------

    /**
     * Cadastra uma nova disciplina
     *
     * @access public
     */
    public function ajax_add_disciplina()
    {
        $data = $this->input->post();
        if (strlen($data['qtde_semestres']) == 0) {
            $data['qtde_semestres'] = null;
        }
        $status = $this->db->insert('ei_disciplinas', $data);
        echo json_encode(['status' => $status]);
    }

    //--------------------------------------------------------------------

    /**
     * Valida os dados para inserção de curso
     *
     * @access private
     * @return  bool|string    TRUE para sucesso, FALSE ou string para falha
     */
    private function validarCurso()
    {
        $config = [
            [
                'field' => 'id_empresa',
                'rules' => 'callback_verificaEmpresa',
            ],
            [
                'field' => 'nome',
                'rules' => 'required|max_length[255]',
            ],
        ];

        $this->load->form_validation();
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() == false) {
            return $this->form_validation->error_string();
        }

        return true;
    }

    //--------------------------------------------------------------------

    /**
     * Valida os dados para inserção de disciplina
     *
     * @access private
     * @return  bool|string    TRUE para sucesso, FALSE ou string para falha
     */
    private function validarDisciplina()
    {
        $config = [
            [
                'field' => 'id_curso',
                'rules' => 'callback_verificaCurso',
            ],
            [
                'field' => 'nome',
                'rules' => 'required|max_length[255]',
            ],
            [
                'field' => 'qtde_semestres',
                'rules' => 'is_natural_no_zero|max_length[2]',
            ],
        ];

        $this->load->form_validation();
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() == false) {
            return $this->form_validation->error_string();
        }

        return true;
    }

    //--------------------------------------------------------------------

    /**
     * Altera um curso
     *
     * @access public
     */
    public function ajax_update_curso()
    {
        $data = $this->input->post();
        $id = $this->input->post('id');
        $id_escolas = $this->input->post('id_escola');
        if (empty($id_escolas)) {
            $id_escolas = [];
        }

        unset($data['id'], $data['id_escola']);

        $escolasCursos = $this->db
            ->select('id, id_escola')
            ->where('id_curso', $id)
            ->get('ei_escolas_cursos')
            ->result();

        $escolasCursos = array_column($escolasCursos, 'id_escola', 'id');

        $this->db->trans_start();
        $this->db->update('ei_cursos', $data, ['id' => $id]);

        foreach ($escolasCursos as $idEscolaCurso => $escolaCurso) {
            if (!in_array($escolaCurso, $id_escolas)) {
                $this->db->delete('ei_escolas_cursos', ['id' => $idEscolaCurso]);
            }
        }
        foreach ($id_escolas as $id_escola) {
            $data2 = ['id_curso' => $id, 'id_escola' => $id_escola];
            $where = array_search($id_escola, $escolasCursos);

            if ($where !== false) {
                $this->db->update('ei_escolas_cursos', $data2, ['id' => $where]);
            } else {
                $this->db->insert('ei_escolas_cursos', $data2);
            }
        }

        $this->db->trans_complete();
        $status = $this->db->trans_status();

        echo json_encode(['status' => $status]);
    }

    //--------------------------------------------------------------------

    /**
     * Altera uma disciplina
     *
     * @access public
     */
    public function ajax_update_disciplina()
    {
        $data = $this->input->post();
        if (strlen($data['qtde_semestres']) == 0) {
            $data['qtde_semestres'] = null;
        }
        $id = $this->input->post('id');
        unset($data['id']);
        $status = $this->db->update('ei_disciplinas', $data, ['id' => $id]);

        echo json_encode(['status' => $status]);
    }

    //--------------------------------------------------------------------

    /**
     * Valida os dados para alteração de curso
     *
     * @access private
     * @return  bool|string    TRUE para sucesso, FALSE ou string para falha
     */
    private function revalidarCurso()
    {
        $config = [
            [
                'field' => 'id',
                'rules' => 'callback_verificaCurso',
            ],
            [
                'field' => 'id',
                'rules' => 'required|numeric|max_length[11]',
            ],
            [
                'field' => 'nome',
                'rules' => 'required|max_length[255]',
            ],
        ];

        $this->load->form_validation();
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() == false) {
            return $this->form_validation->error_string();
        }

        return true;
    }

    //--------------------------------------------------------------------

    /**
     * Valida os dados para alteração de disciplina
     *
     * @access private
     * @return  bool|string    TRUE para sucesso, FALSE ou string para falha
     */
    private function revalidarDisciplina()
    {
        $config = [
            [
                'field' => 'id',
                'rules' => 'callback_verificaDisciplina',
            ],
            [
                'field' => 'id_curso',
                'rules' => 'callback_verificaCurso',
            ],
            [
                'field' => 'nome',
                'rules' => 'required|max_length[255]',
            ],
            [
                'field' => 'qtde_semestres',
                'rules' => 'is_natural_no_zero|max_length[2]',
            ]
        ];

        $this->load->form_validation();
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() == false) {
            return $this->form_validation->error_string();
        }

        return true;
    }

    //--------------------------------------------------------------------

    /**
     * Exclui um curso
     *
     * @access public
     */
    public function ajax_delete_curso()
    {
        $status = $this->db->delete('ei_cursos', ['id' => $this->input->post('id')]);
        echo json_encode(['status' => $status]);
    }

    //--------------------------------------------------------------------

    /**
     * Exclui uma disciplina
     *
     * @access public
     */
    public function ajax_delete_disciplina()
    {
        $status = $this->db->delete('ei_disciplinas', ['id' => $this->input->post('id')]);
        echo json_encode(['status' => $status]);
    }

    //--------------------------------------------------------------------

    /*
    * --------------------------------------------------------------------------
    * Callbacks
    * --------------------------------------------------------------------------
    */
    private function verificaEmpresa(int $id): bool
    {
        if (!$this->db->get_where('usuarios', ['id' => $id])->num_rows()) {
            $this->form_validation->set_message('verificaEmpresa', 'A empresa não foi encontrada');
            return false;
        }
        return true;
    }

    //--------------------------------------------------------------------

    private function verificaCurso(int $id): bool
    {
        if (!$this->db->get_where('ei_cursos', ['id' => $id])->num_rows()) {
            $this->form_validation->set_message('verificaCurso', 'O campo %s não foi encontrado');
            return false;
        }
        return true;
    }

    //--------------------------------------------------------------------

    private function verificaDisciplina(int $id): bool
    {
        if (!$this->db->get_where('ei_disciplinas', ['id' => $id])->num_rows()) {
            $this->form_validation->set_message('verificaDisciplina', 'O campo %s não foi encontrado');
            return false;
        }
        return true;
    }

    //--------------------------------------------------------------------

    public function pdf_curso()
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

        $order = $this->input->get('order');
        $search = $this->input->get('search');

        $qb = $this->db
            ->select("CONCAT(a.id, ' - ', a.nome) AS nome", false)
            ->select("a2.nome AS diretoria", false)
            ->select("CONCAT(c.codigo, ' - ', c.nome) AS escola", false)
            ->join('ei_diretorias a2', 'a2.id = a.id_diretoria')
            ->join('ei_escolas_cursos b', 'b.id_curso = a.id', 'left')
            ->join('ei_escolas c', 'c.id = b.id_escola', 'left')
            ->where('a.id_empresa', $empresa);
        if ($search) {
            $qb->group_start()
                ->like('a.id', $search)
                ->or_like('a.nome', $search)
                ->or_like('a2.nome', $search)
                ->or_like('c.codigo', $search)
                ->or_like('c.nome', $search)
                ->group_end();
        }
        $subquery = $qb->get_compiled_select('ei_cursos a');

        $sql = "SELECT s.diretoria,
                       s.nome,
                       s.escola
                FROM ({$subquery}) s";
        $orderBy = [];
        foreach ($order as $value) {
            $orderBy[] = $value[0] . ' ' . strtoupper($value[1]);
        }
        if ($orderBy) {
            $sql .= ' ORDER BY ' . implode(', ', $orderBy);
        }

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
                    <h1 style="font-weight: bold;">LISTA DE CURSOS</h1>
                </td>
            </tr>
            </tbody>
        </table>
        <br><br>';

        $table = [['Área/cliente', 'Curso', 'Unidade de ensino']];
        foreach ($data as $row) {
            $table[] = $row;
        }
        $this->load->library('table');

        $html = $cabecalho . $this->table->generate($table);

        $this->m_pdf->pdf->writeHTML($html);

        $this->m_pdf->pdf->Output("EI_cursos.pdf", 'D');
    }

}
