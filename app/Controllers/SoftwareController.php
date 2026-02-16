<?php

namespace App\Controllers;

use App\Models\SoftwareModel;

class SoftwareController extends BaseController
{
    public function index()
    {
        $model = new SoftwareModel();
        $data['software'] = $model->findAll();

        $css = [
            'style' => 'admin.css'
        ];

        return view('base/head', $css)
            . view('base/header')
            . view('admin/catalogos/cat_software', $data)
            . view('base/footer');
    }

    public function create()
    {
        return view('admin/software/create');
    }

    public function store()
    {
        $nombre = $this->request->getPost('nombre');
        $estado = $this->request->getPost('estado') ?? 1;

        $model = new SoftwareModel();
        try {
            $model->insert([
                'nombre' => $nombre,
                'estado' => (int) $estado,
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

            return redirect()->to('/admin/software')
                ->with('error', $isDuplicate ? 'El software ya existe.' : 'Error al guardar.');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['ok' => true]);
        }

        return redirect()->to('/admin/software');
    }

    public function show()
    {
        $id = $this->request->getGet('id');
        $model = new SoftwareModel();
        $datos = $model->find($id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'ok' => (bool) $datos,
                'data' => $datos,
            ]);
        }

        return $this->response->setJSON([
            'ok' => (bool) $datos,
            'data' => $datos,
        ]);
    }  
    
    public function update()
    {
        $id = $this->request->getPost('id');
        $nombre = $this->request->getPost('nombre');
        $estado = $this->request->getPost('estado') ?? 1;

        if (!$id) {
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'missing_id',
            ]);
        }

        $model = new SoftwareModel();
        try {
            $model->update($id, [
                'nombre' => $nombre,
                'estado' => (int) $estado,
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

        $model = new SoftwareModel();
        $model->delete($id);

        return $this->response->setJSON(['ok' => true]);
    }
}
