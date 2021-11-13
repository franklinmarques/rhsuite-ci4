<?php

namespace App\Models;

use App\Entities\EiSaldoBancoHora;

class EiSaldoBancoHoraModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_saldos_banco_horas';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiSaldoBancoHora::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_supervisao',
        'saldo_mes1',
        'saldo_mes2',
        'saldo_mes3',
        'saldo_mes4',
        'saldo_mes5',
        'saldo_mes6',
        'saldo_mes7',
        'saldo_acumulado_mes1',
        'saldo_acumulado_mes2',
        'saldo_acumulado_mes3',
        'saldo_acumulado_mes4',
        'saldo_acumulado_mes5',
        'saldo_acumulado_mes6',
        'saldo_acumulado_mes7',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_supervisao'         => 'required|is_natural_no_zero|max_length[11]',
        'saldo_mes1'            => 'string|max_length[10]',
        'saldo_mes2'            => 'string|max_length[10]',
        'saldo_mes3'            => 'string|max_length[10]',
        'saldo_mes4'            => 'string|max_length[10]',
        'saldo_mes5'            => 'string|max_length[10]',
        'saldo_mes6'            => 'string|max_length[10]',
        'saldo_mes7'            => 'string|max_length[10]',
        'saldo_acumulado_mes1'  => 'string|max_length[10]',
        'saldo_acumulado_mes2'  => 'string|max_length[10]',
        'saldo_acumulado_mes3'  => 'string|max_length[10]',
        'saldo_acumulado_mes4'  => 'string|max_length[10]',
        'saldo_acumulado_mes5'  => 'string|max_length[10]',
        'saldo_acumulado_mes6'  => 'string|max_length[10]',
        'saldo_acumulado_mes7'  => 'string|max_length[10]',
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
	protected $beforeDelete         = [];
	protected $afterDelete          = [];
}
