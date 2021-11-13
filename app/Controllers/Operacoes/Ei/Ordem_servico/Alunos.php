<?php

namespace App\Controllers\Ei\Ordem_servico;

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
        $data = $this->db
            ->select('a2.nome AS ordemServico, e.id AS id_depto', false)
            ->select('b.nome AS nomeEscola', false)
            ->select('c.nome AS nomeCliente', false)
            ->select('d.contrato AS nomeContrato', false)
            ->select("CONCAT(a2.ano, '/', a2.semestre) AS anoSemestre", false)
            ->join('ei_ordem_servico a2', 'a.id_ordem_servico = a2.id')
            ->join('ei_escolas b', 'b.id = a.id_escola')
            ->join('ei_diretorias c', 'c.id = b.id_diretoria')
            ->join('ei_contratos d', 'd.id_cliente = c.id')
            ->join('empresa_departamentos e', 'e.nome = c.depto', 'left')
            ->where('a.id', $this->uri->rsegment(3, 0))
            ->get('ei_ordem_servico_escolas a')
            ->row();

        $data->alunos = $this->getAlunos();

        $this->load->view('ei/ordem_servico_alunos', $data);
    }

    //--------------------------------------------------------------------

    public function montar_estrutura()
    {
        parse_str($this->input->post('busca'), $busca);

        $alunoCursos = $this->getAlunoCursos($busca['id_aluno']);

        $data['aluno_cursos'] = form_dropdown('id_aluno_curso', $alunoCursos, $busca['id_aluno_curso'], 'id="curso" class="form-control filtro"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_list(string $idEscola = null)
    {
        $post = $this->input->post();

        $sql = "SELECT s.id, 
                       s.curso, 
                       s.aluno,
                       s.data_inicio,
                       s.data_termino,
                       s.modulo
                FROM (SELECT a.id,
                             e.nome AS curso,
                             c.nome AS aluno,
                             DATE_FORMAT(a.data_inicio, '%d/%m/%Y') AS data_inicio,
                             DATE_FORMAT(a.data_termino, '%d/%m/%Y') AS data_termino,
                             a.modulo
                      FROM ei_ordem_servico_alunos a
                      INNER JOIN ei_ordem_servico_escolas b ON 
                                 b.id = a.id_ordem_servico_escola
                      INNER JOIN ei_alunos c ON 
                                 c.id = a.id_aluno
                      INNER JOIN ei_alunos_cursos d ON 
                                 d.id = a.id_aluno_curso
                      INNER JOIN ei_cursos e ON 
                                 e.id = d.id_curso
                      WHERE b.id = '{$idEscola}') s";
        $records = $this->db->query($sql)->num_rows();

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
        foreach ($list as $ei) {
            $row = [];
            $row[] = $ei->curso;
            $row[] = $ei->aluno;
            $row[] = $ei->data_inicio;
            $row[] = $ei->data_termino;
            $row[] = $ei->modulo;
            $row[] = '
                      <button type="button" class="btn btn-sm btn-info" onclick="edit_aluno(' . $ei->id . ')" title="Editar aluno(a)"><i class="glyphicon glyphicon-pencil"></i> </button>
                      <button type="button" class="btn btn-sm btn-danger" onclick="delete_aluno(' . $ei->id . ')" title="Excluir aluno(a)"><i class="glyphicon glyphicon-trash"></i> </button>
                     ';

            $data[] = $row;
        }

        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $records,
            "recordsFiltered" => $records,
            "data" => $data,
        );

        echo json_encode($output);
    }

    //--------------------------------------------------------------------

    public function ajax_edit()
    {
        $id = $this->input->post('id');
        parse_str($this->input->post('busca'), $busca);

        $data = $this->db
            ->where('id', $id)
            ->get('ei_ordem_servico_alunos')
            ->row();

        $data->data_inicio = date('d/m/Y', strtotime(str_replace('-', '/', $data->data_inicio)));
        $data->data_termino = date('d/m/Y', strtotime(str_replace('-', '/', $data->data_termino)));
        $data->nota = str_replace('.', ',', $data->nota);

        $alunoCursos = $this->getAlunoCursos($data->id_aluno);

        $data->aluno_curso = form_dropdown('id_aluno_curso', $alunoCursos, $data->id_aluno_curso, 'id="aluno_curso" class="form-control filtro"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_add()
    {
        $data = $this->input->post();
        $id = $data['id'];
        unset($data['id']);
        $erro = '';
        if (empty($data['id_aluno'])) {
            $erro .= "O aluno não pode ficar em branco. \n";
        }
        if (empty($data['id_aluno_curso'])) {
            $erro .= "O curso não pode ficar em branco. \n";
        }
        if (empty($data['data_inicio'])) {
            $erro .= "A data de início não pode ficar em branco. \n";
        }
        if (empty($data['data_termino'])) {
            $erro .= "A data de término não pode ficar em branco. \n";
        }
        if (empty($data['modulo'])) {
            $erro .= "O módulo não pode ficar em branco.";
        }
        if ($erro) {
            exit(json_encode(['erro' => $erro]));
        }

        $count = $this->db
            ->select('data_inicio, data_termino, modulo')
            ->where('id_ordem_servico_escola', $data['id_ordem_servico_escola'])
            ->where('id_aluno', $data['id_aluno'])
            ->where('id_aluno_curso', $data['id_aluno_curso'])
            ->where("((data_inicio = '{$data['data_inicio']}' OR data_termino = '{$data['data_termino']}') OR modulo = '{$data['modulo']}')", null, false)
            ->get('ei_ordem_servico_alunos')
            ->num_rows();

        if ($count) {
            exit(json_encode(['erro' => 'Os dias ou módulo já foram cadastrados para este cuidador.']));
        }

        $data['data_inicio'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['data_inicio'])));
        $data['data_termino'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['data_termino'])));
        if (strlen($data['nota'])) {
            $data['nota'] = str_replace(',', '.', $data['nota']);
        } else {
            $data['nota'] = null;
        }

        $status = $this->db->insert('ei_ordem_servico_alunos', $data);

        $this->atualizarNotaGeral($data['id_aluno_curso']);

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_update()
    {
        $id = $this->input->post('id');
        $data = $this->input->post();
        unset($data['id']);
        $erro = '';
        if (empty($data['id_aluno'])) {
            $erro .= "O aluno não pode ficar em branco. \n";
        }
        if (empty($data['id_aluno_curso'])) {
            $erro .= "O curso não pode ficar em branco. \n";
        }
        if (empty($data['data_inicio'])) {
            $erro .= "A data de início não pode ficar em branco. \n";
        }
        if (empty($data['data_termino'])) {
            $erro .= "A data de término não pode ficar em branco. \n";
        }
        if (empty($data['modulo'])) {
            $erro .= "O módulo não pode ficar em branco.";
        }
        if ($erro) {
            exit(json_encode(['erro' => $erro]));
        }

        $count = $this->db
            ->select('data_inicio, data_termino, modulo')
            ->where('id !=', $id)
            ->where('id_ordem_servico_escola', $data['id_ordem_servico_escola'])
            ->where('id_aluno', $data['id_aluno'])
            ->where('id_aluno_curso', $data['id_aluno_curso'])
            ->where("((data_inicio = '{$data['data_inicio']}' OR data_termino = '{$data['data_termino']}') OR modulo = '{$data['modulo']}')", null, false)
            ->get('ei_ordem_servico_alunos')
            ->num_rows();

        if ($count) {
            exit(json_encode(['erro' => 'Os dias ou módulo já foram cadastrados para este cuidador.']));
        }

        $data['data_inicio'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['data_inicio'])));
        $data['data_termino'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['data_termino'])));
        if (strlen($data['nota'])) {
            $data['nota'] = str_replace(',', '.', $data['nota']);
        } else {
            $data['nota'] = null;
        }

        $status = $this->db->update('ei_ordem_servico_alunos', $data, ['id' => $id]);

        $this->atualizarNotaGeral($data['id_aluno_curso']);

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_delete()
    {
        $osAluno = $this->db
            ->select('id, id_aluno_curso')
            ->where('id', $this->input->post('id'))
            ->get('ei_ordem_servico_alunos')
            ->row();

        if (empty($osAluno)) {
            exit(json_encode(['erro' => 'Não foi possível excluir a O.S. do aluno.']));
        }

        $status = $this->db->delete('ei_ordem_servico_alunos', ['id' => $osAluno->id]);

        $this->atualizarNotaGeral($osAluno->id_aluno_curso);

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    private function atualizarNotaGeral(?int $idAlunoCurso = null): void
    {
        $osAluno = $this->db
            ->select_avg('nota')
            ->where('id_aluno_curso', $idAlunoCurso)
            ->get('ei_ordem_servico_alunos')
            ->row();

        $notaGeral = $osAluno->nota ?? null;

        $this->db->update('ei_alunos_cursos', ['nota_geral' => $notaGeral], ['id' => $idAlunoCurso]);
    }

    //--------------------------------------------------------------------

    private function getAlunos(): array
    {
        $rows = $this->db
            ->select('a.id, a.nome')
            ->join('ei_alunos_cursos b', 'b.id_aluno = a.id')
            ->join('ei_cursos c', 'c.id = b.id_curso')
            ->join('ei_escolas_cursos d', 'd.id_curso = c.id')
            ->join('ei_escolas e', 'e.id = d.id_escola AND b.id_escola = e.id')
            ->join('ei_diretorias f', 'f.id = e.id_diretoria')
            ->join('ei_ordem_servico_escolas g', 'g.id_escola = e.id')
            ->where('f.id_empresa', $this->session->userdata('empresa'))
            ->where('g.id', $this->uri->rsegment(3))
            ->order_by('a.nome', 'asc')
            ->get('ei_alunos a')
            ->result();

        return ['' => 'selecione...'] + array_column($rows, 'nome', 'id');
    }

    //--------------------------------------------------------------------

    private function getAlunoCursos(?int $id = null): array
    {
        $rows = $this->db
            ->select('a.id, b.nome')
            ->join('ei_cursos b', 'b.id = a.id_curso')
            ->where('a.id_aluno', $id)
            ->order_by('b.nome', 'asc')
            ->get('ei_alunos_cursos a')
            ->result();

        return ['' => 'selecione...'] + array_column($rows, 'nome', 'id');
    }

}
