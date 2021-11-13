<?php

namespace App\Models;

use App\Entities\AnalisePercepcao;

class AnalisePercepcaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'analise_percepcao';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = AnalisePercepcao::class;
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
	protected $beforeUpdate         = ['prepararPontuacoes'];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];

    //--------------------------------------------------------------------

    public const TIPOS = [
        'I' => 'Individual',
        'G' => 'Grupo',
    ];
    public const TIPOS_POR_EXTENSO = [
        'I' => 'Concorrentes individualmente',
        'G' => 'Grupos de concorrentes',
    ];

    //--------------------------------------------------------------------

    protected function prepararPontuacoes($data)
    {
        if (is_null($data['data'])) {
            return $data;
        }

        $oldDataGroup = $this->find($data['id']);
        if (!is_array($oldDataGroup)) {
            $oldDataGroup = [$oldDataGroup];
        }

        $tipo = $data['data']['tipo'] ?? null;

        if (array_key_exists($tipo, self::$tipos)) {
            foreach ($oldDataGroup as $oldData) {
                if ($tipo != $oldData->tipo) {
                    if ($tipo == 'G') {
                        $atributos = $this->db
                            ->select('id')
                            ->where('id_analise', $data['id'])
                            ->get('analise_percepcao_atributos')
                            ->result_array();

                        $atributos = array_column($atributos, 'id') + [0];

                        $this->db
                            ->where_in('id_atributos', $atributos)
                            ->delete('analise_percepcao_pontuacoes');
                    } else {
                        $this->db->delete('analise_percepcao_grupos', ['id_analise' => $data['id']]);
                    }
                }
            }
        }

        return $data;
    }
}
