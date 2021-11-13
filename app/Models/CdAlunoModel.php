<?php

namespace App\Models;

use App\Entities\CdAluno;

class CdAlunoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'cd_alunos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = CdAluno::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'nome',
        'id_escola',
        'endereco',
        'numero',
        'complemento',
        'municipio',
        'telefone',
        'contato',
        'email',
        'cep',
        'hipotese_diagnostica',
        'nome_responsavel',
        'observacoes',
        'data_matricula',
        'data_afastamento',
        'data_desligamento',
        'periodo_manha',
        'periodo_tarde',
        'periodo_noite',
        'status',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'nome'                  => 'required|string|max_length[100]',
        'id_escola'             => 'required|is_natural_no_zero|max_length[11]',
        'endereco'              => 'string|max_length[255]',
        'numero'                => 'integer|max_length[11]',
        'complemento'           => 'string|max_length[255]',
        'municipio'             => 'string|max_length[100]',
        'telefone'              => 'string|max_length[50]',
        'contato'               => 'string|max_length[255]',
        'email'                 => 'string|max_length[255]',
        'cep'                   => 'string|max_length[20]',
        'hipotese_diagnostica'  => 'required|string|max_length[255]',
        'nome_responsavel'      => 'string|max_length[100]',
        'observacoes'           => 'string',
        'data_matricula'        => 'valid_date',
        'data_afastamento'      => 'valid_date',
        'data_desligamento'     => 'valid_date',
        'periodo_manha'         => 'required|integer|max_length[1]',
        'periodo_tarde'         => 'required|integer|max_length[1]',
        'periodo_noite'         => 'required|integer|max_length[1]',
        'status'                => 'required|string|max_length[1]',
    ];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = [];
	protected $afterInsert          = [];
	protected $beforeUpdate         = ['prepararApontamento'];
	protected $afterUpdate          = ['atualizarApontamento'];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];

    //--------------------------------------------------------------------

    public const STATUS = [
        'A' => 'Ativo',
        'I' => 'Inativo',
        'N' => 'Não frequente',
        'F' => 'Afastado',
    ];

    //--------------------------------------------------------------------

    protected function prepararApontamento($data)
    {
        $this->db->trans_start();

        return $data;
    }

    protected function atualizarApontamento($data)
    {
        if (!$data['result']) {
            $this->db->trans_complete();
            return $data;
        }

        $matriculados = $this->db
            ->select('a.id, a.id_aluno, a.escola, a.id_alocacao, a.turno')
            ->join('cd_alocacao b', "b.id = a.id_alocacao AND DATE_FORMAT(b.data, '%Y-%m') = '" . date('Y-m') . "'")
            ->where('a.id_aluno', $data['data']['id'])
            ->or_where('a.aluno', $data['data']['nome'])
            ->limit(1)
            ->get('cd_matriculados a')
            ->result();

        $periodos = [];
        if (!empty($data['data']['periodo_manha'])) {
            $periodos[] = 'M';
        }
        if (!empty($data['data']['periodo_tarde'])) {
            $periodos[] = 'T';
        }
        if (!empty($data['data']['periodo_noite'])) {
            $periodos[] = 'N';
        }

        foreach ($matriculados as $matriculado) {
            if (in_array($matriculado->turno, $periodos)) {
                $escola = $this->db
                    ->select('nome')
                    ->where('id', $data['data']['id_escola'])
                    ->get('cd_escolas')
                    ->row();

                $data2 = [
                    'id_alocacao' => $matriculado->id_alocacao,
                    'id_aluno' => $id ?? $matriculado->id_aluno,
                    'aluno' => $data['data']['nome'],
                    'escola' => $escola->nome ?? $matriculado->escola,
                    'status' => $data['data']['status'],
                ];

                $this->db->update('cd_matriculados a', $data2, ['a.id' => $matriculado->id]);
            }
        }

        $this->db->trans_complete();

        return $data;
    }
}
