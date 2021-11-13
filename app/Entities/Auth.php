<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Auth extends Entity
{
    protected $datamap = [];
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $casts = [
        'nome' => 'string',
        'email' => 'string',
        'senha' => 'string',
        'token' => 'string',
        'ativo' => 'int',
        'email_verificado_em' => '?datetime',
        'db_tenant' => '?string',
    ];

    public function passwordVerify(string $humanPassword): bool
    {
        if (password_verify($this->attributes['senha'], $humanPassword)) {
            return true;
        }
        return $this->getLegacyEncryptionPassword($humanPassword);
    }

    private function getLegacyEncryptionPassword($humanPassword): bool
    {
        return $this->attributes['senha'] === md5('@#d13g0tr1nd4d3!' . $humanPassword);
    }

    public function setPassword(string $humanPassword)
    {
        $this->attributes['senha'] = password_hash($humanPassword, PASSWORD_BCRYPT, ['cost' => 10]);
    }
}
