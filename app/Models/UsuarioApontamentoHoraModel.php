<?php

namespace App\Models;

use App\Entities\UsuarioApontamentoHora;

class UsuarioApontamentoHoraModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'usuarios_apontamentos_horas';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = UsuarioApontamentoHora::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_usuario',
        'data_hora',
        'turno_evento',
        'latitude',
        'longitude',
        'saldo_horas',
        'banco_horas',
        'descontos_folha',
        'modo_cadastramento',
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
        'data_hora'             => 'required|valid_date',
        'turno_evento'          => 'required|string|max_length[1]',
        'latitude'              => 'numeric|max_length[7]',
        'longitude'             => 'numeric|max_length[7]',
        'saldo_horas'           => 'valid_time',
        'banco_horas'           => 'valid_time',
        'descontos_folha'       => 'valid_time',
        'modo_cadastramento'    => 'string|max_length[1]',
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
        'H' => 'Folga',
        'X' => 'Hora extra',
        'C' => 'Compensação',
        'F' => 'Falta S/A',
        'J' => 'Falta C/A',
        'A' => 'Falta A/C',
        'E' => 'Entrada',
        'S' => 'Saída',
    ];
    public const MODOS_CADASTRAMENTO = [
        'A' => 'Automático',
        'M' => 'Manual',
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
}
