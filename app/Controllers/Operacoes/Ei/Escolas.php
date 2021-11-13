<?php

namespace App\Controllers\Ei;

use App\Controllers\BaseController;

class Escolas extends BaseController
{

    public function index()
    {
        $this->gerenciar();
    }

    //--------------------------------------------------------------------

    public function gerenciar(string $idDiretoria = null)
    {
        $has_contrato = $this->db
            ->get_where('ei_contratos', ['id' => $idDiretoria])
            ->num_rows();

        if ($idDiretoria and !$has_contrato) {
            redirect(site_url('home'));
        }

        $empresa = $this->session->userdata('empresa');
        $id_usuario = $this->session->userdata('id');

        $data = [];

        $qb = $this->db
            ->select('DISTINCT(a.depto) AS nome', false)
            ->join('ei_escolas b', 'b.id_diretoria = a.id', 'left')
            ->join('ei_supervisores c', 'c.id_escola = b.id', 'left')
            ->where('a.id_empresa', $empresa);
        $data['depto'] = [];
        if (in_array($this->session->userdata('nivel'), [11])) {
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

        $qb = $this->db
            ->select('a.id, a.nome')
            ->join('ei_escolas b', 'b.id_diretoria = a.id', 'left')
            ->join('ei_supervisores c', 'c.id_escola = b.id', 'left')
            ->where('a.id_empresa', $empresa);
        if ($idDiretoria) {
            $qb->where('a.id', $idDiretoria);
        }
        if (in_array($this->session->userdata('nivel'), [11])) {
            $qb->where('c.id_supervisor', $id_usuario);
            $data['diretoria'] = [];
            $data['id_diretoria'] = [];
        } else {
            $data['diretoria'] = ['' => 'Todas'];
            $data['id_diretoria'] = ['' => 'selecione...'];
        }
        $diretorias = $qb
            ->group_by('a.id')
            ->order_by('a.nome', 'asc')
            ->get('ei_diretorias a')
            ->result();

        foreach ($diretorias as $diretoria) {
            $data['diretoria'][$diretoria->id] = $diretoria->nome;
            $data['id_diretoria'][$diretoria->id] = $diretoria->nome;
        }

        $qb = $this->db
            ->select('a.id, a.contrato AS nome')
            ->join('ei_diretorias b', 'b.id = a.id_cliente');
        if ($idDiretoria) {
            $qb->where('a.id', $idDiretoria);
        }
        if (in_array($this->session->userdata('nivel'), [11])) {
            $data['contrato'] = [];
            $data['id_contrato'] = [];
        } else {
            $data['contrato'] = ['' => 'Todos'];
            $data['id_contrato'] = ['' => 'selecione...'];
        }
        $contratos = $qb
            ->get('ei_contratos a')
            ->result();

        foreach ($contratos as $contrato) {
            $data['contrato'][$contrato->id] = $contrato->nome;
            $data['id_contrato'][$contrato->id] = $contrato->nome;
        }

        $qb = $this->db
            ->select('c.id_supervisor AS id, d.nome', false)
            ->join('ei_escolas b', 'b.id_diretoria = a.id')
            ->join('ei_supervisores c', 'c.id_escola = b.id')
            ->join('usuarios d', 'd.id = c.id_supervisor')
            ->where('a.id_empresa', $empresa);
        if ($idDiretoria) {
            $qb->where('a.id', $idDiretoria);
        }
        if (in_array($this->session->userdata('nivel'), [11])) {
            $qb->where('c.id_supervisor', $id_usuario);
            $data['supervisor'] = [];
        } else {
            $data['supervisor'] = ['' => 'Todos'];
        }
        $supervisores = $qb
            ->group_by('c.id_supervisor')
            ->order_by('d.nome', 'asc')
            ->get('ei_diretorias a')
            ->result();

        foreach ($supervisores as $supervisor) {
            $data['supervisor'][$supervisor->id] = $supervisor->nome;
        }

        $estados = $this->db
            ->order_by('uf', 'asc')
            ->get('estados')
            ->result();

        $data['estados'] = ['' => 'selecione...'] + array_column($estados, 'uf', 'cod_uf');

        $municipios = $this->db
            ->select('a.municipio AS nome')
            ->join('ei_diretorias b', 'b.id = a.id_diretoria')
            ->where('b.id_empresa', $empresa)
            ->where('a.municipio IS NOT NULL')
            ->group_by('a.municipio')
            ->order_by('a.municipio', 'asc')
            ->get('ei_escolas a')
            ->result();

        $data['municipio'] = ['' => 'Todos'] + array_column($municipios, 'municipio', 'municipio');

        $this->load->view('ei/escolas', $data);
    }

    //--------------------------------------------------------------------

    public function atualizar_filtro()
    {
        $empresa = $this->session->userdata('empresa');
        $id_usuario = $this->session->userdata('id');
        $busca = $this->input->post('busca');
        $filtro = [];

        $qb = $this->db
            ->select('a.municipio')
            ->join('ei_diretorias b', 'b.id = a.id_diretoria')
            ->where('b.id_empresa', $empresa)
            ->where('a.municipio IS NOT NULL');
        if ($busca['depto']) {
            $qb->where('b.depto', $busca['depto']);
        }
        if ($busca['diretoria']) {
            $qb->where('b.id', $busca['diretoria']);
        }
        $municipios = $qb
            ->group_by('a.municipio')
            ->order_by('a.municipio', 'asc')
            ->get('ei_escolas a')
            ->result();

        $filtro['municipio'] = ['' => 'Todos'] + array_column($municipios, 'municipio', 'municipio');

        $qb = $this->db
            ->select('a.id, a.nome')
            ->join('ei_escolas b', 'b.id_diretoria = a.id', 'left')
            ->join('ei_supervisores c', 'c.id_escola = b.id', 'left')
            ->where('a.id_empresa', $empresa);
        if (in_array($this->session->userdata('nivel'), [11])) {
            $qb->where('c.id_supervisor', $id_usuario);
            $filtro['diretoria'] = [];
        } else {
            $filtro['diretoria'] = ['' => 'Todas'];
        }
        $diretorias = $this->db
            ->group_by('a.id')
            ->order_by('a.nome', 'asc')
            ->get('ei_diretorias a')
            ->result();

        foreach ($diretorias as $diretoria) {
            $filtro['diretoria'][$diretoria->id] = $diretoria->nome;
        }

        $qb = $this->db
            ->select('c.id_supervisor AS id, d.nome', false)
            ->join('ei_escolas b', 'b.id_diretoria = a.id')
            ->join('ei_supervisores c', 'c.id_escola = b.id')
            ->join('usuarios d', 'd.id = c.id_supervisor')
            ->where('a.id_empresa', $empresa);
        if ($busca['diretoria']) {
            $qb->where('a.id', $busca['diretoria']);
        }
        if (in_array($this->session->userdata('nivel'), [11])) {
            $qb->where('c.id_supervisor', $id_usuario);
            $filtro['supervisor'] = [];
        } else {
            $filtro['supervisor'] = ['' => 'Todos'];
        }
        $supervisores = $qb
            ->group_by('c.id_supervisor')
            ->order_by('d.nome', 'asc')
            ->get('ei_diretorias a')
            ->result();

        foreach ($supervisores as $supervisor) {
            $filtro['supervisor'][$supervisor->id] = $supervisor->nome;
        }

        $data['municipio'] = form_dropdown('municipio', $filtro['municipio'], $busca['municipio'], 'onchange="atualizarFiltro()" class="form-control input-sm"');
        $data['diretoria'] = form_dropdown('diretoria', $filtro['diretoria'], $busca['diretoria'], 'onchange="atualizarFiltro()" class="form-control input-sm"');
        $data['supervisor'] = form_dropdown('supervisor', $filtro['supervisor'], $busca['supervisor'], 'onchange="atualizarFiltro()" class="form-control input-sm"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_list()
    {
        $post = $this->input->post();
        parse_str($this->input->post('busca'), $arrBusca);
        $busca = $arrBusca['busca'] ?? [];

        $id_diretoria = $this->input->post('id_diretoria');

        $sql = "SELECT s.diretoria,
                       s.municipio,
                       s.codigo,
                       s.nome,
                       s.id,
                       s.local
                FROM (SELECT a.id, 
                             b.nome AS diretoria,
                             a.municipio,
                             a.codigo,
                             a.nome,
                             d.nome AS supervisor,
                             e.local
                      FROM ei_escolas a
                      INNER JOIN ei_diretorias b ON
                                b.id = a.id_diretoria
                      LEFT JOIN ei_supervisores c ON 
                                c.id_escola = a.id
                      LEFT JOIN usuarios d ON
                                 d.id = c.id_supervisor
                      LEFT JOIN geolocalizacoes e ON 
                      			e.local = a.nome
                      WHERE b.id_empresa = {$this->session->userdata('empresa')}";
        if ($id_diretoria) {
            $sql .= " AND b.id = {$id_diretoria}";
        } elseif (!empty($busca['diretoria'])) {
            $sql .= " AND b.id = '{$busca['diretoria']}'";
        }
        if (!empty($busca['depto'])) {
            $sql .= " AND b.depto = '{$busca['depto']}'";
        }
        if (!empty($busca['municipio'])) {
            $sql .= " AND a.municipio = '" . addslashes($busca['municipio']) . "'";
        }
        if (!empty($busca['supervisor'])) {
            $sql .= " AND c.id_supervisor = '{$busca['supervisor']}'";
        }
        if (!empty($busca['contrato'])) {
            $sql .= " AND b.contrato = '{$busca['contrato']}'";
        }
        $sql .= ' GROUP BY a.id 
                  ORDER BY a.municipio ASC) s';
        $recordsTotal = $this->db->query($sql)->num_rows();

        $columns = ['s.id', 's.diretoria', 's.nome', 's.supervisor'];
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
            $btn = $ei->local ? 'success' : 'info';
            $row = [];
            $row[] = $ei->diretoria;
            $row[] = $ei->municipio;
            $row[] = $ei->codigo;
            $row[] = $ei->nome;
            $row[] = '
                      <button type="button" class="btn btn-sm btn-info" onclick="edit_escola(' . $ei->id . ')" title="Editar"><i class="glyphicon glyphicon-pencil"></i> </button>
                      <button type="button" class="btn btn-sm btn-danger" onclick="delete_escola(' . $ei->id . ')" title="Excluir"><i class="glyphicon glyphicon-trash"></i> </button>
                      <button type="button" class="btn btn-sm btn-' . $btn . '" onclick="exportar_geolocalizacao(' . $ei->id . ')" title="Exportar geolocalizacao">GEO</button>
                      <a class="btn btn-sm btn-primary" href="' . site_url('ei/alunos/gerenciar/' . $ei->id) . '" title="Gerenciar alunos">Alunos</a>
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
            ->get_where('ei_escolas', ['id' => $this->input->post('id')])
            ->row();

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_add()
    {
        $data = $this->input->post();
        if (strlen($data['codigo']) == 0) {
            $data['codigo'] = null;
        }
        if (empty($data['numero'])) {
            $data['numero'] = null;
        }
        if (empty($data['municipio'])) {
            $data['municipio'] = null;
        }
        if (empty($data['id_diretoria'])) {
            $data['id_diretoria'] = null;
        }
        if (empty($data['geolocalizacao_1'])) {
            $data['geolocalizacao_1'] = null;
        }
        if (empty($data['geolocalizacao_2'])) {
            $data['geolocalizacao_2'] = null;
        }
        if (empty($data['nome_diretor'])) {
            $data['nome_diretor'] = null;
        }
        if (empty($data['email_diretor'])) {
            $data['email_diretor'] = null;
        }
        if (empty($data['nome_coordenador'])) {
            $data['nome_coordenador'] = null;
        }
        if (empty($data['email_coordenador'])) {
            $data['email_coordenador'] = null;
        }
        if (empty($data['nome_administrativo'])) {
            $data['nome_administrativo'] = null;
        }
        if (empty($data['email_administrativo'])) {
            $data['email_administrativo'] = null;
        }
        if (empty($data['unidade_apoio_1'])) {
            $data['unidade_apoio_1'] = null;
        }
        if (empty($data['codigo_apoio_1'])) {
            $data['codigo_apoio_1'] = null;
        }
        if (empty($data['unidade_apoio_2'])) {
            $data['unidade_apoio_2'] = null;
        }
        if (empty($data['codigo_apoio_2'])) {
            $data['codigo_apoio_2'] = null;
        }
        if (empty($data['unidade_apoio_3'])) {
            $data['unidade_apoio_3'] = null;
        }
        if (empty($data['codigo_apoio_3'])) {
            $data['codigo_apoio_3'] = null;
        }
        $status = $this->db->insert('ei_escolas', $data);
        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_update()
    {
        $data = $this->input->post();
        $id = $data['id'];
        unset($data['id']);
        if (strlen($data['codigo']) == 0) {
            $data['codigo'] = null;
        }
        if (empty($data['numero'])) {
            $data['numero'] = null;
        }
        if (empty($data['municipio'])) {
            $data['municipio'] = null;
        }
        if (empty($data['id_diretoria'])) {
            $data['id_diretoria'] = null;
        }
        if (empty($data['geolocalizacao_1'])) {
            $data['geolocalizacao_1'] = null;
        }
        if (empty($data['geolocalizacao_2'])) {
            $data['geolocalizacao_2'] = null;
        }
        if (empty($data['periodo_manha'])) {
            $data['periodo_manha'] = null;
        }
        if (empty($data['pessoas_contato'])) {
            $data['pessoas_contato'] = null;
        }
        if (empty($data['periodo_tarde'])) {
            $data['periodo_tarde'] = null;
        }
        if (empty($data['periodo_noite'])) {
            $data['periodo_noite'] = null;
        }
        if (empty($data['nome_diretor'])) {
            $data['nome_diretor'] = null;
        }
        if (empty($data['email_diretor'])) {
            $data['email_diretor'] = null;
        }
        if (empty($data['nome_coordenador'])) {
            $data['nome_coordenador'] = null;
        }
        if (empty($data['email_coordenador'])) {
            $data['email_coordenador'] = null;
        }
        if (empty($data['nome_administrativo'])) {
            $data['nome_administrativo'] = null;
        }
        if (empty($data['email_administrativo'])) {
            $data['email_administrativo'] = null;
        }
        if (empty($data['unidade_apoio_1'])) {
            $data['unidade_apoio_1'] = null;
        }
        if (empty($data['codigo_apoio_1'])) {
            $data['codigo_apoio_1'] = null;
        }
        if (empty($data['unidade_apoio_2'])) {
            $data['unidade_apoio_2'] = null;
        }
        if (empty($data['codigo_apoio_2'])) {
            $data['codigo_apoio_2'] = null;
        }
        if (empty($data['unidade_apoio_3'])) {
            $data['unidade_apoio_3'] = null;
        }
        if (empty($data['codigo_apoio_3'])) {
            $data['codigo_apoio_3'] = null;
        }
        $status = $this->db->update('ei_escolas', $data, ['id' => $id]);
        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_delete()
    {
        $status = $this->db->delete('ei_escolas', ['id' => $this->input->post('id')]);
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
            ->where('id', $empresa)
            ->get('usuarios')
            ->row();

        $depto = $this->input->get('depto');
        $diretoria = $this->input->get('diretoria');
        $municipio = $this->input->get('municipio');
        $supervisor = $this->input->get('supervisor');
        $order = $this->input->get('order');
        $search = $this->input->get('search');

        $qb = $this->db
            ->select('b.nome AS diretoria, a.municipio, a.codigo, a.nome')
            ->join('ei_diretorias b', 'b.id = a.id_diretoria')
            ->join('ei_supervisores c', 'c.id_escola = a.id', 'left')
            ->join('usuarios d', 'd.id = c.id_supervisor', 'left')
            ->where('b.id_empresa', $empresa)
            ->group_by('a.id');
        if ($search) {
            $qb->group_start()
                ->like('b.id', $search)
                ->or_like('b.depto', $search)
                ->or_like('a.municipio', $search)
                ->or_like('c.id_supervisor', $search)
                ->or_like('b.contrato', $search)
                ->group_end();
        }
        foreach ($order as $value) {
            $qb->order_by($value[0], $value[1]);
        }
        $subquery = $qb->get_compiled_select('ei_escolas a');

        $sql = "SELECT s.diretoria,
					   NULL,
                       s.municipio,
                       CONCAT(s.codigo, ' - ', s.nome) AS escola
                FROM ({$subquery}) s";
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
                    <h1 style="font-weight: bold;">RELAÇÃO CLIENTE x MUNICÍPIO x ESCOLAS</h1>
                </td>
            </tr>
            </tbody>
        </table>
        <br><br>';

        $table = [['Cliente', '', 'Município', 'Escola']];
        foreach ($data as $row) {
            $table[] = $row;
        }
        $this->load->library('table');

        $html = $cabecalho . $this->table->generate($table);

        $this->m_pdf->pdf->writeHTML($html);

        $this->m_pdf->pdf->Output("EI_escolas.pdf", 'D');
    }

    //--------------------------------------------------------------------

    public function exportar_geolocalizacao()
    {
        $escola = $this->db
            ->where('id', $this->input->post('id'))
            ->get('ei_escolas')
            ->row();

        $geolocalizacao1 = explode(',', $escola->geolocalizacao_1);
        $geolocalizacao2 = explode(',', $escola->geolocalizacao_2);

        $locais = [[
            'latitude' => !empty($geolocalizacao1[0]) ? trim($geolocalizacao1[0]) : null,
            'longitude' => isset($geolocalizacao1[1]) ? trim($geolocalizacao1[1]) : null,
        ]];

        if (!empty($geolocalizacao2[0]) or isset($geolocalizacao2[1])) {
            $locais[] = [
                'latitude' => isset($geolocalizacao2[0]) ? trim($geolocalizacao2[0]) : null,
                'longitude' => isset($geolocalizacao2[1]) ? trim($geolocalizacao2[1]) : null,
            ];
        }

        $cidade = $this->db
            ->select('cod_mun')
            ->where('cod_uf', $escola->id_estado)
            ->like('municipio', $escola->municipio)
            ->get('municipios')
            ->row();

        $data = [
            'id_empresa' => $this->session->userdata('empresa'),
            'local' => $escola->nome,
            'endereco' => $escola->endereco,
            'numero' => $escola->numero,
            'bairro' => $escola->bairro,
            'id_cidade' => $cidade->cod_mun ?? null,
            'id_estado' => $escola->id_estado,
        ];

        $rows = [];

        foreach ($locais as $local) {
            $data['latitude'] = $local['latitude'];
            $data['longitude'] = $local['longitude'];
            $rows[] = $data;
        }

        foreach ($rows as $row) {
            $this->db->insert('geolocalizacoes', $row);
        }

        echo json_encode(['status' => true]);
    }

}
