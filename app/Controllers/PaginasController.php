<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use App\Models\AreasModel;
use App\Models\PaginasModel;
use App\Models\AreaPaginasModel;

class PaginasController extends BaseController
{
    public function index()
    {
        $areasModel = new AreasModel();
        $areas = $areasModel->where('activo', 1)->findAll();

        $css = [
            'style' => 'admin.css'
        ];
        return view('base/head', $css)
            . view('base/header')
            . view('admin/catalogos/cat_paginas', [
                'areas' => $areas
            ])
            . view('base/footer');
    }

    public function show($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $id = (int) ($this->request->getGet('id') ?? 0);
        if ($id <= 0) {
            return $this->response->setJSON(['ok' => false, 'error' => 'ID invalido.']);
        }

        $paginasModel = new PaginasModel();
        $pagina = $paginasModel->find($id);
        if (!$pagina) {
            return $this->response->setJSON(['ok' => false, 'error' => 'Pagina no encontrada.']);
        }

        $areas = [];
        if (empty($pagina['es_global'])) {
            $areaPaginasModel = new AreaPaginasModel();
            $areas = $areaPaginasModel->where('pagina_id', $id)->findAll();
        }

        return $this->response->setJSON([
            'ok' => true,
            'pagina' => $pagina,
            'areas' => $areas,
        ]);
    }

    public function new()
    {
        return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_IMPLEMENTED);
    }

    public function create()
    {
        return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_IMPLEMENTED);
    }

    public function store()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $nombre = trim((string) $this->request->getPost('nombre'));
        $slug = trim((string) $this->request->getPost('slug'));
        $ruta = trim((string) $this->request->getPost('ruta'));
        $icono = trim((string) $this->request->getPost('icono'));
        $esGlobal = (int) ($this->request->getPost('es_global') ?? 0);
        $estado = (int) ($this->request->getPost('estado') ?? 1);
        $orden = (int) ($this->request->getPost('orden') ?? 0);
        $areas = $this->request->getPost('areas') ?? [];

        if ($nombre === '' || $slug === '' || $ruta === '') {
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'Faltan campos obligatorios.'
            ]);
        }

        if ($esGlobal !== 1 && (empty($areas) || !is_array($areas))) {
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'Selecciona al menos un area.'
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $paginasModel = new PaginasModel();
        $areaPaginasModel = new AreaPaginasModel();

        try {
            $paginasModel->insert([
                'nombre' => $nombre,
                'slug' => $slug,
                'ruta' => $ruta,
                'icono' => $icono !== '' ? $icono : null,
                'es_global' => $esGlobal ? 1 : 0,
                'estado' => $estado ? 1 : 0,
            ]);

            $paginaId = $paginasModel->getInsertID();

            if (!$esGlobal && !empty($areas)) {
                $rows = [];
                foreach ($areas as $areaId) {
                    $rows[] = [
                        'area_id' => (int) $areaId,
                        'pagina_id' => (int) $paginaId,
                        'orden' => $orden,
                    ];
                }
                if (!empty($rows)) {
                    $areaPaginasModel->insertBatch($rows);
                }
            }

            $db->transComplete();
        } catch (\Throwable $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'No se pudo guardar la pagina.'
            ]);
        }

        return $this->response->setJSON(['ok' => true]);
    }

    public function list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $db = \Config\Database::connect();
        $paginas = $db->table('paginas p')
            ->select('p.id, p.nombre, p.slug, p.icono, p.es_global, p.estado, p.ruta')
            ->select('GROUP_CONCAT(a.nombre ORDER BY a.nombre SEPARATOR ", ") AS areas')
            ->join('area_paginas ap', 'ap.pagina_id = p.id', 'left')
            ->join('areas a', 'a.id = ap.area_id', 'left')
            ->groupBy('p.id')
            ->orderBy('p.id', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'data' => $paginas
        ]);
    }

    public function edit($id = null)
    {
        return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_IMPLEMENTED);
    }

    public function update($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $id = (int) ($this->request->getPost('id') ?? 0);
        $nombre = trim((string) $this->request->getPost('nombre'));
        $slug = trim((string) $this->request->getPost('slug'));
        $ruta = trim((string) $this->request->getPost('ruta'));
        $icono = trim((string) $this->request->getPost('icono'));
        $esGlobal = (int) ($this->request->getPost('es_global') ?? 0);
        $estado = (int) ($this->request->getPost('estado') ?? 1);
        $orden = (int) ($this->request->getPost('orden') ?? 0);
        $areas = $this->request->getPost('areas') ?? [];

        if ($id <= 0 || $nombre === '' || $slug === '' || $ruta === '') {
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'Faltan campos obligatorios.'
            ]);
        }

        if ($esGlobal !== 1 && (empty($areas) || !is_array($areas))) {
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'Selecciona al menos un area.'
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $paginasModel = new PaginasModel();
        $areaPaginasModel = new AreaPaginasModel();

        try {
            $paginasModel->update($id, [
                'nombre' => $nombre,
                'slug' => $slug,
                'ruta' => $ruta,
                'icono' => $icono !== '' ? $icono : null,
                'es_global' => $esGlobal ? 1 : 0,
                'estado' => $estado ? 1 : 0,
            ]);

            $areaPaginasModel->where('pagina_id', $id)->delete();

            if (!$esGlobal && !empty($areas)) {
                $rows = [];
                foreach ($areas as $areaId) {
                    $rows[] = [
                        'area_id' => (int) $areaId,
                        'pagina_id' => $id,
                        'orden' => $orden,
                    ];
                }
                if (!empty($rows)) {
                    $areaPaginasModel->insertBatch($rows);
                }
            }

            $db->transComplete();
        } catch (\Throwable $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'No se pudo actualizar la pagina.'
            ]);
        }

        return $this->response->setJSON(['ok' => true]);
    }

    public function delete($id = null)
    {
        return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_IMPLEMENTED);
    }
}
