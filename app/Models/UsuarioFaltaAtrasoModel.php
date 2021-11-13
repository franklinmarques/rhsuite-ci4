<?php

namespace App\Models;

use App\Entities\UsuarioFaltaAtraso;

class UsuarioFaltaAtrasoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'usuarios_faltas_atrasos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = UsuarioFaltaAtraso::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'id_usuario',
        'id_colaborador',
        'id_depto',
        'id_area',
        'id_setor',
        'data',
        'falta',
        'horas_atraso',
        'id_colaborador_sub',
        'status',
        'glosa_horas',
        'glosa_dias',
        'horario_entrada',
        'horario_intervalo',
        'horario_retorno',
        'horario_saida',
        'apontamento_positivo',
        'apontamento_negativo',
        'desconto_folha',
        'id_detalhes',
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
        'id_empresa'            => 'required|is_natural_no_zero|max_length[11]',
        'id_usuario'            => 'required|is_natural_no_zero|max_length[11]',
        'id_colaborador'        => 'is_natural_no_zero|max_length[11]',
        'id_depto'              => 'required|is_natural_no_zero|max_length[11]',
        'id_area'               => 'required|is_natural_no_zero|max_length[11]',
        'id_setor'              => 'required|is_natural_no_zero|max_length[11]',
        'data'                  => 'required|valid_date',
        'falta'                 => 'integer|exact_length[1]',
        'horas_atraso'          => 'valid_time',
        'id_colaborador_sub'    => 'is_natural_no_zero|max_length[11]',
        'status'                => 'required|string|max_length[2]',
        'glosa_horas'           => 'valid_time',
        'glosa_dias'            => 'integer|max_length[2]',
        'horario_entrada'       => 'valid_time',
        'horario_intervalo'     => 'valid_time',
        'horario_retorno'       => 'valid_time',
        'horario_saida'         => 'valid_time',
        'apontamento_positivo'  => 'valid_time',
        'apontamento_negativo'  => 'valid_time',
        'desconto_folha'        => 'valid_time',
        'id_detalhes'           => 'integer|max_length[11]',
        'observacoes'           => 'string',
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
