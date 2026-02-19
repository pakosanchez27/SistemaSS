<?php

namespace App\Controllers;

use App\Models\PermisosModel;
use App\Models\RolesModel;
use App\Models\RolesPermisosModel;

class RolesPermisosController extends BaseController
{
    public function index()
    {
        $rolesModel = new RolesModel();
        $permisosModel = new PermisosModel();
        $rolesPermisosModel = new RolesPermisosModel();

        $roles = $rolesModel->findAll();
        $permisos = $permisosModel->findAll();

        $selectedRolId = (int) $this->request->getGet('rol_id');
        if (!$selectedRolId && !empty($roles)) {
            $selectedRolId = (int) $roles[0]['id'];
        }

        $assignedPermIds = [];
        if ($selectedRolId) {
            $rows = $rolesPermisosModel->select('permiso_id')
                ->where('rol_id', $selectedRolId)
                ->findAll();
            $assignedPermIds = array_map('intval', array_column($rows, 'permiso_id'));
        }

        $data = [
            'roles' => $roles,
            'permisos' => $permisos,
            'selectedRolId' => $selectedRolId,
            'assignedPermIds' => $assignedPermIds,
        ];

        $css = [
            'style' => 'admin.css',
        ];

        return view('base/head', $css)
            . view('base/header')
            . view('admin/roles/permisos', $data)
            . view('base/footer');
    }

    public function asignar()
    {
        $rolId = (int) $this->request->getPost('rol_id');
        $permIds = $this->request->getPost('permisos') ?? [];

        if (!$rolId) {
            return redirect()->to('/admin/roles/permisos')
                ->with('error', 'Selecciona un rol.');
        }

        $rolesPermisosModel = new RolesPermisosModel();
        $rolesPermisosModel->where('rol_id', $rolId)->delete();

        if (!empty($permIds)) {
            $rows = [];
            foreach ($permIds as $permId) {
                $rows[] = [
                    'rol_id' => $rolId,
                    'permiso_id' => (int) $permId,
                ];
            }
            $rolesPermisosModel->insertBatch($rows);
        }

        return redirect()->to('/admin/roles/permisos')
            ->with('success', 'Permisos actualizados.');
    }

    public function permisosPorRol()
    {
        $rolId = (int) $this->request->getGet('rol_id');
        if (!$rolId) {
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'missing_rol_id',
            ]);
        }

        $rolesPermisosModel = new RolesPermisosModel();
        $rows = $rolesPermisosModel->select('permiso_id')
            ->where('rol_id', $rolId)
            ->findAll();
        $permIds = array_map('intval', array_column($rows, 'permiso_id'));

        return $this->response->setJSON([
            'ok' => true,
            'data' => $permIds,
        ]);
    }
}
