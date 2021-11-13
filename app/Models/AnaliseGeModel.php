<?php

namespace App\Models;

use App\Entities\AnaliseGe;

class AnaliseGeModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'analise_ge';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = AnaliseGe::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'nome',
        'data',
        'tipo',
        'descricao',
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
        'nome'          => 'required|string|max_length[255]',
        'data'          => 'required|valid_date',
        'tipo'          => 'required|string|max_length[1]',
        'descricao'     => 'string',
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

    public const TIPOS = [
        'P' => 'Produto',
        'U' => 'Portifólio',
    ];
    public const TIPOS_POR_EXTENSO = [
        'P' => 'Produtos',
        'U' => 'Unidade estratégica de negócios',
    ];
    public const PADROES = [
        '3' => [
            '3' => 'Investir para crescer; Manter forças.',
            '2' => 'Atacm liderança; Desenvolvem forças; Desenvolvem fraquezas.',
            '1' => 'Especializar forças; Superar fraquezas; Abandonar caso de insucesso.',
        ],
        '2' => [
            '3' => 'Investir em segmentos atrativos; Fortalecer-se frente à concorrência; Incrementar lucratividade/produtividade.',
            '2' => 'Fortalecer ganhos existentes; Forçar segmentos lucrativos; Forçar segmentos baixo risco.',
            '1' => 'Expandir sem risco; Minimizar investimentos; Nacionalizar operações.',
        ],
        '1' => [
            '3' => 'Administrar ganhos atuais; Focar segmentos atrativos; Defender pontos fortes.',
            '2' => 'Proteger segmentos atrativos; Melhorar portifólio de produtos; Minimizar investimentos.',
            '1' => 'Maximizar fluxo de caixa; Dispor quando caixa favorável; Minimizar custos e investimentos.',
        ],
    ];
    public const PADROES_NIVEIS = [
        '3' => ['3' => 3, '2' => 3, '1' => 2,],
        '2' => ['3' => 3, '2' => 2, '1' => 1,],
        '1' => ['3' => 2, '2' => 1, '1' => 1,],
    ];


}
