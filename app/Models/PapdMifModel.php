<?php

namespace App\Models;

use App\Entities\PapdMif;

class PapdMifModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'papd_mif';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = PapdMif::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_paciente',
        'avaliador',
        'data_avaliacao',
        'mif',
        'observacoes',
        'alimentacao',
        'arrumacao',
        'banho',
        'vestimenta_superior',
        'vestimenta_inferior',
        'higiene_pessoal',
        'controle_vesical',
        'controle_intestinal',
        'leito_cadeira',
        'sanitario',
        'banheiro_chuveiro',
        'marcha',
        'cadeira_rodas',
        'escadas',
        'compreensao_ambas',
        'compreensao_visual',
        'expressao_verbal',
        'expressao_nao_verbal',
        'interacao_social',
        'resolucao_problemas',
        'memoria',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_paciente'           => 'required|is_natural_no_zero|max_length[11]',
        'avaliador'             => 'required|string|max_length[255]',
        'data_avaliacao'        => 'required|valid_date',
        'mif'                   => 'integer|max_length[3]',
        'observacoes'           => 'string',
        'alimentacao'           => 'integer|exact_length[1]',
        'arrumacao'             => 'integer|exact_length[1]',
        'banho'                 => 'integer|exact_length[1]',
        'vestimenta_superior'   => 'integer|exact_length[1]',
        'vestimenta_inferior'   => 'integer|exact_length[1]',
        'higiene_pessoal'       => 'integer|exact_length[1]',
        'controle_vesical'      => 'integer|exact_length[1]',
        'controle_intestinal'   => 'integer|exact_length[1]',
        'leito_cadeira'         => 'integer|exact_length[1]',
        'sanitario'             => 'integer|exact_length[1]',
        'banheiro_chuveiro'     => 'integer|exact_length[1]',
        'marcha'                => 'integer|exact_length[1]',
        'cadeira_rodas'         => 'integer|exact_length[1]',
        'escadas'               => 'integer|exact_length[1]',
        'compreensao_ambas'     => 'integer|exact_length[1]',
        'compreensao_visual'    => 'integer|exact_length[1]',
        'expressao_verbal'      => 'integer|exact_length[1]',
        'expressao_nao_verbal'  => 'integer|exact_length[1]',
        'interacao_social'      => 'integer|exact_length[1]',
        'resolucao_problemas'   => 'integer|exact_length[1]',
        'memoria'               => 'integer|exact_length[1]',
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
