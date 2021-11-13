<?php

namespace App\Controllers\Ei\Apontamento;

use App\Controllers\BaseController;

class Notas_fiscais extends BaseController
{

    public function ajax_list()
    {
        parse_str($this->input->post('busca'), $busca);
        parse_str($this->input->post('filtro'), $filtro);

        $ano = $filtro['ano'] ?: $busca['ano'];
        $semestre = $filtro['semestre'];
        $idMes = $filtro['mes'] ? intval($filtro['mes']) : '';
        if ($idMes > 7 or ($idMes == 7 and $semestre > 1)) {
            $idMes -= 6;
        }

        $this->load->library('calendar');

        $subqueries = [];
        $listaMeses = [
            '1' => 'mes1',
            '2' => 'mes2',
            '3' => 'mes3',
            '4' => 'mes4',
            '5' => 'mes5',
            '6' => 'mes6',
            '7' => 'mes7',
            '0' => 'sub',
        ];
        if ($filtro['mes']) {
            $listaMeses = array_filter($listaMeses, function ($k) use ($idMes) {
                return (int)$k === $idMes;
            }, ARRAY_FILTER_USE_KEY);
        }

        foreach ($listaMeses as $v => $k) {
            $qb = $this->db
                ->select("{$k}a.nota_fiscal_{$k} AS numero_nota_fiscal")
                ->select("{$k}a.data_emissao_{$k} AS data_emissao")
                ->select("{$k}a.arquivo_nota_fiscal_{$k} AS arquivo_nota_fiscal")
                ->select("{$k}a.data_criacao_{$k} AS data_criacao")
                ->select("{$k}a.id, '{$v}' AS id_mes")
                ->select("{$k}a.validacao_nota_fiscal_{$k} AS validacao_nota_fiscal")
                ->join("ei_alocacao {$k}b", "{$k}b.id = {$k}a.id_alocacao")
                ->join("ei_alocacao_escolas {$k}c", "{$k}c.id_alocacao = {$k}b.id")
                ->join("ei_alocados {$k}d", "{$k}d.id_alocacao_escola = {$k}c.id AND {$k}d.id_cuidador = {$k}a.id_cuidador")
                ->where("{$k}b.id_empresa", $this->session->userdata('empresa'))
                ->where("{$k}b.ano", $ano)
                ->where("{$k}b.semestre", $semestre)
                ->where("{$k}a.arquivo_nota_fiscal_{$k} IS NOT NULL")
                ->group_by("{$k}a.id");
            if (!empty($filtro['profissional'])) {
                $qb->where("{$k}a.id_cuidador", $filtro['profissional']);
            }
            if (!empty($filtro['ano'])) {
                $qb->where("{$k}b.ano", $filtro['ano']);
            }
            $subqueries[] = $qb
                ->get_compiled_select("ei_pagamento_prestador {$k}a");
        }
        $sql = implode(' UNION ', $subqueries);

        $this->load->library('dataTables');

        $output = $this->datatables->query($sql);

        $output->ano = $busca['ano'];
        $output->mes = $busca['mes'];
        $output->semestre = $busca['semestre'];

        $data = [];

        foreach ($output->data as $row) {
            $btnVisualizar = $row->validacao_nota_fiscal ? 'btn-success' : 'btn-warning';
            $data[] = [
                $row->arquivo_nota_fiscal,
                dateFormat($row->data_emissao),
                $row->numero_nota_fiscal,
                str_replace(' ', chr(10), datetimeFormat($row->data_criacao, true, false)),
                '<button class="btn btn-sm btn-info" onclick="edit_nota_fiscal(' . $row->id . ', ' . $row->id_mes . ')" title="Editar nota fiscal"><i class="glyphicon glyphicon-pencil"></i></button>
                 <button class="btn btn-sm ' . $btnVisualizar . '" onclick="visualizar_nota_fiscal(' . $row->id . ', ' . $row->id_mes . ')" title="Visualizar nota fiscal"><i class="glyphicon glyphicon-eye-open"></i> Visualizar</button>
                 <button class="btn btn-sm btn-danger" onclick="excluir_nota_fiscal(' . $row->id . ', ' . $row->id_mes . ')" title="Excluir nota fiscal"><i class="glyphicon glyphicon-trash"></i></button>',
            ];
        }

        $output->data = $data;

        $colaboradoresNotasFiscais = $this->db
            ->select('a.id_cuidador, TRIM(b.nome) AS nome', false)
            ->join('usuarios b', 'b.id = a.id_cuidador')
            ->join('ei_alocacao c', 'c.id = a.id_alocacao')
            ->where('c.ano', $ano)
            ->where('c.semestre', $semestre)
            ->group_by('a.id_cuidador')
            ->order_by('TRIM(b.nome)', 'asc')
            ->get('ei_pagamento_prestador a')
            ->result_array();

        $colaboradores = ['' => 'Todos'] + array_column($colaboradoresNotasFiscais, 'nome', 'id_cuidador');

        $output->colaboradores = form_dropdown('', $colaboradores, $filtro['profissional']);

        echo json_encode($output);
    }

    //--------------------------------------------------------------------

