<?php

namespace App\Models;

use App\Entities\UsuarioAfastamento;

class UsuarioAfastamentoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'usuarios_afastamentos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = UsuarioAfastamento::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_usuario',
        'id_empresa',
        'data_afastamento',
        'motivo_afastamento',
        'motivo_afastamento_bck',
        'data_pericia_medica',
        'data_limite_beneficio',
        'data_retorno',
        'historico_afastamento',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_usuario'                => 'required|is_natural_no_zero|max_length[11]',
        'id_empresa'                => 'required|is_natural_no_zero|max_length[11]',
        'data_afastamento'          => 'required|valid_date',
        'motivo_afastamento'        => 'integer|max_length[1]',
        'motivo_afastamento_bck'    => 'string|max_length[255]',
        'data_pericia_medica'       => 'valid_date',
        'data_limite_beneficio'     => 'valid_date',
        'data_retorno'              => 'valid_date',
        'historico_afastamento'     => 'string',
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

    //--------------------------------------------------------------------

    public const MOTIVOS_AFASTAMENTO = [
        '1' => 'Afastado (auxílio doença - INSS)',
        '2' => 'Afastado (maternidade)',
        '3' => 'Afastado (acidente)',
        '4' => 'Afastado (aposentadoria/invalidez)',
        '5' => 'Afastado (auxílio doença - atestado)',
    ];

    //--------------------------------------------------------------------

    public function atualizarStatusUsuario($data)
    {
        if ($data['id'] or $data['data']['id_usuario']) {
            return $data;
        }

        $idUsuario = $data['data']['id_usuario'];

        $this->load->model('usuario_model', 'usuario');

        $status = $this->statusUsuario();

        $this->usuario->update($idUsuario, ['status' => $status[$data['data']['motivo_afastamento']] ?? '5']);

        return $data;
    }

    //--------------------------------------------------------------------

    public function statusUsuario($value = null)
    {
        if ($this->load->is_loaded('usuario_model') == false) {
            $this->load->model('usuario_model', 'usuario');
        }

        $retorno = [];
        foreach ($this->usuario::STATUS as $i => $v) {
            if (in_array($v, self::MOTIVOS_AFASTAMENTO)) {
                $retorno[array_search($v, self::MOTIVOS_AFASTAMENTO)] = $i;
            }
        }

        return is_null($value) ? $retorno : ($retorno[$value] ?? null);
    }
}
