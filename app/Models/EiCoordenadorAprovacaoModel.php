<?php

namespace App\Models;

use App\Entities\EiCoordenadorAprovacao;

class EiCoordenadorAprovacaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_coordenadores_aprovacoes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiCoordenadorAprovacao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'id_aprovador',
        'ano_referencia',
        'semestre_referencia',
        'mes_referencia',
        'cliente_aprovacao',
        'depto_aprovacao',
        'cargo_aprovacao',
        'data_liberacao',
        'arquivo_assinatura_aprovacao',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_empresa'                    => 'required|is_natural_no_zero|max_length[11]',
        'id_aprovador'                  => 'required|is_natural_no_zero|max_length[11]',
        'ano_referencia'                => 'required|int|max_length[4]',
        'semestre_referencia'           => 'required|integer|exact_length[1]',
        'mes_referencia'                => 'required|integer|max_length[2]',
        'cliente_aprovacao'             => 'string|max_length[255]',
        'depto_aprovacao'               => 'string|max_length[255]',
        'cargo_aprovacao'               => 'string|max_length[255]',
        'data_liberacao'                => 'required|valid_date',
        'arquivo_assinatura_aprovacao'  => 'string|max_length[255]',
    ];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = ['referenciarAlocados'];
	protected $afterInsert          = [];
	protected $beforeUpdate         = ['referenciarAlocados'];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];

    //--------------------------------------------------------------------

    protected $uploadConfig = [
        'arquivo_assinatura_aprovacao' => ['upload_path' => './arquivos/ei/assinatura_digital/', 'allowed_types' => 'gif|jpg|jpeg|png'],
    ];

    //--------------------------------------------------------------------

    protected function referenciarAlocados($data)
    {
        if (is_null($data['data'])) {
            return $data;
        }

        $ids = $data['id'] ?? $this->getInsertID();
        if (is_string($ids)) {
            $ids = [$ids];
        }

        $usuario = $this->db
            ->select('email')
            ->where('id', $data['data']['id_aprovador'])
            ->get('usuarios')
            ->row();

        $escolas = $this->db
            ->select('a.id')
            ->select(["CONCAT(a.codigo, ' - ', a.nome) AS nome"], false)
            ->join('ei_diretorias b', 'b.id = a.id_diretoria')
            ->where('b.id_empresa', session('empresa'))
            ->group_start()
            ->where('b.email_coordenador', $usuario->email)
            ->or_where('b.email_administrativo', $usuario->email)
            ->or_where('b.email_supervisor', $usuario->email)
            ->group_end()
            ->get('ei_escolas a')
            ->result_array();

        $alocados = $this->db
            ->select('a.id')
            ->join('ei_alocacoes_escolas b', 'b.id = a.id_alocacao_escola')
            ->join('ei_alocacoes c', 'c.id = b.id_alocacao')
            ->where('c.id_diretoria')
            ->where('c.id_empresa', session('empresa'))
            ->where('c.ano', $data['data']['ano_referencia'])
            ->where('c.semestre', $data['data']['semestre_referencia'])
            ->where_in('b.id_escola', array_keys($escolas) + [0])
            ->get('ei_alocados a')
            ->result_array();

        foreach ($ids as $id) {
            $this->db
                ->set('id_aprovacao_coordenador', $id)
                ->where_in('id_alocado', array_column($alocados, 'id') + [0])
                ->where('mes_referencia', $data['data']['mes_referencia'])
                ->where('status_aprovacao_cps', 2)
                ->update('ei_alocados_aprovacoes');
        }

        return $data;
    }
}
