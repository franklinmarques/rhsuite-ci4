<?php

namespace App\Models;

use App\Entities\IcomPosto;

class IcomPostoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'icom_postos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = IcomPosto::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_setor',
        'id_supervisor',
        'id_usuario',
        'id_funcao',
        'categoria',
        'matricula',
        'endereco_ip1',
        'endereco_ip2',
        'valor_hora_mei',
        'qtde_horas_mei',
        'qtde_horas_dia_mei',
        'valor_mes_clt',
        'qtde_meses_clt',
        'qtde_horas_dia_clt',
        'dia_semana',
        'horario_entrada',
        'horario_intervalo',
        'horario_retorno',
        'horario_saida',
        'horas_dia',
        'minutos_descanso_dia',
        'dia_semana_extra_1',
        'horario_entrada_extra_1',
        'horario_intervalo_extra_1',
        'horario_retorno_extra_1',
        'horario_saida_extra_1',
        'horas_dia_extra_1',
        'minutos_descanso_dia_extra_1',
        'dia_semana_extra_2',
        'horario_entrada_extra_2',
        'horario_intervalo_extra_2',
        'horario_retorno_extra_2',
        'horario_saida_extra_2',
        'horas_dia_extra_2',
        'minutos_descanso_dia_extra_2',
        'data_edicao',
        'tipo_ultimo_evento',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_setor'                      => 'required|is_natural_no_zero|max_length[11]',
        'id_supervisor'                 => 'is_natural_no_zero|max_length[11]',
        'id_usuario'                    => 'required|is_natural_no_zero|max_length[11]',
        'id_funcao'                     => 'required|is_natural_no_zero|max_length[11]',
        'categoria'                     => 'required|string|max_length[3]',
        'matricula'                     => 'integer|max_length[11]',
        'endereco_ip1'                  => 'string|max_length[255]',
        'endereco_ip2'                  => 'string|max_length[255]',
        'valor_hora_mei'                => 'numeric|max_length[10]',
        'qtde_horas_mei'                => 'valid_time',
        'qtde_horas_dia_mei'            => 'valid_time',
        'valor_mes_clt'                 => 'numeric|max_length[10]',
        'qtde_meses_clt'                => 'valid_time',
        'qtde_horas_dia_clt'            => 'valid_time',
        'dia_semana'                    => 'string|max_length[17]',
        'horario_entrada'               => 'valid_time',
        'horario_intervalo'             => 'valid_time',
        'horario_retorno'               => 'valid_time',
        'horario_saida'                 => 'valid_time',
        'horas_dia'                     => 'valid_time',
        'minutos_descanso_dia'          => 'valid_time',
        'dia_semana_extra_1'            => 'string|max_length[17]',
        'horario_entrada_extra_1'       => 'valid_time',
        'horario_intervalo_extra_1'     => 'valid_time',
        'horario_retorno_extra_1'       => 'valid_time',
        'horario_saida_extra_1'         => 'valid_time',
        'horas_dia_extra_1'             => 'valid_time',
        'minutos_descanso_dia_extra_1'  => 'valid_time',
        'dia_semana_extra_2'            => 'string|max_length[17]',
        'horario_entrada_extra_2'       => 'valid_time',
        'horario_intervalo_extra_2'     => 'valid_time',
        'horario_retorno_extra_2'       => 'valid_time',
        'horario_saida_extra_2'         => 'valid_time',
        'horas_dia_extra_2'             => 'valid_time',
        'minutos_descanso_dia_extra_2'  => 'valid_time',
        'data_edicao'                   => 'valid_date',
        'tipo_ultimo_evento'            => 'string|max_length[1]',
    ];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = ['atualizarDataEdicao'];
	protected $afterInsert          = ['atualizarEnderecoIp'];
	protected $beforeUpdate         = ['atualizarDataEdicao'];
	protected $afterUpdate          = ['atualizarEnderecoIp'];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];

    //--------------------------------------------------------------------

    public const CATEGORIAS = [
        'CLT' => 'CLT',
        'MEI' => 'MEI',
    ];
    public const DIAS_SEMANA = [
        '' => 'Período normal',
        '1' => 'Domingo',
        '2' => 'Segunda-feira',
        '3' => 'Terça-feira',
        '4' => 'Quarta-feira',
        '5' => 'Quinta-feira',
        '6' => 'Sexta-feira',
        '7' => 'Sábado',
        'F' => 'Feriado',
        'E' => 'Emenda de feriado',
    ];
    public const DIAS_SEMANA_EXTRA_1 = [
        '1' => 'Domingo',
        '2' => 'Segunda-feira',
        '3' => 'Terça-feira',
        '4' => 'Quarta-feira',
        '5' => 'Quinta-feira',
        '6' => 'Sexta-feira',
        '7' => 'Sábado',
        'F' => 'Feriado',
        'E' => 'Emenda de feriado',
    ];
    public const DIAS_SEMANA_EXTRA_2 = [
        '1' => 'Domingo',
        '2' => 'Segunda-feira',
        '3' => 'Terça-feira',
        '4' => 'Quarta-feira',
        '5' => 'Quinta-feira',
        '6' => 'Sexta-feira',
        '7' => 'Sábado',
        'F' => 'Feriado',
        'E' => 'Emenda de feriado',
    ];

    //--------------------------------------------------------------------

    protected function atualizarDataEdicao($data)
    {
        if (array_key_exists('data', $data) == false) {
            return $data;
        }

        $data['data']['data_edicao'] = date('Y-m-d');

        return $data;
    }

    //--------------------------------------------------------------------

    protected function atualizarEnderecoIp($data)
    {
        if (!$data['result'] or array_key_exists('data', $data) == false) {
            return $data;
        }

        $this->db
            ->set('endereco_ip1', $data['data']['endereco_ip1'])
            ->set('endereco_ip2', $data['data']['endereco_ip2'])
            ->where('id', $data['data']['id_usuario'])
            ->update('usuarios');

        return $data;
    }
}
