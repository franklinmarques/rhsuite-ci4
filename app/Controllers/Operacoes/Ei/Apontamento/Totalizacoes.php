<?php

namespace App\Controllers\Ei\Apontamento;

use App\Controllers\BaseController;

class Totalizacoes extends BaseController
{

    public function fechar_mes()
    {
        $busca = $this->input->post();

        // verifica se o mês alocado existe

        // prepara as variáveis de tempo
        $anoMes = $busca['ano'] . '-' . $busca['mes'];
        $idMes = $this->getIdMes($busca['mes'], $busca['semestre']);
        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';

        $alocacao = $this->db
            ->select("id, dia_fechamento_mes{$idMes} AS dia_fechamento", false)
            ->select("pagamento_fracionado_mes{$idMes} AS pagamento_fracionado", false)
            ->where('id_empresa', $this->session->userdata('empresa'))
            ->where('depto', $busca['depto'])
            ->where('id_diretoria', $busca['diretoria'])
            ->where('id_supervisor', $busca['supervisor'])
            ->where('ano', $busca['ano'])
            ->where('semestre', $busca['semestre'])
            ->get('ei_alocacao')
            ->row();

        if (empty($alocacao)) {
            exit(json_encode(['erro' => 'O semestre alocado não foi encontrado.']));
        }

        $timestamp = strtotime($anoMes . '-01');
        $dataAbertura = date('Y-m-d', $timestamp);
        $dataFechamento = date('Y-m-t', $timestamp);
        $diaPrimeiroDoProximoMes = date('Y-m-d', strtotime('+1 month', $timestamp));

        // retorna dados modificados dos horários do mês selecionado
        $qb = $this->db
            ->select('a.id, a.id_alocado')
            ->select(["IF(a.total_semanas_mes{$idMes} = 0, 0, COUNT(DISTINCT(IF(a.id_cuidador_sub1 IS NOT NULL AND DATE_FORMAT(a.data_substituicao1, '%Y-%m') < '$anoMes', 
								IF(DATE_FORMAT(f.data, '%Y-%m') = '{$anoMes}' AND f.status IN ('FA', 'PV', 'FE', 'EM', 'RE'), f.id, NULL), 
								IF(e.data < IFNULL(a.data_substituicao1, '{$diaPrimeiroDoProximoMes}') AND e.status IN ('FA', 'PV', 'FE', 'EM', 'RE'), e.id, NULL))
						))) AS desconto_mes{$idMes}"], false)
            ->select(["COUNT(DISTINCT(IF(a.id_cuidador_sub1 IS NOT NULL AND DATE_FORMAT(a.data_substituicao1, '%Y-%m') < '$anoMes', 
								IF(DATE_FORMAT(f.data, '%Y-%m') = '{$anoMes}' AND f.status IN ('FA', 'PV', 'FE', 'EM', 'RE') AND (f.desconto_sub1 OR f.desconto_sub2), f.id, NULL), 
								IF(e.data < IFNULL(a.data_substituicao1, '{$diaPrimeiroDoProximoMes}') AND e.status IN ('FA', 'PV', 'FE', 'EM', 'RE') AND (e.desconto_sub1 OR e.desconto_sub2), e.id, NULL))
						)) AS endosso_mes{$idMes}"], false)
            ->select("NULL AS total_mes{$idMes}", false)
            ->select("NULL AS total_endossado_mes{$idMes}", false)
            ->select(["(CASE MONTH(a.data_substituicao1) WHEN '{$busca['mes']}' THEN COUNT(DISTINCT IF(f.status IN ('FA', 'PV', 'FE', 'EM', 'RE'), f.id, NULL)) ELSE a.desconto_sub1 END) AS desconto_sub1"], false)
            ->select(["(CASE MONTH(a.data_substituicao1) WHEN '{$busca['mes']}' THEN COUNT(DISTINCT IF(f.status IN ('FA', 'PV', 'FE', 'EM', 'RE') AND (f.desconto_sub1 OR f.desconto_sub2), f.id, NULL)) ELSE a.endosso_sub1 END) AS endosso_sub1"], false)
            ->select("(CASE MONTH(a.data_substituicao1) WHEN '{$busca['mes']}' THEN NULL ELSE a.total_sub1 END) AS total_sub1", false)
            ->select("(CASE MONTH(a.data_substituicao1) WHEN '{$busca['mes']}' THEN NULL ELSE a.total_endossado_sub1 END) AS total_endossado_sub1", false)
            ->select(["(CASE MONTH(a.data_substituicao2) WHEN '{$busca['mes']}' THEN COUNT(DISTINCT IF(g.status IN ('FA', 'PV', 'FE', 'EM', 'RE'), g.id, NULL)) ELSE a.desconto_sub2 END) AS desconto_sub2"], false)
            ->select(["(CASE MONTH(a.data_substituicao2) WHEN '{$busca['mes']}' THEN COUNT(DISTINCT IF(g.status IN ('FA', 'PV', 'FE', 'EM', 'RE') AND (g.desconto_sub1 OR g.desconto_sub2), g.id, NULL)) ELSE a.endosso_sub2 END) AS endosso_sub2"], false)
            ->select("(CASE MONTH(a.data_substituicao2) WHEN '{$busca['mes']}' THEN NULL ELSE a.total_sub2 END) AS total_sub2", false)
            ->select("(CASE MONTH(a.data_substituicao2) WHEN '{$busca['mes']}' THEN NULL ELSE a.total_endossado_sub2 END) AS total_endossado_sub2", false)
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->join('ei_apontamento e', "e.id_alocado = b.id AND (e.periodo = a.periodo OR e.periodo IS NULL) AND (e.data BETWEEN '{$dataAbertura}' AND '{$dataFechamento}') AND DATE_FORMAT(e.data, '%w') = a.dia_semana AND (e.id_usuario IS NULL OR e.id_usuario = b.id_cuidador)", 'left')
            ->join('ei_apontamento f', "f.id_alocado = b.id AND (f.periodo = a.periodo OR f.periodo IS NULL) AND (f.data BETWEEN '{$dataAbertura}' AND '{$dataFechamento}') AND DATE_FORMAT(f.data, '%w') = a.dia_semana AND (f.id_usuario IS NULL OR f.id_usuario = a.id_cuidador_sub1)", 'left')
            ->join('ei_apontamento g', "g.id_alocado = b.id AND (g.periodo = a.periodo OR g.periodo IS NULL) AND (g.data BETWEEN '{$dataAbertura}' AND '{$dataFechamento}') AND DATE_FORMAT(g.data, '%w') = a.dia_semana AND (g.id_usuario IS NULL OR g.id_usuario = a.id_cuidador_sub2)", 'left')
            ->where('d.id', $alocacao->id);
        if (!empty($busca['id_alocado'])) {
            $qb->where('b.id', $busca['id_alocado']);
        }
        if (!empty($busca['periodo'])) {
            $qb->where('a.periodo', $busca['periodo']);
        }
        if (!empty($busca['cargo'])) {
            $qb->where('a.cargo' . $mesCargoFuncao, $busca['cargo']);
        }
        if (!empty($busca['funcao'])) {
            $qb->where('a.funcao' . $mesCargoFuncao, $busca['funcao']);
        }
        $horariosDoMesAFechar = $qb
            ->group_by('a.id')
            ->get('ei_alocados_horarios a')
            ->result();

        // prepara a transação de dados
        $this->db->trans_start();

        // busca as totalizacoes atuais
        $totalizacoes = $this->db
            ->where('id_alocado', $busca['id_alocado'])
            ->where('periodo', $busca['periodo'])
            ->where('cargo' . $mesCargoFuncao, $busca['cargo'])
            ->where('funcao' . $mesCargoFuncao, $busca['funcao'])
            ->get('ei_alocados_totalizacao')
            ->result();

        // anula os valores as totalizacoes correspondentes
        foreach ($totalizacoes as $totalizacao) {
            $totalDias = array_filter([
                $idMes === 1 ? null : $totalizacao->total_dias_mes1,
                $idMes === 2 ? null : $totalizacao->total_dias_mes2,
                $idMes === 3 ? null : $totalizacao->total_dias_mes3,
                $idMes === 4 ? null : $totalizacao->total_dias_mes4,
                $idMes === 5 ? null : $totalizacao->total_dias_mes5,
                $idMes === 6 ? null : $totalizacao->total_dias_mes6,
                $idMes === 7 ? null : $totalizacao->total_dias_mes7,
            ]);

            if (count($totalDias) > 0) {
                $this->db
                    ->set('total_dias_mes' . $idMes, null)
                    ->set('dias_descontados_mes' . $idMes, null)
                    ->set('total_horas_mes' . $idMes, null)
                    ->set('horas_descontadas_mes' . $idMes, null)
                    ->set('total_horas_faturadas_mes' . $idMes, null)
                    ->set('valor_pagamento_mes' . $idMes, null)
                    ->set('valor_total_mes' . $idMes, null)
                    ->where('id', $totalizacao->id)
                    ->update('ei_alocados_totalizacao');
            } else {
                $this->db->delete('ei_alocados_totalizacao', ['id' => $totalizacao->id]);
            }
        }

        $alocado = $this->db
            ->select('id_cuidador')
            ->where('id', $busca['id_alocado'])
            ->get('ei_alocados')
            ->row();

        $periodo = $busca['periodo'] ?? null;
        $cargo = $busca['cargo'] ?? null;
        $funcao = $busca['funcao'] ?? null;

        if ($alocado) {
            if ($alocacao->pagamento_fracionado and $alocacao->dia_fechamento) {
                $timestampAnterior = strtotime($anoMes . '-' . $alocacao->dia_fechamento . ' -1 month +1 day');

                $dataAberturaAnterior = date('Y-m-d', $timestampAnterior);
                $dataFechamentoAnterior = date('Y-m-t', $timestampAnterior);

                $dataFechamentoAtual = date('Y-m-d', strtotime($anoMes . '-' . $alocacao->dia_fechamento));
                $dataAberturaPosterior = date('Y-m-d', strtotime($dataFechamentoAtual . ' +1 day'));

                $apontamentoAnterior = $this->db
                    ->select("SEC_TO_TIME(IFNULL(SUM(TIME_TO_SEC(IF(b.status = 'FA', c.total_horas_mes{$idMes}, NULL)) * (-1)), 0)) AS falta")
                    ->select("SEC_TO_TIME(IFNULL(SUM(TIME_TO_SEC(IF(b.status IN ('AT', 'SA'), b.desconto, NULL))), 0)) AS desconto")
                    ->join('ei_apontamento b', "b.id_alocado = a.id AND b.status IN ('FA', 'AT', 'SA') AND (data BETWEEN '{$dataAberturaAnterior}' AND '{$dataFechamentoAnterior}')", 'left')
                    ->join('ei_alocados_horarios c', "c.id_alocado = b.id_alocado AND c.dia_semana = DATE_FORMAT(b.data, '%w') AND 
                                                      (c.periodo = '{$periodo}' OR CHAR_LENGTH('{$periodo}') = 0) AND 
                                                      (c.cargo{$mesCargoFuncao} = '{$cargo}' OR CHAR_LENGTH('{$cargo}') = 0) AND 
                                                      (c.funcao{$mesCargoFuncao} = '{$funcao}' OR CHAR_LENGTH('{$funcao}') = 0)", 'left')
                    ->where('a.id', $busca['id_alocado'])
                    ->group_start()
                    ->where('b.periodo', $periodo)
                    ->or_where('b.periodo', null)
                    ->group_end()
                    ->group_by('a.id')
                    ->get('ei_alocados a')
                    ->row();
            } else {
                $dataFechamentoAtual = $dataFechamento;
                $dataAberturaPosterior = date('Y-m-d', strtotime($dataFechamentoAtual . ' +1 day'));
            }

            $apontamentoAtual = $this->db
                ->select("SEC_TO_TIME(IFNULL(SUM(TIME_TO_SEC(IF(b.status = 'FA', c.total_horas_mes{$idMes}, NULL)) * (-1)), 0)) AS falta")
                ->select("SEC_TO_TIME(IFNULL(SUM(TIME_TO_SEC(IF(b.status IN ('AT', 'SA'), b.desconto, NULL))), 0)) AS desconto")
                ->join('ei_apontamento b', "b.id_alocado = a.id AND b.status IN ('FA', 'AT', 'SA') AND (data BETWEEN '{$dataAbertura}' AND '{$dataFechamentoAtual}') AND (b.periodo = '{$periodo}' OR b.periodo IS NULL)", 'left')
                ->join('ei_alocados_horarios c', "c.id_alocado = b.id_alocado AND c.dia_semana = DATE_FORMAT(b.data, '%w') AND 
                                                  (c.periodo = '{$periodo}' OR CHAR_LENGTH('{$periodo}') = 0) AND 
                                                  (c.cargo{$mesCargoFuncao} = '{$cargo}' OR CHAR_LENGTH('{$cargo}') = 0) AND 
                                                  (c.funcao{$mesCargoFuncao} = '{$funcao}' OR CHAR_LENGTH('{$funcao}') = 0)", 'left')
                ->where('a.id', $busca['id_alocado'])
                ->group_by('a.id')
                ->get('ei_alocados a')
                ->row();

            $apontamentoPosterior = $this->db
                ->select("SEC_TO_TIME(IFNULL(SUM(TIME_TO_SEC(IF(b.status = 'FA', c.total_horas_mes{$idMes}, NULL)) * (-1)), 0)) AS falta")
                ->select("SEC_TO_TIME(IFNULL(SUM(TIME_TO_SEC(IF(b.status IN ('AT', 'SA'), b.desconto, NULL))), 0)) AS desconto")
                ->join('ei_apontamento b', "b.id_alocado = a.id AND b.status IN ('FA', 'AT', 'SA') AND (data BETWEEN '{$dataAberturaPosterior}' AND '{$dataFechamento}') AND (b.periodo = '{$periodo}' OR b.periodo IS NULL)", 'left')
                ->join('ei_alocados_horarios c', "c.id_alocado = b.id_alocado AND c.dia_semana = DATE_FORMAT(b.data, '%w') AND 
                                                  (c.periodo = '{$periodo}' OR CHAR_LENGTH('{$periodo}') = 0) AND 
                                                  (c.cargo{$mesCargoFuncao} = '{$cargo}' OR CHAR_LENGTH('{$cargo}') = 0) AND 
                                                  (c.funcao{$mesCargoFuncao} = '{$funcao}' OR CHAR_LENGTH('{$funcao}') = 0)", 'left')
                ->where('a.id', $busca['id_alocado'])
                ->group_by('a.id')
                ->get('ei_alocados a')
                ->row();

            $qb = $this->db
                ->set('falta_anterior_mes' . $idMes, $apontamentoAnterior->falta ?? null)
                ->set('desconto_anterior_mes' . $idMes, $apontamentoAnterior->desconto ?? null)
                ->set('falta_atual_mes' . $idMes, $apontamentoAtual->falta)
                ->set('desconto_atual_mes' . $idMes, $apontamentoAtual->desconto);
            if ($idMes == '7') {
                $qb->set('falta_posterior_mes7', $apontamentoPosterior->falta ?? null)
                    ->set('desconto_posterior_mes7', $apontamentoPosterior->desconto);
            } else {
                $qb->set('falta_anterior_mes' . ($idMes + 1), $apontamentoPosterior->falta ?? null)
                    ->set('desconto_anterior_mes' . ($idMes + 1), $apontamentoPosterior->desconto);
            }
            $qb->where('id_alocacao', $alocacao->id)
                ->where('id_cuidador', $alocado->id_cuidador)
                ->update('ei_pagamento_prestador');
        }

        // salva as modificacoes nos horarios
        foreach ($horariosDoMesAFechar as $data) {
            $this->db->update('ei_alocados_horarios', $data, ['id' => $data->id]);
        }

        // competa a transacao
        $this->db->trans_complete();

        $status = $this->db->trans_status();

        if ($status == false) {
            exit(json_encode(['erro' => 'Erro ao fechar o mês.']));
        }

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function totalizar_mes()
    {
        // prepara as variaveis locais
        $idAlocado = $this->input->post('id_alocado');
        $periodo = $this->input->post('periodo');
        $cargo = $this->input->post('cargo');
        $funcao = $this->input->post('funcao');
        $mes = $this->input->post('mes');
        $ano = $this->input->post('ano');
        $anoMes = $ano . '.' . $mes;
        $semestre = $this->input->post('semestre');
        $idMes = (int)$mes - ((int)$semestre > 1 ? 6 : 0);
        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';

        // verifica se o mes alocado existe
        $alocacao = $this->db
            ->select('id')
            ->select("dia_fechamento_mes{$idMes} AS dia_fechamento", false)
            ->select("pagamento_fracionado_mes{$idMes} AS pagamento_fracionado", false)
            ->where('id_empresa', $this->session->userdata('empresa'))
            ->where('depto', $this->input->post('depto'))
            ->where('id_diretoria', $this->input->post('diretoria'))
            ->where('id_supervisor', $this->input->post('supervisor'))
            ->where('ano', $ano)
            ->where('semestre', $semestre)
            ->get('ei_alocacao')
            ->row();

        if (empty($alocacao)) {
            exit(json_encode(['erro' => 'O semestre alocado não foi encontrado.']));
        }

        // recupera dados modificados dos horarios
        $horarios = $this->db
            ->select('a.id')
            ->select("IF(a.total_semanas_mes{$idMes} = 0, '0:00', SEC_TO_TIME(TIME_TO_SEC(a.total_horas_mes{$idMes}) * (a.total_semanas_mes{$idMes} - a.desconto_mes{$idMes}))) AS total_mes{$idMes}", false)
            ->select("IF(a.total_semanas_mes{$idMes} = 0, '0:00', SEC_TO_TIME(TIME_TO_SEC(a.total_horas_mes{$idMes}) * (a.total_semanas_mes{$idMes} - a.desconto_mes{$idMes} + a.endosso_mes{$idMes}))) AS total_endossado_mes{$idMes}", false)
            ->select("IF(a.total_semanas_mes{$idMes} = 0, '0:00', IF(MONTH(a.data_substituicao1) = '{$mes}', SEC_TO_TIME(TIME_TO_SEC(a.total_horas_mes{$idMes}) * (a.total_semanas_sub1 - a.desconto_sub1)), a.total_sub1)) AS total_sub1", false)
            ->select("IF(a.total_semanas_mes{$idMes} = 0, '0:00', IF(MONTH(a.data_substituicao1) = '{$mes}', SEC_TO_TIME(TIME_TO_SEC(a.total_horas_mes{$idMes}) * (a.total_semanas_sub1 - a.desconto_sub1 + a.endosso_sub1)), a.total_endossado_sub1)) AS total_endossado_sub1", false)
            ->select("IF(a.total_semanas_mes{$idMes} = 0, '0:00', IF(MONTH(a.data_substituicao2) = '{$mes}', SEC_TO_TIME(TIME_TO_SEC(a.total_horas_mes{$idMes}) * (a.total_semanas_sub2 - a.desconto_sub2)), a.total_sub2)) AS total_sub2", false)
            ->select("IF(a.total_semanas_mes{$idMes} = 0, '0:00', IF(MONTH(a.data_substituicao2) = '{$mes}', SEC_TO_TIME(TIME_TO_SEC(a.total_horas_mes{$idMes}) * (a.total_semanas_sub2 - a.desconto_sub2 + a.endosso_sub1)), a.total_endossado_sub2)) AS total_endossado_sub2", false)
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->where(($idAlocado and $periodo and $cargo and $funcao) ? ['b.id' => $idAlocado, 'a.periodo' => $periodo, 'a.cargo' . $mesCargoFuncao => $cargo, 'a.funcao' . $mesCargoFuncao => $funcao] : ['d.id' => $alocacao->id])
            ->group_by('a.id')
            ->get('ei_alocados_horarios a')
            ->result_array();

        if (empty($horarios)) {
            exit(json_encode(['erro' => 'Nenhum horário de cuidador encontrado.']));
        }

        if ($alocacao->pagamento_fracionado and $alocacao->dia_fechamento) {
            $timestamp = strtotime($ano . '-' . $mes . '-' . $alocacao->dia_fechamento);
            $dataAbertura = date('Y-m-d', strtotime('+1 day', strtotime('-1 month', $timestamp)));
            $dataFechamento = date('Y-m-d', $timestamp);
        } else {
            $timestamp = strtotime($ano . '-' . $mes . '-01');
            $dataAbertura = date('Y-m-d', $timestamp);
            $dataFechamento = date('Y-m-t', $timestamp);
        }

        // prepara a transacao de dados
        $this->db->trans_start();

        // atualiza os horarios totalizados
        $this->db->update_batch('ei_alocados_horarios', $horarios, 'id');

        $diasDescontados = 0;
        $horasDescontadas = 0;
        $totalDescontos = 0;
        if ($alocacao->pagamento_fracionado) {
            $alocado = $this->db
                ->select('id_cuidador')
                ->where('id', $idAlocado)
                ->get('ei_alocados')
                ->row();

            if (!empty($alocado)) {
                $this->db->where('id_cuidador', $alocado->id_cuidador);
            }
            $pagamentoPrestador = $this->db
                ->select('TIME_TO_SEC(IFNULL(falta_anterior_mes' . $idMes . ', 0)) + TIME_TO_SEC(IFNULL(falta_atual_mes' . $idMes . ', 0)) AS dias_descontados', false)
                ->select('TIME_TO_SEC(IFNULL(desconto_anterior_mes' . $idMes . ', 0)) + TIME_TO_SEC(IFNULL(desconto_atual_mes' . $idMes . ', 0)) AS horas_descontadas', false)
                ->select('TIME_TO_SEC(IFNULL(' . ($idMes == 7 ? 'desconto_posterior_mes7' : 'desconto_anterior_mes' . ($idMes + 1)) . ', 0)) + TIME_TO_SEC(IFNULL(desconto_atual_mes' . $idMes . ', 0)) AS total_descontos', false)
                ->where('id_alocacao', $alocacao->id)
                ->get('ei_pagamento_prestador')
                ->row();

            $diasDescontados = $pagamentoPrestador->dias_descontados ?? 0;
            $horasDescontadas = $pagamentoPrestador->horas_descontadas ?? 0;
            $totalDescontos = $pagamentoPrestador->total_descontos ?? 0;
        }

        // recupera os dados modificados das totalizacoes do cuidador principal
        $totalizacoesPrincipais = $this->db
            ->select('f.id, a.id_alocado, a.periodo, c.id AS id_cuidador, c.nome AS cuidador')
            ->select('a.cargo, a.cargo_mes2, a.cargo_mes3, a.cargo_mes4, a.cargo_mes5, a.cargo_mes6, a.cargo_mes7')
            ->select('a.funcao, a.funcao_mes2, a.funcao_mes3, a.funcao_mes4, a.funcao_mes5, a.funcao_mes6, a.funcao_mes7')
            ->select("(CASE c.id WHEN a.id_cuidador_sub2 THEN 2 WHEN a.id_cuidador_sub1 THEN 1 END) AS substituicao_semestral", false)
            ->select("NULL AS substituicao_eventual, SEC_TO_TIME('{$totalDescontos}') AS total_descontos_mes{$idMes}", false)
            ->select("SUM(a.total_semanas_mes{$idMes} - a.desconto_mes{$idMes}) AS total_dias_mes{$idMes}", false)
            ->select("IF('{$alocacao->pagamento_fracionado}' = '1' AND '{$diasDescontados}' != '0', SEC_TO_TIME({$diasDescontados}), (
					   SELECT SEC_TO_TIME(TIME_TO_SEC(a.total_horas_mes{$idMes}) * (-1) * COUNT(DISTINCT(CASE x.status WHEN 'FA' THEN x.id END))) 
					   FROM ei_apontamento x
         			   WHERE x.id_alocado = b.id 
         			   		 AND (x.periodo = a.periodo OR x.periodo IS NULL)
         			   		 AND (x.data BETWEEN '{$dataAbertura}' AND '{$dataFechamento}')
         					 AND (x.id_usuario = b.id_cuidador OR x.id_usuario IS NULL)
         			  )) AS dias_descontados_mes{$idMes}", false)
            ->select("SEC_TO_TIME(SUM(TIME_TO_SEC(a.total_mes{$idMes})) + IFNULL((
						SELECT SUM(CASE WHEN x.status IN('FA', 'AT', 'SA') THEN TIME_TO_SEC(x.desconto) ELSE 0 END) 
						FROM ei_apontamento x
         				WHERE x.id_alocado = b.id 
         					  AND (x.periodo = a.periodo OR x.periodo IS NULL)
         					  AND (x.data BETWEEN '{$dataAbertura}' AND '{$dataFechamento}')
         					  AND (x.id_usuario = b.id_cuidador OR x.id_usuario IS NULL)
         			 ), 0)) AS total_horas_mes{$idMes}", false)
            ->select("IF('{$alocacao->pagamento_fracionado}' = '1' AND '{$horasDescontadas}' != '0', 
                       SEC_TO_TIME({$horasDescontadas} - (SELECT SUM(CASE WHEN x.status IN ('EE', 'HE', 'SL') THEN TIME_TO_SEC(x.desconto) END)
					   FROM ei_apontamento x
         			   WHERE x.id_alocado = b.id 
         			   		 AND (x.periodo = a.periodo OR x.periodo IS NULL)
         			   		 AND (x.data BETWEEN '{$dataAbertura}' AND '{$dataFechamento}')
         					 AND (x.id_usuario = b.id_cuidador OR x.id_usuario IS NULL)
         			  )), 
                       (SELECT SEC_TO_TIME(SUM(CASE WHEN x.status IN('AT', 'SA') THEN TIME_TO_SEC(x.desconto) END)) 
					   FROM ei_apontamento x
         			   WHERE x.id_alocado = b.id 
         			   		 AND (x.periodo = a.periodo OR x.periodo IS NULL)
         			   		 AND (x.data BETWEEN '{$dataAbertura}' AND '{$dataFechamento}')
         					 AND (x.id_usuario = b.id_cuidador OR x.id_usuario IS NULL)
         			  )) AS horas_descontadas_mes{$idMes}", false)
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('usuarios c', "c.id = (CASE WHEN a.id_cuidador_sub2 IS NOT NULL AND '{$anoMes}' > DATE_FORMAT(a.data_substituicao2, '%Y.%m') THEN a.id_cuidador_sub2 WHEN a.id_cuidador_sub1 IS NOT NULL AND '{$anoMes}' > DATE_FORMAT(a.data_substituicao1, '%Y.%m') THEN a.id_cuidador_sub1 ELSE b.id_cuidador END)")
            ->join('ei_alocacao_escolas d', 'd.id = b.id_alocacao_escola')
            ->join('ei_alocacao e', 'e.id = d.id_alocacao')
            ->join('ei_alocados_totalizacao f', "f.id_alocado = b.id AND f.periodo = a.periodo AND f.id_cuidador = c.id AND f.cargo{$mesCargoFuncao} = a.cargo{$mesCargoFuncao} AND f.funcao{$mesCargoFuncao} = a.funcao{$mesCargoFuncao}", 'left')
            ->where(($idAlocado and $periodo and $cargo and $funcao) ? ['b.id' => $idAlocado, 'a.periodo' => $periodo, 'a.cargo' . $mesCargoFuncao => $cargo, 'a.funcao' . $mesCargoFuncao => $funcao] : ['e.id' => $alocacao->id])
            ->group_by(['b.id', 'a.periodo', 'c.id'])
            ->get('ei_alocados_horarios a')
            ->result();

        // recupera os dados modificados das totalizacoes do cuidador substituto
        $totalizacoesSubstitutasSemestral = $this->db
            ->select('f.id, a.id_alocado, a.periodo, c.id AS id_cuidador, c.nome AS cuidador')
            ->select('f.cargo, f.cargo_mes2, f.cargo_mes3, f.cargo_mes4, f.cargo_mes5, f.cargo_mes6, f.cargo_mes7')
            ->select('f.funcao, f.funcao_mes2, f.funcao_mes3, f.funcao_mes4, f.funcao_mes5, f.funcao_mes6, f.funcao_mes7')
            ->select("IF(c.id = a.id_cuidador_sub2, 2, 1) AS substituicao_semestral", false)
            ->select("NULL AS substituicao_eventual, NULL AS total_descontos_sub1", false)
            ->select("SUM(IF(c.id = a.id_cuidador_sub2, a.total_semanas_sub2 - a.desconto_sub2, a.total_semanas_sub1 - a.desconto_sub1)) AS total_dias_mes{$idMes}", false)
            ->select("(
					   SELECT SEC_TO_TIME(SUM(CASE x.status WHEN 'FA' THEN IFNULL(TIME_TO_SEC(x.desconto), 0) END)) 
					   FROM ei_apontamento x
         			   WHERE x.id_alocado = b.id 
         			   		 AND x.periodo = a.periodo 
         			   		 AND (x.data BETWEEN '{$dataAbertura}' AND '{$dataFechamento}')
         			   		 AND (x.id_usuario = a.id_cuidador_sub1 OR x.id_usuario IS NULL)
         			  ) AS dias_descontados_mes{$idMes}", false)
            ->select("SEC_TO_TIME(SUM(TIME_TO_SEC(a.total_sub1))) AS total_horas_mes{$idMes}", false)
            ->select("(SELECT SEC_TO_TIME(SUM(CASE WHEN x.status IN('AT', 'SA') THEN TIME_TO_SEC(x.desconto) END)) 
					   FROM ei_apontamento x
         			   WHERE x.id_alocado = b.id 
         			   		 AND x.periodo = a.periodo 
         			   		 AND (x.data BETWEEN '{$dataAbertura}' AND '{$dataFechamento}')
         			   		 AND (x.id_usuario = a.id_cuidador_sub1 OR x.id_usuario IS NULL)
         			  ) AS horas_descontadas_mes{$idMes}", false)
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('usuarios c', "c.id = IFNULL(a.id_cuidador_sub2, a.id_cuidador_sub1) AND '{$mes}' = MONTH(IF(c.id = a.id_cuidador_sub2, a.data_substituicao2, a.data_substituicao1))")
            ->join('ei_alocacao_escolas d', 'd.id = b.id_alocacao_escola')
            ->join('ei_alocacao e', 'e.id = d.id_alocacao')
            ->join('ei_alocados_totalizacao f', "f.id_alocado = b.id AND f.periodo = a.periodo AND f.id_cuidador = c.id AND f.cargo{$mesCargoFuncao} = a.cargo{$mesCargoFuncao} AND f.funcao{$mesCargoFuncao} = a.funcao{$mesCargoFuncao}", 'left')
            ->where(($idAlocado and $periodo and $cargo and $funcao) ? ['b.id' => $idAlocado, 'a.periodo' => $periodo, 'a.cargo' . $mesCargoFuncao => $cargo, 'a.funcao' . $mesCargoFuncao => $funcao] : ['e.id' => $alocacao->id])
            ->where('c.id IS NOT NULL')
            ->group_by(['b.id', 'a.periodo', 'c.id'])
            ->get('ei_alocados_horarios a')
            ->result();

        // recupera os dados modificados das totalizacoes dos cuidadores substitutos de eventos
        $totalizacoesSubstitutasEventuais = $this->db
            ->select('f.id, a.id_alocado, a.periodo, d2.id AS id_cuidador, d2.nome AS cuidador')
            ->select('f.cargo, f.cargo_mes2, f.cargo_mes3, f.cargo_mes4, f.cargo_mes5, f.cargo_mes6, f.cargo_mes7')
            ->select('f.funcao, f.funcao_mes2, f.funcao_mes3, f.funcao_mes4, f.funcao_mes5, f.funcao_mes6, f.funcao_mes7')
            ->select('NULL AS substituicao_semestral', false)
            ->select(["IF(COUNT(a.id_alocado_sub1) > 0, 1, 0) + IF(COUNT(a.id_alocado_sub2) > 0, 2, 0) AS substituicao_eventual"], false)
            ->select(["NULL AS total_descontos_sub1"], false)
            ->select("COUNT(DISTINCT DAY(a.data)) AS total_dias_mes{$idMes}", false)
            ->select(["SEC_TO_TIME(SUM(CASE WHEN a.status IN('FA', 'AT', 'SA') THEN TIME_TO_SEC(CASE WHEN d2.id = a.id_alocado_sub1 THEN a.desconto_sub1 WHEN d2.id = a.id_alocado_sub2 THEN a.desconto_sub2 END) END)) AS total_horas_mes{$idMes}"], false)
            ->select("SEC_TO_TIME(0) AS horas_descontadas_mes{$idMes}", false)
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id  = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id  = c.id_alocacao')
            ->join('usuarios d2', 'd2.id  = a.id_alocado_sub1 OR d2.id  = a.id_alocado_sub2', 'left')
            ->join('ei_alocados_totalizacao f', "f.id_alocado = b.id AND f.periodo = a.periodo AND f.id_cuidador = d2.id AND f.cargo{$mesCargoFuncao} = '{$cargo}' AND f.cargo{$mesCargoFuncao} = '{$funcao}'", 'left')
            ->where(($idAlocado and $periodo) ? ['b.id' => $idAlocado, 'a.periodo' => $periodo] : ['d.id' => $alocacao->id])
            ->where("(a.data BETWEEN '{$dataAbertura}' AND '{$dataFechamento}')")
            ->where('d2.id IS NOT NULL')
            ->group_by(['b.id', 'a.periodo', 'd2.id'])
            ->get('ei_apontamento a')
            ->result();

        // agrupa as totalizacoes recuperadas
        $totalizacoes = [];
        foreach ($totalizacoesPrincipais as $totalizacaoPrincipal) {
            $totalizacoes[] = $totalizacaoPrincipal;
        }
        foreach ($totalizacoesSubstitutasSemestral as $totalizacaoSubstitutaSemestral) {
            $totalizacoes[] = $totalizacaoSubstitutaSemestral;
        }
        foreach ($totalizacoesSubstitutasEventuais as $totalizacaoSubstitutaEventual) {
            $totalizacoes[] = $totalizacaoSubstitutaEventual;
        }
        // salva os dados das totalizacoes
        foreach ($totalizacoes as &$totalizacao) {
            $id = $totalizacao->id;
            unset($totalizacao->id);

            if ($id) {
                $this->db->update('ei_alocados_totalizacao', $totalizacao, ['id' => $id]);
            } else {
                $this->db->insert('ei_alocados_totalizacao', $totalizacao);
            }
        }

        // completa a transacao de dados
        $this->db->trans_complete();

        $status = $this->db->trans_status();

        if ($status == false) {
            exit(json_encode(['erro' => 'Erro ao totalizar o mês.']));
        }

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function salvar_mes()
    {
        $idAlocado = $this->input->post('id_alocado');
        $periodo = $this->input->post('periodo');
        $cargo = $this->input->post('cargo');
        $funcao = $this->input->post('funcao');
        $mes = $this->input->post('mes');
        $ano = $this->input->post('ano');
        $semestre = $this->input->post('semestre');
        $idMes = $this->getIdMes($mes, $semestre);
        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';

        $alocacao = $this->db
            ->where('id_empresa', $this->session->userdata('empresa'))
            ->where('depto', $this->input->post('depto'))
            ->where('id_diretoria', $this->input->post('diretoria'))
            ->where('id_supervisor', $this->input->post('supervisor'))
            ->where('ano', $ano)
            ->where('semestre', $semestre)
            ->get('ei_alocacao')
            ->row();

        $alocacaoEscola = $this->db
            ->select('b.id_escola')
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->where('a.id', $idAlocado)
            ->get('ei_alocados a')
            ->row();

        $idEscola = $alocacaoEscola->id_escola ?? null;

        $qb = $this->db
            ->select('a.id, e.id AS id_totalizacao, d.id_alocado, d.periodo')
            ->select("b.id_escola, d.cargo{$mesCargoFuncao} AS cargo, d.funcao{$mesCargoFuncao} AS funcao")
            ->select(["(SUM(d.total_semanas_mes{$idMes} - IFNULL(d.desconto_mes{$idMes}, 0) + IFNULL(d.endosso_mes{$idMes}, 0) + IF(MONTH(d.data_substituicao1) = '{$mes}', IFNULL(d.total_semanas_sub1, 0) - IFNULL(d.desconto_sub1, 0) + IFNULL(d.endosso_sub1, 0), 0)) / GREATEST(COUNT(DISTINCT d.id), 1) ) / GREATEST(COUNT(DISTINCT g.id), 1) AS total_dias"], false)
            ->select("(SELECT COUNT(DISTINCT f2.id) FROM ei_apontamento f2 WHERE f2.id_alocado = a.id AND f2.periodo = d.periodo AND f2.status IN ('FA', 'PV', 'FE', 'EM', 'RE', 'EE', 'HE', 'SL') AND MONTH(f2.data) = '{$mes}' AND YEAR(f2.data) = c.ano AND DATE_FORMAT(f2.data, '%w') IN (0,6) AND DATE_FORMAT(f2.data, '%w') NOT IN (SELECT f3.dia_semana FROM ei_alocados_horarios f3 WHERE f3.id_alocado = f2.id_alocado AND f3.periodo = f2.periodo)) AS total_eventos_fim_semana", false)
            ->select("(SELECT SUM(TIME_TO_SEC(f3.desconto)) FROM ei_apontamento f3 WHERE f3.id_alocado = a.id AND f3.periodo = d.periodo AND f3.status IN ('FA', 'AT', 'SA', 'EE', 'HE', 'SL', 'PV') AND MONTH(f3.data) = '{$mes}' AND YEAR(f3.data) = c.ano) AS total_descontos", false)
            ->select(["(SUM(TIME_TO_SEC(IFNULL(d.total_endossado_mes{$idMes}, d.total_mes{$idMes})) + IF(MONTH(d.data_substituicao1) = {$mes}, TIME_TO_SEC(IFNULL(d.total_endossado_sub1, IFNULL(d.total_sub1, 0))), 0)) / GREATEST(COUNT(DISTINCT g.id), 1)) AS total_horas"], false)
            ->select("h.preservar_edicao_mes{$idMes} AS preservar_faturamento", false)
            ->select("i.preservar_edicao_mes{$idMes} AS preservar_pagamento_prestador", false)
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->join('ei_alocados_horarios d', 'd.id_alocado = a.id', 'left')
            ->join('ei_alocados_totalizacao e', "e.id_alocado = a.id AND d.periodo = e.periodo AND d.cargo{$mesCargoFuncao} = e.cargo{$mesCargoFuncao} AND d.funcao{$mesCargoFuncao} = e.funcao{$mesCargoFuncao} AND e.id_cuidador IN (a.id_cuidador, d.id_cuidador_sub1) AND e.substituicao_eventual IS NULL", 'left', false)
            ->join('ei_matriculados_turmas f', 'f.id_alocado_horario = d.id', 'left')
            ->join('ei_matriculados g', 'g.id = f.id_matriculado AND g.id_alocacao_escola = b.id', 'left')
            ->join('ei_faturamento h', "h.id_alocacao = c.id AND h.id_escola = b.id_escola AND h.cargo = d.cargo{$mesCargoFuncao} AND h.funcao = d.funcao{$mesCargoFuncao}", 'left', false)
            ->join('ei_pagamento_prestador i', "i.id_alocacao = c.id AND i.id_cuidador = a.id_cuidador AND i.cargo{$mesCargoFuncao} = d.cargo{$mesCargoFuncao} AND i.funcao{$mesCargoFuncao} = d.funcao{$mesCargoFuncao}", 'left', false)
            ->join('usuarios k', 'k.id = d.id_cuidador_sub1', 'left')
            ->join('usuarios l', 'l.id = d.id_cuidador_sub2', 'left')
            ->where('c.id', $alocacao->id);
        if (!empty($idEscola)) {
            $qb->where('b.id_escola', $idEscola);
        }
        if (!empty($cargo)) {
            $qb->where('d.cargo' . $mesCargoFuncao, $cargo);
        }
        if (!empty($funcao)) {
            $qb->where('d.funcao' . $mesCargoFuncao, $funcao);
        }
        $rows = $qb
            ->group_by(['a.id', 'b.id_escola', 'd.cargo' . $mesCargoFuncao, 'd.funcao' . $mesCargoFuncao, 'd.periodo', 'd.dia_semana'])
            ->get_compiled_select('ei_alocados a');

        $rows = $this->db
            ->query("SELECT s.id_totalizacao, s.id_alocado, s.periodo, s.cargo, s.funcao,
                            SUM(s.total_eventos_fim_semana) AS total_eventos_fim_semana,
                            SUM(s.total_dias) AS total_dias,
                            TIME_FORMAT(SEC_TO_TIME(SUM(s.total_descontos)), '%H:%i') AS total_descontos,
                            TIME_FORMAT(SEC_TO_TIME(SUM(s.total_horas)), '%H:%i') AS total_horas,
                            s.preservar_faturamento,
                            s.preservar_pagamento_prestador
                     FROM ({$rows}) s
                     GROUP BY s.id, s.id_escola, s.cargo, s.funcao, s.periodo")
            ->result();

        $dataInicioFechamento = $ano . '-' . $mes . '-01';
        $dataTerminoFechamento = date('Y-m-t', strtotime($dataInicioFechamento));
        if ($alocacao->{'dia_fechamento_mes' . $idMes}) {
            $dataTerminoFechamento = $ano . '-' . $mes . '-' . $alocacao->{'dia_fechamento_mes' . $idMes};
            $dataInicioFechamento = date('Y-m-d', strtotime($dataTerminoFechamento . ' -1month +1day'));
        }

        $rowsPagamentos = $this->db
            ->select("d.id, a.id_alocado, a.periodo, a.cargo{$mesCargoFuncao} AS cargo, a.funcao{$mesCargoFuncao} AS funcao")
            ->select(["TIME_FORMAT(SEC_TO_TIME((SUM(IF(k.status = 'HE', IFNULL(TIME_TO_SEC(k.desconto), 0), 0)) * IFNULL(COUNT(DISTINCT(k.id)) / COUNT(k.id), 1)) + TIME_TO_SEC(IF(ef3.id IS NOT NULL, f.horas_mensais_custo_3, IF(ef2.id IS NOT NULL, f.horas_mensais_custo_2, IF(ef1.id IS NOT NULL, f.horas_mensais_custo, NULL)))) + IFNULL(TIME_TO_SEC(d.dias_descontados_mes{$idMes}), 0) + IFNULL(TIME_TO_SEC(LEAST(d.horas_descontadas_mes{$idMes}, 0)), 0)), '%H:%i') AS total_horas_mes"], false)
            ->select(["TIME_FORMAT(SEC_TO_TIME((SUM(IF(k.status = 'HE', IFNULL(TIME_TO_SEC(k.desconto), 0), 0)) * IFNULL(COUNT(DISTINCT(k.id)) / COUNT(k.id), 1)) + TIME_TO_SEC(IF(ef6.id IS NOT NULL, f.horas_mensais_custo_3t, IF(ef5.id IS NOT NULL, f.horas_mensais_custo_2t, IF(ef4.id IS NOT NULL, f.horas_mensais_custo_1t, NULL)))) + IFNULL(TIME_TO_SEC(d.dias_descontados_mes{$idMes}), 0) + IFNULL(TIME_TO_SEC(LEAST(d.horas_descontadas_mes{$idMes}, 0)), 0)), '%H:%i') AS total_horas_mes_2"], false)
            ->select(["TIME_FORMAT(SEC_TO_TIME((SUM(IF(k.status = 'HE', IFNULL(TIME_TO_SEC(k.desconto), 0), 0)) * IFNULL(COUNT(DISTINCT(k.id)) / COUNT(k.id), 1)) + TIME_TO_SEC(IF(ef9.id IS NOT NULL, f.horas_mensais_custo_3n, IF(ef8.id IS NOT NULL, f.horas_mensais_custo_2n, IF(ef7.id IS NOT NULL, f.horas_mensais_custo_1n, NULL)))) + IFNULL(TIME_TO_SEC(d.dias_descontados_mes{$idMes}), 0) + IFNULL(TIME_TO_SEC(LEAST(d.horas_descontadas_mes{$idMes}, 0)), 0)), '%H:%i') AS total_horas_mes_3"], false)
            ->select(["(CASE WHEN ef3.id IS NOT NULL THEN IF(f.valor_hora_operacional_3 > 0, f.valor_hora_operacional_3, j.valor_pagamento)
                                     WHEN ef2.id IS NOT NULL THEN IF(f.valor_hora_operacional_2 > 0, f.valor_hora_operacional_2, j.valor_pagamento)
                                     WHEN ef1.id IS NOT NULL THEN IF(f.valor_hora_operacional > 0, f.valor_hora_operacional, j.valor_pagamento)
                                     END) valor_hora_operacional"], false)
            ->select(["(CASE WHEN ef6.id IS NOT NULL THEN IF(f.valor_hora_operacional_3t > 0, f.valor_hora_operacional_3t, j.valor_pagamento)
                                     WHEN ef5.id IS NOT NULL THEN IF(f.valor_hora_operacional_2t > 0, f.valor_hora_operacional_2t, j.valor_pagamento)
                                     WHEN ef4.id IS NOT NULL THEN IF(f.valor_hora_operacional_1t > 0, f.valor_hora_operacional_1t, j.valor_pagamento)
                                     END) valor_hora_operacional_2"], false)
            ->select(["(CASE WHEN ef9.id IS NOT NULL THEN IF(f.valor_hora_operacional_3n > 0, f.valor_hora_operacional_3n, j.valor_pagamento)
                                     WHEN ef8.id IS NOT NULL THEN IF(f.valor_hora_operacional_2n > 0, f.valor_hora_operacional_2n, j.valor_pagamento)
                                     WHEN ef7.id IS NOT NULL THEN IF(f.valor_hora_operacional_1n > 0, f.valor_hora_operacional_1n, j.valor_pagamento)
                                     END) valor_hora_operacional_3"], false)
            ->select(["(CASE WHEN ef3.id IS NOT NULL THEN IF(f.valor_hora_operacional_3 > 0, f.valor_hora_operacional_3, j.valor_pagamento)
                                     WHEN ef2.id IS NOT NULL THEN IF(f.valor_hora_operacional_2 > 0, f.valor_hora_operacional_2, j.valor_pagamento)
                                     WHEN ef1.id IS NOT NULL THEN IF(f.valor_hora_operacional > 0, f.valor_hora_operacional, j.valor_pagamento)
                                     END)  * (((SUM(IF(k.status = 'HE', IFNULL(TIME_TO_SEC(k.desconto), 0), 0)) * IFNULL(COUNT(DISTINCT(k.id)) / COUNT(k.id), 1)) + (TIME_TO_SEC(IF(ef3.id IS NOT NULL, f.horas_mensais_custo_3, IF(ef2.id IS NOT NULL, f.horas_mensais_custo_2, IF(ef1.id IS NOT NULL, f.horas_mensais_custo, NULL)))) + IFNULL(TIME_TO_SEC(d.dias_descontados_mes{$idMes}), 0) + IFNULL(TIME_TO_SEC(LEAST(d.horas_descontadas_mes{$idMes}, 0)), 0))) / 3600) AS valor_total"], false)
            ->select(["(CASE WHEN ef6.id IS NOT NULL THEN IF(f.valor_hora_operacional_3t > 0, f.valor_hora_operacional_3t, j.valor_pagamento)
                                     WHEN ef5.id IS NOT NULL THEN IF(f.valor_hora_operacional_2t > 0, f.valor_hora_operacional_2t, j.valor_pagamento)
                                     WHEN ef4.id IS NOT NULL THEN IF(f.valor_hora_operacional_1t > 0, f.valor_hora_operacional_1t, j.valor_pagamento)
                                     END)  * (((SUM(IF(k.status = 'HE', IFNULL(TIME_TO_SEC(k.desconto), 0), 0)) * IFNULL(COUNT(DISTINCT(k.id)) / COUNT(k.id), 1)) + (TIME_TO_SEC(IF(ef6.id IS NOT NULL, f.horas_mensais_custo_3t, IF(ef5.id IS NOT NULL, f.horas_mensais_custo_2t, IF(ef4.id IS NOT NULL, f.horas_mensais_custo_1t, NULL)))) + IFNULL(TIME_TO_SEC(d.dias_descontados_mes{$idMes}), 0) + IFNULL(TIME_TO_SEC(LEAST(d.horas_descontadas_mes{$idMes}, 0)), 0))) / 3600) AS valor_total_2"], false)
            ->select(["(CASE WHEN ef9.id IS NOT NULL THEN IF(f.valor_hora_operacional_3n > 0, f.valor_hora_operacional_3n, j.valor_pagamento)
                                     WHEN ef8.id IS NOT NULL THEN IF(f.valor_hora_operacional_2n > 0, f.valor_hora_operacional_2n, j.valor_pagamento)
                                     WHEN ef7.id IS NOT NULL THEN IF(f.valor_hora_operacional_1n > 0, f.valor_hora_operacional_1n, j.valor_pagamento)
                                     END)  * (((SUM(IF(k.status = 'HE', IFNULL(TIME_TO_SEC(k.desconto), 0), 0)) * IFNULL(COUNT(DISTINCT(k.id)) / COUNT(k.id), 1)) + (TIME_TO_SEC(IF(ef9.id IS NOT NULL, f.horas_mensais_custo_3n, IF(ef8.id IS NOT NULL, f.horas_mensais_custo_2n, IF(ef7.id IS NOT NULL, f.horas_mensais_custo_1n, NULL)))) + IFNULL(TIME_TO_SEC(d.dias_descontados_mes{$idMes}), 0) + IFNULL(TIME_TO_SEC(LEAST(d.horas_descontadas_mes{$idMes}, 0)), 0))) / 3600) AS valor_total_3"], false)
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocados_totalizacao d', "d.id_alocado = b.id AND d.periodo = a.periodo AND d.cargo{$mesCargoFuncao} = a.cargo{$mesCargoFuncao} AND d.funcao{$mesCargoFuncao} = a.funcao{$mesCargoFuncao} AND d.id_cuidador = b.id_cuidador", 'left', false)
            ->join('ei_ordem_servico_horarios e', 'e.id = a.id_os_horario', 'left')
            ->join('ei_ordem_servico_profissionais f', 'f.id = b.id_os_profissional', 'left')
            ->join('ei_ordem_servico_escolas g', 'g.id = f.id_ordem_servico_escola', 'left')
            ->join('ei_ordem_servico h', 'h.id = g.id_ordem_servico', 'left')
            ->join('ei_contratos i', 'i.id = h.id_contrato', 'left')
            ->join('ei_valores_faturamento j', 'j.id_contrato = i.id AND j.ano = h.ano AND j.semestre = h.semestre AND j.id_funcao = e.id_funcao', 'left')
            ->join('ei_apontamento k', "k.id_alocado = b.id AND k.periodo = a.periodo AND (k.data BETWEEN '{$dataInicioFechamento}' AND '{$dataTerminoFechamento}') AND (k.id_alocado_sub1 IS NULL OR k.id_alocado_sub2 IS NULL)", 'left')
            ->join('empresa_funcoes ef1', "ef1.id = f.id_funcao AND ef1.nome = a.funcao{$mesCargoFuncao} AND a.periodo = 1", 'left', false)
            ->join('empresa_funcoes ef2', "ef2.id = f.id_funcao_2m AND ef2.nome = a.funcao{$mesCargoFuncao} AND a.periodo = 1", 'left', false)
            ->join('empresa_funcoes ef3', "ef3.id = f.id_funcao_3m AND ef3.nome = a.funcao{$mesCargoFuncao} AND a.periodo = 1", 'left', false)
            ->join('empresa_funcoes ef4', "ef4.id = f.id_funcao_1t AND ef4.nome = a.funcao{$mesCargoFuncao} AND a.periodo = 2", 'left', false)
            ->join('empresa_funcoes ef5', "ef5.id = f.id_funcao_2t AND ef5.nome = a.funcao{$mesCargoFuncao} AND a.periodo = 2", 'left', false)
            ->join('empresa_funcoes ef6', "ef6.id = f.id_funcao_3t AND ef6.nome = a.funcao{$mesCargoFuncao} AND a.periodo = 2", 'left', false)
            ->join('empresa_funcoes ef7', "ef7.id = f.id_funcao_1n AND ef7.nome = a.funcao{$mesCargoFuncao} AND a.periodo = 3", 'left', false)
            ->join('empresa_funcoes ef8', "ef8.id = f.id_funcao_2n AND ef8.nome = a.funcao{$mesCargoFuncao} AND a.periodo = 3", 'left', false)
            ->join('empresa_funcoes ef9', "ef9.id = f.id_funcao_3n AND ef9.nome = a.funcao{$mesCargoFuncao} AND a.periodo = 3", 'left', false)
            ->where('c.id_alocacao', $alocacao->id)
            ->group_by(['b.id_cuidador', 'c.id_escola', 'a.periodo', 'a.cargo' . $mesCargoFuncao, 'a.funcao' . $mesCargoFuncao])
            ->order_by('b.id_cuidador', 'asc')
            ->order_by('c.id_escola', 'asc')
            ->order_by('a.periodo', 'asc')
            ->order_by('a.cargo' . $mesCargoFuncao, 'asc')
            ->order_by('a.funcao' . $mesCargoFuncao, 'asc')
            ->get('ei_alocados_horarios a')
            ->result();

        $pagamentosTotalizados = [];
        $pagamentosNaoTotalizados = [];
        foreach ($rowsPagamentos as $rowPagamento) {
            if ($rowPagamento->periodo == 3) {
                $dataPagamento = [
                    'total_horas_faturadas_mes' => $rowPagamento->total_horas_mes_3 ?? $rowPagamento->total_horas_mes,
                    'valor_pagamento_mes' => number_format($rowPagamento->valor_hora_operacional_3 ?? $rowPagamento->valor_hora_operacional, 2, '.', ''),
                    'valor_total_mes' => number_format($rowPagamento->valor_total_3 ?? $rowPagamento->valor_total, 2, '.', ''),
                ];
            } elseif ($rowPagamento->periodo == 2) {
                $dataPagamento = [
                    'total_horas_faturadas_mes' => $rowPagamento->total_horas_mes_2 ?? $rowPagamento->total_horas_mes,
                    'valor_pagamento_mes' => number_format($rowPagamento->valor_hora_operacional_2 ?? $rowPagamento->valor_hora_operacional, 2, '.', ''),
                    'valor_total_mes' => number_format($rowPagamento->valor_total_2 ?? $rowPagamento->valor_total, 2, '.', ''),
                ];
            } else {
                $dataPagamento = [
                    'total_horas_faturadas_mes' => $rowPagamento->total_horas_mes,
                    'valor_pagamento_mes' => number_format($rowPagamento->valor_hora_operacional, 2, '.', ''),
                    'valor_total_mes' => number_format($rowPagamento->valor_total, 2, '.', ''),
                ];
            }

            if ($rowPagamento->id) {
                $pagamentosTotalizados[$rowPagamento->id] = $dataPagamento;
            } else {
                $pagamentosNaoTotalizados[$rowPagamento->id_alocado][$rowPagamento->periodo][$rowPagamento->cargo][$rowPagamento->funcao] = $dataPagamento;
            }
        }

        $this->load->helper('time');

        $this->db->trans_begin();

        foreach ($rows as $row) {
            $data = [
                'id' => $row->id_totalizacao,
                'id_alocado' => $row->id_alocado,
                'periodo' => $row->periodo,
            ];

            if (empty($row->preservar_faturamento)) {
                $data["total_dias_mes{$idMes}"] = intval($row->total_dias) + intval($row->total_eventos_fim_semana);
                $data["total_descontos_mes{$idMes}"] = $row->total_descontos;
                $data["total_horas_mes{$idMes}"] = secToTime(timeToSec($row->total_horas) + timeToSec($row->total_descontos));
            }

            if (empty($row->preservar_pagamento_prestador)) {
                if ($data['id']) {
                    $data["total_horas_faturadas_mes{$idMes}"] = $pagamentosTotalizados[$data['id']]['total_horas_faturadas_mes'] ?? null;
                    $data["valor_pagamento_mes{$idMes}"] = $pagamentosTotalizados[$data['id']]['valor_pagamento_mes'] ?? null;
                    $data["valor_total_mes{$idMes}"] = $pagamentosTotalizados[$data['id']]['valor_total_mes'] ?? null;
                } else {
                    $data["total_horas_faturadas_mes{$idMes}"] = $pagamentosNaoTotalizados[$row->id_alocado][$row->periodo][$row->cargo][$row->funcao]['total_horas_faturadas_mes'] ?? null;
                    $data["valor_pagamento_mes{$idMes}"] = $pagamentosNaoTotalizados[$row->id_alocado][$row->periodo][$row->cargo][$row->funcao]['valor_pagamento_mes'] ?? null;
                    $data["valor_total_mes{$idMes}"] = $pagamentosNaoTotalizados[$row->id_alocado][$row->periodo][$row->cargo][$row->funcao]['valor_total_mes'] ?? null;
                }
            }

            if ($data['id'] and (empty($row->preservar_faturamento) or empty($row->preservar_pagamento_prestador))) {
                $this->db->update('ei_alocados_totalizacao', $data, ['id' => $data['id']]);
            } else {
                $this->db->insert('ei_alocados_totalizacao', $data);
            }

            if ($this->db->trans_status() == false) {
                $this->db->trans_rollback();
                exit(json_encode(['erro' => 'Erro ao salvar o mês.']));
            }
        }

        $this->db->trans_commit();

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    public function faturamento_consolidado()
    {
        $idDiretoria = $this->input->post('diretoria');
        $mes = $this->input->post('mes');
        $ano = $this->input->post('ano');

        $data['planilha_faturamento_consolidado'] = $this->planilhaFaturamentoConsolidado($idDiretoria, $mes, $ano);

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function recalcular_ingresso()
    {
        $post = $this->input->post();
        $empresa = $this->session->userdata('empresa');
        $ano = $this->input->post('ano');
        $semestre = $this->input->post('semestre');

        $mes1 = $semestre > 1 ? '07' : '01';
        $mes2 = $semestre > 1 ? '08' : '02';
        $mes3 = $semestre > 1 ? '09' : '03';
        $mes4 = $semestre > 1 ? '10' : '04';
        $mes5 = $semestre > 1 ? '11' : '05';
        $mes6 = $semestre > 1 ? '12' : '06';
        $mes7 = $semestre === '1' ? '07' : '';

        $diaIniMes1 = date('Y-m-d', strtotime("{$ano}-{$mes1}-01"));
        $diaIniMes2 = date('Y-m-d', strtotime("{$ano}-{$mes2}-01"));
        $diaIniMes3 = date('Y-m-d', strtotime("{$ano}-{$mes3}-01"));
        $diaIniMes4 = date('Y-m-d', strtotime("{$ano}-{$mes4}-01"));
        $diaIniMes5 = date('Y-m-d', strtotime("{$ano}-{$mes5}-01"));
        $diaIniMes6 = date('Y-m-d', strtotime("{$ano}-{$mes6}-01"));
        if ($semestre === '1') {
            $diaIniMes7 = date('Y-m-d', strtotime("{$ano}-{$mes7}-01"));
        } else {
            $diaIniMes7 = '';
        }

        $diaFimMes1 = date('Y-m-t', strtotime($diaIniMes1));
        $diaFimMes2 = date('Y-m-t', strtotime($diaIniMes2));
        $diaFimMes3 = date('Y-m-t', strtotime($diaIniMes3));
        $diaFimMes4 = date('Y-m-t', strtotime($diaIniMes4));
        $diaFimMes5 = date('Y-m-t', strtotime($diaIniMes5));
        $diaFimMes6 = date('Y-m-t', strtotime($diaIniMes6));
        if ($semestre === '1') {
            $diaFimMes7 = date('Y-m-t', strtotime($diaIniMes7));
        } else {
            $diaFimMes7 = '';
        }

        $qb = $this->db
            ->select('a.id')
            ->select(["IF(MONTH(a.data_inicio_real) = {$mes1}, (WEEK(DATE_SUB(LAST_DAY(a.data_inicio_real), INTERVAL ((7 + DATE_FORMAT(LAST_DAY(a.data_inicio_real), '%w') - a.dia_semana) % 7) DAY)) + 1) - WEEK(DATE_ADD(a.data_inicio_real, INTERVAL (((7 - DATE_FORMAT(a.data_inicio_real, '%w')) + a.dia_semana) % 7) DAY)), IF(MONTH(a.data_inicio_real) > {$mes1}, 0, total_semanas_mes1)) AS total_semanas_mes1"], false)
            ->select(["IF(MONTH(a.data_inicio_real) = {$mes2}, (WEEK(DATE_SUB(LAST_DAY(a.data_inicio_real), INTERVAL ((7 + DATE_FORMAT(LAST_DAY(a.data_inicio_real), '%w') - a.dia_semana) % 7) DAY)) + 1) - WEEK(DATE_ADD(a.data_inicio_real, INTERVAL (((7 - DATE_FORMAT(a.data_inicio_real, '%w')) + a.dia_semana) % 7) DAY)), IF(MONTH(a.data_inicio_real) > {$mes2}, 0, total_semanas_mes2)) AS total_semanas_mes2"], false)
            ->select(["IF(MONTH(a.data_inicio_real) = {$mes3}, (WEEK(DATE_SUB(LAST_DAY(a.data_inicio_real), INTERVAL ((7 + DATE_FORMAT(LAST_DAY(a.data_inicio_real), '%w') - a.dia_semana) % 7) DAY)) + 1) - WEEK(DATE_ADD(a.data_inicio_real, INTERVAL (((7 - DATE_FORMAT(a.data_inicio_real, '%w')) + a.dia_semana) % 7) DAY)), IF(MONTH(a.data_inicio_real) > {$mes3}, 0, total_semanas_mes3)) AS total_semanas_mes3"], false)
            ->select(["IF(MONTH(a.data_inicio_real) = {$mes4}, (WEEK(DATE_SUB(LAST_DAY(a.data_inicio_real), INTERVAL ((7 + DATE_FORMAT(LAST_DAY(a.data_inicio_real), '%w') - a.dia_semana) % 7) DAY)) + 1) - WEEK(DATE_ADD(a.data_inicio_real, INTERVAL (((7 - DATE_FORMAT(a.data_inicio_real, '%w')) + a.dia_semana) % 7) DAY)), IF(MONTH(a.data_inicio_real) > {$mes4}, 0, total_semanas_mes4)) AS total_semanas_mes4"], false)
            ->select(["IF(MONTH(a.data_inicio_real) = {$mes5}, (WEEK(DATE_SUB(LAST_DAY(a.data_inicio_real), INTERVAL ((7 + DATE_FORMAT(LAST_DAY(a.data_inicio_real), '%w') - a.dia_semana) % 7) DAY)) + 1) - WEEK(DATE_ADD(a.data_inicio_real, INTERVAL (((7 - DATE_FORMAT(a.data_inicio_real, '%w')) + a.dia_semana) % 7) DAY)), IF(MONTH(a.data_inicio_real) > {$mes5}, 0, total_semanas_mes5)) AS total_semanas_mes5"], false)
            ->select(["IF(MONTH(a.data_inicio_real) = {$mes6}, (WEEK(DATE_SUB(LAST_DAY(a.data_inicio_real), INTERVAL ((7 + DATE_FORMAT(LAST_DAY(a.data_inicio_real), '%w') - a.dia_semana) % 7) DAY)) + 1) - WEEK(DATE_ADD(a.data_inicio_real, INTERVAL (((7 - DATE_FORMAT(a.data_inicio_real, '%w')) + a.dia_semana) % 7) DAY)), IF(MONTH(a.data_inicio_real) > {$mes6}, 0, total_semanas_mes6)) AS total_semanas_mes6"], false);
        if ($semestre === '1') {
            $qb->select(["IF(MONTH(a.data_inicio_real) = {$mes7}, (WEEK(DATE_SUB(LAST_DAY(a.data_inicio_real), INTERVAL ((7 + DATE_FORMAT(LAST_DAY(a.data_inicio_real), '%w') - a.dia_semana) % 7) DAY)) + 1) - WEEK(DATE_ADD(a.data_inicio_real, INTERVAL (((7 - DATE_FORMAT(a.data_inicio_real, '%w')) + a.dia_semana) % 7) DAY) + 1), IF(MONTH(a.data_inicio_real) > {$mes7}, 0, total_semanas_mes7)) AS total_semanas_mes7"], false);
        }
        $data = $qb
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->join('ei_matriculados_turmas e', 'e.id_alocado_horario = a.id', 'left')
            ->join('ei_matriculados f', 'f.id = e.id_matriculado AND f.id_alocacao_escola = c.id', 'left')
            ->where('d.id_empresa', $empresa)
            ->where('d.depto', $post['depto'])
            ->where('d.id_diretoria', $post['diretoria'])
            ->where('d.id_supervisor', $post['supervisor'])
            ->where('d.ano', $post['ano'])
            ->where('d.semestre', $post['semestre'])
            ->group_by('a.id')
            ->get('ei_alocados_horarios a')
            ->result();

        $this->db->trans_start();
        $this->db->update_batch('ei_alocados_horarios', $data, 'id');
        $this->db->trans_complete();

        $status = $this->db->trans_status();
        if ($status === false) {
            exit(json_encode(['erro' => 'Erro ao recalcular quantidade de dias.']));
        }

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function recalcular_recesso()
    {
        $post = $this->input->post();
        $empresa = $this->session->userdata('empresa');
        $ano = $this->input->post('ano');
        $semestre = $this->input->post('semestre');

        $mes1 = $semestre > 1 ? '07' : '01';
        $mes2 = $semestre > 1 ? '08' : '02';
        $mes3 = $semestre > 1 ? '09' : '03';
        $mes4 = $semestre > 1 ? '10' : '04';
        $mes5 = $semestre > 1 ? '11' : '05';
        $mes6 = $semestre > 1 ? '12' : '06';
        $mes7 = $semestre === '1' ? '07' : '';

        $diaIniMes1 = date('Y-m-d', strtotime("{$ano}-{$mes1}-01"));
        $diaIniMes2 = date('Y-m-d', strtotime("{$ano}-{$mes2}-01"));
        $diaIniMes3 = date('Y-m-d', strtotime("{$ano}-{$mes3}-01"));
        $diaIniMes4 = date('Y-m-d', strtotime("{$ano}-{$mes4}-01"));
        $diaIniMes5 = date('Y-m-d', strtotime("{$ano}-{$mes5}-01"));
        $diaIniMes6 = date('Y-m-d', strtotime("{$ano}-{$mes6}-01"));
        if ($semestre === '1') {
            $diaIniMes7 = date('Y-m-d', strtotime("{$ano}-{$mes7}-01"));
        } else {
            $diaIniMes7 = '';
        }

        $diaFimMes1 = date('Y-m-t', strtotime($diaIniMes1));
        $diaFimMes2 = date('Y-m-t', strtotime($diaIniMes2));
        $diaFimMes3 = date('Y-m-t', strtotime($diaIniMes3));
        $diaFimMes4 = date('Y-m-t', strtotime($diaIniMes4));
        $diaFimMes5 = date('Y-m-t', strtotime($diaIniMes5));
        $diaFimMes6 = date('Y-m-t', strtotime($diaIniMes6));
        if ($semestre === '1') {
            $diaFimMes7 = date('Y-m-t', strtotime($diaIniMes7));
        } else {
            $diaFimMes7 = '';
        }

        $qb = $this->db
            ->select('a.id')
            ->select(["IF(MONTH(a.data_termino_real) = {$mes1}, (WEEK(DATE_SUB(a.data_termino_real, INTERVAL ((7 + DATE_FORMAT(a.data_termino_real, '%w') - a.dia_semana) % 7) DAY)) + 1) - WEEK(DATE_ADD(DATE_SUB(a.data_termino_real, INTERVAL (DAY(a.data_termino_real) - 1) DAY), INTERVAL (((7 - DATE_FORMAT(DATE_SUB(a.data_termino_real, INTERVAL (DAY(a.data_termino_real) - 1) DAY), '%w')) + a.dia_semana) % 7) DAY)), IF(MONTH(a.data_termino_real) < {$mes1}, 0, total_semanas_mes1)) AS total_semanas_mes1"], false)
            ->select(["IF(MONTH(a.data_termino_real) = {$mes2}, (WEEK(DATE_SUB(a.data_termino_real, INTERVAL ((7 + DATE_FORMAT(a.data_termino_real, '%w') - a.dia_semana) % 7) DAY)) + 1) - WEEK(DATE_ADD(DATE_SUB(a.data_termino_real, INTERVAL (DAY(a.data_termino_real) - 1) DAY), INTERVAL (((7 - DATE_FORMAT(DATE_SUB(a.data_termino_real, INTERVAL (DAY(a.data_termino_real) - 1) DAY), '%w')) + a.dia_semana) % 7) DAY)), IF(MONTH(a.data_termino_real) < {$mes2}, 0, total_semanas_mes2)) AS total_semanas_mes2"], false)
            ->select(["IF(MONTH(a.data_termino_real) = {$mes3}, (WEEK(DATE_SUB(a.data_termino_real, INTERVAL ((7 + DATE_FORMAT(a.data_termino_real, '%w') - a.dia_semana) % 7) DAY)) + 1) - WEEK(DATE_ADD(DATE_SUB(a.data_termino_real, INTERVAL (DAY(a.data_termino_real) - 1) DAY), INTERVAL (((7 - DATE_FORMAT(DATE_SUB(a.data_termino_real, INTERVAL (DAY(a.data_termino_real) - 1) DAY), '%w')) + a.dia_semana) % 7) DAY)), IF(MONTH(a.data_termino_real) < {$mes3}, 0, total_semanas_mes3)) AS total_semanas_mes3"], false)
            ->select(["IF(MONTH(a.data_termino_real) = {$mes4}, (WEEK(DATE_SUB(a.data_termino_real, INTERVAL ((7 + DATE_FORMAT(a.data_termino_real, '%w') - a.dia_semana) % 7) DAY)) + 1) - WEEK(DATE_ADD(DATE_SUB(a.data_termino_real, INTERVAL (DAY(a.data_termino_real) - 1) DAY), INTERVAL (((7 - DATE_FORMAT(DATE_SUB(a.data_termino_real, INTERVAL (DAY(a.data_termino_real) - 1) DAY), '%w')) + a.dia_semana) % 7) DAY)), IF(MONTH(a.data_termino_real) < {$mes4}, 0, total_semanas_mes4)) AS total_semanas_mes4"], false)
            ->select(["IF(MONTH(a.data_termino_real) = {$mes5}, (WEEK(DATE_SUB(a.data_termino_real, INTERVAL ((7 + DATE_FORMAT(a.data_termino_real, '%w') - a.dia_semana) % 7) DAY)) + 1) - WEEK(DATE_ADD(DATE_SUB(a.data_termino_real, INTERVAL (DAY(a.data_termino_real) - 1) DAY), INTERVAL (((7 - DATE_FORMAT(DATE_SUB(a.data_termino_real, INTERVAL (DAY(a.data_termino_real) - 1) DAY), '%w')) + a.dia_semana) % 7) DAY)), IF(MONTH(a.data_termino_real) < {$mes5}, 0, total_semanas_mes5)) AS total_semanas_mes5"], false)
            ->select(["IF(MONTH(a.data_termino_real) = {$mes6}, (WEEK(DATE_SUB(a.data_termino_real, INTERVAL ((7 + DATE_FORMAT(a.data_termino_real, '%w') - a.dia_semana) % 7) DAY)) + 1) - WEEK(DATE_ADD(DATE_SUB(a.data_termino_real, INTERVAL (DAY(a.data_termino_real) - 1) DAY), INTERVAL (((7 - DATE_FORMAT(DATE_SUB(a.data_termino_real, INTERVAL (DAY(a.data_termino_real) - 1) DAY), '%w')) + a.dia_semana) % 7) DAY)), IF(MONTH(a.data_termino_real) < {$mes6}, 0, total_semanas_mes6)) AS total_semanas_mes6"], false);
        if ($semestre === '1') {
            $qb->select(["IF(MONTH(a.data_termino_real) = {$mes7}, (WEEK(DATE_SUB(a.data_termino_real, INTERVAL ((7 + DATE_FORMAT(a.data_termino_real, '%w') - a.dia_semana) % 7) DAY)) + 1) - WEEK(DATE_ADD(DATE_SUB(a.data_termino_real, INTERVAL (DAY(a.data_termino_real) - 1) DAY), INTERVAL (((7 - DATE_FORMAT(DATE_SUB(a.data_termino_real, INTERVAL (DAY(a.data_termino_real) - 1) DAY), '%w')) + a.dia_semana) % 7) DAY)), IF(MONTH(a.data_termino_real) < {$mes7}, 0, total_semanas_mes7)) AS total_semanas_mes7"], false);
        }
        $data = $qb
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->join('ei_matriculados_turmas e', 'e.id_alocado_horario = a.id', 'left')
            ->join('ei_matriculados f', 'f.id = e.id_matriculado AND f.id_alocacao_escola = c.id', 'left')
            ->where('d.id_empresa', $empresa)
            ->where('d.depto', $post['depto'])
            ->where('d.id_diretoria', $post['diretoria'])
            ->where('d.id_supervisor', $post['supervisor'])
            ->where('d.ano', $post['ano'])
            ->where('d.semestre', $post['semestre'])
            ->group_by('a.id')
            ->get('ei_alocados_horarios a')->result();

        $this->db->trans_start();
        $this->db->update_batch('ei_alocados_horarios', $data, 'id');
        $this->db->trans_complete();

        $status = $this->db->trans_status();
        if ($status === false) {
            exit(json_encode(['erro' => 'Erro ao recalcular quantidade de dias.']));
        }

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function recuperar_faturamento_consolidado()
    {
        $idDiretoria = $this->input->post('diretoria');
        $mes = $this->input->post('mes');
        $ano = $this->input->post('ano');

        $data['planilha_faturamento_consolidado'] = $this->planilhaFaturamentoConsolidado($idDiretoria, $mes, $ano, false, true);

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_save_faturamento_consolidado()
    {
        $mes = $this->input->post('mes');
        $semestre = $this->input->post('semestre');
        $idMes = intval($mes) - ($semestre > 1 ? 6 : 0);

        $rows = array_map(null,
            $this->input->post('id'),
            $this->input->post('id_alocacao'),
            $this->input->post('cargo'),
            $this->input->post('funcao'),
            $this->input->post('valor_hora'),
            $this->input->post('total_horas'),
            $this->input->post('valor_faturado'),
        );

        $obs = $this->input->post('observacoes');
        $totalHoras = $this->input->post('total_horas_consolidadas');
        $valorFaturado = $this->input->post('valor_faturado_consolidado');
        $dataObs = [
            "observacoes_mes{$idMes}" => strlen($obs) > 0 ? $obs : null,
            "total_horas_mes{$idMes}" => strlen($totalHoras) > 0 ? $totalHoras : null,
            "valor_faturado_mes{$idMes}" => strlen($valorFaturado) > 0 ? str_replace(['.', ','], ['', '.'], $valorFaturado) : null,
        ];

        $campos = [
            'id',
            'id_alocacao',
            'cargo',
            'funcao',
            "valor_hora_mes{$idMes}",
            "total_horas_mes{$idMes}",
            "valor_faturado_mes{$idMes}",
        ];

        $this->db->trans_start();

        foreach ($rows as $data) {
            $data[4] = str_replace(['.', ','], ['', '.'], $data[4]);
            $data[6] = str_replace(['.', ','], ['', '.'], $data[6]);
            $data = array_combine($campos, $data);
            if ($data['id']) {
                $this->db->update('ei_faturamento_consolidado', $data, ['id' => $data['id']]);
            } else {
                $this->db->insert('ei_faturamento_consolidado', $data);
            }

            $this->db->update('ei_alocacao', $dataObs, ['id' => $data['id_alocacao']]);
        }

        $status = $this->db->trans_status();
        $this->db->trans_complete();

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function pdf_totalizacao_consolidada()
    {
        $this->load->library('m_pdf');

        $stylesheet = '#table thead tr th { border-top: 4px solid #ddd; padding-top: 8px; } ';
        $stylesheet .= '#table { border-bottom: 1px solid #ddd; } ';
        $stylesheet .= '#periodo { border: 1px solid #444; margin-bottom: 0px; } ';
        $stylesheet .= '#periodo thead th { padding: 4px; background-color: #f5f5f5; border: 1px solid #444; } ';
        $stylesheet .= '#periodo tbody td { font-size: 12px; padding: 4px; vertical-align: top; border: 1px solid #444; } ';
        $stylesheet .= 'p strong { font-weight: bold; }';

        $this->m_pdf->pdf->writeHTML($stylesheet, 1);
        $idDiretoria = $this->input->get('diretoria');
        $mes = $this->input->get('mes');
        $idMes = (int)$mes - ($this->input->get('semestre') > 1 ? 6 : 0);
        $ano = $this->input->get('ano');
        $this->m_pdf->pdf->writeHTML($this->planilhaFaturamentoConsolidado($idDiretoria, $mes, $ano, true));

        $this->load->library('Calendar');
        $mes = $this->calendar->get_month_name($mes);

        $this->m_pdf->pdf->Output("PF-Educação Inclusiva - {$mes}/{$ano}.pdf", 'D');
    }

    //--------------------------------------------------------------------

    private function planilhaFaturamentoConsolidado(?int $idDiretoria, ?string $mes, ?int $ano, ?bool $is_pdf = false, ?bool $recuperar = false): string
    {
        // prepara o cabecalho da planilha
        $empresa = $this->db
            ->select('foto, foto_descricao')
            ->where('id', $this->session->userdata('empresa'))
            ->get('usuarios')
            ->row();

        $usuario = $this->db
            ->select('nome, email')
            ->where('id', $this->session->userdata('id'))
            ->get('usuarios')
            ->row();

        // prepara as variaveis locais
        $depto = $this->input->get_post('depto');
        $idSupervisor = $this->input->get_post('supervisor_filtrado');
        $semestre = $this->input->get_post('semestre');
        $idMes = intval($mes) - ($semestre > 1 ? 6 : 0);
        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';

        // recupera dados de apresentacao da planilha
        $qb = $this->db
            ->select("GROUP_CONCAT(DISTINCT a.id ORDER BY a.id SEPARATOR ',') AS id", false)
            ->select('c1.id AS id_medicao_mensal, a.diretoria, null AS valor_hora', false)
            ->select("a.observacoes_mes{$idMes} AS observacoes", false)
            ->select(["GROUP_CONCAT(DISTINCT b3.contrato ORDER BY b3.contrato SEPARATOR ', ') AS contratos"], false)
            ->select(["GROUP_CONCAT(DISTINCT b2.nome ORDER BY b2.nome SEPARATOR ', ') AS ordens_servico"], false)
            ->join('ei_alocacao_escolas b', 'b.id_alocacao = a.id')
            ->join('ei_ordem_servico_escolas b1', 'b1.id = b.id_os_escola')
            ->join('ei_ordem_servico b2', 'b2.id = b1.id_ordem_servico AND b2.ano = a.ano AND b2.semestre = a.semestre')
            ->join('ei_contratos b3', 'b3.id = b2.id_contrato')
            ->join('ei_medicao_mensal_old c1', "c1.id_empresa = a.id_empresa AND c1.depto = a.depto AND c1.id_diretoria = a.id_diretoria AND c1.ano = a.ano AND c1.semestre = a.semestre AND c1.id_supervisor = '{$idSupervisor}'", 'left')
            ->join('ei_alocados c', 'c.id_alocacao_escola = b.id', 'left')
            ->join('ei_alocados_horarios d', 'd.id_alocado = c.id', 'left')
            ->where('a.id_empresa', $this->session->userdata('empresa'))
            ->where('a.depto', $depto)
            ->where('a.id_diretoria', $idDiretoria);
        if ($idSupervisor) {
            $qb->where('a.id_supervisor', $idSupervisor);
        }
        $data = $qb
            ->where('a.ano', $ano)
            ->where('a.semestre', $semestre)
            ->where("d.funcao{$mesCargoFuncao} IS NOT NULL", null, false)
            ->group_by(['a.id_empresa', 'a.depto', 'a.diretoria', 'a.ano', 'a.semestre'])
            ->get('ei_alocacao a')
            ->row();

        $dataInicioMes = "{$ano}-{$mes}-01";
        $dataTerminoMes = date('Y-m-t', strtotime($dataInicioMes));

        // recupera dados de faturamento consolidado
        $subquery = $this->db
            ->select("d.id_alocacao, j.cargo{$mesCargoFuncao} AS cargo, j.funcao{$mesCargoFuncao} AS funcao, j.valor_hora_funcao")
            ->select(["GREATEST(  SUM(IFNULL(TIME_TO_SEC(a.total_horas_mes{$idMes}), 0) - IFNULL(TIME_TO_SEC(i.total_horas_mes{$idMes}), 0))  , 0) AS total_segundos_mes{$idMes}"], false)
            ->join('usuarios b', 'b.id = a.id_cuidador')
            ->join('ei_alocados c', 'c.id = a.id_alocado')
            ->join('ei_alocacao_escolas d', 'd.id = c.id_alocacao_escola')
            ->join('ei_alocacao e', 'e.id = d.id_alocacao')
            ->join('ei_ordem_servico e2', 'e2.nome = d.ordem_servico AND e2.ano = e.ano AND e2.semestre = e.semestre')
            ->join("(SELECT j2.* FROM ei_alocados_horarios j2 GROUP BY j2.id_alocado, j2.periodo, j2.cargo{$mesCargoFuncao}, j2.funcao{$mesCargoFuncao}) j", "j.id_alocado = c.id AND j.periodo = a.periodo AND j.cargo{$mesCargoFuncao} = a.cargo{$mesCargoFuncao} AND j.funcao{$mesCargoFuncao} = a.funcao{$mesCargoFuncao}")
            ->join('ei_alocados_totalizacao i', "i.id_alocado = a.id_alocado AND i.periodo = a.periodo AND i.cargo{$mesCargoFuncao} = a.cargo{$mesCargoFuncao} AND i.funcao{$mesCargoFuncao} = a.funcao{$mesCargoFuncao} AND (i.substituicao_semestral IS NOT NULL AND a.substituicao_semestral IS NULL AND i.substituicao_eventual IS NULL)", 'left')
            ->join('ei_faturamento h', "h.id_alocacao = e.id AND h.id_escola = d.id_escola AND h.cargo = j.cargo{$mesCargoFuncao} AND h.funcao = j.funcao{$mesCargoFuncao}", 'left')
            ->where_in('e.id', explode(',', $data->id) + [0])
            ->where('a.substituicao_eventual IS NULL')
            ->group_start()
            ->where('j.data_inicio_real <=', $dataTerminoMes)
            ->or_where('j.data_inicio_real', null)
            ->group_end()
            ->group_start()
            ->where('j.data_termino_real >=', $dataInicioMes)
            ->or_where('j.data_termino_real', null)
            ->group_end()
            ->group_by(['d.id_escola', 'a.id_cuidador', 'j.periodo', 'j.cargo' . $mesCargoFuncao, 'j.funcao' . $mesCargoFuncao, 'c.id', 'a.periodo'])
            ->order_by('j.funcao' . $mesCargoFuncao, 'asc')
            ->get_compiled_select('ei_alocados_totalizacao a');

        if ($recuperar) {
            $sql = "SELECT t.id, 
                       s.id_alocacao,
                       s.cargo, 
                       s.funcao, 
                       FORMAT(s.valor_hora_funcao, 2, 'de_DE') AS valor_hora,
                       NULL AS total_horas_mes,
                       SUM(s.total_segundos_mes{$idMes}) AS total_segundos_mes,
                       FORMAT(s.valor_hora_funcao * (SUM(s.total_segundos_mes{$idMes}) / 3600), 2, 'de_DE') AS valor_faturado,
                       s.valor_hora_funcao * (SUM(s.total_segundos_mes{$idMes}) / 3600) AS valor_total_individual
                FROM ({$subquery}) s
                LEFT JOIN ei_faturamento_consolidado t ON
                          t.id_alocacao = s.id_alocacao AND t.cargo = s.cargo AND t.funcao = s.funcao
                GROUP BY s.cargo, s.funcao";
        } else {
            $sql = "SELECT t.id, 
                       s.id_alocacao,
                       s.cargo, 
                       s.funcao, 
                       FORMAT(IFNULL(t.valor_hora_mes{$idMes}, s.valor_hora_funcao), 2, 'de_DE') AS valor_hora,
                       t.total_horas_mes{$idMes} AS total_horas_mes,
                       SUM(s.total_segundos_mes{$idMes}) AS total_segundos_mes,
                       FORMAT(IFNULL(t.valor_faturado_mes{$idMes}, s.valor_hora_funcao * (SUM(s.total_segundos_mes{$idMes}) / 3600)), 2, 'de_DE') AS valor_faturado,
                       IFNULL(t.valor_faturado_mes{$idMes}, s.valor_hora_funcao * (SUM(s.total_segundos_mes{$idMes}) / 3600)) AS valor_total_individual
                FROM ({$subquery}) s
                LEFT JOIN ei_faturamento_consolidado t ON
                          t.id_alocacao = s.id_alocacao AND t.cargo = s.cargo AND t.funcao = s.funcao
                GROUP BY s.cargo, s.funcao";
        }
        $alocados = $this->db->query($sql)->result();

        // carrega helpers de data e hora
        $this->load->helper('time');
        $this->load->library('Calendar');

        // formata os valores de input
        if ($recuperar) {
            $totalHoras = $alocacao->{'total_horas_mes' . $idMes} ?? null;
            $valorFaturado = $alocacao->{'valor_faturado_mes' . $idMes} ?? null;
        } else {
            $totalHoras = null;
            $valorFaturado = null;
        }
        if (is_null($totalHoras) and is_null($valorFaturado)) {
            foreach ($alocados as $alocado) {
                $valorFaturado += round($alocado->valor_total_individual, 2);
                $totalHoras += $alocado->total_horas_mes ? timeToSec($alocado->total_horas_mes) : $alocado->total_segundos_mes;
            }
        }

        // retorna conjunto de dados para a view da planilha
        $planilha = [
            'empresa' => $empresa,
            'usuario' => $usuario,
            'mesAtual' => $this->calendar->get_month_name(date('m')),
            'query_string' => "depto={$depto}&diretoria={$idDiretoria}&supervisor={$idSupervisor}&mes={$mes}&ano={$ano}&semestre={$semestre}",
            'is_pdf' => $is_pdf,
            'id_medicao_mensal' => $data->id_medicao_mensal,
            'diretoria' => $data->diretoria,
            'contratos' => $data->contratos,
            'ordensServico' => $data->ordens_servico,
            'mesAno' => ucfirst($this->calendar->get_month_name($mes)) . '/' . $ano,
            'observacoes' => $data->observacoes,
            'alocados' => $alocados,
            'valor_hora' => $data->valor_hora,
            'total_horas' => secToTime($totalHoras, false),
            'valor_faturado' => number_format($valorFaturado, 2, ',', '.'),
            'totalEscolas' => $data->total_escolas ?? null,
            'totalAlunos' => $data->total_alunos ?? null,
            'totalProfissionais' => $data->total_profissionais ?? null,
        ];

        return $this->load->view('ei/planilha_faturamento_consolidado', $planilha, true);
    }

    //--------------------------------------------------------------------

    public function ajax_list()
    {
        $post = $this->input->post();
        $funcao = $post['funcao'] ?? null;

        parse_str($this->input->post('busca'), $busca);
        $semestre = $busca['semestre'] ?? null;
        if (empty($semestre)) {
            $semestre = intval($busca['mes']) > 7 ? '2' : '1';
        }
        $idMes = intval($busca['mes']) - ($semestre === '2' ? 6 : 0);
        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';

        $dataInicioMes = "{$busca['ano']}-{$busca['mes']}-01";
        $dataTerminoMes = date('Y-m-t', strtotime($dataInicioMes));

        $qb = $this->db
            ->select('c.municipio, c.escola, c.codigo, c.ordem_servico, b.total_dias_letivos, a.periodo')
            ->select("DATE_FORMAT(a.dia_semana, '%a') AS semana", false)
            ->select("(CASE a.periodo WHEN 0 THEN 'Madrugada' WHEN 1 THEN 'Manhã' WHEN 2 THEN 'Tarde' WHEN 3 THEN 'Noite' END) AS nome_periodo", false)
            ->select('a.dia_semana')
            ->select("TIME_FORMAT(a.horario_inicio_mes{$idMes}, '%H:%i') AS horario_entrada", false)
            ->select("TIME_FORMAT(a.horario_termino_mes{$idMes}, '%H:%i') AS horario_saida", false)
            ->select("TIME_FORMAT(a.total_horas_mes{$idMes}, '%H:%i') AS total_horas", false)
            ->select(["CASE WHEN MONTH(a.data_substituicao1) < '{$busca['mes']}' || MONTH(a.data_substituicao2) < '{$busca['mes']}' THEN NULL ELSE b.cuidador END AS cuidador"], false)
            ->select(["(CASE WHEN MONTH(a.data_substituicao1) <= '{$busca['mes']}' THEN e.nome END) AS cuidador_sub1"], false)
            ->select(["(CASE WHEN MONTH(a.data_substituicao2) <= '{$busca['mes']}' THEN f.nome END) AS cuidador_sub2"], false)
            ->select(["CASE WHEN MONTH(a.data_substituicao1) < '{$busca['mes']}' || MONTH(a.data_substituicao2) < '{$busca['mes']}' THEN NULL ELSE a.cargo{$mesCargoFuncao} END AS cargo"], false)
            ->select(["CASE WHEN MONTH(a.data_substituicao1) < '{$busca['mes']}' || MONTH(a.data_substituicao2) < '{$busca['mes']}' THEN NULL ELSE a.funcao{$mesCargoFuncao} END AS funcao"], false)
            ->select(["(CASE WHEN MONTH(a.data_substituicao1) <= '{$busca['mes']}' THEN a.cargo_sub1 END) AS cargo_sub1"], false)
            ->select(["(CASE WHEN MONTH(a.data_substituicao1) <= '{$busca['mes']}' THEN a.funcao_sub1 END) AS funcao_sub1"], false)
            ->select(["(CASE WHEN MONTH(a.data_substituicao2) <= '{$busca['mes']}' THEN a.cargo_sub2 END) AS cargo_sub2"], false)
            ->select(["(CASE WHEN MONTH(a.data_substituicao2) <= '{$busca['mes']}' THEN a.funcao_sub2 END) AS funcao_sub2"], false)
            ->select("a.total_semanas_mes{$idMes} AS total_semanas_mes")
            ->select("a.desconto_mes{$idMes} - IFNULL(a.endosso_mes{$idMes}, 0) * (-1) AS desconto_mes")
            ->select(["TIME_FORMAT(IFNULL(a.total_endossado_mes{$idMes}, a.total_mes{$idMes}), '%H:%i') AS total_mes"], false)
            ->select(["(SELECT TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(IFNULL(ax.total_endossado_mes{$idMes}, ax.total_mes{$idMes})))), '%H:%i') FROM ei_alocados_horarios ax WHERE ax.id_alocado = b.id AND ax.periodo = a.periodo AND ax.cargo{$mesCargoFuncao} = a.cargo{$mesCargoFuncao} AND ax.funcao{$mesCargoFuncao} = a.funcao{$mesCargoFuncao}) AS total_horas_mes"], false)
            ->select(["TIME_FORMAT(g.dias_descontados_mes{$idMes}, '%H:%i') AS dias_descontados_mes"], false)
            ->select(["TIME_FORMAT(g.horas_descontadas_mes{$idMes}, '%H:%i') AS horas_descontadas_mes"], false)
            ->select("j.data_liberacao_pagto_mes{$idMes} AS data_liberacao_pagto_mes")
            ->select(["IF(MONTH(a.data_substituicao1) = {$busca['mes']}, a.total_semanas_sub1, NULL) AS total_semanas_sub1_mes"], false)
            ->select(["IF(MONTH(a.data_substituicao1) = {$busca['mes']}, a.desconto_sub1 - IFNULL(a.endosso_sub1, 0), NULL) AS desconto_sub1_mes"], false)
            ->select("TIME_FORMAT(IF(MONTH(a.data_substituicao1) = {$busca['mes']}, IFNULL(a.total_endossado_sub1, a.total_sub1), 0), '%H:%i') AS total_sub1_mes", false)
            ->select(["(CASE WHEN MONTH(a.data_substituicao1) = '{$busca['mes']}' THEN TIME_FORMAT(g2.total_horas_mes{$idMes}, '%H:%i') END) AS total_horas_sub1_mes"], false)
            ->select(["(CASE WHEN MONTH(a.data_substituicao1) = '{$busca['mes']}' THEN TIME_FORMAT(g2.dias_descontados_mes{$idMes}, '%H:%i') END) AS dias_descontados_sub1_mes"], false)
            ->select(["(CASE WHEN MONTH(a.data_substituicao1) = '{$busca['mes']}' THEN TIME_FORMAT(g2.horas_descontadas_mes{$idMes}, '%H:%i') END) AS horas_descontadas_sub1_mes"], false)
            ->select(["IF(MONTH(a.data_substituicao2) = {$busca['mes']}, a.total_semanas_sub2, NULL) AS total_semanas_sub2_mes"], false)
            ->select(["IF(MONTH(a.data_substituicao2) = {$busca['mes']}, a.desconto_sub2  - IFNULL(a.endosso_sub2, 0), NULL) AS desconto_sub2_mes"], false)
            ->select("TIME_FORMAT(IF(MONTH(a.data_substituicao2) = {$busca['mes']}, a.total_endossado_sub2, 0), '%H:%i') AS total_sub2_mes", false)
            ->select(["(CASE WHEN MONTH(a.data_substituicao2) = '{$busca['mes']}' THEN TIME_FORMAT(ADDTIME(g3.total_horas_mes{$idMes}, IFNULL(g3.horas_descontadas_mes{$idMes}, 0)), '%H:%i') END) AS total_horas_sub2_mes"], false)
            ->select(["(CASE WHEN MONTH(a.data_substituicao1) = '{$busca['mes']}' THEN TIME_FORMAT(g3.dias_descontados_mes{$idMes}, '%H:%i') END) AS dias_descontados_sub2_mes"], false)
            ->select(["(CASE WHEN MONTH(a.data_substituicao1) = '{$busca['mes']}' THEN TIME_FORMAT(g3.horas_descontadas_mes{$idMes}, '%H:%i') END) AS horas_descontadas_sub2_mes"], false)
            ->select('a.id, b.id AS id_alocado')
            ->select(["DATE_FORMAT(k.data_aprovacao_mes{$idMes}, '%d/%m/%Y') AS data_aprovacao_mes"], false)
            ->select(['MONTH(a.data_substituicao1)' . ($semestre > 1 ? ' -6' : '') . ' AS mes_sub1'], false)
            ->select(['MONTH(a.data_substituicao2)' . ($semestre > 1 ? ' -6' : '') . ' AS mes_sub2'], false)
            ->select("GROUP_CONCAT(DISTINCT i.aluno ORDER BY i.aluno SEPARATOR ', ') AS alunos", false)
            ->select('i.data_inicio AS data_inicio_aluno_de, i.data_termino AS data_termino_aluno_de')
            ->select("DATE_FORMAT(MIN(i.data_inicio), '%d/%m/%Y') AS data_inicio_aluno", false)
            ->select("DATE_FORMAT(MAX(i.data_termino), '%d/%m/%Y') AS data_termino_aluno", false)
            ->select("IFNULL(DATE_FORMAT(IFNULL(a.data_inicio_real, MIN(i.data_inicio)), '%d/%m/%Y'), '00/00/0000') AS data_inicio_real", false)
            ->select("IFNULL(DATE_FORMAT(a.data_termino_real, '%d/%m/%Y'), '00/00/0000') AS data_termino_real", false)
            ->select("IFNULL(DATE_FORMAT(MAX(i.data_recesso), '%d/%m/%Y'), '00/00/0000') AS data_recesso_aluno", false)
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->join('usuarios e', 'e.id = a.id_cuidador_sub1', 'left')
            ->join('usuarios f', 'f.id = a.id_cuidador_sub2', 'left')
            ->join('ei_alocados_totalizacao g', "g.id_alocado = b.id AND g.periodo = a.periodo AND g.id_cuidador = b.id_cuidador AND g.cargo{$mesCargoFuncao} = a.cargo{$mesCargoFuncao} AND g.funcao{$mesCargoFuncao} = a.funcao{$mesCargoFuncao}", 'left')
            ->join('ei_alocados_totalizacao g2', "g2.id_alocado = b.id AND g2.periodo = a.periodo AND g2.id_cuidador = a.id_cuidador_sub1 AND g2.cargo{$mesCargoFuncao} = a.cargo{$mesCargoFuncao} AND g2.funcao{$mesCargoFuncao} = a.funcao{$mesCargoFuncao}", 'left')
            ->join('ei_alocados_totalizacao g3', "g3.id_alocado = b.id AND g3.periodo = a.periodo AND g3.id_cuidador = a.id_cuidador_sub2 AND g3.cargo{$mesCargoFuncao} = a.cargo{$mesCargoFuncao} AND g3.funcao{$mesCargoFuncao} = a.funcao{$mesCargoFuncao}", 'left')
            ->join('ei_pagamento_prestador j', 'j.id_alocacao = d.id AND j.id_cuidador = b.id_cuidador AND nota_complementar IS NULL', 'left')
            ->join('ei_faturamento k', "k.id_alocacao = d.id AND k.id_escola = c.id_escola AND k.cargo = a.cargo{$mesCargoFuncao} AND k.funcao = a.funcao{$mesCargoFuncao}", 'left')
            ->join('ei_matriculados_turmas h', 'h.id_alocado_horario = a.id', 'left')
            ->join('ei_matriculados i', 'i.id = h.id_matriculado AND i.id_alocacao_escola = c.id', 'left')
            ->where('d.id_empresa', $this->session->userdata('empresa'))
            ->where('d.depto', $busca['depto'])
            ->where('d.id_diretoria', $busca['diretoria'])
            ->where('d.id_supervisor', $busca['supervisor'])
            ->where('d.ano', $busca['ano'])
            ->where('d.semestre', $semestre);
        if (!empty($funcao)) {
            $qb->where('a.funcao' . $mesCargoFuncao, $funcao);
        }
        $query = $qb
            ->group_start()
            ->where('a.data_inicio_real <=', $dataTerminoMes)
            ->or_where('a.data_inicio_real', null)
            ->group_end()
            ->group_start()
            ->where('a.data_termino_real >=', $dataInicioMes)
            ->or_where('a.data_termino_real', null)
            ->group_end()
            ->group_by(['c.ordem_servico', 'c.municipio', 'c.escola', 'a.periodo', 'a.dia_semana', 'a.id', 'e.id', 'f.id'])
            ->order_by('c.codigo', 'asc')
            ->order_by('c.municipio', 'asc')
            ->order_by('c.escola', 'asc')
            ->order_by('c.ordem_servico', 'asc')
            ->order_by('b.cuidador', 'asc')
            ->order_by('alunos', 'asc')
            ->order_by('a.periodo', 'asc')
            ->order_by('a.dia_semana', 'asc')
            ->order_by("a.horario_inicio_mes{$idMes}", 'asc')
            ->get('ei_alocados_horarios a');

        $options = [
            'search' => ['municipio', 'escola', 'alunos', 'cuidador', 'cuidador_sub1', 'cuidador_sub2']
        ];
        $this->load->library('dataTables', $options);

        $output = $this->datatables->generate($query);

        $this->load->library('Calendar');
        $dias_semana = $this->calendar->get_day_names('long');
        $semestres = [];
        $mesInicial = $semestre === '2' ? 7 : 1;
        $mesFinal = $semestre === '2' ? 12 : 7;
        $mesAno = [];
        for ($i = $mesInicial; $i <= $mesFinal; $i++) {
            $semestres[] = ucfirst($this->calendar->get_month_name($busca['mes']));
            $mesAno[] = date('F Y', strtotime('01-' . $busca['mes'] . '-' . $busca['ano']));
        }

        $output->semestre = $semestres;

        $funcoes = $this->db
            ->select("a.cargo{$mesCargoFuncao} AS cargo, a.funcao{$mesCargoFuncao} AS funcao")
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->where('d.id_empresa', $this->session->userdata('empresa'))
            ->where('d.depto', $busca['depto'])
            ->where('d.id_diretoria', $busca['diretoria'])
            ->where('d.id_supervisor', $busca['supervisor'])
            ->where('d.ano', $busca['ano'])
            ->where('d.semestre', $semestre)
            ->group_start()
            ->where('a.data_inicio_real <=', $dataTerminoMes)
            ->or_where('a.data_inicio_real', null)
            ->group_end()
            ->group_start()
            ->where('a.data_termino_real >=', $dataInicioMes)
            ->or_where('a.data_termino_real', null)
            ->group_end()
            ->group_by(['a.cargo' . $mesCargoFuncao, 'a.funcao' . $mesCargoFuncao])
            ->order_by('a.cargo' . $mesCargoFuncao, 'asc')
            ->order_by('a.funcao' . $mesCargoFuncao, 'asc')
            ->get('ei_alocados_horarios a')
            ->result_array();

        $funcoes = ['' => 'Todas'] + array_column($funcoes, 'funcao', 'funcao');
        $output->funcoes = form_dropdown('', $funcoes, $funcao);

        $data = [];

        foreach ($output->data as $row) {

            if (strlen($row->data_inicio_aluno) > 0 and strlen($row->data_termino_aluno) > 0) {
                if (strtotime($row->data_termino_aluno_de) < strtotime($row->data_inicio_aluno_de)) {
                    $dataInicioAluno = '<span style="background-color: #FF0;">' . $row->data_inicio_aluno . '</span>';
                    $dataTerminoAluno = '<span style="background-color: #FF0;">' . $row->data_termino_aluno . '</span>';
                } else {
                    $dataInicioAluno = $row->data_inicio_aluno;
                    $dataTerminoAluno = $row->data_termino_aluno;
                }
            } else {
                $dataInicioAluno = strlen($row->data_inicio_aluno) > 0 ? $row->data_inicio_aluno : '<span style="background-color: #FF0;">XX:XX:XXXX</span>';
                $dataTerminoAluno = strlen($row->data_termino_aluno) > 0 ? $row->data_termino_aluno : '<span style="background-color: #FF0;">XX:XX:XXXX</span>';
            }

            $codigoMunicipio = implode(' - ', [$row->codigo, $row->municipio]);
            $cargo = $row->cargo_sub2 ?? $row->cargo_sub1 ?? $row->cargo;
            $funcao = $row->funcao_sub2 ?? $row->funcao_sub1 ?? $row->funcao;

            $data[] = [
                "<strong>Municipio:</strong> {$codigoMunicipio}&emsp;
                <strong>Escola:</strong> {$row->escola}&emsp;
                <strong>Ordem de serviço:</strong> {$row->ordem_servico}<br>
                <strong>Aluno(s):</strong> {$row->alunos} - {$row->nome_periodo}&emsp;
                <strong>Data início (projetada):</strong> {$dataInicioAluno}&emsp;
                <strong>Data início (real):</strong> {$row->data_inicio_real}&emsp;&emsp;&emsp;&emsp;&emsp;
                <strong>Data término (projetada):</strong> {$dataTerminoAluno}&emsp;
                <strong>Data término (real):</strong> {$row->data_termino_real}<br>
                <button type='button' class='btn btn-xs btn-success btnFecharMes' onclick='fechar_mes($row->id_alocado, $row->periodo, \"$row->cargo\", \"$row->funcao\")'>1 - Fechar mês</button>
                <button type='button' class='btn btn-xs btn-success btnTotalizarMes' onclick='totalizar_mes($row->id_alocado, $row->periodo, \"$row->cargo\", \"$row->funcao\")'>2 - Totalizar mês</button>
                <button type='button' class='btn btn-xs btn-info btnIngresso' onclick='edit_data_real_totalizacao($row->id_alocado, $row->periodo, \"$cargo\", \"$funcao\", 0)'>Editar data início (real)</button>
                <button type='button' class='btn btn-xs btn-info btnRecesso' onclick='edit_data_real_totalizacao($row->id_alocado, $row->periodo, \"$cargo\", \"$funcao\", 1)'>Editar data término (real)</button>
                <button type='button' class='btn btn-xs btn-danger' onclick='edit_desalocacao($row->id_alocado, $row->periodo, \"$cargo\", \"$funcao\")'>Desalocar...</button>",

                // 1---------------------------------------------------

                $dias_semana[$row->dia_semana],
                strlen($row->total_semanas_mes) > 0 ? implode(' às ', array_filter([$row->horario_entrada, $row->horario_saida])) : null,
                $row->total_horas,
                implode(';<br>', array_filter([$row->cuidador, $row->cuidador_sub1, $row->cuidador_sub2])),
                implode(';<br>', array_filter([$row->funcao, $row->funcao_sub1, $row->funcao_sub2])),

                // 6---------------------------------------------------

                $row->total_semanas_mes,
                $row->desconto_mes ? str_replace('.', ',', round($row->desconto_mes, 2)) : null,
                $row->total_mes,
                $row->total_horas_mes,
                $row->dias_descontados_mes,
                $row->horas_descontadas_mes,#10 > 11
                $row->data_liberacao_pagto_mes,

                $row->total_semanas_sub1_mes,
                $row->desconto_sub1_mes ? str_replace('.', ',', round($row->desconto_sub1_mes, 2)) : null,
                $row->total_sub1_mes,
                $row->total_horas_sub1_mes,
                $row->dias_descontados_sub1_mes,
                $row->horas_descontadas_sub1_mes,#16 > 18
                $row->data_liberacao_pagto_mes,

                $row->total_semanas_sub2_mes,
                $row->desconto_sub2_mes ? str_replace('.', ',', round($row->desconto_sub2_mes, 2)) : null,
                $row->total_sub2_mes,
                $row->total_horas_sub2_mes,
                $row->dias_descontados_sub2_mes,
                $row->horas_descontadas_sub2_mes,#22 > 25
                $row->data_liberacao_pagto_mes,

                // 27---------------------------------------------------

                $row->id,
                $row->id_alocado,
                $row->data_aprovacao_mes, #30
                $row->cuidador_sub1,
                $row->funcao_sub1,
                $row->mes_sub1, #33
                $row->cuidador_sub2,
                $row->funcao_sub2,
                $row->mes_sub2,
                $row->funcao,
                $row->periodo,
                $row->cargo,
            ];
        }

        $output->data = $data;

        $qb = $this->db
            ->select("IF(d.id_cuidador_sub1 IS NOT NULL, MONTH(d.data_substituicao1)" . (intval($busca['mes']) > 6 ? ' - 6' : '') . ", null) AS mes_sub1", false)
            ->select("IF(d.id_cuidador_sub2 IS NOT NULL, MONTH(d.data_substituicao2)" . (intval($busca['mes']) > 6 ? ' - 6' : '') . ", null) AS mes_sub2", false)
            ->join('ei_alocacao_escolas b', 'b.id_alocacao = a.id')
            ->join('ei_alocados c', 'c.id_alocacao_escola = b.id')
            ->join('ei_alocados_horarios d', 'd.id_alocado = c.id')
            ->where('a.id_empresa', $this->session->userdata('empresa'))
            ->where('a.depto', $busca['depto'])
            ->where('a.id_diretoria', $busca['diretoria'])
            ->where('a.id_supervisor', $busca['supervisor'])
            ->where('a.ano', $busca['ano'])
            ->where('a.semestre', $semestre);
        if ($post['search']['value']) {
            $qb->like('b.municipio', $post['search']['value']);
        }
        if ($post['length'] > 0) {
            $qb->limit($post['length'], $post['start']);
        }
        $rowSubstituicaoMes = $qb
            ->get('ei_alocacao a')
            ->result();

        $mes_sub1 = array_filter(array_column($rowSubstituicaoMes, 'mes_sub1'));
        $mes_sub2 = array_filter(array_column($rowSubstituicaoMes, 'mes_sub2'));

        for ($i = 1; $i <= 7; $i++) {
            $substituicaoMes['mes' . $i] = [
                !(isset($mes_sub1[$i]) and isset($mes_sub2[$i])),
                isset($mes_sub1[$i]),
                isset($mes_sub2[$i]),
            ];
        }

        $output->substituicaoMes = $substituicaoMes;

        $output->mes = $busca['mes'];
        $output->fechamentoMes = boolval(array_filter(array_column($data, 7), function ($v, $k) {
            return strlen($v) > 0;
        }, ARRAY_FILTER_USE_BOTH));
        $output->totalizacaoMes = boolval(array_filter(array_column($data, 8), function ($v, $k) {
            return strlen($v) > 0;
        }, ARRAY_FILTER_USE_BOTH));

        echo json_encode($output);
    }

    //--------------------------------------------------------------------

    public function ajax_edit_data_real()
    {
        $idAlocado = $this->input->post('id_alocado');
        $periodo = $this->input->post('periodo');
        $cargo = $this->input->post('cargo');
        $funcao = $this->input->post('funcao');
        $idMes = $this->getIdMes($this->input->post('mes'), $this->input->post('semestre'));
        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';

        $data = $this->db
            ->select('f.semestre')
            ->select(["DATE_FORMAT(IFNULL(data_inicio_real, MIN(a.data_inicio)), '%d/%m/%Y') AS data_inicio_real"], false)
            ->select(["IFNULL(DATE_FORMAT(data_termino_real, '%d/%m/%Y'), '00/00/0000') AS data_termino_real"], false)
            ->join('ei_matriculados_turmas b', 'b.id_matriculado = a.id')
            ->join('ei_alocados_horarios c', 'c.id = b.id_alocado_horario')
            ->join('ei_alocados d', 'd.id = c.id_alocado AND d.id_alocacao_escola = a.id_alocacao_escola')
            ->join('ei_alocacao_escolas e', 'e.id = d.id_alocacao_escola')
            ->join('ei_alocacao f', 'f.id = e.id_alocacao')
            ->where('d.id', $idAlocado)
            ->where('c.periodo', $periodo)
            ->group_start()
            ->where("(c.cargo{$mesCargoFuncao} = '{$cargo}' AND c.funcao{$mesCargoFuncao} = '{$funcao}')")
            ->or_where("(c.cargo_sub1 = '{$cargo}' AND c.funcao_sub1 = '{$funcao}')")
            ->or_where("(c.cargo_sub2 = '{$cargo}' AND c.funcao_sub2 = '{$funcao}')")
            ->group_end()
            ->group_by('d.id')
            ->get('ei_matriculados a')
            ->row();

        if (empty($data)) {
            exit(json_encode(['erro' => 'Nenhum aluno alocado.']));
        }

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_save_data_real()
    {
        $postDataReal = $this->input->post('data_real_totalizacao');
        $dataReal = date('Y-m-d', strtotime(str_replace('/', '-', $postDataReal)));
        $fechamento = $this->input->post('fechamento');
        if ($dataReal !== preg_replace('/(\d+)\/(\d+)\/(\d+)/', '$3-$2-$1', $dataReal)) {
            if ($fechamento) {
                exit(json_encode(['erro' => 'A data de término real do semestre é inválida']));
            }
            exit(json_encode(['erro' => 'A data de início real do semestre é inválida']));
        }

        $semestre = $this->input->post('semestre');
        $idAlocado = $this->input->post('id_alocado');
        $periodo = $this->input->post('periodo');
        $cargo = $this->input->post('cargo');
        $funcao = $this->input->post('funcao');
        $idMes = $this->getIdMes($this->input->post('mes'), $semestre);
        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';

        $horarios = $this->db
            ->select('d.ano, IFNULL(a.data_inicio_real, MIN(data_inicio)) AS data_inicio', false)
            ->select('IFNULL(a.data_termino_real, MAX(data_termino)) AS data_termino', false)
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->join('ei_matriculados_turmas e', 'e.id_alocado_horario = a.id', 'left')
            ->join('ei_matriculados f', 'f.id = e.id_matriculado AND f.id_alocacao_escola = c.id', 'left')
            ->where('a.id_alocado', $idAlocado)
            ->where('a.periodo', $periodo)
            ->group_start()
            ->where("(a.cargo{$mesCargoFuncao} = '{$cargo}' AND a.funcao{$mesCargoFuncao} = '{$funcao}')")
            ->or_where("(a.cargo_sub1 = '{$cargo}' AND a.funcao_sub1 = '{$funcao}')")
            ->or_where("(a.cargo_sub2 = '{$cargo}' AND a.funcao_sub2 = '{$funcao}')")
            ->group_end()
            ->group_by('a.id_alocado')
            ->get('ei_alocados_horarios a')
            ->row();

        $dataInicioReal = $fechamento ? $horarios->data_inicio : $dataReal;
        $mesInicioReal = (int)date('m', strtotime($dataInicioReal));
        $dataTerminoReal = $fechamento ? $dataReal : $horarios->data_termino;
        $mesTerminoReal = (int)date('m', strtotime($dataTerminoReal));
        $mesCorrente = $semestre === '2' ? 7 : 1;

        $qb = $this->db
            ->set('data_inicio_real', $dataInicioReal)
            ->set('data_termino_real', $dataTerminoReal);
        for ($i = 1; $i <= 7; $i++) {
            $dataInicioMes = date('Y-m-d', mktime(0, 0, 0, $mesCorrente, 1, (int)$horarios->ano));
            $dataFimMes = date('Y-m-t', strtotime($dataInicioMes));

            if ($mesCorrente >= $mesInicioReal and $mesCorrente <= $mesTerminoReal) {
                if ($mesCorrente === $mesInicioReal) {
                    $dataInicioMes = $dataInicioReal;
                }
                if ($mesCorrente === $mesTerminoReal) {
                    $dataFimMes = $dataTerminoReal;
                }
                $qb->set("total_semanas_mes{$i}", "WEEK(DATE_SUB('{$dataFimMes}', INTERVAL ((7 + DATE_FORMAT('{$dataFimMes}', '%w') - dia_semana) % 7) DAY)) - WEEK(DATE_ADD('{$dataInicioMes}', INTERVAL ((7 - DATE_FORMAT('{$dataInicioMes}', '%w') + dia_semana) % 7) DAY)) + 1", false);
            } else {
                $qb->set("total_semanas_mes{$i}", 0);
            }

            $mesCorrente++;
        }
        $status = $qb
            ->where('id_alocado', $idAlocado)
            ->where('periodo', $periodo)
            ->where('cargo' . $mesCargoFuncao, $cargo)
            ->where('funcao' . $mesCargoFuncao, $funcao)
            ->update('ei_alocados_horarios');

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_save_data_real_totalizacoes()
    {
        $data = $this->db
            ->select('a.id')
            ->select('MIN(f.data_inicio) AS data_inicio_real', false)
            ->select('MAX(f.data_termino) AS data_termino_real', false)
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->join('ei_matriculados_turmas e', 'e.id_alocado_horario = d.id', 'left')
            ->join('ei_matriculados f', 'f.id = e.id_matriculado AND f.id_alocacao_escola = c.id', 'left')
            ->where('c.id_empresa', $this->session->userdata('empresa'))
            ->where('c.depto', $this->input->post('depto'))
            ->where('c.id_diretoria', $this->input->post('diretoria'))
            ->where('c.id_supervisor', $this->input->post('supervisor'))
            ->where('c.ano', $this->input->post('ano'))
            ->where('c.semestre', $this->input->post('semestre'))
            ->where('(a.data_inicio_real IS NULL OR a.data_termino_real IS NULL)', null, false)
            ->get('ei_alocados_horarios a')
            ->result();

        $status = true;
        if ($data) {
            $status = $this->db
                ->set($data)
                ->where_in(array_column($data, 'id'))
                ->update('ei_alocados_horarios');
        }

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function preparar_desalocacao()
    {
        $idAlocado = $this->input->post('id_alocado');
        $periodo = $this->input->post('periodo');
        $cargo = $this->input->post('cargo');
        $funcao = $this->input->post('funcao');
        $idMes = $this->getIdMes($this->input->post('mes'), $this->input->post('semestre'));
        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';

        $data = $this->db
            ->select('c.escola, a.periodo, a.dia_semana')
            ->select("GROUP_CONCAT(DISTINCT e.aluno ORDER BY e.aluno SEPARATOR ', ') AS alunos", false)
            ->select("(CASE a.periodo WHEN 0 THEN 'madrugada' WHEN 1 THEN 'manhã' WHEN 2 THEN 'tarde' WHEN 3 THEN 'noite' END) AS periodo", false)
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_matriculados_turmas d', 'd.id_alocado_horario = a.id', 'left')
            ->join('ei_matriculados e', 'e.id = d.id_matriculado AND e.id_alocacao_escola = c.id', 'left')
            ->where('b.id', $idAlocado)
            ->where('a.periodo', $periodo)
            ->where('a.cargo' . $mesCargoFuncao, $cargo)
            ->where('a.funcao' . $mesCargoFuncao, $funcao)
            ->group_by('c.id')
            ->get('ei_alocados_horarios a')
            ->row();

        if (empty($data)) {
            exit(json_encode(['erro' => 'Nenhum dado alocado encontrado.']));
        }

        $alunos = $this->db
            ->select('e.id_aluno, e.aluno')
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_matriculados_turmas d', 'd.id_alocado_horario = a.id', 'left')
            ->join('ei_matriculados e', 'e.id = d.id_matriculado AND e.id_alocacao_escola = c.id', 'left')
            ->where('b.id', $idAlocado)
            ->where('a.periodo', $periodo)
            ->where('a.cargo' . $mesCargoFuncao, $cargo)
            ->where('a.funcao' . $mesCargoFuncao, $funcao)
            ->group_by('e.id_aluno')
            ->order_by('e.aluno', 'asc')
            ->get('ei_alocados_horarios a')
            ->result_array();

        $alunos = array_filter(array_column($alunos, 'aluno', 'id_aluno'));
        $alunos = empty($alunos) ? ['' => 'Nenhum'] : ['*' => 'Todos'] + $alunos;

        $data->alunos = form_dropdown('', $alunos, empty($alunos) ? '' : '*');

        $this->load->model('ei_alocado_horario_model', 'horario');

        $horarios = $this->horario
            ->select('dia_semana')
            ->where('id_alocado', $idAlocado)
            ->group_by('dia_semana')
            ->findAll();

        $periodos = $this->horario::PERIODOS;
        $diasSemana = $this->horario::DIAS_SEMANA_POR_EXTENSO;
        $diasSemana = ['' => 'Todos'] + array_intersect_key($diasSemana, array_column($horarios, 'dia_semana', 'dia_semana'));

        $data->nome_periodo = $periodos[$periodo];
        $data->dias_semana = form_dropdown('', $diasSemana);

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_delete_alocados()
    {
        $tipo = $this->input->post('tipo');

        if (empty($tipo)) {
            exit(json_encode(['erro' => 'O tipo de dado é obrigatório.']));
        } elseif (in_array($tipo, ['1', '2']) == false) {
            exit(json_encode(['erro' => 'O tipo de dado é inválido.']));
        }

        $id = $this->input->post('id_alocado');
        $periodo = $this->input->post('periodo');
        $diaSemana = $this->input->post('dia_semana');
        $cargo = $this->input->post('cargo');
        $funcao = $this->input->post('funcao');
        $idAluno = $this->input->post('id_aluno');

        $idMes = $this->getIdMes($this->input->post('mes'), $this->input->post('semestre'));
        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';

        $this->db->trans_start();

        $alocado = $this->db
            ->select('a.cuidador, b.escola')
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->where('a.id', $id)
            ->get('ei_alocados a')
            ->row();

        $grupoApontamentoExistente = $this->db
            ->select('a.id_cuidador, b.id_escola, c.ano, c.semestre')
            ->select('GROUP_CONCAT(DISTINCT g.data) AS data', false)
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->join('ei_alocados_horarios d', 'd.id_alocado = a.id', 'left')
            ->join('ei_matriculados_turmas e', 'e.id_alocado_horario = d.id', 'left')
            ->join('ei_matriculados f', "f.id = e.id_matriculado AND (f.id_aluno = '{$idAluno}' OR CHAR_LENGTH('{$idAluno}') = 0)", 'left', false)
            ->join('ei_apontamento g', "g.id_alocado = a.id AND  DATE_FORMAT(g.data,'%w') = d.dia_semana AND (g.periodo = d.periodo OR g.periodo IS NULL)", 'left', false)
            ->where('a.id', $id)
            ->group_start()
            ->where('d.dia_semana', $diaSemana)
            ->or_where("CHAR_LENGTH('{$diaSemana}') =", 0)
            ->group_end()
            ->group_by('a.id')
            ->get('ei_alocados a')
            ->row();

        $arrPeriodos = ['Madrugada', 'Manhã', 'Tarde', 'Noite'];

        $dataLog = [
            'data' => date('Y-m-d H:i:s'),
            'id_usuario' => $this->session->userdata('id'),
            'nome_usuario' => $this->session->userdata('nome'),
            'operacao' => 'Desalocação',
            'nome_escola' => $alocado->escola,
            'id_alocado' => $id,
            'nome_cuidador' => $alocado->cuidador,
            'periodo' => $arrPeriodos[$periodo],
        ];

        if ($tipo === '2') {
            $dataLog['opcao'] = 'Aluno';

            $qb = $this->db
                ->select("b.id, b.id_alocado, d.aluno, d.id_aluno, b.periodo")
                ->select("b.cargo{$mesCargoFuncao} AS cargo, b.funcao{$mesCargoFuncao} AS cargo")
                ->join('ei_alocados_horarios b', 'b.id_alocado = a.id')
                ->join('ei_matriculados_turmas c', 'c.id_alocado_horario = b.id', 'left')
                ->join('ei_matriculados d', 'd.id = c.id_matriculado AND d.id_alocacao_escola = a.id_alocacao_escola', 'left')
                ->join('ei_matriculados e', 'e.id_alocacao_escola = d.id_alocacao_escola AND d.id_aluno != d.id_aluno', 'left')
                ->where('b.id_alocado', $id)
                ->where('b.periodo', $periodo)
                ->group_start()
                ->where('b.dia_semana', $diaSemana)
                ->or_where("CHAR_LENGTH('{$diaSemana}') =", 0)
                ->group_end()
                ->where('b.cargo' . $mesCargoFuncao, $cargo)
                ->where('b.funcao' . $mesCargoFuncao, $funcao)
                ->where('e.id', null);
            if (strlen($idAluno) > 0 and $idAluno !== '*') {
                $qb->where('d.id_aluno', $idAluno);
            } else {
                $qb->where('d.id_aluno', null);
            }
            $alocado = $qb
                ->get('ei_alocados a')
                ->result_array();

            $idAlocados1 = array_column($alocado, 'id') + [0];

            if (strlen($idAluno) > 0 and $idAluno !== '*') {
                $alocado2 = $this->db
                    ->select("b.id, b.id_alocado, d.aluno, d.id_aluno, b.periodo, b.cargo{$mesCargoFuncao} AS cargo, b.funcao{$mesCargoFuncao} AS funcao")
                    ->join('ei_alocados_horarios b', 'b.id_alocado = a.id')
                    ->join('ei_matriculados_turmas c', 'c.id_alocado_horario = b.id', 'left')
                    ->join('ei_matriculados d', 'd.id = c.id_matriculado AND d.id_alocacao_escola = a.id_alocacao_escola', 'left')
                    ->join('ei_matriculados e', 'e.id_alocacao_escola = d.id_alocacao_escola AND d.id_aluno != d.id_aluno', 'left')
                    ->where('b.id_alocado', $id)
                    ->where('b.periodo', $periodo)
                    ->group_start()
                    ->where('b.dia_semana', $diaSemana)
                    ->or_where("CHAR_LENGTH('{$diaSemana}') =", 0)
                    ->group_end()
                    ->where('b.cargo' . $mesCargoFuncao, $cargo)
                    ->where('b.funcao' . $mesCargoFuncao, $funcao)
                    ->where('e.id', null)
                    ->where('d.id_aluno !=', $idAluno)
                    ->get('ei_alocados a')
                    ->result_array();

                $idAlocados2 = array_column($alocado2, 'id');
            } else {
                $idAlocados2 = [];
            }

            $idAlocados = array_unique(array_diff($idAlocados1, $idAlocados2)) + [0];

            if ($alocado) {
                $this->db
                    ->where_in('id', $idAlocados)
                    ->delete('ei_alocados_horarios');
            }

            $alocadoExcluido = $this->db
                ->select('a.id')
                ->join('ei_alocados_horarios b', "b.id_alocado = a.id AND (b.periodo != '{$periodo}' OR b.dia_semana != '{$diaSemana}' OR b.cargo{$mesCargoFuncao} != '{$cargo}' OR b.funcao{$mesCargoFuncao} != '{$funcao}')", 'left')
                ->where('a.id', $id)
                ->where('b.id', null)
                ->get('ei_alocados a')
                ->row();

            if ($alocadoExcluido) {
                $this->db->delete('ei_alocados', ['id' => $alocadoExcluido->id]);
            } else {
                $this->db
                    ->where_in('id_alocado', $idAlocados)
                    ->where('periodo', $periodo)
                    ->group_start()
                    ->where("DATE_FORMAT(data, '%w') =", $diaSemana)
                    ->or_where("CHAR_LENGTH('{$diaSemana}') =", 0)
                    ->group_end()
                    ->delete('ei_apontamento');
            }

            $alunos = $this->db
                ->select('e.id_aluno, e.aluno')
                ->join('ei_alocados b', 'b.id = a.id_alocado')
                ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
                ->join('ei_matriculados_turmas d', 'd.id_alocado_horario = a.id', 'left')
                ->join('ei_matriculados e', 'e.id = d.id_matriculado AND e.id_alocacao_escola = c.id', 'left')
                ->where('b.id', $id)
                ->where('a.periodo', $periodo)
                ->group_start()
                ->where('a.dia_semana', $diaSemana)
                ->or_where("CHAR_LENGTH('{$diaSemana}') =", 0)
                ->group_end()
                ->where('a.cargo' . $mesCargoFuncao, $cargo)
                ->where('a.funcao' . $mesCargoFuncao, $funcao)
                ->group_by('e.id_aluno')
                ->order_by('e.aluno', 'asc')
                ->get('ei_alocados_horarios a')
                ->result_array();

            $alunos = array_filter(array_column($alunos, 'id_aluno')) + [0];

            $alunos1 = $this->db
                ->select('a.id')
                ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
                ->join('ei_alocados c', 'c.id_alocacao_escola = b.id')
                ->join('ei_matriculados_turmas d', 'd.id_matriculado = a.id', 'left')
                ->join('ei_alocados_horarios e', "e.id = d.id_alocado_horario AND e.id_alocado = c.id AND (e.periodo != '{$periodo}' OR e.dia_semana != '{$diaSemana}' OR e.cargo{$mesCargoFuncao} != '{$cargo}' OR e.funcao{$mesCargoFuncao} != '{$funcao}')", 'left')
                ->where('a.id_aluno', $idAluno)
                ->where('c.id', $id)
                ->where('e.id', null)
                ->group_by('a.id')
                ->get('ei_matriculados a')
                ->result_array();

            $idMatriculados1 = array_column($alunos1, 'id') + [0];

            $alunos2 = $this->db
                ->select('a.id')
                ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
                ->join('ei_alocados c', 'c.id_alocacao_escola = b.id')
                ->join('ei_matriculados_turmas d', 'd.id_matriculado = a.id', 'left')
                ->join('ei_alocados_horarios e', "e.id = d.id_alocado_horario AND e.id_alocado = c.id AND (e.periodo = '{$periodo}' OR e.cargo{$mesCargoFuncao} = '{$cargo}' OR e.funcao{$mesCargoFuncao} = '{$funcao}')", 'left')
                ->where_in('a.id_aluno', $alunos)
                ->where('c.id', $id)
                ->where("(e.periodo != '{$periodo}' OR e.dia_semana != '{$diaSemana}' OR e.cargo{$mesCargoFuncao} != '{$cargo}' OR e.funcao{$mesCargoFuncao} != '{$funcao}')")
                ->group_by('a.id')
                ->get('ei_matriculados a')
                ->result_array();

            $idMatriculados2 = array_column($alunos2, 'id');

            $this->db
                ->where_in('id_alocado_horario', $idAlocados1)
                ->where_in('id_matriculado', $idMatriculados1)
                ->delete('ei_matriculados_turmas');

            $idMatriculados = array_diff($idMatriculados1, $idMatriculados2) + [0];

            $this->db
                ->where_in('id', $idMatriculados)
                ->delete('ei_matriculados');

        } else {
            $dataLog['opcao'] = 'Escola';

            $alocado = $this->db
                ->select('a.id, b.escola, a.id_cuidador, b.id_alocacao')
                ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
                ->join('ei_alocados_horarios c', 'c.id_alocado = a.id')
                ->where('a.id', $id)
                ->where('c.periodo', $periodo)
                ->group_start()
                ->where('c.dia_semana', $diaSemana)
                ->or_where("CHAR_LENGTH('{$diaSemana}') =", 0)
                ->group_end()
                ->where('c.cargo' . $mesCargoFuncao, $cargo)
                ->where('c.funcao' . $mesCargoFuncao, $funcao)
                ->get('ei_alocados a')
                ->result_array();

            $escolas = array_column($alocado, 'escola') + [0];

            $this->db
                ->where_in('escola', $escolas)
                ->delete('ei_alocacao_escolas');

            $this->db
                ->where_in('id_alocacao', array_column($alocado, 'id_alocacao') + [0])
                ->where_in('id_cuidador', array_column($alocado, 'id_cuidador') + [0])
                ->delete('ei_pagamento_prestador');
        }

        $apontamentoMeses = '1 AND 7)';
        if ($grupoApontamentoExistente->semestre > 1) {
            $apontamentoMeses = '7 AND 12)';
        }

        $this->db
            ->where('id_usuario', $grupoApontamentoExistente->id_cuidador)
            ->where('YEAR(data_evento)', $grupoApontamentoExistente->ano)
            ->where('(MONTH(data_evento) BETWEEN ' . $apontamentoMeses)
            ->group_start()
            ->where('id_escola', $grupoApontamentoExistente->id_escola)
            ->or_where('id_escola', null)
            ->group_end()
            ->group_start()
            ->where_in('data_evento', explode(',', $grupoApontamentoExistente->data) + [0])
            ->or_where("CHAR_LENGTH('{$grupoApontamentoExistente->data}') =", 0)
            ->group_end()
            ->delete('ei_usuarios_frequencias');

        $this->salvarLogDesalocacao($dataLog);

        $this->db->trans_complete();

        $status = $this->db->trans_status();

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    private function salvarLogDesalocacao($dataLog)
    {
        $this->db->insert('ei_log_desalocacao', $dataLog);
    }

    //--------------------------------------------------------------------

    public function ajax_edit_cargo_funcao()
    {
        $horario = $this->db
            ->select('a.*, semestre')
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->where('a.id', $this->input->post('id_horario'))
            ->get('ei_alocados_horarios a')
            ->row();

        if (empty((array)$horario)) {
            exit(json_encode(['erro' => 'Registro não encontrado.']));
        }

        $mesReferencia = $this->input->post('mes');
        $idMes = $mesReferencia - ($horario->semestre > 1 ? 6 : 0);
        $idMesCargoFuncao = $idMes > 1 ? '_mes' . $idMes : '';

        $data = $this->montarCargosFuncoes($horario->{'cargo' . $idMesCargoFuncao}, $horario->{'funcao' . $idMesCargoFuncao});
        $data['id'] = $horario->id;
        $data['semestre'] = $horario->semestre;

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_filtrar_cargo_funcao()
    {
        $data = $this->montarCargosFuncoes($this->input->post('cargo'));

        echo json_encode(['funcoes' => $data['funcoes']]);
    }

    //--------------------------------------------------------------------

    public function montarCargosFuncoes($nomeCargo = '', $nomeFuncao = ''): array
    {
        $cargos = $this->db
            ->where('id_empresa', $this->session->userdata('empresa'))
            ->order_by('nome')
            ->get('empresa_cargos')
            ->result_array();

        $cargos = ['' => 'selecione...'] + array_column($cargos, 'nome', 'nome');

        $funcoes = $this->db
            ->select('a.id, a.nome')
            ->join('empresa_cargos b', 'b.id = a.id_cargo')
            ->where('b.id_empresa', $this->session->userdata('empresa'))
            ->where('b.nome', $nomeCargo)
            ->order_by('a.nome')
            ->get('empresa_funcoes a')
            ->result_array();

        $funcoes = ['' => 'selecione...'] + array_column($funcoes, 'nome', 'nome');

        return [
            'cargos' => form_dropdown('', $cargos, $nomeCargo),
            'funcoes' => form_dropdown('', $funcoes, $nomeFuncao),
        ];
    }

    //--------------------------------------------------------------------

    public function ajax_edit_horario()
    {
        $data = $this->db
            ->select('a.id, a.dia_semana, b.cuidador, c.escola, c.municipio, d.ano, d.semestre')
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->where('a.id', $this->input->post('id_horario'))
            ->get('ei_alocados_horarios a')
            ->row();

        if (empty($data)) {
            exit(json_encode(['erro' => 'O horário alocado não existe ou foi desalocado do semestre.']));
        }

        $mes = str_pad($this->input->post('mes'), 2, '0', STR_PAD_LEFT);
        $idMes = (int)$mes - ($data->semestre > 1 ? 6 : 0);

        $horario = $this->db
            ->select(["TIME_FORMAT(horario_inicio_mes{$idMes}, '%H:%i') AS horario_inicio"], false)
            ->select(["TIME_FORMAT(horario_termino_mes{$idMes}, '%H:%i') AS horario_termino"], false)
            ->where('id', $data->id)
            ->get('ei_alocados_horarios')
            ->row();

        $data->horario_inicio = $horario->horario_inicio;
        $data->horario_termino = $horario->horario_termino;

        $this->load->library('calendar');
        $nomeMes = $this->calendar->get_month_name($mes);
        $diasSemana = $this->calendar->get_day_names('long');
        $data->mes_ano = ucfirst($nomeMes) . '/' . $data->ano;
        $data->horario_semana = $diasSemana[$data->dia_semana] . ', ' . $data->horario_inicio . ' às ' . $data->horario_termino;

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_save_horario()
    {
        $id = $this->input->post('id');

        $horario = $this->db
            ->select('a.id, d.ano, a.dia_semana, a.periodo, b.id_cuidador, c.id_escola')
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->where('a.id', $id)
            ->get('ei_alocados_horarios a')
            ->row();

        if (empty($horario)) {
            exit(json_encode(['erro' => 'Horários ão encontrado.']));
        }

        $ano = $horario->ano;
        $periodo = $horario->periodo;
        $semana = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'][$horario->dia_semana];
        $semestre = $this->input->post('semestre');
        $idMes = intval($this->input->post('mes')) - ($semestre > 1 ? 6 : 0);

        $horarioInicio = $this->input->post('horario_inicio');

        $horarioTermino = $this->input->post('horario_termino');

        if ($horarioInicio xor $horarioTermino) {
            exit(json_encode(['erro' => 'Os horários devem estar ambos vazios ou ambos preenchidos.']));
        }

        if (strtotime($horarioInicio) > strtotime($horarioTermino)) {
            exit(json_encode(['erro' => 'O horário de saída deve ser maior do que o horário de entrada.']));
        }

        $totalSemanasMes = 0;
        $descontosMes = 0;
        $endossosMes = 0;

        $meses = range($idMes, $semestre > 1 ? 6 : 7);

        $this->db->trans_start();

        foreach ($meses as $mes) {
            if (strtotime($horarioTermino) > strtotime($horarioInicio)) {
                $anoMes = "{$ano}-{$mes}";
                $timestamp = strtotime($anoMes . '-01');
                $dataAbertura = date('Y-m-d', $timestamp);
                $dataFechamento = date('Y-m-t', $timestamp);
                $diaPrimeiroDoProximoMes = date('Y-m-d', strtotime('+1 month', $timestamp));

                $nomeMes = date('F', $timestamp);
                $semanaInicial = date('W', strtotime("first {$semana} of {$nomeMes} {$ano}"));
                $semanaFinal = date('W', strtotime("last {$semana} of {$nomeMes} {$ano} -1 week")) + 1;
                $totalSemanasMes = $semanaFinal - ($semanaInicial - 1);

                $calculoFechamento = $this->db
                    ->select(["COUNT(DISTINCT(IF(a.id_cuidador_sub1 IS NOT NULL AND DATE_FORMAT(a.data_substituicao1, '%Y-%m') < '$anoMes', 
								IF(DATE_FORMAT(d.data, '%Y-%m') = '{$anoMes}' AND d.status IN ('FA', 'PV', 'FE', 'EM', 'RE', 'EE', 'HE', 'SL'), d.id, NULL), 
								IF(c.data < IFNULL(a.data_substituicao1, '{$diaPrimeiroDoProximoMes}') AND c.status IN ('FA', 'PV', 'FE', 'EM', 'RE', 'EE'), c.id, NULL))
						)) AS desconto_mes"], false)
                    ->select(["COUNT(DISTINCT(IF(a.id_cuidador_sub1 IS NOT NULL AND DATE_FORMAT(a.data_substituicao1, '%Y-%m') < '$anoMes', 
								IF(DATE_FORMAT(d.data, '%Y-%m') = '{$anoMes}' AND d.status IN ('FA', 'PV', 'FE', 'EM', 'RE', 'EE', 'HE', 'SL') AND (d.desconto_sub1 OR d.desconto_sub2), d.id, NULL), 
								IF(c.data < IFNULL(a.data_substituicao1, '{$diaPrimeiroDoProximoMes}') AND c.status IN ('FA', 'PV', 'FE', 'EM', 'RE', 'EE', 'HE', 'SL') AND (c.desconto_sub1 OR c.desconto_sub2), c.id, NULL))
						)) AS endosso_mes"], false)
                    ->join('ei_alocados b', 'b.id = a.id_alocado')
                    ->join('ei_apontamento c', "c.id_alocado = b.id AND (c.periodo = a.periodo OR c.periodo IS NULL) AND (c.data BETWEEN '{$dataAbertura}' AND '{$dataFechamento}') AND DATE_FORMAT(c.data, '%w') = a.dia_semana AND (c.id_usuario IS NULL OR c.id_usuario = b.id_cuidador)", 'left')
                    ->join('ei_apontamento d', "d.id_alocado = b.id AND (d.periodo = a.periodo OR d.periodo IS NULL) AND (d.data BETWEEN '{$dataAbertura}' AND '{$dataFechamento}') AND DATE_FORMAT(d.data, '%w') = a.dia_semana AND (d.id_usuario IS NULL OR d.id_usuario = a.id_cuidador_sub1)", 'left')
                    ->where('a.id', $id)
                    ->group_by('a.id')
                    ->get('ei_alocados_horarios a')
                    ->row();

                $descontosMes = $calculoFechamento->desconto_mes;
                $endossosMes = $calculoFechamento->endosso_mes;
            }

            $this->db
                ->set('horario_inicio_mes' . $mes, $horarioInicio)
                ->set('horario_termino_mes' . $mes, $horarioTermino)
                ->set('total_semanas_mes' . $mes, $totalSemanasMes, false)
                ->set('desconto_mes' . $mes, "IF(desconto_mes{$mes} IS NULL, NULL, $descontosMes)", false)
                ->set('endosso_mes' . $mes, "IF(endosso_mes{$mes} IS NULL, NULL, $endossosMes)", false)
                ->set('total_horas_mes' . $mes, "TIMEDIFF('{$horarioTermino}', '{$horarioInicio}')", false)
                ->where('id', $id)
                ->update('ei_alocados_horarios');
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() == false) {
            exit(json_encode(['erro' => 'Não foi possível salvar o s horários.']));
        }

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    public function ajax_edit_qtde_horas()
    {
        $data = $this->db
            ->select('a.id, a.dia_semana, b.cuidador, c.escola, c.municipio, d.ano, d.semestre')
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->where('a.id', $this->input->post('id_horario'))
            ->get('ei_alocados_horarios a')
            ->row();

        if (empty($data)) {
            exit(json_encode(['erro' => 'O horário alocado não existe ou foi desalocado do semestre.']));
        }

        $data->mes = $this->input->post('mes');
        $idMes = $data->mes - ($data->semestre > 1 ? 6 : 0);
        $sub = $this->input->post('substituto');

        $horario = $this->db
            ->select("total_horas_mes{$idMes} AS total_horas_mes", false)
            ->where('id', $data->id)
            ->get('ei_alocados_horarios')
            ->row();

        $this->load->helper('time');
        $data->total_horas_mes = timeSimpleFormat($horario->total_horas_mes);

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_save_qtde_horas()
    {
        $id = $this->input->post('id');
        $mes = $this->input->post('mes');
        $semestre = $this->input->post('semestre');
        $idMes = $this->getIdMes($mes, $semestre);
        $totalHorasMes = $this->input->post('total_horas_mes');

        $horarios = $this->db
            ->select('id_alocado, dia_semana')
            ->where('id', $id)
            ->get('ei_alocados_horarios')
            ->row();

        $status = $this->db
            ->set('total_horas_mes' . $idMes, $totalHorasMes)
            ->where('id_alocado', $horarios->id_alocado)
            ->where('dia_semana', $horarios->dia_semana)
            ->update('ei_alocados_horarios');

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_edit_substituto()
    {
        $idHorario = $this->input->post('id_horario');
        $idMes = $this->getIdMes($this->input->post('mes'), $this->input->post('semestre'));
        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';

        $data = $this->db
            ->select('a.id, a.dia_semana, c.escola, b.cuidador, c.municipio, d.ano, d.semestre')
            ->select("a.cargo{$mesCargoFuncao} AS cargo, a.funcao{$mesCargoFuncao} AS funcao")
            ->select('a.id_cuidador_sub1, a.cargo_sub1, a.funcao_sub1', false)
            ->select("DATE_FORMAT(a.data_substituicao1, '%d/%m/%Y') AS data_substituicao1", false)
            ->select('a.id_cuidador_sub2, a.cargo_sub2, a.funcao_sub2', false)
            ->select("DATE_FORMAT(a.data_substituicao2, '%d/%m/%Y') AS data_substituicao2", false)
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->where('a.id', $idHorario)
            ->get('ei_alocados_horarios a')
            ->row();

        if (empty($data)) {
            exit(json_encode(['erro' => 'O colaborador não existe ou foi desalocado do semestre.']));
        }

        $mes = $this->input->post('mes');
        $idMes = (int)$mes - ($data->semestre > 1 ? 6 : 0);

        $horario = $this->db
            ->select(["TIME_FORMAT(horario_inicio_mes{$idMes}, '%H:%i') AS horario_inicio"], false)
            ->select(["TIME_FORMAT(horario_termino_mes{$idMes}, '%H:%i') AS horario_termino"], false)
            ->where('id', $idHorario)
            ->get('ei_alocados_horarios')
            ->row();

        $data->horario_inicio = $horario->horario_inicio;
        $data->horario_termino = $horario->horario_termino;

        $this->load->library('calendar');
        $mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
        $nomeMes = $this->calendar->get_month_name($mes);
        $diasSemana = $this->calendar->get_day_names('long');
        $data->mes_ano = ucfirst($nomeMes) . '/' . $data->ano;
        $data->horario_semana = $diasSemana[$data->dia_semana] . ', ' . $data->horario_inicio . ' às ' . $data->horario_termino;

        $municipios = $this->db
            ->select('municipio')
            ->where('empresa', $this->session->userdata('empresa'))
            ->where('tipo', 'funcionario')
            ->where('CHAR_LENGTH(municipio) >', 0)
            ->order_by('municipio', 'asc')
            ->get('usuarios')
            ->result();

        $municipioSub = ['' => 'Todos'] + array_column($municipios, 'municipio', 'municipio');

        $usuarios = $this->db
            ->select('a.id, a.nome')
            ->where('a.empresa', $this->session->userdata('empresa'))
            ->where('a.tipo', 'funcionario')
            ->where('a.depto', 'Educação Inclusiva')
            ->where_in('a.status', [1, 3])
            ->order_by('a.nome', 'asc')
            ->get('usuarios a')
            ->result();

        $idCuidadorSub = ['' => 'selecione...'] + array_column($usuarios, 'nome', 'id');

        $cargos = $this->db
            ->select('a.nome')
            ->join('usuarios b', 'b.cargo = a.nome')
            ->where('b.empresa', $this->session->userdata('empresa'))
            ->where('b.depto', 'Educação Inclusiva')
            ->where_in('b.status', [1, 3])
            ->group_by('a.id')
            ->order_by('a.nome', 'asc')
            ->get('empresa_cargos a')
            ->result();

        $cargosSub = ['' => 'selecione...'] + array_column($cargos, 'nome', 'nome');

        $funcoes = $this->db
            ->select('a.nome')
            ->join('empresa_cargos b', 'b.id = a.id_cargo')
            ->join('usuarios c', 'c.funcao = a.nome')
            ->where('c.empresa', $this->session->userdata('empresa'))
            ->where('c.depto', 'Educação Inclusiva')
            ->where('b.nome', $data->cargo_sub1 ?? $data->cargo)
            ->where_in('c.status', [1, 3])
            ->group_by('a.id')
            ->order_by('a.nome', 'asc')
            ->get('empresa_funcoes a')
            ->result();

        $funcoesSub = ['' => 'selecione...'] + array_column($funcoes, 'nome', 'nome');

        $data->id_cuidador_sub1 = form_dropdown('', $idCuidadorSub, $data->id_cuidador_sub1);
        $data->id_cuidador_sub2 = form_dropdown('', $idCuidadorSub, $data->id_cuidador_sub2);
        $data->municipio_sub1 = form_dropdown('', $municipioSub, '');
        $data->municipio_sub2 = form_dropdown('', $municipioSub, '');
        $data->cargo_sub1 = form_dropdown('', $cargosSub, $data->cargo_sub1 ?? $data->cargo);
        $data->cargo_sub2 = form_dropdown('', $cargosSub, $data->cargo_sub2);
        $data->funcao_sub1 = form_dropdown('', $funcoesSub, $data->funcao_sub1 ?? $data->funcao);
        $data->funcao_sub2 = form_dropdown('', $funcoesSub, $data->funcao_sub2);

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function atualizar_substituto()
    {
        $municipio = $this->input->post('municipio');
        $idUsuario = $this->input->post('id_usuario');

        $qb = $this->db
            ->select('id, nome')
            ->where('empresa', $this->session->userdata('empresa'))
            ->where('tipo', 'funcionario');
        if ($municipio) {
            $qb->where('municipio', $municipio);
        }
        $usuarios = $qb
            ->order_by('nome', 'asc')
            ->get('usuarios')
            ->result();

        $idCuidadorSub = ['' => 'selecione...'] + array_column($usuarios, 'nome', 'id');

        $data['usuario'] = form_dropdown('', $idCuidadorSub, $idUsuario);

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function atualizar_funcao_substituto()
    {
        $cargo = $this->input->post('cargo');
        $funcao = $this->input->post('funcao');

        $funcoes = $this->db
            ->select('a.nome')
            ->join('empresa_cargos b', 'b.id = a.id_cargo')
            ->join('usuarios c', 'c.id_funcao = a.id')
            ->where('c.empresa', $this->session->userdata('empresa'))
            ->where('c.depto', 'Educação Inclusiva')
            ->where('b.nome', $cargo)
            ->group_by('a.id')
            ->order_by('a.nome', 'asc')
            ->get('empresa_funcoes a')
            ->result_array();

        $funcoes = ['' => 'selecione...'] + array_column($funcoes, 'nome', 'nome');
        $data = ['funcao_sub1' => form_dropdown('', $funcoes, $funcao)];

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_save_substituto()
    {
        $id = $this->input->post('id');
        $mes = $this->input->post('mes');

        $idCuidadorSub1 = $this->input->post('id_cuidador_sub1');
        if (strlen($idCuidadorSub1) == 0) {
            $idCuidadorSub1 = null;
        }

        $cargoSub1 = $this->input->post('cargo_sub1');
        $funcaoSub1 = $this->input->post('funcao_sub1');

        $dataSubstituicao1 = $this->input->post('data_substituicao1');
        if ($dataSubstituicao1) {
            $dataSubstituicao1 = date('Y-m-d', strtotime(str_replace('/', '-', $dataSubstituicao1)));
        } else {
            $dataSubstituicao1 = null;
        }

        $idCuidadorSub2 = $this->input->post('id_cuidador_sub2');
        if (strlen($idCuidadorSub2) == 0) {
            $idCuidadorSub2 = null;
        }

        $cargoSub2 = $this->input->post('cargo_sub2');
        $funcaoSub2 = $this->input->post('funcao_sub2');

        $dataSubstituicao2 = $this->input->post('data_substituicao2');
        if ($dataSubstituicao2) {
            $dataSubstituicao2 = date('Y-m-d', strtotime(str_replace('/', '-', $dataSubstituicao2)));
        } else {
            $dataSubstituicao2 = null;
        }

        $alocacao = $this->db
            ->select('d.ano, d.semestre')
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->where('a.id', $id)
            ->get('ei_alocados_horarios a')
            ->row();

        $anoMes = $alocacao->ano . '-' . $mes;
        $timestamp = strtotime($anoMes . '-01');
        $diaPrimeiroDoProximoMes = date('Y-m-d', strtotime('+1 month', $timestamp));

        $row = $this->db
            ->select('d.ano, d.semestre')
            ->select(["IF(MONTH(MIN(f.data_inicio)) = '{$mes}', MIN(f.data_inicio), NULL) AS data_inicio"], false)
            ->select(["IF(MONTH(MAX(f.data_termino)) = '{$mes}', MAX(f.data_termino), NULL) AS data_termino"], false)
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->join('ei_matriculados_turmas e', 'e.id_alocado_horario = a.id', 'left')
            ->join('ei_matriculados f', 'f.id = e.id_matriculado AND f.id_alocacao_escola = c.id', 'left')
            ->where('a.id', $id)
            ->group_by('b.id')
            ->get('ei_alocados_horarios a')
            ->row();

        $idMes = (int)$mes - ($row->semestre > 1 ? 6 : 0);

        if ($row->data_inicio) {
            $diaIni = $row->data_inicio;
        } else {
            $diaIni = $row->ano . '-' . $mes . '-01';
        }
        if ($row->data_termino) {
            $diaFim = $row->data_termino;
        } else {
            $diaFim = date('Y-m-t', strtotime($diaIni));
        }

        $qb = $this->db
            ->set('id_cuidador_sub1', $idCuidadorSub1)
            ->set('cargo_sub1', $cargoSub1)
            ->set('funcao_sub1', $funcaoSub1)
            ->set('data_substituicao1', $dataSubstituicao1)
            ->set('id_cuidador_sub2', $idCuidadorSub2)
            ->set('cargo_sub2', $cargoSub2)
            ->set('funcao_sub2', $funcaoSub2)
            ->set('data_substituicao2', $dataSubstituicao2);
        if ($dataSubstituicao1) {
            if ($dataSubstituicao2) {
                $qb->set("total_semanas_mes{$idMes}", "WEEK(DATE_SUB(DATE_SUB('{$dataSubstituicao1}', INTERVAL 1 DAY), INTERVAL ((7 + DATE_FORMAT(DATE_SUB('{$dataSubstituicao1}', INTERVAL 1 DAY), '%w') - dia_semana) % 7) DAY)) - WEEK(DATE_ADD('{$diaIni}', INTERVAL (((7 - DATE_FORMAT('{$diaIni}', '%w')) + dia_semana) % 7) DAY)) + 1", false)
                    ->set('total_semanas_sub1', "WEEK(DATE_SUB(DATE_SUB('{$dataSubstituicao2}', INTERVAL 1 DAY), INTERVAL ((7 + DATE_FORMAT(DATE_SUB('{$dataSubstituicao2}', INTERVAL 1 DAY), '%w') - dia_semana) % 7) DAY)) - WEEK(DATE_ADD('{$dataSubstituicao1}', INTERVAL (((7 - DATE_FORMAT('{$dataSubstituicao1}', '%w')) + dia_semana) % 7) DAY)) + 1", false)
                    ->set('total_semanas_sub2', "WEEK(DATE_SUB('{$diaFim}', INTERVAL ((7 + DATE_FORMAT('$diaFim', '%w') - dia_semana) % 7) DAY)) - WEEK(DATE_ADD('{$dataSubstituicao2}', INTERVAL (((7 - DATE_FORMAT('{$dataSubstituicao2}', '%w')) + dia_semana) % 7) DAY)) + 1", false);
            } else {
                $qb->set("total_semanas_mes{$idMes}", "WEEK(DATE_SUB(DATE_SUB('{$dataSubstituicao1}', INTERVAL 1 DAY), INTERVAL ((7 + DATE_FORMAT(DATE_SUB('{$dataSubstituicao1}', INTERVAL 1 DAY), '%w') - dia_semana) % 7) DAY)) - WEEK(DATE_ADD('{$diaIni}', INTERVAL (((7 - DATE_FORMAT('{$diaIni}', '%w')) + dia_semana) % 7) DAY)) + 1", false)
                    ->set('total_semanas_sub1', "WEEK(DATE_SUB('{$diaFim}', INTERVAL ((7 + DATE_FORMAT('$diaFim', '%w') - dia_semana) % 7) DAY)) - WEEK(DATE_ADD('{$dataSubstituicao1}', INTERVAL (((7 - DATE_FORMAT('{$dataSubstituicao1}', '%w')) + dia_semana) % 7) DAY)) + 1", false)
                    ->set('total_semanas_sub2', null);
            }
        } elseif ($dataSubstituicao2) {
            $qb->set("total_semanas_mes{$idMes}", "WEEK(DATE_SUB(DATE_SUB('{$dataSubstituicao2}', INTERVAL 1 DAY), INTERVAL ((7 + DATE_FORMAT(DATE_SUB('{$dataSubstituicao2}', INTERVAL 1 DAY), '%w') - dia_semana) % 7) + 1 DAY)) - WEEK(DATE_ADD('{$diaIni}', INTERVAL (((7 - DATE_FORMAT('{$diaIni}', '%w')) + dia_semana) % 7) DAY)) + 1", false)
                ->set('total_semanas_sub1', null)
                ->set('total_semanas_sub2', "WEEK(DATE_SUB('{$diaFim}', INTERVAL ((7 + DATE_FORMAT('$diaFim', '%w') - dia_semana) % 7) DAY)) - WEEK('DATE_ADD('{$dataSubstituicao2}', INTERVAL (((7 - DATE_FORMAT('{$dataSubstituicao2}', '%w')) + dia_semana) % 7) DAY)') + 1", false);
        } else {
            $qb->set("total_semanas_mes{$idMes}", "WEEK(DATE_SUB('{$diaFim}', INTERVAL ((7 + DATE_FORMAT('$diaFim', '%w') - dia_semana) % 7) DAY)) - WEEK(DATE_ADD('{$diaIni}', INTERVAL (((7 - DATE_FORMAT('{$diaIni}', '%w')) + dia_semana) % 7) DAY)) + 1", false)
                ->set('total_semanas_sub1', null)
                ->set('total_semanas_sub2', null);
        }
        $status = $qb
            ->where('id', $id)
            ->update('ei_alocados_horarios');

        $data2 = $this->db
            ->select(["COUNT(IF(TIME_TO_SEC(c.desconto) < 0, c.id, 0)) AS desconto_mes"], false)
            ->select(["COUNT(IF(TIME_TO_SEC(d.desconto_sub1) < 0, d.id, 0)) AS desconto_sub1"], false)
            ->select(["COUNT(IF(TIME_TO_SEC(e.desconto_sub2) < 0, e.id, 0)) AS desconto_sub2"], false)
            ->select(["COUNT(IF(TIME_TO_SEC(c.desconto) > 0, c.id, 0)) AS endosso_mes"], false)
            ->select(["COUNT(IF(TIME_TO_SEC(d.desconto_sub1) > 0, d.id, 0)) AS endosso_sub1"], false)
            ->select(["COUNT(IF(TIME_TO_SEC(e.desconto_sub2) > 0, e.id, 0)) AS endosso_sub2"], false)
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_apontamento c', "c.id_alocado = b.id AND (c.periodo = a.periodo OR c.periodo IS NULL) AND MONTH(c.data) = '{$mes}' AND DATE_FORMAT(c.data, '%w') = a.dia_semana AND (c.id_usuario IS NULL OR c.id_usuario = b.id_cuidador) AND c.data < IFNULL(a.data_substituicao1, '{$diaPrimeiroDoProximoMes}') AND c.status IN ('FA', 'PV', 'FE', 'EM', 'RE', 'EE', 'HE', 'SL')", 'left')
            ->join('ei_apontamento d', "d.id_alocado = b.id AND (d.periodo = a.periodo OR d.periodo IS NULL) AND MONTH(d.data) = '{$mes}' AND DATE_FORMAT(d.data, '%w') = a.dia_semana AND (d.id_usuario IS NULL OR d.id_usuario = a.id_cuidador_sub1) AND DATE_FORMAT(a.data_substituicao1, '%Y-%m') < '{$anoMes}' AND DATE_FORMAT(d.data, '%Y-%m') = '{$anoMes}' AND d.status IN ('FA', 'PV', 'FE', 'EM', 'RE', 'EE', 'HE', 'SL')", 'left')
            ->join('ei_apontamento e', "e.id_alocado = b.id AND (e.periodo = a.periodo OR e.periodo IS NULL) AND MONTH(e.data) = '{$mes}' AND DATE_FORMAT(e.data, '%w') = a.dia_semana AND (e.id_usuario IS NULL OR e.id_usuario = a.id_cuidador_sub2) AND DATE_FORMAT(a.data_substituicao2, '%Y-%m') < '{$anoMes}' AND DATE_FORMAT(e.data, '%Y-%m') = '{$anoMes}' AND e.status IN ('FA', 'PV', 'FE', 'EM', 'RE', 'EE', 'HE', 'SL')", 'left')
            ->where('a.id', $id)
            ->get('ei_alocados_horarios a')
            ->row();

        $this->db
            ->set("desconto_mes{$idMes}", $data2->desconto_mes ?: null)
            ->set('desconto_sub1', $data2->desconto_sub1 ?: null)
            ->set('desconto_sub2', $data2->desconto_sub2 ?: null)
            ->set("endosso_mes{$idMes}", $data2->desconto_mes ?: null)
            ->set('endosso_sub1', $data2->desconto_sub1 ?: null)
            ->set('endosso_sub2', $data2->desconto_sub2 ?: null)
            ->where('id', $id)
            ->update('ei_alocados_horarios');

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_edit_qtde_dias()
    {
        $data = $this->db
            ->select('a.id, a.dia_semana, b.cuidador, c.escola, c.municipio, d.ano, d.semestre')
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->where('a.id', $this->input->post('id_horario'))
            ->get('ei_alocados_horarios a')
            ->row();

        if (empty($data)) {
            exit(json_encode(['erro' => 'O horário alocado não existe ou foi desalocado do semestre.']));
        }

        $data->mes = $this->input->post('mes');
        $idMes = $data->mes - ($data->semestre > 1 ? 6 : 0);
        $sub = $this->input->post('substituto');

        $horario = $this->db
            ->select(($sub == '2' ? 'total_semanas_sub2' : ($sub == '1' ? 'total_semanas_sub2' : "total_semanas_mes{$idMes}") . ' AS total_semanas'), false)
            ->where('id', $data->id)
            ->get('ei_alocados_horarios')
            ->row();

        $data->total_semanas = $horario->total_semanas ?? null;

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_edit_desconto_mensal()
    {
        $data = $this->db
            ->select('a.id, a.dia_semana, b.cuidador, c.escola, c.municipio, d.ano, d.semestre')
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->where('a.id', $this->input->post('id_horario'))
            ->get('ei_alocados_horarios a')
            ->row();

        if (empty($data)) {
            exit(json_encode(['erro' => 'O horário alocado não existe ou foi desalocado do semestre.']));
        }

        $data->mes = $this->input->post('mes');
        $idMes = $data->mes - ($data->semestre > 1 ? 6 : 0);
        $sub = $this->input->post('substituto');

        $horario = $this->db
            ->select(($sub == '2' ? 'desconto_sub2' : ($sub == '1' ? 'desconto_sub2' : "desconto_mes{$idMes}") . ' AS desconto'), false)
            ->select(($sub == '2' ? 'endosso_sub2' : ($sub == '1' ? 'endosso_sub2' : "endosso_mes{$idMes}") . ' AS endosso'), false)
            ->where('id', $data->id)
            ->get('ei_alocados_horarios')
            ->row();

        $data->desconto = str_replace('.', ',', $horario->desconto);
        $data->endosso = str_replace('.', ',', $horario->desconto - $horario->endosso);
        $data->endosso_original = $horario->endosso;

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_save_cargo_funcao()
    {
        $horario = $this->db
            ->where('id', $this->input->post('id'))
            ->get('ei_alocados_horarios')
            ->row();

        if (empty((array)$horario)) {
            exit(json_encode(['erro' => 'Registro não encontrado.']));
        }

        $cargo = $this->input->post('cargo');
        $funcao = $this->input->post('funcao');
        $mesInicial = $this->input->post('mes');
        $semestre = $this->input->post('semestre');
        $idMesInicial = $mesInicial - ($semestre > 1 ? 6 : 0);
        $idMeses = range($idMesInicial, $semestre > 1 ? 6 : 7);
        $meses = range($mesInicial, $semestre > 1 ? 6 : 7);

        $this->db->trans_start();

        foreach ($idMeses as $k => $idMes) {
            $idMesCargoFuncao = $idMes > 1 ? '_mes' . $idMes : '';
            $horarioCargo = $horario->{'cargo' . $idMesCargoFuncao};
            $horarioFuncao = $horario->{'funcao' . $idMesCargoFuncao};

            if ($this->input->post('todo_periodo')) {
                $this->db
                    ->set('cargo' . $idMesCargoFuncao, $cargo)
                    ->set('funcao' . $idMesCargoFuncao, $funcao)
                    ->where('id_alocado', $horario->id_alocado)
                    ->where('periodo', $horario->periodo)
                    ->where('cargo' . $idMesCargoFuncao, $horarioCargo)
                    ->where('funcao' . $idMesCargoFuncao, $horarioFuncao)
                    ->update('ei_alocados_horarios');

                $this->db
                    ->set('cargo' . $idMesCargoFuncao, $cargo)
                    ->set('funcao' . $idMesCargoFuncao, $funcao)
                    ->where('id_alocado', $horario->id_alocado)
                    ->where('periodo', $horario->periodo)
                    ->where('cargo' . $idMesCargoFuncao, $horarioCargo)
                    ->where('funcao' . $idMesCargoFuncao, $horarioFuncao)
                    ->update('ei_alocados_totalizacao');

                $this->db
                    ->set('cargo', $cargo)
                    ->set('funcao', $funcao)
                    ->where('id_alocado', $horario->id_alocado)
                    ->where('mes_referencia', $meses[$k])
                    ->where('cargo', $horarioCargo)
                    ->where('funcao', $horarioFuncao)
                    ->update('ei_alocados_aprovacoes');
            } else {
                $outroHorario = $this->db
                    ->where('id_alocado', $horario->id_alocado)
                    ->where('dia_semana', $horario->dia_semana)
                    ->where('periodo', $horario->periodo)
                    ->where('cargo' . $idMesCargoFuncao, $horarioCargo)
                    ->where('funcao' . $idMesCargoFuncao, $horarioFuncao)
                    ->where("horario_inicio_mes{$idMes} >=", $horario->{'horario_inicio_mes' . $idMes})
                    ->where("horario_termino_mes{$idMes} <=", $horario->{'horario_termino_mes' . $idMes})
                    ->where('id !=', $horario->id)
                    ->get('ei_alocados_horarios')
                    ->row();

                if (!empty((array)$outroHorario)) {
                    exit(json_encode(['erro' => 'O horário com a função selecionada já existe em outro registro.']));
                }

                $this->db
                    ->set('cargo' . $idMesCargoFuncao, $cargo)
                    ->set('funcao' . $idMesCargoFuncao, $funcao)
                    ->where('id', $horario->id)
                    ->update('ei_alocados_horarios');
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() == false) {
            exit(json_encode(['erro' => 'Não foi possível atualizar o cargo/funçao, tente mais tarde.']));
        }

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    public function ajax_save_qtde_dias()
    {
        $id = $this->input->post('id');
        $mes = $this->input->post('mes');
        $semestre = $this->input->post('semestre');
        $idMes = $this->getIdMes($mes, $semestre);
        $totalSemanas = $this->input->post('total_semanas');
        $substituto = $this->input->post('substituto');

        $this->load->helper('time');

        $qb = $this->db;
        if ($substituto === '2') {
            $qb->set('total_semanas_sub2', $totalSemanas);
        } elseif ($substituto === '1') {
            $qb->set('total_semanas_sub1', $totalSemanas);
        } elseif ($totalSemanas) {
            $qb->set('total_semanas_mes' . $idMes, $totalSemanas);
        } else {
            $meses = array_unique(range($idMes, $semestre === '1' ? 7 : 6));
            foreach ($meses as $mes) {
                $qb->set('total_semanas_mes' . $mes, $totalSemanas);
            }
        }
        $status = $qb
            ->where('id', $id)
            ->update('ei_alocados_horarios');

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_save_desconto_mensal()
    {
        $id = $this->input->post('id');
        $mes = $this->input->post('mes');
        $semestre = $this->input->post('semestre');
        $idMes = $this->getIdMes($mes, $semestre);
        $desconto = $this->input->post('desconto');
        $endosso = $this->input->post('endosso');
        $endossoOriginal = $this->input->post('endosso_original');
        $substituto = $this->input->post('substituto');
        if ($desconto) {
            $desconto = str_replace(',', '.', $desconto);
        }
        if ($endosso) {
            $endosso = str_replace(',', '.', $endosso) + $endossoOriginal;
        }

        $horarios = $this->db
            ->select('id_alocado, dia_semana, periodo, cargo, funcao')
            ->select("total_horas_mes{$idMes} AS total_horas_mes")
            ->select("total_semanas_mes{$idMes} AS total_semanas_mes")
            ->where('id', $id)
            ->get('ei_alocados_horarios')
            ->row();

        $this->load->helper('time');

        $totalMes = secToTime(timeToSec($horarios->total_horas_mes) * ($horarios->total_semanas_mes - $endosso));

        $qb = $this->db;
        if ($substituto === '2') {
            $qb->set('desconto_sub2', $desconto)
                ->set('endosso_sub2', $endosso)
                ->set('total_endossado_sub2', $totalMes);
        } elseif ($substituto === '1') {
            $qb->set('desconto_sub1', $desconto)
                ->set('endosso_sub1', $endosso)
                ->set('total_endossado_sub1', $totalMes);
        } else {
            $qb->set('desconto_mes' . $idMes, $desconto)
                ->set('endosso_mes' . $idMes, $endosso)
                ->set('total_endossado_mes' . $idMes, $totalMes);
        }
        $status = $qb
            ->where('id_alocado', $horarios->id_alocado)
            ->where('dia_semana', $horarios->dia_semana)
            ->where('periodo', $horarios->periodo)
            ->where('cargo', $horarios->cargo)
            ->where('funcao', $horarios->funcao)
            ->update('ei_alocados_horarios');

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_edit_faturamento()
    {
        $alocado = $this->db
            ->select('a.id, c.semestre')
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->where('a.id', $this->input->post('id_alocado'))
            ->get('ei_alocados a')
            ->row();

        if (empty($alocado)) {
            exit(json_encode(['erro' => 'O mês alocado não existe ou foi desalocado do semestre.']));
        }

        $mes = $this->input->post('mes');
        $idMes = (int)$mes - ($alocado->semestre > 1 ? 6 : 0);
        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';
        $periodo = $this->input->post('periodo');
        $cargo = $this->input->post('cargo');
        $funcao = $this->input->post('funcao');
        $substituto = $this->input->post('substituto');

        $usuario = $this->db
            ->select(["(CASE WHEN '{$mes}' > MONTH(a.data_substituicao1) AND a.data_substituicao1 IS NOT NULL THEN a.id_cuidador_sub1 ELSE b.id_cuidador END) AS id"], false)
            ->select('b.id_cuidador, a.id_cuidador_sub1, a.id_cuidador_sub2')
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->where('b.id', $alocado->id)
            ->where('a.periodo', $periodo)
            ->where('a.cargo' . $mesCargoFuncao, $cargo)
            ->where('a.funcao' . $mesCargoFuncao, $funcao)
            ->get('ei_alocados_horarios a')
            ->row();

        $qb = $this->db
            ->select("f.id, a.id_alocado, a.periodo, c.id_alocacao, c.id_escola, e.cargo{$mesCargoFuncao} AS cargo, e.funcao{$mesCargoFuncao} AS  funcao")
            ->select("'{$mes}' AS mes", false);
        if ($substituto) {
            $qb->select('f.observacoes_sub1 AS observacoes', false)
                ->select('f.preservar_edicao_sub1 AS preservar_edicao', false)
                ->select("DATE_FORMAT(f.data_envio_solicitacao_sub1, '%d/%m/%Y') AS data_envio_solicitacao", false)
                ->select("DATE_FORMAT(f.data_aprovacao_sub1, '%d/%m/%Y') AS data_aprovacao", false)
                ->select("DATE_FORMAT(IFNULL(f.data_impressao_sub1, NOW()), '%d/%m/%Y') AS data_impressao", false);
        } else {
            $qb->select("f.observacoes_mes{$idMes} AS observacoes", false)
                ->select("f.preservar_edicao_mes{$idMes} AS preservar_edicao", false)
                ->select("DATE_FORMAT(f.data_envio_solicitacao_mes{$idMes}, '%d/%m/%Y') AS data_envio_solicitacao", false)
                ->select("DATE_FORMAT(f.data_aprovacao_mes{$idMes}, '%d/%m/%Y') AS data_aprovacao", false)
                ->select("DATE_FORMAT(IFNULL(f.data_impressao_mes{$idMes}, NOW()), '%d/%m/%Y') AS data_impressao", false);
        }
        $data = $qb
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->join('ei_alocados_horarios e', 'e.id_alocado = b.id AND e.periodo = a.periodo')
            ->join('ei_faturamento f', "f.id_alocacao = d.id AND f.id_escola = c.id_escola AND f.cargo = e.cargo{$mesCargoFuncao} AND f.funcao = e.funcao{$mesCargoFuncao}", 'left')
            ->where('b.id', $alocado->id)
            ->where('a.periodo', $periodo)
            ->where('e.cargo' . $mesCargoFuncao, $cargo)
            ->where('e.funcao' . $mesCargoFuncao, $funcao)
            ->where('a.id_cuidador', $usuario->id)
            ->group_by(['c.id_escola', 'e.cargo' . $mesCargoFuncao, 'e.funcao' . $mesCargoFuncao])
            ->get('ei_alocados_totalizacao a')
            ->row();

        if (empty($data)) {
            exit(json_encode(['erro' => 'O colaborador não existe ou foi desalocado do semestre.']));
        }

        $data->planilha_faturamento = $this->planilhaFaturamento($alocado->id, $mes, $periodo, $cargo, $funcao);

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_recuperar_faturamento()
    {
        $idAlocado = $this->input->post('id_alocado');

        $alocado = $this->db
            ->select('a.id, c.semestre')
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->where('a.id', $idAlocado)
            ->get('ei_alocados a')
            ->row();

        $mes = $this->input->post('mes');
        $idMes = (int)$mes - ($alocado->semestre > 1 ? 6 : 0);
        $periodo = $this->input->post('periodo');
        $cargo = $this->input->post('cargo');
        $funcao = $this->input->post('funcao');

        $row = $this->db
            ->select("observacoes_mes{$idMes} AS observacoes", false)
            ->select("preservar_edicao_mes{$idMes} AS preservar_edicao", false)
            ->select("DATE_FORMAT(data_envio_solicitacao_mes{$idMes}, '%d/%m/%Y') AS data_envio_solicitacao", false)
            ->select("DATE_FORMAT(data_aprovacao_mes{$idMes}, '%d/%m/%Y') AS data_aprovacao", false)
            ->where('id', $this->input->post('id'))
            ->get('ei_faturamento')
            ->row();

        $data['data_aprovacao'] = $row->data_aprovacao ?? '';
        $data['observacoes'] = $row->observacoes ?? '';
        $data['planilha_faturamento'] = $this->planilhaFaturamento($idAlocado, $mes, $periodo, $cargo, $funcao, false, true);

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_save_faturamento()
    {
        $id = $this->input->post('id');
        $idAlocacao = $this->input->post('id_alocacao');
        $idEscola = $this->input->post('id_escola');
        $cargo = $this->input->post('cargo');
        $funcao = $this->input->post('funcao');
        $mes = $this->input->post('mes');

        $alocacao = $this->db
            ->select('id, semestre')
            ->where('id', $idAlocacao)
            ->get('ei_alocacao')
            ->row();

        $idMes = $this->getIdMes($mes, $alocacao->semestre);
        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';

        $dataEnvioSolicitacao = $this->input->post('data_envio_solicitacao');
        $dataAprovacao = $this->input->post('data_aprovacao');
        $dataImpressao = $this->input->post('data_impressao');
        $horasDescontadas = $this->input->post('horas_descontadas');
        if ($dataEnvioSolicitacao) {
            $dataEnvioSolicitacao = date('Y-m-d', strtotime(str_replace('/', '-', $dataEnvioSolicitacao)));
        } else {
            $dataEnvioSolicitacao = null;
        }
        if ($dataAprovacao) {
            $dataAprovacao = date('Y-m-d', strtotime(str_replace('/', '-', $dataAprovacao)));
        } else {
            $dataAprovacao = null;
        }
        if ($dataImpressao) {
            $dataImpressao = date('Y-m-d', strtotime(str_replace('/', '-', $dataImpressao)));
        } else {
            $dataImpressao = null;
        }
        if (strlen($horasDescontadas) == 0) {
            $horasDescontadas = null;
        }
        $observacoes = $this->input->post('observacoes');
        if (strlen($observacoes) == 0) {
            $observacoes = null;
        }
        $preservarEdicao = $this->input->post('preservar_edicao');
        if (strlen($preservarEdicao) == 0) {
            $preservarEdicao = null;
        }

        $this->db->trans_start();

        $faturamentos = $this->db
            ->select("d.id, a.id_escola, a.escola, a.id_alocacao, c.cargo{$mesCargoFuncao} AS cargo, c.funcao{$mesCargoFuncao} AS funcao")
            ->join('ei_alocados b', 'b.id_alocacao_escola = a.id')
            ->join('ei_alocados_horarios c', "c.id_alocado = b.id AND c.cargo{$mesCargoFuncao} = '{$cargo}' AND c.funcao{$mesCargoFuncao} = '{$funcao}'", 'left')
            ->join('ei_faturamento d', "d.id_alocacao = a.id_alocacao AND d.id = '{$id}'", 'left')
            ->where('a.id_alocacao', $alocacao->id)
            ->where('a.id_escola', $idEscola)
            ->where("c.cargo{$mesCargoFuncao} IS NOT NULL", null, false)
            ->where("c.funcao{$mesCargoFuncao} IS NOT NULL", null, false)
            ->group_by(['a.id_escola', 'c.cargo' . $mesCargoFuncao, 'c.funcao' . $mesCargoFuncao])
            ->get('ei_alocacao_escolas a')
            ->result();

        foreach ($faturamentos as $faturamento) {
            $faturamento->{'data_envio_solicitacao_mes' . $idMes} = $dataEnvioSolicitacao;
            $faturamento->{'data_aprovacao_mes' . $idMes} = $dataAprovacao;
            $faturamento->{'data_impressao_mes' . $idMes} = $dataImpressao;
            $faturamento->{'observacoes_mes' . $idMes} = $observacoes;
            $faturamento->{'preservar_edicao_mes' . $idMes} = $preservarEdicao;

            if ($faturamento->id) {
                $this->db->update('ei_faturamento', $faturamento, ['id' => $faturamento->id]);
            } else {
                $this->db->insert('ei_faturamento', $faturamento);
            }
        }

        if (is_null($preservarEdicao)) {

            $rows = array_map(null,
                $this->input->post('id_totalizacao'),
                $this->input->post('id_alocado'),
                $this->input->post('periodo'),
                $this->input->post('total_dias'),
                $this->input->post('total_descontos'),
                $this->input->post('total_horas')
            );

            $campos = ['id', 'id_alocado', 'periodo', "total_dias_mes{$idMes}", "total_descontos_mes{$idMes}", "total_horas_mes{$idMes}"];

            foreach ($rows as $data) {
                $data = array_combine($campos, $data);
                if ($data['id']) {
                    $this->db->update('ei_alocados_totalizacao', $data, ['id' => $data['id']]);
                } else {
                    $this->db->insert('ei_alocados_totalizacao', $data);
                }
            }
        }

        $this->db->trans_complete();
        $status = $this->db->trans_status();

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function pdf_faturamento()
    {
        $this->load->library('m_pdf');

        $stylesheet = '#table thead tr th { border-top: 4px solid #ddd; padding-top: 8px; } ';
        $stylesheet .= '#table { border-bottom: 1px solid #ddd; } ';
        $stylesheet .= '#periodo { border: 1px solid #444; margin-bottom: 0px; } ';
        $stylesheet .= '#periodo thead th { padding: 4px; background-color: #f5f5f5; border: 1px solid #444; } ';
        $stylesheet .= '#periodo tbody td { font-size: 12px; padding: 4px; vertical-align: top; border: 1px solid #444; } ';
        $stylesheet .= 'p strong { font-weight: bold; }';

        $this->m_pdf->pdf->writeHTML($stylesheet, 1);
        $idAlocado = $this->input->get('id_alocado');
        $mes = $this->input->get('mes');
        $periodo = $this->input->get('periodo');
        $cargo = $this->input->get('cargo');
        $funcao = $this->input->get('funcao');
        $this->m_pdf->pdf->writeHTML($this->planilhaFaturamento($idAlocado, $mes, $periodo, $cargo, $funcao, true));

        $alocado = $this->db
            ->select('b.escola, c.ano')
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->where('a.id', $idAlocado)
            ->get('ei_alocados a')
            ->row();

        $this->load->library('Calendar');
        $nomeMes = ucfirst($this->calendar->get_month_name(str_pad($mes, 2, '0', STR_PAD_LEFT)));

        $this->m_pdf->pdf->Output("FAT {$alocado->escola} - {$nomeMes}_{$alocado->ano}.pdf", 'D');
    }

    //--------------------------------------------------------------------

    private function planilhaFaturamento(?int $idAlocado, ?string $mes, ?int $periodo, ?string $cargo, ?string $funcao, ?bool $is_pdf = false, ?bool $recuperar = false): string
    {
        // prepara o cabecalho da planilha
        $empresa = $this->db
            ->select('foto, foto_descricao')
            ->where('id', $this->session->userdata('empresa'))
            ->get('usuarios')
            ->row();

        $usuario = $this->db
            ->select('nome, email')
            ->where('id', $this->session->userdata('id'))
            ->get('usuarios')
            ->row();

        // flag de substituto
        $substituto = $this->input->get_post('substituto');

        $semestre = $this->db
            ->select('c.semestre')
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->where('a.id', $idAlocado)
            ->get('ei_alocados a')
            ->row();

        $idMes = $this->getIdMes($mes, $semestre->semestre);
        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';

        // recuperar dados da alocacao
        $alocacao = $this->db
            ->select("c.id, b.id_escola, b.escola, f.cargo{$mesCargoFuncao} AS cargo, f.funcao{$mesCargoFuncao} AS funcao, c.ano, c.semestre")
            ->select('d.nome AS nome_supervisor, IFNULL(e.nome, d.funcao) AS funcao_supervisor, d.email AS email_supervisor', false)
            ->select(["(SELECT GROUP_CONCAT(DISTINCT x.ordem_servico ORDER BY x.ordem_servico ASC SEPARATOR ',') 
						FROM ei_alocacao_escolas x
       					WHERE x.id_escola = b.id_escola and x.id_alocacao = c.id) AS ordem_servico"], false)
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->join('ei_ordem_servico c2', 'c2.nome = b.ordem_servico AND c2.ano = c.ano AND c2.semestre = c.semestre')
            ->join('usuarios d', 'd.id = c.id_supervisor')
            ->join('empresa_funcoes e', 'e.id = d.id_funcao', 'left')
            ->join('ei_alocados_horarios f', 'f.id_alocado = a.id', 'left')
            ->where('a.id', $idAlocado)
            ->where('f.periodo', $periodo)
            ->where('f.cargo' . $mesCargoFuncao, $cargo)
            ->where('f.funcao' . $mesCargoFuncao, $funcao)
            ->group_by('a.id')
            ->get('ei_alocados a')
            ->row();

        // dado do mes atual
        $ordensServico = explode(',', $alocacao->ordem_servico);

        // recupera os dados do cuidador principal ou substituto
        $cuidadores = $this->db
            ->select('b.id AS id_alocado, c.ordem_servico')
            ->select(["(CASE WHEN '{$mes}' > MONTH(a.data_substituicao1) AND a.data_substituicao1 IS NOT NULL THEN a.id_cuidador_sub1 ELSE b.id_cuidador END) AS id"], false)
            ->select(["(CASE WHEN '{$mes}' = MONTH(a.data_substituicao1) AND a.data_substituicao1 IS NOT NULL THEN a.id_cuidador_sub1 END) AS id_sub"], false)
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->join('usuarios e', 'e.id = a.id_cuidador_sub1', 'left')
            ->where('d.id', $alocacao->id)
            ->where('c.id_escola', $alocacao->id_escola)
            ->where_in('c.ordem_servico', $ordensServico)
            ->where('a.cargo' . $mesCargoFuncao, $alocacao->cargo)
            ->where('a.funcao' . $mesCargoFuncao, $alocacao->funcao)
            ->get('ei_alocados_horarios a')
            ->result();

        $idCuidadores = [];
        $idAlocados = [];
        foreach ($cuidadores as $cuidador) {
            $idCuidador = $substituto ? $cuidador->id_sub : $cuidador->id;
            $idCuidadores[$idCuidador] = $idCuidador;
            $idAlocados[$cuidador->id_alocado] = $cuidador->id_alocado;
        }
        $strCuidadores = implode("', '", array_filter($idCuidadores) + [0]);

        // recupera dados de apresentacao da planilha
        $qb = $this->db
            ->select("b.id_escola, b.escola, c.funcao{$mesCargoFuncao} AS funcao, b2.semestre, b2.ano")
            ->select(["GROUP_CONCAT(DISTINCT b.contrato ORDER BY b.contrato ASC SEPARATOR ', ') AS contrato"], false)
            ->select(["GROUP_CONCAT(DISTINCT b.ordem_servico ORDER BY b.ordem_servico ASC SEPARATOR ', ') AS ordem_servico"], false);
        if ($substituto) {
            $qb->select('NULL AS cuidador', false);
        } else {
            $qb->select(["GROUP_CONCAT(DISTINCT f.cuidador ORDER BY a.cuidador ASC SEPARATOR ', ') AS cuidador"], false);
        }
        $qb->select(["GROUP_CONCAT(DISTINCT c2.nome ORDER BY c2.nome ASC SEPARATOR ', ') AS cuidador_sub1"], false)
            ->select(["GROUP_CONCAT(DISTINCT c3.nome ORDER BY c3.nome ASC SEPARATOR ', ') AS cuidador_sub2"], false)
            ->select(["GROUP_CONCAT(DISTINCT e.aluno ORDER BY e.aluno ASC SEPARATOR ', ') AS alunos"], false)
            ->select("g.id AS id_faturamento, g.observacoes_mes{$idMes} AS observacoes, MIN(c.dia_semana) AS dia_semana_inicial", false)
            ->select(["IF(COUNT(c.dia_semana) > 1, MAX(c.dia_semana), NULL) AS dia_semana_final"], false)
            ->select(["TIME_FORMAT(c.horario_inicio_mes{$idMes}, '%H:%i') AS horario_inicio"], false)
            ->select(["TIME_FORMAT(c.horario_termino_mes{$idMes}, '%H:%i') AS horario_termino"], false)
            ->select(["DATE_FORMAT(IF(MONTH(MIN(e.data_inicio)) = '{$mes}', MIN(e.data_inicio), CONCAT(b2.ano, '-', {$mes}, '-1')), '%d/%m/%Y') AS periodo_inicial"], false);
        $idMesPosterior = $idMes == '7' ? '_posterior_mes7' : '_anterior_mes' . ($idMes + 1);
        if ($recuperar) {
            $qb->select(["TIME_FORMAT(SEC_TO_TIME(SUM(DISTINCT(TIME_TO_SEC(h.desconto_atual_mes{$idMes}))) + SUM(DISTINCT(IFNULL(TIME_TO_SEC(h.desconto{$idMesPosterior}), 0)))), '%H:%i') AS horas_descontadas"], false);
        } else {
            $qb->select(["TIME_FORMAT(IFNULL(g.horas_descontadas_mes{$idMes}, SEC_TO_TIME(SUM(DISTINCT(TIME_TO_SEC(h.desconto_atual_mes{$idMes}))) + SUM(DISTINCT(IFNULL(TIME_TO_SEC(h.desconto{$idMesPosterior}), 0))))), '%H:%i') AS horas_descontadas"], false);
        }
        if ($idMes === 1) {
            $qb->select(["DATE_FORMAT(IFNULL(MAX(e.data_recesso),IFNULL(MAX(e.data_termino), IFNULL(DATE_SUB(MAX(c.data_substituicao1), INTERVAL 1 DAY), LAST_DAY(CONCAT(b2.ano, '-', {$mes}, '-1'))))), '%d/%m/%Y') AS periodo_final"], false);
        } else {
            $qb->select(["DATE_FORMAT(IF(MONTH(MAX(e.data_termino)) = '{$mes}', MAX(e.data_termino), LAST_DAY(CONCAT(b2.ano, '-', {$mes}, '-1'))), '%d/%m/%Y') AS periodo_final"], false);
        }
        $data = $qb
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao b2', 'b2.id = b.id_alocacao')
            ->join('ei_alocados_horarios c', 'c.id_alocado = a.id', 'left')
            ->join('usuarios c2', "c2.id = c.id_cuidador_sub1 AND MONTH(c.data_substituicao1) = '{$mes}'", 'left')
            ->join('usuarios c3', "c3.id = c.id_cuidador_sub2 AND MONTH(c.data_substituicao2) = '{$mes}'", 'left')
            ->join('ei_matriculados_turmas d', 'd.id_alocado_horario = c.id', 'left')
            ->join('ei_matriculados e', 'e.id = d.id_matriculado AND e.id_alocacao_escola = b.id', 'left')
            ->join('ei_alocados_totalizacao f', "f.id_alocado = a.id AND f.periodo = c.periodo AND f.id_cuidador IN ('{$strCuidadores}') AND f.substituicao_eventual IS NULL", 'left')
            ->join('ei_faturamento g', "g.id_alocacao = b2.id AND g.id_escola = b.id_escola AND g.cargo = c.cargo AND g.funcao = c.funcao{$mesCargoFuncao}", 'left')
            ->join('ei_pagamento_prestador h', 'h.id_cuidador = a.id_cuidador AND h.id_alocacao = b2.id', 'left')
            ->where_in('a.id', $idAlocados)
            ->where_in('b.ordem_servico', $ordensServico)
            ->where('b2.id', $alocacao->id)
            ->where('b.id_escola', $alocacao->id_escola)
            ->where('c.cargo' . $mesCargoFuncao, $alocacao->cargo)
            ->where('c.funcao' . $mesCargoFuncao, $alocacao->funcao)
            ->group_by('b.id_escola')
            ->get('ei_alocados a')
            ->row();

        // recupera dados de totalizacao
        $qb = $this->db
            ->select("a.id, b.id_escola, a.cuidador, d.id_alocado, d.periodo, e.id AS id_totalizacao, d.cargo{$mesCargoFuncao} AS cargo, d.funcao{$mesCargoFuncao} AS funcao")
            ->select(["GROUP_CONCAT(DISTINCT k.nome ORDER BY k.nome ASC SEPARATOR ', ') AS cuidador_sub1"], false)
            ->select(["GROUP_CONCAT(DISTINCT l.nome ORDER BY l.nome ASC SEPARATOR ', ') AS cuidador_sub2"], false)
            ->select("(CASE d.periodo WHEN 0 THEN 'Madrugada' WHEN 1 THEN 'Manhã' WHEN 2 THEN 'Tarde' WHEN 3 THEN 'Noite' END) AS nome_periodo", false)
            ->select("(SELECT COUNT(DISTINCT f2.id) FROM ei_apontamento f2 WHERE f2.id_alocado = a.id AND f2.periodo = d.periodo AND f2.status IN ('FA', 'PV', 'FE', 'EM', 'RE', 'EE', 'HE', 'SL', 'SB', 'DG') AND MONTH(f2.data) = '{$mes}' AND YEAR(f2.data) = c.ano AND DATE_FORMAT(f2.data, '%w') IN (0,6) AND DATE_FORMAT(f2.data, '%w') NOT IN (SELECT f3.dia_semana FROM ei_alocados_horarios f3 WHERE f3.id_alocado = f2.id_alocado AND f3.periodo = f2.periodo)) AS total_eventos_fim_semana", false)
            ->select("(SELECT SUM(TIME_TO_SEC(f2.desconto)) FROM ei_apontamento f2 WHERE f2.id_alocado = a.id AND f2.periodo = d.periodo AND f2.status IN ('FA', 'PV', 'FE', 'EM', 'RE', 'EE', 'HE', 'SL', 'SB', 'DG') AND MONTH(f2.data) = '{$mes}' AND YEAR(f2.data) = c.ano AND DATE_FORMAT(f2.data, '%w') IN (0,6) AND DATE_FORMAT(f2.data, '%w') NOT IN (SELECT f3.dia_semana FROM ei_alocados_horarios f3 WHERE f3.id_alocado = f2.id_alocado AND f3.periodo = f2.periodo)) AS total_descontos_fim_semana", false)
//            ->select("(SELECT COUNT(DISTINCT f3.id) FROM ei_apontamento f3 WHERE f3.id_alocado = a.id AND f3.status IN ('EE', 'HE', 'SL', 'SB', 'DG') AND MONTH(f3.data) = '{$mes}' AND YEAR(f3.data) = c.ano AND (f3.periodo = d.periodo AND DATE_FORMAT(f3.data, '%w') = d.dia_semana AND d.total_semanas_mes{$idMes} = 0) OR (DATE_FORMAT(f3.data, '%w') IN (0,6))) AS total_eventos_extra", false)
            ->select("(SELECT COUNT(f3.id) FROM ei_apontamento f3 WHERE f3.id_alocado = a.id AND f3.periodo = d.periodo AND DATE_FORMAT(f3.data, '%w') = d.dia_semana AND f3.status IN ('EE', 'HE', 'SL', 'SB', 'DG') AND MONTH(f3.data) = '{$mes}' AND YEAR(f3.data) = c.ano AND d.total_semanas_mes{$idMes} = 0) AS total_dias_extra", false)
            ->select("(SELECT SUM(TIME_TO_SEC(f3.desconto)) FROM ei_apontamento f3 WHERE f3.id_alocado = a.id AND f3.periodo = d.periodo AND DATE_FORMAT(f3.data, '%w') = d.dia_semana AND f3.status IN ('EE', 'HE', 'SL', 'SB', 'DG') AND MONTH(f3.data) = '{$mes}' AND YEAR(f3.data) = c.ano AND d.total_semanas_mes{$idMes} = 0) AS total_horas_extra", false)
            ->select(["GROUP_CONCAT(DISTINCT g.aluno ORDER BY g.aluno ASC SEPARATOR ', ') AS alunos"], false);
        if ($recuperar) {
            $qb->select(["(SUM(d.total_semanas_mes{$idMes} - IFNULL(d.desconto_mes{$idMes}, 0) + IFNULL(d.endosso_mes{$idMes}, 0) + IF(MONTH(d.data_substituicao1) = '{$mes}', IFNULL(d.total_semanas_sub1, 0) - IFNULL(d.desconto_sub1, 0) + IFNULL(d.endosso_sub1, 0), 0)) / GREATEST(COUNT(DISTINCT d.id), 1) ) / GREATEST(COUNT(DISTINCT g.id), 1) AS total_dias"], false)
                ->select("(SELECT SUM(TIME_TO_SEC(f3.desconto)) FROM ei_apontamento f3 WHERE f3.id_alocado = a.id AND f3.periodo = d.periodo AND DATE_FORMAT(f3.data, '%w') = d.dia_semana AND f3.status IN ('FA', 'PV', 'AT', 'SA', 'EE', 'HE', 'SL') AND MONTH(f3.data) = '{$mes}' AND YEAR(f3.data) = c.ano AND d.total_semanas_mes{$idMes} > 0) AS total_descontos", false)
                ->select(["(SUM(TIME_TO_SEC(IFNULL(d.total_endossado_mes{$idMes}, d.total_mes{$idMes})) + IF(MONTH(d.data_substituicao1) = {$mes}, TIME_TO_SEC(IFNULL(d.total_endossado_sub1, IFNULL(d.total_sub1, 0))), 0)) / GREATEST(COUNT(DISTINCT g.id), 1)) AS total_horas"], false);
        } else {
            $qb->select(["e.total_dias_mes{$idMes} AS total_dias, TIME_TO_SEC(e.total_descontos_mes{$idMes}) AS total_descontos"], false)
                ->select(["TIME_TO_SEC(e.total_horas_mes{$idMes}) AS total_horas"], false);
        }
        $rows = $qb
            ->select(["e.total_dias_mes{$idMes} AS total_dias_salvo"], false)
            ->select(["TIME_FORMAT(e.total_descontos_mes{$idMes}, '%H:%i') AS total_descontos_salvo"], false)
            ->select(["TIME_FORMAT(e.total_horas_mes{$idMes}, '%H:%i') AS total_horas_salvo"], false)
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->join('ei_alocados_totalizacao e', "e.id_alocado = a.id AND e.id_cuidador IN ('{$strCuidadores}') AND e.substituicao_eventual IS NULL", 'left')
            ->join('ei_alocados_horarios d', "d.id_alocado = a.id AND d.periodo = e.periodo AND d.cargo{$mesCargoFuncao} = e.cargo{$mesCargoFuncao} AND d.funcao{$mesCargoFuncao} = e.funcao{$mesCargoFuncao}", 'left')
            ->join('ei_matriculados_turmas f', 'f.id_alocado_horario = d.id', 'left')
            ->join('ei_matriculados g', 'g.id = f.id_matriculado AND g.id_alocacao_escola = b.id', 'left')
            ->join('usuarios k', 'k.id = d.id_cuidador_sub1', 'left')
            ->join('usuarios l', 'l.id = d.id_cuidador_sub2', 'left')
            ->where_in('a.id', $idAlocados)
            ->where_in('b.ordem_servico', $ordensServico)
            ->where('c.id', $alocacao->id)
            ->where('b.id_escola', $alocacao->id_escola)
            ->where('d.cargo' . $mesCargoFuncao, $alocacao->cargo)
            ->where('d.funcao' . $mesCargoFuncao, $alocacao->funcao)
            ->group_by(['a.id', 'b.id_escola', 'd.periodo', 'd.cargo' . $mesCargoFuncao, 'd.funcao' . $mesCargoFuncao, 'd.dia_semana'])
            ->get_compiled_select('ei_alocados a');

        $rows = $this->db
            ->query("SELECT s.cuidador, s.id_alocado, s.periodo,
                            s.id_totalizacao, s.cargo, s.funcao,
                            s.cuidador_sub1, s.cuidador_sub2, s.nome_periodo, s.alunos,
                            SUM(s.total_eventos_fim_semana) / COUNT(GREATEST(s.total_eventos_fim_semana, 1)) AS total_eventos_fim_semana,
                            TIME_FORMAT(SEC_TO_TIME(SUM(s.total_horas_extra)), '%H:%i') AS total_horas_extra,
                            SUM(s.total_dias) AS total_dias,
                            SUM(s.total_dias_extra) AS total_dias_extra,
                            TIME_FORMAT(SEC_TO_TIME(SUM(s.total_descontos)), '%H:%i') AS total_descontos,
                            TIME_FORMAT(SEC_TO_TIME(SUM(s.total_horas)), '%H:%i') AS total_horas,
                            TIME_FORMAT(SEC_TO_TIME(SUM(s.total_descontos_fim_semana) / COUNT(GREATEST(s.total_descontos_fim_semana, 1))), '%H:%i') AS total_descontos_fim_semana,
                            s.total_dias_salvo,
                            s.total_descontos_salvo,
                            s.total_horas_salvo
                     FROM ({$rows}) s
                     GROUP BY s.id, s.id_escola, s.cargo, s.funcao, s.periodo")
            ->result();

        $this->load->helper('time');
        // prepara dados dos inputs de cada totalizacao
        $faturamentos = [];
        foreach ($rows as $row) {
            if (!$recuperar and $row->total_dias_salvo) {
                $faturamentoDias = $row->total_dias_salvo;
            } else {
                $faturamentoDias = intval($row->total_dias) + intval($row->total_dias_extra) + intval($row->total_eventos_fim_semana);
            }
            if (!$recuperar and $row->total_descontos_salvo) {
                $faturamentoDescontos = $row->total_descontos_salvo;
            } else {
                $faturamentoDescontos = secToTime(timeToSec($row->total_descontos) + timeToSec($row->total_horas_extra) + timeToSec($row->total_descontos_fim_semana), false);
//                $faturamentoDescontos = timeSimpleFormat($row->total_descontos);
            }
            if (!$recuperar and $row->total_horas_salvo) {
                $faturamentoHoras = $row->total_horas_salvo;
            } elseif ($recuperar) {
                $faturamentoHoras = timeSimpleFormat(secToTime(timeToSec($row->total_horas) + timeToSec($row->total_descontos) + timeToSec($row->total_horas_extra) + timeToSec($row->total_descontos_fim_semana)));
            } else {
                $faturamentoHoras = timeSimpleFormat($row->total_horas);
            }

            $faturamentos[] = [
                'id' => $row->id_totalizacao,
                'id_alocado' => $row->id_alocado,
                'nome_alocado' => implode(', ', array_filter([$row->cuidador, $row->cuidador_sub1, $row->cuidador_sub2])),
                'alunos' => $row->alunos,
                'periodo' => $row->periodo,
                'cargo' => $row->cargo,
                'funcao' => $row->funcao,
                'nome_periodo' => $row->nome_periodo,
                'dias' => $faturamentoDias,
                'descontos' => $faturamentoDescontos,
                'horas' => $faturamentoHoras,
            ];

        }

        // recupera dados dos horarios de trabalho
        $horarioTrabalho = $this->db
            ->select('b.id, a.dia_semana, a.periodo')
            ->select("TIME_FORMAT(IFNULL(a.horario_inicio_mes{$idMes}, '00:00:00'), '%H:%ih') AS inicio", false)
            ->select("TIME_FORMAT(IFNULL(a.horario_termino_mes{$idMes}, '00:00:00'), '%H:%ih') AS termino", false)
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_matriculados_turmas d', 'd.id_alocado_horario = a.id', 'left')
            ->join('ei_matriculados e', 'e.id = d.id_matriculado AND e.id_alocacao_escola = c.id', 'left')
            ->where_in('b.id', $idAlocados)
            ->where_in('c.ordem_servico', $ordensServico)
            ->where('c.id_alocacao', $alocacao->id)
            ->where('c.escola', $alocacao->escola)
            ->where('a.cargo' . $mesCargoFuncao, $alocacao->cargo)
            ->where('a.funcao' . $mesCargoFuncao, $alocacao->funcao)
            ->group_by(['a.dia_semana', "a.horario_inicio_mes{$idMes}", "a.horario_termino_mes{$idMes}"])
            ->order_by('a.dia_semana', 'asc')
            ->order_by("a.horario_inicio_mes{$idMes}", 'asc')
            ->order_by("a.horario_termino_mes{$idMes}", 'asc')
            ->get('ei_alocados_horarios a')
            ->result();

        // formata a data da apresentacao da planilha
        $this->load->library('Calendar');
        $semana = $this->calendar->get_day_names('long');
        $diasSemana = [];
        foreach ($horarioTrabalho as $horario) {
            $strHorario = $horario->inicio . '-' . $horario->termino;
            if (isset($diasSemana[$strHorario])) {
                $diasSemana[$strHorario][0] = str_replace('-feira', '', $diasSemana[$strHorario][0]);
                $diasSemana[$strHorario][1] = ' à ' . str_replace('-feira', '', $semana[$horario->dia_semana]);
            } else {
                $diasSemana[$strHorario][0] = $semana[$horario->dia_semana];
                $diasSemana[$strHorario][1] = '';
            }
            $diasSemana[$strHorario][2] = ', das ' . $horario->inicio . ' às ' . $horario->termino;
        }

        $semestre = $alocacao->semestre ?? 1;
        $dataFaturamento = date('Y-m-d', mktime(0, 0, 0, (int)$mes, 1, $alocacao->ano));
        $dataAtual = $this->input->get('data_atual');
        if ($dataAtual) {
            $dataAtual = date('Y-m-d', strtotime(str_replace('/', '-', $dataAtual)));
        } else {
            $dataAtual = date('Y-m-d');
        }
        $observacoes = $this->input->get('observacoes');
        if (strlen($observacoes) == 0 or $recuperar) {
            $observacoes = $data->observacoes;
        }

        // retorna conjunto de dados para a view da planilha
        $planilha = [
            'empresa' => $empresa,
            'usuario' => $usuario,
            'mesFaturamento' => $this->calendar->get_month_name(date('m', strtotime($dataFaturamento))),
            'anoFaturamento' => date('Y', strtotime($dataFaturamento)),
            'query_string' => "id_alocado={$idAlocado}&mes={$mes}&semestre={$semestre}&periodo={$periodo}&cargo={$cargo}&funcao={$funcao}&substituto={$substituto}",
            'is_pdf' => $is_pdf,
            'contrato' => $data->contrato,
            'escola' => $data->escola,
            'ordemServico' => $data->ordem_servico,
            'alunos' => $data->alunos,
            'profissional' => implode(', ', array_filter([$data->cuidador, $data->cuidador_sub1, $data->cuidador_sub2])),
            'nomePeriodo' => $data->periodo_inicial . ' a ' . $data->periodo_final,
            'horasDescontadas' => $data->horas_descontadas,
            'observacoes' => $observacoes,
            'diasSemana' => array_values($diasSemana),
            'mesAno' => ucfirst($this->calendar->get_month_name($mes)) . '/' . $data->ano,
            'faturamentos' => $faturamentos,
            'supervisor' => $alocacao,
            'diaAtual' => date('d', strtotime($dataAtual)),
            'mesAtual' => $this->calendar->get_month_name(date('m', strtotime($dataAtual))),
            'anoAtual' => date('Y', strtotime($dataAtual))
        ];

        return $this->load->view('ei/planilha_faturamento', $planilha, true);
    }

    //--------------------------------------------------------------------

    public function ajax_edit_ajuste_mensal()
    {
        $idAlocado = $this->input->post('id_alocado');

        $alocacao = $this->db
            ->select('c.semestre')
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->where('a.id', $idAlocado)
            ->get('ei_alocados a')
            ->row();

        $idMes = intval($this->input->post('mes')) - (($alocacao->semestre ?? 1) > 1 ? 6 : 0);
        $periodo = $this->input->post('periodo');
        $substituto = $this->input->post('substituto');

        $qb = $this->db
            ->select("id, '{$idMes}' AS mes", false);
        if ($substituto === '2') {
            $qb->select("TIME_FORMAT(horas_descontadas_sub2, '%H:%i') AS horas_descontadas", false);
        } elseif ($substituto === '1') {
            $qb->select("TIME_FORMAT(horas_descontadas_sub1, '%H:%i') AS horas_descontadas", false);
        } else {
            $qb->select("TIME_FORMAT(horas_descontadas_mes{$idMes}, '%H:%i') AS horas_descontadas", false);
        }
        $data = $qb
            ->where('id_alocado', $idAlocado)
            ->where('periodo', $periodo)
            ->get('ei_alocados_totalizacao')
            ->row();

        if (empty($data)) {
            exit(json_encode(['erro' => 'O colaborador não existe ou foi desalocado do semestre.']));
        }

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_save_ajuste_mensal()
    {
        $id = $this->input->post('id');
        $mes = $this->input->post('mes');
        $horasDescontadas = $this->input->post('horas_descontadas');
        if (strlen($horasDescontadas) == 0) {
            $horasDescontadas = null;
        }

        $status = $this->db
            ->set('horas_descontadas_mes' . $mes, $horasDescontadas)
            ->where('id', $id)
            ->update('ei_alocados_totalizacao');

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function ajax_edit_pagamento_prestador()
    {
        $horario = $this->db
            ->select('a.id, c.id_alocacao, b.id AS id_alocado, b.id_cuidador, b.cuidador, a.periodo, d.ano, d.semestre')
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->where('a.id', $this->input->post('id_horario'))
            ->get('ei_alocados_horarios a')
            ->row();

        if (empty($horario)) {
            exit(json_encode(['erro' => 'O horário alocado não existe ou foi desalocado do semestre.']));
        }

        $mes = (int)$this->input->post('mes');
        $idMes = $mes - ($horario->semestre > 1 ? 6 : 0);
        $substituto = $this->input->post('substituto');

        $qb = $this->db
            ->select("DATE_FORMAT(MIN(h.data_inicio_contrato), '%d/%m/%Y') AS data_inicio_contrato", false)
            ->select("DATE_FORMAT(MAX(h.data_termino_contrato), '%d/%m/%Y') AS data_termino_contrato", false)
            ->select(["IF({$mes} IN (1, 8), d.pagamento_proporcional_inicio, IF({$mes} IN (7, 12), d.pagamento_proporcional_termino, 0)) AS pagamento_proporcional"], false)
            ->select('d.id_complementar_1, d.id_complementar_2', false)
            ->select(["i.nota_fiscal_mes{$idMes} AS numero_nota_fiscal_complementar_1"], false)
            ->select(["i.codigo_alfa_mes{$idMes} AS codigo_alfa_complementar_1"], false)
            ->select("DATE_FORMAT(i.data_emissao_mes{$idMes}, '%d/%m/%Y') AS data_emissao_complementar_1", false)
            ->select(["j.nota_fiscal_mes{$idMes} AS numero_nota_fiscal_complementar_2"], false)
            ->select(["j.codigo_alfa_mes{$idMes} AS codigo_alfa_complementar_2"], false)
            ->select("DATE_FORMAT(j.data_emissao_mes{$idMes}, '%d/%m/%Y') AS data_emissao_complementar_2", false);
        if ($substituto) {
            $qb->select("d.id, d.nota_fiscal_sub AS numero_nota_fiscal", false)
                ->select("DATE_FORMAT(d.data_emissao_sub, '%d/%m/%Y') AS data_emissao", false)
                ->select("DATE_FORMAT(d.data_solicitacao_nota_sub, '%d/%m/%Y') AS data_solicitacao_nota", false)
                ->select("TIME_FORMAT(d.falta_anterior_sub, '%H:%i') AS falta_anterior", false)
                ->select("TIME_FORMAT(d.falta_atual_sub, '%H:%i') AS falta_atual", false)
                ->select("TIME_FORMAT(d.desconto_anterior_sub, '%H:%i') AS desconto_anterior", false)
                ->select("TIME_FORMAT(d.desconto_atual_sub, '%H:%i') AS desconto_atual", false)
                ->select('d.codigo_alfa_sub AS codigo_alfa, d.status_sub AS status')
                ->select("FORMAT(d.valor_extra1_sub, 2, 'de_DE') AS valor_extra_1", false)
                ->select("FORMAT(d.valor_extra2_sub, 2, 'de_DE') AS valor_extra_2", false)
                ->select("d.justificativa1_sub AS justificativa_1", false)
                ->select("d.justificativa2_sub AS justificativa_2", false)
                ->select("DATE_FORMAT(d.data_liberacao_pagto_sub, '%d/%m/%Y') AS data_liberacao_pagto", false)
                ->select('d.preservar_edicao_sub AS preservar_edicao', false);
        } else {
            $qb->select("d.id, d.nota_fiscal_mes{$idMes} AS numero_nota_fiscal", false)
                ->select("DATE_FORMAT(d.data_emissao_mes{$idMes}, '%d/%m/%Y') AS data_emissao", false)
                ->select("DATE_FORMAT(d.data_solicitacao_nota_mes{$idMes}, '%d/%m/%Y') AS data_solicitacao_nota", false)
                ->select("TIME_FORMAT(d.falta_anterior_mes{$idMes}, '%H:%i') AS falta_anterior", false)
                ->select("TIME_FORMAT(d.falta_atual_mes{$idMes}, '%H:%i') AS falta_atual", false)
                ->select("TIME_FORMAT(d.desconto_anterior_mes{$idMes}, '%H:%i') AS desconto_anterior", false)
                ->select("TIME_FORMAT(d.desconto_atual_mes{$idMes}, '%H:%i') AS desconto_atual", false)
                ->select("d.codigo_alfa_mes{$idMes} AS codigo_alfa, d.status_mes{$idMes} AS status")
                ->select("FORMAT(d.valor_extra1_mes{$idMes}, 2, 'de_DE') AS valor_extra_1", false)
                ->select("FORMAT(d.valor_extra2_mes{$idMes}, 2, 'de_DE') AS valor_extra_2", false)
                ->select("d.justificativa1_mes{$idMes} AS justificativa_1", false)
                ->select("d.justificativa2_mes{$idMes} AS justificativa_2", false)
                ->select("DATE_FORMAT(d.data_liberacao_pagto_mes{$idMes}, '%d/%m/%Y') AS data_liberacao_pagto", false)
                ->select("d.preservar_edicao_mes{$idMes} AS preservar_edicao", false);
        }
        $data = $qb
            ->join('ei_alocacao_escolas b', 'b.id_alocacao = a.id')
            ->join('ei_alocados c', 'c.id_alocacao_escola = b.id')
            ->join('ei_pagamento_prestador d', 'd.id_alocacao = a.id AND d.id_cuidador = c.id_cuidador AND nota_complementar IS NULL', 'left')
            ->join('ei_alocados_horarios e', 'e.id_alocado = c.id', 'left')
            ->join('ei_matriculados_turmas f', 'f.id_alocado_horario = e.id', 'left')
            ->join('ei_matriculados g', 'g.id = f.id_matriculado AND g.id_alocacao_escola = b.id', 'left')
            ->join('ei_ordem_servico_horarios h', 'h.id = e.id_os_horario', 'left')
            ->join('ei_pagamento_prestador i', 'i.id = d.id_complementar_1', 'left')
            ->join('ei_pagamento_prestador j', 'j.id = d.id_complementar_2', 'left')
            ->where('a.id', $horario->id_alocacao)
            ->where('c.id_cuidador', $horario->id_cuidador)
            ->group_by('a.id')
            ->get('ei_alocacao a')
            ->row();

        $data->planilha_pagamento_prestador = $this->planilhaPagamentoPrestador($horario->id, $idMes, $horario->ano);
        $data->mes = $mes;

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_recuperar_pagamento_prestador()
    {
        $idHorario = $this->input->post('id_horario');
        $mes = $this->input->post('mes');
        $substituto = $this->input->post('substituto');
        $recuperar = $this->input->post('recuperar');

        $row = $this->db
            ->select('a.id_alocado, d.semestre, d.ano')
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->where('a.id', $idHorario)
            ->get('ei_alocados_horarios a')
            ->row();

        if (empty($row)) {
            exit(json_encode(['erro' => 'O colaborador não existe ou foi desalocado do semestre.']));
        }

        $row->mes = (int)$mes - ($row->semestre > 1 ? 6 : 0);

        $data['planilha_pagamento_prestador'] = $this->planilhaPagamentoPrestador($idHorario, $row->mes, $row->ano, false, $recuperar);

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_recuperar_pagamento_prestador_substituto()
    {
        $idHorario = $this->input->post('id_horario');
        $idMes = $this->input->post('id_mes');

        $alocacao = $this->db
            ->select('e.id, e.semestre, b.id_cuidador, a.id_alocado, a.periodo')
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('usuarios c', 'c.id = b.id_cuidador')
            ->join('ei_alocacao_escolas d', 'd.id = b.id_alocacao_escola')
            ->join('ei_alocacao e', 'e.id = d.id_alocacao')
            ->where('a.id', $idHorario)
            ->group_by('b.id')
            ->get('ei_alocados_horarios a')
            ->row();

        $substituto = $this->input->post('substituto');
        $substitutoSemestre = $this->input->post('substituto_semestre');
        if ($substituto) {
            $idUsuario = $substituto;
        } else {
            $idUsuario = $alocacao->id_cuidador;
        }

        $rowTotalizacoes = $this->db
            ->select('a.id, a.periodo, c.id_escola')
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->where('a.id_alocado', $alocacao->id_alocado)
            ->where('a.id_cuidador', $idUsuario)
            ->group_by(['c.id_escola', 'a.periodo'])
            ->get('ei_alocados_totalizacao a')
            ->result();

        $valores1 = [];
        foreach ($rowTotalizacoes as $rowTotalizacao) {
            $valores1[$rowTotalizacao->id_escola][$rowTotalizacao->periodo] = $rowTotalizacao->id;
        }

        $data = $this->db
            ->select('nome, cnpj, centro_custo, agencia_bancaria, conta_bancaria, nome_banco')
            ->where('id', $idUsuario)
            ->get('usuarios')
            ->row();

        $valores = $this->db
            ->select(["j.id AS id_totalizacao, c.id_escola, a.periodo, IFNULL(j.valor_pagamento_mes{$idMes} ,IFNULL(g.valor_pagamento, g.valor_pagamento2)) AS valor_pagamento"], false)
            ->select(["TIME_FORMAT(IFNULL(j.total_horas_faturadas_mes{$idMes} ,SEC_TO_TIME(SUM(IFNULL(TIME_TO_SEC(a.desconto_sub1), 0) + IFNULL(TIME_TO_SEC(a.desconto_sub2), 0)))), '%H:%i') AS total_horas_mes"], false)
            ->select(["IFNULL(j.valor_total_mes{$idMes}, IFNULL(g.valor_pagamento, g.valor_pagamento2) * SUM(IFNULL(TIME_TO_SEC(a.desconto_sub1), 0) + IFNULL(TIME_TO_SEC(a.desconto_sub2), 0)) / 3600) AS total"], false)
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_ordem_servico_escolas d', 'd.id = c.id_os_escola')
            ->join('ei_ordem_servico e', 'e.id = d.id_ordem_servico')
            ->join('ei_contratos f', 'f.id = e.id_contrato')
            ->join('usuarios h', 'h.id = a.id_alocado_sub1 OR h.id = a.id_alocado_sub2', 'left')
            ->join('empresa_funcoes i', 'i.id = h.id_funcao OR i.nome = h.funcao', 'left')
            ->join('ei_valores_faturamento g', 'g.id_contrato = f.id AND g.ano = e.ano AND g.semestre = e.semestre AND g.id_funcao = i.id', 'left')
            ->join('ei_alocados_totalizacao j', "j.id_alocado = b.id AND j.periodo = a.periodo AND j.id_cuidador = '{$idUsuario}'", 'left')
            ->where('b.id', $alocacao->id_alocado)
            ->where("(CHAR_LENGTH('{$substituto}') = 0 OR h.id = '{$idUsuario}')")
            ->group_by(['c.id_escola', 'a.periodo', 'h.id'])
            ->get('ei_apontamento a')
            ->result();

        $valores2 = [];
        $valores3 = [];
        $valores4 = [];
        foreach ($valores as $bu) {
            $valores2[$bu->id_escola][$bu->periodo] = $bu->total_horas_mes;
            $valores3[$bu->id_escola][$bu->periodo] = $bu->valor_pagamento;
            $valores4[$bu->id_escola][$bu->periodo] = $bu->total;
        }

        $qb = $this->db;
        if ($substitutoSemestre) {
            $qb->select("e.valor_extra1_mes{$idMes} AS valor_extra1", false)
                ->select("e.valor_extra2_mes{$idMes} AS valor_extra2", false);
        } else {
            $qb->select("e.valor_extra1_sub AS valor_extra1", false)
                ->select("e.valor_extra2_sub AS valor_extra2", false);
        }
        $pagamentoPrestador = $qb
            ->join('ei_diretorias a2', 'a2.id = a.id_diretoria')
            ->join('usuarios a3', 'a3.id = a2.id_coordenador')
            ->join('ei_alocacao_escolas b', 'b.id_alocacao = a.id')
            ->join('ei_alocados c', 'c.id_alocacao_escola = b.id')
            ->join('ei_ordem_servico_profissionais c2', 'c2.id = c.id_os_profissional')
            ->join('ei_ordem_servico_escolas c3', 'c3.id = c2.id_ordem_servico_escola')
            ->join('ei_ordem_servico c4', 'c4.id = c3.id_ordem_servico')
            ->join('ei_contratos c5', 'c5.id = c4.id_contrato')
            ->join('ei_valores_faturamento c6', 'c6.id_contrato = c5.id AND c6.ano = c4.ano AND c6.semestre = c4.semestre AND c6.id_funcao = c2.id_funcao', 'left')
            ->join('usuarios d', 'd.id = c.id_cuidador')
            ->join('ei_ordem_servico_horarios c21', 'c21.id_os_profissional = c2.id', 'left')
            ->join('ei_pagamento_prestador e', 'e.id_alocacao = a.id AND e.id_cuidador = c.id_cuidador', 'left')
            ->join('ei_alocados_horarios f', 'f.id_alocado = c.id', 'left')
            ->join('usuarios g', 'g.id = f.id_cuidador_sub1', 'left')
            ->join('usuarios h', 'h.id = f.id_cuidador_sub2', 'left')
            ->where('a.id', $alocacao->id)
            ->where('c.id_cuidador', $alocacao->id_cuidador)
            ->group_by('c.id_cuidador')
            ->get('ei_alocacao a')
            ->row();

        $totalizacoes = $this->db
            ->select(["TIME_FORMAT(IFNULL(d.total_horas_faturadas_mes{$idMes}, 0), '%H:%i') AS total_horas_mes"], false)
            ->select(["IFNULL(d.valor_pagamento_mes{$idMes}, 0) AS valor_hora_operacional"], false)
            ->select(["IFNULL(d.valor_total_mes{$idMes}, 0) AS valor_total"], false)
            ->select('c.id_escola, a.periodo')
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocados_totalizacao d', 'd.id_alocado = b.id AND d.periodo = a.periodo')
            ->join('ei_ordem_servico_horarios e', 'e.id = a.id_os_horario')
            ->join('ei_ordem_servico_profissionais f', 'f.id = e.id_os_profissional')
            ->join('ei_ordem_servico_escolas g', 'g.id = f.id_ordem_servico_escola')
            ->join('ei_ordem_servico h', 'h.id = g.id_ordem_servico')
            ->join('ei_contratos i', 'i.id = h.id_contrato')
            ->join('ei_valores_faturamento j', 'j.id_contrato = i.id AND j.ano = h.ano AND j.semestre = h.semestre AND j.id_funcao = e.id_funcao', 'left')
            ->where('d.id_cuidador', $alocacao->id_cuidador)
            ->where('c.id_alocacao', $alocacao->id)
            ->group_by(['c.id_escola', 'a.periodo'])
            ->get('ei_alocados_horarios a')
            ->result();

        $this->load->helper('time');

        $servicos = [];
        $soma = round($pagamentoPrestador->valor_extra1, 2, PHP_ROUND_HALF_DOWN);
        $soma += round($pagamentoPrestador->valor_extra2, 2, PHP_ROUND_HALF_DOWN);
        foreach ($totalizacoes as $totalizacao) {
            $servicos[] = [
                'id_totalizacao' => $valores1[$totalizacao->id_escola][$totalizacao->periodo] ?? null,
                'qtdeHoras' => $substituto ? ($valores2[$totalizacao->id_escola][$totalizacao->periodo] ?? null) : $totalizacao->total_horas_mes,
                'valorCustoProfissional' => number_format(round($substituto ? ($valores3[$totalizacao->id_escola][$totalizacao->periodo] ?? null) : $totalizacao->valor_hora_operacional, 2, PHP_ROUND_HALF_DOWN), 2, ',', '.'),
                'total' => number_format(round($substituto ? ($valores4[$totalizacao->id_escola][$totalizacao->periodo] ?? null) : $totalizacao->valor_total, 2, PHP_ROUND_HALF_DOWN), 2, ',', '.'),
            ];

            $soma += round($totalizacao->valor_total, 2, PHP_ROUND_HALF_DOWN);
        }

        $data->servicos = $servicos;
        $data->soma = $soma;

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function ajax_save_pagamento_prestador()
    {
        $idHorario = $this->input->post('id_horario');
        $idMes = $this->getIdMes($this->input->post('mes'), $this->input->post('semestre'));
        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';

        $alocado = $this->db
            ->select('c.id_alocacao, b.id_cuidador, d.semestre, b.id_alocacao_escola, c.escola')
            ->select("b.cuidador, a.cargo{$mesCargoFuncao} AS cargo, a.funcao{$mesCargoFuncao} AS funcao")
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->where('a.id', $idHorario)
            ->get('ei_alocados_horarios a')
            ->row();

        $mes = (int)$this->input->post('mes');
        $idMes = $this->getIdMes($mes, $alocado->semestre);
        $numeroNotaFiscal = $this->input->post('numero_nota_fiscal');
        $dataEmissao = $this->input->post('data_emissao');
        $dataSolicitacaoNota = $this->input->post('data_solicitacao_nota');
        $codigoAlfa = $this->input->post('codigo_alfa');
        $status = $this->input->post('status');
        $substituto = $this->input->post('substituto');
        $idAlocadoSub = $this->input->post('id_alocado_sub');

        if (strlen($numeroNotaFiscal) == 0) {
            $numeroNotaFiscal = null;
        }
        if (strlen($dataEmissao) > 0) {
            $dataEmissao = date('Y-m-d', strtotime(str_replace('/', '-', $dataEmissao)));
        } else {
            $dataEmissao = null;
        }
        if (strlen($dataSolicitacaoNota) > 0) {
            $dataSolicitacaoNota = date('Y-m-d', strtotime(str_replace('/', '-', $dataSolicitacaoNota)));
        } else {
            $dataSolicitacaoNota = null;
        }
        if (strlen($codigoAlfa) == 0) {
            $codigoAlfa = null;
        }
        if (strlen($status) == 0) {
            $status = null;
        }
        $dataLiberacaoPagto = $this->input->post('data_liberacao_pagamento');
        if ($dataLiberacaoPagto) {
            $dataLiberacaoPagto = date('Y-m-d', strtotime(str_replace('/', '-', $dataLiberacaoPagto)));
        } else {
            $dataLiberacaoPagto = null;
        }
        $dataInicioContrato = $this->input->post('data_inicio_contrato');
        if ($dataInicioContrato) {
            $dataInicioContrato = date('Y-m-d', strtotime(str_replace('/', '-', $dataInicioContrato)));
        } else {
            $dataInicioContrato = null;
        }
        $dataTerminoContrato = $this->input->post('data_termino_contrato');
        if ($dataTerminoContrato) {
            $dataTerminoContrato = date('Y-m-d', strtotime(str_replace('/', '-', $dataTerminoContrato)));
        } else {
            $dataTerminoContrato = null;
        }
        $valorExtra1 = $this->input->post('valor_extra_1');
        if ($valorExtra1) {
            $valorExtra1 = str_replace(['.', ','], ['', '.'], $valorExtra1);
        } else {
            $valorExtra1 = null;
        }
        $valorExtra2 = $this->input->post('valor_extra_2');
        if ($valorExtra2) {
            $valorExtra2 = str_replace(['.', ','], ['', '.'], $valorExtra2);
        } else {
            $valorExtra2 = null;
        }
        $faltaAnterior = $this->input->post('falta_anterior');
        if (strlen($faltaAnterior) == 0) {
            $faltaAnterior = null;
        }
        $faltaAtual = $this->input->post('falta_atual');
        if (strlen($faltaAtual) == 0) {
            $faltaAtual = null;
        }
        $descontoAnterior = $this->input->post('desconto_anterior');
        if (strlen($descontoAnterior) == 0) {
            $descontoAnterior = null;
        }
        $descontoAtual = $this->input->post('desconto_atual');
        if (strlen($descontoAtual) == 0) {
            $descontoAtual = null;
        }
        $justificativa1 = $this->input->post('justificativa_1');
        if (strlen($justificativa1) == 0) {
            $justificativa1 = null;
        }
        $justificativa2 = $this->input->post('justificativa_2');
        if (strlen($justificativa2) == 0) {
            $justificativa2 = null;
        }
        $tipoPagamento = $this->input->post('tipo_pagamento');
        if (strlen($tipoPagamento) == 0) {
            $tipoPagamento = null;
        }
        $observacoes = $this->input->post('observacoes');
        if (strlen($observacoes) == 0) {
            $observacoes = null;
        }
        $preservarEdicao = $this->input->post('preservar_edicao');
        if (strlen($preservarEdicao) == 0) {
            $preservarEdicao = null;
        }

        $pagamentoProporcional = $this->input->post('pagamento_proporcional');

        $idCuidador = $alocado->id_cuidador;

        $data = [
            'id_alocacao' => $alocado->id_alocacao,
            'id_cuidador' => $idCuidador,
            'cuidador' => $alocado->cuidador,
            'cargo' => $alocado->cargo,
            'funcao' => $alocado->funcao,
            'nota_complementar' => null,
            'pagamento_proporcional_inicio' => in_array($mes, [1, 7]) ? $pagamentoProporcional : null,
            'pagamento_proporcional_termino' => in_array($mes, [7, 12]) ? $pagamentoProporcional : null,
        ];

        if ($substituto) {
            $data['data_liberacao_pagto_sub'] = $dataLiberacaoPagto;
            $data['data_inicio_contrato_sub'] = $dataInicioContrato;
            $data['data_termino_contrato_sub'] = $dataTerminoContrato;
            $data['nota_fiscal_sub'] = $numeroNotaFiscal;
            $data['data_emissao_sub'] = $dataEmissao;
            $data['data_solicitacao_nota_sub'] = $dataSolicitacaoNota;
            $data['codigo_alfa_sub'] = $codigoAlfa;
            $data['status_sub'] = $status;
            $data['valor_extra1_sub'] = $valorExtra1;
            $data['valor_extra2_sub'] = $valorExtra2;
            $data['falta_anterior_sub'] = $faltaAnterior;
            $data['falta_atual_sub'] = $faltaAtual;
            $data['desconto_anterior_sub'] = $descontoAnterior;
            $data['desconto_atual_sub'] = $descontoAtual;
            $data['justificativa1_sub'] = $justificativa1;
            $data['justificativa2_sub'] = $justificativa2;
            $data['tipo_pagamento_sub'] = $tipoPagamento;
            $data['observacoes_sub'] = $observacoes;
            $data['preservar_edicao_sub'] = $preservarEdicao;
        } else {
            $data['data_liberacao_pagto_mes' . $idMes] = $dataLiberacaoPagto;
            $data['data_inicio_contrato_mes' . $idMes] = $dataInicioContrato;
            $data['data_termino_contrato_mes' . $idMes] = $dataTerminoContrato;
            $data['nota_fiscal_mes' . $idMes] = $numeroNotaFiscal;
            $data['data_emissao_mes' . $idMes] = $dataEmissao;
            $data['data_solicitacao_nota_mes' . $idMes] = $dataSolicitacaoNota;
            $data['codigo_alfa_mes' . $idMes] = $codigoAlfa;
            $data['status_mes' . $idMes] = $status;
            $data['valor_extra1_mes' . $idMes] = $valorExtra1;
            $data['valor_extra2_mes' . $idMes] = $valorExtra2;
            $data['falta_anterior_mes' . $idMes] = $faltaAnterior;
            $data['falta_atual_mes' . $idMes] = $faltaAtual;
            $data['desconto_anterior_mes' . $idMes] = $descontoAnterior;
            $data['desconto_atual_mes' . $idMes] = $descontoAtual;
            $data['justificativa1_mes' . $idMes] = $justificativa1;
            $data['justificativa2_mes' . $idMes] = $justificativa2;
            $data['tipo_pagamento_mes' . $idMes] = $tipoPagamento;
            $data['observacoes_mes' . $idMes] = $observacoes;
            $data['preservar_edicao_mes' . $idMes] = $preservarEdicao;
        }

        $this->db->trans_start();

        $id = $this->input->post('id');

        $pagamentoPrestador = $this->db
            ->where(['id' => $id, 'id_cuidador' => $idCuidador])
            ->get('ei_pagamento_prestador')
            ->num_rows();

        if ($pagamentoPrestador) {
            $this->db->update('ei_pagamento_prestador', $data, ['id' => $id]);
        } else {
            $this->db->insert('ei_pagamento_prestador', $data);
            $id = $this->db->insert_id();
        }

        $nf1 = $this->input->post('numero_nota_fiscal_complementar_1');
        $idComplementar1 = $this->input->post('id_complementar_1') ?: null;
        if ($this->input->post('remover_complementar_1')) {
            $this->db->delete('ei_pagamento_prestador', ['id' => $idComplementar1]);
        } else {
            if ($nf1) {
                $dataComplementar1 = $data;
                $dataComplementar1['nota_fiscal_mes' . $idMes] = $nf1;
                $dataComplementar1['nota_complementar'] = 1;
                $dataComplementar1['data_emissao_mes' . $idMes] = strToDate($this->input->post('data_emissao_complementar_1'));
                $dataComplementar1['codigo_alfa_mes' . $idMes] = $this->input->post('codigo_alfa_complementar_1');
                if ($idComplementar1) {
                    $this->db->update('ei_pagamento_prestador', $dataComplementar1, ['id' => $idComplementar1]);
                } else {
                    $this->db->insert('ei_pagamento_prestador', $dataComplementar1);
                    $idComplementar1 = $this->db->insert_id();
                }
            }
        }

        $nf2 = $this->input->post('numero_nota_fiscal_complementar_2');
        $idComplementar2 = $this->input->post('id_complementar_2') ?: null;
        if ($this->input->post('remover_complementar_2')) {
            $this->db->delete('ei_pagamento_prestador', ['id' => $idComplementar2]);
        } else {
            if ($nf2) {
                $dataComplementar2 = $data;
                $dataComplementar2['nota_fiscal_mes' . $idMes] = $nf2;
                $dataComplementar2['nota_complementar'] = 1;
                $dataComplementar2['data_emissao_mes' . $idMes] = strToDate($this->input->post('data_emissao_complementar_2'));
                $dataComplementar2['codigo_alfa_mes' . $idMes] = $this->input->post('codigo_alfa_complementar_2');
                if ($idComplementar2) {
                    $this->db->update('ei_pagamento_prestador', $dataComplementar2, ['id' => $idComplementar2]);
                } else {
                    $this->db->insert('ei_pagamento_prestador', $dataComplementar2);
                    $idComplementar2 = $this->db->insert_id();
                }
            }
        }

        $this->db
            ->set('id_complementar_1', $idComplementar1)
            ->set('id_complementar_2', $idComplementar2)
            ->where('id', $id)
            ->update('ei_pagamento_prestador');

        if (is_null($preservarEdicao)) {
            $idTotalizacao = $this->input->post('id_totalizacao');
            if (empty($idTotalizacao)) {
                $idTotalizacao = [];
            }
            $totalHorasFaturadas = $this->input->post('total_horas_faturadas');
            $valorPagamento = $this->input->post('valor_pagamento');
            $valorTotal = $this->input->post('valor_total');

            foreach ($idTotalizacao as $k => $totalizacao) {
                $qb = $this->db
                    ->set('total_horas_faturadas_mes' . $idMes, $totalHorasFaturadas[$k])
                    ->set('valor_pagamento_mes' . $idMes, str_replace(['.', ','], ['', '.'], $valorPagamento[$k]))
                    ->set('valor_total_mes' . $idMes, str_replace(['.', ','], ['', '.'], $valorTotal[$k]));
                if ($totalizacao) {
                    $qb->where('id', $totalizacao)->update('ei_alocados_totalizacao');
                } else {
                    $qb->insert('ei_alocados_totalizacao');
                }
            }
        }

        $this->db->trans_complete();

        $status = $this->db->trans_status();

        echo json_encode(['status' => $status]);
    }

    //--------------------------------------------------------------------

    private function getIdMes(?string $mes, ?int $semestre): int
    {
        $semestre = intval($mes) > 7 ? 2 : (intval($mes) < 7 ? 1 : $semestre);
        return $mes - ($semestre > 1 ? 6 : 0);
    }

    //--------------------------------------------------------------------

    public function notificar_pagamento_prestador()
    {
        $id = $this->input->post('id');
        $idMes = $this->input->post('id_mes');
        $substituto = $this->input->post('substituto');

        $sufixo = strlen($substituto) > 0 ? 'sub' : 'mes' . $idMes;
        $data = ['data_solicitacao_nota_' . $sufixo => date('Y-m-d')];

        if ($this->db->update('ei_pagamento_prestador', $data, ['id' => $id]) == false) {
            exit(json_encode(['erro' => 'Erro ao notificar solicitação de pagamento']));
        }

        $pgto = $this->db->select('a.id_cuidador, d.nome, d.email, d.telefone')
            ->join('ei_alocacao b', 'b.id = a.id_alocacao')
            ->join('ei_diretorias c', 'c.id = b.id_diretoria')
            ->join('usuarios d', 'd.id = a.id_cuidador')
            ->where('a.id', $id)
            ->get('ei_pagamento_prestador a')
            ->row();

        $usuario = $this->db
            ->select('nome, email')
            ->where('id', $pgto->id_cuidador)
            ->get('usuarios')
            ->row();

        if (empty($usuario)) {
            exit(json_encode(['erro' => 'Profissional não encontrado.']));
        }

        $this->load->library('email');
        $this->load->helper('time');

        $data = [
            'logoEmpresa' => 'imagens/usuarios/' . $this->session->userdata('logomarca'),
            'usuario' => $usuario->nome,
        ];

        $this->email
            ->set_mailtype('html')
            ->from('contato@rhsuite.com.br', 'RhSuite')
            ->to($usuario->email)
//            ->cc('apoio.icom@ame-sp.org.br')
            ->subject($usuario->nome . ' - Notificação de Pagamento de Educação Inclusiva')
            ->message($this->load->view('ei/email_notificacao_pagamento_prestador', $data, true));

        if ($this->email->send() == false) {
            exit(json_encode(['erro' => 'Não foi possível enviar o e-mail, tente novamente.']));
        }

        echo json_encode(['status' => true]);
    }

    //--------------------------------------------------------------------

    public function pdf_pagamento_prestador()
    {
        $this->load->library('m_pdf');

        $stylesheet = '#table thead tr th { border-top: 4px solid #ddd; padding-top: 8px; } ';
        $stylesheet .= '#table { border-bottom: 1px solid #ddd; } ';
        $stylesheet .= '#periodo { border: 1px solid #444; margin-bottom: 0px; } ';
        $stylesheet .= '#periodo thead th { font-size: 11px; padding: 4px; background-color: #f5f5f5; border: 1px solid #444; } ';
        $stylesheet .= '#periodo tbody td { font-size: 11px; padding: 4px; vertical-align: top; border: 1px solid #444; } ';
        $stylesheet .= 'p strong { font-weight: bold; }';

        $this->m_pdf->pdf->writeHTML($stylesheet, 1);
        $idHorario = $this->input->get('horario');
        $substituto = $this->input->get('substituto');
        $alocadoBck = $this->input->get('alocado_bck');

        $mes = $this->input->get('mes');
        $idMes = (int)$mes - ($this->input->get('semestre') > 1 ? 6 : 0);
        $ano = $this->input->get('ano');
        $this->m_pdf->pdf->writeHTML($this->planilhaPagamentoPrestador($idHorario, $idMes, $ano, true));

        if ($alocadoBck) {
            $nomeProfissional = $this->db
                    ->select('nome')
                    ->where('id', $alocadoBck)
                    ->get('usuarios')
                    ->row()
                    ->nome ?? '';
        } else {
            $qb = $this->db;
            if ($substituto) {
                $qb->select('c.nome AS cuidador');
            } else {
                $qb->select('b.cuidador');
            }
            $qb->join('ei_alocados b', 'b.id = a.id_alocado');
            if ($substituto === '2') {
                $qb->join('usuarios c', 'c.id = a.id_cuidador_sub2');
            } elseif ($substituto === '1') {
                $qb->join('usuarios c', 'c.id = a.id_cuidador_sub1');
            }
            $nomeProfissional = $qb
                    ->where('a.id', $idHorario)
                    ->get('ei_alocados_horarios a')
                    ->row()
                    ->cuidador ?? '';
        }

        $this->load->library('Calendar');
        $mes = $this->calendar->get_month_name($mes);

        $this->m_pdf->pdf->Output("PP-{$nomeProfissional} - {$mes}/{$ano}.pdf", 'D');
    }

    //--------------------------------------------------------------------

    private function planilhaPagamentoPrestador(?int $idHorario, ?int $idMes, ?int $ano, ?bool $is_pdf = false, ?bool $recuperar = false): string
    {
        // prepara o cabecalho da planilha
        $empresa = $this->db
            ->select('foto, foto_descricao')
            ->where('id', $this->session->userdata('empresa'))
            ->get('usuarios')
            ->row();

        $usuario = $this->db
            ->select('nome, email')
            ->where('id', $this->session->userdata('id'))
            ->get('usuarios')
            ->row();

        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';

        // retorna dados da alocacao
        $qb = $this->db
            ->select("d.id, d.semestre, b.id_cuidador, a.id_alocado, a.periodo, a.cargo{$mesCargoFuncao} AS cargo, a.funcao{$mesCargoFuncao} AS funcao");
        if ($idMes) {
            $qb->select("d.dia_fechamento_mes{$idMes} AS dia_fechamento", false);
        } else {
            $qb->select("NULL AS dia_fechamento", false);
        }
        $alocacao = $qb
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->where('a.id', $idHorario)
            ->get('ei_alocados_horarios a')
            ->row();

        $idAlocado = $alocacao->id_alocado;

        // prepara variaveis locais
        $substituto = $this->input->get_post('substituto');
        $usoHorasFaturadas = $this->input->get_post('uso_horas_faturadas');
        $semestre = $alocacao->semestre ?? 1;

        $mes = str_pad($idMes + (intval($semestre) > 1 ? 6 : 0), 2, '0', STR_PAD_LEFT);

        // recupera dados do cuidador
        $cuidador = $this->db
            ->select(["(CASE WHEN '{$mes}' > MONTH(a.data_substituicao1) AND a.data_substituicao1 IS NOT NULL THEN a.id_cuidador_sub1 ELSE b.id_cuidador END) AS id"], false)
            ->select(["(CASE WHEN '{$mes}' > MONTH(a.data_substituicao1) AND a.data_substituicao1 IS NOT NULL THEN c.nome ELSE b.cuidador END) AS nome"], false)
            ->select(["(CASE WHEN '{$mes}' = MONTH(a.data_substituicao1) AND a.data_substituicao1 IS NOT NULL THEN a.id_cuidador_sub1 END) AS id_sub"], false)
            ->select(["(CASE WHEN '{$mes}' = MONTH(a.data_substituicao1) AND a.data_substituicao1 IS NOT NULL THEN c.nome END) AS nome_sub"], false)
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('usuarios c', 'c.id = a.id_cuidador_sub1', 'left')
            ->where('b.id', $alocacao->id_alocado)
            ->where('a.periodo', $alocacao->periodo)
            ->where('a.cargo' . $mesCargoFuncao, $alocacao->cargo)
            ->where('a.funcao' . $mesCargoFuncao, $alocacao->funcao)
            ->get('ei_alocados_horarios a')
            ->row();

        $idCuidador = $substituto ? $cuidador->id_sub : $cuidador->id;
        $nomeCuidador = $substituto ? $cuidador->nome_sub : $cuidador->nome;

        // recupera dados da apresentacao da planilha
        $qb = $this->db
            ->select('a3.nome AS solicitante, a.depto', false)
            ->select(["IF(a2.telefone, CONCAT(55, TRIM(a2.telefone)), NULL) AS telefone_diretoria"], false)
            ->select("SUM(IF(ef1.id IS NOT NULL, c2.valor_hora_operacional, 0) +
                          IF(ef2.id IS NOT NULL, c2.valor_hora_operacional_2, 0) +
                          IF(ef3.id IS NOT NULL, c2.valor_hora_operacional_3, 0) +
                          IF(ef4.id IS NOT NULL, c2.valor_hora_operacional_1t, 0) +
                          IF(ef5.id IS NOT NULL, c2.valor_hora_operacional_2t, 0) +
                          IF(ef6.id IS NOT NULL, c2.valor_hora_operacional_3t, 0) +
                          IF(ef7.id IS NOT NULL, c2.valor_hora_operacional_1n, 0) +
                          IF(ef8.id IS NOT NULL, c2.valor_hora_operacional_2n, 0) +
                          IF(ef9.id IS NOT NULL, c2.valor_hora_operacional_3n, 0)) AS valor_hora_operacional", false)
            ->select("FORMAT(c6.valor_pagamento, 2, 'de_DE') AS valor_pagamento", false)
            ->select("FORMAT(c6.valor_pagamento2, 2, 'de_DE') AS valor_pagamento2", false)
            ->select(["GROUP_CONCAT(DISTINCT h.nome ORDER BY h.nome SEPARATOR ', ') AS cuidador_sub1"], false)
            ->select(["GROUP_CONCAT(DISTINCT i.nome ORDER BY i.nome SEPARATOR ', ') AS cuidador_sub2"], false);
        if ($substituto) {
            $qb->select('g.nome AS cuidador, g.cnpj, g.centro_custo, g.nome_banco, g.agencia_bancaria, g.conta_bancaria')
                ->select("e.valor_extra1_sub AS valor_extra1", false)
                ->select("e.valor_extra2_sub AS valor_extra2", false)
                ->select("e.justificativa1_sub AS justificativa1", false)
                ->select("e.justificativa2_sub AS justificativa2", false)
                ->select("IFNULL(e.tipo_pagamento_sub, 1) AS tipo_pagamento", false)
                ->select("e.observacoes_sub AS observacoes", false);
        } else {
            $qb->select('g.nome AS cuidador, g.cnpj, g.centro_custo, g.nome_banco, g.agencia_bancaria, g.conta_bancaria')
                ->select("e.valor_extra1_mes{$idMes} AS valor_extra1", false)
                ->select("e.valor_extra2_mes{$idMes} AS valor_extra2", false)
                ->select("e.justificativa1_mes{$idMes} AS justificativa1", false)
                ->select("e.justificativa2_mes{$idMes} AS justificativa2", false)
                ->select("IFNULL(e.tipo_pagamento_mes{$idMes}, 1) AS tipo_pagamento", false)
                ->select("e.observacoes_mes{$idMes} AS observacoes", false);
        }
        $pagamentoPrestador = $qb
            ->select('a3.nome AS solicitante, a.depto', false)
            ->select(["IF(a2.telefone, CONCAT(55, TRIM(a2.telefone)), NULL) AS telefone_diretoria"], false)
            ->select("SUM(IF(ef1.id IS NOT NULL, c2.valor_hora_operacional, 0) +
                          IF(ef2.id IS NOT NULL, c2.valor_hora_operacional_2, 0) +
                          IF(ef3.id IS NOT NULL, c2.valor_hora_operacional_3, 0) +
                          IF(ef4.id IS NOT NULL, c2.valor_hora_operacional_1t, 0) +
                          IF(ef5.id IS NOT NULL, c2.valor_hora_operacional_2t, 0) +
                          IF(ef6.id IS NOT NULL, c2.valor_hora_operacional_3t, 0) +
                          IF(ef7.id IS NOT NULL, c2.valor_hora_operacional_1n, 0) +
                          IF(ef8.id IS NOT NULL, c2.valor_hora_operacional_2n, 0) +
                          IF(ef9.id IS NOT NULL, c2.valor_hora_operacional_3n, 0)) AS valor_hora_operacional", false)
            ->select("FORMAT(c6.valor_pagamento, 2, 'de_DE') AS valor_pagamento", false)
            ->select("FORMAT(c6.valor_pagamento2, 2, 'de_DE') AS valor_pagamento2", false)
            ->select(["GROUP_CONCAT(DISTINCT h.nome ORDER BY h.nome SEPARATOR ', ') AS cuidador_sub1"], false)
            ->select(["GROUP_CONCAT(DISTINCT i.nome ORDER BY i.nome SEPARATOR ', ') AS cuidador_sub2"], false)
            ->join('ei_diretorias a2', 'a2.id = a.id_diretoria')
            ->join('usuarios a3', 'a3.id = a2.id_coordenador')
            ->join('ei_alocacao_escolas b', 'b.id_alocacao = a.id')
            ->join('ei_alocados c', 'c.id_alocacao_escola = b.id')
            ->join('ei_ordem_servico_profissionais c2', 'c2.id = c.id_os_profissional', 'left')
            ->join('ei_ordem_servico_escolas c3', 'c3.id = c2.id_ordem_servico_escola', 'left')
            ->join('ei_ordem_servico c4', 'c4.id = c3.id_ordem_servico', 'left')
            ->join('ei_contratos c5', 'c5.id = c4.id_contrato', 'left')
            ->join('ei_valores_faturamento c6', 'c6.id_contrato = c5.id AND c6.ano = c4.ano AND c6.semestre = c4.semestre AND c6.id_funcao = c2.id_funcao', 'left')
            ->join('usuarios d', 'd.id = c.id_cuidador', 'left')
            ->join('ei_ordem_servico_horarios c21', 'c21.id_os_profissional = c2.id', 'left')
            ->join('ei_pagamento_prestador e', 'e.id_alocacao = a.id AND e.id_cuidador = c.id_cuidador', 'left')
            ->join('ei_alocados_horarios f', 'f.id_alocado = c.id', 'left')
            ->join('ei_alocados_totalizacao c0', "c0.id_alocado = c.id AND c0.periodo = f.periodo AND c0.cargo{$mesCargoFuncao} = f.cargo{$mesCargoFuncao} AND c0.funcao{$mesCargoFuncao} = f.funcao{$mesCargoFuncao} AND c0.id_cuidador = '{$idCuidador}'", 'left', false)
            ->join('usuarios g', 'g.id = c0.id_cuidador', 'left')
            ->join('usuarios h', 'h.id = f.id_cuidador_sub1', 'left')
            ->join('usuarios i', 'i.id = f.id_cuidador_sub2', 'left')
            ->join('empresa_funcoes ef1', 'ef1.id = c2.id_funcao', 'left')
            ->join('empresa_funcoes ef2', 'ef2.id = c2.id_funcao_2m', 'left')
            ->join('empresa_funcoes ef3', 'ef3.id = c2.id_funcao_3m', 'left')
            ->join('empresa_funcoes ef4', 'ef4.id = c2.id_funcao_1t', 'left')
            ->join('empresa_funcoes ef5', 'ef5.id = c2.id_funcao_2t', 'left')
            ->join('empresa_funcoes ef6', 'ef6.id = c2.id_funcao_3t', 'left')
            ->join('empresa_funcoes ef7', 'ef7.id = c2.id_funcao_1n', 'left')
            ->join('empresa_funcoes ef8', 'ef8.id = c2.id_funcao_2n', 'left')
            ->join('empresa_funcoes ef9', 'ef9.id = c2.id_funcao_3n', 'left')
            ->where('a.id', $alocacao->id)
            ->where('c.id_cuidador', $alocacao->id_cuidador)
            ->group_by('c.id_cuidador')
            ->get('ei_alocacao a')
            ->row();

        // recupera dados dos substitutos de eventos
        $substitutoEvento = $this->input->get_post('alocado_bck');
        if ($substitutoEvento and $pagamentoPrestador) {
            $rowSubstitutoEvento = $this->db
                ->select('nome, cnpj, telefone, centro_custo, agencia_bancaria, conta_bancaria, nome_banco')
                ->where('id', $substitutoEvento)
                ->get('usuarios')
                ->row();

            // substitui dados de pagamento de acordo com a condicao de substituicao de evento
            $pagamentoPrestador->cuidador = $rowSubstitutoEvento->nome;
            $pagamentoPrestador->cnpj = $rowSubstitutoEvento->cnpj;
            $pagamentoPrestador->centro_custo = $rowSubstitutoEvento->centro_custo;
            $pagamentoPrestador->nome_banco = $rowSubstitutoEvento->nome_banco;
            $pagamentoPrestador->agencia_bancaria = $rowSubstitutoEvento->agencia_bancaria;
            $pagamentoPrestador->conta_bancaria = $rowSubstitutoEvento->conta_bancaria;
        }

        // prepara a lista de substitutos de eventos
        $rowSubstitutosEventos = $this->db
            ->select('b.id, b.nome')
            ->join('usuarios b', 'b.id = a.id_alocado_sub1 OR b.id = a.id_alocado_sub2')
            ->where('a.id_alocado', $alocacao->id_alocado)
            ->group_by(['b.id', 'b.nome'])
            ->order_by('b.nome', 'asc')
            ->get('ei_apontamento a')
            ->result();

        $substitutosEventos = ['' => 'selecione...'] + array_column($rowSubstitutosEventos, 'nome', 'id');

        // prepara os dados de faturamento
        $rowValoresFaturamento = $this->db
            ->select(["c.id_escola, a.periodo, IFNULL(g.valor_pagamento, g.valor_pagamento2) AS valor_pagamento"], false)
            ->select(["TIME_FORMAT(SEC_TO_TIME(SUM(IFNULL(TIME_TO_SEC(a.desconto_sub1), 0) + IFNULL(TIME_TO_SEC(a.desconto_sub2), 0))), '%H:%i') AS total_horas_mes"], false)
            ->select(["TIME_FORMAT(SEC_TO_TIME(IFNULL(g.valor_pagamento, g.valor_pagamento2) * SUM(IFNULL(TIME_TO_SEC(a.desconto_sub1), 0) + IFNULL(TIME_TO_SEC(a.desconto_sub2), 0))), '%H:%i') AS total"], false)
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_ordem_servico_escolas d', 'd.id = c.id_os_escola')
            ->join('ei_ordem_servico e', 'e.id = d.id_ordem_servico')
            ->join('ei_contratos f', 'f.id = e.id_contrato')
            ->join('usuarios h', 'h.id = a.id_alocado_sub1 OR h.id = a.id_alocado_sub2', 'left')
            ->join('empresa_funcoes i', 'i.id = h.id_funcao OR i.nome = h.funcao', 'left')
            ->join('ei_valores_faturamento g', 'g.id_contrato = f.id AND g.ano = e.ano AND g.semestre = e.semestre AND g.id_funcao = i.id', 'left')
            ->where('b.id', $alocacao->id_alocado)
            ->where("(CHAR_LENGTH('{$substitutoEvento}') = 0 OR h.id = '{$substitutoEvento}')")
            ->group_by(['c.id_escola', 'a.periodo'])
            ->get('ei_apontamento a')
            ->result();

        $totalHorasMesSub = [];
        $valorPagamentoSub = [];
        $totalSub = [];
        foreach ($rowValoresFaturamento as $valoresFaturamento) {
            $totalHorasMesSub[$valoresFaturamento->id_escola][$valoresFaturamento->periodo] = $valoresFaturamento->total_horas_mes;
            $valorPagamentoSub[$valoresFaturamento->id_escola][$valoresFaturamento->periodo] = $valoresFaturamento->valor_pagamento;
            $totalSub[$valoresFaturamento->id_escola][$valoresFaturamento->periodo] = $valoresFaturamento->total;
        }

        // recupera os dados das totalizacoes
        $qb = $this->db
            ->select("d.id, c.id_escola, c.escola, a.periodo, a.cargo{$mesCargoFuncao} AS cargo, a.funcao{$mesCargoFuncao} AS funcao")
            ->select("(CASE a.periodo WHEN 0 THEN 'Madrugada' WHEN 1 THEN 'Manhã' WHEN 2 THEN 'Tarde' WHEN 3 THEN 'Noite' END) AS nome_periodo", false);
        if ($substituto) {
            if ($recuperar) {
                $qb->select(["TIME_FORMAT(SEC_TO_TIME((SUM(IF(k.status = 'HE', IFNULL(TIME_TO_SEC(k.desconto_sub1), 0) + IFNULL(TIME_TO_SEC(k.desconto_sub2), 0), 0)) * IFNULL(COUNT(DISTINCT(k.id)) / COUNT(k.id), 1)) + TIME_TO_SEC(IF(ef3.id IS NOT NULL, f.horas_mensais_custo_3, IF(ef2.id IS NOT NULL, f.horas_mensais_custo_2, IF(ef1.id IS NOT NULL, f.horas_mensais_custo, NULL)))) + IFNULL(TIME_TO_SEC(d.dias_descontados_mes{$idMes}), 0) + IFNULL(TIME_TO_SEC(LEAST(d.horas_descontadas_mes{$idMes}, 0)), 0)), '%H:%i') AS total_horas_mes"], false)
                    ->select(["TIME_FORMAT(SEC_TO_TIME((SUM(IF(k.status = 'HE', IFNULL(TIME_TO_SEC(k.desconto_sub1), 0) + IFNULL(TIME_TO_SEC(k.desconto_sub2), 0), 0)) * IFNULL(COUNT(DISTINCT(k.id)) / COUNT(k.id), 1)) + TIME_TO_SEC(IF(ef6.id IS NOT NULL, f.horas_mensais_custo_3t, IF(ef5.id IS NOT NULL, f.horas_mensais_custo_2t, IF(ef4.id IS NOT NULL, f.horas_mensais_custo_1t, NULL)))) + IFNULL(TIME_TO_SEC(d.dias_descontados_mes{$idMes}), 0) + IFNULL(TIME_TO_SEC(LEAST(d.horas_descontadas_mes{$idMes}, 0)), 0)), '%H:%i') AS total_horas_mes_2"], false)
                    ->select(["TIME_FORMAT(SEC_TO_TIME((SUM(IF(k.status = 'HE', IFNULL(TIME_TO_SEC(k.desconto_sub1), 0) + IFNULL(TIME_TO_SEC(k.desconto_sub2), 0), 0)) * IFNULL(COUNT(DISTINCT(k.id)) / COUNT(k.id), 1)) + TIME_TO_SEC(IF(ef9.id IS NOT NULL, f.horas_mensais_custo_3n, IF(ef8.id IS NOT NULL, f.horas_mensais_custo_2n, IF(ef7.id IS NOT NULL, f.horas_mensais_custo_1n, NULL)))) + IFNULL(TIME_TO_SEC(d.dias_descontados_mes{$idMes}), 0) + IFNULL(TIME_TO_SEC(LEAST(d.horas_descontadas_mes{$idMes}, 0)), 0)), '%H:%i') AS total_horas_mes_3"], false)
                    ->select(["(CASE WHEN ef3.id IS NOT NULL THEN IF(f.valor_hora_operacional_3 > 0, f.valor_hora_operacional_3, j.valor_pagamento)
                                          WHEN ef2.id IS NOT NULL THEN IF(f.valor_hora_operacional_2 > 0, f.valor_hora_operacional_2, j.valor_pagamento)
                                          WHEN ef1.id IS NOT NULL THEN IF(f.valor_hora_operacional > 0, f.valor_hora_operacional, j.valor_pagamento)
                                          END) valor_hora_operacional"], false)
                    ->select(["(CASE WHEN ef6.id IS NOT NULL THEN IF(f.valor_hora_operacional_3t > 0, f.valor_hora_operacional_3t, j.valor_pagamento)
                                          WHEN ef5.id IS NOT NULL THEN IF(f.valor_hora_operacional_2t > 0, f.valor_hora_operacional_2t, j.valor_pagamento)
                                          WHEN ef4.id IS NOT NULL THEN IF(f.valor_hora_operacional_1t > 0, f.valor_hora_operacional_1t, j.valor_pagamento)
                                          END) valor_hora_operacional_2"], false)
                    ->select(["(CASE WHEN ef9.id IS NOT NULL THEN IF(f.valor_hora_operacional_3n > 0, f.valor_hora_operacional_3n, j.valor_pagamento)
                                          WHEN ef8.id IS NOT NULL THEN IF(f.valor_hora_operacional_2n > 0, f.valor_hora_operacional_2n, j.valor_pagamento)
                                          WHEN ef7.id IS NOT NULL THEN IF(f.valor_hora_operacional_1n > 0, f.valor_hora_operacional_1n, j.valor_pagamento)
                                          END) valor_hora_operacional_3"], false)
                    ->select(["(CASE WHEN ef3.id IS NOT NULL THEN IF(f.valor_hora_operacional_3 > 0, f.valor_hora_operacional_3, j.valor_pagamento)
                                          WHEN ef2.id IS NOT NULL THEN IF(f.valor_hora_operacional_2 > 0, f.valor_hora_operacional_2, j.valor_pagamento)
                                          WHEN ef1.id IS NOT NULL THEN IF(f.valor_hora_operacional > 0, f.valor_hora_operacional, j.valor_pagamento)
                                          END) * (((SUM(IF(k.status = 'HE', IFNULL(TIME_TO_SEC(k.desconto_sub1), 0) + IFNULL(TIME_TO_SEC(k.desconto_sub2), 0), 0)) * IFNULL(COUNT(DISTINCT(k.id)) / COUNT(k.id), 1)) + (TIME_TO_SEC(IF(ef3.id IS NOT NULL, f.horas_mensais_custo_3, IF(ef2.id IS NOT NULL, f.horas_mensais_custo_2, IF(ef1.id IS NOT NULL, f.horas_mensais_custo, NULL)))) + IFNULL(TIME_TO_SEC(d.dias_descontados_mes{$idMes}), 0) + IFNULL(TIME_TO_SEC(LEAST(d.horas_descontadas_mes{$idMes}, 0)), 0))) / 3600) AS valor_total"], false)
                    ->select(["(CASE WHEN ef6.id IS NOT NULL THEN IF(f.valor_hora_operacional_3t > 0, f.valor_hora_operacional_3t, j.valor_pagamento)
                                          WHEN ef5.id IS NOT NULL THEN IF(f.valor_hora_operacional_2t > 0, f.valor_hora_operacional_2t, j.valor_pagamento)
                                          WHEN ef4.id IS NOT NULL THEN IF(f.valor_hora_operacional_1t > 0, f.valor_hora_operacional_1t, j.valor_pagamento)
                                          END) * (((SUM(IF(k.status = 'HE', IFNULL(TIME_TO_SEC(k.desconto_sub1), 0) + IFNULL(TIME_TO_SEC(k.desconto_sub2), 0), 0)) * IFNULL(COUNT(DISTINCT(k.id)) / COUNT(k.id), 1)) + (TIME_TO_SEC(IF(ef6.id IS NOT NULL, f.horas_mensais_custo_3t, IF(ef5.id IS NOT NULL, f.horas_mensais_custo_2t, IF(ef4.id IS NOT NULL, f.horas_mensais_custo_1t, NULL)))) + IFNULL(TIME_TO_SEC(d.dias_descontados_mes{$idMes}), 0) + IFNULL(TIME_TO_SEC(LEAST(d.horas_descontadas_mes{$idMes}, 0)), 0))) / 3600) AS valor_total_2valor_hora_operacional_2"], false)
                    ->select(["(CASE WHEN ef9.id IS NOT NULL THEN IF(f.valor_hora_operacional_3n > 0, f.valor_hora_operacional_3n, j.valor_pagamento)
                                          WHEN ef8.id IS NOT NULL THEN IF(f.valor_hora_operacional_2n > 0, f.valor_hora_operacional_2n, j.valor_pagamento)
                                          WHEN ef7.id IS NOT NULL THEN IF(f.valor_hora_operacional_1n > 0, f.valor_hora_operacional_1n, j.valor_pagamento)
                                          END) * (((SUM(IF(k.status = 'HE', IFNULL(TIME_TO_SEC(k.desconto_sub1), 0) + IFNULL(TIME_TO_SEC(k.desconto_sub2), 0), 0)) * IFNULL(COUNT(DISTINCT(k.id)) / COUNT(k.id), 1)) + (TIME_TO_SEC(IF(ef9.id IS NOT NULL, f.horas_mensais_custo_3n, IF(ef8.id IS NOT NULL, f.horas_mensais_custo_2n, IF(ef7.id IS NOT NULL, f.horas_mensais_custo_1n, NULL)))) + IFNULL(TIME_TO_SEC(d.dias_descontados_mes{$idMes}), 0) + IFNULL(TIME_TO_SEC(LEAST(d.horas_descontadas_mes{$idMes}, 0)), 0))) / 3600) AS valor_total_3"], false);

            } elseif ($usoHorasFaturadas) {
                $qb->select(["TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(IF(MONTH(a.data_substituicao1) = '{$mes}', a.total_sub1, 0))) + IFNULL(TIME_TO_SEC(d.dias_descontados_mes{$idMes}), 0) + IFNULL(TIME_TO_SEC(LEAST(d.horas_descontadas_mes{$idMes}, 0)), 0)), '%H:%i') AS total_horas_mes"], false)
                    ->select(["IFNULL(d.valor_pagamento_mes{$idMes}, (CASE WHEN ef3.id IS NOT NULL THEN IF(f.valor_hora_operacional_3 > 0, f.valor_hora_operacional_3, j.valor_pagamento)
                                                                           WHEN ef2.id IS NOT NULL THEN IF(f.valor_hora_operacional_2 > 0, f.valor_hora_operacional_2, j.valor_pagamento)
                                                                           WHEN ef1.id IS NOT NULL THEN IF(f.valor_hora_operacional > 0, f.valor_hora_operacional, j.valor_pagamento)
                                                                           END) valor_hora_operacional"], false)
                    ->select(["IFNULL(d.valor_pagamento_mes{$idMes}, (CASE WHEN ef6.id IS NOT NULL THEN IF(f.valor_hora_operacional_3t > 0, f.valor_hora_operacional_3t, j.valor_pagamento)
                                                                           WHEN ef5.id IS NOT NULL THEN IF(f.valor_hora_operacional_2t > 0, f.valor_hora_operacional_2t, j.valor_pagamento)
                                                                           WHEN ef4.id IS NOT NULL THEN IF(f.valor_hora_operacional_1t > 0, f.valor_hora_operacional_1t, j.valor_pagamento)
                                                                           END) valor_hora_operacional_2"], false)
                    ->select(["IFNULL(d.valor_pagamento_mes{$idMes}, (CASE WHEN ef9.id IS NOT NULL THEN IF(f.valor_hora_operacional_3n > 0, f.valor_hora_operacional_3n, j.valor_pagamento)
                                                                           WHEN ef8.id IS NOT NULL THEN IF(f.valor_hora_operacional_2n > 0, f.valor_hora_operacional_2n, j.valor_pagamento)
                                                                           WHEN ef7.id IS NOT NULL THEN IF(f.valor_hora_operacional_1n > 0, f.valor_hora_operacional_1n, j.valor_pagamento)
                                                                           END) valor_hora_operacional_3"], false)
                    ->select(["IFNULL(d.valor_pagamento_mes{$idMes}, (CASE WHEN ef3.id IS NOT NULL THEN IF(f.valor_hora_operacional_3 > 0, f.valor_hora_operacional_3, j.valor_pagamento)
                                                                           WHEN ef2.id IS NOT NULL THEN IF(f.valor_hora_operacional_2 > 0, f.valor_hora_operacional_2, j.valor_pagamento)
                                                                           WHEN ef1.id IS NOT NULL THEN IF(f.valor_hora_operacional > 0, f.valor_hora_operacional, j.valor_pagamento)
                                                                           END) * IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC(IF(MONTH(a.data_substituicao1) = '{$mes}', a.total_sub1, 0))) + IFNULL(TIME_TO_SEC(d.dias_descontados_mes{$idMes}), 0) + IFNULL(TIME_TO_SEC(LEAST(d.horas_descontadas_mes{$idMes}, 0)), 0)), 0) AS valor_total"], false)
                    ->select(["IFNULL(d.valor_pagamento_mes{$idMes}, (CASE WHEN ef6.id IS NOT NULL THEN IF(f.valor_hora_operacional_3t > 0, f.valor_hora_operacional_3t, j.valor_pagamento)
                                                                           WHEN ef5.id IS NOT NULL THEN IF(f.valor_hora_operacional_2t > 0, f.valor_hora_operacional_2t, j.valor_pagamento)
                                                                           WHEN ef4.id IS NOT NULL THEN IF(f.valor_hora_operacional_1t > 0, f.valor_hora_operacional_1t, j.valor_pagamento)
                                                                           END)  * IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC(IF(MONTH(a.data_substituicao1) = '{$mes}', a.total_sub1, 0))) + IFNULL(TIME_TO_SEC(d.dias_descontados_mes{$idMes}), 0) + IFNULL(TIME_TO_SEC(LEAST(d.horas_descontadas_mes{$idMes}, 0)), 0)), 0) AS valor_total_2"], false)
                    ->select(["IFNULL(d.valor_pagamento_mes{$idMes}, (CASE WHEN ef9.id IS NOT NULL THEN IF(f.valor_hora_operacional_3n > 0, f.valor_hora_operacional_3n, j.valor_pagamento)
                                                                           WHEN ef8.id IS NOT NULL THEN IF(f.valor_hora_operacional_2n > 0, f.valor_hora_operacional_2n, j.valor_pagamento)
                                                                           WHEN ef7.id IS NOT NULL THEN IF(f.valor_hora_operacional_1n > 0, f.valor_hora_operacional_1n, j.valor_pagamento)
                                                                           END)  * IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC(IF(MONTH(a.data_substituicao1) = '{$mes}', a.total_sub1, 0))) + IFNULL(TIME_TO_SEC(d.dias_descontados_mes{$idMes}), 0) + IFNULL(TIME_TO_SEC(LEAST(d.horas_descontadas_mes{$idMes}, 0)), 0)), 0) AS valor_total_3"], false);
            } else {
                $qb->select(["TIME_FORMAT(IFNULL(d.total_horas_faturadas_mes{$idMes}, 0), '%H:%i') AS total_horas_mes"], false)
                    ->select(["IFNULL(d.valor_pagamento_mes{$idMes}, 0) AS valor_hora_operacional"], false)
                    ->select(["IFNULL(d.valor_total_mes{$idMes}, 0) AS valor_total"], false);
            }
        } else {
            if ($recuperar) {
                $qb->select(["TIME_FORMAT(SEC_TO_TIME((SUM(IF(k.status = 'HE', IFNULL(TIME_TO_SEC(k.desconto), 0), 0)) * IFNULL(COUNT(DISTINCT(k.id)) / COUNT(k.id), 1)) + TIME_TO_SEC(IF(ef3.id IS NOT NULL, f.horas_mensais_custo_3, IF(ef2.id IS NOT NULL, f.horas_mensais_custo_2, IF(ef1.id IS NOT NULL, f.horas_mensais_custo, NULL)))) + IFNULL(TIME_TO_SEC(d.dias_descontados_mes{$idMes}), 0) + IFNULL(TIME_TO_SEC(LEAST(d.horas_descontadas_mes{$idMes}, 0)), 0)), '%H:%i') AS total_horas_mes"], false)
                    ->select(["TIME_FORMAT(SEC_TO_TIME((SUM(IF(k.status = 'HE', IFNULL(TIME_TO_SEC(k.desconto), 0), 0)) * IFNULL(COUNT(DISTINCT(k.id)) / COUNT(k.id), 1)) + TIME_TO_SEC(IF(ef6.id IS NOT NULL, f.horas_mensais_custo_3t, IF(ef5.id IS NOT NULL, f.horas_mensais_custo_2t, IF(ef4.id IS NOT NULL, f.horas_mensais_custo_1t, NULL)))) + IFNULL(TIME_TO_SEC(d.dias_descontados_mes{$idMes}), 0) + IFNULL(TIME_TO_SEC(LEAST(d.horas_descontadas_mes{$idMes}, 0)), 0)), '%H:%i') AS total_horas_mes_2"], false)
                    ->select(["TIME_FORMAT(SEC_TO_TIME((SUM(IF(k.status = 'HE', IFNULL(TIME_TO_SEC(k.desconto), 0), 0)) * IFNULL(COUNT(DISTINCT(k.id)) / COUNT(k.id), 1)) + TIME_TO_SEC(IF(ef9.id IS NOT NULL, f.horas_mensais_custo_3n, IF(ef8.id IS NOT NULL, f.horas_mensais_custo_2n, IF(ef7.id IS NOT NULL, f.horas_mensais_custo_1n, NULL)))) + IFNULL(TIME_TO_SEC(d.dias_descontados_mes{$idMes}), 0) + IFNULL(TIME_TO_SEC(LEAST(d.horas_descontadas_mes{$idMes}, 0)), 0)), '%H:%i') AS total_horas_mes_3"], false)
                    ->select(["(CASE WHEN ef3.id IS NOT NULL THEN IF(f.valor_hora_operacional_3 > 0, f.valor_hora_operacional_3, j.valor_pagamento)
                                     WHEN ef2.id IS NOT NULL THEN IF(f.valor_hora_operacional_2 > 0, f.valor_hora_operacional_2, j.valor_pagamento)
                                     WHEN ef1.id IS NOT NULL THEN IF(f.valor_hora_operacional > 0, f.valor_hora_operacional, j.valor_pagamento)
                                     END) valor_hora_operacional"], false)
                    ->select(["(CASE WHEN ef6.id IS NOT NULL THEN IF(f.valor_hora_operacional_3t > 0, f.valor_hora_operacional_3t, j.valor_pagamento)
                                     WHEN ef5.id IS NOT NULL THEN IF(f.valor_hora_operacional_2t > 0, f.valor_hora_operacional_2t, j.valor_pagamento)
                                     WHEN ef4.id IS NOT NULL THEN IF(f.valor_hora_operacional_1t > 0, f.valor_hora_operacional_1t, j.valor_pagamento)
                                     END) valor_hora_operacional_2"], false)
                    ->select(["(CASE WHEN ef9.id IS NOT NULL THEN IF(f.valor_hora_operacional_3n > 0, f.valor_hora_operacional_3n, j.valor_pagamento)
                                     WHEN ef8.id IS NOT NULL THEN IF(f.valor_hora_operacional_2n > 0, f.valor_hora_operacional_2n, j.valor_pagamento)
                                     WHEN ef7.id IS NOT NULL THEN IF(f.valor_hora_operacional_1n > 0, f.valor_hora_operacional_1n, j.valor_pagamento)
                                     END) valor_hora_operacional_3"], false)
                    ->select(["(CASE WHEN ef3.id IS NOT NULL THEN IF(f.valor_hora_operacional_3 > 0, f.valor_hora_operacional_3, j.valor_pagamento)
                                     WHEN ef2.id IS NOT NULL THEN IF(f.valor_hora_operacional_2 > 0, f.valor_hora_operacional_2, j.valor_pagamento)
                                     WHEN ef1.id IS NOT NULL THEN IF(f.valor_hora_operacional > 0, f.valor_hora_operacional, j.valor_pagamento)
                                     END)  * (((SUM(IF(k.status = 'HE', IFNULL(TIME_TO_SEC(k.desconto), 0), 0)) * IFNULL(COUNT(DISTINCT(k.id)) / COUNT(k.id), 1)) + (TIME_TO_SEC(IF(ef3.id IS NOT NULL, f.horas_mensais_custo_3, IF(ef2.id IS NOT NULL, f.horas_mensais_custo_2, IF(ef1.id IS NOT NULL, f.horas_mensais_custo, NULL)))) + IFNULL(TIME_TO_SEC(d.dias_descontados_mes{$idMes}), 0) + IFNULL(TIME_TO_SEC(LEAST(d.horas_descontadas_mes{$idMes}, 0)), 0))) / 3600) AS valor_total"], false)
                    ->select(["(CASE WHEN ef6.id IS NOT NULL THEN IF(f.valor_hora_operacional_3t > 0, f.valor_hora_operacional_3t, j.valor_pagamento)
                                     WHEN ef5.id IS NOT NULL THEN IF(f.valor_hora_operacional_2t > 0, f.valor_hora_operacional_2t, j.valor_pagamento)
                                     WHEN ef4.id IS NOT NULL THEN IF(f.valor_hora_operacional_1t > 0, f.valor_hora_operacional_1t, j.valor_pagamento)
                                     END)  * (((SUM(IF(k.status = 'HE', IFNULL(TIME_TO_SEC(k.desconto), 0), 0)) * IFNULL(COUNT(DISTINCT(k.id)) / COUNT(k.id), 1)) + (TIME_TO_SEC(IF(ef6.id IS NOT NULL, f.horas_mensais_custo_3t, IF(ef5.id IS NOT NULL, f.horas_mensais_custo_2t, IF(ef4.id IS NOT NULL, f.horas_mensais_custo_1t, NULL)))) + IFNULL(TIME_TO_SEC(d.dias_descontados_mes{$idMes}), 0) + IFNULL(TIME_TO_SEC(LEAST(d.horas_descontadas_mes{$idMes}, 0)), 0))) / 3600) AS valor_total_2"], false)
                    ->select(["(CASE WHEN ef9.id IS NOT NULL THEN IF(f.valor_hora_operacional_3n > 0, f.valor_hora_operacional_3n, j.valor_pagamento)
                                     WHEN ef8.id IS NOT NULL THEN IF(f.valor_hora_operacional_2n > 0, f.valor_hora_operacional_2n, j.valor_pagamento)
                                     WHEN ef7.id IS NOT NULL THEN IF(f.valor_hora_operacional_1n > 0, f.valor_hora_operacional_1n, j.valor_pagamento)
                                     END)  * (((SUM(IF(k.status = 'HE', IFNULL(TIME_TO_SEC(k.desconto), 0), 0)) * IFNULL(COUNT(DISTINCT(k.id)) / COUNT(k.id), 1)) + (TIME_TO_SEC(IF(ef9.id IS NOT NULL, f.horas_mensais_custo_3n, IF(ef8.id IS NOT NULL, f.horas_mensais_custo_2n, IF(ef7.id IS NOT NULL, f.horas_mensais_custo_1n, NULL)))) + IFNULL(TIME_TO_SEC(d.dias_descontados_mes{$idMes}), 0) + IFNULL(TIME_TO_SEC(LEAST(d.horas_descontadas_mes{$idMes}, 0)), 0))) / 3600) AS valor_total_3"], false);
            } elseif ($usoHorasFaturadas) {
                $qb->select(["TIME_FORMAT(SEC_TO_TIME(  SUM(TIME_TO_SEC(a.total_mes{$idMes})) + IFNULL(TIME_TO_SEC(d.dias_descontados_mes{$idMes}), 0) + IFNULL(TIME_TO_SEC(LEAST(d.horas_descontadas_mes{$idMes}, 0)), 0)), '%H:%i') AS total_horas_mes"], false)
                    ->select(["IFNULL(d.valor_pagamento_mes{$idMes}, (CASE WHEN ef3.id IS NOT NULL THEN IF(f.valor_hora_operacional_3 > 0, f.valor_hora_operacional_3, j.valor_pagamento)
                                                                           WHEN ef2.id IS NOT NULL THEN IF(f.valor_hora_operacional_2 > 0, f.valor_hora_operacional_2, j.valor_pagamento)
                                                                           WHEN ef1.id IS NOT NULL THEN IF(f.valor_hora_operacional > 0, f.valor_hora_operacional, j.valor_pagamento)
                                                                           END) valor_hora_operacional"], false)
                    ->select(["IFNULL(d.valor_pagamento_mes{$idMes}, (CASE WHEN ef6.id IS NOT NULL THEN IF(f.valor_hora_operacional_3t > 0, f.valor_hora_operacional_3t, j.valor_pagamento)
                                                                           WHEN ef5.id IS NOT NULL THEN IF(f.valor_hora_operacional_2t > 0, f.valor_hora_operacional_2t, j.valor_pagamento)
                                                                           WHEN ef4.id IS NOT NULL THEN IF(f.valor_hora_operacional_1t > 0, f.valor_hora_operacional_1t, j.valor_pagamento)
                                                                           END) valor_hora_operacional_2"], false)
                    ->select(["IFNULL(d.valor_pagamento_mes{$idMes}, (CASE WHEN ef9.id IS NOT NULL THEN IF(f.valor_hora_operacional_3n > 0, f.valor_hora_operacional_3n, j.valor_pagamento)
                                                                           WHEN ef8.id IS NOT NULL THEN IF(f.valor_hora_operacional_2n > 0, f.valor_hora_operacional_2n, j.valor_pagamento)
                                                                           WHEN ef7.id IS NOT NULL THEN IF(f.valor_hora_operacional_1n > 0, f.valor_hora_operacional_1n, j.valor_pagamento)
                                                                           END) valor_hora_operacional_3"], false)
                    ->select(["IFNULL(d.valor_pagamento_mes{$idMes}, (CASE WHEN ef3.id IS NOT NULL THEN IF(f.valor_hora_operacional_3 > 0, f.valor_hora_operacional_3, j.valor_pagamento)
                                                                           WHEN ef2.id IS NOT NULL THEN IF(f.valor_hora_operacional_2 > 0, f.valor_hora_operacional_2, j.valor_pagamento)
                                                                           WHEN ef1.id IS NOT NULL THEN IF(f.valor_hora_operacional > 0, f.valor_hora_operacional, j.valor_pagamento)
                                                                           END) * IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC(a.total_mes{$idMes})) + IFNULL(TIME_TO_SEC(d.dias_descontados_mes{$idMes}), 0) + IFNULL(TIME_TO_SEC(LEAST(d.horas_descontadas_mes{$idMes}, 0)), 0)), 0) AS valor_total"], false)
                    ->select(["IFNULL(d.valor_pagamento_mes{$idMes}, (CASE WHEN ef6.id IS NOT NULL THEN IF(f.valor_hora_operacional_3t > 0, f.valor_hora_operacional_3t, j.valor_pagamento)
                                                                           WHEN ef5.id IS NOT NULL THEN IF(f.valor_hora_operacional_2t > 0, f.valor_hora_operacional_2t, j.valor_pagamento)
                                                                           WHEN ef4.id IS NOT NULL THEN IF(f.valor_hora_operacional_1t > 0, f.valor_hora_operacional_1t, j.valor_pagamento)
                                                                           END) * IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC(a.total_mes{$idMes})) + IFNULL(TIME_TO_SEC(d.dias_descontados_mes{$idMes}), 0) + IFNULL(TIME_TO_SEC(LEAST(d.horas_descontadas_mes{$idMes}, 0)), 0)), 0) AS valor_total_2"], false)
                    ->select(["IFNULL(d.valor_pagamento_mes{$idMes}, (CASE WHEN ef9.id IS NOT NULL THEN IF(f.valor_hora_operacional_3n > 0, f.valor_hora_operacional_3n, j.valor_pagamento)
                                                                           WHEN ef8.id IS NOT NULL THEN IF(f.valor_hora_operacional_2n > 0, f.valor_hora_operacional_2n, j.valor_pagamento)
                                                                           WHEN ef7.id IS NOT NULL THEN IF(f.valor_hora_operacional_1n > 0, f.valor_hora_operacional_1n, j.valor_pagamento)
                                                                           END) * IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC(a.total_mes{$idMes})) + IFNULL(TIME_TO_SEC(d.dias_descontados_mes{$idMes}), 0) + IFNULL(TIME_TO_SEC(LEAST(d.horas_descontadas_mes{$idMes}, 0)), 0)), 0) AS valor_total_3"], false);
            } else {
                $qb->select(["TIME_FORMAT(IFNULL(d.total_horas_faturadas_mes{$idMes}, 0), '%H:%i') AS total_horas_mes"], false)
                    ->select(["IFNULL(d.valor_pagamento_mes{$idMes}, 0) AS valor_hora_operacional"], false)
                    ->select(["IFNULL(d.valor_total_mes{$idMes}, 0) AS valor_total"], false);
            }
        }
        $qb->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocados_totalizacao d', "d.id_alocado = b.id AND d.periodo = a.periodo AND d.cargo{$mesCargoFuncao} = a.cargo{$mesCargoFuncao} AND d.funcao{$mesCargoFuncao} = a.funcao{$mesCargoFuncao} AND d.id_cuidador = '{$idCuidador}'", 'left', false)
            ->join('ei_ordem_servico_horarios e', 'e.id = a.id_os_horario', 'left')
            ->join('ei_ordem_servico_profissionais f', 'f.id = b.id_os_profissional', 'left')
            ->join('ei_ordem_servico_escolas g', 'g.id = f.id_ordem_servico_escola', 'left')
            ->join('ei_ordem_servico h', 'h.id = g.id_ordem_servico', 'left')
            ->join('ei_contratos i', 'i.id = h.id_contrato', 'left')
            ->join('ei_valores_faturamento j', 'j.id_contrato = i.id AND j.ano = h.ano AND j.semestre = h.semestre AND j.id_funcao = e.id_funcao', 'left');
        $dataInicioFechamento = $ano . '-' . $mes . '-01';
        $dataTerminoFechamento = date('Y-m-t', strtotime($dataInicioFechamento));
        if ($alocacao->dia_fechamento) {
            $dataTerminoFechamento = $ano . '-' . $mes . '-' . $alocacao->dia_fechamento;
            $dataInicioFechamento = date('Y-m-d', strtotime($dataTerminoFechamento . ' -1month +1day'));
        }
        if (strlen($substitutoEvento) > 0) {
            $qb->join('ei_apontamento k', "k.id_alocado = b.id AND k.periodo = a.periodo AND (k.data BETWEEN '{$dataInicioFechamento}' AND '{$dataTerminoFechamento}') AND (k.id_alocado_sub1 = '{$substitutoEvento}' OR k.id_alocado_sub2 = '{$substitutoEvento}')", 'left');
        } else {
            $qb->join('ei_apontamento k', "k.id_alocado = b.id AND k.periodo = a.periodo AND (k.data BETWEEN '{$dataInicioFechamento}' AND '{$dataTerminoFechamento}') AND (k.id_alocado_sub1 IS NULL OR k.id_alocado_sub2 IS NULL)", 'left');
        }
        $totalizacoes = $qb
            ->join('empresa_funcoes ef1', "ef1.id = f.id_funcao AND ef1.nome = a.funcao{$mesCargoFuncao} AND a.periodo = 1", 'left', false)
            ->join('empresa_funcoes ef2', "ef2.id = f.id_funcao_2m AND ef2.nome = a.funcao{$mesCargoFuncao} AND a.periodo = 1", 'left', false)
            ->join('empresa_funcoes ef3', "ef3.id = f.id_funcao_3m AND ef3.nome = a.funcao{$mesCargoFuncao} AND a.periodo = 1", 'left', false)
            ->join('empresa_funcoes ef4', "ef4.id = f.id_funcao_1t AND ef4.nome = a.funcao{$mesCargoFuncao} AND a.periodo = 2", 'left', false)
            ->join('empresa_funcoes ef5', "ef5.id = f.id_funcao_2t AND ef5.nome = a.funcao{$mesCargoFuncao} AND a.periodo = 2", 'left', false)
            ->join('empresa_funcoes ef6', "ef6.id = f.id_funcao_3t AND ef6.nome = a.funcao{$mesCargoFuncao} AND a.periodo = 2", 'left', false)
            ->join('empresa_funcoes ef7', "ef7.id = f.id_funcao_1n AND ef7.nome = a.funcao{$mesCargoFuncao} AND a.periodo = 3", 'left', false)
            ->join('empresa_funcoes ef8', "ef8.id = f.id_funcao_2n AND ef8.nome = a.funcao{$mesCargoFuncao} AND a.periodo = 3", 'left', false)
            ->join('empresa_funcoes ef9', "ef9.id = f.id_funcao_3n AND ef9.nome = a.funcao{$mesCargoFuncao} AND a.periodo = 3", 'left', false)
            ->where('b.id_cuidador', $alocacao->id_cuidador)
            ->where('c.id_alocacao', $alocacao->id)
            ->group_by(['b.id_cuidador', 'c.id_escola', 'a.periodo', 'a.cargo' . $mesCargoFuncao, 'a.funcao' . $mesCargoFuncao])
            ->order_by('b.id_cuidador', 'asc')
            ->order_by('c.id_escola', 'asc')
            ->order_by('a.periodo', 'asc')
            ->order_by('a.cargo' . $mesCargoFuncao, 'asc')
            ->order_by('a.funcao' . $mesCargoFuncao, 'asc')
            ->get('ei_alocados_horarios a')
            ->result();

        // monta os dados para a apresentacao da planilha
        $servicos = [];
        $soma = round($pagamentoPrestador->valor_extra1, 2, PHP_ROUND_HALF_DOWN);
        $soma += round($pagamentoPrestador->valor_extra2, 2, PHP_ROUND_HALF_DOWN);
        foreach ($totalizacoes as $totalizacao) {

            if ($totalizacao->periodo == 3) {
                $rowTotaHorasMes = $totalizacao->total_horas_mes_3 ?? $totalizacao->total_horas_mes;
                $rowValorHoraOperacional = $totalizacao->valor_hora_operacional_3 ?? $totalizacao->valor_hora_operacional;
                $rowValorTotal = $totalizacao->valor_total_3 ?? $totalizacao->valor_total;
            } elseif ($totalizacao->periodo == 2) {
                $rowTotaHorasMes = $totalizacao->total_horas_mes_2 ?? $totalizacao->total_horas_mes;
                $rowValorHoraOperacional = $totalizacao->valor_hora_operacional_2 ?? $totalizacao->valor_hora_operacional;
                $rowValorTotal = $totalizacao->valor_total_2 ?? $totalizacao->valor_total;
            } else {
                $rowTotaHorasMes = $totalizacao->total_horas_mes;
                $rowValorHoraOperacional = $totalizacao->valor_hora_operacional;
                $rowValorTotal = $totalizacao->valor_total;
            }

            $servicos[] = [
                'id' => $totalizacao->id,
                'escola' => $totalizacao->escola,
                'periodo' => $totalizacao->nome_periodo,
                'cargo' => $totalizacao->cargo,
                'funcao' => $totalizacao->funcao,
                'qtdeHoras' => $substitutoEvento ? ($totalHorasMesSub[$totalizacao->id_escola][$totalizacao->periodo] ?? null) : $rowTotaHorasMes,
                'valorCustoProfissional' => number_format(round($substitutoEvento ? ($valorPagamentoSub[$totalizacao->id_escola][$totalizacao->periodo] ?? null) : $rowValorHoraOperacional, 2, PHP_ROUND_HALF_DOWN), 2, ',', '.'),
                'total' => number_format(round($substitutoEvento ? ($totalSub[$totalizacao->id_escola][$totalizacao->periodo] ?? null) : $rowValorTotal, 2, PHP_ROUND_HALF_DOWN), 2, ',', '.'),
            ];

            if ($totalizacao->periodo == 3) {
                $valorTotalSoma = $totalizacao->valor_total_3 ?? $totalizacao->valor_total;
            } elseif ($totalizacao->periodo == 2) {
                $valorTotalSoma = $totalizacao->valor_total_2 ?? $totalizacao->valor_total;
            } else {
                $valorTotalSoma = $totalizacao->valor_total;
            }

            $soma += round($substitutoEvento ? ($totalSub[$totalizacao->id_escola][$totalizacao->periodo] ?? null) : $valorTotalSoma, 2, PHP_ROUND_HALF_DOWN);
        }

        $msgWhatsApp = $pagamentoPrestador->cuidador . ' - Caro prestador(a), você tem uma nova notificação de pagamento; entre na Plataforma para validá-la e preencher os dados solicitados.';

        // formata a data da apresentacao da planilha
        $this->load->library('Calendar');

        // retorna conjunto de dados para a view da planilha
        $planilha = [
            'empresa' => $empresa,
            'usuario' => $usuario,
            'msgWhatsApp' => $msgWhatsApp,
            'mesAtual' => $this->calendar->get_month_name(date('m')),
            'query_string' => "horario={$idHorario}&mes={$mes}&ano={$ano}&semestre={$semestre}&substituto={$substituto}",
            'is_pdf' => $is_pdf,
            'solicitante' => $pagamentoPrestador->solicitante,
            'id_alocado' => $alocacao->id_alocado,
            'id_horario' => $idHorario,
            'id_mes' => $idMes,
            'prestador' => $pagamentoPrestador->cuidador,
            'telefone_prestador' => $pagamentoPrestador->telefone_diretoria,
            'prestador_sub1' => $pagamentoPrestador->cuidador_sub1,
            'prestador_sub2' => $pagamentoPrestador->cuidador_sub2,
            'tipo_pagamento' => $pagamentoPrestador->tipo_pagamento,
            'pagamento_inicio_semestre' => $pagamentoPrestador->valor_pagamento,
            'pagamento_ajustado' => $pagamentoPrestador->valor_pagamento2,
            'pagamento_ordem_servico' => $pagamentoPrestador->valor_hora_operacional,
            'substituto' => $substituto,
            'substitutos_eventos' => $substitutosEventos,
            'observacoes' => $pagamentoPrestador->observacoes,
            'cnpj' => $pagamentoPrestador->cnpj,
            'departamento' => $pagamentoPrestador->depto,
            'centroCusto' => $pagamentoPrestador->centro_custo,
            'agencia' => $pagamentoPrestador->agencia_bancaria,
            'conta' => $pagamentoPrestador->conta_bancaria,
            'banco' => $pagamentoPrestador->nome_banco,
            'mesAno' => ucfirst($this->calendar->get_month_name($mes)) . '/' . $ano,
            'justificativa1' => $pagamentoPrestador->justificativa1,
            'valorExtra1' => $pagamentoPrestador->valor_extra1,
            'justificativa2' => $pagamentoPrestador->justificativa2,
            'valorExtra2' => $pagamentoPrestador->valor_extra2,
            'valorTotal' => number_format(round($soma, 2, PHP_ROUND_HALF_DOWN), 2, ',', '.'),
            'servicos' => $servicos,
            'teste' => $totalizacoes,
        ];

        return $this->load->view('ei/planilha_pagamento_prestador', $planilha, true);
    }

}
