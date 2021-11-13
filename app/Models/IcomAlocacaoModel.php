<?php

namespace App\Models;

use App\Entities\IcomAlocacao;

class IcomAlocacaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_alocacoes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomAlocacao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'id_depto',
        'id_area',
        'id_setor',
        'mes',
        'ano',
        'data_aprovacao_pagto',
        'id_usuario_aprovador_pagto',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'                    => 'required|is_natural_no_zero|max_length[11]',
        'id_depto'                      => 'required|integer|max_length[11]',
        'id_area'                       => 'required|integer|max_length[11]',
        'id_setor'                      => 'required|integer|max_length[11]',
        'mes'                           => 'required|integer|exact_length[2]',
        'ano'                           => 'required|int|max_length[4]',
        'data_aprovacao_pagto'          => 'valid_date',
        'id_usuario_aprovador_pagto'    => 'string|max_length[255]',
    ];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = [];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
    protected $beforeDelete         = ['restaurarSaldoBancoHoras'];
    protected $afterDelete          = ['atualizarSaldoBancoHoras'];

    //--------------------------------------------------------------------

    protected function restaurarSaldoBancoHoras($data)
    {
        $this->db->trans_start();

        if (!empty($data[$this->primaryKey]) == false) {
            return $data;
        }

        $rows = $this->db
            ->select('b.id_usuario, c.banco_horas_icom')
            ->select("SUM(TIME_TO_SEC(d.saldo_banco_horas)) AS saldo_banco_horas", false)
            ->join('icom_alocados b', 'b.id_alocacao = a.id')
            ->join('usuarios c', 'c.id = b.id_usuario')
            ->join('icom_apontamentos d', 'd.id_alocado = b.id', 'left')
            ->where_in('a.id', $data[$this->primaryKey])
            ->where('d.saldo_banco_horas IS NOT NULL')
            ->group_by('c.id')
            ->get('icom_alocacoes a')
            ->result();

        if (empty($rows)) {
            return $data;
        }

        $this->load->helper('time');

        foreach ($rows as $row) {
            $bancoHoras = timeToSec($row->banco_horas_icom) - ($row->saldo_banco_horas ?? 0);

            $this->db->update('usuarios', ['banco_horas_icom' => secToTime($bancoHoras)], ['id' => $row->id_usuario]);
        }

        return $data;
    }

    //--------------------------------------------------------------------

    protected function atualizarSaldoBancoHoras($data)
    {
        $this->db->trans_complete();

        return $data;
    }
}
