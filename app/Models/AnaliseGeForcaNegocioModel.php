<?php

namespace App\Models;

use App\Entities\AnaliseGeForcaNegocio;

class AnaliseGeForcaNegocioModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'analise_ge_forca_negocios';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = AnaliseGeForcaNegocio::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_produto',
        'nome',
        'peso',
        'classificacao',
        'indice_relativo',
        'indice_padrao',
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
        'nome'              => 'required|string|max_length[255]',
        'peso'              => 'integer|max_length[3]',
        'classificacao'     => 'integer|max_length[1]',
        'indice_relativo'   => 'numeric|max_length[3]',
        'indice_padrao'     => 'integer|max_length[1]',
    ];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = ['calcularIndiceRelativo'];
	protected $afterInsert          = [];
	protected $beforeUpdate         = ['calcularIndiceRelativo'];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];

    //--------------------------------------------------------------------

    public const NIVEIS_FORCA = [
        '3' => 'Forte',
        '2' => 'Média',
        '1' => 'Fraca',
    ];
    public const CLASSIFICACOES = [
        '1' => '1',
        '2' => '2',
        '3' => '3',
        '4' => '4',
        '5' => '5',
    ];
    public const PADROES = [
        ['indice_padrao' => 1, 'classificacao' => 4, 'peso' => 10, 'nome' => 'Participação de mercado'],
        ['indice_padrao' => 2, 'classificacao' => 2, 'peso' => 15, 'nome' => 'Crescimento de participação'],
        ['indice_padrao' => 3, 'classificacao' => 5, 'peso' => 10, 'nome' => 'Quantidade de produtos'],
        ['indice_padrao' => 4, 'classificacao' => 2, 'peso' => 13, 'nome' => 'Reputação da marca'],
        ['indice_padrao' => 5, 'classificacao' => 5, 'peso' => 13, 'nome' => 'Rede de distribuição'],
        ['indice_padrao' => 6, 'classificacao' => 3, 'peso' => 25, 'nome' => 'Efetividade promocional'],
        ['indice_padrao' => 7, 'classificacao' => 5, 'peso' => 5, 'nome' => 'Capacidade produtiva'],
        ['indice_padrao' => 8, 'classificacao' => 5, 'peso' => 5, 'nome' => 'Competência gerencial'],
        ['indice_padrao' => 9, 'classificacao' => 2, 'peso' => 4, 'nome' => 'Controle de custos'],
    ];

    //--------------------------------------------------------------------

    protected function calcularIndiceRelativo($data): array
    {
        if (is_null($data['data'])) {
            return $data;
        }

        if (isset($data['data']['peso']) and isset($data['data']['classificacao'])) {
            if (strlen($data['data']['peso']) > 0 and strlen($data['data']['classificacao']) > 0) {
                $data['data']['indice_relativo'] = ($data['data']['peso'] * $data['data']['classificacao']) / 100;
            } else {
                $data['data']['indice_relativo'] = null;
            }
        }

        return $data;
    }

}
