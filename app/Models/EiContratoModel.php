<?php

namespace App\Models;

use App\Entities\EiContrato;

class EiContratoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_contratos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiContrato::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_cliente',
        'contrato',
        'data_inicio',
        'data_termino',
        'data_reajuste1',
        'indice_reajuste1',
        'data_reajuste2',
        'indice_reajuste2',
        'data_reajuste3',
        'indice_reajuste3',
        'data_reajuste4',
        'indice_reajuste4',
        'data_reajuste5',
        'indice_reajuste5',
        'minutos_tolerancia_entrada_saida',
        'horario_padrao_banda_morta',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_cliente'                        => 'required|is_natural_no_zero|max_length[11]',
        'contrato'                          => 'required|string|max_length[30]',
        'data_inicio'                       => 'required|valid_date',
        'data_termino'                      => 'required|valid_date',
        'data_reajuste1'                    => 'valid_date',
        'indice_reajuste1'                  => 'numeric|max_length[11]',
        'data_reajuste2'                    => 'valid_date',
        'indice_reajuste2'                  => 'numeric|max_length[11]',
        'data_reajuste3'                    => 'valid_date',
        'indice_reajuste3'                  => 'numeric|max_length[11]',
        'data_reajuste4'                    => 'valid_date',
        'indice_reajuste4'                  => 'numeric|max_length[11]',
        'data_reajuste5'                    => 'valid_date',
        'indice_reajuste5'                  => 'numeric|max_length[11]',
        'minutos_tolerancia_entrada_saida'  => 'integer|max_length[11]',
        'horario_padrao_banda_morta'        => 'integer|exact_length[1]',
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
