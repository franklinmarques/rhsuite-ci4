<?php

namespace App\Controllers\Ei\Apontamento;

use App\Controllers\BaseController;

class Controle_materiais extends BaseController
{

    public function ajax_list()
    {
        $post = $this->input->post();

        parse_str($this->input->post('busca'), $busca);
        $semestre = $busca['semestre'] ?? null;
        if (empty($semestre)) {
            $semestre = intval($busca['mes']) > 7 ? '2' : '1';
        }

        $recordsTotal = $this->db
            ->select('b.municipio, a.aluno, b.escola, b.ordem_servico, a.id')
            ->select(["CASE WHEN MONTH(e.data_substituicao1) < '{$busca['mes']}' || MONTH(e.data_substituicao2) < '{$busca['mes']}' THEN NULL ELSE f.cuidador END AS cuidador"], false)
            ->select(["CASE WHEN MONTH(e.data_substituicao1) <= '{$busca['mes']}' THEN g.nome ELSE NULL END AS cuidador_sub1"], false)
            ->select(["CASE WHEN MONTH(e.data_substituicao2) <= '{$busca['mes']}' THEN h.nome ELSE NULL END AS cuidador_sub2"], false)
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->join('ei_matriculados_turmas d', 'd.id_matriculado = a.id')
            ->join('ei_alocados_horarios e', 'e.id = d.id_alocado_horario')
            ->join('ei_alocados f', 'f.id = e.id_alocado AND f.id_alocacao_escola = b.id')
            ->join('usuarios g', 'g.id = e.id_cuidador_sub1', 'left')
            ->join('usuarios h', 'h.id = e.id_cuidador_sub2', 'left')
            ->where('c.id_empresa', $this->session->userdata('empresa'))
            ->where('c.depto', $busca['depto'])
            ->where('c.id_diretoria', $busca['diretoria'])
            ->where('c.id_supervisor', $busca['supervisor'])
            ->where('c.ano', $busca['ano'])
            ->where('c.semestre', $semestre)
            ->group_by('a.id')
            ->get('ei_matriculados a')
            ->num_rows();

        $sql = "SELECT s.* FROM ({$this->db->last_query()}) s";

        if ($post['search']['value']) {
            $sql .= " WHERE s.municipio LIKE '%{$post['search']['value']}%' OR 
                            s.escola LIKE '%{$post['search']['value']}%' OR 
                            s.ordem_servico LIKE '%{$post['search']['value']}%' OR 
                            s.aluno LIKE '%{$post['search']['value']}%'";
            $recordsFiltered = $this->db->query($sql)->num_rows();
        } else {
            $recordsFiltered = $recordsTotal;
        }

        if ($post['order']) {
            $orderBy = [];
            foreach ($post['order'] as $order) {
                $orderBy[] = intval($order['column'] + 1) . ' ' . $order['dir'];
            }
            $sql .= ' ORDER BY ' . implode(', ', $orderBy);
            if ($post['length'] > 0) {
                $sql .= " LIMIT {$post['start']}, {$post['length']}";
            }
        }
        $matriculados = $this->db->query($sql)->result();

        $rowFrequencias = $this->db
            ->select('a.id_matriculado, a.status')
            ->select("DATE_FORMAT(a.data, '%d') AS dia", false)
            ->select("COUNT(b.id_frequencia) AS total_insumos", false)
            ->join('ei_controle_materiais b', 'b.id_frequencia = a.id', 'left')
            ->where_in('a.id_matriculado', array_column($matriculados, 'id') + [0])
            ->where("DATE_FORMAT(a.data, '%Y-%m') =", "{$busca['ano']}-{$busca['mes']}")
            ->group_by('a.id')
            ->get('ei_frequencias a')
            ->result();

        $frequencias = [];
        $nomeDoStatus = [
            '' => 'Aluno presente',
            'AF' => 'Aluno faltou',
            'AI' => 'Aluno inativo',
        ];
        foreach ($rowFrequencias as $rowfrequencia) {
            $frequencias[$rowfrequencia->id_matriculado][intval($rowfrequencia->dia)] = [
                'status' => $rowfrequencia->status,
                'tipo' => $nomeDoStatus[$rowfrequencia->status] ?? null,
                'insumos' => $rowfrequencia->total_insumos,
            ];
        }

        $data = [];
        foreach ($matriculados as $matriculado) {
            $row = [
                "<strong>Municipio:</strong> {$matriculado->municipio}&emsp;
                <strong>Escola:</strong> {$matriculado->escola}<br>
                <strong>Ordem de serviço:</strong> {$matriculado->ordem_servico} 
                <strong>Cuidador(a):</strong> " . implode('; ', array_filter([$matriculado->cuidador, $matriculado->cuidador_sub1, $matriculado->cuidador_sub2])),
                $matriculado->aluno,
            ];
            for ($i = 1; $i <= 31; $i++) {
                $row[] = $frequencias[$matriculado->id][$i] ?? [];
            }
            $row[] = $matriculado->id;

            $data[] = $row;
        }

        $this->load->library('Calendar');
        $dias_semana = $this->calendar->get_day_names('long');
        $semana = [];
        for ($i = 1; $i <= 7; $i++) {
            $semana[$i] = $dias_semana[date('w', mktime(0, 0, 0, $busca['mes'], $i, $busca['ano']))];
        }
        $nomeSemestre = '';
        if ($busca['mes'] == 7) {
            $nomeSemestre = " - {$semestre}&ordm; semestre";
        }
        $calendario = [
            'mes' => $busca['mes'],
            'ano' => $busca['ano'],
            'mes_ano' => $this->calendar->get_month_name($busca['mes']) . ' ' . $busca['ano'] . $nomeSemestre,
            'qtde_dias' => date('t', mktime(0, 0, 0, $busca['mes'], 1, $busca['ano'])),
            'semana' => $semana,
        ];

        $output = [
            'draw' => $this->input->post('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'calendar' => $calendario,
            'data' => $data,
        ];

        echo json_encode($output);
    }

    //--------------------------------------------------------------------

    public function ajax_edit()
    {
        $id_matriculado = $this->input->post('id_matriculado');
        $date = $this->input->post('date');

        $data = $this->db
            ->select('c.id AS id_frequencia, a.aluno, b.escola, b.municipio, b.ordem_servico, c.status')
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_frequencias c', "c.id_matriculado = a.id AND c.data = '{$date}'", 'left')
            ->where('a.id', $id_matriculado)
            ->get('ei_matriculados a')
            ->row();

        $qb = $this->db;
        if ($data->id_frequencia) {
            $qb->select('a.id, a.nome, a.tipo, IFNULL(b.qtde, 0) AS qtde, b.id_frequencia', false)
                ->join('ei_controle_materiais b', "b.id_insumo = a.id AND b.id_frequencia = '{$data->id_frequencia}'", 'left')
                ->join('ei_frequencias c', 'c.id = b.id_frequencia', 'left');
        } else {
            $qb->select('a.id, a.nome, a.tipo, 0 AS qtde', false);
        }
        $rows = $qb
            ->where('a.id_empresa', $this->session->userdata('empresa'))
            ->order_by('a.id', 'asc')
            ->get('ei_insumos a')
            ->result();

        $this->load->library('table');
        $this->table->set_template([
            'table_open' => '<table class="table table-condensed" width="100%">',
        ]);

        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $this->table->add_row(
                    $row->nome, form_input([
                    'name' => "qtde_insumos[{$row->id}]",
                    'value' => $row->qtde,
                    'type' => 'number',
                    'class' => 'form-control qtde_insumos text-right input-sm',
                    'style' => 'width: 100px;',
                ]), $row->tipo);
            }
        } else {
            $this->table->add_row('<span class="text-center">Nenhum insumo encontrado.</span>');
        }

