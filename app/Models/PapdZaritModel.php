<?php

namespace App\Models;

use App\Entities\PapdZarit;

class PapdZaritModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'papd_zarit';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = PapdZarit::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_paciente',
        'avaliador',
        'pessoa_pesquisada',
        'data_avaliacao',
        'zarit',
        'observacoes',
        'assistencia_excessiva',
        'tempo_desperdicado',
        'estresse_cotidiano',
        'constrangimento_alheio',
        'influencia_negativa',
        'futuro_receoso',
        'dependencia',
        'impacto_saude',
        'perda_privacidade',
        'perda_vida_social',
        'dependencia_exclusiva',
        'tempo_desgaste',
        'perda_controle',
        'duvida_prestatividade',
        'expectativa_qualidade',
        'sobrecarga',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_paciente'               => 'required|is_natural_no_zero|max_length[11]',
        'avaliador'                 => 'required|string|max_length[255]',
        'pessoa_pesquisada'         => 'string|max_length[255]',
        'data_avaliacao'            => 'required|valid_date',
        'zarit'                     => 'integer|max_length[3]',
        'observacoes'               => 'string',
        'assistencia_excessiva'     => 'integer|exact_length[1]',
        'tempo_desperdicado'        => 'integer|exact_length[1]',
        'estresse_cotidiano'        => 'integer|exact_length[1]',
        'constrangimento_alheio'    => 'integer|exact_length[1]',
        'influencia_negativa'       => 'integer|exact_length[1]',
        'futuro_receoso'            => 'integer|exact_length[1]',
        'dependencia'               => 'integer|exact_length[1]',
        'impacto_saude'             => 'integer|exact_length[1]',
        'perda_privacidade'         => 'integer|exact_length[1]',
        'perda_vida_social'         => 'integer|exact_length[1]',
        'dependencia_exclusiva'     => 'integer|exact_length[1]',
        'tempo_desgaste'            => 'integer|exact_length[1]',
        'perda_controle'            => 'integer|exact_length[1]',
        'duvida_prestatividade'     => 'integer|exact_length[1]',
        'expectativa_qualidade'     => 'integer|exact_length[1]',
        'sobrecarga'                => 'integer|exact_length[1]',
    ];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = ['setSobrecarga'];
	protected $afterInsert          = [];
	protected $beforeUpdate         = ['setSobrecarga'];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];

    //--------------------------------------------------------------------

    public const SOBRECARGAS = [
        '1' => 'Leve',
        '2' => 'Moderada',
        '3' => 'Grave',
    ];

    //--------------------------------------------------------------------

    protected function setSobrecarga($data): array
    {
        if (!empty($data['data']) == false) {
            return $data;
        }

        if (intval($data['data']['zarit']) > 21) {
            $data['data']['sobrecarga'] = 3;
        } elseif (intval($data['data']['zarit']) > 14) {
            $data['data']['sobrecarga'] = 2;
        } else {
            $data['data']['sobrecarga'] = 1;
        }

        return $data;
    }
}
