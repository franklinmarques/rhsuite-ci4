<?php

namespace App\Models;

use App\Entities\Atividade;

class AtividadeModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'atividades';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = Atividade::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_atividade_mae',
        'id_usuario',
        'tipo',
        'prioridade',
        'atividade',
        'data_cadastro',
        'data_limite',
        'data_lembrete',
        'data_fechamento',
        'status',
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
        'id_atividade_mae'  => 'is_natural_no_zero|max_length[11]',
        'id_usuario'        => 'required|is_natural_no_zero|max_length[11]',
        'tipo'              => 'required|string|max_length[1]',
        'prioridade'        => 'required|integer|max_length[1]',
        'atividade'         => 'required|string',
        'data_cadastro'     => 'required|valid_date',
        'data_limite'       => 'required|valid_date',
        'data_lembrete'     => 'required|valid_date',
        'data_fechamento'   => 'valid_date',
        'status'            => 'required|integer|max_length[1]',
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

    //--------------------------------------------------------------------

    public const TIPO = [
        'G' => 'Gestão',
        'O' => 'Operacional',
    ];
    public const PRIORIDADE = [
        '0' => 'Baixa',
        '1' => 'Média',
        '2' => 'Alta',
    ];
    public const STATUS = [
        '0' => 'Não-finalizado',
        '1' => 'Finalizado',
    ];

}
