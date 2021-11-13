<?php

namespace App\Models;

use App\Entities\DimensionamentoEtapa;

class DimensionamentoEtapaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'dimensionamento_etapas';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = DimensionamentoEtapa::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_atividade',
        'nome',
        'tipo_atividade',
        'grau_complexidade',
        'tamanho_item',
        'peso_item',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_atividade'      => 'required|is_natural_no_zero|max_length[11]',
        'nome'              => 'required|string|max_length[255]',
        'tipo_atividade'    => 'integer|exact_length[1]',
        'grau_complexidade' => 'integer|exact_length[1]',
        'tamanho_item'      => 'integer|exact_length[1]',
        'peso_item'         => 'numeric|max_length[9]',
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

    public const GRAUS_COMPLEXIDADE = [
        '5' => 'Extremamente alta',
        '4' => 'Alta',
        '3' => 'Média',
        '2' => 'Baixa',
        '1' => 'Extremamente baixa',
    ];
    public const TIPOS_ATIVIDADE = [
        '1' => 'Administrativa',
        '2' => 'Física',
        '3' => 'Intelectual',
    ];
    public const DETALHES_TIPOS_ATIVIDADE = [
        '1' => 'Administrativa (papéis, reuniões)',
        '2' => 'Física (bens tangíveis, volumes, pesos)',
        '3' => 'Intelectual (estudos, pesquisas, relatórios)',
    ];
    public const TAMANHOS_ITEM = [
        '5' => 'Extremamente grande',
        '4' => 'Grande',
        '3' => 'Médio',
        '2' => 'Pequeno',
        '1' => 'Extremamente pequeno',
        '0' => 'Sem tamanho definido',
    ];
}
