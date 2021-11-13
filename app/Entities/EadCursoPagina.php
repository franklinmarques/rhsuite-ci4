<?php

namespace App\Entities;

class EadCursoPagina extends AbstractEntity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [
        'id' => 'int',
        'id_curso' => 'int',
        'ordem' => 'int',
        'modulo' => 'string',
        'titulo' => 'string',
        'conteudo' => '?string',
        'pdf' => '?string',
        'url' => '?string',
        'arquivo_video' => '?string',
        'categoria_biblioteca' => '?int',
        'titulo_biblioteca' => '?string',
        'tags_biblioteca' => '?string',
        'biblioteca' => '?int',
        'audio' => '?string',
        'video' => '?string',
        'autoplay' => 'int',
        'nota_corte' => '?int',
        'id_pagina_aprovacao' => '?int',
        'id_pagina_reprovacao' => '?int',
        'aleatorizacao' => '?string',
        'data_cadastro' => 'datetime',
        'data_editado' => '?datetime',
        'id_copia' => '?int',
    ];
}
