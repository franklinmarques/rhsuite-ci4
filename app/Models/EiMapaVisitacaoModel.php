<?php

namespace App\Models;

use App\Entities\EiMapaVisitacao;

class EiMapaVisitacaoModel extends AbstractModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'ei_mapa_visitacoes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = EiMapaVisitacao::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
        'id_mapa_unidade',
        'tipo_atividade',
        'data_visita',
        'data_visita_anterior',
        'id_supervisor_visitante',
        'supervisor_visitante',
        'cliente',
        'municipio',
        'escola',
        'unidade_visitada',
        'prestadores_servicos_tratados',
        'coordenador_responsavel',
        'motivo_visita',
        'gastos_materiais',
        'sumario_visita',
        'observacoes',
    ];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
        'id_mapa_unidade'               => 'required|is_natural_no_zero|max_length[11]',
        'tipo_atividade'                => 'string|max_length[14]',
        'data_visita'                   => 'required|valid_date',
        'data_visita_anterior'          => 'valid_date',
        'id_supervisor_visitante'       => 'is_natural_no_zero|max_length[11]',
        'supervisor_visitante'          => 'string|max_length[255]',
        'cliente'                       => 'integer|max_length[11]',
        'municipio'                     => 'string|max_length[255]',
        'escola'                        => 'string|max_length[255]',
        'unidade_visitada'              => 'integer|max_length[11]',
        'prestadores_servicos_tratados' => 'string|max_length[255]',
        'coordenador_responsavel'       => 'integer|max_length[11]',
        'motivo_visita'                 => 'integer|max_length[1]',
        'gastos_materiais'              => 'numeric|max_length[10]',
        'sumario_visita'                => 'string',
        'observacoes'                   => 'string',
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

    public const TIPOS_ATIVIDADES = [
        'escola' => 'Visita escola',
        'cliente' => 'Visita cliente',
        'presencial' => 'Reunião presencial',
        'online' => 'Reunião online',
        'administrativa' => 'Atividade administrativa',
        'outra' => 'Outras',
    ];
    public const MOTIVOS_ATIVIDADES = [
        '1' => 'Visita realizada',
        '2' => 'Visita programada',
        '3' => 'Solicitação da unidade',
        '4' => 'Solicitação de materiais',
        '5' => 'Processo seletivo',
        '6' => 'Ocorrência com aluno',
        '7' => 'Ocorrência com funcionário',
        '8' => 'Ocorrência na escola',
        '9' => 'Atividades administrativas',
    ];
}
