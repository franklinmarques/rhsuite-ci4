<?php

namespace App\Models;

use App\Entities\KanbanAtividade;

class KanbanAtividadeModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'kanban_atividades';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = KanbanAtividade::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'id_quadro',
        'id_usuario_responsavel',
        'nome',
        'descricao',
        'ordem',
        'status',
        'id_etapa_atual',
        'data_limite',
        'tempo_estimado',
        'tempo_gasto',
        'data_criacao',
        'data_fechamento',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'                => 'required|is_natural_no_zero|max_length[11]',
        'id_quadro'                 => 'required|is_natural_no_zero|max_length[11]',
        'id_usuario_responsavel'    => 'required|is_natural_no_zero|max_length[11]',
        'nome'                      => 'required|string|max_length[50]',
        'descricao'                 => 'string',
        'ordem'                     => 'required|is_natural_no_zero|max_length[11]',
        'status'                    => 'required|string|max_length[1]',
        'id_etapa_atual'            => 'required|is_natural_no_zero|max_length[11]',
        'data_limite'               => 'valid_date',
        'tempo_estimado'            => 'integer|max_length[11]',
        'tempo_gasto'               => 'valid_time',
        'data_criacao'              => 'required|valid_date',
        'data_fechamento'           => 'valid_date',
    ];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = ['atualizarOrdem'];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];

    //--------------------------------------------------------------------

    public const STATUS = [
        'D' => 'Dentro do prazo',
        'N' => 'No prazo',
        'A' => 'Atrasado',
    ];

    //--------------------------------------------------------------------

    public function atualizarOrdem($data)
    {
        if (array_key_exists('data', $data) == false) {
            return $data;
        }

        $totalAtividades = $this->db
            ->where('id_empresa', session('empresa'))
            ->where('id_quadro', $data['data']['id_quadro'])
            ->where('id_etapa_atual', $data['data']['id_etapa_atual'])
            ->get($this->table)
            ->num_rows();

        $data['data']['ordem'] = $totalAtividades + 1;

        return $data;
    }
}
