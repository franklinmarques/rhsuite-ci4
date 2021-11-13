<?php

namespace App\Models;

use App\Entities\IcomAlocacaoMediaDesempenho;

class IcomAlocacaoMediaDesempenhoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_alocacoes_medias_desempenho';
	protected $primaryKey           = 'id_alocacao';
	protected $useAutoIncrement     = false;
	protected $insertID             = 0;
	protected $returnType           = IcomAlocacaoMediaDesempenho::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_alocacao',
        'comprometimento',
        'pontualidade',
        'script',
        'simpatia',
        'empatia',
        'postura',
        'ferramenta',
        'tradutorio',
        'linguistico',
        'neutralidade',
        'discricao',
        'fidelidade',
        'tempo_medio',
        'qtde_atendidas',
        'qtde_recusadas',
        'taxa_ocupacao',
        'taxa_absenteismo',
        'qtde_reclamacoes',
        'extra_1',
        'extra_2',
        'extra_3',
        'total_comportamento_performance',
        'total_monitoria_qualidade',
        'total_desempenho_quantitativo',
        'total',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_alocacao'                       => 'required|integer|max_length[11]',
        'comprometimento'                   => 'numeric|max_length[3]',
        'pontualidade'                      => 'numeric|max_length[3]',
        'script'                            => 'numeric|max_length[3]',
        'simpatia'                          => 'numeric|max_length[3]',
        'empatia'                           => 'numeric|max_length[3]',
        'postura'                           => 'numeric|max_length[3]',
        'ferramenta'                        => 'numeric|max_length[3]',
        'tradutorio'                        => 'numeric|max_length[3]',
        'linguistico'                       => 'numeric|max_length[3]',
        'neutralidade'                      => 'numeric|max_length[3]',
        'discricao'                         => 'numeric|max_length[3]',
        'fidelidade'                        => 'numeric|max_length[3]',
        'tempo_medio'                       => 'valid_time',
        'qtde_atendidas'                    => 'numeric|max_length[11]',
        'qtde_recusadas'                    => 'numeric|max_length[11]',
        'taxa_ocupacao'                     => 'numeric|max_length[5]',
        'taxa_absenteismo'                  => 'numeric|max_length[5]',
        'qtde_reclamacoes'                  => 'numeric|max_length[11]',
        'extra_1'                           => 'numeric|max_length[3]',
        'extra_2'                           => 'numeric|max_length[3]',
        'extra_3'                           => 'numeric|max_length[3]',
        'total_comportamento_performance'   => 'numeric|max_length[3]',
        'total_monitoria_qualidade'         => 'numeric|max_length[3]',
        'total_desempenho_quantitativo'     => 'numeric|max_length[11]',
        'total'                             => 'numeric|max_length[11]',
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
