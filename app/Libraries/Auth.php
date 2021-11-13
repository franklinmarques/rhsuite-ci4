<?php

namespace App\Libraries;

use App\Entities\Usuario;
use App\Models\UsuarioModel;

class Auth
{
    public function attempt($data) {
        return true;
    }

    public function login(string $email, string $password): bool
    {
        if (!($user = $this->user(['email' => $email, 'senha' => $password]))) {
            return false;
        }
        $this->createSession($user);
        return true;
    }

    public function user(array $credentials)
    {
        return (new UsuarioModel)->where($credentials)->first();
    }

    public function createSession(Usuario $user)
    {
        $tipo = $user->getTipo();
        session()->set($tipo->toArray());
    }

    public function logout()
    {
        session_destroy();
        redirect()->to(site_url('login'));
    }

}
