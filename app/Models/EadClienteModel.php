<?php

namespace App\Models;

use App\Entities\EadCliente;

class EadClienteModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ead_clientes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EadCliente::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_empresa',
        'nome',
        'cliente',
        'email',
        'senha',
        'token',
        'foto',
        'data_cadastro',
        'data_edicao',
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
        'id_empresa'    => 'required|is_natural_no_zero|max_length[11]',
        'nome'          => 'required|string|max_length[255]',
        'cliente'       => 'required|string|max_length[255]',
        'email'         => 'required|string|max_length[255]',
        'senha'         => 'required|string|max_length[32]',
        'token'         => 'required|string|max_length[255]',
        'foto'          => 'string|max_length[255]',
        'data_cadastro' => 'required|valid_date',
        'data_edicao'   => 'valid_date',
        'status'        => 'required|integer|max_length[2]',
    ];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = ['encriptarSenha', 'gerarToken'];
	protected $afterInsert          = [];
	protected $beforeUpdate         = ['encriptarSenha'];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];

    //--------------------------------------------------------------------

    protected $uploadConfig = ['foto' => ['upload_path' => './imagens/usuarios/', 'allowed_types' => 'gif|jpg|png']];

    public const STATUS = [
        '1' => 'Ativo',
        '0' => 'Inativo',
    ];

    //--------------------------------------------------------------------

    protected function encriptarSenha(array $data): array
    {
        if (array_key_exists('senha', $data['data'] ?? []) === false) {
            return $data;
        }

        if (strlen($data['data']['senha']) > 0) {
            if ($this->load->is_loaded('Auth') == false) {
                $this->load->library('Auth');
            }

            $data['data']['senha'] = $this->auth->encryptPassword($data['data']['senha']);
        } else {
            unset($data['data']['senha']);
        }

        return $data;
    }

    //--------------------------------------------------------------------

    protected function gerarToken(array $data): array
    {
        if (array_key_exists('data', $data) == false) {
            return $data;
        }

        $data['data']['token'] = uniqid();

        return $data;
    }
}
