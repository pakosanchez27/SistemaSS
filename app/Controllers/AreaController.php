<?php

namespace App\Controllers;

use App\Models\AreasModel;

class AreaController extends BaseController
{
    public function index()
    {
        $model = new AreasModel();
        $data['areas'] = $model->findAll();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'ok' => true,
                'data' => $data['areas'],
            ]);
        }

        $css = [
            'style' => 'admin.css'
        ];

        return view('base/head', $css)
            . view('base/header')
            . view('admin/catalogos/cat_areas', $data)
            . view('base/footer');
    }

    public function store()
    {
        $nombre = $this->request->getPost('nombre');
        $activo = $this->request->getPost('activo') ?? 1;

        if (!$nombre) {
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'missing_nombre',
            ]);
        }

        $model = new AreasModel();
        $model->insert([
            'nombre' => $nombre,
            'activo' => (int) $activo,
        ]);

        return $this->response->setJSON(['ok' => true]);
    }

    public function show()
    {
        $id = $this->request->getGet('id');
        $model = new AreasModel();
        $area = $model->find($id);

        return $this->response->setJSON([
            'ok' => (bool) $area,
            'data' => $area,
        ]);
    }

    public function update()
    {
        $id = $this->request->getPost('id');
        $nombre = $this->request->getPost('nombre');
        $activo = $this->request->getPost('activo') ?? 1;

        if (!$id) {
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'missing_id',
            ]);
        }

        $model = new AreasModel();
        $model->update($id, [
            'nombre' => $nombre,
            'activo' => (int) $activo,
        ]);

        return $this->response->setJSON(['ok' => true]);
    }

    public function delete()
    {
        $id = $this->request->getPost('id');
        if (!$id) {
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'missing_id',
            ]);
        }

        $model = new AreasModel();
        $model->delete($id);

        return $this->response->setJSON(['ok' => true]);
    }
}
