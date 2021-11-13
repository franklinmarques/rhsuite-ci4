<?php

namespace App\Controllers;

use App\Entities\Evento;
use App\Models\EventoModel;
use App\Models\UsuarioModel;
use App\Services\DepartamentoAreaSetorService;
use CodeIgniter\HTTP\ResponseInterface;

class Agenda extends BaseController
{
    public function index(): ResponseInterface
    {
        $data = $this->getEvento(new EventoModel());
        return $this->response->setJSON($data);
    }

    private function getEvento(EventoModel $agendaEventoModel)
    {
        $qb = $agendaEventoModel
            ->select('id, title, description, status, color')
            ->select("date_to AS date, 'meeting' AS type, link AS url", false);
        if (session('tipo') != 'administrador') {
            $qb->where('id_usuario', session('id'))
                ->orWhere('id_usuario_referenciado', session('id'));
        }
        return $qb->find();
    }

    public function filter(): ResponseInterface
    {
        $empresa = session('empresa');
        $idDepto = $this->request->getPost('depto');
        $idArea = $this->request->getPost('area');
        $idUsuarioReferenciado = $this->request->getPost('id_usuario_referenciado');

        $deptoAreaSetorService = new DepartamentoAreaSetorService();
        $areas = ['' => 'selecione...'] + $deptoAreaSetorService->getAreas($idDepto);

        $usuarioModel = new UsuarioModel();
        $qb = $usuarioModel->select('id, nome');
        if (session('tipo') != 'administrador') {
            $qb->where('empresa', $empresa);
        }
        $usuarios = $qb
            ->whereIn('tipo', ['funcionario', 'selecionador'])
            ->whereIn('status', [USUARIO_ATIVO, USUARIO_EM_EXPERIENCIA])
            ->where('id_depto', $idDepto)
            ->where('id_area', $idArea)
            ->orderBy('nome')
            ->asArray()
            ->find();

        $usuarios = ['' => 'selecione...'] + array_column($usuarios, 'nome', 'id');

        $data = [
            'areas' => form_dropdown('', $areas, $idArea),
            'usuarios' => form_dropdown('', $usuarios, $idUsuarioReferenciado),
        ];

        return $this->response->setJSON($data);
    }

    public function insert()
    {
        $agendaEventoModel = new EventoModel();
        $agendaEvento = new Evento($this->request->getPost());
        $agendaEvento->date_from = date('Y-m-d H:i:s');
        $agendaEvento->id_usuario = session('id');
        if (is_null($agendaEvento->id_usuario_referenciado)) {
            $agendaEvento->id_usuario_referenciado = session('id');
        }
        return $agendaEventoModel->insertOrFail($agendaEvento);
    }

    public function finalize()
    {
        $this->validarEvento($agendaEventoModel = new EventoModel());
        return $agendaEventoModel->updateOrFail($this->request->getPost('id'), ['status' => 1]);
    }

    public function delete()
    {
        $this->validarEvento($agendaEventoModel = new EventoModel());
        return $agendaEventoModel->deleteOrFail($this->request->getPost('id'));
    }

    private function validarEvento(EventoModel $agendaEventoModel)
    {
        if (empty($this->getEvento($agendaEventoModel))) {
            if (session('tipo') == 'administrador') {
                $this->response->setStatusCode(400, 'Evento não localizado.');
            } else {
                $this->response->setStatusCode(400, 'Evento não localizado ou sem permissão para alteração.');
            }
        }
    }
}