    public function visualizar_nota_fiscal()
    {
        $id = $this->input->post('id');
        $idMes = $this->input->post('id_mes');

        $data = $this->db
            ->select("arquivo_nota_fiscal_mes{$idMes} AS arquivo_nota_fiscal", false)
            ->select("validacao_nota_fiscal_mes{$idMes} AS validacao_nota_fiscal", false)
            ->where('id', $id)
            ->get('ei_pagamento_prestador')
            ->row();

        if (empty($data)) {
            exit(json_encode(['erro' => 'Arquivo de nota fiscal não encontrado.']));
        }

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_edit()
    {
        $this->load->model('ei_pagamento_prestador_model', 'pagamento_prestador');
        $idMes = $this->input->post('id_mes');

        $data = $this->pagamento_prestador
//            ->setTable($this->pagamento_prestador->getTable() . ' a')
            ->select("a.id, '{$idMes}' AS id_mes")
            ->select('b.ano, b.semestre, c.nome, c.cnpj')
            ->select('a.arquivo_nota_fiscal_mes' . $idMes . ' AS arquivo_nota_fiscal', false)
            ->select('a.mes_competencia_' . $idMes . ' AS mes_competencia', false)
            ->from('ei_pagamento_prestador a')
            ->join('ei_alocacao b', 'b.id = a.id_alocacao')
            ->join('usuarios c', 'c.id = a.id_cuidador')
            ->where('a.id', $this->input->post('id'))
            ->group_by('a.id')
            ->first();

        if (empty($data)) {
            exit(json_encode(['erro' => 'Arquivo de nota fiscal não encontrado.']));
        }

        $ext = strstr($data->arquivo_nota_fiscal, '.');
        $data->nome = str_replace(' ', '_', trim($data->nome));
        $data->cnpj = str_replace([' ', '.', '/'], ['_', '_', ''], trim($data->cnpj));
        $data->mascara_nota_fiscal = implode('-', ['EI', $data->ano . $data->id_mes, $data->nome, $data->cnpj]) . $ext;
        if ($data->semestre == '2') {
            $mesesCompetencia = [
                '1' => 'Julho',
                '2' => 'Agosto',
                '3' => 'Setembro',
                '4' => 'Outubro',
                '5' => 'Novembro',
                '6' => 'Dezembro',
            ];
        } else {
            $mesesCompetencia = [
                '1' => 'Janeiro',
                '2' => 'Fevereiro',
                '3' => 'Março',
                '4' => 'Abril',
                '5' => 'Maio',
                '6' => 'Junho',
                '7' => 'Julho',
            ];
        }
        $data->meses_competencia = form_dropdown('', ['' => 'selecione...'] + $mesesCompetencia, $data->mes_competencia);
        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_save()
    {
        $this->load->model('ei_pagamento_prestador_model', 'pagamento_prestador');
        $id = $this->input->post('id');
        $idMes = $this->input->post('id_mes');
        $mesCompetencia = $this->input->post('mes_competencia');
        $filename = $this->input->post('arquivo_nota_fiscal');
        if (strlen($filename) == 0) {
            exit(json_encode(['erro' => 'O nome do arquivo é obrigatório.']));
        }

        $uploadConfig = $this->pagamento_prestador->getUploadConfig();
        $filePath = $uploadConfig['arquivo_nota_fiscal_mes' . $idMes]['upload_path'] ?? null;

        $data = $this->pagamento_prestador->findOne($id);
        $oldFilename = $filePath . $data->{'arquivo_nota_fiscal_mes' . $idMes};
        if (file_exists($oldFilename) == false) {
            exit(json_encode(['erro' => 'Arquivo de nota fiscal anterior não pôde ser lido.']));
        }

        $ext = pathinfo($oldFilename, PATHINFO_EXTENSION);
        if (strpos($filename, $ext) === false) {
            $filename .= '.' . $ext;
        }
        if ($mesCompetencia == $idMes) {
            $data->{'arquivo_nota_fiscal_mes' . $idMes} = $filename;
        } else {
            $data->{'arquivo_nota_fiscal_mes' . $mesCompetencia} = $filename;
        }
        $newFilename = $filePath . $filename;

        $this->db->trans_begin();
        $this->pagamento_prestador->update($id, $data) or $this->pagamento_prestador->errors();
        if ($this->db->trans_status() == false) {
            $this->db->trans_rollback();
            exit(json_encode(['erro' => $this->pagamento_prestador->errors()]));
        }
        if (rename($oldFilename, $newFilename) == false) {
            $this->db->trans_rollback();
            exit(json_encode(['erro' => 'Arquivo de nota fiscal não pode ser salvo.']));
        }

        $this->db->trans_commit();

//        if ($mesCompetencia != $idMes) {
//            $this->db
//                ->set('arquivo_nota_fiscal_mes' . $idMes, null)
//                ->where('id', $id)
//                ->update('ei_pagamento_prestador');
//        }

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    public function ajax_save_validacao()
    {
        $this->load->model('ei_pagamento_prestador_model', 'pagamento_prestador');
        $id = $this->input->post('id');
        $idMes = $this->input->post('id_mes');
        $validacao = $this->input->post('validacao');
        if (strlen($validacao) == 0) {
            $validacao = null;
        }
        $data = $this->pagamento_prestador->findOrFail($id);
        $data->{'validacao_nota_fiscal_mes' . $idMes} = $validacao;
        echo $this->pagamento_prestador->updateOrFail($id, $data);
    }

    //--------------------------------------------------------------------

    public function ajax_delete()
    {
        $this->load->model('ei_pagamento_prestador_model', 'pagamento_prestador');
        $id = $this->input->post('id');
        $idMes = $this->input->post('id_mes');

        $data = $this->pagamento_prestador->findOne($id);
        $data->{'arquivo_nota_fiscal_mes' . $idMes} = null;

        $this->pagamento_prestador->update($id, $data) or $this->pagamento_prestador->errors();

        echo json_encode(['status' => true]);
    }

}
