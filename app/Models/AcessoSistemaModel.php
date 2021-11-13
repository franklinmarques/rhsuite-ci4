<?php

namespace App\Models;

use App\Entities\AcessoSistema;
use ReflectionException;

class AcessoSistemaModel extends AbstractModel
{
    protected $DBGroup = 'default';
    protected $table = 'acesso_sistema';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = AcessoSistema::class;
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id_usuario',
        'tipo',
        'data_acesso',
        'data_atualizacao',
        'data_saida',
        'endereco_ip',
        'agente_usuario',
        'id_sessao',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'id_usuario' => 'required|integer|max_length[11]',
        'tipo' => 'string|max_length[20]',
        'data_acesso' => 'required|valid_date',
        'data_atualizacao' => 'valid_date',
        'data_saida' => 'valid_date',
        'endereco_ip' => 'string|max_length[45]',
        'agente_usuario' => 'string|max_length[255]',
        'id_sessao' => 'string|max_length[128]',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = ['configurarUsuario'];
    protected $afterInsert          = [];
    protected $beforeUpdate         = [];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

    //--------------------------------------------------------------------

    protected function configurarUsuario($data): array
    {
        if (array_key_exists('data', $data) === false) {
            return $data;
        }

        $data['data']['id_usuario'] = session('id');
        $data['data']['tipo'] = session('tipo');
        $data['data']['endereco_ip'] = $this->input->ip_address();
        $data['data']['agente_usuario'] = $this->input->user_agent();
        $data['data']['id_sessao'] = session_id();

        return $data;
    }

    //--------------------------------------------------------------------

    public function finalizar($data)
    {
        $this->disableUseTimestamps();

        $data['data_saida'] = date('Y-m-d H:i:s');

        try {
            $retorno = $this->update($data);
        } catch (ReflectionException $e) {
            return $e->getMessage();
        }

        $this->disableUseTimestamps(false);

        return $retorno;
    }

    //--------------------------------------------------------------------

    public function detalhes($idLog = null)
    {
        $log = $this->db
            ->select('id')
            ->where('id_usuario', session('id'))
            ->order_by('id', 'desc')
            ->limit(1)
            ->get($this->table)
            ->row();

        $id = $log->id ?? null;

        $tempoLimite = $this->config->item('sess_expiration');

        $case = "CASE WHEN data_saida IS NOT NULL THEN 'finalizado'
                      WHEN DATE_ADD(IFNULL(data_atualizacao, data_acesso), INTERVAL $tempoLimite SECOND)  >= NOW() THEN 'logado'
                      ELSE 'expirado' END";

        $qb = $this->db
            ->select('*')
            ->select("DATE_FORMAT(data_acesso, '%d/%m/%Y &ensp; %H:%i:%s') AS data_hora_acesso", false)
            ->select("DATE_FORMAT(data_atualizacao, '%d/%m/%Y &ensp; %H:%i:%s') AS data_hora_atualizacao", false)
            ->select("DATE_FORMAT(data_saida, '%d/%m/%Y &ensp; %H:%i:%s') AS data_hora_saida", false)
            ->select("($case) AS status", false);
        if ($idLog) {
            $qb->where('id', $idLog);
        }
        $row = $qb
            ->get($this->table)
            ->row();

        $usuario = $this->db
            ->select('nome')
            ->get_where('usuarios', ['id' => $row->id_usuario])
            ->row();

        $row->nome = $usuario->nome ?? '';

        return $row;
    }
}
