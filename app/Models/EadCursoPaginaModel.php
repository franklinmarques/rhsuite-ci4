<?php

namespace App\Models;

use App\Entities\EadCursoPagina;

class EadCursoPaginaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ead_cursos_paginas';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EadCursoPagina::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_curso',
        'ordem',
        'modulo',
        'titulo',
        'conteudo',
        'pdf',
        'url',
        'arquivo_video',
        'categoria_biblioteca',
        'titulo_biblioteca',
        'tags_biblioteca',
        'biblioteca',
        'audio',
        'video',
        'autoplay',
        'nota_corte',
        'id_pagina_aprovacao',
        'id_pagina_reprovacao',
        'aleatorizacao',
        'data_cadastro',
        'data_editado',
        'id_copia',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_curso'              => 'required|is_natural_no_zero|max_length[11]',
        'ordem'                 => 'required|integer|max_length[11]',
        'modulo'                => 'required|string|max_length[20]',
        'titulo'                => 'required|string|max_length[255]',
        'conteudo'              => 'string',
        'pdf'                   => 'string|max_length[255]',
        'url'                   => 'string|max_length[255]',
        'arquivo_video'         => 'string|max_length[255]',
        'categoria_biblioteca'  => 'integer|max_length[11]',
        'titulo_biblioteca'     => 'string|max_length[255]',
        'tags_biblioteca'       => 'string|max_length[255]',
        'biblioteca'            => 'integer|max_length[11]',
        'audio'                 => 'string|max_length[255]',
        'video'                 => 'string|max_length[255]',
        'autoplay'              => 'required|integer|max_length[1]',
        'nota_corte'            => 'integer|max_length[3]',
        'id_pagina_aprovacao'   => 'is_natural_no_zero|max_length[11]',
        'id_pagina_reprovacao'  => 'is_natural_no_zero|max_length[11]',
        'aleatorizacao'         => 'string|max_length[1]',
        'data_cadastro'         => 'required|valid_date',
        'data_editado'          => 'valid_date',
        'id_copia'              => 'is_natural_no_zero|max_length[11]',
    ];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = [];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];
}
