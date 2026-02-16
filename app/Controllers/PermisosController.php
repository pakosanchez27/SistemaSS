<?php

namespace App\Controllers;

use App\Models\RolesModel;
use App\Models\PermisosModel;
use App\Models\RolesPermisosModel;

class RolesPermisosController extends BaseController
{
    public function index()
    {
        $roles = new RolesModel();
        $permisos = new PermisosModel();

        $data['roles'] = $roles->findAll();
        $data['permisos'] = $permisos->findAll();

        return view('admin/roles/permisos', $data);
    }

    public function asignar()
    {
        $model = new RolesPermisosModel();
        $rol_id = $this->request->getPost('rol_id');
        $permisos = $this->request->getPost('permisos');

        $model->where('rol_id', $rol_id)->delete();

        if ($permisos) {
            foreach ($permisos as $permiso_id) {
                $model->insert([
                    'rol_id' => $rol_id,
                    'permiso_id' => $permiso_id
                ]);
            }
        }

        return redirect()->back()->with('success','Permisos asignados');
    }
}
