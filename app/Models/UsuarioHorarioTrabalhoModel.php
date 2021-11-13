<?php

namespace App\Models;

use App\Entities\UsuarioHorarioTrabalho;

class UsuarioHorarioTrabalhoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'usuarios_horarios_trabalho';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = UsuarioHorarioTrabalho::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_usuario',
        'turno',
        'domingo',
        'segunda_feira',
        'terca_feira',
        'quarta_feira',
        'quinta_feira',
        'sexta_feira',
        'sabado',
        'horas_dia',
        'minutos_descanso_dia',
        'horario_entrada',
        'horario_intervalo',
        'horario_retorno',
        'horario_saida',
        'sem_intervalo',
        'data_cadastro',
        'data_edicao',
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
        'turno'                 => 'required|string|max_length[255]',
        'domingo'               => 'integer|exact_length[1]',
        'segunda_feira'         => 'integer|exact_length[1]',
        'terca_feira'           => 'integer|exact_length[1]',
        'quarta_feira'          => 'integer|exact_length[1]',
        'quinta_feira'          => 'integer|exact_length[1]',
        'sexta_feira'           => 'integer|exact_length[1]',
        'sabado'                => 'integer|exact_length[1]',
        'horas_dia'             => 'required|valid_time',
        'minutos_descanso_dia'  => 'valid_time',
        'horario_entrada'       => 'required|valid_time',
        'horario_intervalo'     => 'valid_time',
        'horario_retorno'       => 'valid_time',
        'horario_saida'         => 'required|valid_time',
        'sem_intervalo'         => 'integer|exact_length[1]',
        'data_cadastro'         => 'required|valid_date',
        'data_edicao'           => 'valid_date',
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
