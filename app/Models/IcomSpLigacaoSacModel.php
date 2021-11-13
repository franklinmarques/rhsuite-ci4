<?php

namespace App\Models;

use App\Entities\IcomSpLigacaoSac;

class IcomSpLigacaoSacModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_sp_ligacoes_sac';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomSpLigacaoSac::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'data',
        'nome_empresa',
        'protocolo',
        'telefone',
        'atendimento',
        'tipo_servico',
        'privado',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'    => 'required|is_natural_no_zero|max_length[11]',
        'data'          => 'required|valid_date',
        'nome_empresa'  => 'required|string|max_length[255]',
        'protocolo'     => 'required|string|max_length[25]',
        'telefone'      => 'string|max_length[255]',
        'atendimento'   => 'required|string|max_length[1]',
        'tipo_servico'  => 'required|string|max_length[255]',
        'privado'       => 'required|integer|exact_length[1]',
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

    public const TIPOS_ATENDIMENTO = [
        'R' => 'Realizado',
        'X' => 'Recusado',
        'T' => 'Encaminhado para TTS',
        'W' => 'Encaminhado para atendimento Webchat',
        'P' => 'Encaminhado para atendimento presencial',
    ];
}
