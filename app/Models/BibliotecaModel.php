<?php

namespace App\Models;

use App\Entities\Biblioteca;

class BibliotecaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'biblioteca';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = Biblioteca::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_usuario',
        'tipo',
        'id_categoria',
        'titulo',
        'descricao',
        'link',
        'disciplina',
        'ano_serie',
        'tema_curricular',
        'uso',
        'licenca',
        'produzido_por',
        'tags',
        'data_cadastro',
        'data_editado',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_usuario'        => 'required|integer|max_length[11]',
        'tipo'              => 'required|integer|max_length[11]',
        'id_categoria'      => 'required|integer|max_length[11]',
        'titulo'            => 'required|string|max_length[255]',
        'descricao'         => 'required|string',
        'link'              => 'required|string|max_length[255]',
        'disciplina'        => 'required|string|max_length[255]',
        'ano_serie'         => 'required|string|max_length[255]',
        'tema_curricular'   => 'required|string|max_length[255]',
        'uso'               => 'required|string|max_length[255]',
        'licenca'           => 'required|string|max_length[255]',
        'produzido_por'     => 'required|string|max_length[255]',
        'tags'              => 'required|string',
        'data_cadastro'     => 'required|valid_date',
        'data_editado'      => 'required|valid_date',
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
