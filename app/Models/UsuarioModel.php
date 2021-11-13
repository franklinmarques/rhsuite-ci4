<?php

namespace App\Models;

use App\Entities\Usuario;

class UsuarioModel extends AbstractModel
{
	protected $table                = 'usuarios';
	protected $returnType           = Usuario::class;
	protected $useSoftDeletes       = false;
	protected $allowedFields        = [
        'nome',
        'tipo',
        'email',
        'senha',
        'token',
        'email_anterior',
        'email_verificado_em',
        'ativo',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'nome'                  => 'required|string|max_length[255]',
        'tipo'                  => 'required|string|max_length[20]',
        'email'                 => 'required|string|max_length[255]|is_unique[usuarios.email,id,{id}]',
        'senha'                 => 'required|string|max_length[32]',
        'token'                 => 'required|string|max_length[255]',
        'email_anterior'        => 'string|max_length[255]',
        'email_verificado_em'   => 'valid_date',
        'ativo'                 => 'integer|exact_length[1]',
    ];

	// Callbacks
	protected $beforeInsert         = ['encriptarSenha', 'gerarToken'];
	protected $beforeUpdate         = ['encriptarSenha'];

    //--------------------------------------------------------------------

    public const TIPOS = [
        'administrador' => 'administrador',
        'empresa' => 'empresa',
        'funcionario' => 'funcionario',
    ];

    //--------------------------------------------------------------------

    protected function encriptarSenha($data): array
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

    protected function gerarToken($data)
    {
        if (array_key_exists('data', $data) == false) {
            return $data;
        }

        $data['data']['token'] = uniqid();

        return $data;
    }
}
