<?php

namespace App\Controllers\Ei;

use App\Controllers\BaseController;

class Gestao_aprovacoes extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logomarca') != 'CPS-AME.jpg') {
            $this->session->set_userdata('logomarca', 'CPS-AME.jpg');
        }
        $this->load->model('ei_alocado_aprovacao_model', 'aprovacao');
    }

    //--------------------------------------------------------------------

    public function index()
    {
        $data = [
            'escolas' => ['' => 'Todas'],
            'meses' => [
                '01' => 'Janeiro',
                '02' => 'Fevereiro',
                '03' => 'Março',
                '04' => 'Abril',
                '05' => 'Maio',
                '06' => 'Junho',
                '07' => 'Julho',
                '08' => 'Agosto',
                '09' => 'Setembro',
                '10' => 'Outubro',
                '11' => 'Novembro',
                '12' => 'Dezembro',
            ],
        ];
        $data['mes'] = $data['meses'][date('m')];
        $data['semestre'] = array_slice(array_values($data['meses']), intval(date('n')) > 6 ? 6 : 0, 7);
        if (!isset($data['semestre'][6])) {
            $data['semestre'][6] = 'Jul';
        }

        $semestre = date('m') > 7 ? 2 : 1;
        $data['nomeSemestre'] = '';
        if ($data['mes'] == 7) {
            $data['nomeSemestre'] = " - {$semestre}&ordm; semestre";
        }

        $diretoria = $this->db
            ->where('email_coordenador', $this->session->userdata('email'))
            ->get('ei_diretorias')
            ->row();

        if ($diretoria) {
            $data['cliente'] = $diretoria->nome_coordenador;
            $data['depto'] = $diretoria->depto_cliente;
            $data['cargo'] = $diretoria->cargo_coordenador;
            $data['assinaturaCoordenador'] = $diretoria->assinatura_digital_coordenador;
        } else {
            $diretoria = $this->db
                ->where('email_supervisor', $this->session->userdata('email'))
                ->get('ei_diretorias')
                ->row();

            $data['cliente'] = $diretoria->nome_supervisor ?? null;
            $data['depto'] = $diretoria->depto_cliente ?? null;
            $data['cargo'] = $diretoria->cargo_supervisor ?? null;
            $data['assinaturaCoordenador'] = $diretoria->assinatura_digital_coordenador ?? null;
        }

        $aprovacao = $this->db
            ->where('cliente_aprovacao', $data['cliente'])
            ->where('depto_aprovacao', $data['depto'])
            ->where('cargo_aprovacao', $data['cargo'])
            ->where('ano_referencia', date('Y'))
            ->where('mes_referencia', date('m'))
            ->get('ei_coordenadores_aprovacoes')
            ->row();

        $data['assinatura'] = $aprovacao->assinatura_digital_aprovacao ?? null;
        $data['dataLiberacao'] = $aprovacao->data_liberacao ?? null;
        $data['mesReferencia'] = $aprovacao->mes_referencia ?? null;

        $this->load->view('ei/gestao_aprovacoes', $data);
    }

    //--------------------------------------------------------------------

    public function ajax_list()
    {
        $ano = $this->input->post('ano');
        $semestre = $this->input->post('semestre');
        $mes = $this->input->post('mes');
        $idMes = $mes - ($semestre > 1 ? 6 : 0);
        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';
        $idCuidador = $this->input->post('id_cuidador');
        $idEscola = $this->input->post('id_escola');

        $escolas = $this->db
            ->select('a.id')
            ->select(["CONCAT(a.codigo, ' - ', a.nome) AS nome"], false)
            ->join('ei_diretorias b', 'b.id = a.id_diretoria')
            ->where('b.id_empresa', $this->session->userdata('empresa'))
            ->group_start()
            ->where('b.email_coordenador', $this->session->userdata('email'))
            ->or_where('b.email_administrativo', $this->session->userdata('email'))
            ->or_where('b.email_supervisor', $this->session->userdata('email'))
            ->group_end()
            ->order_by('a.codigo', 'asc')
            ->order_by('a.nome', 'asc')
            ->get('ei_escolas a')
            ->result_array();

        $escolas = array_column($escolas, 'nome', 'id');

        $qb = $this->db
            ->select('d.codigo, GROUP_CONCAT(DISTINCT b.aluno ORDER BY b.aluno ASC) AS alunos', false)
            ->select('b.curso, TRIM(c.cuidador) AS cuidador, a.funcao', false)
            ->select('a.status_aprovacao_escola, a.data_hora_aprovacao_escola, a.nome_aprovador_escola')
            ->select('COUNT(DISTINCT DAY(x.data_evento)) AS total_dias', false)
            ->select("SUM(
                            IFNULL(TIMESTAMPDIFF(SECOND, x.horario_entrada_real_1, x.horario_saida_real_1), 0) + 
                            IFNULL(TIMESTAMPDIFF(SECOND, x.horario_entrada_real_2, x.horario_saida_real_2), 0) + 
                            IFNULL(TIMESTAMPDIFF(SECOND, x.horario_entrada_real_3, x.horario_saida_real_3), 0)
                            ) * COUNT(DISTINCT(x.id)) / GREATEST(COUNT(*), 1) AS total_segundos", false)
            ->select('a.id, d.escola, a.status_aprovacao_cps, a.data_hora_aprovacao_cps, a.nome_aprovador_cps')
            ->select('d.id_escola, c.id_cuidador, a.tipo_arquivo, a.assinatura_digital, a.arquivo_medicao')
            ->join('usuarios a2', 'a2.id = c.id_cuidador')
            ->join('ei_alocacao_escolas d', 'd.id = c.id_alocacao_escola')
            ->join('ei_alocacao e', 'e.id = d.id_alocacao')
            ->join('ei_alocados_horarios c2', 'c2.id_alocado = c.id')
            ->join('ei_matriculados_turmas b2', 'b2.id_alocado_horario = c2.id')
            ->join('ei_matriculados b', 'b.id = b2.id_matriculado AND b.id_alocacao_escola = d.id')
            ->join('ei_alocados_aprovacoes a', "a.id_alocado = c.id AND a.cargo = c2.cargo{$mesCargoFuncao} AND a.funcao = c2.funcao{$mesCargoFuncao} AND a.mes_referencia = '{$mes}'", false)
            ->join('ei_usuarios_frequencias x', "x.id_usuario = c.id_cuidador AND x.id_escola = d.id_escola AND YEAR(x.data_evento) = e.ano AND MONTH(x.data_evento) = a.mes_referencia", 'left', false)
            ->where('e.id_empresa', $this->session->userdata('empresa'))
            ->where('e.ano', $ano)
//            ->where_in('a2.status', [USUARIO_ATIVO, USUARIO_EM_EXPERIENCIA, USUARIO_INATIVO])
            ->where('e.semestre', $semestre);
        if ($idEscola) {
            $qb->where('d.id_escola', $idEscola);
        } else {
            $qb->where_in('d.id_escola', array_keys($escolas) + [0]);
        }
        if ($idCuidador) {
            $qb->where('c.id_cuidador', $idCuidador);
        }
        $sql = $qb
            ->group_by(['c.id', 'a.id'])
            ->get_compiled_select('ei_alocados c');

        $this->load->library('dataTables');
        $this->load->helper('time');

        $output = $this->datatables->query($sql);

        $status = $this->aprovacao::STATUS;
        $statusAprovacao = $this->aprovacao::STATUS_APROVACAO;
        $data = [];
        foreach ($output->data as $row) {
            if ($row->tipo_arquivo == 'P') {
                $btnVisualizar = '<button type="button" class="btn btn-sm btn-info" onclick="visualizar_arquivo_aprovacao(' . $row->id . ')" title="Visualizar arquivo"><i class="glyphicon glyphicon-eye-open"></i> </button>';
            } elseif ($row->tipo_arquivo == 'I') {
                $btnVisualizar = '<button type="button" class="btn btn-sm btn-info" onclick="visualizar_medicao(' . $row->id . ')" title="Visualizar medição"><i class="glyphicon glyphicon-eye-open"></i> </button>';
            } else {
                $btnVisualizar = '<button type="button" class="btn btn-sm btn-info" disabled title="Visualizar medição/arquivo"><i class="glyphicon glyphicon-eye-open"></i> </button>';
            }
            if ($row->status_aprovacao_escola == '4' and in_array($row->tipo_arquivo, ['I', 'P'])) {
                $btnEditar = '<button type="button" class="btn btn-sm btn-info" onclick="edit_aprovacao(' . $row->id . ')" title="Aprovar medição"><i class="glyphicon glyphicon-pencil"></i> </button>';
            } else {
                $btnEditar = '<button type="button" class="btn btn-sm btn-info" disabled title="Aprovar medição"><i class="glyphicon glyphicon-pencil"></i> </button>';
            }
            $data[] = [
                implode(' - ', [$row->codigo, $row->escola]),
                $row->alunos,
                $row->curso,
                $row->cuidador,
                $row->funcao,
                $status[$row->status_aprovacao_escola] ?? null,
                datetimeFormat($row->data_hora_aprovacao_escola, true),
                $row->nome_aprovador_escola,
                $row->total_dias,
                secToTime($row->total_segundos, false),
                $btnVisualizar . '&nbsp;' . $btnEditar,
                $statusAprovacao[$row->status_aprovacao_cps] ?? null,
                datetimeFormat($row->data_hora_aprovacao_cps, true),
                $row->nome_aprovador_cps,
                $row->status_aprovacao_escola,
                $row->status_aprovacao_cps,
            ];
        }

        $output->data = $data;

        $colaboradores = $this->db
            ->select('b.id, b.nome')
            ->join('usuarios b', 'b.id = a.id_cuidador')
            ->join('ei_alocacao_escolas c', 'c.id = a.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->where('d.id_empresa', $this->session->userdata('empresa'))
            ->where('d.ano', $ano)
            ->where('d.semestre', $semestre)
            ->where('c.id_escola', $idEscola)
            ->group_by(['b.id', 'b.nome'])
            ->order_by('b.nome', 'asc')
            ->get('ei_alocados a')
            ->result();

        $colaboradores = array_column($colaboradores, 'nome', 'id');

        $output->colaboradores = form_dropdown('', ['' => 'Todos'] + $colaboradores, $idCuidador);
        $output->escolas = form_dropdown('', ['' => 'Todas'] + $escolas, $idEscola);

        $diretoria = $this->db
            ->where('email_coordenador', $this->session->userdata('email'))
            ->get('ei_diretorias')
            ->row();

        if ($diretoria) {
            $aprovacao = $this->db
                ->where('cliente_aprovacao', $diretoria->nome_coordenador ?? null)
                ->where('depto_aprovacao', $diretoria->depto_cliente ?? null)
                ->where('cargo_aprovacao', $diretoria->cargo_coordenador ?? null)
                ->where('ano_referencia', $ano)
                ->where('semestre_referencia', $semestre)
                ->where('mes_referencia', $mes)
                ->get('ei_coordenadores_aprovacoes')
                ->row();
        } else {
            $diretoria = $this->db
                ->where('email_supervisor', $this->session->userdata('email'))
                ->get('ei_diretorias')
                ->row();

            $aprovacao = $this->db
                ->where('cliente_aprovacao', $diretoria->nome_supervisor ?? null)
                ->where('depto_aprovacao', $diretoria->depto_cliente ?? null)
                ->where('cargo_aprovacao', $diretoria->cargo_supervisor ?? null)
                ->where('ano_referencia', $ano)
                ->where('semestre_referencia', $semestre)
                ->where('mes_referencia', $mes)
                ->get('ei_coordenadores_aprovacoes')
                ->row();
        }

        $this->load->library('calendar');

        $assinaturaDigital = $aprovacao->arquivo_assinatura_aprovacao ?? null;
        if ($assinaturaDigital) {
            $assinaturaDigital = '<img src="' . base_url('arquivos/ei/assinatura_digital/' . $assinaturaDigital) . '" style="width: 60px; height: auto;">';
        }
        $mesReferencia = $aprovacao->mes_referencia ?? null;
        if ($mesReferencia) {
            $mesReferencia = $this->calendar->get_month_name(str_pad($aprovacao->mes_referencia, 2, '0', STR_PAD_LEFT));
        }

        $output->aprovacao = [
            'assinatura_aprovador' => $assinaturaDigital,
            'mes_referencia' => $mesReferencia,
            'data_liberacao' => dateFormat($aprovacao->data_liberacao ?? null),
        ];

        echo json_encode($output);
    }

    //--------------------------------------------------------------------

    public function ajax_edit()
    {
        $data = $this->aprovacao
            ->select('id, status_aprovacao_cps, data_hora_aprovacao_escola, data_hora_aprovacao_cps')
            ->findOrFail($this->input->post('id'));
        $data->data_hora_aprovacao_escola = datetimeFormat($data->data_hora_aprovacao_escola, true);
        $data->data_hora_aprovacao_cps = datetimeFormat($data->data_hora_aprovacao_cps, true);
        $data->status_aprovacao_cps = $data->status_aprovacao_cps ?: 1;
        return $this->respond($data);
    }

    //--------------------------------------------------------------------

    public function ajax_add()
    {
        echo $this->aprovacao->insertOrFail($this->setData());
    }

    //--------------------------------------------------------------------

    public function ajax_update()
    {
        echo $this->aprovacao->updateOrFail($this->input->post('id'), $this->setData());
    }

    //--------------------------------------------------------------------

    private function setData()
    {
        $this->load->library('entities');
        $data = $this->entities->create('eiAlocadoAprovacao', $this->input->post());
        $this->aprovacao->setValidationLabel('status_aprovacao_cps', 'Status aprovação');
        $this->aprovacao->setValidationLabel('data_hora_aprovacao_cps', 'Data/horário');
        $data->nome_aprovador_cps = $this->session->userdata('nome');
        return $data;
    }

    //--------------------------------------------------------------------

    public function ajax_delete()
    {
        echo $this->aprovacao->deleteOrFail($this->input->post('id'));
    }

    //--------------------------------------------------------------------

    public function save_aprovacoes()
    {
        $this->load->library('entities');
        $data = $this->entities->create('eiCoordenadorAprovacao', $this->input->post());
        $data->id_empresa = $this->session->userdata('empresa');
        $data->id_aprovador = $this->session->userdata('id');
        $data->data_liberacao = date('Y-m-d');

        $this->load->model('ei_coordenador_aprovacao_model', 'aprovador');
        $this->aprovador->setValidationLabel('mes_referencia', 'Mês Referência');

        $oldData = $this->aprovador
            ->where('id_empresa', $data->id_empresa)
            ->where('id_aprovador', $data->id_aprovador)
            ->where('ano_referencia', $data->ano_referencia)
            ->where('semestre_referencia', $data->semestre_referencia)
            ->where('mes_referencia', $data->mes_referencia)
            ->findOne();
        $data->id = $oldData->id ?? null;
        echo $this->aprovador->saveOrFail($data);
    }

    //--------------------------------------------------------------------

    public function save_grupo_aprovacoes()
    {
        if (!in_array($this->session->userdata('nivel'), [NIVEL_ACESSO_CLIENTE_NIVEL_0, NIVEL_ACESSO_CLIENTE_NIVEL_1])) {
            die(json_encode(['erro' => 'Nível de acesso não permitido!']));
        }

        $idEscola = $this->input->post('id_escola');
        $idCuidador = $this->input->post('id_cuidador');
        $ano = $this->input->post('ano');
        $semestre = $this->input->post('semestre');
        $mesReferencia = $this->input->post('mes_referencia');
        $idMes = $this->getIdMes($mesReferencia, $semestre);
        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';

        $qb = $this->db
            ->select('a.id')
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocados_horarios b2', "b2.id_alocado = b.id AND b2.cargo{$mesCargoFuncao} = a.cargo AND b2.funcao{$mesCargoFuncao} = a.funcao")
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->where('d.id_empresa', $this->session->userdata('empresa'))
            ->where('d.ano', $ano)
            ->where('d.semestre', $semestre)
            ->where('a.mes_referencia', $mesReferencia)
            ->where('a.arquivo_medicao !=', null);
        if ($idEscola) {
            $qb->where('c.id_escola', $idEscola);
        }
        if ($idCuidador) {
            $qb->where('b.id_cuidador', $idCuidador);
        }
        $aprovacoes = $qb
            ->group_by(['a.id'])
            ->get('ei_alocados_aprovacoes a')
            ->result_array();

        $idAprovacoes = array_column($aprovacoes, 'id');
        if (empty($idAprovacoes)) {
            die(json_encode(['erro' => 'Nenhuma medição encontrada para aprovação.']));
        }

        $status = $this->db
            ->set('data_hora_aprovacao_cps', date('Y-m-d H:i:s'))
            ->set('nome_aprovador_cps', $this->session->userdata('nome'))
            ->set('status_aprovacao_cps', 2)
            ->where_in('id', $idAprovacoes)
            ->update('ei_alocados_aprovacoes');
        if ($status == false) {
            die(json_encode(['erro' => 'Não foi possível aprovar as medições.']));
        }

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    private function getIdMes(?string $mes, ?int $semestre): int
    {
        $semestre = intval($mes) > 7 ? 2 : (intval($mes) < 7 ? 1 : $semestre);
        return (int)$mes - ($semestre > 1 ? 6 : 0);
    }

    //--------------------------------------------------------------------

    public function visualizar_arquivo_aprovacao()
    {
        $this->load->model('ei_alocado_aprovacao_model', 'aprovacao');
        $data = $this->aprovacao
            ->select('id, arquivo_medicao')
            ->findOne($this->input->post('id'));
        if (empty($data)) {
            exit(json_encode(['erro' => $this->aprovacao->errors()]));
        }
        $path = $this->aprovacao->getUploadConfig()['arquivo_medicao']['upload_path'] ?? '';
        $data->nome_arquivo_medicao = $data->arquivo_medicao;
        $data->arquivo_medicao = str_replace('./', base_url(), $path) . $data->arquivo_medicao;
        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function visualizar_medicao()
    {
        if ($this->session->userdata('tipo') != 'funcionario') {
            exit(json_encode(['erro' => 'Acesso não permitido.']));
        }

        $medicao = $this->db
            ->select(['a.*, c.id_escola'], false)
            ->select('d.depto, d.id_diretoria, d.id_supervisor, d.ano, d.semestre')
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->where('a.id', $this->input->post('id'))
            ->get('ei_alocados_aprovacoes a')
            ->row();

        if (empty($medicao)) {
            exit(json_encode(['erro' => 'Medição não encontrada.']));
        }
        $idAlocado = $medicao->id_alocado;
        $idEscola = $medicao->id_escola;
        $mes = $medicao->mes_referencia;
        $ano = $medicao->ano;

        $usuario = $this->db
            ->select('b.id, b.nome, b.cnpj, b.assinatura_digital, c.nome AS funcao')
            ->join('usuarios b', 'b.id = a.id_cuidador')
            ->join('empresa_funcoes c', 'c.id = b.id_funcao')
            ->where('a.id', $idAlocado)
            ->get('ei_alocados a')
            ->row();

        if (empty($usuario)) {
            return '';
        }

        $data['empresa'] = $this->db
            ->select('foto, foto_descricao')
            ->get_where('usuarios', ['id' => $this->session->userdata('empresa')])
            ->row();

        $data['cabecalho'] = null;
        $horariosReais = null;
        $strHorarioReal = $horariosReais === '1' ? '_real' : '';
        $data['horario_real'] = $horariosReais === '1' ? ' real' : '';

        $this->load->library('calendar');

        $alocacaoEscola = $this->db
            ->select("GROUP_CONCAT(DISTINCT b.escola ORDER BY b.escola ASC SEPARATOR ' / ') AS escola")
            ->select('d.assinatura_digital, e.assinatura_digital AS assinatura_coordenador')
            ->select('f.assinatura_digital AS assinatura_aprovacao, f.arquivo_medicao')
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->join('usuarios d', 'd.id = c.id_supervisor')
            ->join('usuarios e', 'e.id = c.coordenador')
            ->join('ei_alocados_aprovacoes f', "f.id_alocado = a.id AND f.mes_referencia = '{$mes}'", 'left')
            ->where('a.id', $idAlocado)
            ->group_by('c.id')
            ->get('ei_alocados a')
            ->row();

        $queryString = [
            'profissional' => $usuario->id,
            'escola' => $idEscola,
            'depto' => $medicao->depto,
            'diretoria' => $medicao->id_diretoria,
            'supervisor' => $medicao->id_supervisor,
            'ano' => $ano,
            'mes' => $mes,
            'semestre' => $medicao->semestre,
            'aprovacao' => 1,
        ];

        $nomeSemestre = '';
        if ($mes == 7) {
            $nomeSemestre = " - {$medicao->semestre}&ordm; semestre";
        }

        $data['profissional'] = $usuario->nome ?? null;
        $data['funcao'] = $usuario->funcao ?? null;
        $data['cnpj'] = $usuario->cnpj ?? null;
        $data['mes_ano'] = $this->calendar->get_month_name($mes) . '/' . $ano . $nomeSemestre;
        $data['escola'] = $alocacaoEscola->escola ?? $idEscola;
        $data['assinatura_digital_prestador'] = $usuario->assinatura_digital ?? null;
        $data['assinatura_digital_coordenador'] = $alocacaoEscola->assinatura_coordenador ?? null;
        $data['assinatura_digital_supervisor'] = $alocacaoEscola->assinatura_digital ?? null;
        $data['assinatura_digital_aprovacao'] = $alocacaoEscola->assinatura_aprovacao ?? null;
        $data['query_string'] = http_build_query($queryString);
        $data['is_pdf'] = true;

        $data['rows'] = $this->db
            ->select(["DATE_FORMAT(data_evento, '%d') AS dia"], false)
            ->select(["TIME_FORMAT(horario_entrada{$strHorarioReal}_1, '%H:%i') AS horario_entrada_1"], false)
            ->select(["TIME_FORMAT(horario_saida{$strHorarioReal}_1, '%H:%i') AS horario_saida_1"], false)
            ->select(["TIME_FORMAT(horario_entrada{$strHorarioReal}_2, '%H:%i') AS horario_entrada_2"], false)
            ->select(["TIME_FORMAT(horario_saida{$strHorarioReal}_2, '%H:%i') AS horario_saida_2"], false)
            ->select(["TIME_FORMAT(horario_entrada{$strHorarioReal}_3, '%H:%i') AS horario_entrada_3"], false)
            ->select(["TIME_FORMAT(horario_saida{$strHorarioReal}_3, '%H:%i') AS horario_saida_3"], false)
            ->select('status_entrada_1, status_entrada_2, status_entrada_3')
            ->select('status_saida_1, status_saida_2, status_saida_3')
            ->select('observacoes')
            ->where('MONTH(data_evento)', $mes)
            ->where('YEAR(data_evento)', $ano)
            ->where('id_usuario', $usuario->id)
            ->where('id_escola', $idEscola)
            ->where('MONTH(data_evento)', $mes)
            ->where('YEAR(data_evento)', $ano)
            ->order_by('data_evento', 'asc')
            ->get('ei_usuarios_frequencias')
            ->result();

        $this->load->model('ei_usuario_frequencia_model', 'usuario_frequencia');
        $data['status'] = $this->usuario_frequencia::STATUS;
        $data['status'] = array_intersect_key($data['status'], array_flip(['FT', 'FR', 'EF', 'RE', 'SB', 'DG']));

        $dataAtual = $this->input->get('data_atual');
        $arrDataAtual = explode('/', $dataAtual);
        if (checkdate(intval($arrDataAtual[1] ?? 0), intval($arrDataAtual[0] ?? 0), intval($arrDataAtual[2] ?? 0)) == false) {
            $ultimoDiaDoMes = date('Y-m-t', mktime(0, 0, 0, (int)$mes, 1, (int)$ano));
            $dia = intval(date('t', strtotime($ultimoDiaDoMes)));
            $diaSemana = date('w', strtotime($ultimoDiaDoMes));
            if ($diaSemana == 0) {
                $dia -= 2;
            } elseif ($diaSemana == 6) {
                $dia -= 1;
            }
            $dataAtual = $dia . '/' . $mes . '/' . $ano;
        }
        $data['data_atual'] = $dataAtual;

        $this->load->helper('time');
        $totalDias = [];
        $totalHoras = 0;
        foreach ($data['rows'] as $row) {
            if (array_key_exists($row->status_entrada_1, $data['status']) == false and
                array_key_exists($row->status_entrada_2, $data['status']) == false and
                array_key_exists($row->status_entrada_3, $data['status']) == false) {
                $totalDias[$row->dia] = $row->dia;
            }
//            $totalDias[$row->dia] = $row->dia;
            $totalHoras += (timeToSec($row->horario_saida_1) - timeToSec($row->horario_entrada_1))
                + (timeToSec($row->horario_saida_2) - timeToSec($row->horario_entrada_2))
                + (timeToSec($row->horario_saida_3) - timeToSec($row->horario_entrada_3));
        }
        $data['total_dias'] = count($totalDias);
        $data['total_horas'] = secToTime($totalHoras, false);

        $view = $this->load->view('ei/usuario_frequencias_pdf', $data, true);

        preg_match_all('/<body style="color: #000;">(.*?)<\/body>/s', $view, $conteudo);

        echo json_encode(['relatorio' => $conteudo[0][0] ?? '']);

        $mes = $this->input->get('mes');
        $ano = $this->input->get('ano');

        $this->load->library('calendar');
    }

    //--------------------------------------------------------------------

    public function pdf_medicoes()
    {
        $data['empresa'] = $this->db
            ->select('foto_descricao')
            ->where('id', $this->session->userdata('empresa'))
            ->get('usuarios')
            ->row();

        $diretoria = $this->db
            ->where('email_coordenador', $this->session->userdata('email'))
            ->get('ei_diretorias')
            ->row();

        if ($diretoria) {
            $data['cliente'] = $diretoria->nome_coordenador;
            $data['depto'] = $diretoria->depto_cliente;
            $data['cargo'] = $diretoria->cargo_coordenador;
        } else {
            $diretoria = $this->db
                ->where('email_supervisor', $this->session->userdata('email'))
                ->get('ei_diretorias')
                ->row();

            $data['cliente'] = $diretoria->nome_supervisor ?? null;
            $data['depto'] = $diretoria->depto_cliente ?? null;
            $data['cargo'] = $diretoria->cargo_supervisor ?? null;
        }

        $ano = $this->input->get('ano');
        $semestre = $this->input->get('semestre');
        $mes = $this->input->get('mes') + 1;
        $idMes = $mes - ($semestre > 1 ? 6 : 0);
        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';
        $idCuidador = $this->input->get('id_usuario');
        $idEscola = $this->input->get('id_escola');

        $escolas = $this->db
            ->select('a.id')
            ->select(["CONCAT(a.codigo, ' - ', a.nome) AS nome"], false)
            ->join('ei_diretorias b', 'b.id = a.id_diretoria')
            ->where('b.id_empresa', $this->session->userdata('empresa'))
            ->group_start()
            ->where('b.email_coordenador', $this->session->userdata('email'))
            ->or_where('b.email_administrativo', $this->session->userdata('email'))
            ->or_where('b.email_supervisor', $this->session->userdata('email'))
            ->group_end()
            ->order_by('a.codigo', 'asc')
            ->order_by('a.nome', 'asc')
            ->group_by('a.id')
            ->get('ei_escolas a')
            ->result_array();

        $escolas = array_column($escolas, 'nome', 'id');

        $qb = $this->db
            ->select('d.codigo, GROUP_CONCAT(DISTINCT b.aluno ORDER BY b.aluno ASC) AS alunos', false)
            ->select('b.curso, TRIM(c.cuidador) AS cuidador, a.funcao', false)
            ->select('a.status_aprovacao_escola, a.data_hora_aprovacao_escola, a.nome_aprovador_escola')
            ->select('COUNT(DISTINCT DAY(x.data_evento)) AS total_dias', false)
            ->select("SUM(
                            IFNULL(TIMESTAMPDIFF(SECOND, x.horario_entrada_real_1, x.horario_saida_real_1), 0) + 
                            IFNULL(TIMESTAMPDIFF(SECOND, x.horario_entrada_real_2, x.horario_saida_real_2), 0) + 
                            IFNULL(TIMESTAMPDIFF(SECOND, x.horario_entrada_real_3, x.horario_saida_real_3), 0)
                            ) * COUNT(DISTINCT(x.id)) / GREATEST(COUNT(*), 1) AS total_segundos", false)
            ->select('a.id, d.escola, a.status_aprovacao_cps, a.data_hora_aprovacao_cps, a.nome_aprovador_cps')
            ->select('d.id_escola, c.id_cuidador, a.tipo_arquivo, a.assinatura_digital, a.arquivo_medicao')
            ->join('usuarios a2', 'a2.id = c.id_cuidador')
            ->join('ei_alocacao_escolas d', 'd.id = c.id_alocacao_escola')
            ->join('ei_alocacao e', 'e.id = d.id_alocacao')
            ->join('ei_alocados_horarios c2', 'c2.id_alocado = c.id')
            ->join('ei_matriculados_turmas b2', 'b2.id_alocado_horario = c2.id')
            ->join('ei_matriculados b', 'b.id = b2.id_matriculado AND b.id_alocacao_escola = d.id')
            ->join('ei_alocados_aprovacoes a', "a.id_alocado = c.id AND a.cargo = c2.cargo{$mesCargoFuncao} AND a.funcao = c2.funcao{$mesCargoFuncao} AND a.mes_referencia = '{$mes}'", false)
            ->join('ei_usuarios_frequencias x', "x.id_usuario = c.id_cuidador AND x.id_escola = d.id_escola AND YEAR(x.data_evento) = e.ano AND MONTH(x.data_evento) = a.mes_referencia", 'left', false)
            ->where('e.id_empresa', $this->session->userdata('empresa'))
//            ->where_in('a2.status', [USUARIO_ATIVO, USUARIO_EM_EXPERIENCIA])
            ->where('e.ano', $ano)
            ->where('e.semestre', $semestre);
        if (!empty($idEscola)) {
            $qb->where('d.id_escola', $idEscola);
        } else {
            $qb->where_in('d.id_escola', array_keys($escolas) + [0]);
        }
        if (!empty($idCuidador)) {
            $qb->where('c.id_cuidador', $idCuidador);
        }
        $data['rows'] = $qb
            ->group_by(['c.id', 'a.id'])
            ->order_by('d.codigo', 'asc')
            ->get('ei_alocados c')
            ->result();

        $diretoria = $this->db
            ->where('email_coordenador', $this->session->userdata('email'))
            ->get('ei_diretorias')
            ->row();

        if ($diretoria) {
            $aprovacao = $this->db
                ->where('cliente_aprovacao', $diretoria->nome_coordenador ?? null)
                ->where('depto_aprovacao', $diretoria->depto_cliente ?? null)
                ->where('cargo_aprovacao', $diretoria->cargo_coordenador ?? null)
                ->get('ei_coordenadores_aprovacoes')
                ->row();
        } else {
            $diretoria = $this->db
                ->where('email_supervisor', $this->session->userdata('email'))
                ->get('ei_diretorias')
                ->row();

            $aprovacao = $this->db
                ->where('cliente_aprovacao', $diretoria->nome_supervisor ?? null)
                ->where('depto_aprovacao', $diretoria->depto_cliente ?? null)
                ->where('cargo_aprovacao', $diretoria->cargo_supervisor ?? null)
                ->get('ei_coordenadores_aprovacoes')
                ->row();
        }

        $this->load->library('calendar');

        $assinaturaDigital = $aprovacao->arquivo_assinatura_aprovacao ?? null;
        if ($assinaturaDigital) {
            $assinaturaDigital = base_url('arquivos/ei/assinatura_digital/' . $assinaturaDigital);
        }
        $mesReferencia = null;
        if ($aprovacao->mes_referencia) {
            $mesReferencia = $this->calendar->get_month_name(str_pad($aprovacao->mes_referencia, 2, '0', STR_PAD_LEFT));
        }

        $data['assinatura'] = $assinaturaDigital;
        $data['mesReferencia'] = $mesReferencia;
        $data['dataLiberacao'] = dateFormat($aprovacao->data_liberacao ?? null);
        $data['status'] = $this->aprovacao::STATUS;
        $data['statusAprovacao'] = $this->aprovacao::STATUS_APROVACAO;

        $this->load->library('m_pdf');

        $stylesheet = 'table thead th { font-size: 14px; padding: 5px; text-align: center; font-weight: bold; } ';
        $stylesheet .= 'table tbody tr { border-width: 5px; padding: 5px; border-color: #ddd; } ';
        $stylesheet .= 'table tbody td { font-size: 14px; padding: 5px; } ';

        $stylesheet .= '#table_medicoes thead th { font-size: 11px; padding: 5px; background-color: #f5f5f5; } ';
        $stylesheet .= '#table_medicoes tbody td { font-size: 11px; padding: 5px; text-align: left; } ';
        $stylesheet .= '#table_medicoes tbody td:nth-child(3) { text-align: right; }';

        $this->load->helper('time');

        $this->m_pdf->pdf->setTopMargin(48);
        $this->m_pdf->pdf->AddPage('L');
        $this->m_pdf->pdf->writeHTML($stylesheet, 1);
        $this->m_pdf->pdf->writeHTML($this->load->view('ei/pdf_gestao_aprovacoes', $data, true));

        $this->m_pdf->pdf->Output("Educação Inclusiva - Gestão de Aprovações de Medições - {$mes}/{$ano}.pdf", 'D');
    }

}
