<?php

namespace App\Controllers\Ei\Apontamento;

use App\Controllers\BaseController;

class Medicoes extends BaseController
{

    public function enviar_email_para_aprovacao()
    {
        $data['empresa'] = $this->db
            ->select('foto, foto_descricao')
            ->get_where('usuarios', ['id' => $this->session->userdata('empresa')])
            ->row();

        $data['cabecalho'] = $this->input->post('cabecalho');

        $mes = $this->input->post('mes');
        $ano = $this->input->post('ano');
        $idMes = $this->getIdMes($mes, $this->input->post('semestre'));
        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';

        $this->load->library('calendar');

        $profissional = $this->input->post('profissional');
        $escola = $this->input->post('escola');
        $dataInicio = $this->input->post('data_inicio');
        $dataTermino = $this->input->post('data_termino');

        $usuario = $this->db
            ->select('a.id, a.nome, a.cnpj, b.nome AS cargo, c.nome AS funcao')
            ->join('empresa_cargos b', 'b.id = a.id_cargo')
            ->join('empresa_funcoes c', 'c.id = a.id_funcao')
            ->where('a.id', $profissional)
            ->get('usuarios a')
            ->row();

        $alocacaoEscola = $this->db
            ->select('a.id AS id_alocado, b.escola, b.codigo, b.id_alocacao, b.id_escola')
            ->select('d.email_diretor, d.email_coordenador, d.email_administrativo')
            ->select('e.email_supervisor AS email_supervisao')
            ->select('e.email_coordenador AS email_coordenacao')
            ->select('e.email_administrativo AS email_administracao')
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->join('ei_escolas d', 'd.id = b.id_escola')
            ->join('ei_diretorias e', 'e.id = c.id_diretoria')
            ->where('c.depto', $this->input->post('depto'))
            ->where('c.id_diretoria', $this->input->post('diretoria'))
            ->where('c.id_supervisor', $this->input->post('supervisor'))
            ->where('c.ano', $this->input->post('ano'))
            ->where('c.semestre', $this->input->post('semestre'))
            ->where('b.id_escola', $escola)
            ->where('a.id_cuidador', $profissional)
            ->get('ei_alocados a')
            ->row();

        $horarios = $this->db
            ->select("cargo{$mesCargoFuncao} AS cargo")
            ->select("funcao{$mesCargoFuncao} AS funcao")
            ->where('id_alocado', $alocacaoEscola->id_alocado)
            ->get('ei_alocados_horarios')
            ->result();

        $cargos = array_column($horarios, 'cargo');
        $funcoes = array_column($horarios, 'funcao');

        /*$this->db->where('id_alocacao', $alocacaoEscola->id_alocacao)
            ->where('id_escola', $alocacaoEscola->id_escola)
            ->get('ei_faturament');*/

        $data['profissional'] = $usuario->nome;
        $data['funcao'] = $usuario->funcao ?? null;
        $data['cnpj'] = $usuario->cnpj ?? null;
        $data['mes_ano'] = $this->calendar->get_month_name($mes) . '/' . $ano;
        $data['escola'] = $alocacaoEscola->escola ?? $escola;

        $qb = $this->db
            ->select('b.nome, c.nome AS funcao')
            ->select(["DATE_FORMAT(a.data_evento, '%d') AS dia"], false)
            ->select(["TIME_FORMAT(a.horario_entrada_1, '%H:%i') AS horario_entrada_1"], false)
            ->select(["TIME_FORMAT(a.horario_saida_1, '%H:%i') AS horario_saida_1"], false)
            ->select(["TIME_FORMAT(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, a.horario_entrada_1, a.horario_saida_1)), '%H:%i') AS total_horas_1"], false)
            ->select(["TIME_FORMAT(a.horario_entrada_2, '%H:%i') AS horario_entrada_2"], false)
            ->select(["TIME_FORMAT(a.horario_saida_2, '%H:%i') AS horario_saida_2"], false)
            ->select(["TIME_FORMAT(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, a.horario_entrada_2, a.horario_saida_2)), '%H:%i') AS total_horas_2"], false)
            ->select(["TIME_FORMAT(a.horario_entrada_3, '%H:%i') AS horario_entrada_3"], false)
            ->select(["TIME_FORMAT(a.horario_saida_3, '%H:%i') AS horario_saida_3"], false)
            ->select(["TIME_FORMAT(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, a.horario_entrada_3, a.horario_saida_3)), '%H:%i') AS total_horas_3"], false)
            ->select('a.observacoes')
            ->join('usuarios b', 'b.id = a.id_usuario')
            ->join('empresa_funcoes c', 'c.id = b.id_funcao')
            ->where('MONTH(a.data_evento)', $mes)
            ->where('YEAR(a.data_evento)', $ano);
        if ($profissional) {
            $qb->where('a.id_usuario', $usuario->id);
        }
        /*if ($escola) {
            $qb->where('a.escola', $escola);
        }*/
        if ($dataInicio) {
            $qb->where('a.data_evento <=', $dataInicio);
        }
        if ($dataTermino) {
            $qb->where('a.data_evento <=', $dataTermino);
        }
        $data['rows'] = $qb
            ->group_by('a.id')
            ->order_by('a.data_evento', 'asc')
            ->get('ei_usuarios_frequencias a')
            ->result();

        $destinatarios = array_filter([
//            $alocacaoEscola->email_diretor,
//            $alocacaoEscola->email_coordenador,
//            $alocacaoEscola->email_administrativo,
//            $alocacaoEscola->email_supervisao,
//            $alocacaoEscola->email_supervisao,
//            $alocacaoEscola->email_coordenacao,

            $alocacaoEscola->email_diretor,
            $alocacaoEscola->email_coordenador,
            $alocacaoEscola->email_administrativo,
        ]);

        if (empty($destinatarios)) {
            exit(json_encode(['erro' => 'Nenhum destinatário encontrado.']));
        }

        $this->load->library('email');

        $this->load->library('calendar');
        $msg = "<p style='color: darkblue;'>
                <h3 style='text-align: center; border: 2px solid darkblue; padding: 10px;'>AVISO DE MEDIÇÃO DISPONÍVEL PARA APROVAÇÃO</h3>
                <br><br>
                Prezado(a) sr(a) Diretor(a),
                <br><br>
                Acione o link abaixo para ter acesso ao sistema de aprovação de medições de frequência dos prestadores de serviço do programa Educação Inclusiva.
                <br><br>
                Saudações.
                <br><br>
                <u>" . site_url('cps') . '</u>
                </p>';
        $this->email->from('contato@rhsuite.com.br', 'RhSuite');
        $this->email->to($destinatarios);
        $this->email->set_mailtype('html');
        $this->email->subject('Relatório de Medição');
        $this->email->message($msg);
//        $this->email->attach($filePath . $fileName);

        $status = $this->email->send();

        if ($status == false) {
            exit(json_encode(['erro', 'Não foi possível enviar o e-mail.']));
        }

        $aprovacao = $this->db
            ->where('id_alocado', $alocacaoEscola->id_alocado)
            ->where_in('cargo', $cargos ?: [$usuario->cargo])
            ->where_in('funcao', $funcoes ?: [$usuario->funcao])
            ->where('mes_referencia', $mes)
            ->get('ei_alocados_aprovacoes')
            ->row();

        if (count((array)$aprovacao)) {
            $this->db
                ->set('data_hora_envio_solicitacao', date('Y-m-d H:i:s'))
                ->where('id', $aprovacao->id)
                ->update('ei_alocados_aprovacoes');
        } else {
            $cargosFuncoes = $horarios;
            if (empty($cargosFuncoes)) {
                $cargosFuncoes = (object)[
                    'cargo' => $usuario->cargo,
                    'funcao' => $usuario->funcao,
                ];
            }

            foreach ($cargosFuncoes as $cargoFuncao) {
                $this->db
                    ->set('id_alocado', $alocacaoEscola->id_alocado)
                    ->set('cargo', $cargoFuncao->cargo)
                    ->set('funcao', $cargoFuncao->funcao)
                    ->set('mes_referencia', $mes)
                    ->set('data_hora_envio_solicitacao', date('Y-m-d H:i:s'))
                    ->insert('ei_alocados_aprovacoes');
            }
        }

        /*$faturamento = $this->db
            ->where('id_alocacao', $alocacaoEscola->id_alocacao)
            ->where('id_escola', $alocacaoEscola->id_escola)
            ->get('ei_faturamento')
            ->row();

        if ($faturamento) {
            $this->db
                ->set('data_envio_solicitacao_mes' . $idMes, date('Y-m-d'))
                ->where('id', $faturamento->id)
                ->update('ei_faturamento');
        } else {
            $this->db
                ->set('id_alocacao', $alocacaoEscola->id_alocacao)
                ->set('id_escola', $alocacaoEscola->id_escola)
                ->set('escola', $alocacaoEscola->escola)
                ->set('cargo', $usuario->cargo)
                ->set('funcao', $usuario->funcao)
                ->set('data_envio_solicitacao_mes' . $idMes, date('Y-m-d'))
                ->insert('ei_faturamento');
        }*/

        echo json_encode(['status' => $status]);
    }

