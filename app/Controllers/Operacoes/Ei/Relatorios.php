<?php

namespace App\Controllers\Ei;

use App\Controllers\BaseController;

class Relatorios extends BaseController
{

    public function index()
    {
        $this->funcionarios();
    }

    //--------------------------------------------------------------------

    public function funcionarios($pdf = false)
    {
        $data = $this->input->get();

        $data['empresa'] = $this->db
            ->select('foto, foto_descricao')
            ->where('id', $this->session->userdata('empresa'))
            ->get('usuarios')
            ->row();

        $supervisor = $this->input->get('supervisor');
        $diretoria = $this->input->get('diretoria');
        $departamento = $this->input->get('departamento');

        $qb = $this->db
            ->select('a.nome AS supervisor, d.nome AS diretoria, d.depto')
            ->join('ei_supervisores b', 'b.id_supervisor = a.id', 'left')
            ->join('ei_escolas c', 'c.id = b.id_escola', 'left')
            ->join('ei_diretorias d', 'd.id = c.id_diretoria', 'left');
        if ($supervisor) {
            $qb->where('a.id', $supervisor);
        }
        if ($diretoria) {
            $qb->where('d.id', $diretoria);
        }
        if ($departamento) {
            $qb->where('d.depto', $departamento);
        }
        $row = $qb
            ->get('usuarios a')
            ->row();

        $data['departamento'] = $departamento ? $row->depto : '';
        $data['diretoria'] = $diretoria ? $row->diretoria : '';
        $data['supervisor'] = $supervisor ? $row->supervisor : '';
        $data['postos'] = false;

        $qb = $this->db
            ->select('a.id, a.nome, a.depto, a.area, a.contrato, c.setor')
            ->select('b.nome AS nome_usuario, b.depto AS depto_usuario, b.telefone, b.email')
            ->join('usuarios b', 'b.id = a.id_usuario', 'left')
            ->join('alocacao_unidades c', 'c.id_contrato = a.id')
            ->join('alocacao_reajuste d', 'd.id_cliente = a.id');
        if (!empty($data['depto'])) {
            $qb->where('a.depto', $data['depto']);
        }
        if (!empty($data['area'])) {
            $qb->where('a.area', $data['area']);
            if (strpos($data['area'], 'Ipesp') !== false) {
                $data['postos'] = true;
            }
        }
        if (!empty($data['setor'])) {
            $qb->where('c.setor', $data['setor']);
        }
        $data['contrato'] = $qb
            ->get('alocacao_contratos a')
            ->row();

        $data['dias'] = date('t', mktime(0, 0, 0, $data['mes'], 1, $data['ano']));
        $this->load->library('Calendar');
        $data['mes_nome'] = $this->calendar->get_month_name($data['mes']);
        $data['calculo_totalizacao'] = $data['calculo_totalizacao'] ?? '1';
        $data['apontamentos'] = $this->ajaxList();

        $sql = "SELECT h.numero, h.nome 
                FROM (SELECT @rownum:= @rownum + 1 AS numero, s.data, s.id_cuidador_sub, s.nome 
                      FROM (SELECT a.id_cuidador_sub, b.id, a.data, d.nome 
                            FROM ei_apontamento a 
                            INNER JOIN ei_alocados b ON b.id = a.id_alocado 
                            INNER JOIN ei_alocacao c ON c.id = b.id_alocacao 
                            LEFT JOIN usuarios d ON d.id = a.id_cuidador_sub 
                            WHERE a.id_cuidador_sub IS NOT NULL 
                            GROUP BY a.id_cuidador_sub 
                            ORDER BY d.nome) s, 
                           (SELECT @rownum:= 0) x) h
                WHERE DATE_FORMAT(h.data, '%Y-%m') = '{$data['ano']}-{$data['mes']}'";
        $legendas = $this->db->query($sql)->result();

        $data['legendas'] = [];
        foreach ($legendas as $legenda) {
            $data['legendas'][$legenda->numero] = $legenda->nome;
        }
        $data['funcionarios'] = $this->ajaxFuncionarios();
        $data['observacoes'] = $this->ajaxObservacoes();
        $data['is_pdf'] = $pdf;
        $data['query_string'] = 'q?' . http_build_query($this->input->get());

        if ($pdf) {
            return $this->load->view('ei/relatorio', $data, true);
        }

        $this->load->view('ei/relatorio', $data);
    }

    //--------------------------------------------------------------------

    public function escolas($pdf = false)
    {
        if ($pdf !== true) {
            $pdf = false;
        }
        $data = $this->input->get();

        $data['empresa'] = $this->db
            ->select('foto, foto_descricao')
            ->where('id', $this->session->userdata('empresa'))
            ->get('usuarios')
            ->row();

        $supervisor = $this->input->get('supervisor');
        $diretoria = $this->input->get('diretoria');
        $departamento = $this->input->get('departamento');

        $qb = $this->db
            ->select('a.nome AS supervisor, d.nome AS diretoria, d.depto')
            ->join('ei_supervisores b', 'b.id_supervisor = a.id', 'left')
            ->join('ei_escolas c', 'c.id = b.id_escola', 'left')
            ->join('ei_diretorias d', 'd.id = c.id_diretoria', 'left');
        if ($supervisor) {
            $qb->where('a.id', $supervisor);
        }
        if ($diretoria) {
            $qb->where('d.id', $diretoria);
        }
        if ($departamento) {
            $qb->where('d.depto', $departamento);
        }
        $row = $qb
            ->get('usuarios a')
            ->row();

        $data['departamento'] = $departamento ? $row->depto : '';
        $data['diretoria'] = $diretoria ? $row->diretoria : '';
        $data['supervisor'] = $supervisor ? $row->supervisor : '';
        $data['postos'] = false;

        $qb = $this->db
            ->select('a.id, a.nome, a.depto, a.area, a.contrato, c.setor')
            ->select('b.nome AS nome_usuario, b.depto AS depto_usuario, b.telefone, b.email')
            ->join('usuarios b', 'b.id = a.id_usuario', 'left')
            ->join('alocacao_unidades c', 'c.id_contrato = a.id')
            ->join('alocacao_reajuste d', 'd.id_cliente = a.id');
        if (!empty($data['depto'])) {
            $qb->where('a.depto', $data['depto']);
        }
        if (!empty($data['area'])) {
            $qb->where('a.area', $data['area']);
            if (strpos($data['area'], 'Ipesp') !== false) {
                $data['postos'] = true;
            }
        }
        if (!empty($data['setor'])) {
            $qb->where('c.setor', $data['setor']);
        }
        $data['contrato'] = $qb
            ->get('alocacao_contratos a')
            ->row();

        $data['dias'] = date('t', mktime(0, 0, 0, $data['mes'], 1, $data['ano']));
        $this->load->library('Calendar');
        $data['mes_nome'] = $this->calendar->get_month_name($data['mes']);
        $data['calculo_totalizacao'] = $data['calculo_totalizacao'] ?? '1';
        $data['apontamentos'] = $this->ajaxList();

        $sql = "SELECT h.numero, h.nome 
                FROM (SELECT @rownum:= @rownum + 1 AS numero, s.data, s.id_cuidador_sub, s.nome 
                      FROM (SELECT a.id_cuidador_sub, b.id, a.data, d.nome 
                            FROM ei_apontamento a 
                            INNER JOIN ei_alocados b ON b.id = a.id_alocado 
                            INNER JOIN ei_alocacao c ON c.id = b.id_alocacao 
                            LEFT JOIN usuarios d ON d.id = a.id_cuidador_sub 
                            WHERE a.id_cuidador_sub IS NOT NULL 
                            GROUP BY a.id_cuidador_sub 
                            ORDER BY d.nome) s, 
                           (SELECT @rownum:= 0) x) h
                WHERE DATE_FORMAT(h.data, '%Y-%m') = '{$data['ano']}-{$data['mes']}'";
        $legendas = $this->db->query($sql)->result();

        $data['legendas'] = [];
        foreach ($legendas as $legenda) {
            $data['legendas'][$legenda->numero] = $legenda->nome;
        }
        $data['funcionarios'] = $this->ajaxFuncionarios();
        $data['observacoes'] = $this->ajaxObservacoes();
        $data['is_pdf'] = $pdf;
        $data['query_string'] = 'q?' . http_build_query($this->input->get());

        if ($pdf) {
            return $this->load->view('ei/relatorio_escolas', $data, true);
        }

        $this->load->view('ei/relatorio_escolas', $data);
    }

    //--------------------------------------------------------------------

    public function insumos($pdf = false)
    {
        if ($pdf !== true) {
            $pdf = false;
        }
        $data = $this->input->get();

        $data['empresa'] = $this->db
            ->select('foto, foto_descricao')
            ->where('id', $this->session->userdata('empresa'))
            ->get('usuarios')
            ->row();

        $supervisor = $this->input->get('supervisor');
        $diretoria = $this->input->get('diretoria');
        $departamento = $this->input->get('departamento');

        $qb = $this->db
            ->select('a.nome AS supervisor, d.nome AS diretoria, d.depto')
            ->join('ei_supervisores b', 'b.id_supervisor = a.id', 'left')
            ->join('ei_escolas c', 'c.id = b.id_escola', 'left')
            ->join('ei_diretorias d', 'd.id = c.id_diretoria', 'left');
        if ($supervisor) {
            $qb->where('a.id', $supervisor);
        }
        if ($diretoria) {
            $qb->where('d.id', $diretoria);
        }
        if ($departamento) {
            $qb->where('d.depto', $departamento);
        }
        $row = $qb
            ->get('usuarios a')
            ->row();

        $data['departamento'] = $departamento ? $row->depto : '';
        $data['diretoria'] = $diretoria ? $row->diretoria : '';
        $data['supervisor'] = $supervisor ? $row->supervisor : '';
        $data['postos'] = false;

        $qb = $this->db
            ->select('a.id, a.nome, a.depto, a.area, a.contrato, c.setor')
            ->select('b.nome AS nome_usuario, b.depto AS depto_usuario, b.telefone, b.email')
            ->join('usuarios b', 'b.id = a.id_usuario', 'left')
            ->join('alocacao_unidades c', 'c.id_contrato = a.id')
            ->join('alocacao_reajuste d', 'd.id_cliente = a.id');
        if (!empty($data['depto'])) {
            $qb->where('a.depto', $data['depto']);
        }
        if (!empty($data['area'])) {
            $qb->where('a.area', $data['area']);
            if (strpos($data['area'], 'Ipesp') !== false) {
                $data['postos'] = true;
            }
        }
        if (!empty($data['setor'])) {
            $qb->where('c.setor', $data['setor']);
        }
        $data['contrato'] = $qb
            ->get('alocacao_contratos a')
            ->row();

        $data['dias'] = date('t', mktime(0, 0, 0, $data['mes'], 1, $data['ano']));
        $this->load->library('Calendar');
        $data['mes_nome'] = $this->calendar->get_month_name($data['mes']);
        $data['calculo_totalizacao'] = $data['calculo_totalizacao'] ?? '1';
        $data['apontamentos'] = $this->ajaxList();

        $sql = "SELECT h.numero, h.nome 
                FROM (SELECT @rownum:= @rownum + 1 AS numero, s.data, s.id_cuidador_sub, s.nome 
                      FROM (SELECT a.id_cuidador_sub, b.id, a.data, d.nome 
                            FROM ei_apontamento a 
                            INNER JOIN ei_alocados b ON b.id = a.id_alocado 
                            INNER JOIN ei_alocacao c ON c.id = b.id_alocacao 
                            LEFT JOIN usuarios d ON d.id = a.id_cuidador_sub 
                            WHERE a.id_cuidador_sub IS NOT NULL 
                            GROUP BY a.id_cuidador_sub 
                            ORDER BY d.nome) s, 
                           (SELECT @rownum:= 0) x) h
                WHERE DATE_FORMAT(h.data, '%Y-%m') = '{$data['ano']}-{$data['mes']}'";
        $legendas = $this->db->query($sql)->result();

        $data['legendas'] = [];
        foreach ($legendas as $legenda) {
            $data['legendas'][$legenda->numero] = $legenda->nome;
        }
        $data['funcionarios'] = $this->ajaxFuncionarios();
        $insumos = $this->ajaxInsumos();
        $data['titulos'] = $insumos['titulos'];
        $data['insumos'] = $insumos['registros'];
        $data['is_pdf'] = $pdf;
        $data['query_string'] = 'q?' . http_build_query($this->input->get());

        if ($pdf) {
            return $this->load->view('ei/relatorio_insumos', $data, true);
        }

        $this->load->view('ei/relatorio_insumos', $data);
    }

    //--------------------------------------------------------------------

    private function ajaxList()
    {
        $busca = $this->input->get();

        $sql = "SELECT s.escola, 
                       s.municipio,
                       s.turno,
                       s.nome,
                       s.remanejado,
                       s.numero,
                       s.dia_01,
                       s.dia_02,
                       s.dia_03,
                       s.dia_04,
                       s.dia_05,
                       s.dia_06,
                       s.dia_07,
                       s.dia_08,
                       s.dia_09,
                       s.dia_10,
                       s.dia_11,
                       s.dia_12,
                       s.dia_13,
                       s.dia_14,
                       s.dia_15,
                       s.dia_16,
                       s.dia_17,
                       s.dia_18,
                       s.dia_19,
                       s.dia_20,
                       s.dia_21,
                       s.dia_22,
                       s.dia_23,
                       s.dia_24,
                       s.dia_25,
                       s.dia_26,
                       s.dia_27,
                       s.dia_28,
                       s.dia_29,
                       s.dia_30,
                       s.dia_31,
                       s.sub_01,
                       s.sub_02,
                       s.sub_03,
                       s.sub_04,
                       s.sub_05,
                       s.sub_06,
                       s.sub_07,
                       s.sub_08,
                       s.sub_09,
                       s.sub_10,
                       s.sub_11,
                       s.sub_12,
                       s.sub_13,
                       s.sub_14,
                       s.sub_15,
                       s.sub_16,
                       s.sub_17,
                       s.sub_18,
                       s.sub_19,
                       s.sub_20,
                       s.sub_21,
                       s.sub_22,
                       s.sub_23,
                       s.sub_24,
                       s.sub_25,
                       s.sub_26,
                       s.sub_27,
                       s.sub_28,
                       s.sub_29,
                       s.sub_30,
                       s.sub_31
                FROM (SELECT e.escola, 
                             c.municipio, 
                             e.turno, 
                             e.cuidador AS nome, 
                             e.remanejado,
                             d.rownum AS numero,                            
                             ";
        for ($i = 1; $i <= 31; $i++) {
            $dia = str_pad($i, 2, '0', STR_PAD_LEFT);
            if (strtotime("{$busca['ano']}-{$busca['mes']}-$dia") <= strtotime(date('Y-m-d'))) {
                $sql .= "(SELECT h.status
                          FROM ei_apontamento h
                          LEFT JOIN usuarios k ON
                                    k.id = h.id_cuidador_sub
                          WHERE h.id_alocado = e.id AND 
                                DATE_FORMAT(h.data, '%Y-%m') = DATE_FORMAT(f.data, '%Y-%m') AND 
                                DATE_FORMAT(h.data, '%d') = '{$dia}') AS dia_{$dia},
                         (CASE WHEN d.id = g.id_cuidador_sub THEN d.rownum END) AS sub_{$dia},
                         (SELECT h.rownum 
                          FROM (SELECT @rownum_{$dia}:= @rownum_{$dia} + 1 AS rownum, s.data, s.id_cuidador_sub, s.id
                               FROM (SELECT a.id_cuidador_sub, b.id, a.data 
                                     FROM ei_apontamento a 
                                     INNER JOIN ei_alocados b ON b.id = a.id_alocado 
                                     INNER JOIN ei_alocacao c ON c.id = b.id_alocacao 
                                     LEFT JOIN usuarios d ON d.id = a.id_cuidador_sub 
                                     WHERE a.id_cuidador_sub IS NOT NULL 
                                     GROUP BY a.id_cuidador_sub 
                                     ORDER BY d.nome) s, 
                                    (SELECT @rownum_{$dia}:= 0) x) h
                          WHERE h.id = e.id AND
                                DATE_FORMAT(h.data, '%Y-%m') = DATE_FORMAT(f.data, '%Y-%m') AND 
                                DATE_FORMAT(h.data, '%d') = '{$dia}') AS ub_{$dia},";
            } else {
                $sql .= "'' AS dia_{$dia}, '' AS sub_{$dia}, ";
            }
        }
        $sql .= "d.nome AS nome_cuidador
                 FROM ei_alocados e
                 INNER JOIN ei_alocacao f ON
                            f.id = e.id_alocacao
                 INNER JOIN ei_diretorias c ON
                            c.nome = f.diretoria
                 INNER JOIN ei_escolas b
                            ON b.id_diretoria = c.id
                 INNER JOIN ei_supervisores h ON
                            h.id_escola = b.id
                 LEFT JOIN ei_cuidadores a 
                           ON a.id_escola = b.id
                 LEFT JOIN (SELECT @rownum:= @rownum + 1 AS rownum, a.* 
                            FROM (SELECT b.*
                                  FROM ei_cuidadores a
                                  INNER JOIN usuarios b ON b.id = a.id_cuidador
                                  GROUP BY a.id_cuidador
                                  ORDER BY b.nome) a, (SELECT @rownum:= 0) s) d ON 
                           d.id = a.id_cuidador
                 LEFT JOIN ei_apontamento g ON 
                           g.id_alocado = e.id AND
                           DATE_FORMAT(g.data, '%Y-%m') = DATE_FORMAT(f.data, '%Y-%m')
                 WHERE c.id_empresa = {$this->session->userdata('empresa')} AND 
                            DATE_FORMAT(f.data, '%Y-%m') = '{$busca['ano']}-{$busca['mes']}'";
        if (!empty($busca['depto'])) {
            $sql .= " AND c.depto = '{$busca['depto']}'";
        }
        if (!empty($busca['diretoria'])) {
            $sql .= " AND c.id = '{$busca['diretoria']}'";
        }
        if (!empty($busca['supervisor'])) {
            $sql .= " AND h.id_supervisor = '{$busca['supervisor']}'";
        }
        $sql .= " GROUP BY e.escola, e.turno
                 ORDER BY f.municipio ASC, 
                          e.escola ASC,
                          f.municipio ASC,
                          FIELD(e.turno, 'M', 'T', 'N'),
                          e.cuidador ASC) s";

