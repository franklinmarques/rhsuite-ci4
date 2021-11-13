<?php

namespace App\Models;

use App\Entities\EiLivroAta;

class EiLivroAtaModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_livros_ata';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiLivroAta::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_usuario_frequencia',
        'id_usuario',
        'data',
        'periodo',
        'periodo_relatorio',
        'data_inicio_periodo',
        'data_termino_periodo',
        'profissional',
        'alunos',
        'curso',
        'modulo',
        'escola',
        'atividades_realizadas',
        'dificuldades_encontradas',
        'sugestoes_observacoes',
        'id_alocado',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_usuario_frequencia'     => 'integer|max_length[11]',
        'id_usuario'                => 'required|is_natural_no_zero|max_length[11]',
        'data'                      => 'required|valid_date',
        'periodo'                   => 'integer|exact_length[1]',
        'periodo_relatorio'         => 'string|max_length[255]',
        'data_inicio_periodo'       => 'valid_date',
        'data_termino_periodo'      => 'valid_date',
        'profissional'              => 'string|max_length[255]',
        'alunos'                    => 'string|max_length[255]',
        'curso'                     => 'string|max_length[255]',
        'modulo'                    => 'string|max_length[255]',
        'escola'                    => 'string|max_length[255]',
        'atividades_realizadas'     => 'string',
        'dificuldades_encontradas'  => 'string',
        'sugestoes_observacoes'     => 'string',
        'id_alocado'                => 'is_natural_no_zero|max_length[11]',
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

    //--------------------------------------------------------------------

    public const PERIODOS = [
        '0' => 'Madrugada',
        '1' => 'Manhã',
        '2' => 'Tarde',
        '3' => 'Noite',
    ];
}
