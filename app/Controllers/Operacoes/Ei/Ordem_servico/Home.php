<?php

namespace App\Controllers\Ei\Ordem_servico;

use App\Controllers\BaseController;

class Home extends BaseController
{

    public function index()
    {
        $filtro = $this->montarEstrutura();

        $data = [
            'id_diretoria' => ['' => 'selecione...'] + $filtro['diretoria'],
            'id_contrato' => ['' => 'selecione...'] + $filtro['contrato'],
            'id_escola' => ['' => 'selecione...'],
            'id_curso' => ['' => 'selecione...'],
            'diretorias' => ['' => 'Todas'] + $filtro['diretoria'],
            'contratos' => ['' => 'Todos'] + $filtro['contrato'],
            'anoSemestres' => ['' => 'Todos'] + $filtro['ano_semestre'],
            'ordensServico' => ['' => 'Todas'] + $filtro['ordem_servico'],
            'municipios' => ['' => 'selecione...'] + $filtro['municipio'],
            'escolas' => ['' => 'selecione...'] + $filtro['escola'],
        ];

        $this->load->view('ei/ordem_servico', $data);
    }

    //--------------------------------------------------------------------

    public function atualizar_filtro(array $busca = [])
    {
        $retorno = count($busca);
        if (empty($busca)) {
            $busca = $this->input->post();
        }

        $filtro = $this->montarEstrutura();
        $contratos = ['' => 'Todos'] + $filtro['contrato'];
        $anosSemestres = ['' => 'Todos'] + $filtro['ano_semestre'];
        $ordemServicos = ['' => 'Todas'] + $filtro['ordem_servico'];
        $municipios = ['' => 'Todos'] + $filtro['municipio'];

        $data['contrato'] = form_dropdown('busca[contrato]', $contratos, $busca['contrato'], 'onchange="atualizarFiltro()" class="form-control input-sm filtro"');
        $data['ano_semestre'] = form_dropdown('busca[ano_semestre]', $anosSemestres, $busca['ano_semestre'], 'onchange="atualizarFiltro()" class="form-control input-sm filtro"');
        $data['ordem_servico'] = form_dropdown('busca[ordem_servico]', $ordemServicos, $busca['ordem_servico'], 'onchange="atualizarFiltro()" class="form-control input-sm filtro"');
        $data['municipio'] = form_dropdown('busca[unicipio]', $municipios, $busca['municipio'], 'onchange="atualizarFiltro()" class="form-control input-sm filtro"');

        if ($retorno) {
            return $data;
        }

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function atualizar_escolas()
    {
        $idOrdemServico = $this->input->post('id_ordem_servico');
        $municipio = $this->input->post('municipio');
        $escolasSelecionadas = $this->input->post('escolas');

        $qb = $this->db
            ->select(["a.id, CONCAT_WS(' - ', a.codigo, a.nome) AS nome"], false)
            ->join('ei_diretorias b', 'b.id = a.id_diretoria')
            ->join('ei_contratos c', 'c.id_cliente = b.id', 'left')
            ->join('ei_ordem_servico d', "d.id_contrato = c.id AND d.id = {$idOrdemServico}", 'left')
            ->where('b.id_empresa', $this->session->userdata('empresa'));
        if ($municipio) {
            $qb->where('a.municipio', $municipio);
        }
        if ($escolasSelecionadas) {
            $qb->or_where_in('a.id', $escolasSelecionadas);
        }
        $escolas = $qb
            ->order_by('a.codigo', 'asc')
            ->order_by('a.nome', 'asc')
            ->get('ei_escolas a')
            ->result();

        $escolas = array_column($escolas, 'nome', 'id');

        $data['escola'] = form_multiselect('id_escola[]', $escolas, $escolasSelecionadas, 'id="escola" class="demo1" size="8"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function filtrar_escolas_selecionadas()
    {
        $post = $this->input->post();

        $qb = $this->db
            ->select('a.id')
            ->join('ei_diretorias b', 'b.id = a.id_diretoria')
            ->join('ei_contratos c', 'c.id_cliente = b.id', 'left')
            ->join('ei_ordem_servico d', "d.id_contrato = c.id AND d.id = {$post['id_ordem_servico']}", 'left')
            ->where('b.id_empresa', $this->session->userdata('empresa'));
        if ($post['municipio']) {
            $qb->where('a.municipio', $post['municipio']);
        }
        $rows = $qb
            ->where_in('a.id', $post['escolas'])
            ->get('ei_escolas a')
            ->result();

        $data['escolas'] = array_column($rows, 'id');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function atualizar_contratos()
    {
        $busca['diretoria'] = $this->input->post('id_diretoria');
        $contrato = $this->input->post('id_contrato');
        $options = ['' => 'selecione...'] + $this->montarEstrutura($busca)['contrato'];

        $data['contrato'] = form_dropdown('id_contrato', $options, $contrato, 'id="contrato" class="form-control"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    private function montarEstrutura(array $busca = []): array
    {
        $empresa = $this->session->userdata('empresa');
        if (empty($busca)) {
            $busca = $this->input->post('busca');
        }

        $diretorias = $this->db
            ->select('id, nome')
            ->where('id_empresa', $empresa)
            ->order_by('nome', 'asc')
            ->get('ei_diretorias')
            ->result();

        $data['diretoria'] = array_column($diretorias, 'nome', 'id');

        $qb = $this->db->select('a.id, a.contrato')
            ->join('ei_diretorias b', 'b.id = a.id_cliente')
            ->where('b.id_empresa', $empresa);
        if (!empty($busca['diretoria'])) {
            $qb->where('b.id', $busca['diretoria']);
        }
        $contratos = $qb
            ->order_by('a.contrato', 'asc')
            ->get('ei_contratos a')
            ->result();

        $data['contrato'] = array_column($contratos, 'contrato', 'id');

        $qb = $this->db
            ->select('a.id, a.nome')
            ->select("CONCAT(a.ano, '/', a.semestre) AS ano_semestre", false)
            ->join('ei_contratos b', 'b.id = a.id_contrato')
            ->join('ei_diretorias c', 'c.id = b.id_cliente')
            ->where('c.id_empresa', $empresa);
        if (!empty($busca['diretoria'])) {
            $qb->where('c.id', $busca['diretoria']);
        }
        if (!empty($busca['contrato'])) {
            $qb->where('b.id', $busca['contrato']);
        }
        $anoSemestre = $qb
            ->group_by(['a.ano', 'a.semestre'])
            ->order_by('a.ano', 'desc')
            ->order_by('a.semestre', 'desc')
            ->get('ei_ordem_servico a')
            ->result();

        $data['ano_semestre'] = array_column($anoSemestre, 'ano_semestre', 'ano_semestre');

        $qb = $this->db
            ->select('a.id, a.nome')
            ->join('ei_contratos b', 'b.id = a.id_contrato')
            ->join('ei_diretorias c', 'c.id = b.id_cliente')
            ->where('c.id_empresa', $empresa);
        if (!empty($busca['diretoria'])) {
            $qb->where('c.id', $busca['diretoria']);
        }
        if (!empty($busca['contrato'])) {
            $qb->where('b.id', $busca['contrato']);
        }
        if (!empty($busca['anoSemestre'])) {
            $qb->where("CONCAT(a.ano, '/', a.semestre) =", $busca['anoSemestre']);
        }
        $ordemServico = $qb
            ->order_by('a.nome', 'asc')
            ->get('ei_ordem_servico a')
            ->result();

        $data['ordem_servico'] = array_column($ordemServico, 'nome', 'id');

        $qb = $this->db
            ->select('a.municipio')
            ->join('ei_ordem_servico_escolas b', 'b.id_escola = a.id')
            ->join('ei_ordem_servico c', 'c.id = b.id_ordem_servico')
            ->join('ei_contratos d', 'd.id = c.id_contrato')
            ->join('ei_diretorias e', 'e.id = d.id_cliente AND a.id_diretoria = e.id')
            ->where('e.id_empresa', $empresa);
        if (!empty($busca['diretoria'])) {
            $qb->where('e.id', $busca['diretoria']);
        }
        if (!empty($busca['contrato'])) {
            $qb->where('d.id', $busca['contrato']);
        }
        if (!empty($busca['anoSemestre'])) {
            $qb->where("CONCAT(c.ano, '/', c.semestre) =", $busca['anoSemestre']);
        }
        $municipios = $qb
            ->group_by('a.municipio')
            ->order_by('a.municipio', 'asc')
            ->get('ei_escolas a')
            ->result();

        $data['municipio'] = array_column($municipios, 'municipio', 'municipio');

        $qb = $this->db
            ->select('a.id, a.nome')
            ->join('ei_ordem_servico_escolas b', 'b.id_escola = a.id')
            ->join('ei_ordem_servico c', 'c.id = b.id_ordem_servico')
            ->join('ei_contratos d', 'd.id = c.id_contrato')
            ->join('ei_diretorias e', 'e.id = d.id_cliente AND a.id_diretoria = e.id')
            ->where('e.id_empresa', $empresa);
        if (!empty($busca['diretoria'])) {
            $qb->where('e.id', $busca['diretoria']);
        }
        if (!empty($busca['contrato'])) {
            $qb->where('d.id', $busca['contrato']);
        }
        if (!empty($busca['municipio'])) {
            $qb->where('a.municipio', $busca['municipio']);
        }
        $escolas = $qb
            ->order_by('a.nome', 'asc')
            ->get('ei_escolas a')
            ->result();

        $data['escola'] = array_column($escolas, 'nome', 'id');

        return $data;
    }

    //--------------------------------------------------------------------

    public function ajax_list()
    {
        $post = $this->input->post();
        parse_str($this->input->post('busca'), $busca);

        $sql = "SELECT s.contrato, 
                       s.ordem_servico, 
                       s.ano_semestre, 
                       s.id_os_escola,
                       s.ordem_escola,
                       s.escola, 
                       s.id_escola,
                       s.id
                FROM (SELECT a.id,
                             b.contrato,
                             a.nome AS ordem_servico,
                             CONCAT(a.ano, '/', a.semestre) AS ano_semestre,
                             d.id AS id_os_escola,
                             d.id_escola,
                             CONCAT_WS(' - ', e.codigo, e.nome) AS escola,
                             IF(CHAR_LENGTH(e.codigo) > 0, e.codigo, CAST(e.nome AS DECIMAL)) AS ordem_escola
                      FROM ei_ordem_servico a 
                      INNER JOIN ei_contratos b 
                                 ON b.id = a.id_contrato
                      INNER JOIN ei_diretorias c
                                 ON c.id = b.id_cliente
                      LEFT JOIN ei_ordem_servico_escolas d 
                                ON d.id_ordem_servico = a.id
                      LEFT JOIN ei_escolas e
                                ON e.id = d.id_escola
                                AND e.id_diretoria = c.id
                      WHERE c.id_empresa = '{$this->session->userdata('empresa')}'";
        if ($busca['diretoria']) {
            $sql .= " AND c.id = '{$busca['diretoria']}'";
        }
        if ($busca['contrato']) {
            $sql .= " AND b.id = '{$busca['contrato']}'";
        }
        if ($busca['ano_semestre']) {
            $sql .= " AND CONCAT(a.ano, '/', a.semestre) = '{$busca['ano_semestre']}'";
        }
        if ($busca['ordem_servico']) {
            $sql .= " AND a.id = '{$busca['ordem_servico']}'";
        }
        if ($busca['municipio']) {
            $sql .= " AND e.municipio = '" . addslashes($busca['municipio']) . "'";
        }
        if ($busca['escola']) {
            $sql .= " AND e.id = '" . addslashes($busca['escola']) . "'";
        }
        $sql .= ') s
        ORDER BY s.contrato ASC, 
                 s.ordem_servico ASC, 
                 s.ano_semestre ASC, 
                 s.ordem_escola ASC';

        $this->load->library('dataTables');
        $output = $this->datatables->query($sql);

        $idEscolas = array_unique(array_column($output->data, 'id_escola'));

        $totalAlunos = $this->db
            ->select('DISTINCT(id_aluno) AS id_aluno', false)
            ->where_in('id_ordem_servico_escola', array_column($output->data, 'id_os_escola') + [0])
            ->get('ei_ordem_servico_alunos')
            ->num_rows();

        $data = [];
        foreach ($output->data as $ei) {
            $row = [];
            $row[] = $ei->contrato;
            $row[] = $ei->ordem_servico;
            $row[] = $ei->ano_semestre;
            $row[] = '
                      <button type="button" class="btn btn-sm btn-info" onclick="edit_os(' . $ei->id . ')" title="Editar área/cliente"><i class="glyphicon glyphicon-pencil"></i> </button>
                      <button type="button" class="btn btn-sm btn-danger" onclick="delete_os(' . $ei->id . ')" title="Excluir área/cliente"><i class="glyphicon glyphicon-trash"></i> </button>
                      <button type="button" class="btn btn-sm btn-info" onclick="gerenciar_escolas(' . $ei->id . ')" title="Gerenciar unidade de ensino">Escolas</button>
                     ';
            $row[] = $ei->escola;
            if ($ei->id_os_escola) {
                $row[] = '
                          <button type="button" class="btn btn-sm btn-info" onclick="edit_escola(' . $ei->id_os_escola . ')" title="Editar escola"><i class="glyphicon glyphicon-pencil"></i></button>
                          <button type="button" class="btn btn-sm btn-primary" onclick="alunos(' . $ei->id_os_escola . ')" title="Gerenciar alunos">Alunos</button>
                          <button type="button" class="btn btn-sm btn-primary" onclick="profissionais(' . $ei->id_os_escola . ')" title="Gerenciar profissionais">Cuidadores/Horários</button>
                          <!-- <button type="button" class="btn btn-sm btn-primary" title="Relatorio"><i class="glyphicon glyphicon-print"></i></button> -->
                         ';
            } else {
                $row[] = '
                          <button type="button" class="btn btn-sm btn-info disabled" title="Editar escola"><i class="glyphicon glyphicon-pencil"></i></button>
                          <button type="button" class="btn btn-sm btn-primary disabled" title="Gerenciar aluno">Alunos</button>
                          <button type="button" class="btn btn-sm btn-primary disabled" title="Gerenciar profissionais">Cuidadores/Horários</button>
                          <!-- <button type="button" class="btn btn-sm btn-primary disabled" title="Relatorio"><i class="glyphicon glyphicon-print"></i></button> -->
                         ';
            }

            $data[] = $row;
        }

        $output->data = $data;

        $output->total_escolas = count($idEscolas);
        $output->total_alunos = $totalAlunos;

        echo json_encode($output);
    }

    //--------------------------------------------------------------------

    public function ajax_edit()
    {
        $id = $this->input->post('id');

        $data = $this->db
            ->select('a.*, c.id AS diretoria', false)
            ->join('ei_contratos b', 'b.id = a.id_contrato')
            ->join('ei_diretorias c', 'c.id = b.id_cliente')
            ->get_where('ei_ordem_servico a', ['a.id' => $id])
            ->row();

        $contratos = $this->db
            ->select('id, contrato')
            ->where('id', $data->diretoria)
            ->get('ei_contratos')
            ->result();

        $contratos = array_column($contratos, 'contrato', 'id');

        $data->contrato = form_dropdown('id_contrato', $contratos, $data->id_contrato, 'id="contrato" class="form-control"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function gerenciar_escolas()
    {
        $id = $this->input->post('id');

        $data = $this->db
            ->select('a.*, b.contrato, c.id AS id_diretoria, c.nome AS diretoria', false)
            ->select("CONCAT(a.ano, '/', a.semestre) AS ano_semestre", false)
            ->join('ei_contratos b', 'b.id = a.id_contrato')
            ->join('ei_diretorias c', 'c.id = b.id_cliente')
            ->where('a.id', $id)
            ->get('ei_ordem_servico a')
            ->row();

        $escolas = $this->db
            ->select(["a.id, CONCAT_WS(' - ', a.codigo, a.nome) AS nome"], false)
            ->join('ei_diretorias c', 'c.id = a.id_diretoria')
            ->where('c.id', $data->id_diretoria)
            ->order_by('a.codigo', 'asc')
            ->order_by('a.nome', 'asc')
            ->get('ei_escolas a')->result();

        $escolas = array_column($escolas, 'nome', 'id');

        $escolasSelecionadas = $this->db
            ->select('id_escola')
            ->where('id_ordem_servico', $id)
            ->get('ei_ordem_servico_escolas')
            ->result();

        $escolasSelecionadas = array_column($escolasSelecionadas, 'id_escola');

        $data->escola = form_multiselect('id_escola[]', $escolas, $escolasSelecionadas, 'id="escola" class="demo1" size="8"');

        $municipios = $this->db
            ->select('a.municipio')
            ->join('ei_diretorias b', 'b.id = a.id_diretoria')
            ->where('b.id', $data->id_diretoria)
            ->where('a.municipio IS NOT NULL')
            ->group_by('a.municipio')
            ->order_by('a.municipio', 'asc')
            ->get('ei_escolas a')
            ->result();

        $municipios = ['' => 'Todos'] + array_column($municipios, 'municipio', 'municipio');

        $data->municipio = form_dropdown('', $municipios, '', 'id="municipio" class="demo1" size="8"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function editar_escola()
    {
        $id = $this->input->post('id');
        $selecione = ['' => 'selecione...'];

        $osEscola = $this->db
            ->select('a.id, a.id_escola, a.id_ordem_servico, c.id_contrato, d.id_cliente')
            ->select('b.codigo, b.nome, c.nome AS nome_os, d.contrato, e.nome AS diretoria', false)
            ->select(["CONCAT(c.ano, '/', c.semestre) AS ano_semestre"], false)
            ->join('ei_escolas b', 'b.id = a.id_escola')
            ->join('ei_ordem_servico c', 'c.id = a.id_ordem_servico')
            ->join('ei_contratos d', 'd.id = c.id_contrato')
            ->join('ei_diretorias e', 'e.id = d.id_cliente')
            ->where('a.id', $id)
            ->get('ei_ordem_servico_escolas a')
            ->row();

        $contratos = $this->db
            ->select('id, contrato')
            ->where('id_cliente', $osEscola->id_cliente)
            ->order_by('contrato', 'asc')
            ->get('ei_contratos')
            ->result();

        $contratos = $selecione + array_column($contratos, 'contrato', 'id');

        $texto = implode('<br>', [
            implode(' - ', [$osEscola->codigo, $osEscola->nome]),
            $osEscola->diretoria,
            $osEscola->contrato,
            $osEscola->nome_os,
            $osEscola->ano_semestre,
        ]);

        $data = [
            'id' => $id,
            'texto_escola' => $texto,
            'contratos' => form_dropdown('', $contratos),
            'ano_semestre' => form_dropdown('', $selecione),
            'ordens_servico' => form_dropdown('', $selecione),
        ];

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function montar_dados_escola()
    {
        $idContrato = $this->input->post('id_contrato');
        $idAnoSemestre = $this->input->post('ano_semestre');
        if ($idAnoSemestre) {
            list($ano, $semestre) = explode('/', $idAnoSemestre);
        } else {
            $ano = null;
            $semestre = null;
        }

        $idOrdemServico = $this->input->post('id_ordem_servico');
        $selecione = ['' => 'selecione...'];

        $anoSemestre = $this->db
            ->select(["CONCAT(ano, '/', semestre) AS ano_semestre"], false)
            ->where('id_contrato', $idContrato)
            ->get('ei_ordem_servico')
            ->result();

        $anoSemestre = array_column($anoSemestre, 'ano_semestre', 'ano_semestre');
        asort($anoSemestre);
        $anoSemestre = $selecione + $anoSemestre;

        $ordensServico = $this->db
            ->select('a.id, a.nome')
            ->join('ei_contratos b', 'b.id = a.id_contrato')
            ->where('b.id', $idContrato)
            ->where('a.ano', $ano)
            ->where('a.semestre', $semestre)
            ->order_by('a.nome', 'asc')
            ->get('ei_ordem_servico a')
            ->result();

        $ordensServico = $selecione + array_column($ordensServico, 'nome', 'id');

        $data = [
            'ano_semestre' => form_dropdown('', $anoSemestre, $idAnoSemestre),
            'ordens_servico' => form_dropdown('', $ordensServico, $idOrdemServico),
        ];

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_add()
    {
        $data = $this->input->post();
        $status = $this->db->insert('ei_ordem_servico', $data);

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_add_escola()
    {
        $idOrdemServico = $this->input->post('id_ordem_servico');
        $idEscolas = $this->input->post('id_escola');
        if (is_null($idEscolas)) {
            $idEscolas = [];
        }

        $this->db->trans_start();

        $qb = $this->db->where('id_ordem_servico', $idOrdemServico);
        if ($idEscolas) {
            $qb->where_not_in('id_escola', $idEscolas);
        }
        $qb->delete('ei_ordem_servico_escolas');

        $rows = $this->db
            ->select('id_escola')
            ->where('id_ordem_servico', $idOrdemServico)
            ->get('ei_ordem_servico_escolas')
            ->result();

        $rows = array_column($rows, 'id_escola');
        $idEscolas = array_diff($idEscolas, $rows);

        $data = [];
        foreach ($idEscolas as $idEscola) {
            $data[] = [
                'id_ordem_servico' => $idOrdemServico,
                'id_escola' => $idEscola,
            ];
        }
        if ($data) {
            $this->db->insert_batch('ei_ordem_servico_escolas', $data);
        }

        $this->db->update('ei_ordem_servico', ['escolas_nao_cadastradas' => $this->input->post('escolas_nao_cadastradas')], ['id' => $idOrdemServico]);

        $this->db->trans_complete();

        $status = $this->db->trans_status();

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function salvar_escola()
    {
        $id = $this->input->post('id');
        $idOrdemServico = $this->input->post('id_ordem_servico');

        $osEscola = $this->db
            ->where('id', $id)
            ->get('ei_ordem_servico_escolas')
            ->row();

        if (empty($osEscola)) {
            exit(json_encode(['erro' => 'Unidade de ensino não encontrada ou excluída recentemente.']));
        }

        $novaOS = $this->db
            ->select('a.id, b.id_escola')
            ->join('ei_ordem_servico_escolas b', "b.id_ordem_servico = a.id AND a.id != '{$osEscola->id_ordem_servico}' AND b.id_escola = '{$osEscola->id_escola}'", 'left')
            ->where('a.id', $idOrdemServico)
            ->get('ei_ordem_servico a')
            ->row();

        if (empty($novaOS)) {
            exit(json_encode(['erro' => 'Nova Ordem de Serviço não encontrada ou excluída recentemente.']));
        } elseif ($novaOS->id_escola) {
            exit(json_encode(['erro' => 'Unidade de Ensino já existente na nova Ordem de Serviço.']));
        }

        $this->db->trans_start();
        $this->db->update('ei_ordem_servico_escolas', ['id_ordem_servico' => $idOrdemServico], ['id' => $id]);
        $this->db->trans_complete();

        $status = $this->db->trans_status();

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function importar_escolas()
    {
        if (!(isset($_FILES) && !empty($_FILES) && empty($_FILES['arquivo']['error']))) {
            exit(json_encode(['erro' => 'Erro no envio do arquivo. Tente mais tarde.']));
        }

        $config = [
            'upload_path' => './arquivos/ei/csv/',
            'file_name' => utf8_decode($_FILES['arquivo']['name']),
            'allowed_types' => '*',
            'overwrite' => true,
        ];

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('arquivo')) {
            exit(json_encode(['erro' => $this->upload->display_errors()]));
        }

        $csv = $this->upload->data();
        $handle = fopen($config['upload_path'] . $csv['file_name'], "r");

        $label = array_flip([
            'codigoescola', 'escola', 'codigoaluno', 'nomealuno', 'deficiencia', 'codigocurso',
            'curso', 'horario', 'segundai', 'segundaf', 'tercai', 'tercaf', 'quartai', 'quartaf',
            'quintai', 'quintaf', 'sextai', 'sextaf', 'sabadoi', 'sabadof', 'codsupervisor',
            'idprofissional', 'tipoprofissional', 'profissional', 'datainicio', 'datatermino',
            'modulo', 'valorhora', 'horasdia', 'horassemana', 'horasmes', 'l1', 'l2', 'l3', 'l4',
            'valorhorasmes', 'valorhorap', 'Dias', 'Total de Horas', 'Total', 'OS',
        ]);

        $ordemServico = $this->db
            ->select('a.id, b.id_cliente AS id_diretoria')
            ->join('ei_contratos b', 'b.id = a.id_contrato')
            ->where('a.id', $this->input->post('id_ordem_servico'))
            ->get('ei_ordem_servico a')
            ->row();

        $this->db->trans_begin();

        $x = 0;
        $status = 0;

        $arrEscolasNaoCadastradas = [];

        while (($row = fgetcsv($handle, 1850, ";")) !== false) {
            $x++;
            if ($x == 1) {
                if (count(array_filter($row)) == count($label)) {
                    $label = array_combine($row, array_keys($row));
                }
                continue;
            }

            $osEscola = $this->db
                ->select("'{$ordemServico->id}' AS id_ordem_servico", false)
                ->select('c.id, a.id AS id_escola', false)
                ->join('ei_ordem_servico_escolas c', "c.id_escola = a.id AND c.id_ordem_servico = '{$ordemServico->id}'", 'left')
                ->where('a.id_diretoria', $ordemServico->id_diretoria)
                ->where('a.codigo', $row[$label['codigoescola']])
                ->get('ei_escolas a')
                ->row_array();

            if (empty($osEscola)) {
                $arrEscolasNaoCadastradas[] = ($row[$label['codigoescola']] . ' - ' . $row[$label['escola']]);
            } elseif (empty($osEscola['id'])) {
                $this->db->insert('ei_ordem_servico_escolas', $osEscola);
            }

            if ($this->db->trans_status() == true) {
                $status++;
            }
        }

        $escolasNaoCadastradas = implode(chr(10), $arrEscolasNaoCadastradas);
        if (strlen($escolasNaoCadastradas) == 0) {
            $escolasNaoCadastradas = null;
        }

        if (empty($status)) {
            $this->db->trans_rollback();
            exit(json_encode(['erro' => 'Erro ao importar as escolas']));
        }

        $this->db->update('ei_ordem_servico', ['escolas_nao_cadastradas' => $escolasNaoCadastradas], ['id' => $ordemServico->id]);

        $this->db->trans_commit();

        fclose($handle);

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    public function importar_alunos()
    {
        if (!(isset($_FILES) && !empty($_FILES) && empty($_FILES['arquivo']['error']))) {
            exit(json_encode(['erro' => 'Erro no envio do arquivo. Tente mais tarde.']));
        }

        $config = [
            'upload_path' => './arquivos/ei/csv/',
            'file_name' => utf8_decode($_FILES['arquivo']['name']),
            'allowed_types' => '*',
            'overwrite' => true,
        ];

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('arquivo')) {
            exit(json_encode(['erro' => $this->upload->display_errors()]));
        }

        $csv = $this->upload->data();
        $handle = fopen($config['upload_path'] . $csv['file_name'], "r");

        $label = array_flip([
            'codigoescola', 'escola', 'codigoaluno', 'nomealuno', 'deficiencia', 'codigocurso',
            'curso', 'horario', 'segundai', 'segundaf', 'tercai', 'tercaf', 'quartai', 'quartaf',
            'quintai', 'quintaf', 'sextai', 'sextaf', 'sabadoi', 'sabadof', 'codsupervisor',
            'idprofissional', 'tipoprofissional', 'profissional', 'datainicio', 'datatermino',
            'modulo', 'valorhora', 'horasdia', 'horassemana', 'horasmes', 'l1', 'l2', 'l3', 'l4',
            'valorhorasmes', 'valorhorap', 'Dias', 'Total de Horas', 'Total', 'OS',
        ]);

        $ordemServico = $this->db
            ->select('a.id, a.ano, b.id_cliente AS id_diretoria')
            ->join('ei_contratos b', 'b.id = a.id_contrato')
            ->where('a.id', $this->input->post('id_ordem_servico'))
            ->get('ei_ordem_servico a')
            ->row();

        $this->db->trans_begin();

        $x = 0;
        $status = 0;

        $meses = ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'];
        $months = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];

        $arrAlunosNaoCadastrados = [];

        $this->load->model('ei_ordem_servico_aluno_model', 'osAluno');
        $print = '';
        while (($row = fgetcsv($handle, 1850, ";")) !== false) {
            $x++;
            if ($x == 1) {
                if (count(array_filter($row)) == count($label)) {
                    $label = array_combine($row, array_keys($row));
                }
                continue;
            }

            $row[$label['datainicio']] = str_replace($meses, $months, $row[$label['datainicio']]);
            $row[$label['datatermino']] = str_replace($meses, $months, $row[$label['datatermino']]);

            $alunos = $this->db
                ->select('e.id, d.id AS id_ordem_servico_escola, a.id AS id_aluno, b.id AS id_aluno_curso', false)
                ->select(["STR_TO_DATE('{$row[$label['datainicio']]}', '%d/%m/%Y') AS data_inicio"], false)
                ->select(["STR_TO_DATE('{$row[$label['datatermino']]}', '%d/%m/%Y') AS data_termino"], false)
                ->select("'{$row[$label['modulo']]}' AS modulo", false)
                ->join('ei_alunos_cursos b', 'b.id_aluno = a.id')
                ->join('ei_cursos c', 'c.id = b.id_curso')
                ->join('ei_ordem_servico_escolas d', "d.id_escola = b.id_escola AND d.id_ordem_servico = '{$ordemServico->id}'")
                ->join('ei_ordem_servico_alunos e', 'e.id_ordem_servico_escola = d.id AND e.id_aluno = a.id AND e.id_aluno_curso = b.id', 'left')
                ->where('c.id_diretoria', $ordemServico->id_diretoria)
                ->where('a.id', $row[$label['codigoaluno']])
                ->group_start()
                ->where('c.id', $row[$label['codigocurso']])
                ->or_where("c.id IS NULL AND b.id = '{$row[$label['codigocurso']]}'", null, false)
                ->group_end()
                ->group_by('a.id')
                ->get('ei_alunos a')
                ->row_array();

            $idAluno = $alunos['id'];

            if ($alunos and $this->osAluno->validate($alunos)) {

                if (!empty($alunos['id'])) {
                    $this->db->update('ei_ordem_servico_alunos', $alunos, ['id' => $alunos['id']]);
                } else {
                    $this->db->insert('ei_ordem_servico_alunos', $alunos);
                    $idAluno = $this->db->insert_id();
                }
                $dataAluno = [
                    'hipotese_diagnostica' => $row[$label['deficiencia']],
                ];
                $this->db->update('ei_alunos', $dataAluno, ['id' => $alunos['id_aluno']]);
                $status += 1;
            } else {
                $arrAlunosNaoCadastrados[] = ($row[$label['codigoaluno']] . ' - ' . $row[$label['nomealuno']]);
            }

            if ($row[$label['codigoescola']] == 88) {
                $print .=
                    'id = ' . $idAluno .
                    ', id_anterior = ' . $alunos['id'] .
                    ', cod_aluno = ' . $row[$label['codigoaluno']] .
                    ', id_os_escola = ' . $alunos['id_ordem_servico_escola'] .
                    ', id_aluno = ' . $alunos['id_aluno'] .
                    ', id_aluno_curso = ' . $alunos['id_aluno_curso'] .
                    ', id_curso = ' . $row[$label['codigocurso']] .
                    ', data início = ' . $alunos['data_inicio'] .
                    ', data término = ' . $alunos['data_termino'] .
                    ', módulo = ' . $alunos['modulo'] . chr(10);
            }
        }

        $alunosNaoCadastrados = implode(chr(10), $arrAlunosNaoCadastrados);
        if (strlen($alunosNaoCadastrados) == 0) {
            $alunosNaoCadastrados = null;
        }

        $this->db->update('ei_ordem_servico', ['escolas_nao_cadastradas' => $alunosNaoCadastrados], ['id' => $ordemServico->id]);

        $this->db->trans_commit();

        fclose($handle);

        echo json_encode(['status' => true, 'msg' => $print]);
    }

    //--------------------------------------------------------------------

    public function importar_profissionais()
    {
        if (!(isset($_FILES) && !empty($_FILES) && empty($_FILES['arquivo']['error']))) {
            exit(json_encode(['erro' => 'Erro no envio do arquivo. Tente mais tarde.']));
        }

        $config = [
            'upload_path' => './arquivos/ei/csv/',
            'file_name' => utf8_decode($_FILES['arquivo']['name']),
            'allowed_types' => '*',
            'overwrite' => true,
        ];

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('arquivo')) {
            exit(json_encode(['erro' => $this->upload->display_errors()]));
        }

        $csv = $this->upload->data();
        $handle = fopen($config['upload_path'] . $csv['file_name'], "r");

        $label = array_flip([
            'codigoescola', 'escola', 'codigoaluno', 'nomealuno', 'deficiencia', 'codigocurso',
            'curso', 'horario', 'segundai', 'segundaf', 'tercai', 'tercaf', 'quartai', 'quartaf',
            'quintai', 'quintaf', 'sextai', 'sextaf', 'sabadoi', 'sabadof', 'codsupervisor',
            'idprofissional', 'tipoprofissional', 'profissional', 'datainicio', 'datatermino',
            'modulo', 'valorhora', 'horasdia', 'horassemana', 'horasmes', 'l1', 'l2', 'l3', 'l4',
            'valorhorasmes', 'valorhorap', 'Dias', 'Total de Horas', 'Total', 'OS',
        ]);

        $ordemServico = $this->db
            ->select('a.id, a.ano, b.id_cliente AS id_diretoria')
            ->join('ei_contratos b', 'b.id = a.id_contrato')
            ->where('a.id', $this->input->post('id_ordem_servico'))
            ->get('ei_ordem_servico a')
            ->row();

        $this->load->helper('time');

        $this->db->trans_begin();

        $x = 0;
        $status = 0;
        $umQuartoDeDia = 21600;
        $print = '';
        while (($row = fgetcsv($handle, 1850, ";")) !== false) {
            $x++;
            if ($x == 1) {
                if (count(array_filter($row)) == count($label)) {
                    $label = array_combine(array_map('trim', $row), array_keys($row));
                }
                continue;
            }

            $valorHora = str_replace(['.', ','], ['', '.'], $row[$label['valorhora']]);
            $valorHorasMes = str_replace(['.', ','], ['', '.'], $row[$label['valorhorasmes']]);
            $valorHorap = str_replace(['.', ','], ['', '.'], $row[$label['valorhorap']]);

            $osProfissional = $this->db
                ->select('h.id')
                ->select(["c.id AS id_ordem_servico_escola, g.id AS id_usuario, e.id_supervisor"], false)
                ->select('g.id_depto AS id_departamento, g.id_area, g.id_setor', false)
                ->select('g.id_cargo, g.id_funcao, g.municipio', false)
                ->select('b.data_inicio AS data_inicio_contrato, b.data_termino AS data_termino_contrato', false)
                ->select("'{$valorHora}' AS valor_hora_operacional", false)
                ->select("'{$valorHorasMes}' AS horas_mensais_custo", false)
                ->select("'{$valorHorap}' AS valor_hora", false)
                ->join('ei_contratos b', 'b.id = a.id_contrato')
                ->join('ei_ordem_servico_escolas c', 'c.id_ordem_servico = a.id')
                ->join('ei_escolas d', "d.id = c.id_escola AND d.codigo = '{$row[$label['codigoescola']]}'")
                ->join('ei_supervisores e', "e.id_escola = d.id AND e.id_supervisor = '{$row[$label['codsupervisor']]}'")
                ->join('usuarios f', 'f.id = e.id_supervisor')
                ->join('usuarios g', "g.empresa = f.empresa AND g.id = '{$row[$label['idprofissional']]}'")
                ->join('ei_ordem_servico_profissionais h', 'h.id_ordem_servico_escola = c.id AND h.id_usuario = g.id', 'left')
                ->where('a.id', $ordemServico->id)
                ->get('ei_ordem_servico a')
                ->row_array();

            if ($osProfissional) {
                if (!empty($osProfissional['id'])) {
                    $idProfissional = $osProfissional['id'];
                    $this->db->update('ei_ordem_servico_profissionais', $osProfissional, ['id' => $idProfissional]);
                } else {
                    $this->db->insert('ei_ordem_servico_profissionais', $osProfissional);
                    $idProfissional = $this->db->insert_id();
                }

                $diasSemana = [
                    '1' => [$row[$label['segundai']], $row[$label['segundaf']]],
                    '2' => [$row[$label['tercai']], $row[$label['tercaf']]],
                    '3' => [$row[$label['quartai']], $row[$label['quartaf']]],
                    '4' => [$row[$label['quintai']], $row[$label['quintaf']]],
                    '5' => [$row[$label['sextai']], $row[$label['sextaf']]],
                    '6' => [$row[$label['sabadoi']], $row[$label['sabadof']]],
                ];

                foreach ($diasSemana as $diaSemana => $horarios) {
                    if (empty($horarios[0]) or empty($horarios[1])) {
                        continue;
                    }
                    $horas = strstr($horarios[0], ':', true);
                    $periodo = strlen($horas) > 0 ? floor(intval($horas) / 6) : null;

                    $osHorario = $this->db
                        ->select('b.id, a.id AS id_os_profissional')
                        ->select("'{$row[$label['tipoprofissional']]}' AS id_funcao", false)
                        ->select("'{$diaSemana}' AS dia_semana", false)
                        ->select("'{$periodo}' AS periodo", false)
                        ->select("'{$horarios[0]}' AS horario_inicio", false)
                        ->select("'{$horarios[1]}' AS horario_termino", false)
                        ->select("'{$row[$label['tipoprofissional']]}' AS id_funcao", false)
                        ->join('ei_ordem_servico_horarios b', "b.id_os_profissional = a.id AND b.dia_semana = '{$diaSemana}' AND b.horario_inicio = '{$horarios[0]}'", 'left')
                        ->where('a.id', $idProfissional)
                        ->get('ei_ordem_servico_profissionais a')
                        ->row_array();

                    if ($osHorario) {
                        if (!empty($osHorario['id'])) {
                            $idHorario = $osHorario['id'];
                            $this->db->update('ei_ordem_servico_horarios', $osHorario, ['id' => $idHorario]);
                        } else {
                            $this->db->insert('ei_ordem_servico_horarios', $osHorario);
                            $idHorario = $this->db->insert_id();
                        }

                        $turma = $this->db
                            ->select('a.id AS id_os_horario, d.id AS id_os_aluno')
                            ->join('ei_ordem_servico_profissionais b', 'b.id = a.id_os_profissional')
                            ->join('ei_ordem_servico_escolas c', 'c.id = b.id_ordem_servico_escola')
                            ->join('ei_ordem_servico_alunos d', 'd.id_ordem_servico_escola = c.id', 'left')
                            ->join('ei_ordem_servico_turmas e', 'e.id_os_aluno = d.id AND e.id_os_horario = a.id', 'left')
                            ->where('a.id', $idHorario)
                            ->where('d.id_aluno', $row[$label['codigoaluno']])
                            ->where('e.id_os_aluno', null)
                            ->get('ei_ordem_servico_horarios a')
                            ->row_array();

                        if ($turma) {
                            $this->db->insert('ei_ordem_servico_turmas', $turma);
                        }

                        if ($row[$label['codigoescola']] == 8) {
                            $print .=
                                'id_profissional = ' . $idProfissional .
                                ', cod_escola = ' . $row[$label['codigoescola']] .
                                ', cod_aluno = ' . $row[$label['codigoaluno']] .
                                ', cod_cuidador = ' . $row[$label['idprofissional']] .
                                ', id_horario = ' . $idHorario .
                                ', id_os_aluno = ' . $turma['id_os_aluno'] .
                                ', id_os_horario = ' . $turma['id_os_horario'] .
                                ', supervisor = ' . $osProfissional['id_supervisor'] . chr(10);
                        }
                    }
                }
            }

            if ($this->db->trans_status() == true) {
                $status++;
            }
        }

        if (empty($status)) {
            $this->db->trans_rollback();
            exit(json_encode(['erro' => 'Erro ao importar os cuidadores']));
        }

        $this->db->trans_commit();

        fclose($handle);

        echo json_encode(['status' => true, 'msg' => $print]);
    }

    //--------------------------------------------------------------------

    public function ajax_update()
    {
        $id = $this->input->post('id');
        $data = $this->input->post();
        unset($data['id']);

        $status = $this->db->update('ei_ordem_servico', $data, ['id' => $id]);

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_delete()
    {
        $id = $this->input->post('id');
        $senhaExclusao = $this->input->post('senha_exclusao');
        if (strlen($senhaExclusao) > 0) {
            $senhaExclusao .= '@';
        }

        $ordemServico = $this->db
            ->select('a.id')
            ->join('ei_contratos b', 'b.id = a.id_contrato')
            ->join('ei_diretorias c', 'c.id = b.id_cliente')
            ->where('a.id', $id)
            ->where('c.senha_exclusao', $senhaExclusao)
            ->get('ei_ordem_servico a')
            ->row();

        if (!$ordemServico) {
            exit(json_encode(['acesso_negado' => 'Senha inválida.']));
        }

        $status = $this->db->delete('ei_ordem_servico', ['id' => $ordemServico->id]);

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_delete_curso()
    {
        $status = $this->db->delete('ei_ordem_servico_cursos', ['id' => $this->input->post('id')]);

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function copiar_os()
    {
        $ano = $this->input->post('ano');
        $semestre = $this->input->post('semestre');
        $ordensServicoSelecionadas = $this->input->post('id');

        $qb = $this->db
            ->select('id, nome');
        if ($ano) {
            $qb->where('ano', $ano);
        }
        if ($semestre) {
            $qb->where('semestre', $semestre);
        }
        $rows = $qb
            ->order_by('nome', 'asc')
            ->get('ei_ordem_servico')
            ->result();

        $ordensServico = array_column($rows, 'nome', 'id');
        $data['ordens_servico'] = form_multiselect('id[]', $ordensServico, $ordensServicoSelecionadas, 'id="ordens_servico" class="demo2" size="8"');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function salvar_copia_os()
    {
        $idOS = $this->input->post('id');
        if (!is_array($idOS)) {
            $idOS = [];
        }
        if (empty($idOS)) {
            exit(json_encode(['erro' => 'Nenhuma O.S. selecionada para cópia']));
        }

        $nome = $this->input->post('nome');
        $idContrato = $this->input->post('id_contrato');
        $numeroEmpenho = $this->input->post('numero_empenho');
        $ano = $this->input->post('ano');
        $semestre = $this->input->post('semestre');
        if (strlen($nome) == 0) {
            exit(json_encode(['erro' => 'O nome das novas O.S. é obrigatório']));
        }
        if (strlen($ano) == 0) {
            exit(json_encode(['erro' => 'O ano das novas O.S. é obrigatório']));
        } elseif (date('Y', mktime(0, 0, 0, 1, 1, $ano)) != $ano) {
            exit(json_encode(['erro' => 'O ano das novas O.S. é inválido']));
        }
        if (strlen($semestre) == 0) {
            exit(json_encode(['erro' => 'O semestre das novas O.S. é obrigatório']));
        }

        $os = $this->db
            ->where('nome', $nome)
            ->get('ei_ordem_servico')
            ->num_rows();

        if ($os > 0) {
            exit(json_encode(['erro' => 'O nome da O.S. já existe.']));
        }

        $data = [
            'nome' => $nome,
            'id_contrato' => $idContrato,
            'numero_empenho' => $numeroEmpenho,
            'ano' => $ano,
            'semestre' => $semestre,
        ];

        // Busca as escolas antigas
        $escolas = $this->db
            ->select('id_escola')
            ->where_in('id_ordem_servico', $idOS)
            ->group_by('id_escola')
            ->get('ei_ordem_servico_escolas')
            ->result_array();

        // Busca os profissionais antigos
        $rowsProfissionais = $this->db
            ->select('a.*, b.id_escola', false)
            ->join('ei_ordem_servico_escolas b', 'b.id = a.id_ordem_servico_escola')
            ->where_in('b.id_ordem_servico', $idOS)
            ->order_by('a.id', 'asc')
            ->get('ei_ordem_servico_profissionais a')
            ->result_array();

        $profissionais = [];
        foreach ($rowsProfissionais as $rowProfissional) {
            $profissionais[$rowProfissional['id_escola']][$rowProfissional['id_usuario']] = $rowProfissional;
        }

        // Busca os horários antigos
        $rowsHorarios = $this->db
            ->select('a.*, b.id_usuario, c.id_escola', false)
            ->join('ei_ordem_servico_profissionais b', 'b.id = a.id_os_profissional')
            ->join('ei_ordem_servico_escolas c', 'c.id = b.id_ordem_servico_escola')
            ->where_in('c.id_ordem_servico', $idOS)
            ->get('ei_ordem_servico_horarios a')
            ->result_array();

        $horarios = [];
        foreach ($rowsHorarios as $rowHorario) {
            $horarios[$rowHorario['id_escola']][$rowHorario['id_usuario']][] = $rowHorario;
        }

        // Busca os alunos antigos
        $rowsAlunos = $this->db
            ->select('a.*, b.id_escola', false)
            ->join('ei_ordem_servico_escolas b', 'b.id = a.id_ordem_servico_escola')
            ->where_in('b.id_ordem_servico', $idOS)
            ->order_by('a.id', 'asc')
            ->get('ei_ordem_servico_alunos a')
            ->result_array();

        $alunos = [];
        foreach ($rowsAlunos as $rowAluno) {
            $alunos[$rowAluno['id_escola']][$rowAluno['id_aluno']] = $rowAluno;
        }

        // Buscar turmas antigas
        $rowsTurmas = $this->db
            ->select('c.id_usuario, d.id_aluno, e.id_escola, b.id AS id_horario')
            ->join('ei_ordem_servico_horarios b', 'b.id = a.id_os_horario')
            ->join('ei_ordem_servico_profissionais c', 'c.id = b.id_os_profissional')
            ->join('ei_ordem_servico_alunos d', 'd.id = a.id_os_aluno')
            ->join('ei_ordem_servico_escolas e', 'e.id = c.id_ordem_servico_escola AND e.id = d.id_ordem_servico_escola')
            ->where_in('e.id_ordem_servico', $idOS)
            ->get('ei_ordem_servico_turmas a')
            ->result();

        $turmas = [];
        foreach ($rowsTurmas as $rowTurma) {
            $turmas[$rowTurma->id_escola][$rowTurma->id_aluno][$rowTurma->id_usuario][] = $rowTurma->id_horario;
        }

        $this->db->trans_start();

        // Ordem_servico
        $this->db->insert('ei_ordem_servico', $data);
        $id = $this->db->insert_id();

        // Escolas
        foreach ($escolas as $escola) {
            $escola['id_ordem_servico'] = $id;
            $this->db->insert('ei_ordem_servico_escolas', $escola);
            $idOSEscola = $this->db->insert_id();

            $idHorarios = [];

            // Profissionais
            if (isset($profissionais[$escola['id_escola']])) {
                foreach ($profissionais[$escola['id_escola']] as $profissional) {
                    unset($profissional['id'], $profissional['id_escola']);
                    $profissional['id_ordem_servico_escola'] = $idOSEscola;
                    $this->db->insert('ei_ordem_servico_profissionais', $profissional);
                    $idOSProfissional = $this->db->insert_id();

                    // Horários
                    foreach ($horarios[$escola['id_escola']][$profissional['id_usuario']] as $horario) {
                        $idOSHorario = $horario['id'];
                        unset($horario['id'], $horario['id_usuario'], $horario['id_escola']);
                        $horario['id_os_profissional'] = $idOSProfissional;
                        $this->db->insert('ei_ordem_servico_horarios', $horario);

                        $idHorarios[$idOSHorario] = $this->db->insert_id();
                    }
                }
            }

            // Alunos
            if (isset($alunos[$escola['id_escola']])) {
                foreach ($alunos[$escola['id_escola']] as $aluno) {
                    unset($aluno['id'], $aluno['id_escola']);
                    $aluno['id_ordem_servico_escola'] = $idOSEscola;
                    $this->db->insert('ei_ordem_servico_alunos', $aluno);
                    $idOSAluno = $this->db->insert_id();

                    // Turmas
                    if (isset($turmas[$escola['id_escola']][$aluno['id_aluno']])) {
                        foreach ($turmas[$escola['id_escola']][$aluno['id_aluno']] as $idUsuarioOLD) {
                            foreach ($idUsuarioOLD as $idHorariosOLD) {
                                if (isset($idHorarios[$idHorariosOLD])) {
                                    $turma = [
                                        'id_os_aluno' => $idOSAluno,
                                        'id_os_horario' => $idHorarios[$idHorariosOLD],
                                    ];
                                    $this->db->insert('ei_ordem_servico_turmas', $turma);
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->db->trans_complete();
        $status = $this->db->trans_status();

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function salvar_copia_os_old()
    {
        $idsAnteriores = $this->input->post('id');
        if (!is_array($idsAnteriores)) {
            $idsAnteriores = [];
        }
        $nome = $this->input->post('nome');
        $idContrato = $this->input->post('id_contrato');
        $numeroEmpenho = $this->input->post('numero_empenho');
        $ano = $this->input->post('ano');
        $semestre = $this->input->post('semestre');
        if (strlen($nome) == 0) {
            exit(json_encode(['erro' => 'O nome das novas O.S. é obrigatório']));
        }
        if (strlen($ano) == 0) {
            exit(json_encode(['erro' => 'O ano das novas O.S. é obrigatório']));
        } elseif (date('Y', mktime(0, 0, 0, 1, 1, $ano)) != $ano) {
            exit(json_encode(['erro' => 'O ano das novas O.S. é inválido']));
        }
        if (strlen($semestre) == 0) {
            exit(json_encode(['erro' => 'O semestre das novas O.S. é obrigatório']));
        }

        $this->db->trans_start();

        $data = [
            'nome' => $nome,
            'id_contrato' => $idContrato,
            'numero_empenho' => $numeroEmpenho,
            'ano' => $ano,
            'semestre' => $semestre,
        ];

        // Ordem_servico
        $this->db->insert('ei_ordem_servico', $data);
        $id = $this->db->insert_id();

        $osEscolas = $this->db
            ->where_in('id_ordem_servico', $idsAnteriores)
            ->get('ei_ordem_servico_escolas')
            ->result();

        // Ordem_servico_escola
        foreach ($osEscolas as $osEscola) {
            $idEscolaAnterior = $osEscola->id;
            unset($osEscola->id);
            $osEscola->id_ordem_servico = $id;

            $this->db->insert('ei_ordem_servico_escolas', $osEscola);
            $idEscola = $this->db->insert_id();

            $osProfissionais = $this->db
                ->where('id_ordem_servico_escola', $idEscolaAnterior)
                ->get('ei_ordem_servico_profissionais')
                ->result();

            $arrIdProfissional = [0];

            // Ordem_servico_profissionais
            foreach ($osProfissionais as $osProfissional) {
                $idProfissionalAnterior = $osProfissional->id;
                unset($osProfissional->id);
                $osProfissional->id_ordem_servico_escola = $idEscola;

                $this->db->insert('ei_ordem_servico_profissionais', $osProfissional);
                $idProfissional = $this->db->insert_id();
                $arrIdProfissional[$idProfissionalAnterior] = $idProfissional;

                $osHorarios = $this->db
                    ->where('id_os_profissional', $idProfissionalAnterior)
                    ->get('ei_ordem_servico_horarios')
                    ->result();

                foreach ($osHorarios as $osHorario) {
                    unset($osHorario->id);
                    $osHorario->id_os_profissional = $idProfissional;
                    $totalDiasMes = $this->contarSemanasDoMes($idProfissional, $osHorario->dia_semana);
                    $osHorario->total_dias_mes1 = $totalDiasMes[0] ?? null;
                    $osHorario->total_dias_mes2 = $totalDiasMes[0] ?? null;
                    $osHorario->total_dias_mes3 = $totalDiasMes[0] ?? null;
                    $osHorario->total_dias_mes4 = $totalDiasMes[0] ?? null;
                    $osHorario->total_dias_mes5 = $totalDiasMes[0] ?? null;
                    $osHorario->total_dias_mes6 = $totalDiasMes[0] ?? null;

                    $this->db->insert('ei_ordem_servico_horarios', $osHorario);
                }
            }

            $osAlunos = $this->db
                ->where('id_ordem_servico_escola', $idEscolaAnterior)
                ->get('ei_ordem_servico_alunos')
                ->result();

            // Ordem_servico_alunos
            foreach ($osAlunos as $osAluno) {
                $idAlunoAnterior = $osAluno->id;
                unset($osAluno->id);
                $osAluno->id_ordem_servico_escola = $idEscola;

                $this->db->insert('ei_ordem_servico_alunos', $osAluno);
                $idAluno = $this->db->insert_id();

                $osTurmas = $this->db
                    ->select("id_os_profissional, '{$idAluno}' AS id_os_aluno", false)
                    ->where('id_os_aluno', $idAlunoAnterior)
                    ->where_in('id_os_profissional', array_keys($arrIdProfissional))
                    ->get('ei_ordem_servico_turmas')
                    ->result();

                // Ordem_servico_turmas
                foreach ($osTurmas as $osTurma) {
                    $osTurma->id_os_profissional = $arrIdProfissional[$osTurma->id_os_profissional];
                    $this->db->insert('ei_ordem_servico_turmas', $osTurma);
                }
            }
        }

        $this->db->trans_complete();
        $status = $this->db->trans_status();

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    private function contarSemanasDoMes(?int $idOSProfissional, ?int $diaDaSemana): array
    {
        switch ($diaDaSemana) {
            case 0:
                $semana = 'sun';
                break;
            case 1:
                $semana = 'mon';
                break;
            case 2:
                $semana = 'tue';
                break;
            case 3:
                $semana = 'wed';
                break;
            case 4:
                $semana = 'thu';
                break;
            case 5:
                $semana = 'fri';
                break;
            case 6:
                $semana = 'sat';
                break;
            default:
                return [];
        }

        $row = $this->db
            ->select('c.ano, c.semestre')
            ->select("DATE_FORMAT(MIN(f.data_inicio), '%M %Y') AS mes_inicial", false)
            ->select("DATE_FORMAT(MAX(f.data_termino), '%M %Y') AS mes_final", false)
            ->select('MIN(f.data_inicio) AS data_inicio', false)
            ->select('MAX(f.data_termino) AS data_termino', false)
            ->join('ei_ordem_servico_escolas b', 'b.id = a.id_ordem_servico_escola')
            ->join('ei_ordem_servico c', 'c.id = b.id_ordem_servico')
            ->join('ei_ordem_servico_horarios d', 'd.id_os_profissional = a.id', 'left')
            ->join('ei_ordem_servico_turmas e', 'e.id_os_horario = d.id', 'left')
            ->join('ei_ordem_servico_alunos f', 'f.id = e.id_os_aluno', 'left')
            ->where('a.id', $idOSProfissional)
            ->group_by('a.id')
            ->get('ei_ordem_servico_profissionais a')
            ->row();

        $mesInicial = intval($row->semestre) == 2 ? 7 : 1;
        $mesFinal = $mesInicial + 5;
        $mesAno = [];
        for ($i = $mesInicial; $i <= $mesFinal; $i++) {
            $mesAno[] = date('F Y', strtotime('01-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-' . $row->ano));
        }

        $data = [];
        foreach ($mesAno as $mes) {
            if ($mes == $row->mes_inicial and $row->data_inicio) {
                $semanaInicial = date('W', strtotime("{$semana} {$row->data_inicio}"));
            } else {
                $semanaInicial = date('W', strtotime("first {$semana} of {$mes}"));
            }
            if ($mes == $row->mes_final and $row->data_termino) {
                $semanaFinal = date('W', strtotime($semana, strtotime("{$row->data_termino} -1 week +1 day"))) + 1;
            } else {
                $semanaFinal = date('W', strtotime("last {$semana} of {$mes} -1 week")) + 1;
            }
            $data[] = $semanaFinal - ($semanaInicial - 1);
        }

        return $data;
    }

}