        return $this->db->query($sql)->result();
    }

    //--------------------------------------------------------------------

    private function ajaxFuncionarios()
    {
        $busca = $this->input->get();

        $sql = "SELECT s.id, 
                       s.nome,
                       s.remanejado,
                       s.municipio,
                       s.turno,
                       s.num_turno,
                       s.dia_01,
                       s.dia_02,
                       s.dia_03,
                       s.dia_04,
                       s.dia_05,
                       s.dia_06,
                       s.dia_07,
                       s.dia_08,
                       s.dia_09,
                       s.dia_10,
                       s.dia_11,
                       s.dia_12,
                       s.dia_13,
                       s.dia_14,
                       s.dia_15,
                       s.dia_16,
                       s.dia_17,
                       s.dia_18,
                       s.dia_19,
                       s.dia_20,
                       s.dia_21,
                       s.dia_22,
                       s.dia_23,
                       s.dia_24,
                       s.dia_25,
                       s.dia_26,
                       s.dia_27,
                       s.dia_28,
                       s.dia_29,
                       s.dia_30,
                       s.dia_31
                FROM (SELECT a.id, 
                             a.escola,
                             a.cuidador AS nome,
                             a.remanejado,
                             b.municipio,
                             a.turno,                         
                             ";
        for ($i = 1; $i <= 31; $i++) {
            $dia = str_pad($i, 2, '0', STR_PAD_LEFT);
            if (strtotime("{$busca['ano']}-{$busca['mes']}-$dia") <= strtotime(date('Y-m-d'))) {
                $sql .= "(SELECT CASE WHEN (h.status = 'FE' OR h.data <= CURDATE()) AND a.id IS NOT NULL 
                                      THEN CONCAT('[', GROUP_CONCAT(
                                                CONCAT('\"', h.id, '\",'), 
                                                CONCAT('\"', IFNULL(h.id_cuidador_sub, ''), '\",'), 
                                                CONCAT('\"', IFNULL(h.qtde_dias, ''), '\",'), 
                                                CONCAT('\"', IFNULL(DATE_FORMAT(h.apontamento_asc, '%H:%i'), ''), '\",'), 
                                                CONCAT('\"', IFNULL(DATE_FORMAT(h.apontamento_desc, '%H:%i'), ''), '\",'), 
                                                CONCAT('\"', IFNULL(h.saldo, ''), '\",'), 
                                                CONCAT('\"', IFNULL(h.observacoes, ''), '\",'), 
                                                CONCAT('\"', IFNULL(h.status, ''), '\",'), 
                                                CONCAT('\"', IFNULL(k.nome, ''), '\"')
                                           ),']')
                                      WHEN a.id IS NOT NULL THEN '[\"\"]' 
                                      ELSE '' END
                          FROM ei_apontamento h
                          INNER JOIN ei_alocados i ON
                                     i.id = h.id_alocado
                          LEFT JOIN ei_cuidadores j ON
                                    j.id = i.id_vinculado
                          LEFT JOIN usuarios k ON
                                    k.id = h.id_cuidador_sub
                          WHERE h.id_alocado = a.id AND
                                DATE_FORMAT(h.data, '%Y-%m') = DATE_FORMAT(b.data, '%Y-%m') AND
                                DATE_FORMAT(h.data, '%d') = '{$dia}') AS dia_{$dia}, ";
            } else {
                $sql .= "'' AS dia_{$dia}, ";
            }
        }
        $sql .= "CASE a.turno WHEN 'M' THEN 1
                              WHEN 'T' THEN 2
                              WHEN 'N' THEN 3 
                              ELSE 0 END AS num_turno
                FROM ei_alocados a
                INNER JOIN ei_alocacao b ON
                           b.id = a.id_alocacao
                INNER JOIN ei_diretorias d ON
                           d.nome = b.diretoria
                INNER JOIN usuarios e ON
                           e.nome = b.supervisor
                LEFT JOIN usuarios f ON
                           f.nome = a.cuidador
                LEFT JOIN ei_apontamento c ON 
                          c.id_alocado = a.id AND
                          DATE_FORMAT(c.data, '%Y-%m') = DATE_FORMAT(b.data, '%Y-%m')
                WHERE b.id_empresa = {$this->session->userdata('empresa')} AND 
                            DATE_FORMAT(b.data, '%Y-%m') = '{$busca['ano']}-{$busca['mes']}'";
        if ($busca['depto']) {
            $sql .= " AND b.depto = '{$busca['depto']}'";
        }
        $sql .= " AND d.id = '{$busca['diretoria']}'";
        if ($busca['supervisor']) {
            $sql .= " AND e.id = '{$busca['supervisor']}'";
        }
        $sql .= ' GROUP BY a.escola, a.turno, a.id ORDER BY a.escola ASC, a.cuidador ASC) s';

        return $this->db->query($sql)->result();
    }

    //--------------------------------------------------------------------

    private function ajaxInsumos(): array
    {
        $busca = $this->input->get();

        $depto = $this->input->get('depto');
        $diretoria = $this->input->get('diretoria');
        $supervisor = $this->input->get('supervisor');
        $mes = $this->input->get('mes');
        $ano = $this->input->get('ano');

        $sqlColunas = "SELECT GROUP_CONCAT(CONCAT(' SUM(IF(h.id = ',  s.id, ', g.qtde, NULL)) AS \'', LCASE(REPLACE(REPLACE(s.nome, '-', ''), ' ', '_'))), '\''
                        ) AS insumo
               FROM (SELECT * FROM ei_insumos ORDER BY nome) s";

        $rowColunas = $this->db->query($sqlColunas)->row();
        $colunas = convert_accented_characters($rowColunas->insumo);

        $sql = "SELECT d.id AS id_escola, 
                       x.escola, 
                       (SELECT COUNT(DISTINCT (IFNULL(s.id_aluno, '')))
                        FROM ei_alocados t 
                        LEFT JOIN ei_matriculados s 
                                  ON s.escola = t.escola 
                                  AND s.turno = t.turno
                                  AND s.status IN ('A','N')
                        WHERE t.escola = d.nome
                              AND t.id_alocacao = b.id) AS total_alunos,
                       IFNULL(a.aluno, '&nbsp;') AS aluno,
                       {$colunas}, 
                       SUM(g.qtde) AS total
                FROM ei_alocados x
                INNER JOIN ei_alocacao b ON
                           b.id = x.id_alocacao
                INNER JOIN ei_diretorias c ON
                           c.nome = b.diretoria AND 
                           c.id_empresa = b.id_empresa
                INNER JOIN ei_escolas d ON
                           d.id_diretoria = c.id AND 
                           d.nome = x.escola
                LEFT JOIN ei_matriculados a 
                          ON a.id_alocacao = b.id 
                          AND a.escola = x.escola
                          AND a.turno = x.turno
                          AND a.status IN ('A','N')
                LEFT JOIN ei_frequencias f ON
                          f.id_matriculado = a.id
                LEFT JOIN ei_consumos g ON
                          g.id_frequencia = f.id
                LEFT JOIN ei_insumos h ON
                          h.id = g.id_insumo
                WHERE (b.depto = '{$depto}' OR CHAR_LENGTH('{$depto}') = 0)
                      AND (c.id = '{$diretoria}' OR CHAR_LENGTH('{$diretoria}') = 0)
                      AND (b.supervisor = (SELECT nome 
                                           FROM usuarios 
                                           WHERE id = '{$supervisor}') 
                          OR CHAR_LENGTH('{$supervisor}') = 0)
                      AND DATE_FORMAT(b.data, '%Y-%m') = '{$ano}-{$mes}'
                GROUP BY x.escola, x.turno, a.aluno
                ORDER BY x.escola ASC, FIELD(x.turno, 'M', 'T', 'N'), a.aluno ASC";

        $data['registros'] = $this->db->query($sql)->result_array();

        $sqlNomeColunas = "SELECT LCASE(REPLACE(REPLACE(a.nome, '-', ''), ' ', '_')) AS id, 
                                  IF(SUM(s.qtde) > 0, CONCAT(a.nome, ' (', SUM(s.qtde), ')'), a.nome) AS nome 
                           FROM ei_insumos a 
                           LEFT JOIN (SELECT b.id_insumo, 
                                             b.qtde 
                                      FROM ei_alocados x 
                                      INNER JOIN ei_alocacao e ON e.id = x.id_alocacao
                                                 AND DATE_FORMAT(e.data, '%Y-%m') = '{$ano}-{$mes}'
                                      INNER JOIN ei_diretorias f 
                                                 ON f.nome = e.diretoria 
                                                 AND f.id_empresa = e.id_empresa
                                                 AND (f.depto = '{$depto}' OR CHAR_LENGTH('{$depto}') = 0) 
                                                 AND (f.id = '{$diretoria}' OR CHAR_LENGTH('{$diretoria}') = 0)
                                      INNER JOIN ei_escolas g 
                                                 ON g.id_diretoria = f.id 
                                      INNER JOIN ei_supervisores h
                                                 ON h.id_escola = g.id
                                                 AND (h.id_supervisor = '{$supervisor}' OR CHAR_LENGTH('{$supervisor}') = 0)
                                      LEFT JOIN ei_matriculados d 
                                                ON d.id_alocacao = e.id
                                                AND d.escola = x.escola 
                                                AND d.turno = x.turno
                                      LEFT JOIN ei_frequencias c 
                                                ON c.id_matriculado = d.id
                                                AND DATE_FORMAT(c.data, '%Y-%m') = '{$ano}-{$mes}' 
                                      LEFT JOIN ei_consumos b ON b.id_frequencia = c.id) s 
                                     ON s.id_insumo = a.id
                           GROUP BY a.id ORDER BY a.nome";

        $rows = $this->db->query($sqlNomeColunas)->result();

        $data['titulos'] = [];
        foreach ($rows as $row) {
            $data['titulos'][convert_accented_characters($row->id)] = $row->nome;
        }

        return $data;
    }

    //--------------------------------------------------------------------

    private function ajaxObservacoes(): array
    {
        $busca = $this->input->get();

        $sqlSemana = "SELECT DAY(CASE WEEKDAY(a.data) 
                                      WHEN 5 THEN DATE_ADD(a.data, INTERVAL 2 DAY)
                                      WHEN 6 THEN DATE_ADD(a.data, INTERVAL 1 DAY)
                                      ELSE a.data END) AS data_ini,
           DAY(LAST_DAY(a.data)) AS data_fim
                      FROM (SELECT STR_TO_DATE('{$busca['ano']}-{$busca['mes']}-01','%Y-%m-%d') as data) a";
        $dias = $this->db->query($sqlSemana)->row();

        $primeiraSemana = 8 - date('N', strtotime($busca['ano'] . '-' . $busca['mes'] . '-01'));
        $semana = [];
        for ($i = $dias->data_ini; $i <= $dias->data_fim; $i += $primeiraSemana) {
            $semana[] = [
                'data_ini' => $i,
                'data_fim' => min($i + ($i > $dias->data_ini ? 4 : $primeiraSemana - 3), $dias->data_fim),
            ];
            if ($i > $dias->data_ini) {
                $primeiraSemana = 7;
            }
            if ($i > $dias->data_fim) {
                break;
            }
        }

        $data = ['semanas' => $semana];

        $sql = "SELECT a.status, 
                       CASE a.status
                            WHEN 'FA' THEN 'Falta com atestado'
                            WHEN 'FS' THEN 'Falta sem atestado'
                            WHEN 'FE' THEN 'Feriado escola'
                            WHEN 'EM' THEN 'Emenda Feriado'
                            WHEN 'AA' THEN 'Aluno ausente'
                            WHEN 'AF' THEN 'Afastamento'
                            WHEN 'AP' THEN 'Apontamento'
                            WHEN 'AD' THEN 'Funcionário admitido'
                            WHEN 'AT' THEN 'Acidente de trabalho'
                            WHEN 'DE' THEN 'Funcionário demitido'
                            WHEN 'FC' THEN 'Feriado escola/cuidador'
                            WHEN 'IA' THEN 'Intercorrência de alunos'
                            WHEN 'IC' THEN 'Intercorrência de cuidadores'
                            WHEN 'ID' THEN 'Intercorrência de diretoria'
                            WHEN 'NA' THEN 'Funcionário não-alocado'
                            WHEN 'RE' THEN 'Funcionário remanejado'
                            WHEN 'SL' THEN 'Sábado letivo'
                            ELSE 'Outro' END AS nome_status, 
                       CASE (DAY(a.data) + (CASE WHEN WEEKDAY(DATE_SUB(a.data, INTERVAL (DAY(a.data) - 1) DAY)) < 5 
                                                 THEN WEEKDAY(DATE_SUB(a.data, INTERVAL (DAY(a.data) - 1) DAY)) 
                                                 ELSE 0 END) + (6 - WEEKDAY(a.data))) / 7 
                            WHEN 1 THEN 'semana1' 
                            WHEN 2 THEN 'semana2' 
                            WHEN 3 THEN 'semana3' 
                            WHEN 4 THEN 'semana4'
                            WHEN 5 THEN 'semana5' 
                            END as semana, 
                       e.id, 
                       e.nome, 
                       a.id_alocado, 
                       a.observacoes, 
                       DAY(a.data) AS dia
                FROM ei_apontamento a
                INNER JOIN ei_alocados b ON 
                           b.id = a.id_alocado
                INNER JOIN ei_alocacao c ON 
                           c.id = b.id_alocacao
                INNER JOIN ei_diretorias g ON 
                           g.depto = c.depto AND
                           g.nome = c.diretoria AND 
                           g.municipio = c.municipio
                INNER JOIN ei_escolas f ON 
                           f.id_diretoria = g.id
                INNER JOIN ei_supervisores h ON
                           h.id_escola = f.id
                LEFT JOIN ei_cuidadores d on 
                           d.id = b.id_vinculado
                LEFT JOIN usuarios e ON 
                           e.id = d.id_cuidador                
                WHERE DATE_FORMAT(c.data, '%Y-%m') = '{$busca['ano']}-{$busca['mes']}'";
        if (isset($busca['depto'])) {
            $sql .= " AND g.depto = '{$busca['depto']}'";
        }
        if (isset($busca['diretoria'])) {
            $sql .= " AND g.id = '{$busca['diretoria']}'";
        }
        if (isset($busca['supervisor'])) {
            $sql .= " AND h.id_supervisor = '{$busca['supervisor']}'";
        }
        $sql .= 'GROUP BY b.escola, b.turno ORDER BY a.status, a.data';

        $rows = $this->db->query($sql)->result();

        $arr_observacoes = [];

        foreach ($rows as $row) {
            $data[$row->status] = [
                'status' => $row->nome_status,
                'semana1' => [],
                'semana2' => [],
                'semana3' => [],
                'semana4' => [],
                'semana5' => [],
            ];
            $arr_observacoes[] = $row->observacoes;
        }

        $arr_observacoes = array_unique($arr_observacoes);

        foreach ($rows as $row2) {
            $data[$row2->status][$row2->semana][$row2->id]['nome'] = $row2->nome;

            $key_obs = array_search($row2->observacoes, $arr_observacoes);

            $data[$row2->status][$row2->semana][$row2->id]['observacoes'][$key_obs]['nome'] = $row2->observacoes;
            $data[$row2->status][$row2->semana][$row2->id]['observacoes'][$key_obs]['dias'][] = $row2->dia;
        }

        return $data;
    }

    //--------------------------------------------------------------------

    private function getIdMes(?string $mes, ?int $semestre): int
    {
        $semestre = intval($mes) > 7 ? 2 : (intval($mes) < 7 ? 1 : $semestre);
        return $mes - ($semestre > 1 ? 6 : 0);
    }

    //--------------------------------------------------------------------

    public function medicao($isPdf = false)
    {
        $data['empresa'] = $this->db
            ->select('foto, foto_descricao')
            ->where('id', $this->session->userdata('empresa'))
            ->get('usuarios')
            ->row();

        $data['is_pdf'] = $isPdf === true;

        $mes = $this->input->get('mes');
        if (strlen($mes) == 0) {
            $mes = date('m');
        }
        $ano = $this->input->get('ano');
        if (strlen($ano) == 0) {
            $ano = date('Y');
        }
        $dataInicioMes = "{$ano}-{$mes}-01";
        $dataTerminoMes = date('Y-m-t', strtotime($dataInicioMes));

        if (checkdate($mes, 1, $ano) == false or strlen($mes) !== 2 or strlen($ano) !== 4) {
            redirect(site_url('ei/relatorios/medicao'));
        }

        $this->load->library('Calendar');
        $data['mes_nome'] = $this->calendar->get_month_name($mes);
        $data['mes'] = $mes;
        $data['ano'] = $ano;
        $data['semestre'] = $this->input->get('semestre');
        $idMes = intval($mes) - ($data['semestre'] > 1 ? 6 : 0);
        $data['query_string'] = http_build_query($this->input->get());
        $idDiretoria = $this->input->get('diretoria');
        $data['id_diretoria'] = $idDiretoria;
        $data['depto'] = $this->input->get('depto');

        $data['alocacao'] = $this->db
            ->select('id AS id_medicao_mensal, total_escolas, total_alunos, observacoes')
            ->group_start()
            ->group_start()
            ->where('depto', $this->input->get('depto'))
            ->where('id_diretoria', $this->input->get('diretoria'))
            ->group_end()
            ->or_where('(depto IS NULL AND id_diretoria IS NULL)', null, false)
            ->group_end()
            ->where('ano', $this->input->get('ano'))
            ->where('semestre', $this->input->get('semestre'))
            ->where('mes', $this->input->get('mes'))
            ->get('ei_medicao_mensal')
            ->row();

        $this->load->helper('time');

        if (!empty($data['alocacao'])) {
            $data['funcoes'] = $this->db
                ->select('a.*, c.id AS id_funcao', false)
                ->select('a.funcao AS nome, a.resultado_monetario AS resultado', false)
                ->select('NULL AS total_segundos_mes, 0 AS total_secs_realizados', false)
                ->join('empresa_cargos b', 'b.nome = a.cargo', 'left')
                ->join('empresa_funcoes c', 'c.nome = a.funcao AND c.id_cargo = b.id', 'left')
                ->where('id_medicao_mensal', $data['alocacao']->id_medicao_mensal ?? null)
                ->get('ei_medicao_mensal_funcoes a')
                ->result();
            foreach ($data['funcoes'] as &$funcoes) {
                $funcoes->total_segundos_mes = timeToSec($funcoes->total_horas);
            }
        } else {
            $data['alocacao'] = $this->db
                ->select('NULL AS id_medicao_mensal, NULL AS observacoes', false)
                ->select(["GROUP_CONCAT(DISTINCT a.id ORDER BY a.id ASC SEPARATOR ',') AS id"], false)
                ->select('COUNT(DISTINCT(escola)) AS total_escolas', false)
                ->select('COUNT(DISTINCT(aluno)) AS total_alunos', false)
                ->join('ei_alocacao_escolas b', 'b.id_alocacao = a.id')
                ->join('ei_matriculados c', 'c.id_alocacao_escola = b.id', 'left')
                ->where('a.id_empresa', $this->session->userdata('empresa'))
                ->where('a.ano', $this->input->get('ano'))
                ->where('a.semestre', $this->input->get('semestre'))
                ->group_start()
                ->where('a.id_diretoria', $idDiretoria)
                ->or_where("CHAR_LENGTH('{$idDiretoria}') =", 0)
                ->group_end()
                ->get('ei_alocacao a')
                ->row();

            $idAlocacoes = explode(',', $data['alocacao']->id ?? '0');

            $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';

            $subquery = $this->db
                ->select("d.id_alocacao, a.cuidador, j.cargo{$mesCargoFuncao} AS cargo, j.funcao{$mesCargoFuncao} AS funcao, l.id AS id_funcao, j.valor_hora_funcao", false)
                ->select(["GREATEST(  SUM(IFNULL(TIME_TO_SEC(a.total_horas_mes{$idMes}), 0) - IFNULL(TIME_TO_SEC(i.total_horas_mes{$idMes}), 0))  , 0) AS segundos_projetados_mes{$idMes}"], false)
                ->select(["GREATEST(  SUM(IFNULL(TIME_TO_SEC(a.total_horas_mes{$idMes}), 0) - IFNULL(TIME_TO_SEC(i.total_horas_mes{$idMes}), 0))  , 0) AS segundos_realizados_mes{$idMes}"], false)
                ->select(["IFNULL(a.valor_total_mes{$idMes}, j.valor_hora_operacional * ((IFNULL(TIME_TO_SEC(j.horas_mensais_custo), 0) + IFNULL(TIME_TO_SEC(a.horas_descontadas_mes{$idMes}), 0)) / 3600)) AS pagamentos_efetuados2"], false)
                ->select(["IFNULL(a.valor_total_mes{$idMes}, 0) - IFNULL(i.valor_total_mes{$idMes}, 0) AS pagamentos_efetuados"], false)
                ->join('usuarios b', 'b.id = a.id_cuidador')
                ->join('ei_alocados c', 'c.id = a.id_alocado')
                ->join('ei_alocacao_escolas d', 'd.id = c.id_alocacao_escola')
                ->join('ei_alocacao e', 'e.id = d.id_alocacao')
                ->join('ei_ordem_servico e2', 'e2.nome = d.ordem_servico AND e2.ano = e.ano AND e2.semestre = e.semestre')
                ->join("(SELECT * FROM ei_alocados_horarios GROUP BY id_alocado, periodo,cargo, funcao{$mesCargoFuncao}) j", "j.id_alocado = c.id AND j.periodo = a.periodo AND j.cargo{$mesCargoFuncao} = a.cargo{$mesCargoFuncao} AND j.funcao{$mesCargoFuncao} = a.funcao{$mesCargoFuncao}", 'left', false)
                ->join('empresa_cargos l1', 'l1.nome = j.cargo', 'left')
                ->join('empresa_funcoes l', "l.nome = j.funcao{$mesCargoFuncao} AND l.id_cargo = l1.id", 'left')
                ->join('ei_alocados_totalizacao i', 'i.id_alocado = a.id_alocado AND i.periodo = a.periodo AND i.substituicao_semestral IS NOT NULL AND a.substituicao_semestral IS NULL AND i.substituicao_eventual IS NULL', 'left')
                ->join('ei_faturamento h', "h.id_alocacao = e.id AND h.id_escola = d.id_escola AND h.cargo = j.cargo AND h.funcao = j.funcao{$mesCargoFuncao}", 'left')
                ->where_in('e.id', $idAlocacoes, false)
                ->group_start()
                ->where('j.data_inicio_real <=', $dataTerminoMes)
                ->or_where('j.data_inicio_real', null)
                ->group_end()
                ->group_start()
                ->where('j.data_termino_real >=', $dataInicioMes)
                ->or_where('j.data_termino_real', null)
                ->group_end()
                ->where('a.substituicao_eventual IS NULL')
                ->where('j.id IS NOT NULL')
                ->group_by(['d.id_escola', 'a.id_cuidador', 'j.periodo', 'j.cargo', 'j.funcao' . $mesCargoFuncao, 'c.id', 'a.periodo'])
                ->order_by('j.funcao' . $mesCargoFuncao, 'asc')
                ->get_compiled_select('ei_alocados_totalizacao a');

            $data['funcoes'] = $this->db
                ->select('NULL AS id, s.id_alocacao, s.cargo, s.funcao AS nome, s.id_funcao', false)
                ->select('COUNT(DISTINCT(s.cuidador)) AS total_pessoas', false)
                ->select('NULL AS total_secs_projetados', false)
                ->select('NULL AS total_secs_realizados', false)
                ->select("SUM(s.segundos_realizados_mes{$idMes}) AS total_segundos_mes", false)
                ->select('NULL AS resultado', false)
                ->select(["IFNULL(t.valor_hora_mes{$idMes}, SUM(s.valor_hora_funcao)) AS valor_hora"], false)
                ->select(["FORMAT(t.valor_faturado_mes{$idMes}, 2, 'de_DE') AS receita_projetada"], false)
                ->select(["FORMAT(s.valor_hora_funcao * (SUM(s.segundos_realizados_mes{$idMes}) / 3600), 2, 'de_DE') AS receita_efetuada"], false)
                ->select(["GREATEST(SUM(IFNULL(s.pagamentos_efetuados, 0)), 0) AS pagamentos_efetuados"], false)
                ->select(["FORMAT((s.valor_hora_funcao * (SUM(s.segundos_realizados_mes{$idMes}) / 3600)) - SUM(IFNULL(s.pagamentos_efetuados, 0)), 2, 'de_DE') AS resultado"], false)
                ->select(["((s.valor_hora_funcao * (SUM(s.segundos_realizados_mes{$idMes}) / 3600)) - SUM(IFNULL(s.pagamentos_efetuados, 0))) / GREATEST(s.valor_hora_funcao * (SUM(s.segundos_realizados_mes{$idMes}) / 3600), 1) * 100 AS resultado_percentual"], false)
                ->from("({$subquery}) s")
                ->join('ei_faturamento_consolidado t', 't.id_alocacao = s.id_alocacao AND t.cargo = s.cargo AND t.funcao = s.funcao', 'left')
                ->group_by(['s.cargo', 's.funcao'])
                ->order_by('s.funcao', 'asc')
                ->get()
                ->result();
        }

        if ($data['is_pdf']) {
            return $this->load->view('ei/relatorio_medicao', $data, true);
        }

        $this->load->view('ei/relatorio_medicao', $data);
    }

    //--------------------------------------------------------------------

    public function medicoes_consolidadas($isPdf = false)
    {
        $data['empresa'] = $this->db
            ->select('foto, foto_descricao')
            ->where('id', $this->session->userdata('empresa'))
            ->get('usuarios')
            ->row();

        $data['is_pdf'] = $isPdf === true;

        $ano = $this->input->get('ano');
        if (strlen($ano) == 0 or strlen($ano) !== 4) {
            $ano = date('Y');
        }

        $data['depto'] = $this->input->get('depto');
        $data['diretoria'] = $this->input->get('diretoria');
        $data['ano'] = $ano;
        $data['query_string'] = http_build_query($this->input->get());

        $data['alocacao'] = new stdClass();
        $data['funcoes'] = [];

        $totalPessoasMes = [];
        $totalHorasUtilizadas = [];
        $totalReceita = [];
        $totalPagamentos = [];
        $totalResultado = [];
        $totalResultadoPercentual = [];

        $alocacao = $this->db
            ->select('e.nome AS cargo, f.nome AS funcao, d.qtde_horas')
            ->where('a.id_empresa', $this->session->userdata('empresa'))
            ->where('a.depto', $this->input->get('depto'))
            ->where('a.id_diretoria', $this->input->get('diretoria'))
            ->where('a.ano', $this->input->get('ano'))
            ->join('ei_ordem_servico b', 'b.id = a.id_ordem_servico', 'left')
            ->join('ei_contratos c', 'c.id = b.id_contrato', 'left')
            ->join('ei_valores_faturamento d', 'd.id_contrato = c.id AND d.ano = a.ano AND d.semestre = a.semestre', 'left')
            ->join('empresa_cargos e', 'e.id = d.id_cargo', 'left')
            ->join('empresa_funcoes f', 'f.id = d.id_funcao', 'left')
            ->get('ei_alocacao a')
            ->result();

        $this->load->helper('time');
        $totalHorasAlocadas = [];
        foreach ($alocacao as $row) {
            $totalHorasAlocadas[$row->cargo][$row->funcao] = timeSimpleFormat($row->qtde_horas);
        }

        for ($i = 1; $i <= 13; $i++) {
            $semestre = $i > 7 ? 2 : 1;
            $idMes = $i - ($semestre > 1 ? 7 : 0);
            $mes = $i === 7 ? '7_1' : ($i === 8 ? '7_2' : ($i - ($semestre > 1 ? 1 : 0)));
            $mes2 = $i - ($i > 7 ? 1 : 0);

            $medicaoMensal = $this->db
                ->select('a.*, b.nome AS diretoria', false)
                ->join('ei_diretorias b', 'b.id = a.id_diretoria', 'left')
                ->where('a.ano', $this->input->get('ano'))
                ->where('a.semestre', $semestre)
                ->where('a.mes', $mes2)
                ->get('ei_medicao_mensal a')
                ->row();

            if (empty($medicaoMensal)) {
                continue;
            }

            $data['alocacao']->diretoria = $medicaoMensal->diretoria ?? null;
            $data['alocacao']->{'total_escolas_mes' . $mes} = $medicaoMensal->total_escolas;
            $data['alocacao']->{'total_alunos_mes' . $mes} = $medicaoMensal->total_alunos;

            $medicaoMensalFuncoes = $this->db
                ->select("cargo, funcao AS nome", false)
                ->select("total_pessoas AS total_pessoas_mes{$idMes}", false)
                ->select("total_horas AS total_horas_mes{$idMes}", false)
                ->select("receita_efetuada AS receita_efetuada_mes{$idMes}", false)
                ->select("pagamentos_efetuados AS pagamentos_efetuados_mes{$idMes}", false)
                ->select("resultado_monetario AS resultado_mes{$idMes}", false)
                ->select("resultado_percentual AS resultado_percentual_mes{$idMes}", false)
                ->where('id_medicao_mensal', $medicaoMensal->id ?? null)
                ->get('ei_medicao_mensal_funcoes')
                ->result();

            foreach ($medicaoMensalFuncoes as $funcao) {
                $funcao->total_horas_alocadas = $totalHorasAlocadas[$funcao->cargo][$funcao->nome] ?? null;
                $funcao->{'total_segundos_mes' . $mes} = timeToSec($funcao->{'total_horas_mes' . $mes});

                $totalPessoasMes[$funcao->nome][] = $funcao->{'total_pessoas_mes' . $mes};
                $totalHorasUtilizadas[$funcao->nome][] = $funcao->{'total_segundos_mes' . $mes};
                $totalReceita[$funcao->nome][] = $funcao->{'receita_efetuada_mes' . $mes};
                $totalPagamentos[$funcao->nome][] = $funcao->{'pagamentos_efetuados_mes' . $mes};
                $totalResultado[$funcao->nome][] = $funcao->{'resultado_mes' . $mes};
                $totalResultadoPercentual[$funcao->nome][] = $funcao->{'resultado_percentual_mes' . $mes};

                $funcao->{'total_horas_mes' . $mes} = timeSimpleFormat($funcao->{'total_horas_mes' . $mes});

                $funcao->total_horas_utilizadas = array_sum($totalHorasUtilizadas[$funcao->nome]);
                $funcao->total_receita = array_sum($totalReceita[$funcao->nome]);
                $funcao->total_pagamentos = array_sum($totalPagamentos[$funcao->nome]);
                $funcao->total_resultado = array_sum($totalResultado[$funcao->nome]);
                $funcao->total_resultado_percentual = array_sum($totalResultadoPercentual[$funcao->nome]) / count($totalResultadoPercentual[$funcao->nome]);

                if (array_key_exists($funcao->nome, $data['funcoes'])) {
                    $data['funcoes'][$funcao->nome] = (object)array_merge((array)$data['funcoes'][$funcao->nome], (array)$funcao);
                } else {
                    $data['funcoes'][$funcao->nome] = $funcao;
                }
            }
        }

        $this->load->helper('time');

        if ($data['is_pdf']) {
            return $this->load->view('ei/relatorio_medicoes_consolidadas', $data, true);
        }

        $this->load->view('ei/relatorio_medicoes_consolidadas', $data);
    }

    //--------------------------------------------------------------------

    public function pdf()
    {
        $this->load->library('m_pdf');

        $stylesheet = '#table thead tr th { border-top: 4px solid #ddd; padding-top: 8px; } ';
        $stylesheet .= '#funcionarios { border: 1px solid #444; margin-bottom: 0px; } ';
        $stylesheet .= '#funcionarios thead th { font-size: 12px; padding: 4px; background-color: #f5f5f5; border: 1px solid #444; } ';
        $stylesheet .= '#funcionarios tbody td { font-size: 10px; padding: 4px; vertical-align: top; border: 1px solid #444; } ';

        $stylesheet .= '#legenda { border: 0px solid #444; margin-bottom: 0px; } ';
        $stylesheet .= '#legenda thead th { font-size: 12px; padding: 4px; background-color: #f5f5f5; border-bottom: 2px solid #444; } ';
        $stylesheet .= '#legenda tbody td { font-size: 12px; padding: 4px; vertical-align: top; border-bottom: 1px solid #444; } ';
        $stylesheet .= '#legenda tbody tr:nth-child(8) td { font-size: 13px; padding: 5px; font-weight: bold; background-color: #f5f5f5; } ';

        $stylesheet .= '#observacoes { border: 1px solid #444; margin-bottom: 0px; } ';
        $stylesheet .= '#observacoes thead th { font-size: 12px; padding: 4px; background-color: #f5f5f5; border: 1px solid #444; } ';
        $stylesheet .= '#observacoes tbody td { font-size: 10px; padding: 4px; vertical-align: top; border: 1px solid #444; } ';

        $this->m_pdf->pdf->setTopMargin(38);
        $this->m_pdf->pdf->AddPage('L');
        $this->m_pdf->pdf->writeHTML($stylesheet, 1);
        $this->m_pdf->pdf->writeHTML($this->funcionarios(true));

        $data = $this->input->get();

        $this->load->library('Calendar');
        $this->calendar->month_type = 'short';
        $nome = 'Medição de Funcionários - ' . $this->calendar->get_month_name($data['mes']) . '_' . $data['ano'];

        $this->m_pdf->pdf->Output($nome . '.pdf', 'D');
    }

    //--------------------------------------------------------------------

    public function pdf_medicao()
    {
        $this->load->library('m_pdf');

        $stylesheet = '#table thead tr th { border-top: 4px solid #ddd; padding-top: 8px; } ';
        $stylesheet .= 'table.medicao {  border: 1px solid #333; margin-bottom: 0px; } ';
        $stylesheet .= 'table.medicao thead tr th { font-size: 13px; padding: 4px; background-color: #f5f5f5; border: 1px solid #333;  } ';
        $stylesheet .= 'table.medicao tbody tr td { font-size: 12px; padding: 4px; vertical-align: top; border: 1px solid #333;  } ';

        $this->m_pdf->pdf->setTopMargin(52);
        $this->m_pdf->pdf->AddPage('L');
        $this->m_pdf->pdf->writeHTML($stylesheet, 1);
        $this->m_pdf->pdf->writeHTML($this->medicao(true));

        $data = $this->input->get();

        $this->load->library('Calendar');
        $this->calendar->month_type = 'short';
        $nome = 'Relatório de Medição Mensal de Educação Inclusiva - ' . $this->calendar->get_month_name($data['mes']) . '_' . $data['ano'];

        $this->m_pdf->pdf->Output($nome . '.pdf', 'D');
    }

    //--------------------------------------------------------------------

    public function pdf_medicoes_consolidadas()
    {
        $this->load->library('m_pdf');

        $stylesheet = '#table thead tr th { border-top: 4px solid #ddd; padding-top: 8px; } ';
        $stylesheet .= 'table.medicoes_consolidadas {  border: 1px solid #333; margin-bottom: 0px; } ';
        $stylesheet .= 'table.medicoes_consolidadas thead tr th { font-size: 11px; padding: 4px; background-color: #f5f5f5; border: 1px solid #333;  } ';
        $stylesheet .= 'table.medicoes_consolidadas tbody tr td { font-size: 10px; padding: 4px; vertical-align: top; border: 1px solid #333;  } ';

        $this->m_pdf->pdf->setTopMargin(72);
        $this->m_pdf->pdf->AddPage('L');
        $this->m_pdf->pdf->writeHTML($stylesheet, 1);
        $this->m_pdf->pdf->writeHTML($this->medicoes_consolidadas(true));

        $data = $this->input->get();

        $nome = 'Relatório de Medições Consolidadas de Educação Inclusiva - ' . $data['ano'];

        $this->m_pdf->pdf->Output($nome . '.pdf', 'D');
    }

    //--------------------------------------------------------------------

    public function pdf_escolas()
    {
        $this->load->library('m_pdf');

        $stylesheet = '#table thead tr th { border-top: 4px solid #ddd; padding-top: 8px; } ';
        $stylesheet .= '#escolas { border: 1px solid #444; margin-bottom: 0px; } ';
        $stylesheet .= '#escolas thead th { font-size: 12px; padding: 4px; background-color: #DFF0D8; border: 1px solid #444; } ';
        $stylesheet .= '#escolas thead th { font-size: 12px; padding: 4px; background-color: #f5f5f5; border: 1px solid #444; } ';
        $stylesheet .= '#escolas tbody td { font-size: 10px; padding: 4px; vertical-align: top; border: 1px solid #444; } ';

        $stylesheet .= '#legenda { border: 0px solid #444; margin-bottom: 0px; } ';
        $stylesheet .= '#legenda thead th { font-size: 12px; padding: 4px; background-color: #f5f5f5; border-bottom: 2px solid #444; } ';
        $stylesheet .= '#legenda tbody td { font-size: 12px; padding: 4px; vertical-align: top; border-bottom: 1px solid #444; } ';
        $stylesheet .= '#legenda tbody tr:nth-child(8) td { font-size: 13px; padding: 5px; font-weight: bold; background-color: #f5f5f5; } ';

        $stylesheet .= '#observacoes { border: 1px solid #444; margin-bottom: 0px; } ';
        $stylesheet .= '#observacoes thead th { font-size: 12px; padding: 4px; background-color: #f5f5f5; border: 1px solid #444; } ';
        $stylesheet .= '#observacoes tbody td { font-size: 10px; padding: 4px; vertical-align: top; border: 1px solid #444; } ';

        $this->m_pdf->pdf->setTopMargin(38);
        $this->m_pdf->pdf->AddPage('L');
        $this->m_pdf->pdf->writeHTML($stylesheet, 1);
        $this->m_pdf->pdf->writeHTML($this->escolas(true));

        $data = $this->input->get();

        $this->load->library('Calendar');
        $this->calendar->month_type = 'short';
        $nome = 'Medição de Escolas - ' . $this->calendar->get_month_name($data['mes']) . '_' . $data['ano'];

        $this->m_pdf->pdf->Output($nome . '.pdf', 'D');
    }

    //--------------------------------------------------------------------

    public function pdf_insumos()
    {
        $this->load->library('m_pdf');

        $stylesheet = '#table thead tr th { border-top: 4px solid #ddd; padding-top: 8px; } ';
        $stylesheet .= '#insumos { border: 1px solid #444; margin-bottom: 0px; } ';
        $stylesheet .= '#insumos thead th { font-size: 12px; padding: 4px; background-color: #f5f5f5; border: 1px solid #444; } ';
        $stylesheet .= '#insumos tbody td { font-size: 10px; padding: 4px; vertical-align: top; border: 1px solid #444; } ';

        $this->m_pdf->pdf->setTopMargin(38);
        $this->m_pdf->pdf->AddPage('L');
        $this->m_pdf->pdf->writeHTML($stylesheet, 1);
        $this->m_pdf->pdf->writeHTML($this->insumos(true));

        $data = $this->input->get();

        $this->load->library('Calendar');
        $this->calendar->month_type = 'short';
        $nome = 'Apontamento de Insumos - ' . $this->calendar->get_month_name($data['mes']) . '_' . $data['ano'];

        $this->m_pdf->pdf->Output($nome . '.pdf', 'D');
    }

    //--------------------------------------------------------------------

    public function pdf_usuario_frequencias()
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

        if ($this->input->get('aprovacao')) {
            $usuarioAtual = $this->db
                ->select('assinatura_digital')
                ->where('id', $this->session->userdata('id'))
                ->get('usuarios')
                ->row();
        }

        $usuario = $this->db
            ->select('a.id, a.nome, a.cnpj, a.assinatura_digital, b.nome AS funcao')
            ->join('empresa_funcoes b', 'b.id = a.id_funcao')
            ->where('a.id', $profissional)
            ->get('usuarios a')
            ->row();

        $alocacaoEscola = $this->db
            ->select("a.id, GROUP_CONCAT(b.escola ORDER BY b.escola ASC SEPARATOR ' / ') AS escola")
            ->select('d.assinatura_digital, e.assinatura_digital AS assinatura_coordenador')
            ->select('f.assinatura_digital AS assinatura_aprovacao, f.arquivo_medicao')
            ->join('ei_alocacao_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacao c', 'c.id = b.id_alocacao')
            ->join('usuarios d', 'd.id = c.id_supervisor')
            ->join('usuarios e', 'e.id = c.coordenador')
            ->join('ei_alocados_aprovacoes f', "f.id_alocado = a.id AND f.mes_referencia = '{$mes}'", 'left')
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
        $data['assinatura_digital_supervisor'] = $alocacaoEscola->assinatura_digital ?? $usuarioAtual->assinatura_digital ?? null;
        $data['assinatura_digital_aprovacao'] = $alocacaoEscola->assinatura_aprovacao ?? null;
        $data['query_string'] = http_build_query($this->input->get());
        $data['is_pdf'] = true;

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
            ->where('MONTH(data_evento)', $mes)
            ->where('YEAR(data_evento)', $ano);
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
            ->order_by('data_evento', 'asc')
            ->get('ei_usuarios_frequencias a')
            ->result();

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
        $data['total_dias'] = $this->input->get('total_dias');
        $data['total_horas'] = $this->input->get('total_horas');

        if ($descontos->id) {
            if (empty($data['total_dias'])) {
                $data['total_dias'] = (int)$descontos->desconto_dias;
            }
            if (empty($data['total_horas'])) {
                $data['total_horas'] = timeSimpleFormat($descontos->desconto_horas);
            }
        } else {
            if (empty($data['total_dias'])) {
                $data['total_dias'] = count($totalDias) - $descontos->desconto_dias;
            }
            if (empty($data['total_horas'])) {
                $data['total_horas'] = secToTime($totalHoras - timeToSec($descontos->desconto_horas), false);
            }
        }

        $this->load->library('m_pdf');

        $stylesheet = '#table_2 thead th { font-size: 12px; padding: 5px; background-color: #f5f5f5;} ';
        $stylesheet .= '#table_2 tbody td { font-size: 12px; padding: 5px; vertical-align: top; } ';
        $stylesheet .= '#livro_ata thead tr th { padding: 5px; font-size: 12px; text-align: center; background-color: #f5f5f5; border-color: #ddd; } ';
        $stylesheet .= '#livro_ata tbody tr td { font-size: 10px; padding: 5px; } ';

        $this->m_pdf->pdf->setTopMargin(90);
        $this->m_pdf->pdf->AddPage('P');
        $this->m_pdf->pdf->writeHTML($stylesheet, 1);
        $this->m_pdf->pdf->writeHTML($this->load->view('ei/usuario_frequencias_pdf', $data, true));

        $this->calendar->month_type = 'short';

        if ($this->input->get('aprovacao')) {
            $codEscola = $this->db
                ->select('codigo')
                ->where('id', $escola)
                ->get('ei_escolas')
                ->row();

            $this->m_pdf->pdf->Output(implode('-', [$mes, $ano, $codEscola->codigo ?? $data['escola'], $data['profissional']]) . '.pdf', 'D');
        } else {
            $this->m_pdf->pdf->Output('Frequências usuário - ' . implode(' - ', [$data['profissional'], $data['mes_ano']]) . '.pdf', 'D');
        }
    }

    //--------------------------------------------------------------------

    public function pdf_cuidadores()
    {
        $id_empresa = $this->session->userdata('empresa');
        $diretoria = $this->input->get('diretoria');
        $depto = $this->input->get('depto');
        $supervisor = $this->input->get('supervisor');
        $mes = $this->input->get('mes');
        $ano = $this->input->get('ano');

        $empresa = $this->db
            ->select('foto, foto_descricao')
            ->get_where('usuarios', ['id' => $id_empresa])
            ->row();

        if (is_file('imagens/usuarios/' . $empresa->foto)) {
            $empresa->foto = base_url('imagens/usuarios/' . $empresa->foto);
        }
        if (is_file('imagens/usuarios/' . $empresa->foto_descricao)) {
            $empresa->foto_descricao = base_url('imagens/usuarios/' . $empresa->foto_descricao);
        }
        $data['empresa'] = $empresa;

        $sql = "SELECT s.id,
                       s.municipio_escola,
                       s.cuidador,
                       s.data_admissao,
                       s.vale_transporte,
                       s.id_turno,
                       s.aluno,
                       s.hipotese_diagnostica,
                       s.turno
                FROM (SELECT a.id,
                             CONCAT(a.municipio, ' &emsp; <strong>Escola:</strong> ', b.escola) AS municipio_escola,
                             (CASE b.turno 
                           WHEN 'M' THEN 1
                           WHEN 'T' THEN 2
                           WHEN 'N' THEN 3
                           END) AS id_turno,
                             (CASE b.turno 
                                   WHEN 'M' THEN 'Manhã'
                                   WHEN 'T' THEN 'Tarde'
                                   WHEN 'N' THEN 'Noite'
                                   END) AS turno,
                     GROUP_CONCAT(CONCAT('<strong>&#149; </strong>', IFNULL(b.cuidador, CONCAT('<span class=\"text-danger\">', IF(b.remanejado = 2, 'Alocar cuidador', IF(b.remanejado = 1, 'Remanejado', 'A contratar')), '</span>')) ) ORDER BY b.cuidador SEPARATOR '<br>') AS cuidador,
                     CASE WHEN b.cuidador IS NOT NULL 
                          THEN GROUP_CONCAT(CONCAT('<strong>&#149; </strong>', c.data_admissao) ORDER BY b.cuidador SEPARATOR '<br>') 
                          END AS data_hora_admissao,
                     CASE WHEN b.cuidador IS NOT NULL
                          THEN GROUP_CONCAT(CONCAT('<strong>&#149; </strong>', DATE_FORMAT(c.data_admissao, '%d/%m/%Y')) ORDER BY b.cuidador SEPARATOR '<br>') 
                          END AS data_admissao,
                     CASE WHEN b.cuidador IS NOT NULL
                          THEN GROUP_CONCAT(CONCAT('<strong>&#149; </strong>', IF(CHAR_LENGTH(c.valor_vt) > 0, CONCAT(c.nome_cartao, ' (', c.valor_vt, ')'), c.nome_cartao)) ORDER BY b.cuidador SEPARATOR '<br>') 
                          END AS vale_transporte,
                             d.aluno,
                             d.hipotese_diagnostica
                      FROM ei_alocacao a
                      INNER JOIN ei_alocados b 
                                 ON b.id_alocacao = a.id
                      LEFT JOIN usuarios c ON
                                c.nome = b.cuidador
                      LEFT JOIN ei_matriculados d 
                                 ON d.id_alocacao = a.id 
                                 AND d.escola = b.escola
                                 AND d.turno = b.turno
                                 AND d.status IN ('A','N')
                      WHERE a.id_empresa = {$id_empresa}
                            AND DATE_FORMAT(a.data, '%Y-%m') = '{$ano}-{$mes}'
                            AND (a.depto = '{$depto}' OR CHAR_LENGTH('{$depto}') = 0)
                            AND (CHAR_LENGTH('{$diretoria}') = 0
                                 OR a.diretoria = (SELECT nome 
                                                   FROM ei_diretorias 
                                                   WHERE id = '{$diretoria}'))
                            AND (CHAR_LENGTH('{$supervisor}') = 0
                                 OR b.supervisor = (SELECT nome 
                                                    FROM usuarios 
                                                    WHERE id = '{$supervisor}'))
                      GROUP BY b.escola, b.turno, d.aluno, d.turno
                      ORDER BY a.municipio, 
                               b.cuidador, 
                               b.escola, 
                               b.turno, 
                               d.aluno) s";

        $data['rows'] = $this->db->query($sql)->result();

        $this->load->library('m_pdf');

        $stylesheet = '#cuidadores thead tr th { font-size: 12px; padding: 5px; text-align: center; font-weight: normal; } ';
        $stylesheet .= '#cuidadores thead tr, #cuidadores tbody tr { border-width: 5px; border-color: #ddd; } ';
        $stylesheet .= '#cuidadores tbody td { font-size: 10px; padding: 5px; } ';
        $stylesheet .= '#table thead th { font-size: 12px; padding: 5px; background-color: #f5f5f5;} ';
        $stylesheet .= '#table tbody td { font-size: 10px; padding: 5px; vertical-align: top; } ';

        $this->m_pdf->pdf->AddPage('L');
        $this->m_pdf->pdf->writeHTML($stylesheet, 1);
        $this->m_pdf->pdf->writeHTML($this->load->view('ei/escolas_pdf', $data, true));

        $this->m_pdf->pdf->Output('Relação de Escolas.pdf', 'D');
    }

    //--------------------------------------------------------------------

    public function resultados($pdf = false)
    {
        if ($pdf !== true) {
            $pdf = false;
        }

        $get = $this->input->get();

        $rows = $this->db
            ->query("SET lc_time_names = 'pt_BR'")
            ->select("a.*, DATE_FORMAT(b.data, '%m') AS mes", false)
            ->join('ei_alocacao b', 'b.id = a.id_alocacao')
            ->join('ei_diretorias c', 'c.nome = b.diretoria AND c.depto = b.depto')
            ->join('ei_escolas d', 'd.id_diretoria = c.id')
            ->join('ei_supervisores e', 'e.id_escola = d.id')
            ->join('usuarios f', 'f.id = e.id_supervisor AND f.nome = a.supervisor')
            ->where('c.id', $get['diretoria'])
            ->where('c.depto', $get['depto'])
            ->where('e.id_supervisor', $get['supervisor'])
            ->where("DATE_FORMAT(b.data, '%Y') =", $get['ano'])
            ->order_by('b.data', 'asc')
            ->get('ei_observacoes a')
            ->result();

        $data = [];
        $data['total_meses'] = 14;

        $diretoria = $this->db
            ->select('nome, depto')
            ->get_where('ei_diretorias', ['id' => $get['diretoria']])
            ->row();

        $data['departamento'] = $diretoria->depto;
        $data['diretoria'] = $diretoria->nome;

        $supervisor = $this->db
            ->select('nome')
            ->get_where('usuarios', ['id' => $get['supervisor']])
            ->row();

        $data['supervisor'] = $supervisor->nome;

        $data['meses'] = [
            '01' => 'Jan',
            '02' => 'Fev',
            '03' => 'Mar',
            '04' => 'Abr',
            '05' => 'Mai',
            '06' => 'Jun',
            '07' => 'Jul',
            '08' => 'Ago',
            '09' => 'Set',
            '10' => 'Out',
            '11' => 'Nov',
            '12' => 'Dez',
        ];

        $data['ano'] = $get['ano'];
        $data['is_pdf'] = $pdf;
        $data['query_string'] = http_build_query($get);
        $data['modo'] = 'normal';

        $mesesVazios = [
            '01' => null,
            '02' => null,
            '03' => null,
            '04' => null,
            '05' => null,
            '06' => null,
            '07' => null,
            '08' => null,
            '09' => null,
            '10' => null,
            '11' => null,
            '12' => null,
        ];

        $data['total_faltas'] = $mesesVazios;
        $data['total_faltas_justificadas'] = $mesesVazios;
        $data['turnover_substituicao'] = $mesesVazios;
        $data['turnover_aumento_quadro'] = $mesesVazios;
        $data['turnover_desligamento_empresa'] = $mesesVazios;
        $data['turnover_desligamento_solicitacao'] = $mesesVazios;
        $data['intercorrencias_diretoria'] = $mesesVazios;
        $data['intercorrencias_cuidador'] = $mesesVazios;
        $data['intercorrencias_alunos'] = $mesesVazios;
        $data['acidentes_trabalho'] = $mesesVazios;
        $data['total_escolas'] = $mesesVazios;
        $data['total_alunos'] = $mesesVazios;
        $data['dias_letivos'] = $mesesVazios;
        $data['total_cuidadores'] = $mesesVazios;
        $data['total_cuidadores_cobrados'] = $mesesVazios;
        $data['total_cuidadores_ativos'] = $mesesVazios;
        $data['total_cuidadores_afastados'] = $mesesVazios;
        $data['total_supervisores'] = $mesesVazios;
        $data['total_supervisores_cobrados'] = $mesesVazios;
        $data['total_supervisores_ativos'] = $mesesVazios;
        $data['total_supervisores_afastados'] = $mesesVazios;
        $data['faturamentos_projetados'] = $mesesVazios;
        $data['faturamentos_realizados'] = $mesesVazios;

        foreach ($rows as $row) {
            $mes = $row->mes;
            $data['total_faltas'][$mes] = $row->total_faltas;
            $data['total_faltas_justificadas'][$mes] = $row->total_faltas_justificadas;
            $data['turnover_substituicao'][$mes] = $row->turnover_substituicao;
            $data['turnover_aumento_quadro'][$mes] = $row->turnover_aumento_quadro;
            $data['turnover_desligamento_empresa'][$mes] = $row->turnover_desligamento_empresa;
            $data['turnover_desligamento_solicitacao'][$mes] = $row->turnover_desligamento_solicitacao;
            $data['intercorrencias_diretoria'][$mes] = $row->intercorrencias_diretoria;
            $data['intercorrencias_cuidador'][$mes] = $row->intercorrencias_cuidador;
            $data['intercorrencias_alunos'][$mes] = $row->intercorrencias_alunos;
            $data['acidentes_trabalho'][$mes] = $row->acidentes_trabalho;
            $data['total_escolas'][$mes] = $row->total_escolas;
            $data['total_alunos'][$mes] = $row->total_alunos;
            $data['dias_letivos'][$mes] = $row->dias_letivos;
            $data['total_cuidadores'][$mes] = $row->total_cuidadores;
            $data['total_cuidadores_cobrados'][$mes] = $row->total_cuidadores_cobrados;
            $data['total_cuidadores_ativos'][$mes] = $row->total_cuidadores_ativos;
            $data['total_cuidadores_afastados'][$mes] = $row->total_cuidadores_afastados;
            $data['total_supervisores'][$mes] = $row->total_supervisores;
            $data['total_supervisores_cobrados'][$mes] = $row->total_supervisores_cobrados;
            $data['total_supervisores_ativos'][$mes] = $row->total_supervisores_ativos;
            $data['total_supervisores_afastados'][$mes] = $row->total_supervisores_afastados;
            $data['faturamentos_projetados'][$mes] = $row->faturamento_projetado;
            $data['faturamentos_realizados'][$mes] = $row->faturamento_realizado;
        }

        if ($pdf) {
            return $this->load->view('ei/relatorio_resultados', $data, true);
        }

        $this->load->view('ei/relatorio_resultados', $data);
    }

    //--------------------------------------------------------------------

    public function resultados_diretorias($pdf = false)
    {
        if ($pdf !== true) {
            $pdf = false;
        }

        $get = $this->input->get();

        $this->db->query("SET lc_time_names = 'pt_BR'");

        $rows = $this->db
            ->select("DATE_FORMAT(b.data, '%m') AS mes", false)
            ->select('SUM(a.total_faltas) AS total_faltas', false)
            ->select('SUM(a.total_faltas_justificadas) AS total_faltas_justificadas', false)
            ->select('SUM(a.turnover_substituicao) AS turnover_substituicao', false)
            ->select('SUM(a.turnover_aumento_quadro) AS turnover_aumento_quadro', false)
            ->select('SUM(a.turnover_desligamento_empresa) AS turnover_desligamento_empresa', false)
            ->select('SUM(a.turnover_desligamento_solicitacao) AS turnover_desligamento_solicitacao', false)
            ->select('SUM(a.intercorrencias_diretoria) AS intercorrencias_diretoria', false)
            ->select('SUM(a.intercorrencias_cuidador) AS intercorrencias_cuidador', false)
            ->select('SUM(a.intercorrencias_alunos) AS intercorrencias_alunos', false)
            ->select('SUM(a.acidentes_trabalho) AS acidentes_trabalho', false)
            ->select('SUM(a.total_escolas) AS total_escolas', false)
            ->select('SUM(a.total_alunos) AS total_alunos', false)
            ->select('SUM(a.total_cuidadores) AS total_cuidadores', false)
            ->select('SUM(a.total_cuidadores_cobrados) AS total_cuidadores_cobrados', false)
            ->select('SUM(a.total_cuidadores_ativos) AS total_cuidadores_ativos', false)
            ->select('SUM(a.total_cuidadores_afastados) AS total_cuidadores_afastados', false)
            ->select('SUM(a.total_supervisores) AS total_supervisores', false)
            ->select('SUM(a.total_supervisores_cobrados) AS total_supervisores_cobrados', false)
            ->select('SUM(a.total_supervisores_ativos) AS total_supervisores_ativos', false)
            ->select('SUM(a.total_supervisores_afastados) AS total_supervisores_afastados', false)
            ->select('SUM(a.faturamento_projetado) AS faturamento_projetado', false)
            ->select('SUM(a.faturamento_realizado) AS faturamento_realizado', false)
            ->join('ei_alocacao b', 'b.id = a.id_alocacao')
            ->join('ei_diretorias c', 'c.nome = b.diretoria AND c.depto = b.depto AND c.municipio = b.municipio')
            ->where('c.id', $get['diretoria'])
            ->where('c.depto', $get['depto'])
            ->where("DATE_FORMAT(b.data, '%Y') =", $get['ano'])
            ->group_by('b.data')
            ->order_by('b.data', 'asc')
            ->get('ei_observacoes a')
            ->result();

        $data = [];
        $data['total_meses'] = 14;

        $diretoria = $this->db
            ->select('nome, depto')
            ->get_where('ei_diretorias', ['id' => $get['diretoria']])
            ->row();

        $data['departamento'] = $diretoria->depto;
        $data['diretoria'] = $diretoria->nome;

        $supervisores = $this->db
            ->select('supervisor AS nome', false)
            ->where("DATE_FORMAT(data, '%Y') =", $get['ano'])
            ->where('depto', $diretoria->depto)
            ->where('diretoria', $diretoria->nome)
            ->group_by('supervisor')
            ->order_by('supervisor', 'asc')
            ->get('ei_alocacao')
            ->result();

        foreach ($supervisores as $supervisor) {
            $data['supervisor'][] = $supervisor->nome;
        }

        $data['meses'] = [
            '01' => 'Jan',
            '02' => 'Fev',
            '03' => 'Mar',
            '04' => 'Abr',
            '05' => 'Mai',
            '06' => 'Jun',
            '07' => 'Jul',
            '08' => 'Ago',
            '09' => 'Set',
            '10' => 'Out',
            '11' => 'Nov',
            '12' => 'Dez',
        ];

        $data['ano'] = $get['ano'];
        $data['is_pdf'] = $pdf;
        $data['query_string'] = http_build_query($get);
        $data['modo'] = 'diretorias';

        $mesesVazios = [
            '01' => null,
            '02' => null,
            '03' => null,
            '04' => null,
            '05' => null,
            '06' => null,
            '07' => null,
            '08' => null,
            '09' => null,
            '10' => null,
            '11' => null,
            '12' => null,
        ];

        $data['total_faltas'] = $mesesVazios;
        $data['total_faltas_justificadas'] = $mesesVazios;
        $data['turnover_substituicao'] = $mesesVazios;
        $data['turnover_aumento_quadro'] = $mesesVazios;
        $data['turnover_desligamento_empresa'] = $mesesVazios;
        $data['turnover_desligamento_solicitacao'] = $mesesVazios;
        $data['intercorrencias_diretoria'] = $mesesVazios;
        $data['intercorrencias_cuidador'] = $mesesVazios;
        $data['intercorrencias_alunos'] = $mesesVazios;
        $data['acidentes_trabalho'] = $mesesVazios;
        $data['total_escolas'] = $mesesVazios;
        $data['total_alunos'] = $mesesVazios;
        $data['dias_letivos'] = null;
        $data['total_cuidadores'] = $mesesVazios;
        $data['total_cuidadores_cobrados'] = $mesesVazios;
        $data['total_cuidadores_ativos'] = $mesesVazios;
        $data['total_cuidadores_afastados'] = $mesesVazios;
        $data['total_supervisores'] = $mesesVazios;
        $data['total_supervisores_cobrados'] = $mesesVazios;
        $data['total_supervisores_ativos'] = $mesesVazios;
        $data['total_supervisores_afastados'] = $mesesVazios;
        $data['faturamentos_projetados'] = $mesesVazios;
        $data['faturamentos_realizados'] = $mesesVazios;

        foreach ($rows as $row) {
            $mes = $row->mes;
            $data['total_faltas'][$mes] = $row->total_faltas;
            $data['total_faltas_justificadas'][$mes] = $row->total_faltas_justificadas;
            $data['turnover_substituicao'][$mes] = $row->turnover_substituicao;
            $data['turnover_aumento_quadro'][$mes] = $row->turnover_aumento_quadro;
            $data['turnover_desligamento_empresa'][$mes] = $row->turnover_desligamento_empresa;
            $data['turnover_desligamento_solicitacao'][$mes] = $row->turnover_desligamento_solicitacao;
            $data['intercorrencias_diretoria'][$mes] = $row->intercorrencias_diretoria;
            $data['intercorrencias_cuidador'][$mes] = $row->intercorrencias_cuidador;
            $data['intercorrencias_alunos'][$mes] = $row->intercorrencias_alunos;
            $data['acidentes_trabalho'][$mes] = $row->acidentes_trabalho;
            $data['total_escolas'][$mes] = $row->total_escolas;
            $data['total_alunos'][$mes] = $row->total_alunos;
            //$data['dias_letivos'][$mes] = $row->dias_letivos;
            $data['total_cuidadores'][$mes] = $row->total_cuidadores;
            $data['total_cuidadores_cobrados'][$mes] = $row->total_cuidadores_cobrados;
            $data['total_cuidadores_ativos'][$mes] = $row->total_cuidadores_ativos;
            $data['total_cuidadores_afastados'][$mes] = $row->total_cuidadores_afastados;
            $data['total_supervisores'][$mes] = $row->total_supervisores;
            $data['total_supervisores_cobrados'][$mes] = $row->total_supervisores_cobrados;
            $data['total_supervisores_ativos'][$mes] = $row->total_supervisores_ativos;
            $data['total_supervisores_afastados'][$mes] = $row->total_supervisores_afastados;
            $data['faturamentos_projetados'][$mes] = $row->faturamento_projetado;
            $data['faturamentos_realizados'][$mes] = $row->faturamento_realizado;
        }

        if ($pdf) {
            return $this->load->view('ei/relatorio_resultados', $data, true);
        }

        $this->load->view('ei/relatorio_resultados', $data);
    }

    //--------------------------------------------------------------------

    public function pdf_resultados()
    {
        $this->load->library('m_pdf');

        $stylesheet = '#recursos_alocados thead tr th { border-top: 4px solid #ddd; padding-top: 8px; } ';
        $stylesheet .= '#recursos_alocados { border: 1px solid #444; margin-bottom: 0px; } ';
        $stylesheet .= '#recursos_alocados thead th { font-size: 12px; padding: 4px; background-color: #f5f5f5; border: 1px solid #444; } ';
        $stylesheet .= '#recursos_alocados tbody td { font-size: 10px; padding: 4px; vertical-align: top; border: 1px solid #444; } ';

        $stylesheet .= '#faltas { border: 1px solid #444; margin-bottom: 0px; } ';
        $stylesheet .= '#faltas thead th { font-size: 12px; padding: 4px; background-color: #f5f5f5; border: 1px solid #444; } ';
        $stylesheet .= '#faltas tbody td { font-size: 10px; padding: 4px; vertical-align: top; border: 1px solid #444; } ';

        $stylesheet .= '#movimentacoes { border: 1px solid #444; margin-bottom: 0px; } ';
        $stylesheet .= '#movimentacoes thead th { font-size: 12px; padding: 4px; background-color: #f5f5f5; border: 1px solid #444; } ';
        $stylesheet .= '#movimentacoes tbody td { font-size: 10px; padding: 4px; vertical-align: top; border: 1px solid #444; } ';

        $stylesheet .= '#faturamento { border: 1px solid #444; margin-bottom: 0px; } ';
        $stylesheet .= '#faturamento thead th { font-size: 12px; padding: 4px; background-color: #f5f5f5; border: 1px solid #444; } ';
        $stylesheet .= '#faturamento tbody td { font-size: 10px; padding: 4px; vertical-align: top; border: 1px solid #444; } ';

        $this->m_pdf->pdf->setTopMargin(38);
        $this->m_pdf->pdf->AddPage('L');
        $this->m_pdf->pdf->writeHTML($stylesheet, 1);
        $this->m_pdf->pdf->writeHTML($this->resultados(true));

        $data = $this->input->get();

        $this->load->library('Calendar');
        $this->calendar->month_type = 'short';
        $nome = 'Cuidadores - Acompanhamento individual ' . $this->calendar->get_month_name($data['mes']) . '_' . $data['ano'];

        $this->m_pdf->pdf->Output($nome . '.pdf', 'D');
    }

    //--------------------------------------------------------------------

    public function pdf_resultados_diretorias()
    {
        $this->load->library('m_pdf');

        $stylesheet = '#recursos_alocados thead tr th { border-top: 4px solid #ddd; padding-top: 8px; } ';
        $stylesheet .= '#recursos_alocados { border: 1px solid #444; margin-bottom: 0px; } ';
        $stylesheet .= '#recursos_alocados thead th { font-size: 12px; padding: 4px; background-color: #f5f5f5; border: 1px solid #444; } ';
        $stylesheet .= '#recursos_alocados tbody td { font-size: 10px; padding: 4px; vertical-align: top; border: 1px solid #444; } ';

        $stylesheet .= '#faltas { border: 1px solid #444; margin-bottom: 0px; } ';
        $stylesheet .= '#faltas thead th { font-size: 12px; padding: 4px; background-color: #f5f5f5; border: 1px solid #444; } ';
        $stylesheet .= '#faltas tbody td { font-size: 10px; padding: 4px; vertical-align: top; border: 1px solid #444; } ';

        $stylesheet .= '#movimentacoes { border: 1px solid #444; margin-bottom: 0px; } ';
        $stylesheet .= '#movimentacoes thead th { font-size: 12px; padding: 4px; background-color: #f5f5f5; border: 1px solid #444; } ';
        $stylesheet .= '#movimentacoes tbody td { font-size: 10px; padding: 4px; vertical-align: top; border: 1px solid #444; } ';

        $stylesheet .= '#faturamento { border: 1px solid #444; margin-bottom: 0px; } ';
        $stylesheet .= '#faturamento thead th { font-size: 12px; padding: 4px; background-color: #f5f5f5; border: 1px solid #444; } ';
        $stylesheet .= '#faturamento tbody td { font-size: 10px; padding: 4px; vertical-align: top; border: 1px solid #444; } ';

        $this->m_pdf->pdf->setTopMargin(38);
        $this->m_pdf->pdf->AddPage('L');
        $this->m_pdf->pdf->writeHTML($stylesheet, 1);
        $this->m_pdf->pdf->writeHTML($this->resultados_diretorias(true));

        $data = $this->input->get();

        $this->load->library('Calendar');
        $this->calendar->month_type = 'short';
        $nome = 'Cuidadores - Acompanhamento de diretoria ' . $this->calendar->get_month_name($data['mes']) . '_' . $data['ano'];

        $this->m_pdf->pdf->Output($nome . '.pdf', 'D');
    }

    //--------------------------------------------------------------------

    public function resultados_consolidados($pdf = false)
    {
        if ($pdf !== true) {
            $pdf = false;
        }

        $get = $this->input->get();

        $this->db->query("SET lc_time_names = 'pt_BR'");

        $rows = $this->db
            ->select("DATE_FORMAT(b.data, '%m') AS mes", false)
            ->select('SUM(a.total_faltas) AS total_faltas', false)
            ->select('SUM(a.total_faltas_justificadas) AS total_faltas_justificadas', false)
            ->select('SUM(a.turnover_substituicao) AS turnover_substituicao', false)
            ->select('SUM(a.turnover_aumento_quadro) AS turnover_aumento_quadro', false)
            ->select('SUM(a.turnover_desligamento_empresa) AS turnover_desligamento_empresa', false)
            ->select('SUM(a.turnover_desligamento_solicitacao) AS turnover_desligamento_solicitacao', false)
            ->select('SUM(a.intercorrencias_diretoria) AS intercorrencias_diretoria', false)
            ->select('SUM(a.intercorrencias_cuidador) AS intercorrencias_cuidador', false)
            ->select('SUM(a.intercorrencias_alunos) AS intercorrencias_alunos', false)
            ->select('SUM(a.acidentes_trabalho) AS acidentes_trabalho', false)
            ->select('SUM(a.total_escolas) AS total_escolas', false)
            ->select('SUM(a.total_alunos) AS total_alunos', false)
            ->select('SUM(a.total_cuidadores) AS total_cuidadores', false)
            ->select('SUM(a.total_cuidadores_cobrados) AS total_cuidadores_cobrados', false)
            ->select('SUM(a.total_cuidadores_ativos) AS total_cuidadores_ativos', false)
            ->select('SUM(a.total_cuidadores_afastados) AS total_cuidadores_afastados', false)
            ->select('SUM(a.total_supervisores) AS total_supervisores', false)
            ->select('SUM(a.total_supervisores_cobrados) AS total_supervisores_cobrados', false)
            ->select('SUM(a.total_supervisores_ativos) AS total_supervisores_ativos', false)
            ->select('SUM(a.total_supervisores_afastados) AS total_supervisores_afastados', false)
            ->select('SUM(a.faturamento_projetado) AS faturamento_projetado', false)
            ->select('SUM(a.faturamento_realizado) AS faturamento_realizado', false)
            ->join('ei_alocacao b', 'b.id = a.id_alocacao')
            ->where('b.id_empresa', $this->session->userdata('empresa'))
            ->where("DATE_FORMAT(b.data, '%Y') =", $get['ano'])
            ->group_by('b.data')
            ->order_by('b.data', 'asc')
            ->get('ei_observacoes a')
            ->result();

        $data = [];
        $data['total_meses'] = 14;
        $data['meses'] = [
            '01' => 'Jan',
            '02' => 'Fev',
            '03' => 'Mar',
            '04' => 'Abr',
            '05' => 'Mai',
            '06' => 'Jun',
            '07' => 'Jul',
            '08' => 'Ago',
            '09' => 'Set',
            '10' => 'Out',
            '11' => 'Nov',
            '12' => 'Dez',
        ];

        $data['ano'] = $get['ano'];
        $data['is_pdf'] = $pdf;
        $data['query_string'] = http_build_query($get);
        $data['modo'] = 'consolidado';

        $mesesVazios = [
            '01' => null,
            '02' => null,
            '03' => null,
            '04' => null,
            '05' => null,
            '06' => null,
            '07' => null,
            '08' => null,
            '09' => null,
            '10' => null,
            '11' => null,
            '12' => null,
        ];

        $data['total_faltas'] = $mesesVazios;
        $data['total_faltas_justificadas'] = $mesesVazios;
        $data['turnover_substituicao'] = $mesesVazios;
        $data['turnover_aumento_quadro'] = $mesesVazios;
        $data['turnover_desligamento_empresa'] = $mesesVazios;
        $data['turnover_desligamento_solicitacao'] = $mesesVazios;
        $data['intercorrencias_diretoria'] = $mesesVazios;
        $data['intercorrencias_cuidador'] = $mesesVazios;
        $data['intercorrencias_alunos'] = $mesesVazios;
        $data['acidentes_trabalho'] = $mesesVazios;
        $data['total_escolas'] = $mesesVazios;
        $data['total_alunos'] = $mesesVazios;
        $data['dias_letivos'] = null;
        $data['total_cuidadores'] = $mesesVazios;
        $data['total_cuidadores_cobrados'] = $mesesVazios;
        $data['total_cuidadores_ativos'] = $mesesVazios;
        $data['total_cuidadores_afastados'] = $mesesVazios;
        $data['total_supervisores'] = $mesesVazios;
        $data['total_supervisores_cobrados'] = $mesesVazios;
        $data['total_supervisores_ativos'] = $mesesVazios;
        $data['total_supervisores_afastados'] = $mesesVazios;
        $data['faturamentos_projetados'] = $mesesVazios;
        $data['faturamentos_realizados'] = $mesesVazios;

        foreach ($rows as $row) {
            $mes = $row->mes;
            $data['total_faltas'][$mes] = $row->total_faltas;
            $data['total_faltas_justificadas'][$mes] = $row->total_faltas_justificadas;
            $data['turnover_substituicao'][$mes] = $row->turnover_substituicao;
            $data['turnover_aumento_quadro'][$mes] = $row->turnover_aumento_quadro;
            $data['turnover_desligamento_empresa'][$mes] = $row->turnover_desligamento_empresa;
            $data['turnover_desligamento_solicitacao'][$mes] = $row->turnover_desligamento_solicitacao;
            $data['intercorrencias_diretoria'][$mes] = $row->intercorrencias_diretoria;
            $data['intercorrencias_cuidador'][$mes] = $row->intercorrencias_cuidador;
            $data['intercorrencias_alunos'][$mes] = $row->intercorrencias_alunos;
            $data['acidentes_trabalho'][$mes] = $row->acidentes_trabalho;
            $data['total_escolas'][$mes] = $row->total_escolas;
            $data['total_alunos'][$mes] = $row->total_alunos;
            $data['total_cuidadores'][$mes] = $row->total_cuidadores;
            $data['total_cuidadores_cobrados'][$mes] = $row->total_cuidadores_cobrados;
            $data['total_cuidadores_ativos'][$mes] = $row->total_cuidadores_ativos;
            $data['total_cuidadores_afastados'][$mes] = $row->total_cuidadores_afastados;
            $data['total_supervisores'][$mes] = $row->total_supervisores;
            $data['total_supervisores_cobrados'][$mes] = $row->total_supervisores_cobrados;
            $data['total_supervisores_ativos'][$mes] = $row->total_supervisores_ativos;
            $data['total_supervisores_afastados'][$mes] = $row->total_supervisores_afastados;
            $data['faturamentos_projetados'][$mes] = $row->faturamento_projetado;
            $data['faturamentos_realizados'][$mes] = $row->faturamento_realizado;
        }

        if ($pdf) {
            return $this->load->view('ei/relatorio_resultados', $data, true);
        }

        $this->load->view('ei/relatorio_resultados', $data);
    }

    //--------------------------------------------------------------------

    public function pdf_resultados_consolidados()
    {
        $this->load->library('m_pdf');

        $stylesheet = '#recursos_alocados thead tr th { border-top: 4px solid #ddd; padding-top: 8px; } ';
        $stylesheet .= '#recursos_alocados { border: 1px solid #444; margin-bottom: 0px; } ';
        $stylesheet .= '#recursos_alocados thead th { font-size: 12px; padding: 4px; background-color: #f5f5f5; border: 1px solid #444; } ';
        $stylesheet .= '#recursos_alocados tbody td { font-size: 10px; padding: 4px; vertical-align: top; border: 1px solid #444; } ';

        $stylesheet .= '#faltas { border: 1px solid #444; margin-bottom: 0px; } ';
        $stylesheet .= '#faltas thead th { font-size: 12px; padding: 4px; background-color: #f5f5f5; border: 1px solid #444; } ';
        $stylesheet .= '#faltas tbody td { font-size: 10px; padding: 4px; vertical-align: top; border: 1px solid #444; } ';

        $stylesheet .= '#movimentacoes { border: 1px solid #444; margin-bottom: 0px; } ';
        $stylesheet .= '#movimentacoes thead th { font-size: 12px; padding: 4px; background-color: #f5f5f5; border: 1px solid #444; } ';
        $stylesheet .= '#movimentacoes tbody td { font-size: 10px; padding: 4px; vertical-align: top; border: 1px solid #444; } ';

        $stylesheet .= '#faturamento { border: 1px solid #444; margin-bottom: 0px; } ';
        $stylesheet .= '#faturamento thead th { font-size: 12px; padding: 4px; background-color: #f5f5f5; border: 1px solid #444; } ';
        $stylesheet .= '#faturamento tbody td { font-size: 10px; padding: 4px; vertical-align: top; border: 1px solid #444; } ';

        $this->m_pdf->pdf->setTopMargin(38);
        $this->m_pdf->pdf->AddPage('L');
        $this->m_pdf->pdf->writeHTML($stylesheet, 1);
        $this->m_pdf->pdf->writeHTML($this->resultados_consolidados(true));

        $data = $this->input->get();

        $this->load->library('Calendar');
        $this->calendar->month_type = 'short';
        $nome = 'Cuidadores - Acompanhamento mensal consolidado ' . $this->calendar->get_month_name($data['mes']) . '_' . $data['ano'];

        $this->m_pdf->pdf->Output($nome . '.pdf', 'D');
    }

    //--------------------------------------------------------------------

    public function pdf_mapa_carregamento_old()
    {
        $get = $this->input->get();
        $idMes = intval($get['mes']) - ($get['semestre'] > 1 ? 6 : 0);

        $empresa = $this->session->userdata('empresa');
        $this->load->library('m_pdf');

        $stylesheet = 'table { font-size: 12px; } ';
        $stylesheet .= 'table tr th, table tr td { border: 1px solid #fff; } ';
        $this->m_pdf->pdf->AddPage('L');
        $this->m_pdf->pdf->writeHTML($stylesheet, 1);

        $usuario = $this->db
            ->select('foto, foto_descricao')
            ->get_where('usuarios', ['id' => $empresa])
            ->row();

        $alocacao = $this->db
            ->select('a.id, COUNT(DISTINCT(b.escola)) AS total_escolas', false)
            ->select('COUNT(DISTINCT(f.aluno)) AS total_alunos', false)
            ->select('COUNT(DISTINCT(c.cuidador)) AS total_profissionais', false)
            ->join('ei_alocacao_escolas b', 'b.id_alocacao = a.id', 'left')
            ->join('ei_alocados c', 'c.id_alocacao_escola = b.id')
            ->join('ei_alocados_horarios d', 'd.id_alocado = c.id', 'left')
            ->join('ei_matriculados_turmas e', 'e.id_alocado_horario = d.id', 'left')
            ->join('ei_matriculados f', 'f.id = e.id_matriculado AND f.id_alocacao_escola = b.id', 'left')
            ->where('a.id_empresa', $empresa)
            ->where('a.depto', $get['depto'])
            ->where('a.id_diretoria', $get['diretoria'])
            ->where('a.id_supervisor', $get['supervisor'])
            ->where('a.ano', $get['ano'])
            ->where('a.semestre', $get['semestre'])
            ->get('ei_alocacao a')
            ->row();

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
                    <h1 style="font-weight: bold;">MAPA DE CARREGAMENTO DE O. S. - ' . $get['ano'] . '/' . ($get['mes'] > 6 ? 2 : 1) . '</h1>
                </td>
            </tr>
                        </tbody>
        </table>
        <table style="width: 100%;">
            <tr>
                <td style="text-align: center;">Número de escolas: ' . $alocacao->total_escolas . '</td>
                <td style="text-align: center;">Número de alunos:  ' . $alocacao->total_alunos . '</td>
                <td style="text-align: center;">Número de profissionais:  ' . $alocacao->total_profissionais . '</td>
            </tr>
        </table>
        <br><br>';

        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';

        $this->db->query("SET lc_time_names = 'pt_BR'");

        $sql = "SELECT s.ordem_servico,
                       s.funcao,
                       s.cuidador, 
                       s.escola,
                       s.aluno,
                       s.curso,
                       GROUP_CONCAT(CONCAT(s.min_semana, s.max_semana, ', ', s.horario_inicio, ' às ', s.horario_termino) ORDER BY s.min_semana, s.max_semana, s.horario_inicio, s.horario_termino SEPARATOR ';<br>') AS horario,
                       CONCAT(s.data_inicio, ' a ', s.data_termino) AS periodo,
                       s.modulo,
                       s.codigo,
                       s.municipio
                FROM (SELECT REPLACE(a2.ordem_servico, '/{$get['ano']}', '') AS ordem_servico,
                             CONCAT_WS(' - ', a2.codigo, a2.escola) AS escola,
                             GROUP_CONCAT(DISTINCT(c.aluno) ORDER BY c.aluno SEPARATOR '<br>') AS aluno,
                             c.curso,
                             CASE MIN(g.dia_semana) 
                                  WHEN '0' THEN 'Dom'
                                  WHEN '1' THEN '2&ordf;'
                                  WHEN '2' THEN '3&ordf;'
                                  WHEN '3' THEN '4&ordf;'
                                  WHEN '4' THEN '5&ordf;'
                                  WHEN '5' THEN '6&ordf;'
                                  WHEN '6' THEN 'Sáb'
                                  END AS min_semana,
                             CASE MAX(g.dia_semana) 
                                  WHEN MIN(dia_semana) THEN ''
                                  WHEN '0' THEN ' a Dom'
                                  WHEN '1' THEN ' a 2&ordf;'
                                  WHEN '2' THEN ' a 3&ordf;'
                                  WHEN '3' THEN ' a 4&ordf;'
                                  WHEN '4' THEN ' a 5&ordf;'
                                  WHEN '5' THEN ' a 6&ordf;'
                                  WHEN '6' THEN ' a Sáb'
                                  END AS max_semana,
                             TIME_FORMAT(MIN(g.horario_inicio_mes{$idMes}), '%H:%i') AS horario_inicio,
                             TIME_FORMAT(MAX(g.horario_termino_mes{$idMes}), '%H:%i') AS horario_termino,
                             a.cuidador,
                             g.funcao{$mesCargoFuncao} AS funcao,
                             DATE_FORMAT(c.data_inicio, '%d/%b') AS data_inicio,
                             DATE_FORMAT(c.data_termino, '%d/%b') AS data_termino,
                             c.modulo,
                             a2.codigo,
                             a2.municipio
                      FROM ei_alocacao b
                      INNER JOIN ei_alocacao_escolas a2 ON b.id = a2.id_alocacao
                      INNER JOIN ei_alocados a ON a2.id = a.id_alocacao_escola
                      LEFT JOIN ei_alocados_horarios g ON g.id_alocado = a.id
                      LEFT JOIN ei_matriculados_turmas c2 ON c2.id_alocado_horario = g.id
                      LEFT JOIN ei_matriculados c ON c.id = c2.id_matriculado AND c.id_alocacao_escola = a2.id
                      WHERE b.id = '{$alocacao->id}'
                      GROUP BY a2.escola, a.cuidador, g.cargo, g.funcao{$mesCargoFuncao}, c.curso, g.horario_inicio_mes{$idMes}, g.horario_termino_mes{$idMes}
                      ORDER BY IF(CHAR_LENGTH(a2.codigo) > 0, a2.codigo, CAST(a2.escola AS DECIMAL)) ASC,
                      a2.municipio ASC, a2.escola ASC, COALESCE(g.funcao{$mesCargoFuncao}, 'zzz') ASC, a.cuidador, a2.ordem_servico ASC, a.cuidador ASC, c.aluno ASC) s
                GROUP BY s.ordem_servico, s.escola, s.cuidador, s.aluno
                ORDER BY IF(CHAR_LENGTH(s.codigo) > 0, s.codigo, CAST(s.escola AS DECIMAL)) ASC, s.municipio ASC, s.escola ASC, COALESCE(s.funcao, 'zzz') ASC, s.ordem_servico ASC, s.aluno ASC";

        $data = $this->db->query($sql)->result_array();

        $table = [['O.S.', 'Função', 'Profissional', 'Escola', 'Aluno(s)', 'Curso', 'Horário', 'Período', 'Módulo']];
        foreach ($data as $row) {
            unset($row['codigo'], $row['municipio']);
            $table[] = $row;
        }
        $this->load->library('table');

        $html = $cabecalho . $this->table->generate($table);

        $this->m_pdf->pdf->writeHTML($html);

        $this->m_pdf->pdf->Output("EI - Mapa Carregamento de OS.pdf", 'D');
    }

    //--------------------------------------------------------------------

    public function pdf_mapa_carregamento_os()
    {
        $get = $this->input->get();
        $idMes = intval($get['mes']) - ($get['semestre'] > 1 ? 6 : 0);
        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';

        $empresa = $this->session->userdata('empresa');

        $usuario = $this->db
            ->select('foto, foto_descricao')
            ->get_where('usuarios', ['id' => $empresa])
            ->row();

        $nomeOS = '';

        $alocacao = $this->db
            ->select('id')
            ->where('a.id_empresa', $empresa)
            ->where('a.depto', $get['depto'])
            ->where('a.id_diretoria', $get['diretoria'])
            ->where('a.id_supervisor', $get['supervisor'])
            ->where('a.ano', $get['ano'])
            ->where('a.semestre', $get['semestre'])
            ->get('ei_alocacao a')
            ->row();

        $rowsOS = $this->db
            ->select('b.id_escola, f.id_aluno, c.id_cuidador')
            ->select('b.codigo, b.escola, f.aluno, c.cuidador')
            ->join('ei_alocacao_escolas b', 'b.id_alocacao = a.id', 'left')
            ->join('ei_alocados c', 'c.id_alocacao_escola = b.id')
            ->join('ei_alocados_horarios d', 'd.id_alocado = c.id', 'left')
            ->join('ei_matriculados_turmas e', 'e.id_alocado_horario = d.id', 'left')
            ->join('ei_matriculados f', 'f.id = e.id_matriculado AND f.id_alocacao_escola = b.id', 'left')
            ->where('a.id', $alocacao->id)
            ->get('ei_alocacao a')
            ->result();

        $totalEscolas = count(array_unique(array_column($rowsOS, 'id_escola')));
        $totalProfissionais = count(array_unique(array_column($rowsOS, 'id_cuidador')));
        $totalAlunos = count(array_unique(array_column($rowsOS, 'id_aluno')));

        $listaEscolas = [];
        $listaProfissionais = [];
        $listaAlunos = [];
        foreach ($rowsOS as $rowOS) {
            $listaEscolas[$rowOS->codigo ?? $rowOS->escola] = implode(' - ', [$rowOS->codigo, $rowOS->escola]);
            $listaProfissionais[$rowOS->id_cuidador] = $rowOS->cuidador;
            $listaAlunos[$rowOS->id_aluno] = $rowOS->aluno;
        }

        ksort($listaEscolas);
        asort($listaProfissionais);
        asort($listaAlunos);

        $listaEscolas = array_values(array_filter($listaEscolas));
        $listaProfissionais = array_values(array_filter($listaProfissionais));
        $listaAlunos = array_values(array_filter($listaAlunos));

        $lista = [];
        $tamanhoLista = max(count($listaEscolas), count($listaProfissionais), count($listaAlunos));
        for ($i = 0; $i <= $tamanhoLista; $i++) {
            $lista[$i]['escola'] = $listaEscolas[$i] ?? null;
            $lista[$i]['profissional'] = $listaProfissionais[$i] ?? null;
            $lista[$i]['aluno'] = $listaAlunos[$i] ?? null;
        }

        $data2 = [
            'empresa' => $usuario,
            'anoSemestre' => $get['ano'] . '/' . $get['semestre'],
            'totalEscolas' => $totalEscolas,
            'totalProfissionais' => $totalProfissionais,
            'totalAlunos' => $totalAlunos,
            'lista' => $lista,
        ];

        $subquery = "SELECT REPLACE(a2.ordem_servico, '/{$get['ano']}', '') AS ordem_servico,
                             CONCAT_WS(' - ', a2.codigo, a2.escola) AS escola,
                             GROUP_CONCAT(DISTINCT(c.aluno) ORDER BY c.aluno SEPARATOR '<br>') AS aluno,
                             c.curso,
                             CASE MIN(g.dia_semana) 
                                  WHEN '0' THEN 'Dom'
                                  WHEN '1' THEN '2&ordf;'
                                  WHEN '2' THEN '3&ordf;'
                                  WHEN '3' THEN '4&ordf;'
                                  WHEN '4' THEN '5&ordf;'
                                  WHEN '5' THEN '6&ordf;'
                                  WHEN '6' THEN 'Sáb'
                                  END AS min_semana,
                             CASE MAX(g.dia_semana) 
                                  WHEN MIN(dia_semana) THEN ''
                                  WHEN '0' THEN ' a Dom'
                                  WHEN '1' THEN ' a 2&ordf;'
                                  WHEN '2' THEN ' a 3&ordf;'
                                  WHEN '3' THEN ' a 4&ordf;'
                                  WHEN '4' THEN ' a 5&ordf;'
                                  WHEN '5' THEN ' a 6&ordf;'
                                  WHEN '6' THEN ' a Sáb'
                                  END AS max_semana,
                             TIME_FORMAT(MIN(g.horario_inicio_mes{$idMes}), '%H:%i') AS horario_inicio,
                             TIME_FORMAT(MAX(g.horario_termino_mes{$idMes}), '%H:%i') AS horario_termino,
                             a.id_cuidador,
                             a.cuidador,
                             g.funcao{$mesCargoFuncao} AS funcao,
                             DATE_FORMAT(c.data_inicio, '%d/%b') AS data_inicio,
                             DATE_FORMAT(c.data_termino, '%d/%b') AS data_termino,
                             c.modulo,
                             a2.codigo,
                             a2.municipio
                      FROM ei_alocacao b
                      INNER JOIN ei_alocacao_escolas a2 ON b.id = a2.id_alocacao
                      INNER JOIN ei_alocados a ON a2.id = a.id_alocacao_escola
                      LEFT JOIN ei_alocados_horarios g ON g.id_alocado = a.id
                      LEFT JOIN ei_matriculados_turmas c2 ON c2.id_alocado_horario = g.id
                      LEFT JOIN ei_matriculados c ON c.id = c2.id_matriculado AND c.id_alocacao_escola = a2.id
                      WHERE b.id = '{$alocacao->id}'
                      GROUP BY a2.escola, a.cuidador, g.cargo, g.funcao{$mesCargoFuncao}, c.curso, g.horario_inicio_mes{$idMes}, g.horario_termino_mes{$idMes}
                      ORDER BY IF(CHAR_LENGTH(a2.codigo) > 0, a2.codigo, CAST(a2.escola AS DECIMAL)) ASC,
                               a2.municipio ASC, a2.escola ASC, COALESCE(g.funcao{$mesCargoFuncao}, 'zzz') ASC, a.cuidador, a2.ordem_servico ASC, a.cuidador ASC, c.aluno ASC";

        $this->db->query("SET lc_time_names = 'pt_BR'");

        $sql = "SELECT s.ordem_servico,
                       s.funcao,
                       s.cuidador, 
                       s.escola,
                       s.aluno,
                       s.curso,
                       GROUP_CONCAT(CONCAT(s.min_semana, s.max_semana, ', ', s.horario_inicio, ' às ', s.horario_termino) ORDER BY s.min_semana, s.max_semana, s.horario_inicio, s.horario_termino SEPARATOR ';<br>') AS horario,
                       CONCAT(s.data_inicio, ' a ', s.data_termino) AS periodo,
                       s.modulo,
                       s.codigo,
                       s.municipio
                FROM ({$subquery}) s
                GROUP BY s.ordem_servico, s.escola, s.cuidador, s.aluno
                ORDER BY IF(CHAR_LENGTH(s.codigo) > 0, s.codigo, CAST(s.escola AS DECIMAL)) ASC, s.municipio ASC, s.escola ASC, COALESCE(s.funcao, 'zzz') ASC, s.ordem_servico ASC, s.aluno ASC";

        $data = $this->db->query($sql)->result();

        $sqlTotal = "SELECT s.funcao, 
                            COUNT(DISTINCT(s.id_cuidador)) AS qtde_cuidadores
                    FROM ({$subquery}) s
                    GROUP BY s.funcao";
        $total = $this->db->query($sqlTotal)->result();

        $totalGroup = [];
        $qtdeProfissionais = [];
        foreach ($total as $row) {
            $totalGroup[$row->funcao] = $row;
            $qtdeProfissionais[$row->funcao] = $row->qtde_cuidadores;
        }

        $data2['rows'] = $data;
        $proximaFuncao = array_column($data, 'funcao');
        $primeiraFuncao = array_shift($proximaFuncao);
        array_push($proximaFuncao, $primeiraFuncao);
        $data2['proximaFuncao'] = $proximaFuncao;
        $data2['total'] = $totalGroup;

        ksort($qtdeProfissionais);
        $data2['qtdeProfissionais'] = $qtdeProfissionais;

        $stylesheet = '#table thead tr th { border-top: 4px solid #ddd; padding-top: 8px; } ';
        $stylesheet .= '#ordens_servico { border: 1px solid #aaa; margin-bottom: 0px; } ';
        $stylesheet .= '#ordens_servico thead th { font-size: 12px; padding: 4px; background-color: #DFF0D8; border: 1px solid #aaa; } ';
        $stylesheet .= '#ordens_servico thead th { font-size: 12px; padding: 4px; background-color: #f5f5f5; border: 1px solid #aaa; } ';
        $stylesheet .= '#ordens_servico tbody td { font-size: 10px; padding: 4px; vertical-align: top; border: 1px solid #aaa; } ';

        $view = $this->load->view('ei/pdf_mapa_carregamento_os', $data2, true);

        $this->load->library('m_pdf');
        $this->m_pdf->pdf->setTopMargin(48);
        $this->m_pdf->pdf->AddPage('L');
        $this->m_pdf->pdf->writeHTML($stylesheet, 1);
        $this->m_pdf->pdf->writeHTML($view);

        $this->m_pdf->pdf->Output("EI - Mapa de Carregamento de OS.pdf", 'D');
    }

    //--------------------------------------------------------------------

    public function pdf_mapa_escolas_x_alunos()
    {
        $get = $this->input->get('busca');

        $empresa = $this->session->userdata('empresa');

        $usuario = $this->db
            ->select('foto, foto_descricao')
            ->get_where('usuarios', ['id' => $empresa])
            ->row();

        $nomeOS = '';

        $qb = $this->db
            ->select('a.id, d.id_escola, f.id_aluno, h.id_usuario')
            ->select('e.codigo, e.nome AS escola, g.nome AS aluno, i.nome AS usuario')
            ->join('ei_contratos b', 'b.id = a.id_contrato')
            ->join('ei_diretorias c', 'c.id = b.id_cliente')
            ->join('ei_ordem_servico_escolas d', 'd.id_ordem_servico = a.id', 'left')
            ->join('ei_escolas e', 'e.id = d.id_escola AND e.id_diretoria = c.id', 'left')
            ->join('ei_ordem_servico_alunos f', 'f.id_ordem_servico_escola = d.id', 'left')
            ->join('ei_alunos g', 'g.id = f.id_aluno', 'left')
            ->join('ei_ordem_servico_profissionais h', 'h.id_ordem_servico_escola = d.id', 'left')
            ->join('usuarios i', 'i.id = h.id_usuario', 'left')
            ->where('c.id_empresa', $empresa);
        if ($get['diretoria']) {
            $qb->where('c.id', $get['diretoria']);
        }
        if ($get['contrato']) {
            $qb->where('b.id', $get['contrato']);
        }
        if ($get['ano_semestre']) {
            $qb->where("CONCAT(a.ano, '/', a.semestre) = '{$get['ano_semestre']}'", null, false);
        }
        if ($get['ordem_servico']) {
            $qb->where('a.id', $get['ordem_servico']);
            $nomeOS = ' - ' . $get['ordem_servico'];
        }
        if ($get['municipio']) {
            $qb->where('e.municipio', $get['municipio']);
        }
        if ($get['escola']) {
            $qb->where('e.id', $get['escola']);
        }
        $rowsOS = $qb
            ->get('ei_ordem_servico a')
            ->result();

        $ordensServico = implode(', ', array_unique(array_column($rowsOS, 'id') + [0]));
        $totalEscolas = count(array_unique(array_column($rowsOS, 'id_escola')));
        $totalProfissionais = count(array_unique(array_column($rowsOS, 'id_usuario')));
        $totalAlunos = count(array_unique(array_column($rowsOS, 'id_aluno')));

        $listaEscolas = [];
        $listaProfissionais = [];
        $listaAlunos = [];
        foreach ($rowsOS as $rowOS) {
            $listaEscolas[$rowOS->codigo ?? $rowOS->escola] = implode(' - ', [$rowOS->codigo, $rowOS->escola]);
            $listaProfissionais[$rowOS->id_usuario] = $rowOS->usuario;
            $listaAlunos[$rowOS->id_aluno] = $rowOS->aluno;
        }

        ksort($listaEscolas);
        asort($listaProfissionais);
        asort($listaAlunos);

        $listaEscolas = array_values(array_filter($listaEscolas));
        $listaProfissionais = array_values(array_filter($listaProfissionais));
        $listaAlunos = array_values(array_filter($listaAlunos));

        $lista = [];
        $tamanhoLista = max(count($listaEscolas), count($listaProfissionais), count($listaAlunos));
        for ($i = 0; $i <= $tamanhoLista; $i++) {
            $lista[$i]['escola'] = $listaEscolas[$i] ?? null;
            $lista[$i]['profissional'] = $listaProfissionais[$i] ?? null;
            $lista[$i]['aluno'] = $listaAlunos[$i] ?? null;
        }

        $data2 = [
            'empresa' => $usuario,
            'nomeOS' => $nomeOS,
            'ordensServico' => $ordensServico,
            'totalEscolas' => $totalEscolas,
            'totalProfissionais' => $totalProfissionais,
            'totalAlunos' => $totalAlunos,
            'lista' => $lista,
        ];

        $subquery = "SELECT a.nome AS ordem_servico,
                             CONCAT_WS(' - ', c.codigo, c.nome) AS escola,
                             e.nome AS aluno,
                             g.nome AS curso,
                             k.nome AS funcao, 
                             CASE MIN(i.dia_semana) 
                                  WHEN '0' THEN 'Dom'
                                  WHEN '1' THEN '2&ordf;'
                                  WHEN '2' THEN '3&ordf;'
                                  WHEN '3' THEN '4&ordf;'
                                  WHEN '4' THEN '5&ordf;'
                                  WHEN '5' THEN '6&ordf;'
                                  WHEN '6' THEN 'Sáb'
                                  END AS min_semana,
                             CASE MAX(i.dia_semana) 
                                  WHEN MIN(i.dia_semana) THEN ''
                                  WHEN '0' THEN ' a Dom'
                                  WHEN '1' THEN ' a 2&ordf;'
                                  WHEN '2' THEN ' a 3&ordf;'
                                  WHEN '3' THEN ' a 4&ordf;'
                                  WHEN '4' THEN ' a 5&ordf;'
                                  WHEN '5' THEN ' a 6&ordf;'
                                  WHEN '6' THEN ' a Sáb'
                                  END AS max_semana,
                             TIME_FORMAT(MIN(i.horario_inicio), '%H:%i') AS horario_inicio,
                             TIME_FORMAT(MAX(i.horario_termino), '%H:%i') AS horario_termino,
                             DATE_FORMAT(MIN(d.data_inicio), '%d/%m') AS data_inicio,
                             DATE_FORMAT(MAX(d.data_termino), '%d/%m') AS data_termino,
                             d.modulo,
                             c.codigo,
                             c.municipio,
                             l.id AS id_profissional
                      FROM ei_ordem_servico a
                      INNER JOIN ei_ordem_servico_escolas b ON b.id_ordem_servico = a.id
                      INNER JOIN ei_escolas c ON c.id = b.id_escola
                      INNER JOIN ei_ordem_servico_alunos d ON d.id_ordem_servico_escola = b.id
                      INNER JOIN ei_alunos e ON e.id = d.id_aluno
                      LEFT JOIN ei_alunos_cursos f ON f.id = d.id_aluno_curso AND f.id_aluno = e.id
                      LEFT JOIN ei_cursos g ON g.id = f.id_curso
                      LEFT JOIN ei_ordem_servico_turmas j ON j.id_os_aluno = d.id
                      LEFT JOIN ei_ordem_servico_horarios i ON i.id = j.id_os_horario
                      LEFT JOIN ei_ordem_servico_profissionais h ON h.id = i.id_os_profissional AND h.id_ordem_servico_escola = b.id
                      LEFT JOIN usuarios l ON l.id = h.id_usuario
                      LEFT JOIN empresa_funcoes k ON k.id = i.id_funcao
                      WHERE a.id IN ({$ordensServico})
                      GROUP BY a.id, b.id_escola, e.id, k.id, i.horario_inicio, i.horario_termino
                      ORDER BY IF(CHAR_LENGTH(c.codigo) > 0, c.codigo, CAST(c.nome AS DECIMAL)) ASC, c.municipio ASC, c.nome ASC, COALESCE(k.nome, 'zzz') ASC, 
                               a.nome ASC, e.nome ASC";

        $this->db->query("SET lc_time_names = 'pt_BR'");

        $sql = "SELECT s.ordem_servico,
                       s.funcao,
                       s.escola,
                       s.aluno,
                       s.curso,
                       s.data_inicio,
                       s.data_termino,
                       GROUP_CONCAT(CONCAT(s.min_semana, s.max_semana, ', ', s.horario_inicio, ' às ', s.horario_termino) ORDER BY s.min_semana, s.max_semana, s.horario_inicio, s.horario_termino SEPARATOR ';<br>') AS horario,
                       s.modulo,
                       s.codigo,
                       s.municipio
                FROM ({$subquery}) s
                GROUP BY s.ordem_servico, s.escola, s.aluno
                ORDER BY IF(CHAR_LENGTH(s.codigo) > 0, s.codigo, CAST(s.escola AS DECIMAL)) ASC, s.municipio ASC, s.escola ASC, COALESCE(s.funcao, 'zzz') ASC, s.ordem_servico ASC, s.aluno ASC";

        $data = $this->db->query($sql)->result();

        $sqlTotal = "SELECT s.funcao, 
                            COUNT(DISTINCT(s.id_profissional)) AS qtde_profissionais
                    FROM ({$subquery}) s
                    GROUP BY s.funcao";
        $total = $this->db->query($sqlTotal)->result();
        $totalGroup = [];
        $qtdeProfissionais = [];
        foreach ($total as $row) {
            $totalGroup[$row->funcao] = $row;
            $qtdeProfissionais[$row->funcao] = $row->qtde_profissionais;
        }

        $data2['rows'] = $data;
        $proximaFuncao = array_column($data, 'funcao');
        $primeiraFuncao = array_shift($proximaFuncao);
        array_push($proximaFuncao, $primeiraFuncao);
        $data2['proximaFuncao'] = $proximaFuncao;
        $data2['total'] = $totalGroup;

        ksort($qtdeProfissionais);
        $data2['qtdeProfissionais'] = $qtdeProfissionais;

        $stylesheet = '#table thead tr th { border-top: 4px solid #ddd; padding-top: 8px; } ';
        $stylesheet .= '#ordens_servico { border: 1px solid #aaa; margin-bottom: 0px; } ';
        $stylesheet .= '#ordens_servico thead th { font-size: 12px; padding: 4px; background-color: #DFF0D8; border: 1px solid #aaa; } ';
        $stylesheet .= '#ordens_servico thead th { font-size: 12px; padding: 4px; background-color: #f5f5f5; border: 1px solid #aaa; } ';
        $stylesheet .= '#ordens_servico tbody td { font-size: 10px; padding: 4px; vertical-align: top; border: 1px solid #aaa; } ';

        $view = $this->load->view('ei/pdf_mapa_escolas_x_alunos', $data2, true);

        $this->load->library('m_pdf');
        $this->m_pdf->pdf->setTopMargin(48);
        $this->m_pdf->pdf->AddPage('L');
        $this->m_pdf->pdf->writeHTML($stylesheet, 1);
        $this->m_pdf->pdf->writeHTML($view);

        $this->m_pdf->pdf->Output("EI - Mapa Escolas X Alunos{$nomeOS}.pdf", 'D');
    }

    //--------------------------------------------------------------------

    public function mapa_completo_escolas_x_alunos()
    {
        $data = $this->gerarMapaCompletoEscolaXAlunos();
        $this->load->view('ei/mapa_completo_escolas_x_alunos', $data);
    }

    //--------------------------------------------------------------------

    public function pdf_mapa_completo_escolas_x_alunos()
    {
        $data2 = $this->gerarMapaCompletoEscolaXAlunos(true);

        $stylesheet = '#table thead tr th { border-top: 4px solid #ddd; padding-top: 8px; } ';
        $stylesheet .= '#ordens_servico { border: 1px solid #aaa; margin-bottom: 0px; } ';
        $stylesheet .= '#ordens_servico thead th { font-size: 12px; padding: 4px; background-color: #DFF0D8; border: 1px solid #aaa; } ';
        $stylesheet .= '#ordens_servico thead th { font-size: 12px; padding: 4px; background-color: #f5f5f5; border: 1px solid #aaa; } ';
        $stylesheet .= '#ordens_servico tbody td { font-size: 10px; padding: 4px; vertical-align: top; border: 1px solid #aaa; } ';

        $view = $this->load->view('ei/mapa_completo_escolas_x_alunos', $data2, true);

        $this->load->library('m_pdf');
        $alturaCabecalho = $data2['imprimirCabecalho'] ? 20 : 0;
        $alturaFuncoes = $data2['imprimirFuncoes'] ? 20 : 0;
        $this->m_pdf->pdf->setTopMargin(20 + $alturaCabecalho + $alturaFuncoes);
        $this->m_pdf->pdf->AddPage('L');
        $this->m_pdf->pdf->writeHTML($stylesheet, 1);
        $this->m_pdf->pdf->writeHTML($view);

        $this->m_pdf->pdf->Output("EI - Mapa Completo Escolas X Alunos{$data2['nomeOS']}.pdf", 'D');
    }

    //--------------------------------------------------------------------

    public function xlsx_mapa_completo_escolas_x_alunos()
    {
        $this->load->library('phpSpreadsheet');

        $this->phpspreadsheet->sheet->setCellValue('A1', 'Cód. unidade');
        $this->phpspreadsheet->sheet->setCellValue('B1', 'Aluno');
        $this->phpspreadsheet->sheet->setCellValue('C1', 'Deficiência');
        $this->phpspreadsheet->sheet->setCellValue('D1', 'Curso');
        $this->phpspreadsheet->sheet->setCellValue('E1', 'Horário');
        $this->phpspreadsheet->sheet->setCellValue('F1', 'Profissional');
        $this->phpspreadsheet->sheet->setCellValue('G1', 'Função');
        $this->phpspreadsheet->sheet->setCellValue('H1', 'Data início');
        $this->phpspreadsheet->sheet->setCellValue('I1', 'Data término');
        $this->phpspreadsheet->sheet->setCellValue('J1', 'Módulo');
        $this->phpspreadsheet->sheet->setCellValue('K1', 'Valor hora');
        $this->phpspreadsheet->sheet->setCellValue('L1', 'Horas semanais');

        $this->phpspreadsheet->sheet->getStyle('A1:L1')
            ->applyFromArray([
                'font' => [
                    'bold' => true
                ],
            ]);

        $data = $this->gerarMapaCompletoEscolaXAlunos();

        $qtdeLinhas = 2;
        $nomeFuncao = null;

        $this->phpspreadsheet->sheet->getColumnDimension('E')->setAutoSize(true);
        $this->phpspreadsheet->sheet->getStyle('A:L')->getAlignment()->setVertical('top');

        foreach ($data['rows'] as $row) {
            if ($nomeFuncao != $row->funcao) {
                $this->phpspreadsheet->sheet->setCellValue('A' . $qtdeLinhas, $row->funcao);
                $this->phpspreadsheet->sheet->getStyle('A' . $qtdeLinhas)->getAlignment()->setHorizontal('center');
                $this->phpspreadsheet->sheet->getStyle('A' . $qtdeLinhas)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                ]);
                $this->phpspreadsheet->sheet->mergeCells("A{$qtdeLinhas}:L{$qtdeLinhas}");
                $qtdeLinhas++;
            }

            $this->phpspreadsheet->sheet->setCellValue('A' . $qtdeLinhas, $row->escola);
            $this->phpspreadsheet->sheet->setCellValue('B' . $qtdeLinhas, $row->aluno);
            $this->phpspreadsheet->sheet->setCellValue('C' . $qtdeLinhas, $row->hipotese_diagnostica);
            $this->phpspreadsheet->sheet->setCellValue('D' . $qtdeLinhas, $row->curso);
            $this->phpspreadsheet->sheet->setCellValue('E' . $qtdeLinhas, trim(str_replace(';', ";\n", strip_tags($row->horario))));
            $this->phpspreadsheet->sheet->getStyle('E' . $qtdeLinhas)->getAlignment()->setWrapText(true);
            $this->phpspreadsheet->sheet->setCellValue('F' . $qtdeLinhas, $row->profissional);
            $this->phpspreadsheet->sheet->setCellValue('G' . $qtdeLinhas, $row->funcao);
            if ($row->data_inicio_completa) {
                $this->phpspreadsheet->sheet->setCellValue('H' . $qtdeLinhas,
                    \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($row->data_inicio_completa));
                $this->phpspreadsheet->sheet->getStyle('H' . $qtdeLinhas)
                    ->getNumberFormat()
                    ->setFormatCode(
                        \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY
                    );
            } else {
                $this->phpspreadsheet->sheet->setCellValue('H' . $qtdeLinhas, null);
            }
            if ($row->data_termino_completa) {
                $this->phpspreadsheet->sheet->setCellValue('I' . $qtdeLinhas,
                    \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($row->data_termino_completa));
                $this->phpspreadsheet->sheet->getStyle('I' . $qtdeLinhas)
                    ->getNumberFormat()
                    ->setFormatCode(
                        \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY
                    );
            } else {
                $this->phpspreadsheet->sheet->setCellValue('I' . $qtdeLinhas, null);
            }
            $this->phpspreadsheet->sheet->setCellValue('J' . $qtdeLinhas, $row->modulo);
            if ($row->valor_hora) {
                $this->phpspreadsheet->sheet->setCellValue('K' . $qtdeLinhas, $row->valor_hora);
                $this->phpspreadsheet->sheet->getStyle('K' . $qtdeLinhas)
                    ->getNumberFormat()
                    ->setFormatCode('"R$"#,###,##0.00_-');
            } else {
                $this->phpspreadsheet->sheet->setCellValue('K' . $qtdeLinhas, null);
            }
            if ($row->horas_mensais_custo) {
                $this->phpspreadsheet->sheet->setCellValue('L' . $qtdeLinhas, $row->horas_mensais_custo);
                $this->phpspreadsheet->sheet->getStyle('L' . $qtdeLinhas)
                    ->getNumberFormat()
                    ->setFormatCode('h:mm');
            } else {
                $this->phpspreadsheet->sheet->setCellValue('L' . $qtdeLinhas, null);
            }

            $nomeFuncao = $row->funcao;
            $qtdeLinhas++;
        }

        $fileName = "arquivos/ei/mapa_completo_escolas_x_alunos.xlsx";

        $this->phpspreadsheet->writer->save($fileName);
        header("Content-Type: application/vnd.ms-excel");
        redirect(base_url($fileName));
    }

    //--------------------------------------------------------------------

    private function gerarMapaCompletoEscolaXAlunos($isPdf = false): array
    {
        $get = [
            'diretoria' => $this->input->get('diretoria'),
            'contrato' => $this->input->get('contrato'),
            'ano_semestre' => $this->input->get('ano_semestre'),
            'ordem_servico' => $this->input->get('ordem_servico'),
            'municipio' => $this->input->get('municipio'),
            'escola' => $this->input->get('escola'),
        ];

        $empresa = $this->session->userdata('empresa');

        $usuario = $this->db
            ->select('foto, foto_descricao')
            ->get_where('usuarios', ['id' => $empresa])
            ->row();

        $nomeOS = '';

        $qb = $this->db
            ->select('a.id, d.id_escola, f.id_aluno, h.id_usuario')
            ->select('e.codigo, e.nome AS escola, g.nome AS aluno, i.nome AS usuario')
            ->join('ei_contratos b', 'b.id = a.id_contrato')
            ->join('ei_diretorias c', 'c.id = b.id_cliente')
            ->join('ei_ordem_servico_escolas d', 'd.id_ordem_servico = a.id', 'left')
            ->join('ei_escolas e', 'e.id = d.id_escola AND e.id_diretoria = c.id', 'left')
            ->join('ei_ordem_servico_alunos f', 'f.id_ordem_servico_escola = d.id', 'left')
            ->join('ei_alunos g', 'g.id = f.id_aluno', 'left')
            ->join('ei_ordem_servico_profissionais h', 'h.id_ordem_servico_escola = d.id', 'left')
            ->join('usuarios i', 'i.id = h.id_usuario', 'left')
            ->where('c.id_empresa', $empresa);
        if ($get['diretoria']) {
            $qb->where('c.id', $get['diretoria']);
        }
        if ($get['contrato']) {
            $qb->where('b.id', $get['contrato']);
        }
        if ($get['ano_semestre']) {
            $qb->where("CONCAT(a.ano, '/', a.semestre) = '{$get['ano_semestre']}'", null, false);
        }
        if ($get['ordem_servico']) {
            $qb->where('a.id', $get['ordem_servico']);
            $nomeOS = ' - ' . $get['ordem_servico'];
        }
        if ($get['municipio']) {
            $qb->where('e.municipio', $get['municipio']);
        }
        if ($get['escola']) {
            $qb->where('e.id', $get['escola']);
        }
        $rowsOS = $qb
            ->get('ei_ordem_servico a')
            ->result();

        $ordensServico = implode(', ', array_unique(array_column($rowsOS, 'id') + [0]));
        $totalEscolas = count(array_unique(array_column($rowsOS, 'id_escola')));
        $totalProfissionais = count(array_unique(array_column($rowsOS, 'id_usuario')));
        $totalAlunos = count(array_unique(array_column($rowsOS, 'id_aluno')));

        $listaEscolas = [];
        $listaProfissionais = [];
        $listaAlunos = [];
        foreach ($rowsOS as $rowOS) {
            $listaEscolas[$rowOS->codigo ?? $rowOS->escola] = implode(' - ', [$rowOS->codigo, $rowOS->escola]);
            $listaProfissionais[$rowOS->id_usuario] = $rowOS->usuario;
            $listaAlunos[$rowOS->id_aluno] = $rowOS->aluno;
        }

        ksort($listaEscolas);
        asort($listaProfissionais);
        asort($listaAlunos);

        $listaEscolas = array_values(array_filter($listaEscolas));
        $listaProfissionais = array_values(array_filter($listaProfissionais));
        $listaAlunos = array_values(array_filter($listaAlunos));

        $lista = [];
        $tamanhoLista = max(count($listaEscolas), count($listaProfissionais), count($listaAlunos));
        for ($i = 0; $i <= $tamanhoLista; $i++) {
            $lista[$i]['escola'] = $listaEscolas[$i] ?? null;
            $lista[$i]['profissional'] = $listaProfissionais[$i] ?? null;
            $lista[$i]['aluno'] = $listaAlunos[$i] ?? null;
        }

        $data2 = [
            'empresa' => $usuario,
            'nomeOS' => $nomeOS,
            'ordensServico' => $ordensServico,
            'totalEscolas' => $totalEscolas,
            'totalProfissionais' => $totalProfissionais,
            'totalAlunos' => $totalAlunos,
            'lista' => $lista,
        ];

        $subquery = "SELECT a.nome AS ordem_servico,
                             CONCAT_WS(' - ', c.codigo, c.nome) AS escola,
                             FORMAT(a3.valor, 2, 'de_DE') AS valor_hora,
                             (TIME_TO_SEC(i.horario_termino) + IF(i.horario_inicio > i.horario_termino, 86400, 0)) - TIME_TO_SEC(i.horario_inicio) AS horas_mensais_custo,
                             FORMAT(h.faturamento_semestral_projetado, 2, 'de_DE') AS faturamento_semestral_projetado,
                             i.qtde_dias,
                             FORMAT(i.horas_diarias, 2, 'de_DE') AS horas_diarias,
                             FORMAT(i.horas_semanais, 2, 'de_DE') AS horas_semanais,
                             TIME_FORMAT(i.horas_mensais_custo, '%H:%i') AS horas_mensais,
                             FORMAT(i.horas_semestre, 2, 'de_DE') AS horas_semestre,
                             CONCAT_WS(' - ', e.id, e.nome) AS aluno,
                             e.hipotese_diagnostica,
                             CONCAT_WS(' - ', g.id, g.nome) AS curso,
                             k.nome AS funcao,
                             i.dia_semana,
                             CASE MIN(i.dia_semana) 
                                  WHEN '0' THEN 'Dom'
                                  WHEN '1' THEN '2&ordf;'
                                  WHEN '2' THEN '3&ordf;'
                                  WHEN '3' THEN '4&ordf;'
                                  WHEN '4' THEN '5&ordf;'
                                  WHEN '5' THEN '6&ordf;'
                                  WHEN '6' THEN 'Sáb'
                                  END AS min_semana,
                             CASE MAX(i.dia_semana) 
                                  WHEN MIN(i.dia_semana) THEN ''
                                  WHEN '0' THEN ' a Dom'
                                  WHEN '1' THEN ' a 2&ordf;'
                                  WHEN '2' THEN ' a 3&ordf;'
                                  WHEN '3' THEN ' a 4&ordf;'
                                  WHEN '4' THEN ' a 5&ordf;'
                                  WHEN '5' THEN ' a 6&ordf;'
                                  WHEN '6' THEN ' a Sáb'
                                  END AS max_semana,
                             CASE i.dia_semana
                                  WHEN '0' THEN 'Domingo'
                                  WHEN '1' THEN 'Segunda'
                                  WHEN '2' THEN 'Terça'
                                  WHEN '3' THEN 'Quarta'
                                  WHEN '4' THEN 'Quinta'
                                  WHEN '5' THEN 'Sexta'
                                  WHEN '6' THEN 'Sábado'
                                  END AS nome_dia_semana,
                             TIME_FORMAT(MIN(i.horario_inicio), '%H:%i') AS horario_inicio,
                             TIME_FORMAT(MAX(i.horario_termino), '%H:%i') AS horario_termino,
                             DATE_FORMAT(MIN(d.data_inicio), '%d/%m') AS data_inicio,
                             DATE_FORMAT(MAX(d.data_termino), '%d/%m') AS data_termino,
                             DATE_FORMAT(MIN(d.data_inicio), '%d/%m/%Y') AS data_inicio_completa,
                             DATE_FORMAT(MAX(d.data_termino), '%d/%m/%Y') AS data_termino_completa,
                             c.codigo,
                             l.id AS id_profissional,
                             CONCAT_WS(' - ', l.id, l.nome) AS profissional,
                             d.modulo,
                             c.municipio,
                             i.horas_semestre AS horas_semestre_de,
                             h.faturamento_semestral_projetado AS faturamento_semestral_projetado_de
                      FROM ei_ordem_servico a
                      INNER JOIN ei_contratos a2 ON a2.id = a.id_contrato
                      INNER JOIN ei_ordem_servico_escolas b ON b.id_ordem_servico = a.id
                      INNER JOIN ei_escolas c ON c.id = b.id_escola
                      INNER JOIN ei_ordem_servico_alunos d ON d.id_ordem_servico_escola = b.id
                      INNER JOIN ei_alunos e ON e.id = d.id_aluno
                      LEFT JOIN ei_alunos_cursos f ON f.id = d.id_aluno_curso AND f.id_aluno = e.id
                      LEFT JOIN ei_cursos g ON g.id = f.id_curso
                      LEFT JOIN ei_ordem_servico_turmas j ON j.id_os_aluno = d.id
                      LEFT JOIN ei_ordem_servico_horarios i ON i.id = j.id_os_horario
                      LEFT JOIN ei_ordem_servico_profissionais h ON h.id = i.id_os_profissional AND h.id_ordem_servico_escola = b.id
                      LEFT JOIN usuarios l ON l.id = h.id_usuario
                      LEFT JOIN empresa_funcoes k ON k.id = i.id_funcao
                      LEFT JOIN ei_valores_faturamento a3 ON 
                                a3.id_contrato = a2.id AND a3.ano = a.ano AND 
                                a3.semestre = a.semestre AND a3.id_funcao = k.id
                      WHERE a.id IN ({$ordensServico})
                      GROUP BY a.id, b.id_escola, e.id, k.id, i.dia_semana, i.horario_inicio, i.horario_termino,
                               k.nome, c.codigo, c.nome, c.municipio, a.nome, e.nome
                      ORDER BY COALESCE(k.nome, 'zzz') ASC, 
                               IF(CHAR_LENGTH(c.codigo) > 0, c.codigo, CAST(c.nome AS DECIMAL)) ASC, 
                               c.municipio ASC, c.nome ASC, 
                      a.nome ASC, e.nome ASC";

        $this->db->query("SET lc_time_names = 'pt_BR'");

        $sql = "SELECT s.escola,
                       s.aluno,
                       s.hipotese_diagnostica,
                       s.curso,
                       GROUP_CONCAT(CONCAT(s.nome_dia_semana, ', ', s.horario_inicio, ' às ', s.horario_termino) ORDER BY s.dia_semana, s.min_semana, s.max_semana, s.horario_inicio, s.horario_termino SEPARATOR ';<br>') AS horario,
                       s.profissional,
                       s.data_inicio,
                       s.data_termino,
                       s.data_inicio_completa,
                       s.data_termino_completa,
                       s.modulo,
                       s.valor_hora,
                       s.horas_diarias,
                       s.horas_semanais,
                       s.horas_mensais,
                       TIME_FORMAT(SEC_TO_TIME(SUM(s.horas_mensais_custo)), '%H:%i') AS horas_mensais_custo,
                       s.qtde_dias,
                       s.horas_semestre,
                       s.faturamento_semestral_projetado,
                       s.ordem_servico,
                       s.funcao
                FROM ({$subquery}) s
                GROUP BY s.ordem_servico, s.escola, s.aluno
                ORDER BY COALESCE(s.funcao, 'zzz') ASC, 
                         IF(CHAR_LENGTH(s.codigo) > 0, s.codigo, CAST(s.escola AS DECIMAL)) ASC, 
                         s.municipio ASC, s.escola ASC, s.ordem_servico ASC, s.aluno ASC";

        $data = $this->db->query($sql)->result();

        $sqlTotal = "SELECT s.funcao, 
                            COUNT(DISTINCT(s.id_profissional)) AS qtde_profissionais,
                            SUM(s.horas_mensais_custo) AS qtde_segundos_semanais,
                            FORMAT(SUM(s.horas_semestre_de), 2, 'de_DE') AS horas_semestre, 
                            FORMAT(SUM(s.faturamento_semestral_projetado_de), 2, 'de_DE') AS faturamento_semestral_projetado
                    FROM ({$subquery}) s
                    GROUP BY s.funcao";
        $total = $this->db->query($sqlTotal)->result();
        $totalGroup = [];
        $qtdeProfissionais = [];
        $qtdeHorasSemanais = [];

        $this->load->helper('time');
        foreach ($total as $row) {
            $totalGroup[$row->funcao] = $row;
            $qtdeProfissionais[$row->funcao] = [
                'total_profissionais' => $row->qtde_profissionais,
                'horas_semanais' => secToTime($row->qtde_segundos_semanais, false),
            ];
        }

        $data2['rows'] = $data;
        $proximaFuncao = array_column($data, 'funcao');
        $primeiraFuncao = array_shift($proximaFuncao);
        array_push($proximaFuncao, $primeiraFuncao);
        $data2['proximaFuncao'] = $proximaFuncao;
        $data2['total'] = $totalGroup;

        ksort($qtdeProfissionais);
        $data2['qtdeProfissionais'] = $qtdeProfissionais;

        $data2['imprimirCabecalho'] = $this->input->get('cabecalho');
        $data2['imprimirFuncoes'] = $this->input->get('funcoes');
        $data2['imprimirProfissionais'] = $this->input->get('profissionais');
        $data2['isPdf'] = $isPdf === true;
        $data2['query'] = http_build_query($get);

        return $data2;
    }

    //--------------------------------------------------------------------

    public function pagamento_prestadores()
    {
        $data = $this->ajax_pagamento_prestadores();
        $this->load->view('ei/pagamento_prestadores', $data);
    }

    //--------------------------------------------------------------------

    public function ajax_pagamento_prestadores($isPdf = false)
    {
        $where = [
            'depto' => $this->input->get_post('depto'),
            'diretoria' => $this->input->get_post('diretoria'),
            'ano' => $this->input->get_post('ano'),
            'semestre' => $this->input->get_post('semestre'),
            'mes' => $this->input->get_post('mes'),
            'supervisor' => $this->input->get_post('supervisor'),
            'funcao' => $this->input->get_post('funcao'),
        ];

        $supervisor = $this->input->get_post('supervisor');
        $funcao = $this->input->get_post('funcao');

        $usuario = $this->db
            ->select('nome')
            ->where('id', $supervisor)
            ->get('usuarios')
            ->row();

        $this->load->library('Calendar');

        $data = [
            'nomeSupervisor' => $usuario->nome ?? '',
            'query_string' => http_build_query($where),
            'nomeMes' => $this->calendar->get_month_name($where['mes']),
            'ano' => $where['ano'],
            'is_pdf' => $isPdf,
        ];

        $idMes = intval($where['mes']) - ($where['semestre'] > 1 ? 6 : 0);

        $data['empresa'] = $this->db
            ->select('foto, foto_descricao')
            ->where('id', $this->session->userdata('empresa'))
            ->get('usuarios')
            ->row();

        $dataInicioContrato = date('Y-m-d', mktime(0, 0, 0, (int)$where['mes'], 1, (int)$where['ano']));
        $dataTerminoContrato = date('Y-m-t', mktime(0, 0, 0, (int)$where['mes'], 1, (int)$where['ano']));

        $this->load->model('ei_pagamento_prestador_model', 'pagamento');

        $statusPgto = $this->pagamento::STATUS;

        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';

        $subquery = $this->db
            ->select("b.id, a.cuidador, j.funcao{$mesCargoFuncao} AS funcao, b.cnpj, b.cpf, b.rg, d.escola", false)
            ->select('b.nome_banco, b.agencia_bancaria, b.conta_bancaria, b.tipo_conta_bancaria, b.pessoa_conta_bancaria, b.operacao_conta_bancaria')
            ->select("(CASE h.status_mes{$idMes} WHEN 1 THEN '{$statusPgto['1']}' WHEN 2 THEN '{$statusPgto['2']}' ELSE '{$statusPgto['']}' END) AS status", false)
            ->select("(CASE WHEN h.status_mes{$idMes} = 1 THEN 'bg-success' WHEN h.status_mes{$idMes} = 2 THEN 'bg-danger' WHEN h.data_solicitacao_nota_mes{$idMes} IS NOT NULL THEN 'bg-warning' END) AS cor_status", false)
            ->select(["DATE_FORMAT(h.data_solicitacao_nota_mes{$idMes}, '%d/%m/%Y') AS data_solicitacao_nota"], false)
            ->select(["DATE_FORMAT(h.data_liberacao_pagto_mes{$idMes}, '%d/%m/%Y') AS data_liberacao_pagto"], false)
            ->select("h.observacoes_mes{$idMes} AS observacoes", false)
            ->select(["IFNULL(TIME_TO_SEC(a.total_horas_faturadas_mes{$idMes}), 0) - IFNULL(TIME_TO_SEC(i.total_horas_faturadas_mes{$idMes}), 0) AS total_horas"], false)
            ->select(["IFNULL(a.valor_total_mes{$idMes}, 0) - IFNULL(i.valor_total_mes{$idMes}, 0) AS valor_total"], false)
            ->join('usuarios b', 'b.id = a.id_cuidador')
            ->join('ei_alocados c', 'c.id = a.id_alocado')
            ->join('ei_alocacao_escolas d', 'd.id = c.id_alocacao_escola')
            ->join('ei_alocacao e', 'e.id = d.id_alocacao')
            ->join("(SELECT * FROM ei_alocados_horarios GROUP BY id_alocado, periodo, cargo{$mesCargoFuncao}, funcao{$mesCargoFuncao}) j", "j.id_alocado = c.id AND j.periodo = a.periodo AND j.cargo{$mesCargoFuncao} = a.cargo{$mesCargoFuncao} AND j.funcao{$mesCargoFuncao} = a.funcao{$mesCargoFuncao}", 'left', false)
            ->join('ei_alocados_totalizacao i', 'i.id_alocado = a.id_alocado AND i.periodo = a.periodo AND i.substituicao_semestral IS NOT NULL AND a.substituicao_semestral IS NULL AND i.substituicao_eventual IS NULL', 'left')
            ->join('ei_pagamento_prestador h', 'h.id_alocacao = e.id AND h.id_cuidador = a.id_cuidador', 'left')
            ->where('e.id_empresa', $this->session->userdata('empresa'))
            ->where('e.depto', $this->input->get_post('depto'))
            ->where('e.id_diretoria', $this->input->get_post('diretoria'))
            ->group_start()
            ->where("e.id_supervisor = '{$supervisor}' OR CHAR_LENGTH('{$supervisor}') = 0", null, false)
            ->group_end()
            ->where('e.ano', $this->input->get_post('ano'))
            ->where('e.semestre', $this->input->get_post('semestre'))
            ->group_start()
            ->where("j.funcao{$mesCargoFuncao} = '{$funcao}' OR CHAR_LENGTH('{$funcao}') = 0", null, false)
            ->where('a.substituicao_eventual IS NULL')
            ->group_end()
            ->group_by(['d.id_escola', 'a.id_cuidador', 'j.cargo' . $mesCargoFuncao, 'j.funcao' . $mesCargoFuncao, 'c.id', 'a.periodo'])
            ->order_by('a.cuidador', 'asc')
            ->order_by('d.escola', 'asc')
            ->get_compiled_select('ei_alocados_totalizacao a');

        $sql = "SELECT s.cuidador, s.funcao, s.cnpj, s.cpf, s.rg, s.escola, s.status, s.cor_status, 
                       s.nome_banco, s.agencia_bancaria, s.conta_bancaria, s.tipo_conta_bancaria, s.pessoa_conta_bancaria, s.operacao_conta_bancaria,
                       s.data_solicitacao_nota, s.data_liberacao_pagto, s.observacoes,
                       SUM(GREATEST(s.total_horas, 0)) / 3600 AS total_horas,
                       TIME_FORMAT(SEC_TO_TIME(SUM(GREATEST(s.total_horas, 0))), '%H:%i') AS total_horas_temp,
                       TIME_FORMAT(SEC_TO_TIME(SUM(GREATEST(s.total_horas, 0))), '%H:%i:%s') AS total_horas_completa,
                       FORMAT(GREATEST(SUM(s.valor_total), 0), 2, 'de_DE') AS valor_total,
                       GREATEST(SUM(IFNULL(s.valor_total, 0)), 0) AS valor_total_real
                FROM ({$subquery}) s
                GROUP BY s.id
                ORDER BY s.cuidador ASC";

        $data['rows'] = $this->db->query($sql)->result();

        $supervisores = $this->db
            ->select('id_supervisor AS id, supervisor AS nome')
            ->where('id_empresa', $this->session->userdata('empresa'))
            ->where('depto', $this->input->get_post('depto'))
            ->where('id_diretoria', $this->input->get_post('diretoria'))
            ->where('ano', $this->input->get_post('ano'))
            ->where('semestre', $this->input->get_post('semestre'))
            ->group_by('id_supervisor')
            ->order_by('supervisor', 'asc')
            ->get('ei_alocacao')
            ->result();

        $funcoes = $this->db
            ->select("e.funcao{$mesCargoFuncao} AS funcao")
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->join('ei_alocados_horarios e', 'e.id_alocado = b.id AND e.periodo = a.periodo', 'left')
            ->where('d.id_empresa', $this->session->userdata('empresa'))
            ->where('d.depto', $this->input->get_post('depto'))
            ->where('d.id_diretoria', $this->input->get_post('diretoria'))
            ->group_start()
            ->where("d.id_supervisor = '{$supervisor}' OR CHAR_LENGTH('{$supervisor}') = 0", null, false)
            ->group_end()
            ->where('d.ano', $this->input->get_post('ano'))
            ->where('d.semestre', $this->input->get_post('semestre'))
            ->where("e.funcao{$mesCargoFuncao} IS NOT NULL", null, false)
            ->where('a.substituicao_eventual IS NULL')
            ->group_by('e.funcao' . $mesCargoFuncao)
            ->order_by('e.funcao' . $mesCargoFuncao, 'asc')
            ->get('ei_alocados_totalizacao a')
            ->result();

        $totalHoras = 0;
        $totalHorasTemp = 0;
        $valorTotal = 0;

        $this->load->helper('time');

        foreach ($data['rows'] as $row) {
            $totalHoras += $row->total_horas;
            $totalHorasTemp += timeToSec($row->total_horas_completa);
            $valorTotal += $row->valor_total_real;
        }

        $data['total'] = [
            'horas' => number_format($totalHoras, 1, ',', ''),
            'horas_temp' => secToTime($totalHorasTemp, false),
            'valor' => number_format($valorTotal, 2, ',', '.'),
        ];

        $data['supervisores'] = ['' => 'Todos'] + array_column($supervisores, 'nome', 'id');
        $data['supervisor'] = $supervisor;

        $data['funcoes'] = ['' => 'Todas'] + array_column($funcoes, 'funcao', 'funcao');
        $data['funcao'] = $funcao;

        $this->load->model('usuario_model', 'usuario');
        $data['tipoContaBancaria'] = $this->usuario::TIPOS_CONTA_BANCARIA;
        $data['pessoaContaBancaria'] = $this->usuario::TIPOS_PESSOA_CONTA_BANCARIA;

        return $data;
    }

    //--------------------------------------------------------------------

    public function ajax_save_medicao()
    {
        $data = $this->input->post();

        $totalEscolas = $data['total_escolas'];
        $totalAlunos = $data['total_alunos'];

        unset($data['total_escolas'], $data['total_alunos']);

        $this->db->trans_start();

        foreach ($data as &$row) {
            $row['total_escolas'] = $totalEscolas;
            $row['total_alunos'] = $totalAlunos;
            $row['receita_projetada'] = str_replace(['.', ','], ['', '.'], $row['receita_projetada']);
            $row['receita_efetuada'] = str_replace(['.', ','], ['', '.'], $row['receita_efetuada']);
            $row['pagamentos_efetuados'] = str_replace(['.', ','], ['', '.'], $row['pagamentos_efetuados']);
            $row['resultado'] = str_replace(['.', ','], ['', '.'], $row['resultado']);
            $row['resultado_percentual'] = str_replace(',', '.', $row['resultado_percentual']);

            if ($row['id']) {
                $this->db->update('ei_faturamento_consolidado', $row, ['id' => $row['id']]);
            } else {
                $this->db->insert('ei_faturamento_consolidado', $row);
            }
        }

        $this->db->trans_complete();

        $status = $this->db->trans_status();

        echo json_encode(['status' => $status !== false]);
    }

    //--------------------------------------------------------------------

    public function pdf_pagamento_prestadores()
    {
        $this->load->library('m_pdf');

        $stylesheet = '#pagamento_prestadores thead tr th { border-top: 4px solid #ddd; padding-top: 8px; } ';
        $stylesheet .= '#pagamento_prestadores { border: 1px solid #444; margin-bottom: 0px; } ';
        $stylesheet .= '#pagamento_prestadores thead th { font-size: 12px; padding: 4px; background-color: #f5f5f5; border: 1px solid #444; } ';
        $stylesheet .= '#pagamento_prestadores tbody td { font-size: 10px; padding: 4px; vertical-align: top; border: 1px solid #444; } ';

        $data = $this->ajax_pagamento_prestadores(true);

        $this->m_pdf->pdf->setTopMargin(38);
        $this->m_pdf->pdf->AddPage('L');
        $this->m_pdf->pdf->writeHTML($stylesheet, 1);
        $this->m_pdf->pdf->writeHTML($this->load->view('ei/pagamento_prestadores', $data, true));

        $data = $this->input->get();

        $this->load->library('Calendar');
        $this->calendar->month_type = 'short';
        $nome = 'Pagamentos Consolidados de Prestadores de Serviços - ' . $this->calendar->get_month_name($data['mes']) . '_' . $data['ano'];

        $this->m_pdf->pdf->Output($nome . '.pdf', 'D');
    }

    //--------------------------------------------------------------------

    public function zip_notas_fiscais_pagamento()
    {
        $ano = $this->input->get('ano');
        $semestre = $this->input->get('semestre');
        $mes = $this->input->get('mes');
        $idMes = intval($mes) - ($semestre === '2' ? 6 : 0);

        $depto = $this->input->get('depto');
        $idDiretoria = $this->input->get('diretoria');
        $idSupervisor = $this->input->get('supervisor');

        $qb = $this->db->select('id');
        if ($idSupervisor) {
            $qb->where('id_supervisor', $idSupervisor);
        }
        $alocacoes = $qb
            ->where('id_empresa', $this->session->userdata('empresa'))
            ->where('depto', $depto)
            ->where('id_diretoria', $idDiretoria)
            ->where('ano', $ano)
            ->where('semestre', $semestre)
            ->get('ei_alocacao')
            ->result_array();

        $idAlocacoes = array_column($alocacoes, 'id');

        $pagamentos = $this->db
            ->select("arquivo_nota_fiscal_mes{$idMes} AS arquivo_nota_fiscal", false)
            ->where_in('id_alocacao', $idAlocacoes)
            ->where("arquivo_nota_fiscal_mes{$idMes} IS NOT NULL")
            ->get('ei_pagamento_prestador')
            ->result();

        $this->load->library('zip');

        $path = 'arquivos/ei/notas_fiscais/';
        foreach ($pagamentos as $pagamento) {
            $this->zip->read_file($path . $pagamento->arquivo_nota_fiscal);
        }

        $this->zip->download("Notas Fiscais EI-{$ano}-{$mes}.zip");
    }

    //--------------------------------------------------------------------

    public function faturamento_consolidado()
    {
        $data = $this->ajaxFaturamentoConsolidado();
        $this->load->view('ei/faturamento_consolidado', $data);
    }

    //--------------------------------------------------------------------

    public function faturamento_consolidado_cps()
    {
        $data = $this->ajaxFaturamentoConsolidado();
        $this->load->view('ei/faturamento_consolidado_cps', $data);
    }

    //--------------------------------------------------------------------

    private function ajaxFaturamentoConsolidado(?bool $isPdf = false): array
    {
        $where = [
            'depto' => $this->input->get_post('depto'),
            'diretoria' => $this->input->get_post('diretoria'),
            'ano' => $this->input->get_post('ano'),
            'semestre' => $this->input->get_post('semestre'),
            'mes' => $this->input->get_post('mes'),
            'supervisor' => $this->input->get_post('supervisor'),
            'funcao' => $this->input->get_post('funcao'),
        ];

        $this->load->library('Calendar');

        $data = [
            'query_string' => http_build_query($where),
            'nomeMes' => $this->calendar->get_month_name($where['mes']),
            'ano' => $where['ano'],
            'is_pdf' => $isPdf,
        ];

        $supervisor = $this->input->get_post('supervisor');
        $funcao = $this->input->get_post('funcao');

        $idMes = $where['mes'] - ($where['semestre'] > 1 ? 6 : 0);
        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';

        $data['empresa'] = $this->db
            ->select('foto, foto_descricao')
            ->where('id', $this->session->userdata('empresa'))
            ->get('usuarios')
            ->row();

        $dataInicioMes = "{$where['ano']}-{$where['mes']}-01";
        $dataTerminoMes = date('Y-m-t', strtotime($dataInicioMes));

        $data['rows'] = $this->db
            ->select("a.cuidador, j.funcao{$mesCargoFuncao} AS funcao, d.municipio")
            ->select(["CONCAT_WS(' - ', d.codigo, d.escola) AS escola"], false)
            ->select(["GROUP_CONCAT(DISTINCT j1.nome ORDER BY j1.nome ASC SEPARATOR ',') AS cuidador_sub1"], false)
            ->select(["GROUP_CONCAT(DISTINCT j2.nome ORDER BY j2.nome ASC SEPARATOR ',') AS cuidador_sub2"], false)
            ->select(["GROUP_CONCAT(DISTINCT l.aluno ORDER BY l.aluno ASC SEPARATOR ', ') AS alunos"], false)
            ->select(["GROUP_CONCAT(DISTINCT l.curso ORDER BY l.curso ASC SEPARATOR ', ') AS cursos"], false)
            ->select(["DATE_FORMAT(h.data_envio_solicitacao_mes{$idMes}, '%d/%m/%Y') AS data_envio_solicitacao"], false)
            ->select(["DATE_FORMAT(h.data_aprovacao_mes{$idMes}, '%d/%m/%Y') AS data_aprovacao"], false)
            ->select(["ROUND(GREATEST(IFNULL(a.total_dias_mes{$idMes}, 0) - IFNULL(i.total_dias_mes{$idMes}, 0), 0)) AS total_dias"], false)
            ->select(["ROUND(GREATEST(IFNULL(a.total_dias_mes{$idMes}, 0) - IFNULL(i.total_dias_mes{$idMes}, 0), 0)) AS total_dias_ame"], false)
            ->select('NULL AS total_dias_cps, NULL AS total_dias_dif', false)
            ->select(["TIME_FORMAT(SEC_TO_TIME(GREATEST(  (IFNULL(TIME_TO_SEC(a.total_horas_mes{$idMes}), 0) - IFNULL(TIME_TO_SEC(i.total_horas_mes{$idMes}), 0))  , 0)), '%H:%i') AS total_horas"], false)
            ->select(["TIME_FORMAT(SEC_TO_TIME(GREATEST(  (IFNULL(TIME_TO_SEC(a.total_horas_mes{$idMes}), 0) - IFNULL(TIME_TO_SEC(i.total_horas_mes{$idMes}), 0))  , 0)), '%H:%i') AS total_horas_ame"], false)
            ->select('NULL AS total_horas_cps, NULL AS total_horas_dif', false)
            ->select(["DATE_FORMAT(m.data_hora_envio_solicitacao, '%d/%m/%Y %H:%i') AS data_hora_envio_solicitacao"], false)
            ->select(["DATE_FORMAT(m.data_hora_aprovacao_escola, '%d/%m/%Y %H:%i') AS data_hora_aprovacao_escola"], false)
            ->join('usuarios b', 'b.id = a.id_cuidador')
            ->join('ei_alocados c', 'c.id = a.id_alocado')
            ->join('ei_alocacao_escolas d', 'd.id = c.id_alocacao_escola')
            ->join('ei_alocacao e', 'e.id = d.id_alocacao')
            ->join('ei_ordem_servico e2', 'e2.nome = d.ordem_servico AND e2.ano = e.ano AND e2.semestre = e.semestre')
            ->join("(SELECT j2.* FROM ei_alocados_horarios j2 GROUP BY j2.id_alocado, j2.periodo, j2.cargo{$mesCargoFuncao}, j2.funcao{$mesCargoFuncao}) j", "j.id_alocado = c.id AND j.periodo = a.periodo AND j.cargo{$mesCargoFuncao} = a.cargo{$mesCargoFuncao} AND j.funcao{$mesCargoFuncao} = a.funcao{$mesCargoFuncao}")
            ->join('usuarios j1', 'j1.id = j.id_cuidador_sub1', 'left')
            ->join('usuarios j2', 'j2.id = j.id_cuidador_sub2', 'left')
            ->join('ei_alocados_totalizacao i', "i.id_alocado = a.id_alocado AND i.periodo = a.periodo AND i.cargo{$mesCargoFuncao} = a.cargo{$mesCargoFuncao} AND i.funcao{$mesCargoFuncao} = a.funcao{$mesCargoFuncao} AND (i.substituicao_semestral IS NOT NULL AND a.substituicao_semestral IS NULL AND i.substituicao_eventual IS NULL)", 'left')
            ->join('ei_faturamento h', "h.id_alocacao = e.id AND h.id_escola = d.id_escola AND h.cargo = j.cargo{$mesCargoFuncao} AND h.funcao = j.funcao{$mesCargoFuncao}", 'left')
            ->join('ei_matriculados_turmas k', 'k.id_alocado_horario = j.id', 'left')
            ->join('ei_matriculados l', 'l.id = k.id_matriculado AND l.id_alocacao_escola = d.id', 'left')
            ->join('ei_alocados_aprovacoes m', "m.id_alocado = c.id AND m.cargo = a.cargo{$mesCargoFuncao} AND m.funcao = a.funcao{$mesCargoFuncao} AND m.mes_referencia = '{$where['mes']}'", 'left', false)
            ->where('e.id_empresa', $this->session->userdata('empresa'))
            ->where('e.depto', $this->input->get_post('depto'))
            ->where('e.id_diretoria', $this->input->get_post('diretoria'))
            ->group_start()
            ->where("e.id_supervisor = '{$supervisor}' OR CHAR_LENGTH('{$supervisor}') = 0", null, false)
            ->group_end()
            ->where('e.ano', $this->input->get_post('ano'))
            ->where('e.semestre', $this->input->get_post('semestre'))
            ->group_start()
            ->where("j.funcao{$mesCargoFuncao} = '{$funcao}' OR CHAR_LENGTH('{$funcao}') = 0", null, false)
            ->group_end()
            ->group_start()
            ->where('j.data_inicio_real <=', $dataTerminoMes)
            ->or_where('j.data_inicio_real', null)
            ->group_end()
            ->group_start()
            ->where('j.data_termino_real >=', $dataInicioMes)
            ->or_where('j.data_termino_real', null)
            ->group_end()
            ->where('a.substituicao_eventual IS NULL')
            ->group_by(['d.id_escola', 'a.id_cuidador', 'j.periodo', 'j.cargo' . $mesCargoFuncao, 'j.funcao' . $mesCargoFuncao, 'c.id', 'a.periodo'])
            ->order_by('d.codigo', 'asc')
            ->order_by('d.escola', 'asc')
            ->order_by('a.cuidador', 'asc')
            ->order_by('j.funcao' . $mesCargoFuncao, 'asc')
            ->get('ei_alocados_totalizacao a')
            ->result();

        $supervisores = $this->db
            ->select('id_supervisor AS id, supervisor AS nome')
            ->where('id_empresa', $this->session->userdata('empresa'))
            ->where('depto', $this->input->get_post('depto'))
            ->where('id_diretoria', $this->input->get_post('diretoria'))
            ->where('ano', $this->input->get_post('ano'))
            ->where('semestre', $this->input->get_post('semestre'))
            ->group_by('id_supervisor')
            ->order_by('supervisor', 'asc')
            ->get('ei_alocacao')
            ->result();

        $funcoes = $this->db
            ->select("e.funcao{$mesCargoFuncao} AS funcao")
            ->join('ei_alocados b', 'b.id = a.id_alocado')
            ->join('ei_alocacao_escolas c', 'c.id = b.id_alocacao_escola')
            ->join('ei_alocacao d', 'd.id = c.id_alocacao')
            ->join('ei_alocados_horarios e', 'e.id_alocado = b.id AND e.periodo = a.periodo', 'left')
            ->where('d.id_empresa', $this->session->userdata('empresa'))
            ->where('d.depto', $this->input->get_post('depto'))
            ->where('d.id_diretoria', $this->input->get_post('diretoria'))
            ->group_start()
            ->where("d.id_supervisor = '{$supervisor}' OR CHAR_LENGTH('{$supervisor}') = 0", null, false)
            ->group_end()
            ->where('d.ano', $this->input->get_post('ano'))
            ->where('d.semestre', $this->input->get_post('semestre'))
            ->where("e.funcao{$mesCargoFuncao} IS NOT NULL", null, false)
            ->where('a.substituicao_eventual IS NULL')
            ->group_by('e.funcao' . $mesCargoFuncao)
            ->order_by('e.funcao' . $mesCargoFuncao, 'asc')
            ->get('ei_alocados_totalizacao a')
            ->result();

        $totalHoras = 0;

        $this->load->helper('time');

        $data['totalHorasAme'] = null;
        $data['totalHorasCps'] = null;
        $data['totalHorasDif'] = null;

        foreach ($data['rows'] as $row) {
            $row->cuidador = implode(', ', array_filter([$row->cuidador, $row->cuidador_sub1, $row->cuidador_sub2]));
            $totalHoras += timeToSec($row->total_horas);

            $data['totalHorasAme'] += timeToSec($row->total_horas_ame);
            $data['totalHorasCps'] += timeToSec($row->total_horas_cps);
            $data['totalHorasDif'] += timeToSec($row->total_horas_dif);
        }

        $data['totalHorasAme'] = secToTime($data['totalHorasAme'], false);
        $data['totalHorasCps'] = secToTime($data['totalHorasCps'], false);
        $data['totalHorasDif'] = secToTime($data['totalHorasDif'], false);

        $data['total_horas'] = secToTime($totalHoras, false);

        $data['supervisores'] = ['' => 'Todos'] + array_column($supervisores, 'nome', 'id');
        $data['supervisor'] = $supervisor;

        $data['funcoes'] = ['' => 'Todas'] + array_column($funcoes, 'funcao', 'funcao');
        $data['funcao'] = $funcao;

        return $data;
    }

    //--------------------------------------------------------------------

    public function pdf_faturamento_consolidado()
    {
        $this->load->library('m_pdf');

        $stylesheet = '#faturamento_consolidado thead tr th { border-top: 4px solid #ddd; padding-top: 8px; } ';
        $stylesheet .= '#faturamento_consolidado { border: 1px solid #444; margin-bottom: 0px; } ';
        $stylesheet .= '#faturamento_consolidado thead th { font-size: 12px; padding: 4px; background-color: #f5f5f5; border: 1px solid #444; } ';
        $stylesheet .= '#faturamento_consolidado tbody td { font-size: 10px; padding: 4px; vertical-align: top; border: 1px solid #444; } ';

        $data = $this->ajaxFaturamentoConsolidado(true);

        $this->m_pdf->pdf->setTopMargin(10);
        $this->m_pdf->pdf->AddPage('L');
        $this->m_pdf->pdf->writeHTML($stylesheet, 1);
        $this->m_pdf->pdf->writeHTML($this->load->view('ei/faturamento_consolidado', $data, true));

        $data = $this->input->get();

        $this->load->library('Calendar');
        $this->calendar->month_type = 'short';
        $nome = 'Faturamentos Consolidados - ' . $this->calendar->get_month_name($data['mes']) . '_' . $data['ano'];

        $this->m_pdf->pdf->Output($nome . '.pdf', 'D');
    }

    //--------------------------------------------------------------------

    public function pdf_faturamento_consolidado_cps()
    {
        $this->load->library('m_pdf');

        $stylesheet = '#faturamento_consolidado thead tr th { border-top: 4px solid #ddd; padding-top: 8px; } ';
        $stylesheet .= '#faturamento_consolidado { border: 1px solid #444; margin-bottom: 0px; } ';
        $stylesheet .= '#faturamento_consolidado thead th { font-size: 12px; padding: 4px; background-color: #f5f5f5; border: 1px solid #444; } ';
        $stylesheet .= '#faturamento_consolidado tbody td { font-size: 10px; padding: 4px; vertical-align: top; border: 1px solid #444; } ';

        $data = $this->ajaxFaturamentoConsolidado(true);

        $this->m_pdf->pdf->setTopMargin(10);
        $this->m_pdf->pdf->AddPage('L');
        $this->m_pdf->pdf->writeHTML($stylesheet, 1);
        $this->m_pdf->pdf->writeHTML($this->load->view('ei/faturamento_consolidado_cps', $data, true));

        $data = $this->input->get();

        $this->load->library('Calendar');
        $this->calendar->month_type = 'short';
        $nome = 'Faturamentos Consolidados CPS - ' . $this->calendar->get_month_name($data['mes']) . '_' . $data['ano'];

        $this->m_pdf->pdf->Output($nome . '.pdf', 'D');
    }

    //--------------------------------------------------------------------

    public function xlsx_faturamento_consolidado_cps()
    {
        $this->load->library('phpSpreadsheet');

        $this->phpspreadsheet->sheet->setCellValue('A1', 'Unidade de Ensino');
        $this->phpspreadsheet->sheet->setCellValue('B1', 'Aluno(s)');
        $this->phpspreadsheet->sheet->setCellValue('C1', 'Curso(s)');
        $this->phpspreadsheet->sheet->setCellValue('D1', 'Profissionais');
        $this->phpspreadsheet->sheet->setCellValue('E1', 'Função');
        $this->phpspreadsheet->sheet->setCellValue('F1', 'Qtde. dias AME');
        $this->phpspreadsheet->sheet->setCellValue('G1', 'Qtde. dias CPS');
        $this->phpspreadsheet->sheet->setCellValue('H1', 'Qtde. dias dif.');
        $this->phpspreadsheet->sheet->setCellValue('I1', 'Qtde. horas AME');
        $this->phpspreadsheet->sheet->setCellValue('J1', 'Qtde. horas CPS');
        $this->phpspreadsheet->sheet->setCellValue('K1', 'Qtde. horas dif.');
        $this->phpspreadsheet->sheet->setCellValue('L1', 'Data aprovação faturamento');

        $mes = $this->input->get('mes');
        $ano = $this->input->get('ano');

        $data = $this->ajaxFaturamentoConsolidado();

        $this->phpspreadsheet->sheet->setCellValue('I2', $data['totalHorasAme']);
        $this->phpspreadsheet->sheet->setCellValue('J2', $data['totalHorasCps']);
        $this->phpspreadsheet->sheet->setCellValue('K2', $data['totalHorasDif']);

        $this->phpspreadsheet->sheet->getStyle('A1:L2')
            ->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
            ]);

        $rows = 3;

        foreach ($data['rows'] as $faturamentoConsolidado) {
            $this->phpspreadsheet->sheet->setCellValue('A' . $rows, $faturamentoConsolidado->escola);
            $this->phpspreadsheet->sheet->setCellValue('B' . $rows, $faturamentoConsolidado->alunos);
            $this->phpspreadsheet->sheet->setCellValue('C' . $rows, $faturamentoConsolidado->cursos);
            $this->phpspreadsheet->sheet->setCellValue('D' . $rows, $faturamentoConsolidado->cuidador);
            $this->phpspreadsheet->sheet->setCellValue('E' . $rows, $faturamentoConsolidado->funcao);
            $this->phpspreadsheet->sheet->setCellValue('F' . $rows, $faturamentoConsolidado->total_dias_ame);
            $this->phpspreadsheet->sheet->setCellValue('G' . $rows, $faturamentoConsolidado->total_dias_cps);
            $this->phpspreadsheet->sheet->setCellValue('H' . $rows, $faturamentoConsolidado->total_dias_dif);
            $this->phpspreadsheet->sheet->setCellValue('I' . $rows, $faturamentoConsolidado->total_horas_ame);
            $this->phpspreadsheet->sheet->setCellValue('J' . $rows, $faturamentoConsolidado->total_horas_cps);
            $this->phpspreadsheet->sheet->setCellValue('K' . $rows, $faturamentoConsolidado->total_horas_dif);
            if ($faturamentoConsolidado->data_aprovacao) {
                $this->phpspreadsheet->sheet->setCellValue('L' . $rows,
                    \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($faturamentoConsolidado->data_aprovacao));
                $this->phpspreadsheet->sheet->getStyle('L' . $rows)
                    ->getNumberFormat()
                    ->setFormatCode(
                        \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY
                    );
            } else {
                $this->phpspreadsheet->sheet->setCellValue('L' . $rows, null);
            }
            $rows++;
        }

        $this->load->library('calendar');
        $mesAno = $this->calendar->get_month_name($mes) . '-' . $ano;

        $fileName = "arquivos/ei/ConsolidadoFaturamentoCPS_{$mesAno}.xlsx";

        $this->phpspreadsheet->writer->save($fileName);
        header("Content-Type: application/vnd.ms-excel");
        redirect(base_url($fileName));
    }

    //--------------------------------------------------------------------

    public function recuperar_medicao_mensal()
    {
        $data['empresa'] = $this->db
            ->select('foto, foto_descricao')
            ->where('id', $this->session->userdata('empresa'))
            ->get('usuarios')
            ->row();

        $mes = $this->input->post('mes');
        if (strlen($mes) == 0) {
            $mes = date('m');
        }
        $ano = $this->input->post('ano');
        if (strlen($ano) == 0) {
            $ano = date('Y');
        }

        if (checkdate($mes, 1, $ano) == false or strlen($mes) !== 2 or strlen($ano) !== 4) {
            redirect(site_url('ei/relatorios/medicao'));
        }

        $this->load->library('Calendar');
        $data['mes_nome'] = $this->calendar->get_month_name($mes);
        $data['mes'] = $mes;
        $data['ano'] = $ano;
        $dataInicioMes = "{$ano}-{$mes}-01";
        $dataTerminoMes = date('Y-m-t', strtotime($dataInicioMes));
        $data['semestre'] = $this->input->post('semestre');
        $idMes = intval($mes) - ($data['semestre'] > 1 ? 6 : 0);
        $mesCargoFuncao = $idMes > 1 ? ('_mes' . $idMes) : '';
        $data['query_string'] = http_build_query($this->input->get());
        $idDiretoria = $this->input->post('diretoria');
        $data['id_diretoria'] = $idDiretoria;
        $idMedicaoMensal = $this->input->post('id_medicao_mensal');

        $data['alocacao'] = $this->db
            ->select(["GROUP_CONCAT(DISTINCT a.id ORDER BY a.id ASC SEPARATOR ',') AS id"], false)
            ->select('COUNT(DISTINCT(escola)) AS total_escolas', false)
            ->select('COUNT(DISTINCT(aluno)) AS total_alunos', false)
            ->join('ei_alocacao_escolas b', 'b.id_alocacao = a.id')
            ->join('ei_matriculados c', 'c.id_alocacao_escola = b.id', 'left')
            ->where('a.id_empresa', $this->session->userdata('empresa'))
            ->where('a.ano', $this->input->post('ano'))
            ->where('a.semestre', $this->input->post('semestre'))
            ->group_start()
            ->where('a.id_diretoria', $idDiretoria)
            ->or_where("CHAR_LENGTH('{$idDiretoria}') =", 0)
            ->group_end()
            ->get('ei_alocacao a')
            ->row();

        $idAlocacoes = explode(',', $data['alocacao']->id ?? '0');

        $subquery = $this->db
            ->select("k.id, d.id_alocacao, a.cuidador, j.cargo{$mesCargoFuncao} AS cargo, j.funcao{$mesCargoFuncao} AS funcao, l.id AS id_funcao, j.valor_hora_funcao")
            ->select(["GREATEST(  SUM(IFNULL(TIME_TO_SEC(a.total_horas_mes{$idMes}), 0) - IFNULL(TIME_TO_SEC(i.total_horas_mes{$idMes}), 0))  , 0) AS segundos_realizados_mes{$idMes}"], false)
            ->select(["IFNULL(a.valor_total_mes{$idMes}, j.valor_hora_operacional * ((IFNULL(TIME_TO_SEC(j.horas_mensais_custo), 0) + IFNULL(TIME_TO_SEC(a.horas_descontadas_mes{$idMes}), 0)) / 3600)) AS pagamentos_efetuados2"], false)
            ->select(["IFNULL(a.valor_total_mes{$idMes}, 0) - IFNULL(i.valor_total_mes{$idMes}, 0) AS pagamentos_efetuados"], false)
            ->join('usuarios b', 'b.id = a.id_cuidador')
            ->join('ei_alocados c', 'c.id = a.id_alocado')
            ->join('ei_alocacao_escolas d', 'd.id = c.id_alocacao_escola')
            ->join('ei_alocacao e', 'e.id = d.id_alocacao')
            ->join('ei_ordem_servico e2', 'e2.nome = d.ordem_servico AND e2.ano = e.ano AND e2.semestre = e.semestre')
            ->join("(SELECT * FROM ei_alocados_horarios GROUP BY id_alocado, periodo, cargo{$mesCargoFuncao}, funcao{$mesCargoFuncao}) j", "j.id_alocado = c.id AND j.periodo = a.periodo AND j.cargo{$mesCargoFuncao} = a.cargo{$mesCargoFuncao} AND j.funcao{$mesCargoFuncao} = a.funcao{$mesCargoFuncao}", 'left', false)
            ->join('usuarios j1', 'j1.id = j.id_cuidador_sub1', 'left')
            ->join('usuarios j2', 'j2.id = j.id_cuidador_sub2', 'left')
            ->join('empresa_cargos l1', "l1.nome = j.cargo{$mesCargoFuncao}", 'left')
            ->join('empresa_funcoes l', "l.nome = j.funcao{$mesCargoFuncao} AND l.id_cargo = l1.id", 'left')
            ->join('ei_alocados_totalizacao i', "i.id_alocado = a.id_alocado AND i.periodo = a.periodo AND i.cargo{$mesCargoFuncao} = a.cargo AND i.funcao{$mesCargoFuncao} = a.funcao{$mesCargoFuncao} AND (i.substituicao_semestral IS NOT NULL AND a.substituicao_semestral IS NULL AND i.substituicao_eventual IS NULL)", 'left')
            ->join('ei_faturamento h', "h.id_alocacao = e.id AND h.id_escola = d.id_escola AND h.cargo = j.cargo{$mesCargoFuncao} AND h.funcao = j.funcao{$mesCargoFuncao}", 'left')
            ->join('ei_medicao_mensal_funcoes k', "k.id_medicao_mensal = '{$idMedicaoMensal}' AND k.cargo = a.cargo{$mesCargoFuncao} AND k.funcao = a.funcao{$mesCargoFuncao}", 'left')
            ->where_in('e.id', $idAlocacoes, false)
            ->group_start()
            ->where('j.data_inicio_real <=', $dataTerminoMes)
            ->or_where('j.data_inicio_real', null)
            ->group_end()
            ->group_start()
            ->where('j.data_termino_real >=', $dataInicioMes)
            ->or_where('j.data_termino_real', null)
            ->group_end()
            ->where('a.substituicao_eventual IS NULL')
            ->where('j.id IS NOT NULL')
            ->group_by(['d.id_escola', 'a.id_cuidador', 'j.periodo', 'j.cargo' . $mesCargoFuncao, 'j.funcao' . $mesCargoFuncao, 'c.id', 'a.periodo'])
            ->order_by('j.funcao' . $mesCargoFuncao, 'asc')
            ->get_compiled_select('ei_alocados_totalizacao a');

        $funcoes = $this->db
            ->select('s.id, s.id_alocacao, s.cargo, s.funcao AS nome, s.id_funcao', false)
            ->select('COUNT(DISTINCT s.cuidador) AS total_cuidadores', false)
            ->select("SUM(s.segundos_realizados_mes{$idMes}) AS total_segundos_mes", false)
            ->select(["IFNULL(t.valor_hora_mes{$idMes}, SUM(s.valor_hora_funcao)) AS valor_hora"], false)
            ->select(["FORMAT(t.valor_faturado_mes{$idMes}, 2, 'de_DE') AS receita_projetada"], false)
            ->select(["FORMAT(s.valor_hora_funcao * (SUM(IFNULL(s.segundos_realizados_mes{$idMes}, 0)) / 3600), 2, 'de_DE') AS receita_efetuada"], false)
            ->select(["FORMAT(GREATEST(SUM(IFNULL(s.pagamentos_efetuados, 0)), 0), 2, 'de_DE') AS pagamentos_efetuados"], false)
            ->select(["FORMAT((s.valor_hora_funcao * (SUM(s.segundos_realizados_mes{$idMes}) / 3600)) - SUM(IFNULL(s.pagamentos_efetuados, 0)), 2, 'de_DE') AS resultado"], false)
            ->select(["((s.valor_hora_funcao * (SUM(s.segundos_realizados_mes{$idMes}) / 3600)) - SUM(IFNULL(s.pagamentos_efetuados, 0))) / GREATEST(s.valor_hora_funcao * (SUM(s.segundos_realizados_mes{$idMes}) / 3600), 1) * 100 AS resultado_percentual"], false)
            ->from("({$subquery}) s")
            ->join('ei_faturamento_consolidado t', 't.id_alocacao = s.id_alocacao AND t.cargo = s.cargo AND t.funcao = s.funcao', 'left')
            ->group_by(['s.cargo', 's.funcao'])
            ->order_by('s.funcao', 'asc')
            ->get()
            ->result();

        $this->load->helper('time');

        foreach ($funcoes as $funcao) {
            $nomeMedicaoMensalFuncao = $funcao->id_funcao;
            $funcao->total_horas_realizadas = secToTime($funcao->total_segundos_mes, false);
            $funcao->resultado_percentual = number_format($funcao->resultado_percentual, 1, ',', '');
            unset($funcao->nome);
            unset($funcao->total_segundos_mes);
            $data['funcoes'][$nomeMedicaoMensalFuncao] = $funcao;
        }

        $this->load->helper('time');

        echo json_encode($data);
    }

    //--------------------------------------------------------------------

    public function salvar_medicao_mensal()
    {
        $idDiretoria = $this->input->post('id_diretoria');

        $alocacao = $this->db
            ->select(['a.id, COUNT(DISTINCT c.cuidador) AS total_cuidadores'], false)
            ->join('ei_alocacao_escolas b', 'b.id_alocacao = a.id', 'left')
            ->join('ei_alocados c', 'c.id_alocacao_escola = b.id', 'left')
            ->where('a.id_empresa', $this->session->userdata('empresa'))
            ->where('a.ano', $this->input->post('ano'))
            ->where('a.semestre', $this->input->post('semestre'))
            ->group_start()
            ->where('a.id_diretoria', $idDiretoria)
            ->or_where("CHAR_LENGTH('{$idDiretoria}') =", 0)
            ->group_end()
            ->get('ei_alocacao a')
            ->row();

        if (empty($alocacao)) {
            exit(json_encode(['erro' => 'Mês alocado não encontrado.']));
        }

        $medicaoMensal = [
            'id' => $this->input->post('id_medicao_mensal'),
            'ano' => $this->input->post('ano'),
            'semestre' => $this->input->post('semestre'),
            'mes' => $this->input->post('mes'),
            'depto' => $this->input->post('depto') ?: null,
            'id_diretoria' => $idDiretoria ?: null,
            'total_escolas' => $this->input->post('total_escolas'),
            'total_alunos' => $this->input->post('total_alunos'),
            'total_cuidadores' => $alocacao->total_cuidadores,
            'observacoes' => $this->input->post('observacoes'),
        ];

        $this->load->model('ei_medicao_mensal_model', 'medicao');
        $this->load->model('ei_medicao_mensal_funcao_model', 'medicao_funcao');

        $this->load->library('entities');
        $medicaoMensal = $this->entities->create('EiMedicaoMensal', $medicaoMensal);
        $this->db->trans_start();
        if ($medicaoMensal->id) {
            if ($this->medicao->update($medicaoMensal->id, $medicaoMensal) == false) {
                exit(json_encode(['erro' => $this->medicao->errors()]));
            }
        } else {
            if ($this->medicao->insert($medicaoMensal) == false) {
                exit(json_encode(['erro' => $this->medicao->errors()]));
            }
            $medicaoMensal->id = $this->medicao->getInsertID();
        }

        $this->medicao->setValidationLabel('observacoes', 'Observações');
        $this->medicao->setValidationLabel('total_escolas', 'Quantidade de Escolas');
        $this->medicao->setValidationLabel('total_alunos', 'Quantidade de Alunos');
        $this->medicao_funcao->setValidationLabel('total_pessoas', 'Quantidades');
        $this->medicao_funcao->setValidationLabel('total_horas', 'Qtde horas realizadas');
        $this->medicao_funcao->setValidationLabel('receita_efetuada', 'Receita Efetuada');
        $this->medicao_funcao->setValidationLabel('pagamentos_efetuados', 'Pagamentos Efetuados');
        $this->medicao_funcao->setValidationLabel('resultado_monetario', 'Resultado (R$)');
        $this->medicao_funcao->setValidationLabel('resultado_percentual', 'Resultado (%)');

        $cargos = $this->input->post('cargo');
        $funcoes = $this->input->post('funcao');
        $totalPessoas = $this->input->post('total_cuidadores');
        $totalHorasRealizadas = $this->input->post('total_horas_realizadas');
        $receitaEfetuada = $this->input->post('receita_efetuada');
        $pagamentosEfetuados = $this->input->post('pagamentos_efetuados');
        $resultado = $this->input->post('resultado');
        $resultadoPercentual = $this->input->post('resultado_percentual');

        $idsFuncoes = $this->input->post('id');
        foreach ($idsFuncoes as $k => $idFuncao) {
            $medicaoMensalFuncao = [
                'id' => $idFuncao,
                'id_medicao_mensal' => $medicaoMensal->id,
                'cargo' => $cargos[$k] ?? null,
                'funcao' => $funcoes[$k] ?? null,
                'total_pessoas' => $totalPessoas[$k] ?? null,
                'total_horas' => $totalHorasRealizadas[$k] ?? null,
                'receita_efetuada' => $receitaEfetuada[$k] ?? null,
                'pagamentos_efetuados' => $pagamentosEfetuados[$k] ?? null,
                'resultado_monetario' => $resultado[$k] ?? null,
                'resultado_percentual' => $resultadoPercentual[$k] ?? null,
            ];
            $medicaoMensalFuncao = $this->entities->create('EiMedicaoMensalFuncao', $medicaoMensalFuncao);
            if ($this->medicao_funcao->save($medicaoMensalFuncao) == false) {
                exit(json_encode(['erro' => $this->medicao_funcao->errors()]));
            }
        }
        $this->db->trans_complete();

        echo json_encode(['status' => true]);
    }

}
