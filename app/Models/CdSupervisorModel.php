<?php

namespace App\Models;

use App\Entities\CdSupervisor;

class CdSupervisorModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'cd_supervisores';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = CdSupervisor::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_supervisor',
        'id_escola',
        'turno',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_supervisor' => 'required|is_natural_no_zero|max_length[11]',
        'id_escola'     => 'required|is_natural_no_zero|max_length[11]',
        'turno'         => 'required|string|max_length[1]',
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

    public const TURNOS = [
        '' => 'Integral',
        'M' => 'Manhã',
        'T' => 'Tarde',
        'N' => 'Noite',
    ];

}
