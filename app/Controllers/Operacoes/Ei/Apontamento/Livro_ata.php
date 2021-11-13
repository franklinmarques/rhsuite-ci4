<?php

namespace App\Controllers\Ei\Apontamento;

use App\Controllers\BaseController;

class Livro_ata extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('ei_livro_ata_model', 'livroAta');
    }

    //--------------------------------------------------------------------

    public function ajax_list()
    {
        parse_str($this->input->post('busca'), $busca);
        parse_str($this->input->post('filtro'), $filtro);

        $dataInicio = strToDate($filtro['data_inicio'] ?? null);
        $dataTermino = strToDate($filtro['data_termino'] ?? null);

        $qb = $this->db
            ->select('a.data, a.data_inicio_periodo, a.data_termino_periodo, a.alunos, a.periodo, a.curso, a.modulo, a.escola')
            ->select('a.atividades_realizadas, a.dificuldades_encontradas, a.sugestoes_observacoes')
            ->select(["a.id, DATE_FORMAT(a.data, '%d/%m/%Y') AS data_de"], false)
            ->select(["DATE_FORMAT(a.data_inicio_periodo, '%d/%m/%Y') AS data_inicio_periodo_de"], false)
            ->select(["DATE_FORMAT(a.data_termino_periodo, '%d/%m/%Y') AS data_termino_periodo_de"], false)
            ->join('usuarios b', 'b.id = a.id_usuario')
            ->where('b.empresa', $this->session->userdata('empresa'))
            ->where('MONTH(a.data)', $busca['mes'])
            ->where('YEAR(a.data)', $busca['ano']);
        if (!empty($filtro['profissional'])) {
            $qb->where('b.id', $filtro['profissional']);
        }
        if (!empty($filtro['escola'])) {
            $qb->where('a.escola', $filtro['escola']);
        }
        if ($dataInicio) {
            $qb->where('a.data_inicio_periodo >=', $dataInicio);
        }
        if ($dataTermino) {
            $qb->where('a.data_termino_periodo <=', $dataTermino);
        }
        $sql = $qb->get_compiled_select('ei_livro_ata a');

        $this->load->library('dataTables');

        $output = $this->datatables->query($sql);

        $data = [];

        $periodos = $this->livroAta::PERIODOS;

        foreach ($output->data as $row) {
            $data[] = [
                $row->data_de,
                $row->data_inicio_periodo_de,
                $row->data_termino_periodo_de,
                $row->alunos,
                $periodos[$row->periodo] ?? null,
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

        $colaboradores = $this->db
            ->select('a.id_usuario, b.nome')
            ->join('usuarios b', 'b.id = a.id_usuario')
            ->where('MONTH(a.data)', $busca['mes'])
            ->where('YEAR(a.data)', $busca['ano'])
            ->group_by('a.id_usuario')
            ->order_by('b.nome', 'asc')
            ->get('ei_livro_ata a')
            ->result_array();

        $colaboradores = ['' => 'Todos'] + array_column($colaboradores, 'nome', 'id_usuario');

        $escolas = $this->db
            ->select('escola')
            ->where('CHAR_LENGTH(escola) >', 0)
            ->where('MONTH(data)', $busca['mes'])
            ->where('YEAR(data)', $busca['ano'])
            ->group_start()
            ->where('id_usuario', $filtro['profissional'])
            ->or_where("CHAR_LENGTH('{$filtro['profissional']}') = 0")
            ->group_end()
            ->group_by('escola')
            ->order_by('escola', 'asc')
            ->get('ei_livro_ata')
            ->result_array();

        $escolas = ['' => 'Todas'] + array_column($escolas, 'escola', 'escola');

        $output->colaboradores = form_dropdown('', $colaboradores, $filtro['profissional']);
        $output->escolas = form_dropdown('', $escolas, $filtro['escola']);

        echo json_encode($output);
    }

    //--------------------------------------------------------------------

    public function ajax_edit()
    {
        $data = $this->db
            ->where('id', $this->input->post('id'))
            ->get('ei_livro_ata')
            ->row();

        if (empty($data)) {
            exit(json_encode(['erro' => 'Evento de livro de ATA não encontrado.']));
        }

        $data->data = dateFormat($data->data);
        $data->data_inicio_periodo = dateFormat($data->data_inicio_periodo);
        $data->data_termino_periodo = dateFormat($data->data_termino_periodo);

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_save()
    {
        $this->load->library('entities');
        $data = $this->entities->create('EiLivroAta', $this->input->post());
        $this->load->model('ei_livro_ata_model', 'livro_ata');
        if ($this->livro_ata->save($data) == false) {
            exit(json_encode(['retorno' => 0, 'aviso' => $this->livro_ata->errors()]));
        }
        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    public function ajax_delete()
    {
        $status = $this->db->delete('ei_livro_ata', ['id' => $this->input->post('id')]);

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function pdf()
    {
        $data['empresa'] = $this->db
            ->select('foto, foto_descricao')
            ->get_where('usuarios', ['id' => $this->session->userdata('empresa')])
            ->row();

        $mes = $this->input->get('mes');
        $ano = $this->input->get('ano');

        $this->load->library('calendar');

        $idUsuario = $this->input->get('profissional');
        $escola = $this->input->get('escola');
        $dataInicio = strToDate($this->input->get('data_inicio'));
        $dataTermino = strToDate($this->input->get('data_termino'));

        $usuario = $this->db
            ->select('a.cnpj, a.nome, b.nome AS funcao')
            ->join('empresa_funcoes b', 'b.id = a.id_funcao')
            ->where('a.id', $idUsuario)
            ->get('usuarios a')
            ->row();

        $data['profissional'] = $usuario->nome;
        $data['funcao'] = $usuario->funcao ?? null;
        $data['cnpj'] = $usuario->cnpj ?? null;
        $data['mes_ano'] = $this->calendar->get_month_name($mes) . '/' . $ano;
        $data['escola'] = $escola;
        $data['periodo'] = null;
        if ($dataInicio and $dataTermino) {
            $data['periodo'] = 'De ' . dateFormat($dataInicio) . ' a ' . dateFormat($dataTermino);
        } else {
            if ($dataInicio) {
                $data['periodo'] = 'A partir de ' . dateFormat($dataInicio);
            } elseif ($dataTermino) {
                $data['periodo'] = 'Até ' . dateFormat($dataTermino);
            }
        }

        $qb = $this->db
            ->select('a.data, a.alunos, a.curso, a.modulo, a.escola')
            ->select('a.atividades_realizadas, a.dificuldades_encontradas, a.sugestoes_observacoes')
            ->select(["DATE_FORMAT(a.data, '%d/%m/%Y') AS data"], false)
            ->select('a.profissional, a.alunos, a.curso, a.modulo, a.escola')
            ->select('a.atividades_realizadas, a.dificuldades_encontradas, a.sugestoes_observacoes')
            ->join('usuarios b', 'b.id = a.id_usuario')
            ->where('b.empresa', $this->session->userdata('empresa'))
            ->where('MONTH(a.data)', $mes)
            ->where('YEAR(a.data)', $ano);
        if ($idUsuario) {
            $qb->where('a.id_usuario', $idUsuario);
        }
        if ($escola) {
            $qb->where('a.escola', $escola);
        }
        if ($dataInicio) {
            $qb->where('a.data >=', $dataInicio);
        }
        if ($dataTermino) {
            $qb->where('a.data <=', $dataTermino);
        }
        $data['rows'] = $qb
            ->group_by(['a.id', 'a.data'])
            ->order_by('a.data', 'asc')
            ->get('ei_livro_ata a')
            ->result();

        $this->load->library('m_pdf');

        $stylesheet = '#table thead th { font-size: 12px; padding: 5px; background-color: #f5f5f5;} ';
        $stylesheet .= '#table tbody td { font-size: 14px; padding: 5px; vertical-align: top; } ';
        $stylesheet .= '#livro_ata thead tr th { padding: 5px; text-align: center; background-color: #f5f5f5; border-color: #ddd; } ';
        $stylesheet .= '#livro_ata tbody tr td { font-size: 12px; padding: 5px; } ';

        $topMarginAdd = 0;
        if ($data['escola']) {
            $topMarginAdd += 8;
        }
        if ($data['profissional'] or $data['cnpj']) {
            $topMarginAdd += 8;
        }
        if ($data['periodo']) {
            $topMarginAdd += 8;
        }
        $this->m_pdf->pdf->setTopMargin(48 + $topMarginAdd);
        $this->m_pdf->pdf->AddPage('L');
        $this->m_pdf->pdf->writeHTML($stylesheet, 1);
        $this->m_pdf->pdf->writeHTML($this->load->view('ei/livro_ata_pdf', $data, true));

        $this->calendar->month_type = 'short';

        $this->m_pdf->pdf->Output('Livro ATA - ' . $data['mes_ano'] . '.pdf', 'D');
    }

}
