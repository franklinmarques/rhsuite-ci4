<?php

namespace App\Controllers\Ei\Apontamento;

use App\Controllers\BaseController;

class Dias_letivos extends BaseController
{

    public function ajax_list()
    {
        parse_str($this->input->post('busca'), $busca);

        $sql = "SELECT s.id,
                       s.id_cuidador,
                       s.cuidador,
                       s.cuidador_sub1,
                       s.cuidador_sub2,
                       s.municipio,
                       s.escola,
                       s.ordem_servico,
                       SUM(total_semanas_mes1) - SUM(faltas_mes1) AS total_dias_mes1,
                       SUM(total_semanas_mes2) - SUM(faltas_mes2) AS total_dias_mes2,
                       SUM(total_semanas_mes3) - SUM(faltas_mes3) AS total_dias_mes3,
                       SUM(total_semanas_mes4) - SUM(faltas_mes4) AS total_dias_mes4,
                       SUM(total_semanas_mes5) - SUM(faltas_mes5) AS total_dias_mes5,
                       SUM(total_semanas_mes6) - SUM(faltas_mes6) AS total_dias_mes6,
                       SUM(total_semanas_mes7) - SUM(faltas_mes7) AS total_dias_mes7
                FROM (SELECT a.id,
                             a.id_cuidador,
                             a.cuidador,
                             f.nome AS cuidador_sub1,
                             g.nome AS cuidador_sub2,
                             b.municipio,
                             b.escola,
                             b.ordem_servico,
                             ROUND(MAX(d.total_semanas_mes1) - IFNULL(MAX(d.desconto_mes1), 0)) AS total_semanas_mes1,
                             ROUND(MAX(d.total_semanas_mes2) - IFNULL(MAX(d.desconto_mes2), 0)) AS total_semanas_mes2,
                             ROUND(MAX(d.total_semanas_mes3) - IFNULL(MAX(d.desconto_mes3), 0)) AS total_semanas_mes3,
                             ROUND(MAX(d.total_semanas_mes4) - IFNULL(MAX(d.desconto_mes4), 0)) AS total_semanas_mes4,
                             ROUND(MAX(d.total_semanas_mes5) - IFNULL(MAX(d.desconto_mes5), 0)) AS total_semanas_mes5,
                             ROUND(MAX(d.total_semanas_mes6) - IFNULL(MAX(d.desconto_mes6), 0)) AS total_semanas_mes6,
                             ROUND(MAX(d.total_semanas_mes7) - IFNULL(MAX(d.desconto_mes7), 0)) AS total_semanas_mes7,
                             SUM(IF((MONTH(e.data) = 1 AND c.semestre = 1) OR (MONTH(e.data) = 7 AND c.semestre = 2), 1, 0)) AS faltas_mes1,
                             SUM(IF((MONTH(e.data) = 2 AND c.semestre = 1) OR (MONTH(e.data) = 8 AND c.semestre = 2), 1, 0)) AS faltas_mes2,
                             SUM(IF((MONTH(e.data) = 3 AND c.semestre = 1) OR (MONTH(e.data) = 9 AND c.semestre = 2), 1, 0)) AS faltas_mes3,
                             SUM(IF((MONTH(e.data) = 4 AND c.semestre = 1) OR (MONTH(e.data) = 10 AND c.semestre = 2), 1, 0)) AS faltas_mes4,
                             SUM(IF((MONTH(e.data) = 5 AND c.semestre = 1) OR (MONTH(e.data) = 11 AND c.semestre = 2), 1, 0)) AS faltas_mes5,
                             SUM(IF((MONTH(e.data) = 6 AND c.semestre = 1) OR (MONTH(e.data) = 12 AND c.semestre = 2), 1, 0)) AS faltas_mes6,
                             SUM(IF(MONTH(e.data) = 7 AND c.semestre = 1, 1, 0)) AS faltas_mes7
                      FROM ei_alocados a
                      INNER JOIN ei_alocacao_escolas b ON b.id = a.id_alocacao_escola
                      INNER JOIN ei_alocacao c ON c.id = b.id_alocacao
                      LEFT JOIN ei_alocados_horarios d ON d.id_alocado = a.id
                      LEFT JOIN ei_apontamento e ON e.id_alocado = a.id AND e.status IN ('FA', 'PV', 'FE', 'EM', 'RE') AND DATE_FORMAT(e.data, '%w') = d.dia_semana
                      LEFT JOIN usuarios f ON f.id = d.id_cuidador_sub1
                      LEFT JOIN usuarios g ON g.id = d.id_cuidador_sub2
                      WHERE c.id_empresa = '{$this->session->userdata('empresa')}'
                            AND c.depto = '{$busca['depto']}'
                            AND c.id_diretoria = '{$busca['diretoria']}'
                            AND c.id_supervisor = '{$busca['supervisor']}'
                            AND c.ano = '{$busca['ano']}'
                            AND c.semestre = '{$busca['semestre']}'
                      GROUP BY a.cuidador, d.dia_semana) s
                GROUP BY s.cuidador";

        $config = [
            'search' => ['cuidador', 'escola', 'municipio', 'ordem_servico'],
            'order' => ['cuidador'],
        ];
        $this->load->library('dataTables', $config);

        $output = $this->datatables->query($sql);

        $data = [];

        foreach ($output->data as $alocado) {
            $row = [
                "<strong>Municipio:</strong> {$alocado->municipio}&emsp;
                 <strong>Escola:</strong> {$alocado->escola}<br>
                 <strong>Ordem de serviço:</strong> {$alocado->ordem_servico}",
                implode(';<br>', array_filter([$alocado->cuidador, $alocado->cuidador_sub1, $alocado->cuidador_sub2])),
            ];
            $total = [
                $alocado->total_dias_mes1,
                $alocado->total_dias_mes2,
                $alocado->total_dias_mes3,
                $alocado->total_dias_mes4,
                $alocado->total_dias_mes5,
                $alocado->total_dias_mes6,
                $alocado->total_dias_mes7,
            ];
            $row = array_merge($row, $total);
            $row[] = array_sum($total);

            $data[] = $row;
        }

        $output->data = $data;

        $semestre = intval($busca['semestre']);

        $this->load->library('Calendar');
        $this->calendar->month_type = 'short';
        $meses = [];
        $nomeMeses = [];
        $mesInicial = $semestre === 2 ? 7 : 1;
        $mesFinal = $semestre === 2 ? 12 : 7;
        for ($i = $mesInicial; $i <= $mesFinal; $i++) {
            $mes = str_pad($i, 2, '0', STR_PAD_LEFT);
            $meses[] = $mes;
            $nomeMeses[] = ucfirst($this->calendar->get_month_name($mes));
        }

        $output->semestre = $nomeMeses;
        $output->meses = $meses;

        echo json_encode($output);
    }

}
