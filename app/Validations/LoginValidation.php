<?php

namespace App\Validations;

class LoginValidation
{

    public static function getRules(): array
    {
        return [
            'email' => [
                'label' => 'e-mail',
                'rules' => ['required','valid_email', self::teste()]
            ],
            'senha' => [
                'label' => 'senha',
                'rules' => 'required'
            ],
        ];
    }

    public static function getLabels(): array
    {
        return [
            'email' => 'E-mail',
            'senha' => 'Senha',
        ];
    }

    public static function getMessages(): array
    {
        return ['teste'=> 'aaaaaaaaaa'];
    }

    public static function teste() {
        return false;
    }


}
