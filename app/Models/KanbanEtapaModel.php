<?php

namespace App\Models;

use App\Entities\KanbanEtapa;

class KanbanEtapaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'kanban_etapas';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = KanbanEtapa::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_atividade',
        'id_coluna',
        'status',
        'data_inicio',
        'data_termino',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_atividade'  => 'required|is_natural_no_zero|max_length[11]',
        'id_coluna'     => 'required|is_natural_no_zero|max_length[11]',
        'status'        => 'required|string|max_length[1]',
        'data_inicio'   => 'required|valid_date',
        'data_termino'  => 'valid_date',
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

    public const STATUS = [
        'D' => 'Dentro do prazo',
        'N' => 'No prazo',
        'A' => 'Atrasado',
    ];
}
