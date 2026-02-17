<?php

namespace App\Controllers;

use App\Models\RolesModel;

class RolesController extends BaseController
{
    public function index()
    {
        $model = new RolesModel();
        $data['roles'] = $model->findAll();

          $css = [
            'style' => 'admin.css'
        ];

        return view('base/head', $css)
            . view('base/header')
            . view('admin/roles/index', $data)
            . view('base/footer');
    }

    public function create()
    {
        return view('admin/roles/create');
    }

    public function store()
    {
        $model = new RolesModel();
        try {
            $model->insert([
                'nombre' => $this->request->getPost('nombre')
            ]);
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            $isDuplicate = str_contains($e->getMessage(), 'Duplicate entry')
                || str_contains($e->getMessage(), '1062');

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'ok' => false,
                    'error' => $isDuplicate ? 'duplicate' : 'db',
                ]);
            }

            return redirect()->to('/admin/roles')
                ->with('error', $isDuplicate ? 'El rol ya existe.' : 'Error al guardar.');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['ok' => true]);
        }

        return redirect()->to('/admin/roles');
    }

    public function show()
    {
        $id = $this->request->getGet('id');
        $model = new RolesModel();
        $datos = $model->find($id);

        return $this->response->setJSON([
            'ok' => (bool) $datos,
            'data' => $datos,
        ]);
    }

    public function update()
    {
        $id = $this->request->getPost('id');
        $nombre = $this->request->getPost('nombre');

        if (!$id) {
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'missing_id',
            ]);
        }

        $model = new RolesModel();
        try {
            $model->update($id, [
                'nombre' => $nombre,
            ]);
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            $isDuplicate = str_contains($e->getMessage(), 'Duplicate entry')
                || str_contains($e->getMessage(), '1062');

            return $this->response->setJSON([
                'ok' => false,
                'error' => $isDuplicate ? 'duplicate' : 'db',
            ]);
        }

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

        $model = new RolesModel();
        $model->delete($id);

        return $this->response->setJSON(['ok' => true]);
    }
}
