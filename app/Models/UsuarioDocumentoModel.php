<?php

namespace App\Models;

use App\Entities\UsuarioDocumento;

class UsuarioDocumentoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'usuarios_documentos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = UsuarioDocumento::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_usuario',
        'tipo',
        'nome',
        'arquivo',
        'data_inicio',
        'data_termino',
        'valor_hora_periodo',
        'valor_mensal',
        'qtde_horas_mensais',
        'localidade',
        'status_ativo',
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
        'tipo'                  => 'required|integer|exact_length[1]',
        'nome'                  => 'required|string|is_unique[usuarios_documentos.nome,id,{id}]|max_length[255]',
        'arquivo'               => 'string|max_length[255]',
        'data_inicio'           => 'valid_date',
        'data_termino'          => 'valid_date',
        'valor_hora_periodo'    => 'numeric|max_length[10]',
        'valor_mensal'          => 'numeric|max_length[10]',
        'qtde_horas_mensais'    => 'valid_time',
        'localidade'            => 'string',
        'status_ativo'          => 'integer|exact_length[1]',
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
