<?php

namespace App\Models;

use App\Entities\StAlocado;

class StAlocadoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'st_alocados';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = StAlocado::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_alocacao',
        'id_usuario',
        'nome',
        'cargo',
        'funcao',
        'id_posto',
        'tipo_horario',
        'nivel',
        'tipo_bck',
        'data_recesso',
        'data_retorno',
        'id_usuario_bck',
        'nome_bck',
        'data_desligamento',
        'id_usuario_sub',
        'nome_sub',
        'dias_acrescidos',
        'horas_acrescidas',
        'total_acrescido',
        'total_faltas',
        'total_atrasos',
        'horas_saldo',
        'horas_saldo_acumulado',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_alocacao'           => 'required|is_natural_no_zero|max_length[11]',
        'id_usuario'            => 'required|is_natural_no_zero|max_length[11]',
        'nome'                  => 'required|string|max_length[255]',
        'cargo'                 => 'string|max_length[255]',
        'funcao'                => 'string|max_length[255]',
        'id_posto'              => 'is_natural_no_zero|max_length[11]',
        'tipo_horario'          => 'required|string|max_length[1]',
        'nivel'                 => 'required|string|max_length[1]',
        'tipo_bck'              => 'string|max_length[1]',
        'data_recesso'          => 'valid_date',
        'data_retorno'          => 'valid_date',
        'id_usuario_bck'        => 'is_natural_no_zero|max_length[11]',
        'nome_bck'              => 'string|max_length[255]',
        'data_desligamento'     => 'valid_date',
        'id_usuario_sub'        => 'is_natural_no_zero|max_length[11]',
        'nome_sub'              => 'string|max_length[255]',
        'dias_acrescidos'       => 'numeric|max_length[10]',
        'horas_acrescidas'      => 'numeric|max_length[10]',
        'total_acrescido'       => 'numeric|max_length[10]',
        'total_faltas'          => 'valid_time',
        'total_atrasos'         => 'valid_time',
        'horas_saldo'           => 'valid_time',
        'horas_saldo_acumulado' => 'valid_time',
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