    //--------------------------------------------------------------------

    public function enviar_email_para_aprovacao_old()
    {
        $data['empresa'] = $this->db
            ->select('foto, foto_descricao')
            ->get_where('usuarios', ['id' => $this->session->userdata('empresa')])
            ->row();

        $data['cabecalho'] = $this->input->post('cabecalho');

        $mes = $this->input->post('mes');
        $ano = $this->input->post('ano');
        $idMes = $this->getIdMes($mes, $this->input->post('semestre'));

        $this->load->library('calendar');

        $profissional = $this->input->post('profissional');
        $escola = $this->input->post('escola');
        $dataInicio = $this->input->post('data_inicio');
        $dataTermino = $this->input->post('data_termino');

        $usuario = $this->db
            ->select('a.id, a.nome, a.cnpj, b.nome AS cargo, c.nome AS funcao')
            ->join('empresa_cargos b', 'b.id = a.id_cargo')
            ->join('empresa_funcoes c', 'c.id = a.id_funcao')
            ->where('a.id', $profissional)
            ->get('usuarios a')
            ->row();

        $alocacaoEscola = $this->db
            ->select('b.escola, b.codigo, b.id_alocacao, b.id_escola')
            ->select('d.email_diretor, d.email_coordenador, d.email_administrativo')
            ->select('e.email_supervisor AS email_supervisao')
            ->select('e.email_coordenador AS email_coordenacao')
            ->select('e.email_administrativo AS email_administracao')
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->join('ei_escolas d', 'd.id = b.id_escola')
            ->join('ei_diretorias e', 'e.id = c.id_diretoria')
            ->where('c.depto', $this->input->post('depto'))
            ->where('c.id_diretoria', $this->input->post('diretoria'))
            ->where('c.id_supervisor', $this->input->post('supervisor'))
            ->where('c.ano', $this->input->post('ano'))
            ->where('c.semestre', $this->input->post('semestre'))
            ->where('a.id_cuidador', $profissional)
            ->get('ei_alocados a')
            ->row();

        /*$this->db->where('id_alocacao', $alocacaoEscola->id_alocacao)
            ->where('id_escola', $alocacaoEscola->id_escola)
            ->get('ei_faturament');*/

        $data['profissional'] = $usuario->nome;
        $data['funcao'] = $usuario->funcao ?? null;
        $data['cnpj'] = $usuario->cnpj ?? null;
        $data['mes_ano'] = $this->calendar->get_month_name($mes) . '/' . $ano;
        $data['escola'] = $alocacaoEscola->escola ?? $escola;

        $qb = $this->db
            ->select('b.nome, c.nome AS funcao')
            ->select(["DATE_FORMAT(a.data_evento, '%d') AS dia"], false)
            ->select(["TIME_FORMAT(a.horario_entrada_1, '%H:%i') AS horario_entrada_1"], false)
            ->select(["TIME_FORMAT(a.horario_saida_1, '%H:%i') AS horario_saida_1"], false)
            ->select(["TIME_FORMAT(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, a.horario_entrada_1, a.horario_saida_1)), '%H:%i') AS total_horas_1"], false)
            ->select(["TIME_FORMAT(a.horario_entrada_2, '%H:%i') AS horario_entrada_2"], false)
            ->select(["TIME_FORMAT(a.horario_saida_2, '%H:%i') AS horario_saida_2"], false)
            ->select(["TIME_FORMAT(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, a.horario_entrada_2, a.horario_saida_2)), '%H:%i') AS total_horas_2"], false)
            ->select(["TIME_FORMAT(a.horario_entrada_3, '%H:%i') AS horario_entrada_3"], false)
            ->select(["TIME_FORMAT(a.horario_saida_3, '%H:%i') AS horario_saida_3"], false)
            ->select(["TIME_FORMAT(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, a.horario_entrada_3, a.horario_saida_3)), '%H:%i') AS total_horas_3"], false)
            ->select('a.observacoes')
            ->join('usuarios b', 'b.id = a.id_usuario')
            ->join('empresa_funcoes c', 'c.id = b.id_funcao')
            ->where('MONTH(a.data_evento)', $mes)
            ->where('YEAR(a.data_evento)', $ano);
        if ($profissional) {
            $qb->where('a.id_usuario', $usuario->id);
        }
        /*if ($escola) {
            $qb->where('a.escola', $escola);
        }*/
        if ($dataInicio) {
            $qb->where('a.data_evento <=', $dataInicio);
        }
        if ($dataTermino) {
            $qb->where('a.data_evento <=', $dataTermino);
        }
        $data['rows'] = $qb
            ->group_by('a.id')
            ->order_by('a.data_evento', 'asc')
            ->get('ei_usuarios_frequencias a')
            ->result();

        $this->load->library('m_pdf');

        $stylesheet = '#table thead th { font-size: 13px; padding: 5px; background-color: #f5f5f5;} ';
        $stylesheet .= '#table tbody td { font-size: 14px; padding: 5px; vertical-align: top; } ';
        $stylesheet .= '#livro_ata thead tr th { padding: 5px; text-align: center; background-color: #f5f5f5; border-color: #ddd; } ';
        $stylesheet .= '#livro_ata tbody tr td { font-size: 13px; padding: 5px; } ';

        $this->m_pdf->pdf->setTopMargin($data['cabecalho'] ? 100 : 80);
        $this->m_pdf->pdf->AddPage('L');
        $this->m_pdf->pdf->writeHTML($stylesheet, 1);
        $this->m_pdf->pdf->writeHTML($this->load->view('ei/usuario_frequencias_pdf', $data, true));

        $this->calendar->month_type = 'short';

//        $this->m_pdf->pdf->Output('Frequências usuário - ' . $data['mes_ano'] . '.pdf', 'D');
        $filePath = 'arquivos/ei/medicao/';
        $fileName = "FAT-{$alocacaoEscola->codigo}-{$mes}-{$ano}.pdf";

        $this->m_pdf->pdf->Output($filePath . $fileName, \Mpdf\Output\Destination::FILE);

        $destinatarios = array_filter([
            $alocacaoEscola->email_diretor,
            $alocacaoEscola->email_coordenador,
            $alocacaoEscola->email_administrativo,
            $alocacaoEscola->email_supervisao,
            $alocacaoEscola->email_supervisao,
            $alocacaoEscola->email_coordenacao,
        ]);

        if (empty($destinatarios)) {
            exit(json_encode(['erro' => 'Nenhum destinatário encontrado.']));
        }

        $this->load->library('email');

        $this->load->library('calendar');
        $nomeMes = ucfirst($this->calendar->get_month_name(str_pad($mes, 2, '0', STR_PAD_LEFT)));

        $nomes = implode(', ', array_unique(array_column($data['rows'], 'nome')));
        $funcoes = implode(', ', array_unique(array_column($data['rows'], 'funcao')));
        $msg = "Prezados,<br><br>
               Segue anexo relatório de medição mensal referente a:<br><br>
               Nome: {$nomes}<br>
               Mês/ano: {$nomeMes}/{$ano}<br>
               Serviço: {$funcoes}<br><br>
               Saudações.";
        $this->email->from('contato@rhsuite.com.br', 'RhSuite');
        $this->email->to($destinatarios);
        $this->email->set_mailtype('html');
        $this->email->subject('Relatório de Medição');
        $this->email->message($msg);
        $this->email->attach($filePath . $fileName);

        $status = $this->email->send();

        if ($status == false) {
            exit(json_encode(['erro', 'Não foi possível enviar o e-mail.']));
        }

        $faturamento = $this->db
            ->where('id_alocacao', $alocacaoEscola->id_alocacao)
            ->where('id_escola', $alocacaoEscola->id_escola)
            ->get('ei_faturamento')
            ->row();

        if ($faturamento) {
            $this->db
                ->set('data_envio_solicitacao_mes' . $idMes, date('Y-m-d'))
                ->where('id', $faturamento->id)
                ->update('ei_faturamento');
        } else {
            $this->db
                ->set('id_alocacao', $alocacaoEscola->id_alocacao)
                ->set('id_escola', $alocacaoEscola->id_escola)
                ->set('escola', $alocacaoEscola->escola)
                ->set('cargo', $usuario->cargo)
                ->set('funcao', $usuario->funcao)
                ->set('data_envio_solicitacao_mes' . $idMes, date('Y-m-d'))
                ->insert('ei_faturamento');
        }

        echo json_encode(['status' => $status]);
    }

