<?php

namespace App\Models;

use App\Entities\Auth;
use CodeIgniter\Model;
use Exception;

class AuthModel extends Model
{
    protected $DBGroup = 'app';
    protected $table = 'auths';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = Auth::class;
    protected $allowedFields        = [
        'nome',
        'email',
        'senha',
        'token',
        'ativo',
        'email_verificado_em',
        'db_tenant',
    ];

    // Validation
    protected $validationRules  = [
        'nome'                  => 'required|string|max_length[255]',
        'email'                 => 'required|valid_email',
        'senha'                 => 'required|string|max_length[255]',
        'token'                 => 'string|max_length[255]',
        'ativo'                 => 'integer|exact_length[1]',
        'email_verificado_em'   => 'required|valid_date',
        'db_tenant'             => 'integer|exact_length[1]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $beforeInsert = ['beforeInsertError'];
    protected $beforeUpdate = ['beforeUpdateError'];
    protected $beforeDelete = ['beforeDeleteError'];

    /**
     * @throws Exception
     */
    protected function beforeInsertError($data)
    {
        throw new Exception('Não é possível inserir dados em uma view.');
    }

    /**
     * @throws Exception
     */
    protected function beforeUpdateError($data)
    {
        throw new Exception('Não é possível alterar dados em uma view.');
    }

    /**
     * @throws Exception
     */
    protected function beforeDeleteError($data)
    {
        throw new Exception('Não é possível excluir dados em uma view.');
    }

    public static function refreshAllUsers()
    {
        db_connect()->query('CALL getAllUsers()');
    }
}
