<?php

namespace App\Models;

use App\Entities\FacilityItem;

class FacilityItemModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'facilities_itens';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = FacilityItem::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_sala',
        'ativo',
        'nome',
        'codigo',
        'tipo',
        'data_entrada_operacao',
        'anos_duracao',
        'periodicidade_vistoria',
        'mes_vistoria_jan',
        'mes_vistoria_fev',
        'mes_vistoria_mar',
        'mes_vistoria_abr',
        'mes_vistoria_mai',
        'mes_vistoria_jun',
        'mes_vistoria_jul',
        'mes_vistoria_ago',
        'mes_vistoria_set',
        'mes_vistoria_out',
        'mes_vistoria_nov',
        'mes_vistoria_dez',
        'periodicidade_manutencao',
        'mes_manutencao_jan',
        'mes_manutencao_fev',
        'mes_manutencao_mar',
        'mes_manutencao_abr',
        'mes_manutencao_mai',
        'mes_manutencao_jun',
        'mes_manutencao_jul',
        'mes_manutencao_ago',
        'mes_manutencao_set',
        'mes_manutencao_out',
        'mes_manutencao_nov',
        'mes_manutencao_dez',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_sala'                   => 'required|is_natural_no_zero|max_length[11]',
        'ativo'                     => 'required|integer|exact_length[1]',
        'nome'                      => 'required|string|max_length[50]',
        'codigo'                    => 'string|max_length[10]',
        'tipo'                      => 'string|max_length[50]',
        'data_entrada_operacao'     => 'valid_date',
        'anos_duracao'              => 'integer|max_length[3]',
        'periodicidade_vistoria'    => 'string|max_length[1]',
        'mes_vistoria_jan'          => 'integer|exact_length[1]',
        'mes_vistoria_fev'          => 'integer|exact_length[1]',
        'mes_vistoria_mar'          => 'integer|exact_length[1]',
        'mes_vistoria_abr'          => 'integer|exact_length[1]',
        'mes_vistoria_mai'          => 'integer|exact_length[1]',
        'mes_vistoria_jun'          => 'integer|exact_length[1]',
        'mes_vistoria_jul'          => 'integer|exact_length[1]',
        'mes_vistoria_ago'          => 'integer|exact_length[1]',
        'mes_vistoria_set'          => 'integer|exact_length[1]',
        'mes_vistoria_out'          => 'integer|exact_length[1]',
        'mes_vistoria_nov'          => 'integer|exact_length[1]',
        'mes_vistoria_dez'          => 'integer|exact_length[1]',
        'periodicidade_manutencao'  => 'string|max_length[1]',
        'mes_manutencao_jan'        => 'integer|exact_length[1]',
        'mes_manutencao_fev'        => 'integer|exact_length[1]',
        'mes_manutencao_mar'        => 'integer|exact_length[1]',
        'mes_manutencao_abr'        => 'integer|exact_length[1]',
        'mes_manutencao_mai'        => 'integer|exact_length[1]',
        'mes_manutencao_jun'        => 'integer|exact_length[1]',
        'mes_manutencao_jul'        => 'integer|exact_length[1]',
        'mes_manutencao_ago'        => 'integer|exact_length[1]',
        'mes_manutencao_set'        => 'integer|exact_length[1]',
        'mes_manutencao_out'        => 'integer|exact_length[1]',
        'mes_manutencao_nov'        => 'integer|exact_length[1]',
        'mes_manutencao_dez'        => 'integer|exact_length[1]',
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