    //--------------------------------------------------------------------

    private function getIdMes(?string $mes, ?int $semestre): int
    {
        $semestre = intval($mes) > 7 ? 2 : (intval($mes) < 7 ? 1 : $semestre);
        return $mes - ($semestre > 1 ? 6 : 0);
    }

    //--------------------------------------------------------------------

    public function relatorio_servicos_prestados()
    {
        $data['empresa'] = $this->db
            ->select('foto, foto_descricao')
            ->where('id', $this->session->userdata('empresa'))
            ->get('usuarios')
            ->row();

        $data['cabecalho'] = $this->input->get('cabecalho');
        $horariosReais = $this->input->get('horarios_reais');
        $strHorarioReal = $horariosReais === '1' ? '_real' : '';
        $data['horario_real'] = $horariosReais === '1' ? ' real' : '';

        $mes = $this->input->get('mes');
        $ano = $this->input->get('ano');
        $semestre = $this->input->get('semestre');
        $idMes = (int)$mes - ($semestre > 1 ? 6 : 0);

        $this->load->library('calendar');

        $profissional = $this->input->get('profissional');
        $escola = $this->input->get('escola');
        $periodo = $this->input->get('periodo');
        $dataInicio = $this->input->get('data_inicio');
        $dataTermino = $this->input->get('data_termino');
        $status = $this->input->get('status');

        $usuarioAtual = $this->db
            ->select('assinatura_digital')
            ->where('id', $this->session->userdata('id'))
            ->get('usuarios')
            ->row();

        $usuario = $this->db
            ->select('a.id, a.nome, a.cnpj, a.assinatura_digital, b.nome AS funcao')
            ->join('empresa_funcoes b', 'b.id = a.id_funcao')
            ->where('a.id', $profissional)
            ->get('usuarios a')
            ->row();

        $alocacaoEscola = $this->db
            ->select("a.id, c.id AS id_alocacao, GROUP_CONCAT(DISTINCT b.escola ORDER BY b.escola ASC SEPARATOR ' / ') AS escola")
            ->select('d.assinatura_digital, e.assinatura_digital AS assinatura_coordenador')
            ->select('f.assinatura_digital AS assinatura_aprovacao, f.arquivo_medicao')
            ->select(['IFNULL(h.assinatura_digital, IFNULL(i.assinatura_digital, j.assinatura_digital)) AS assinatura_diretor'], false)
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->join('usuarios d', 'd.id = c.id_supervisor')
            ->join('usuarios e', 'e.id = c.coordenador')
            ->join('ei_alocados_aprovacoes f', "f.id_alocado = a.id AND f.mes_referencia = '{$mes}'", 'left')
            ->join('ei_escolas g', 'g.id = b.id_escola', 'left')
            ->join('usuarios h', 'h.email = g.email_diretor', 'left')
            ->join('usuarios i', 'i.email = g.email_coordenador', 'left')
            ->join('usuarios j', 'j.email = g.email_administrativo', 'left')
            ->where('c.depto', $this->input->get('depto'))
            ->where('c.id_diretoria', $this->input->get('diretoria'))
            ->where('c.id_supervisor', $this->input->get('supervisor'))
            ->where('c.ano', $this->input->get('ano'))
            ->where('c.semestre', $this->input->get('semestre'))
            ->where('a.id_cuidador', $this->input->get('profissional'))
            ->group_start()
            ->where('b.id_escola', $escola)
            ->or_where("CHAR_LENGTH('{$escola}') =", 0)
            ->group_end()
            ->group_by('c.id')
            ->get('ei_alocados a')
            ->row();

        $data['profissional'] = $usuario->nome ?? null;
        $data['funcao'] = $usuario->funcao ?? null;
        $data['cnpj'] = $usuario->cnpj ?? null;
        $data['mes_ano'] = $this->calendar->get_month_name($mes) . '/' . $ano;
        $data['escola'] = $alocacaoEscola->escola ?? $escola;
        $data['assinatura_digital_prestador'] = $usuario->assinatura_digital ?? null;
        $data['assinatura_digital_coordenador'] = $alocacaoEscola->assinatura_coordenador ?? null;
        $data['assinatura_digital_supervisor'] = $alocacaoEscola->assinatura_digital ?? $alocacaoEscola->assinatura_diretor ?? null;
        $data['assinatura_digital_aprovacao'] = $alocacaoEscola->assinatura_aprovacao ?? null;
        $data['query_string'] = http_build_query($this->input->get());
        $data['is_pdf'] = false;

        $qb = $this->db
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
//            ->where("(status_entrada_1 NOT IN('FT', 'FR', 'EF','RE') OR status_entrada_1 IS NULL)", null, false)
//            ->where("(status_entrada_2 NOT IN('FT', 'FR', 'EF','RE') OR status_entrada_2 IS NULL)", null, false)
//            ->where("(status_entrada_3 NOT IN('FT', 'FR', 'EF','RE') OR status_entrada_3 IS NULL)", null, false)
//            ->where("(status_saida_1 NOT IN('FT', 'FR', 'EF','RE') OR status_saida_1 IS NULL)", null, false)
//            ->where("(status_saida_2 NOT IN('FT', 'FR', 'EF','RE') OR status_saida_2 IS NULL)", null, false)
//            ->where("(status_saida_3 NOT IN('FT', 'FR', 'EF','RE') OR status_saida_3 IS NULL)", null, false)
            ->where('YEAR(data_evento)', $ano)
            ->where('MONTH(data_evento)', $mes);
        if ($profissional) {
            $qb->where('id_usuario', $usuario->id);
        }
        if ($escola) {
            $qb->where('id_escola', $escola);
        }
        if ($periodo) {
            $qb->group_start()
                ->where("horario_entrada{$strHorarioReal}_{$periodo} IS NOT NULL")
                ->or_where("horario_saida{$strHorarioReal}_{$periodo} IS NOT NULL")
                ->group_end();
        }
        if ($dataInicio) {
            $qb->where('data_evento <=', $dataInicio);
        }
        if ($dataTermino) {
            $qb->where('data_evento <=', $dataTermino);
        }
        $data['rows'] = $qb
            ->order_by('data_evento')
            ->order_by("IFNULL(horario_entrada{$strHorarioReal}_1, '0:00')")
            ->order_by("IFNULL(horario_saida{$strHorarioReal}_1, '0:00')")
            ->order_by("IFNULL(horario_entrada{$strHorarioReal}_2, '0:00')")
            ->order_by("IFNULL(horario_saida{$strHorarioReal}_2, '0:00')")
            ->order_by("IFNULL(horario_entrada{$strHorarioReal}_3, '0:00')")
            ->order_by("IFNULL(horario_saida{$strHorarioReal}_3, '0:00')")
            ->get('ei_usuarios_frequencias')
            ->result();

        $qb = $this->db
            ->select("MAX(a.total_dias_mes{$idMes}) AS total_dias", false)
            ->select("SUM(TIME_TO_SEC(a.total_horas_mes{$idMes})) AS total_segundos", false)
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->where('d.id', $alocacaoEscola->id_alocacao);
        if ($profissional) {
            $qb->where('a.id_cuidador', $usuario->id);
        }
        if ($escola) {
            $qb->where('c.id_escola', $escola);
        }
        if ($periodo) {
            $qb->where('a.periodo', $periodo);
        }
        $totalizacao = $qb
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
            ->group_start()
            ->where('b.periodo', $periodo)
            ->or_where("CHAR_LENGTH('{$periodo}') =", 0)
            ->group_end()
            ->group_by('c.id')
            ->get_compiled_select('ei_alocados a');
        $sqlDescontos = "SELECT s.id, SUM(s.desconto_dias) AS desconto_dias,
                                SEC_TO_TIME(SUM(s.desconto_horas)) AS desconto_horas
                         FROM ({$sqlDescontos}) s";
        $descontos = $this->db->query($sqlDescontos)->row();

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
//        if (empty($data['total_dias'])) {
        $data['total_dias'] = count($totalDias);
//        }
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

        $this->load->view('ei/usuario_frequencias_pdf', $data);
    }

