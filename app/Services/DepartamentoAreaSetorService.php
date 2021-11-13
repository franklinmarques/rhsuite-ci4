<?php

namespace App\Services;

class DepartamentoAreaSetorService extends AbstractService
{
    public function getDeptos(): array
    {
        $qb = $this->db
            ->table('empresa_departamentos')
            ->select('id, nome');
        if ($this->empresa) {
            $qb->where('id_empresa', $this->empresa);
        }
        $departamentos = $qb
            ->orderBy('nome')
            ->get()
            ->getResultArray();

        return array_column($departamentos, 'nome', 'id');
    }

    public function getAreas($idDepto = ''): array
    {
        $qb = $this->db
            ->table('empresa_areas a')
            ->select('a.id, a.nome')
            ->join('empresa_departamentos b', 'b.id = a.id_departamento');
        if ($this->empresa) {
            $qb->where('b.id_empresa', $this->empresa);
        }
        $areas = $qb
            ->where('b.id', $idDepto)
            ->orderBy('a.nome')
            ->get()
            ->getResultArray();

        return array_column($areas, 'nome', 'id');
    }

    public function getSetores($idArea = ''): array
    {
        $qb = $this->db
            ->table('empresa_setores a')
            ->select('a.id, a.nome')
            ->join('empresa_areas b', 'b.id = a.id_area')
            ->join('empresa_departamentos c', 'c.id = b.id_departamento');
        if ($this->empresa) {
            $qb->where('c.id_empresa', $this->empresa);
        }
        $setores = $qb
            ->where('b.id', $idArea)
            ->orderBy('a.nome')
            ->get()
            ->getResultArray();

        return array_column($setores, 'nome', 'id');
    }
}
