<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Empresa extends Entity
{
    protected $datamap = [];
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $casts = [
        'id_usuario' => 'int',
        'nome' => 'string',
        'url' => 'string',
        'foto' => 'string',
        'foto_descricao' => '?string',
        'cabecalho' => '?string',
        'imagem_inicial' => 'string',
        'tipo_tela_inicial' => 'int',
        'imagem_fundo' => '?string',
        'video_fundo' => '?string',
        'assinatura_digital' => '?string',
        'hash_acesso' => '?string',
        'max_colaboradores' => '?int',
        'visualizacao_pilula_conhecimento' => '?bool',
        'visualizacao_rodape' => '?bool',
        'status' => '?bool',
    ];
}