    //--------------------------------------------------------------------

    public function ajax_list()
    {
        parse_str($this->input->post('busca'), $busca);
        parse_str($this->input->post('filtro'), $filtro);

        $timestamp = mktime(0, 0, 0, (int)$busca['mes'], 1, (int)$busca['ano']);
        $dataInicioMes = date('Y-m-d', $timestamp);
        $dataTerminoMes = date('Y-m-t', $timestamp);

        $strStatus = "'FT', 'FR', 'EF', 'RE', 'SB', 'DG'";

        $qb = $this->db
            ->select('b.nome, a.data_evento')
            ->select(["IF(a.status_entrada_1 IN ({$strStatus}), a.status_entrada_1, TIME_FORMAT(a.horario_entrada_1, '%H:%i')) AS horario_entrada_1"], false)
            ->select(["IF(a.status_entrada_1 IN ({$strStatus}), a.status_entrada_1, TIME_FORMAT(a.horario_entrada_real_1, '%H:%i')) AS horario_entrada_real_1"], false)
            ->select(["IF(a.status_entrada_1 IN ({$strStatus}), NULL, TIME_FORMAT(TIMEDIFF(a.horario_entrada_1, TIME(a.horario_entrada_real_1)), '%H:%i')) AS horario_entrada_dif_1"], false)
            ->select(["IF(a.status_saida_1 IN ({$strStatus}), a.status_saida_1, TIME_FORMAT(a.horario_saida_1, '%H:%i')) AS horario_saida_1"], false)
            ->select(["IF(a.status_saida_1 IN ({$strStatus}), a.status_saida_1, TIME_FORMAT(a.horario_saida_real_1, '%H:%i')) AS horario_saida_real_1"], false)
            ->select(["IF(a.status_saida_1 IN ({$strStatus}), NULL, TIME_FORMAT(TIMEDIFF(TIME(a.horario_saida_real_1), a.horario_saida_1), '%H:%i')) AS horario_saida_dif_1"], false)

//            ->select(["TIME_FORMAT(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, a.horario_entrada_1, a.horario_saida_1)), '%H:%i') AS total_horas_1"], false)
            ->select(["IF(a.status_entrada_2 IN ({$strStatus}), a.status_entrada_2, TIME_FORMAT(a.horario_entrada_2, '%H:%i')) AS horario_entrada_2"], false)
            ->select(["IF(a.status_entrada_2 IN ({$strStatus}), a.status_entrada_2, TIME_FORMAT(a.horario_entrada_real_2, '%H:%i')) AS horario_entrada_real_2"], false)
            ->select(["IF(a.status_entrada_2 IN ({$strStatus}), NULL, TIME_FORMAT(TIMEDIFF(a.horario_entrada_2, TIME(a.horario_entrada_real_2)), '%H:%i')) AS horario_entrada_dif_2"], false)
            ->select(["IF(a.status_saida_2 IN ({$strStatus}), a.status_saida_2, TIME_FORMAT(a.horario_saida_2, '%H:%i')) AS horario_saida_2"], false)
            ->select(["IF(a.status_saida_2 IN ({$strStatus}), a.status_saida_2, TIME_FORMAT(a.horario_saida_real_2, '%H:%i')) AS horario_saida_real_2"], false)
            ->select(["IF(a.status_saida_2 IN ({$strStatus}), NULL, TIME_FORMAT(TIMEDIFF(TIME(a.horario_saida_real_2), a.horario_saida_2), '%H:%i')) AS horario_saida_dif_2"], false)

//            ->select(["TIME_FORMAT(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, a.horario_entrada_2, a.horario_saida_2)), '%H:%i') AS total_horas_2"], false)
            ->select(["IF(a.status_entrada_3 IN ({$strStatus}), a.status_entrada_3, TIME_FORMAT(a.horario_entrada_3, '%H:%i')) AS horario_entrada_3"], false)
            ->select(["IF(a.status_entrada_3 IN ({$strStatus}), a.status_entrada_3, TIME_FORMAT(a.horario_entrada_real_3, '%H:%i')) AS horario_entrada_real_3"], false)
            ->select(["IF(a.status_entrada_3 IN ({$strStatus}), NULL, TIME_FORMAT(TIMEDIFF(a.horario_entrada_3, TIME(a.horario_entrada_real_3)), '%H:%i')) AS horario_entrada_dif_3"], false)
            ->select(["IF(a.status_saida_3 IN ({$strStatus}), a.status_saida_3, TIME_FORMAT(a.horario_saida_3, '%H:%i')) AS horario_saida_3"], false)
            ->select(["IF(a.status_saida_3 IN ({$strStatus}), a.status_saida_3, TIME_FORMAT(a.horario_saida_real_3, '%H:%i')) AS horario_saida_real_3"], false)
            ->select(["IF(a.status_saida_3 IN ({$strStatus}), NULL, TIME_FORMAT(TIMEDIFF(TIME(a.horario_saida_real_3), a.horario_saida_3), '%H:%i')) AS horario_saida_dif_3"], false)
//            ->select(["TIME_FORMAT(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, a.horario_entrada_3, a.horario_saida_3)), '%H:%i') AS total_horas_3"], false)
            ->select('a.observacoes, a.justificativa, a.avaliacao_justificativa, a.id')
            ->select(["DATE_FORMAT(a.data_evento, '%d/%m/%Y') AS data_evento_de"], false)
            ->join('usuarios b', 'b.id = a.id_usuario')
            ->where('b.empresa', $this->session->userdata('empresa'));
        if (!empty($filtro['profissional'])) {
            $qb->where('a.id_usuario', $filtro['profissional']);
        }
        if (!empty($filtro['escola'])) {
            $qb->where('a.id_escola', $filtro['escola']);
        }
        if (!empty($filtro['periodo'])) {
            $qb->group_start()
                ->where("horario_entrada_{$filtro['periodo']} IS NOT NULL")
                ->or_where("horario_entrada_real_{$filtro['periodo']} IS NOT NULL")
                ->or_where("horario_saida_{$filtro['periodo']} IS NOT NULL")
                ->or_where("horario_saida_real_{$filtro['periodo']} IS NOT NULL")
                ->group_end();
        }
        $sql = $qb
            ->where('a.data_evento >=', $dataInicioMes)
            ->where('a.data_evento <=', $dataTerminoMes)
            ->order_by('b.nome', 'asc')
            ->order_by('a.data_evento', 'asc')
            ->get_compiled_select('ei_usuarios_frequencias a');

        $this->load->library('dataTables');

        $output = $this->datatables->query($sql);

        $data = [];

        foreach ($output->data as $row) {
            $data[] = [
                $row->nome,
                $row->data_evento_de,
                $row->horario_entrada_1,
                $row->horario_entrada_real_1,
                $row->horario_entrada_dif_1,
                $row->horario_saida_1,
                $row->horario_saida_real_1,
                $row->horario_saida_dif_1,
//                $row->total_horas_1,
                $row->horario_entrada_2,
                $row->horario_entrada_real_2,
                $row->horario_entrada_dif_2,
                $row->horario_saida_2,
                $row->horario_saida_real_2,
                $row->horario_saida_dif_2,
//                $row->total_horas_2,
                $row->horario_entrada_3,
                $row->horario_entrada_real_3,
                $row->horario_entrada_dif_3,
                $row->horario_saida_3,
                $row->horario_saida_real_3,
                $row->horario_saida_dif_3,
//                $row->total_horas_3,
                $row->observacoes,
                $row->justificativa,
                $row->avaliacao_justificativa,
                '<button class="btn btn-sm btn-info" onclick="edit_medicao(' . $row->id . ');" title="Editar medição"><i class="fa fa-pencil"></i></button>
                 <button class="btn btn-sm btn-danger" onclick="delete_medicao(' . $row->id . ');" title="Excluir medição"><i class="fa fa-trash"></i></button>',
                $row->id,
            ];
        }

        $output->data = $data;

        $livroATA1 = $this->db
            ->select('a.id_usuario, b.nome')
            ->join('usuarios b', 'b.id = a.id_usuario')
            ->where('b.empresa', $this->session->userdata('empresa'))
            ->where('YEAR(a.data_evento)', $busca['ano'])
            ->where_in('b.status', [USUARIO_ATIVO, USUARIO_EM_EXPERIENCIA])
//            ->where('MONTH(a.data_evento)', $busca['mes'])
            ->group_by('a.id_usuario')
            ->order_by('TRIM(b.nome)', 'asc')
            ->get('ei_usuarios_frequencias a')
            ->result_array();

        $colaboradores = ['' => 'Todos'] + array_column($livroATA1, 'nome', 'id_usuario');

        $livroATA2 = $this->db
            ->select("a.id_escola, b.codigo, b.nome")
            ->join('ei_escolas b', 'b.id = a.id_escola')
            ->join('ei_diretorias c', 'c.id = b.id_diretoria')
            ->where('c.id_empresa', $this->session->userdata('empresa'))
            ->where('YEAR(a.data_evento)', $busca['ano'])
//            ->where('MONTH(a.data_evento)', $busca['mes'])
            ->group_start()
            ->where('a.id_usuario', $filtro['profissional'])
            ->or_where("CHAR_LENGTH('{$filtro['profissional']}') = 0")
            ->group_end()
            ->group_by('a.id_escola')
            ->order_by('b.codigo', 'asc')
            ->order_by('b.nome', 'asc')
            ->get('ei_usuarios_frequencias a')
            ->result_array();

        $escolas = ['' => 'Todas'];
        foreach ($livroATA2 as $row) {
            $escolas[$row['id_escola']] = implode(' - ', [$row['codigo'], $row['nome']]);
        }

        $output->colaboradores = form_dropdown('', $colaboradores, $filtro['profissional']);
        $output->escolas = form_dropdown('', $escolas, $filtro['escola']);

        $qb = $this->db;
        if (!empty($busca['depto'])) {
            $qb->where('c.depto', $busca['depto']);
        }
        if (!empty($busca['diretoria'])) {
            $qb->where('c.id_diretoria', $busca['diretoria']);
        }
        if (!empty($busca['supervisor'])) {
            $qb->where('c.id_supervisor', $busca['supervisor']);
        }
        $aprovacao = $qb
            ->select('d.status_aprovacao_escola, d.observacoes_escola, d.arquivo_medicao')
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->join('ei_alocados_aprovacoes d', "d.id_alocado = a.id and d.mes_referencia = '{$busca['mes']}'", 'left')
            ->where('c.id_empresa', $this->session->userdata('empresa'))
            ->where('c.ano', $busca['ano'])
            ->where('c.semestre', $busca['semestre'])
            ->where('a.id_cuidador', $filtro['profissional'])
            ->where('b.id_escola', $filtro['escola'])
            ->group_by('a.id')
            ->get('ei_alocados a')
            ->row();

        $this->load->model('ei_alocado_aprovacao_model', 'aprovacao');
        $statusAprovacao = ['' => 'selecione...'] + $this->aprovacao::STATUS;
        $output->status_aprovacao = form_dropdown('', $statusAprovacao, $aprovacao->status_aprovacao_escola ?? '');
        $output->possui_arquivo_medicao = !empty($aprovacao->arquivo_medicao);
        $output->observacoes = $aprovacao->observacoes_escola ?? null;

        echo json_encode($output);
    }

