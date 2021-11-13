<?php

namespace App\Models;

use App\Entities\IcomSpItem;

class IcomSpItemModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_sp_itens';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomSpItem::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'data',
        'tipo_old',
        'tipo',
        'versao',
        'mes',
        'ano',
        'descricao',
        'arquivo',
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
        'tipo'          => 'string|max_length[13]',
        'versao'        => 'string|max_length[50]',
        'mes'           => 'required|integer|max_length[2]',
        'ano'           => 'required|int|max_length[4]',
        'descricao'     => 'string',
        'arquivo'       => 'required|string|max_length[255]',
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

    protected $uploadConfig = [
        'arquivo' => ['upload_path' => './arquivos/icom/pdf/', 'allowed_types' => 'pdf']
    ];

    public const TIPOS = [
        'escala' => 'Escala',
        'pausa' => 'Pausa',
        'manual' => 'Manual',
        'escalonamento' => 'Escalonamento',
        'revezamento' => 'Revezamento',
    ];
    public const TIPOS_OLD = [
        '1' => 'Manual',
        '13' => 'Escala',
        '2' => 'Escala Folgas ABR',
        '3' => 'Escala Folgas ICOM',
        '4' => 'Escala Folgas ABR/ICOM',
        '5' => 'Escala Final de Semana ABR',
        '6' => 'Escala Final de Semana ICOM',
        '7' => 'Escala Final de Semana ABR/ICOM',
        '8' => 'Folga Bônus ABR',
        '9' => 'Folga Bônus ICOM',
        '10' => 'Folga Bônus ABR/ICOM',
        '11' => 'Revezamento ABR/ICOM',
        '12' => 'Escalonamento',
    ];
}
