<?php

namespace App\Controllers;

use App\Models\RolesModel;

class RolesController extends BaseController
{
    public function index()
    {
        $model = new RolesModel();
        $data['roles'] = $model->findAll();
        return view('admin/roles/index', $data);
    }

    public function create()
    {
        return view('admin/roles/create');
    }

    public function store()
    {
        $model = new RolesModel();
        $model->insert([
            'nombre' => $this->request->getPost('nombre')
        ]);
        return redirect()->to('/admin/roles');
    }
}
