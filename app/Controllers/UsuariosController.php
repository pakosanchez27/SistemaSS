<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\RolesModel;
use App\Models\AreasModel;

class UsuariosController extends BaseController
{
    public function index()
    {
        $model = new UsersModel();
        $usuarios = $model->findAll();

        $rolesModel = new RolesModel();
        $roles = $rolesModel->findAll();
        
        $areasModel = new AreasModel();
        $areas = $areasModel->findAll();

        $css = [
            'style' => 'admin.css'
        ];

        return view('base/head', $css)
            . view('base/header')
            . view('admin/usuarios/index', [
            'usuarios' => $usuarios,
            'roles' => $roles,
            'areas' => $areas
            ])
            . view('base/footer');
    }

    public function create()
    {
        
    }

    public function store()
    {
        $model = new UsersModel();

        $model->insert([
            'nombre' => $this->request->getPost('nombre'),
            'ap_paterno' => $this->request->getPost('ap_paterno'),
            'ap_materno' => $this->request->getPost('ap_materno'),
            'rol_id' => $this->request->getPost('rol_id'),
            'estado' => 1
        ]);

        return redirect()->to('/admin/usuarios');
    }
}
