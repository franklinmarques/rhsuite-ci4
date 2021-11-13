<?php

namespace App\Models;

use App\Entities\AnaliseAdlMaturidade;

class AnaliseAdlMaturidadeModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'analise_adl_maturidades';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = AnaliseAdlMaturidade::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_produto',
        'grau_maturidade',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_produto'        => 'required|is_natural_no_zero|max_length[11]',
        'grau_maturidade'   => 'required|integer|max_length[1]',
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

    public const GRAUS = [
        '1' => 'Embrionário',
        '2' => 'Crescimento',
        '3' => 'Maturidade',
        '4' => 'Declínio',
    ];
    public const POSICOES = [
        '6' => [
            '1' => 'Ampliar posição.',
            '2' => 'Manter posição.',
            '3' => 'Crescer com o mercado.',
            '4' => 'Manter posição.',
        ],
        '5' => [
            '1' => 'Buscar melhorar posição.',
            '2' => 'Buscar melhorar posição. Ampliar participação no mercado.',
            '3' => 'Manter posição, buscando crescimento atrelado ao setor.',
            '4' => 'Tentar encontrar novos mercados.',
        ],
        '4' => [
            '1' => 'Imprescindível melhorar a posição.',
            '2' => 'Buscar melhorar posição. Ampliar participação no mercado.',
            '3' => 'Identificar o nicho de mercado e manter a posição.',
            '4' => 'Encontrar outros mercados ou abandonar o atual mercado.',
        ],
        '3' => [
            '1' => 'Identificar nichos onde possa melhorar a sua posição.',
            '2' => 'Tentar melhorar a posição em determinados nichos.',
            '3' => 'Encontrar um nicho e mantê-lo, ou sair do mercado.',
            '4' => 'Abandonar.',
        ],
        '2' => [
            '1' => 'Melhorar ou sair do mercado.',
            '2' => 'Encontrar um nicho e protegê-lo',
            '3' => 'Sair do mercado.',
            '4' => 'Abandonar.',
        ],
        '1' => [
            '1' => 'Sair do mercado.',
            '2' => 'Sair do mercado.',
            '3' => 'Sair do mercado.',
            '4' => 'Sair do mercado.',
        ],
    ];
    public const NIVEIS = [
        '6' => [
            '1' => 1,
            '2' => 1,
            '3' => 1,
            '4' => 1,
        ],
        '5' => [
            '1' => 1,
            '2' => 1,
            '3' => 1,
            '4' => 0,
        ],
        '4' => [
            '1' => 1,
            '2' => 1,
            '3' => 0,
            '4' => -1,
        ],
        '3' => [
            '1' => 1,
            '2' => 0,
            '3' => -1,
            '4' => -1,
        ],
        '2' => [
            '1' => 0,
            '2' => -1,
            '3' => -1,
            '4' => -1,
        ],
        '1' => [
            '1' => -1,
            '2' => -1,
            '3' => -1,
            '4' => -1,
        ],
    ];
}
