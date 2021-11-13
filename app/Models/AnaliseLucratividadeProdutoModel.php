<?php

namespace App\Models;

use App\Entities\AnaliseLucratividadeProduto;

class AnaliseLucratividadeProdutoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'analise_lucratividade_produtos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = AnaliseLucratividadeProduto::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_analise',
        'nome',
        'categoria',
        'nivel',
        'potencial_valor',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_analise'        => 'required|is_natural_no_zero|max_length[11]',
        'nome'              => 'required|string|max_length[255]',
        'categoria'         => 'required|string|max_length[1]',
        'nivel'             => 'required|integer|max_length[1]',
        'potencial_valor'   => 'numeric|max_length[10]',
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

    public const CATEGORIAS = [
        'A' => 'A',
        'B' => 'B',
        'C' => 'C',
        'D' => 'D',
        'E' => 'E',
    ];
    public const NIVEIS = [
        '3' => 'Alto',
        '2' => 'Médio',
        '1' => 'Baixo',
        '0' => 'Nenhum',
        '-1' => 'Variável',
    ];
    public const NIVEIS_POR_EXTENSO = [
        '3' => 'Alta lucratividade',
        '2' => 'Média lucratividade',
        '1' => 'Baixa lucratividade',
        '0' => 'Nenhuma lucratividade',
        '-1' => 'Lucratividade variável',
    ];

}
