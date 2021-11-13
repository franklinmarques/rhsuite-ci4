<?php

namespace App\Controllers;

use App\Models\AuthModel;
use App\Models\UsuarioModel;
use App\Validations\LoginValidation;

class Login extends BaseController
{
    public function index(): string
    {
        $data = [
            'nome' => getenv('app.name'),
            'email' => getenv('app.mail'),
            'logoempresa' => '',
            'logo' => '',
            'cabecalho' => '',
            'imagem_fundo' => '',
            'video_fundo' => '',
            'visualizacao_pilula_conhecimento' => [],
            'area_conhecimento' => [],
            'tema' => '',
        ];
        return view('login', $data);
    }

    public function autenticar()
    {
        $email = $this->request->getPost('email');
        $senha = $this->request->getPost('senha');

        $authModel = new AuthModel();

        if (!$authModel->validate(['email' => $email, 'senha' => $senha])) {
            return $this->response->setStatusCode(400, 'Bad Request')
                ->setJSON(['message' => $authModel->errors()]);
        }

        $auth = $authModel->where('email', $email)->first();
        if (!$auth or !$auth->passwordVerify($senha) or !$auth->email_verificado_em) {
            return $this->response->setStatusCode(401, 'Unauthorized')
                ->setJSON(['message' => 'E-mail ou senha inválidos.']);
        }

        if (!$auth->ativo) {
            return $this->response->setStatusCode(401, 'Unauthorized')
                ->setJSON(['message' => 'Perfil inativo, contate o seu administrador.']);
        }

        if ($auth->db_tenant != db_connect()->getDatabase()) {
            db_connect()->setDatabase($auth->db_tenant);
        }

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->where('email', $auth->email)->first();
//        dd($usuario->empresa);


        $usuario = $auth->getUser();

        session()->set([
            'db' => $auth->db_tenant,
            'id' => $usuario->id,
            'nome' => $usuario->nome,
            'url' => $usuario->url,
            'foto' => $usuario->foto,
            'cabecalho' => $usuario->cabecalho,
        ]);


        if (!$this->validate(LoginValidation::getRules())) {
            return json_encode($this->validator->getErrors());
        }

        $credentials = [
            'email' => $this->request->getPost('email'),
            'senha' => $this->request->getPost('senha'),
        ];
        if (auth()->attempt($credentials) == false) {
            return json_encode(['erro' => 'E-mail ou senha inválidos.']);
        }
        return json_encode('success');
    }

    public function reset_password()
    {

    }
}
