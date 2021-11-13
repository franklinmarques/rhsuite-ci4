<?php

namespace App\Models;

use App\Entities\EiCargaHoraria;

class EiCargaHorariaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_cargas_horarias';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiCargaHoraria::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_supervisao',
        'data',
        'horario_entrada',
        'horario_saida',
        'horario_entrada_1',
        'horario_saida_1',
        'total',
        'carga_horaria',
        'saldo_dia',
        'observacoes',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_supervisao'     => 'required|is_natural_no_zero|max_length[11]',
        'data'              => 'required|valid_date',
        'horario_entrada'   => 'valid_time',
        'horario_saida'     => 'valid_time',
        'horario_entrada_1' => 'valid_time',
        'horario_saida_1'   => 'valid_time',
        'total'             => 'valid_time',
        'carga_horaria'     => 'valid_time',
        'saldo_dia'         => 'valid_time',
        'observacoes'       => 'string',
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
