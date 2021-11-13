<?php

namespace App\Models;

use App\Entities\Evento;

class EventoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'eventos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = Evento::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'date_from',
        'date_to',
        'type',
        'title',
        'description',
        'link',
        'color',
        'status',
        'id_usuario',
        'id_usuario_referenciado',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'date_from'                 => 'required|valid_date',
        'date_to'                   => 'required|valid_date',
        'type'                      => 'required|integer|max_length[3]',
        'title'                     => 'required|string|max_length[165]',
        'description'               => 'required|string',
        'link'                      => 'string|max_length[300]',
        'color'                     => 'string|max_length[7]',
        'status'                    => 'required|integer|max_length[1]',
        'id_usuario'                => 'integer|max_length[11]',
        'id_usuario_referenciado'   => 'integer|max_length[11]',
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
