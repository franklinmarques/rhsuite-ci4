<?php

namespace App\Controllers;

class Meu_perfil extends BaseController
{
    public function index(): string
    {
        $data = auth()->user();
        return view('meu_perfil', $data);
    }

    public function save()
    {
        $data = auth()->user();
        return $data->saveOrFail();
    }
}