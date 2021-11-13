<?php

namespace App\Controllers\Ei;

use App\Controllers\BaseController;

class Gestao_medicoes extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logomarca') != 'CPS-AME.jpg') {
            $this->session->set_userdata('logomarca', 'CPS-AME.jpg');
        }
    }

    //--------------------------------------------------------------------

    public function index()
    {
        $escolas = $this->db
            ->select('a.id')
            ->select(["CONCAT(a.codigo, ' - ', a.nome) AS nome"], false)
            ->join('ei_diretorias b', 'b.id = a.id_diretoria')
            ->where('b.id_empresa', $this->session->userdata('empresa'))
            ->group_start()
            ->where('a.email_coordenador', $this->session->userdata('email'))
            ->or_where('a.email_administrativo', $this->session->userdata('email'))
            ->or_where('a.email_diretor', $this->session->userdata('email'))
            ->group_end()
            ->order_by('a.codigo', 'asc')
            ->order_by('a.nome', 'asc')
            ->get('ei_escolas a')
            ->result();

        $escolas = array_column($escolas, 'nome', 'id');

        $semestre = date('m') > 7 ? 2 : 1;

        $alocacao = $this->db
            ->select('b.medicao_liberada_mes1')
            ->select('b.medicao_liberada_mes2')
            ->select('b.medicao_liberada_mes3')
            ->select('b.medicao_liberada_mes4')
            ->select('b.medicao_liberada_mes5')
            ->select('b.medicao_liberada_mes6')
            ->select('b.medicao_liberada_mes7')
            ->join('ei_alocacao b', 'b.id = a.id_alocacao')
            ->join('ei_escolas c', 'c.id = a.id_escola')
            ->where('b.id_empresa', $this->session->userdata('empresa'))
            ->where('b.ano', date('Y'))
            ->where('b.semestre', $semestre)
            ->where_in('a.id_escola', array_keys($escolas) + [0])
            ->group_start()
            ->where('c.email_coordenador', $this->session->userdata('email'))
            ->or_where('c.email_administrativo', $this->session->userdata('email'))
            ->or_where('c.email_diretor', $this->session->userdata('email'))
            ->group_end()
            ->get('ei_alocacao_escolas a')
            ->row();

        $this->load->model('ei_alocado_aprovacao_model', 'aprovacao');

        $data = [
            'escolas' => ['' => 'selecione...'] + $escolas,
            'statusAprovacao' => ['' => 'selecione...'] + $this->aprovacao::STATUS,
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

        $data['nomeMes'] = $data['meses'][date('m')];
        $data['mes'] = date('m');

        foreach (['07', '06', '05', '04', '03', '02', '01'] as $mes) {
            if (!empty($alocacao->{'medicao_liberada_mes' . intval($mes)})) {
                $data['nomeMes'] = $data['meses'][str_pad(intval($mes) + ($semestre > 1 ? 6 : 0), 2, '0', 0)];
                $data['mes'] = $mes + ($semestre > 1 ? 6 : 0);
                break;
            }
        }

        $data['semestre'] = array_slice(array_values($data['meses']), intval(date('n')) > 6 ? 6 : 0, 7);
        if (!isset($data['semestre'][6])) {
            $data['semestre'][6] = 'Jul';
        }
        $data['nomeSemestre'] = '';
        if ($data['mes'] == 7) {
            $data['nomeSemestre'] = " - {$semestre}&ordm; semestre";
        }

        $this->load->view('ei/gestao_medicoes', $data);
    }

    //--------------------------------------------------------------------

    public function atualizar_filtro()
    {
        $idEscola = $this->input->post('id_escola');
        $idAlocado = $this->input->post('id_alocado');
        $statusAprovacaoEscola = $this->input->post('status_aprovacao_escola');
        $mes = str_pad($this->input->post('mes'), 2, '0', 0);
        $semestre = $this->input->post('semestre');
        $idMes = (int)$mes - ($semestre > 1 ? 6 : 0);
        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';
        $ano = $this->input->post('ano');

        $alocados = $this->db
            ->select('a.id, d.nome, b.id_alocacao, a.id_cuidador')
            ->select('c.depto, c.id_diretoria, c.id_supervisor')
            ->select("c2.cargo{$mesCargoFuncao} AS cargo, c2.funcao{$mesCargoFuncao} AS funcao")
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->join('ei_alocados_horarios c2', 'c2.id_alocado = a.id')
            ->join('usuarios d', 'd.id = a.id_cuidador')
            ->where('c.ano', $ano)
            ->where('c.semestre', $semestre)
            ->where('b.id_escola', $idEscola)
//            ->where_in('d.status', [USUARIO_ATIVO, USUARIO_EM_EXPERIENCIA, USUARIO_INATIVO])
            ->group_by(['a.id_cuidador', 'd.nome'])
            ->order_by('d.nome', 'asc')
            ->get('ei_alocados a')
            ->result();

        $cuidadores = array_column($alocados, 'id_cuidador', 'id');
        $deptos = array_column($alocados, 'depto', 'id');
        $diretorias = array_column($alocados, 'id_diretoria', 'id');
        $supervisores = array_column($alocados, 'id_supervisor', 'id');
        $alocados = array_column($alocados, 'nome', 'id');
        $cargos = array_column($alocados, 'cargo');
        $funcoes = array_column($alocados, 'funcao');

        $idAlocado = array_key_exists($idAlocado, $alocados) ? $idAlocado : null;
        $idCuidador = $cuidadores[$idAlocado] ?? null;
        $depto = $deptos[$idAlocado] ?? null;
        $idDiretoria = $diretorias[$idAlocado] ?? null;
        $idSupervisor = $supervisores[$idAlocado] ?? null;

        $alocacao = $this->db
            ->select("b.medicao_liberada_mes{$idMes} AS medicao_liberada_mes")
            ->join('ei_alocacao b', 'b.id = a.id_alocacao')
            ->join('ei_escolas c', 'c.id = a.id_escola')
            ->where('b.id_empresa', $this->session->userdata('empresa'))
            ->where('b.ano', $ano)
            ->where('b.semestre', $semestre)
            ->where('a.id_escola', $idEscola)
            ->group_start()
            ->where('c.email_coordenador', $this->session->userdata('email'))
            ->or_where('c.email_administrativo', $this->session->userdata('email'))
            ->or_where('c.email_diretor', $this->session->userdata('email'))
            ->group_end()
            ->get('ei_alocacao_escolas a')
            ->row();

        $aprovacao = $this->db
            ->select('a.*', false)
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->where('a.id_alocado', $idAlocado)
            ->where('d.ano', $this->input->post('ano'))
            ->where('d.semestre', $semestre)
            ->where('a.mes_referencia', $this->input->post('mes'))
            ->where_in('a.cargo', $cargos + [0])
            ->where_in('a.funcao', $funcoes + [0])
            ->get('ei_alocados_aprovacoes a')
            ->row();

        if ($aprovacao) {
            $aprovacao->data_hora_aprovacao_escola = datetimeFormat($aprovacao->data_hora_aprovacao_escola, true) ?? date('d/m/Y H:i');
            $aprovacao->nome_aprovador_escola = $aprovacao->nome_aprovador_escola ?: $this->session->userdata('nome');
            $aprovacao->tipo_arquivo = $aprovacao->tipo_arquivo ?: 'I';
        } else {
            $aprovacao = $this->db->list_fields('ei_alocados_aprovacoes');
            $aprovacao = (object)array_combine($aprovacao, array_pad([], count($aprovacao), null));
        }

        $arquivoMedicao = null;
        if (!empty($aprovacao->arquivo_medicao)) {
            $this->load->model('ei_alocado_aprovacao_model', 'aprovacao');

            $path = $this->aprovacao->getUploadConfig()['arquivo_medicao']['upload_path'] ?? '';
            $arquivoMedicao = str_replace('./', base_url(), $path) . $aprovacao->arquivo_medicao;
        }

        if (empty($statusAprovacaoEscola) and !empty($aprovacao)) {
            $statusAprovacaoEscola = $aprovacao->status_aprovacao_escola;
        }
        $relatorio = null;
        if (in_array($statusAprovacaoEscola, [2, 3, 4])) {
            $aprovacao->status_aprovacao_escola = $statusAprovacaoEscola;
            $relatorio = $this->relatorio_servicos_prestados($idAlocado, $idEscola, $mes, $semestre, $ano);
        }
        if ($statusAprovacaoEscola == '3') {
            $aprovacao->data_hora_aprovacao_escola = null;
            $aprovacao->nome_aprovador_escola = null;
        }

        $data = [
            'alocados' => form_dropdown('', ['' => 'selecione...'] + $alocados, $idAlocado, 'id="colaboradores" class="form-control input-sm" onchange="atualizar_filtro();"'),
            'medicao_liberada_mes' => isset($alocacao) ? $alocacao->medicao_liberada_mes : false,
            'id_cuidador' => $idCuidador,
            'depto' => $depto,
            'id_diretoria' => $idDiretoria,
            'id_supervisor' => $idSupervisor,
            'aprovacao' => $aprovacao,
            'possui_arquivos' => !empty($aprovacao->assinatura_digital) or !empty($aprovacao->arquivo_medicao),
            'possui_arquivo_medicao' => !empty($aprovacao->arquivo_medicao),
            'arquivo_medicao' => $arquivoMedicao,
            'relatorio' => $relatorio,
        ];

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function relatorio_servicos_prestados($idAlocado, $idEscola, $mes, $semestre, $ano)
    {
        if ($this->session->userdata('tipo') != 'funcionario') {
            return '';
        }

        $usuarioAtual = $this->db
            ->select('assinatura_digital')
            ->where('id', $this->session->userdata('id'))
            ->get('usuarios')
            ->row();

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
        $horariosProgramados = $this->session->userdata('nivel') == NIVEL_ACESSO_CLIENTE_NIVEL_2;
        $strHorarioReal = $horariosProgramados ? '' : '_real';
        $data['horario_real'] = $horariosProgramados ? '' : 'real';
//        $strHorarioReal = '_real';
//        $data['horario_real'] = ' real';
        $idMes = (int)$mes - ($semestre > 1 ? 6 : 0);
        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';

        $this->load->library('calendar');

        $alocacaoEscola = $this->db
            ->select("a.id, GROUP_CONCAT(DISTINCT b.escola ORDER BY b.escola ASC SEPARATOR ' / ') AS escola")
            ->select('d.assinatura_digital, e.assinatura_digital AS assinatura_coordenador')
            ->select('b.id_alocacao, c.depto, c.id_diretoria, c.id_supervisor, c.ano, c.semestre')
            ->select('f.assinatura_digital AS assinatura_aprovacao, f.arquivo_medicao')
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->join('ei_alocados_horarios c2', 'c2.id_alocado = a.id')
            ->join('usuarios d', 'd.id = c.id_supervisor')
            ->join('usuarios e', 'e.id = c.coordenador')
            ->join('ei_alocados_aprovacoes f', "f.id_alocado = a.id AND f.cargo = c2.cargo{$mesCargoFuncao} AND f.funcao = c2.funcao{$mesCargoFuncao} AND f.mes_referencia = '{$mes}'", 'left', false)
            ->where('c.ano', $ano)
            ->where('a.id', $idAlocado)
            ->group_start()
            ->where('b.id_escola', $idEscola)
            ->or_where("CHAR_LENGTH('{$idEscola}') =", 0)
            ->group_end()
            ->group_by('c.id')
            ->get('ei_alocados a')
            ->row();

        $queryString = [
            'profissional' => $usuario->id,
            'escola' => $idEscola,
            'depto' => $alocacaoEscola->depto,
            'diretoria' => $alocacaoEscola->id_diretoria,
            'supervisor' => $alocacaoEscola->id_supervisor,
            'ano' => $ano,
            'mes' => $mes,
            'semestre' => $alocacaoEscola->semestre,
            'aprovacao' => 1,
        ];

        $nomeSemestre = '';
        if ($mes == 7) {
            $nomeSemestre = " - {$semestre}&ordm; semestre";
        }

        $data['profissional'] = $usuario->nome ?? null;
        $data['funcao'] = $usuario->funcao ?? null;
        $data['cnpj'] = $usuario->cnpj ?? null;
        $data['mes_ano'] = $this->calendar->get_month_name($mes) . '/' . $ano . $nomeSemestre;
        $data['escola'] = $alocacaoEscola->escola ?? $idEscola;
        $data['assinatura_digital_prestador'] = $usuario->assinatura_digital ?? null;
        $data['assinatura_digital_coordenador'] = $alocacaoEscola->assinatura_coordenador ?? null;
        $data['assinatura_digital_supervisor'] = $alocacaoEscola->assinatura_digital ?? $usuarioAtual->assinatura_digital ?? null;
        $data['assinatura_digital_aprovacao'] = $alocacaoEscola->assinatura_aprovacao ?? null;
        $data['query_string'] = http_build_query($queryString);
        $data['is_pdf'] = false;

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
            ->where('YEAR(data_evento)', $ano)
            ->where('MONTH(data_evento)', $mes)
            ->where('id_usuario', $usuario->id)
            ->where('id_escola', $idEscola)
            ->order_by('data_evento')
            ->order_by("IFNULL(horario_entrada{$strHorarioReal}_1, '0:00')")
            ->order_by("IFNULL(horario_saida{$strHorarioReal}_1, '0:00')")
            ->order_by("IFNULL(horario_entrada{$strHorarioReal}_2, '0:00')")
            ->order_by("IFNULL(horario_saida{$strHorarioReal}_2, '0:00')")
            ->order_by("IFNULL(horario_entrada{$strHorarioReal}_3, '0:00')")
            ->order_by("IFNULL(horario_saida{$strHorarioReal}_3, '0:00')")
            ->get('ei_usuarios_frequencias')
            ->result();

        $totalizacao = $this->db
            ->select("MAX(a.total_dias_mes{$idMes}) AS total_dias", false)
            ->select("SUM(TIME_TO_SEC(a.total_horas_mes{$idMes})) AS total_segundos", false)
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->where('d.id', $alocacaoEscola->id_alocacao)
            ->where('a.id_cuidador', $usuario->id)
            ->where('c.id_escola', $idEscola)
            ->get('ei_alocados_totalizacao a')
            ->row();

        $this->load->helper('time');

        $data['total_dias'] = $totalizacao->total_dias ?? null;
        $data['total_horas'] = secToTime($totalizacao->total_segundos ?? null, false);

        $sqlDescontos = $this->db
            ->select('c.id')
            ->select("IFNULL(c.total_dias_mes{$idMes}, SUM(b.desconto_mes{$idMes})) AS desconto_dias")
            ->select("IFNULL(TIME_TO_SEC(c.total_horas_mes{$idMes}), SUM(TIME_TO_SEC(b.total_horas_mes{$idMes}) * b.desconto_mes{$idMes})) AS desconto_horas")
            ->join('ei_alocados_horarios b', 'b.id_alocado = a.id')
            ->join('ei_alocados_totalizacao c', 'c.id_alocado = a.id AND c.periodo = b.periodo')
            ->where('a.id', $alocacaoEscola->id ?? null)
            ->group_by('c.id')
            ->get_compiled_select('ei_alocados a');

        $sqlDescontos = "SELECT s.id, SUM(s.desconto_dias) AS desconto_dias,
                                SEC_TO_TIME(SUM(s.desconto_horas)) AS desconto_horas
                         FROM ({$sqlDescontos}) s";
        $descontos = $this->db->query($sqlDescontos)->row();

        $this->load->model('ei_usuario_frequencia_model', 'usuario_frequencia');

        $data['status'] = $this->usuario_frequencia::STATUS;
        $data['status'] = array_intersect_key($data['status'], array_flip(['FT', 'FR', 'EF', 'RE', 'SB', 'DG']));

        $dataAtual = $this->input->get('data_atual') ?: date('d/m/Y');
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

//        $this->load->helper('time');
        $totalDias = [];
        $totalHoras = 0;
        foreach ($data['rows'] as $row) {
            if (array_key_exists($row->status_entrada_1, $data['status']) == false and
                array_key_exists($row->status_entrada_2, $data['status']) == false and
                array_key_exists($row->status_entrada_3, $data['status']) == false) {
                $totalDias[$row->dia] = $row->dia;
            }
//            $totalDias[$row->dia] = $row->dia;
            $totalHoras += (
                (timeToSec($row->horario_saida_1) - timeToSec($row->horario_entrada_1))
                + (timeToSec($row->horario_saida_2) - timeToSec($row->horario_entrada_2))
                + (timeToSec($row->horario_saida_3) - timeToSec($row->horario_entrada_3))
            );
        }
        $data['total_dias'] = count($totalDias);
        if (empty($data['total_horas'])) {
            if ($descontos->desconto_dias and $descontos->desconto_horas) {
//            $data['total_dias'] = (int)$descontos->desconto_dias;
//            $data['total_horas'] = timeSimpleFormat($descontos->desconto_horas);
                $data['total_horas'] = secToTime($totalHoras, false);
            } else {
//            $data['total_dias'] = count($totalDias) - $descontos->desconto_dias;
                $data['total_horas'] = secToTime($totalHoras - timeToSec($descontos->desconto_horas), false);
            }
        }

        $data['btn_upload_medicoes'] = true;

        $view = $this->load->view('ei/usuario_frequencias_pdf', $data, true);

        preg_match_all('/<body style="color: #000;">(.*?)<\/body>/s', $view, $conteudo);

        return $conteudo[0][0] ?? '';
    }

    //--------------------------------------------------------------------

    public function imprimir()
    {
        if ($this->session->userdata('tipo') != 'funcionario') {
            exit(json_encode(['erro', 'Tipo de usuário não permitido.']));
        }

        $idEscola = $this->input->post('id_escola');
        $idAlocado = $this->input->post('id_alocado');
        $mes = str_pad($this->input->post('mes'), 2, '0', 0);
        $semestre = $this->input->post('semestre');
        $ano = $this->input->post('ano');


        $usuario = $this->db
            ->select('b.id, b.nome, b.cnpj, b.assinatura_digital, c.nome AS funcao')
            ->join('usuarios b', 'b.id = a.id_cuidador')
            ->join('empresa_funcoes c', 'c.id = b.id_funcao')
            ->where('a.id', $idAlocado)
            ->get('ei_alocados a')
            ->row();

        if (empty($usuario)) {
            exit(json_encode(['erro' => 'Usuário não encontrado.']));
        }

        $data['empresa'] = $this->db
            ->select('foto, foto_descricao')
            ->get_where('usuarios', ['id' => $this->session->userdata('empresa')])
            ->row();

        $data['cabecalho'] = null;
        $horariosProgramados = $this->session->userdata('nivel') == NIVEL_ACESSO_CLIENTE_NIVEL_2;
        $strHorarioReal = $horariosProgramados ? '' : '_real';
        $data['horario_real'] = $horariosProgramados ? '' : ' real';
//        $strHorarioReal = '_real';
//        $data['horario_real'] = ' real';
        $idMes = (int)$mes - ($semestre > 1 ? 6 : 0);
        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';

        $this->load->library('calendar');

        $alocacaoEscola = $this->db
            ->select("a.id, GROUP_CONCAT(DISTINCT b.escola ORDER BY b.escola ASC SEPARATOR ' / ') AS escola")
            ->select('d.assinatura_digital, e.assinatura_digital AS assinatura_coordenador')
            ->select('b.id_alocacao, c.depto, c.id_diretoria, c.id_supervisor, c.ano, c.semestre')
            ->select('f.assinatura_digital AS assinatura_aprovacao, f.arquivo_medicao')
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->join('ei_alocados_horarios c2', 'c2.id_alocado = a.id')
            ->join('usuarios d', 'd.id = c.id_supervisor')
            ->join('usuarios e', 'e.id = c.coordenador')
            ->join('usuarios g', 'g.id = a.id_cuidador')
            ->join('ei_alocados_aprovacoes f', "f.id_alocado = a.id AND f.cargo = c2.cargo{$mesCargoFuncao} AND f.funcao = c2.funcao{$mesCargoFuncao} AND f.mes_referencia = '{$mes}'", 'left', false)
            ->where('c.ano', $ano)
            ->where('a.id', $idAlocado)
            ->group_start()
            ->where('b.id_escola', $idEscola)
            ->or_where("CHAR_LENGTH('{$idEscola}') =", 0)
            ->group_end()
//            ->where_in('g.status', [USUARIO_ATIVO, USUARIO_EM_EXPERIENCIA])
            ->group_by('c.id')
            ->get('ei_alocados a')
            ->row();

        $queryString = [
            'profissional' => $usuario->id,
            'escola' => $idEscola,
            'depto' => $alocacaoEscola->depto,
            'diretoria' => $alocacaoEscola->id_diretoria,
            'supervisor' => $alocacaoEscola->id_supervisor,
            'ano' => $ano,
            'mes' => $mes,
            'semestre' => $alocacaoEscola->semestre,
            'aprovacao' => 1,
        ];

        $data['query_string'] = http_build_query($queryString);

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
            ->order_by('data_evento', 'asc')
            ->get('ei_usuarios_frequencias')
            ->result();

        $totalizacao = $this->db
            ->select("MAX(a.total_dias_mes{$idMes}) AS total_dias", false)
            ->select("SUM(TIME_TO_SEC(a.total_horas_mes{$idMes})) AS total_segundos", false)
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->where('d.id', $alocacaoEscola->id_alocacao)
            ->where('a.id_cuidador', $usuario->id)
            ->where('c.id_escola', $idEscola)
            ->get('ei_alocados_totalizacao a')
            ->row();

        $this->load->helper('time');

        $data['total_dias'] = $totalizacao->total_dias ?? null;
        $data['total_horas'] = secToTime($totalizacao->total_segundos ?? null, false);

        $sqlDescontos = $this->db
            ->select('c.id')
            ->select("IFNULL(c.total_dias_mes{$idMes}, SUM(b.desconto_mes{$idMes})) AS desconto_dias")
            ->select("IFNULL(TIME_TO_SEC(c.total_horas_mes{$idMes}), SUM(TIME_TO_SEC(b.total_horas_mes{$idMes}) * b.desconto_mes{$idMes})) AS desconto_horas")
            ->join('ei_alocados_horarios b', 'b.id_alocado = a.id')
            ->join('ei_alocados_totalizacao c', 'c.id_alocado = a.id AND c.periodo = b.periodo')
            ->where('a.id', $alocacaoEscola->id ?? null)
            ->group_by('c.id')
            ->get_compiled_select('ei_alocados a');

        $sqlDescontos = "SELECT s.id, SUM(s.desconto_dias) AS desconto_dias,
                                SEC_TO_TIME(SUM(s.desconto_horas)) AS desconto_horas
                         FROM ({$sqlDescontos}) s";
        $descontos = $this->db->query($sqlDescontos)->row();

        $this->load->model('ei_usuario_frequencia_model', 'usuario_frequencia');

        $data['status'] = $this->usuario_frequencia::STATUS;
        $data['status'] = array_intersect_key($data['status'], array_flip(['FT', 'FR', 'EF', 'RE', 'SB', 'DG']));

        $dataAtual = $this->input->get('data_atual') ?: date('d/m/Y');
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

//        $this->load->helper('time');
        $totalDias = [];
        $totalHoras = 0;
        foreach ($data['rows'] as $row) {
            if (array_key_exists($row->status_entrada_1, $data['status']) == false and
                array_key_exists($row->status_entrada_2, $data['status']) == false and
                array_key_exists($row->status_entrada_3, $data['status']) == false) {
                $totalDias[$row->dia] = $row->dia;
            }
//            $totalDias[$row->dia] = $row->dia;
            $totalHoras += (
                (timeToSec($row->horario_saida_1) - timeToSec($row->horario_entrada_1))
                + (timeToSec($row->horario_saida_2) - timeToSec($row->horario_entrada_2))
                + (timeToSec($row->horario_saida_3) - timeToSec($row->horario_entrada_3))
            );
        }
        $data['total_dias'] = count($totalDias);
        if (empty($data['total_horas'])) {
            if ($descontos->desconto_dias and $descontos->desconto_horas) {
//            $data['total_dias'] = (int)$descontos->desconto_dias;
//            $data['total_horas'] = timeSimpleFormat($descontos->desconto_horas);
                $data['total_horas'] = secToTime($totalHoras, false);
            } else {
//            $data['total_dias'] = count($totalDias) - $descontos->desconto_dias;
                $data['total_horas'] = secToTime($totalHoras - timeToSec($descontos->desconto_horas), false);
            }
        }

        $data['cabecalho'] = $this->session->userdata('nivel') != NIVEL_ACESSO_CLIENTE_NIVEL_2 ? '1' : '';

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function visualizar_arquivo_medicao()
    {
        $this->load->model('ei_alocado_aprovacao_model', 'aprovacao');

        $data = $this->aprovacao
            ->select('id, arquivo_medicao')
            ->findOne($this->input->post('id'));

        if (empty($data)) {
            exit(json_encode(['erro' => 'Medição com aprovação pendente.']));
        }

        $path = $this->aprovacao->getUploadConfig()['arquivo_medicao']['upload_path'] ?? '';
        $data->nome_arquivo_medicao = $data->arquivo_medicao;
        $data->arquivo_medicao = str_replace('./', base_url(), $path) . $data->arquivo_medicao;

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function editar_arquivo_medicao()
    {
        $this->load->model('ei_alocado_aprovacao_model', 'aprovacao');
        $data = $this->aprovacao
            ->select('id, arquivo_medicao')
            ->findOne($this->input->post('id'));
        if (empty($data)) {
            exit(json_encode(['erro' => 'Medição com aprovação pendente.']));
        }
        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function editar_assinatura_digital()
    {
        $this->load->model('ei_alocado_aprovacao_model', 'aprovacao');
        $data = $this->aprovacao
            ->select('id, assinatura_digital')
            ->findOne($this->input->post('id'));
        if (empty($data)) {
            exit(json_encode(['erro' => $this->aprovacao->errors()]));
        }
        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function salvar_status_aprovacao()
    {
        $id = $this->input->post('id');
        if (empty($id)) {
            exit(json_encode(['erro' => 'Nenhuma medição a ser salva.']));
        }

        $this->load->library('entities');

        $post = $this->input->post();
        unset($post['possui_arquivo_medicao']);

        $data = $this->entities->create('EiAlocadoAprovacao', $post);

        $this->load->model('ei_alocado_aprovacao_model', 'aprovacao');

        $this->aprovacao->setValidationLabel('id_alocado', 'Colaborador(a)');
        $this->aprovacao->setValidationLabel('status_aprovacao_escola', 'Status Medição');
        $this->aprovacao->setValidationLabel('data_hora_aprovacao_escola', 'Data/Horário Validação');
        $this->aprovacao->setValidationLabel('nome_aprovador_escola', 'Nome Validador');
        $this->aprovacao->setValidationLabel('observacoes_escola', 'Observações Para Ajustes');

        if ($this->aprovacao->update($id, $data) == false) {
            exit(json_encode(['erro' => $this->aprovacao->errors()]));
        }

        echo json_encode(['msg' => 'Medição ' . ($data->id ? 'atualizada' : 'cadastrada') . ' com sucesso.']);
    }

    //--------------------------------------------------------------------

    public function upload_arquivo_medicao()
    {
        $id = $this->input->post('id');
        $this->load->model('ei_alocado_aprovacao_model', 'aprovacao');
        $data = $this->aprovacao->findOne($id);
        if (empty($data)) {
            exit(json_encode(['erro' => 'Registro não encontrado.']));
        }

        $data->tipo_arquivo = 'P';
        $data->assinatura_digital = null;
        $data->arquivo_medicao = $_FILES['arquivo_medicao']['name'] ?? null;
        $this->aprovacao->setValidationLabel('arquivo_medicao', 'Arquivo');

        if ($this->aprovacao->update($id, $data) == false) {
            exit(json_encode(['erro' => $this->aprovacao->errors()]));
        }

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    public function upload_assinatura_digital()
    {
        $id = $this->input->post('id');
        $this->load->model('ei_alocado_aprovacao_model', 'aprovacao');
        $data = $this->aprovacao->findOne($id);
        if (empty($data)) {
            exit(json_encode(['erro' => 'Registro não encontrado.']));
        }

        $data->tipo_arquivo = 'I';
        $data->arquivo_medicao = null;
        $data->assinatura_digital = $_FILES['assinatura_digital']['name'] ?? null;
        $this->aprovacao->setValidationLabel('assinatura_digital', 'Assinatura Digital');
        if ($this->aprovacao->update($id, $data) == false) {
            exit(json_encode(['erro' => $this->aprovacao->errors()]));
        }
        $alocacao = $this->db
            ->select('a.id, b.id_escola, c.ano, c.semestre')
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->where('a.id', $data->id_alocado)
            ->get('ei_alocados a')
            ->row();
        $relatorio = $this->relatorio_servicos_prestados($alocacao->id, $alocacao->id_escola, $data->mes_referencia, $alocacao->semestre, $alocacao->ano);
        echo json_encode(['relatorio' => $relatorio]);
    }

    //--------------------------------------------------------------------

    public function limpar_arquivos()
    {
        $id = $this->input->post('id');
        $this->load->model('ei_alocado_aprovacao_model', 'aprovacao');
        $data = $this->aprovacao->findOne($id);
        $data->assinatura_digital = null;
        $data->arquivo_medicao = null;
        if ($this->aprovacao->update($id, $data) == false) {
            exit(json_encode(['erro' => $this->aprovacao->errors()]));
        }
        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    public function upload_medicao_assinada2()
    {
        if (isset($_FILES['arquivo']) == false) {
            exit(json_encode(['erro' => 'Nenhum arquivo enviado.']));
        }

        $config = [
            'upload_path' => './arquivos/ei/medicoes/',
            'allowed_types' => 'pdf|gif|jpg|png',
            'file_name' => utf8_decode($_FILES['arquivo']['name']),
        ];

        if ($_FILES['arquivo']['type'] == 'application/pdf') {
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('arquivo') == false) {
                exit(json_encode(['erro' => $this->upload->display_errors()]));
            }
        } else {
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->Image($_FILES['arquivo']['tmp'], 0, 0, 210, 297, 'jpg', '', true, false);

            $data = $this->input->get();

            $nome = 'Relatório de Medições Consolidadas de Educação Inclusiva - ' . $data['ano'];

            $this->m_pdf->pdf->Output($nome . '.pdf', 'D');
        }

        echo json_encode(['status' => true]);
    }

}
