<?php

namespace App\Models;

use App\Entities\UsuarioApontamentoHora2;

class UsuarioApontamentoHora2Model extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'usuarios_apontamentos_horas_2';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = UsuarioApontamentoHora2::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_usuario',
        'id_old',
        'data_hora',
        'turno_evento',
        'numero_turno',
        'data_hora_entrada',
        'tipo_evento_entrada',
        'data_hora_saida',
        'tipo_evento_saida',
        'latitude',
        'longitude',
        'saldo_horas',
        'saldo_horas_2',
        'banco_horas',
        'descontos_folha',
        'modo_automatico',
        'entrada_automatica',
        'saida_automatica',
        'id_depto',
        'id_area',
        'id_setor',
        'justificativa',
        'aceite_justificativa',
        'data_aceite',
        'observacoes_aceite',
        'id_usuario_aceite',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_usuario'            => 'required|is_natural_no_zero|max_length[11]',
        'id_old'                => 'integer|max_length[11]',
        'data_hora'             => 'required|valid_date',
        'turno_evento'          => 'required|string|max_length[2]',
        'numero_turno'          => 'integer|max_length[1]',
        'data_hora_entrada'     => 'required|valid_date',
        'tipo_evento_entrada'   => 'required|string|max_length[2]',
        'data_hora_saida'       => 'valid_date',
        'tipo_evento_saida'     => 'string|max_length[2]',
        'latitude'              => 'numeric|max_length[7]',
        'longitude'             => 'numeric|max_length[7]',
        'saldo_horas'           => 'valid_time',
        'saldo_horas_2'         => 'valid_time',
        'banco_horas'           => 'valid_time',
        'descontos_folha'       => 'valid_time',
        'modo_automatico'       => 'integer|exact_length[1]',
        'entrada_automatica'    => 'integer|exact_length[1]',
        'saida_automatica'      => 'integer|exact_length[1]',
        'id_depto'              => 'is_natural_no_zero|max_length[11]',
        'id_area'               => 'is_natural_no_zero|max_length[11]',
        'id_setor'              => 'is_natural_no_zero|max_length[11]',
        'justificativa'         => 'string',
        'aceite_justificativa'  => 'string|max_length[1]',
        'data_aceite'           => 'valid_date',
        'observacoes_aceite'    => 'string',
        'id_usuario_aceite'     => 'integer|max_length[11]',
    ];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = [];
	protected $afterInsert          = [];
	protected $beforeUpdate         = ['registrarAceite'];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];

    //--------------------------------------------------------------------

    public const TURNOS_EVENTO = [
        'N' => 'Presença normal',
        'FO' => 'Folga', #H
        'X' => 'Hora extra',//X
        'C' => 'Compensação',//C
        'F' => 'Falta S/A',//F
        'J' => 'Falta C/A',//J
        'A' => 'Falta A/C',//A
        'B' => 'Falta Abonada',//B
        'E' => 'Entrada',
        'EE' => 'Entrada Especial',
        'EF' => 'Entrada Fracionada',
        'EX' => 'Entrada Hora Extra',
        'S' => 'Saída',
        'SE' => 'Saída Especial',
        'SF' => 'Saída Fracionada',
        'SX' => 'Saída Hora Extra',
        'FR' => 'Férias',
        'DL' => 'Desligamento',
    ];
    public const NUMEROS_TURNO = [
        '1' => 'Manhã',
        '2' => 'Tarde',
        '3' => 'Noite',
    ];
    public const ACEITES_JUSTIFICATIVA = [
        'A' => 'Aceita',
        'N' => 'Não aceita',
    ];

    //--------------------------------------------------------------------

    protected function registrarAceite($data): array
    {
        if (empty($data['data'])) {
            return $data;
        }

        $data['data']['data_aceite'] = date('Y-m-d H:i:s');
        $data['data']['id_usuario_aceite'] = session('id');

        return $data;
    }

    //--------------------------------------------------------------------

    public function insertByOld($ids, $post)
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        $data = [
            'id_usuario' => $post['id_alocado'],
            'id_old' => 'is_natural_no_zero|max_length[11]',
            'data_hora' => 'required|valid_date',
            'turno_evento' => 'required|in_list[C,F,E,S]',
            'data_hora_entrada' => 'required|valid_date',
            'tipo_evento_entrada' => 'required|exact_length[2]',
            'data_hora_saida' => 'valid_date',
            'tipo_evento_saida' => 'exact_length[2]',
            'latitude' => 'max_length[9]',
            'longitude' => 'max_length[9]',
            'saldo_horas' => 'valid_time',
            'banco_horas' => 'valid_time',
            'descontos_folha' => 'valid_time',
            'modo_automatico' => 'is_natural_no_zero|less_than_equal_to[1]',
            'id_depto' => 'is_natural_no_zero|max_length[11]',
            'id_area' => 'is_natural_no_zero|max_length[11]',
            'id_setor' => 'is_natural_no_zero|max_length[11]',
            'justificativa' => 'max_length[65535]',
            'aceite_justificativa' => 'in_list[A,N]',
            'data_aceite' => 'valid_datetime',
            'observacoes_aceite' => 'max_length[65535]',
            'id_usuario_aceite' => 'is_natural_no_zero|max_length[11]',
        ];

        foreach ($ids as $id) {
            $data['id_old'] = $id;

            $this->db->insert('usuarios_apontamentos_horas_2', $data);
        }
    }

    //--------------------------------------------------------------------

    public function updateByOld($id, $post)
    {
        if (!is_array($id)) {
            $id = [$id];
        }
        $data = [
            'id_alocado' => $post['id_alocado'],
            'tipo_entrada' => $post['tipo_evento_entrada'] ?? $post['tipo_evento'],
            'entrada_automatica' => $post['modo_acesso'] === 'A' ? 1 : null,
            'data_entrada' => $post['data'],
            'hora_entrada' => $post['horario_entrada'] ?? null,
            'desconto_folha_entrada' => $post['desconto_folha'] ?? null,
            'saldo_horas_entrada' => $post['saldo_banco_horas'] ?? null,
            'tipo_saida' => $post['tipo_evento_saida'] ?? null,
            'saida_automatica' => $post['modo_acesso'] === 'A' ? 1 : null,
            'data_saida' => !empty($post['hora_saida']) ? $post['data'] : null,
            'hora_saida' => $post['hora_saida'] ?? null,
            'desconto_folha_saida' => $post['desconto_folha_saida'] ?? null,
            'saldo_horas_saida' => $post['saida_banco_horas'] ?? null,
            'horas_diarias' => $post['qtde_horas_diarias'] ?? null,
            'observacoes' => $post['observaoces'] ?? null
        ];

        $this->db
            ->set($data)
            ->where_in('id_old', $id)
            ->update('usuarios_apontamentos_horas_2');
    }

    //--------------------------------------------------------------------

    public function deleteByOld($id)
    {
        if (!is_array($id)) {
            $id = [$id];
        }

        $this->db
            ->where_in('id_old', $id)
            ->delete('usuarios_apontamentos_horas_2');
    }
}
