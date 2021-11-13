<?php

namespace App\Models;

use App\Entities\UsuarioDemissao;

class UsuarioDemissaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'usuarios_demissoes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = UsuarioDemissao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_usuario',
        'id_empresa',
        'data_demissao',
        'motivo_demissao',
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
        'id_usuario'        => 'required|is_natural_no_zero|max_length[11]',
        'id_empresa'        => 'required|is_natural_no_zero|max_length[11]',
        'data_demissao'     => 'required|valid_date',
        'motivo_demissao'   => 'required|integer|max_length[1]',
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

    public const MOTIVOS_DEMISSAO = [
        '1' => 'Demissão sem justa causa',
        '2' => 'Demissão por justa causa',
        '3' => 'Pedido de demissão',
        '4' => 'Término do contrato',
        '5' => 'Rescisão antecipada pelo empregado',
        '6' => 'Rescisão antecipada pelo empregador',
        '7' => 'Desistência da vaga',
        '8' => 'Rescisão estagiário',
        '9' => 'Rescisão por acordo',
        '10' => 'Distrato temporário',
        '11' => 'Distrato',
        '12' => 'Falecimento',
    ];
}