        $data->qtde_insumos = $this->table->generate();

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_save()
    {
        $data = $this->input->post();
        $id = $data['id'];
        $insumos = $data['qtde_insumos'];
        unset($data['id'], $data['qtde_insumos']);
        if (empty($insumos)) {
            $insumos = [];
        }
        if (strlen($data['status']) == 0) {
            $data['status'] = null;
        }

        $this->db->trans_start();

        if (array_filter($insumos) or $data['status']) {
            if ($id) {
                $this->db->update('ei_frequencias', $data, ['id' => $id]);
            } else {
                $this->db->insert('ei_frequencias', $data);
                $id = $this->db->insert_id();
            }

            $rows = $this->db
                ->select('id, id_insumo')
                ->where('id_frequencia', $id)
                ->get('ei_controle_materiais')
                ->result();

            $controleMaterial = [];
            foreach ($rows as $row) {
                $controleMaterial[$row->id_insumo] = $row->id;
            }
        } else {
            $this->db->delete('ei_frequencias', ['id' => $id]);
        }

        foreach ($insumos as $id_insumo => $qtde) {
            $data = [
                'id_frequencia' => $id,
                'id_insumo' => $id_insumo,
                'qtde' => $qtde,
            ];

            if (isset($controleMaterial[$id_insumo])) {
                if ($qtde > 0) {
                    $this->db->update('ei_controle_materiais', $data, ['id' => $controleMaterial[$id_insumo]]);
                } else {
                    $this->db->delete('ei_controle_materiais', ['id' => $controleMaterial[$id_insumo]]);
                }
            } elseif ($qtde > 0) {
                $this->db->insert('ei_controle_materiais', $data);
            }
        }

        $this->db->trans_complete();
        $status = $this->db->trans_status();

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_delete()
    {
        $status = $this->db->delete('ei_frequencias', ['id' => $this->input->post('id')]);

        echo json_encode(['status' => $status !== false]);
    }

}
