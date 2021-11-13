<?php

namespace App\Controllers\Ei;

use App\Controllers\BaseController;
use App\Entities\EiInsumo;
use App\Models\EiInsumoModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class Insumos extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        if (!in_array($this->session->userdata('nivel'), [
            0,
            NIVEL_ACESSO_COLABORADOR_CLT,
            NIVEL_ACESSO_PRESIDENTE,
            NIVEL_ACESSO_GERENTE,
            NIVEL_ACESSO_COORDENADOR,
            NIVEL_ACESSO_SUPERVISOR,
        ])) {
            redirect(site_url('home'));
        }
        $this->load->model('ei_insumo_model', 'insumo');
    }

    //--------------------------------------------------------------------

    public function index(): string
    {
        return view('ei/insumos', ['empresa' => session('empresa')]);
    }

    //--------------------------------------------------------------------

    public function list()
    {
        $sql = $this->db
            ->select('nome, tipo, id')
            ->where('id_empresa', $this->session->userdata('empresa'))
            ->get_compiled_select($this->insumo->getTable());

        $this->load->library('dataTables');

        $output = $this->datatables->query($sql);

        $data = [];

        foreach ($output->data as $row) {
            $data[] = [
                $row->nome,
                $row->tipo,
                '<button class="btn btn-sm btn-info" onclick="edit_insumo(' . $row->id . ')" title="Editar insumo"><i class="glyphicon glyphicon-pencil"></i></button>
                 <button class="btn btn-sm btn-danger" onclick="delete_insumo(' . $row->id . ')" title="Excluir insumo"><i class="glyphicon glyphicon-trash"></i></button>',
            ];
        }

        $output->data = $data;

        echo json_encode($output);
    }

    //--------------------------------------------------------------------

    public function edit(): ResponseInterface
    {
        $data = (new EiInsumoModel)->findOneOrFail($this->request->getPost('id'));
        return $this->response->setJSON($data);
    }

    //--------------------------------------------------------------------

    /**
     * @throws ReflectionException
     */
    public function insert()
    {
        return (new EiInsumoModel)->insertOrFail(new EiInsumo($this->request->getPost()));
    }

    //--------------------------------------------------------------------

    /**
     * @throws ReflectionException
     */
    public function update()
    {
        $data = new EiInsumo($this->request->getPost());
        return (new EiInsumoModel)->updateOrFail($data->id, $data);
    }

    //--------------------------------------------------------------------

    public function delete()
    {
        return (new EiInsumoModel)->deleteOrFail($this->request->getPost('id'));
    }

}
