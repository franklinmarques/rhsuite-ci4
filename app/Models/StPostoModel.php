<?php

namespace App\Models;

use App\Entities\StPosto;

class StPostoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'st_postos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = StPosto::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_usuario',
        'data',
        'depto',
        'area',
        'setor',
        'cargo',
        'funcao',
        'contrato',
        'total_dias_mensais',
        'total_horas_diarias',
        'matricula',
        'login',
        'horario_entrada',
        'horario_saida',
        'valor_posto',
        'valor_dia',
        'valor_hora',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_usuario'            => 'required|is_natural_no_zero|max_length[11]',
        'data'                  => 'required|valid_date',
        'depto'                 => 'string|max_length[255]',
        'area'                  => 'string|max_length[255]',
        'setor'                 => 'string|max_length[255]',
        'cargo'                 => 'string|max_length[255]',
        'funcao'                => 'string|max_length[255]',
        'contrato'              => 'string|max_length[255]',
        'total_dias_mensais'    => 'required|integer|max_length[11]',
        'total_horas_diarias'   => 'required|integer|max_length[11]',
        'matricula'             => 'string|max_length[255]',
        'login'                 => 'string|max_length[255]',
        'horario_entrada'       => 'valid_time',
        'horario_saida'         => 'valid_time',
        'valor_posto'           => 'required|numeric|max_length[10]',
        'valor_dia'             => 'required|numeric|max_length[10]',
        'valor_hora'            => 'required|numeric|max_length[10]',
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
