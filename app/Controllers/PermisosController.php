<?php

namespace App\Controllers;

use App\Models\PermisosModel;

class PermisosController extends BaseController
{
    public function index()
    {
        $model = new PermisosModel();
        $data['permisos'] = $model->findAll();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'ok' => true,
                'data' => $data['permisos'],
            ]);
        }

        $css = [
            'style' => 'admin.css'
        ];

        return view('base/head', $css)
            . view('base/header')
            . view('admin/catalogos/cat_permisos', $data)
            . view('base/footer');
    }

    public function store()
    {
        $name = $this->request->getPost('name');
        $description = $this->request->getPost('description');

        if (!$name) {
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'missing_name',
            ]);
        }

        $model = new PermisosModel();
        try {
            $model->insert([
                'name' => $name,
                'description' => $description,
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

    public function show()
    {
        $id = $this->request->getGet('id');
        $model = new PermisosModel();
        $permiso = $model->find($id);

        return $this->response->setJSON([
            'ok' => (bool) $permiso,
            'data' => $permiso,
        ]);
    }

    public function update()
    {
        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        $description = $this->request->getPost('description');

        if (!$id) {
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'missing_id',
            ]);
        }

        $model = new PermisosModel();
        try {
            $model->update($id, [
                'name' => $name,
                'description' => $description,
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

        $model = new PermisosModel();
        $model->delete($id);

        return $this->response->setJSON(['ok' => true]);
    }
}