    //--------------------------------------------------------------------

    public function ajax_edit_avaliacao_justificativa()
    {
        $data = $this->db
            ->select('id, avaliacao_justificativa')
            ->where('id', $this->input->post('id'))
            ->get('ei_usuarios_frequencias')
            ->row();

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_save_avaliacao_justificativa()
    {
        $this->load->model('ei_usuario_frequencia_model', 'frequencia');
        $avaliacaoJustificativa = $this->input->post('avaliacao_justificativa');
        if (strlen($avaliacaoJustificativa) == 0) {
            $avaliacaoJustificativa = null;
        }

        $this->db
            ->set('avaliacao_justificativa', $avaliacaoJustificativa)
            ->where('id', $this->input->post('id'))
            ->update('ei_usuarios_frequencias');

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    public function ajax_new()
    {
        $idUsuario = $this->input->post('id_usuario');

        $qb = $this->db
            ->select('a.id, a.nome')
            ->join('empresa_departamentos b', 'b.id = a.id_depto')
            ->where('a.empresa', $this->session->userdata('empresa'))
            ->where('b.nome', 'Educação Inclusiva')
            ->where_in('a.status', [USUARIO_ATIVO, USUARIO_EM_EXPERIENCIA]);
        if ($idUsuario) {
            $qb->where('a.id', $idUsuario);
        }
        $colaboradores = $qb
            ->order_by('TRIM(a.nome)', 'asc')
            ->get('usuarios a')
            ->result_array();

        $colaboradores = ['' => 'selecione...'] + array_column($colaboradores, 'nome', 'id');

        $idEscola = $this->input->post('id_escola');

        $qb = $this->db
            ->select('a.id, a.nome')
            ->join('ei_diretorias b', 'b.id = a.id_diretoria')
            ->where('b.id_empresa', $this->session->userdata('empresa'));
        if ($idEscola) {
            $qb->where('a.id', $idEscola);
        }
        $escolas = $qb
            ->order_by('TRIM(a.nome)', 'asc')
            ->get('ei_escolas a')
            ->result_array();

        $escolas = ['' => 'selecione...'] + array_column($escolas, 'nome', 'id');

        $data = [
            'usuarios' => form_dropdown('', $colaboradores, $idUsuario),
            'escolas' => form_dropdown('', $escolas, $idEscola),
        ];

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_edit()
    {
        $data = $this->db
            ->select('a.*, b.nome, c.codigo, c.nome AS escola', false)
            ->join('usuarios b', 'b.id = a.id_usuario')
            ->join('ei_escolas c', 'c.id = a.id_escola')
            ->where('a.id', $this->input->post('id'))
            ->get('ei_usuarios_frequencias a')
            ->row();

        if (empty($data)) {
            exit(json_encode(['erro' => 'Erro ao editar a medição.']));
        }

        $escolas = [$data->id_escola => implode(' - ', array_filter([$data->codigo, $data->escola]))];
        $data->escolas = form_dropdown('', $escolas, $data->id_escola);

        $data->data_evento = dateFormat($data->data_evento);
        $this->load->helper('time');
        $data->horario_entrada_1 = timeSimpleFormat($data->horario_entrada_1);
        $data->horario_entrada_2 = timeSimpleFormat($data->horario_entrada_2);
        $data->horario_entrada_3 = timeSimpleFormat($data->horario_entrada_3);
        $data->horario_saida_1 = timeSimpleFormat($data->horario_saida_1);
        $data->horario_saida_2 = timeSimpleFormat($data->horario_saida_2);
        $data->horario_saida_3 = timeSimpleFormat($data->horario_saida_3);
        $data->horario_entrada_real_1 = timeSimpleFormat($data->horario_entrada_real_1);
        $data->horario_entrada_real_2 = timeSimpleFormat($data->horario_entrada_real_2);
        $data->horario_entrada_real_3 = timeSimpleFormat($data->horario_entrada_real_3);
        $data->horario_saida_real_1 = timeSimpleFormat($data->horario_saida_real_1);
        $data->horario_saida_real_2 = timeSimpleFormat($data->horario_saida_real_2);
        $data->horario_saida_real_3 = timeSimpleFormat($data->horario_saida_real_3);
        $data->usuarios = form_dropdown('', [$data->id_usuario => $data->nome], $data->id_usuario);

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function filtrar_escolas()
    {
        parse_str($this->input->post('busca'), $busca);
        $idUsuario = $this->input->post('id_usuario');

        $qb = $this->db
            ->select("b.id_escola, c.codigo, c.nome")
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_escolas c', 'c.id = b.id_escola')
            ->join('ei_alocacao d', 'd.id = b.id_alocacao')
            ->where('d.id_empresa', $this->session->userdata('empresa'))
            ->where('d.depto', $busca['depto'])
            ->where('d.id_diretoria', $busca['diretoria'])
            ->where('d.id_supervisor', $busca['supervisor'])
            ->where('d.ano', $busca['ano'])
            ->where('d.semestre', $busca['semestre']);
        if ($idUsuario) {
            $qb->where('a.id_cuidador', $idUsuario);
        }
        $rowEscolas = $qb
            ->group_by('b.id_escola')
            ->order_by('c.codigo', 'asc')
            ->order_by('c.nome', 'asc')
            ->get('ei_alocados a')
            ->result_array();

        $escolas = ['' => 'selecione...'];
        foreach ($rowEscolas as $row) {
            $escolas[$row['id_escola']] = implode(' - ', array_filter([$row['codigo'], $row['nome']]));
        }

        $data = [
            'escolas' => form_dropdown('', $escolas, $this->input->post('id_escola')),
        ];

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_save()
    {
        $this->load->library('entities');

        $data = $this->entities->create('EiUsuarioFrequencia', $this->input->post());

        if (strlen($data->horario_entrada_real_1) > 0) {
            $data->horario_entrada_real_1 = $data->data_evento . ' ' . $data->horario_entrada_real_1;
        }
        if (strlen($data->horario_saida_real_1) > 0) {
            $data->horario_saida_real_1 = $data->data_evento . ' ' . $data->horario_saida_real_1;
        }
        if (strlen($data->horario_entrada_real_2) > 0) {
            $data->horario_entrada_real_2 = $data->data_evento . ' ' . $data->horario_entrada_real_2;
        }
        if (strlen($data->horario_saida_real_2) > 0) {
            $data->horario_saida_real_2 = $data->data_evento . ' ' . $data->horario_saida_real_2;
        }
        if (strlen($data->horario_entrada_real_3) > 0) {
            $data->horario_entrada_real_3 = $data->data_evento . ' ' . $data->horario_entrada_real_3;
        }
        if (strlen($data->horario_saida_real_3) > 0) {
            $data->horario_saida_real_3 = $data->data_evento . ' ' . $data->horario_saida_real_3;
        }

        $this->load->model('ei_usuario_frequencia_model', 'medicao');

        $this->medicao->setValidationLabel('id_usuario', 'Colaborador');
        $this->medicao->setValidationLabel('data_evento', 'Data');
        $this->medicao->setValidationLabel('horario_entrada_1', 'Entrada Programada (Manhã)');
        $this->medicao->setValidationLabel('horario_entrada_2', 'Entrada Programada (Tarde)');
        $this->medicao->setValidationLabel('horario_entrada_3', 'Entrada Programada (Noite)');
        $this->medicao->setValidationLabel('horario_saida_1', 'Saída Programada (Manhã)');
        $this->medicao->setValidationLabel('horario_saida_2', 'Saída Programada (Tarde)');
        $this->medicao->setValidationLabel('horario_saida_3', 'Saída Programada (Noite)');
        $this->medicao->setValidationLabel('horario_entrada_real_1', 'Entrada Real (Manhã)');
        $this->medicao->setValidationLabel('horario_entrada_real_2', 'Entrada Real (Tarde)');
        $this->medicao->setValidationLabel('horario_entrada_real_3', 'Entrada Real (Noite)');
        $this->medicao->setValidationLabel('horario_saida_real_1', 'Saída Real (Manhã)');
        $this->medicao->setValidationLabel('horario_saida_real_2', 'Saída Real (Tarde)');
        $this->medicao->setValidationLabel('horario_saida_real_3', 'Saída Real (Noite)');
        $this->medicao->setValidationLabel('observacoes', 'Observações');
        $this->medicao->setValidationLabel('justificativa', 'Justificativa');

        if ($this->medicao->validate($data) == false) {
            exit(json_encode(['erro' => $this->medicao->errors()]));
        }

        $qb = $this->db
            ->where('data_evento', $data->data_evento)
            ->where('id_usuario', $data->id_usuario)
            ->where('id_escola', $data->id_escola);
        if (strlen($data->horario_entrada_real_1) > 0 or strlen($data->horario_saida_real_1) > 0) {
            $qb->where('(horario_entrada_real_1 IS NULL AND horario_saida_real_1 IS NULL)', null, false);
        }
        if (strlen($data->horario_entrada_real_2) > 0 or strlen($data->horario_saida_real_2) > 0) {
            $qb->where('(horario_entrada_real_2 IS NULL AND horario_saida_real_2 IS NULL)', null, false);
        }
        if (strlen($data->horario_entrada_real_3) > 0 or strlen($data->horario_saida_real_3) > 0) {
            $qb->where('(horario_entrada_real_3 IS NULL AND horario_saida_real_3 IS NULL)', null, false);
        }
        $oldData = $qb
            ->get('ei_usuarios_frequencias')
            ->row();

        if (strlen($data->id) == 0 and !empty($oldData->id)) {
            $data->id = $oldData->id;
        }


        for ($i = 1; $i <= 3; $i++) {
            if (strlen($data->{'horario_entrada_' . $i}) == 0 and isset($oldData->{'horario_entrada_' . $i})) {
                $data->{'horario_entrada_' . $i} = $oldData->{'horario_entrada_' . $i} ?? null;
            }
            if (strlen($data->{'horario_entrada_real_' . $i}) == 0 and isset($oldData->{'horario_entrada_real_' . $i})) {
                $data->{'horario_entrada_real_' . $i} = $oldData->{'horario_entrada_real_' . $i} ?? null;
            }
            if (strlen($data->{'horario_saida_' . $i}) == 0 and isset($oldData->{'horario_saida_' . $i})) {
                $data->{'horario_saida_' . $i} = $oldData->{'horario_saida_' . $i} ?? null;
            }
            if (strlen($data->{'horario_saida_real_' . $i}) == 0 and isset($oldData->{'horario_saida_real_' . $i})) {
                $data->{'horario_saida_real_' . $i} = $oldData->{'horario_saida_real_' . $i} ?? null;
            }
            if (strlen($data->{'horario_entrada_' . $i}) > 0 and strlen($data->{'horario_saida_' . $i}) > 0) {
                $data->periodo_atual = $i;
            }
        }

        $this->medicao->skipValidation();

        if ($data->id) {
            $status = $this->medicao->update($data->id, $data);
        } else {
            $status = $this->medicao->insert($data);
        }
        if ($status == false) {
            exit(json_encode(['erro' => $this->medicao->errors()]));
        }

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    public function ajax_delete()
    {
        $this->load->model('ei_usuario_frequencia_model', 'medicao');

        if ($this->input->post('manter_apontamento')) {
            $this->medicao->allowCallbacks(false);
        }

        $this->medicao->delete($this->input->post('id')) or exit(json_encode(['erro' => $this->medicao->errors()]));

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    public function salvar_status_aprovacao()
    {
        parse_str($this->input->post('busca'), $busca);
        $dataAtual = $this->input->post('data_atual');
        $idCuidador = $this->input->post('id_cuidador');
        $idEscola = $this->input->post('id_escola');
        $status = $this->input->post('status');
        $observacoes = $this->input->post('observacoes');

        if (!empty($busca['depto'])) {
            $this->db->where('e.depto', $busca['depto']);
        }
        if (!empty($busca['diretoria'])) {
            $this->db->where('e.id_diretoria', $busca['diretoria']);
        }
        if (!empty($busca['supervisor'])) {
            $this->db->where('e.id_supervisor', $busca['supervisor']);
        }
        $idMes = $this->getIdMes($busca['mes'], $busca['semestre']);
        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';

        $data = $this->db
            ->select('a.id, c.id AS id_alocado, a.mes_referencia')
            ->select("c2.cargo{$mesCargoFuncao} AS cargo, c2.funcao{$mesCargoFuncao} AS funcao")
            ->select(["NULLIF('{$status}', '') AS status_aprovacao_escola"], false)
            ->select(["NULLIF('{$observacoes}', '') AS observacoes_escola"], false)
            ->join('ei_alocacao_escolas d', 'd.id = c.id_alocacao_escola')
            ->join('ei_alocacao e', 'e.id = d.id_alocacao')
            ->join('ei_alocados_horarios c2', 'c2.id_alocado = c.id')
            ->join('ei_alocados_aprovacoes a', "a.id_alocado = c.id AND a.cargo = c2.cargo{$mesCargoFuncao} AND a.funcao = c2.funcao{$mesCargoFuncao} AND a.mes_referencia = '{$busca['mes']}'", 'left', false)
            ->where('e.id_empresa', $this->session->userdata('empresa'))
            ->where('e.ano', $busca['ano'])
            ->where('e.semestre', $busca['semestre'])
            ->where('c.id_cuidador', $idCuidador)
            ->where('d.id_escola', $idEscola)
            ->group_by(['c.id', 'a.id'])
            ->get('ei_alocados c')
            ->result_array();

        $this->load->model('ei_alocado_aprovacao_model', 'aprovacao');

        $this->db->trans_start();

        foreach ($data as $row) {
            if ($row['id']) {
                $this->aprovacao->update($row['id'], $row);
            } else {
                $row['mes_referencia'] = $busca['mes'];
                $this->aprovacao->insert($row);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() == false) {
            exit(json_encode(['erro' => 'Não foi possível salvar o status da aprovação.']));
        }

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    public function imprimir_arquivo_medicao()
    {
        $depto = $this->input->get('depto');
        $idDiretoria = $this->input->get('diretoria');
        $idSupervisor = $this->input->get('supervisor');
        $mes = $this->input->get('mes');
        $ano = $this->input->get('ano');
        $semestre = $this->input->get('semestre');
        $profissional = $this->input->get('profissional');
        $escola = $this->input->get('escola');

        $aprovacao = $this->db
            ->select('d.arquivo_medicao')
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->join('ei_alocados_aprovacoes d', "d.id_alocado = a.id and d.mes_referencia = '{$mes}'", 'left')
            ->where('c.id_empresa', $this->session->userdata('empresa'))
            ->where('c.ano', $ano)
            ->where('c.semestre', $semestre)
            ->where('a.id_cuidador', $profissional)
            ->where('b.id_escola', $escola)
            ->where('c.depto', $depto)
            ->where('c.id_diretoria', $idDiretoria)
            ->where('c.id_supervisor', $idSupervisor)
            ->where('d.status_aprovacao_escola', 4)
            ->group_by('a.id')
            ->get('ei_alocados a')
            ->row();

        $data = null;
        if ($aprovacao) {
            $this->load->model('ei_alocado_aprovacao_model');

            $path = $this->ei_alocado_aprovacao_model->getUploadConfig()['arquivo_medicao']['upload_path'];
            $data = base_url($path . $aprovacao->arquivo_medicao);
        }

        $this->load->view('ei/pdf_arquivo_medicao', ['arquivoMedicao' => $data]);
    }

}
