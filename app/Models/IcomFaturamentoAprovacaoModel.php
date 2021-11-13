<?php

namespace App\Models;

use App\Entities\IcomFaturamentoAprovacao;

class IcomFaturamentoAprovacaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_faturamentos_aprovacoes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomFaturamentoAprovacao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'mes_referencia',
        'ano_referencia',
        'id_usuario_aprovador',
        'data_aprovacao',
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
        'mes_referencia'        => 'required|integer|max_length[2]',
        'ano_referencia'        => 'required|int|max_length[4]',
        'id_usuario_aprovador'  => 'required|is_natural_no_zero|max_length[11]',
        'data_aprovacao'        => 'required|valid_date',
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
