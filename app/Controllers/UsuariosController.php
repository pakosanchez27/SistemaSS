<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\RolesModel;
use App\Models\AreasModel;
use App\Models\PermisosModel;
use App\Models\AccesosModel;

class UsuariosController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $currentUserId = session()->get('user_id');
        $query = $db->table('users u')
            ->select('u.id, u.nombre, u.ap_paterno, u.ap_materno, u.cargo, u.telefono, u.estado, a.nombre as area, r.nombre as rol, ac.username as correo')
            ->join('areas a', 'a.id = u.area_id', 'left')
            ->join('roles r', 'r.id = u.rol_id', 'left')
            ->join('accesos ac', 'ac.user_id = u.id', 'left');

        if ($currentUserId) {
            $query->where('u.id !=', (int) $currentUserId);
        }

        $usuarios = $query->get()->getResultArray();

        $rolesModel = new RolesModel();
        $roles = $rolesModel->findAll();
        
        $areasModel = new AreasModel();
        $areas = $areasModel->findAll();

        $permisosModel = new PermisosModel();
        $permisos = $permisosModel->findAll();

        $css = [
            'style' => 'admin.css'
        ];

        return view('base/head', $css)
            . view('base/header')
            . view('admin/usuarios/index', [
            'usuarios' => $usuarios,
            'roles' => $roles,
            'areas' => $areas,
            'permisos' => $permisos
            ])
            . view('base/footer');
    }

    public function create()
    {
        
    }

    public function store()
    {
        $nombre = trim((string) $this->request->getPost('nombre'));
        $apPaterno = trim((string) $this->request->getPost('ap_paterno'));
        $apMaterno = trim((string) $this->request->getPost('ap_materno'));
        $telefono = trim((string) $this->request->getPost('telefono'));
        $cargo = trim((string) $this->request->getPost('cargo'));
        $areaId = $this->request->getPost('area');
        $rolId = $this->request->getPost('rol_id');
        $correo = trim((string) $this->request->getPost('correo'));
        $password = (string) ($this->request->getPost('password') ?? $this->request->getPost('anos_laborando'));
        $permIds = $this->request->getPost('permisos') ?? [];

        if (!$nombre || !$apPaterno || !$rolId || !$areaId || !$correo || !$password) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'ok' => false,
                    'error' => 'missing_fields',
                ]);
            }
            return redirect()->to('/admin/usuarios')
                ->with('error', 'Faltan campos obligatorios.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $usersModel = new UsersModel();
        $accesosModel = new AccesosModel();

        try {
            $usersModel->insert([
                'nombre' => $nombre,
                'ap_paterno' => $apPaterno,
                'ap_materno' => $apMaterno,
                'telefono' => $telefono,
                'cargo' => $cargo,
                'area_id' => (int) $areaId,
                'rol_id' => (int) $rolId,
                'estado' => 1,
            ]);

            $userId = $usersModel->getInsertID();

            $accesosModel->insert([
                'user_id' => $userId,
                'username' => $correo,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'estado' => 1,
            ]);

            if (is_array($permIds) && !empty($permIds)) {
                $permIds = array_values(array_unique(array_map('intval', $permIds)));
                $rows = [];
                foreach ($permIds as $permId) {
                    $rows[] = [
                        'user_id' => $userId,
                        'permiso_id' => $permId,
                    ];
                }
                if (!empty($rows)) {
                    $db->table('user_permisos')->insertBatch($rows);
                }
            }

            $db->transComplete();
        } catch (\Throwable $e) {
            $db->transRollback();
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'ok' => false,
                    'error' => 'db',
                ]);
            }
            return redirect()->to('/admin/usuarios')
                ->with('error', 'No se pudo crear el usuario.');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['ok' => true]);
        }

        return redirect()->to('/admin/usuarios')->with('success', 'Usuario creado.');
    }

    public function resetPassword()
    {
        $userId = $this->request->getPost('user_id');
        $password = (string) $this->request->getPost('password');

        if (!$userId || !$password) {
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'missing_fields',
            ]);
        }

        $accesosModel = new AccesosModel();
        $accesosModel->where('user_id', (int) $userId)
            ->set('password', password_hash($password, PASSWORD_DEFAULT))
            ->update();

        return $this->response->setJSON(['ok' => true]);
    }

    public function permisos()
    {
        $userId = $this->request->getGet('user_id');
        if (!$userId) {
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'missing_user_id',
            ]);
        }

        $db = \Config\Database::connect();
        $permisos = $db->query("
            SELECT p.id, p.name, p.description,
                   CASE WHEN up.user_id IS NULL THEN 0 ELSE 1 END AS assigned
            FROM permisos p
            LEFT JOIN user_permisos up
              ON up.permiso_id = p.id AND up.user_id = ?
            WHERE p.deleted_at IS NULL
            ORDER BY p.name
        ", [(int) $userId])->getResultArray();

        return $this->response->setJSON([
            'ok' => true,
            'data' => $permisos,
        ]);
    }

    public function updatePermisos()
    {
        $userId = $this->request->getPost('user_id');
        $permIds = $this->request->getPost('permisos') ?? [];

        if (!$userId) {
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'missing_user_id',
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $db->table('user_permisos')->where('user_id', (int) $userId)->delete();

            if (is_array($permIds) && !empty($permIds)) {
                $permIds = array_values(array_unique(array_map('intval', $permIds)));
                $rows = [];
                foreach ($permIds as $permId) {
                    $rows[] = [
                        'user_id' => (int) $userId,
                        'permiso_id' => $permId,
                    ];
                }
                if (!empty($rows)) {
                    $db->table('user_permisos')->insertBatch($rows);
                }
            }

            $db->transComplete();
        } catch (\Throwable $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'db',
            ]);
        }

        return $this->response->setJSON(['ok' => true]);
    }

    public function show()
    {
        $id = $this->request->getGet('id');
        if (!$id) {
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'missing_id',
            ]);
        }

        $db = \Config\Database::connect();
        $data = $db->table('users u')
            ->select('u.id, u.nombre, u.ap_paterno, u.ap_materno, u.cargo, u.telefono, u.estado, u.area_id, u.rol_id, ac.username as correo')
            ->join('accesos ac', 'ac.user_id = u.id', 'left')
            ->where('u.id', (int) $id)
            ->get()
            ->getRowArray();

        return $this->response->setJSON([
            'ok' => (bool) $data,
            'data' => $data,
        ]);
    }

    public function update()
    {
        $id = $this->request->getPost('id');
        $nombre = trim((string) $this->request->getPost('nombre'));
        $apPaterno = trim((string) $this->request->getPost('ap_paterno'));
        $apMaterno = trim((string) $this->request->getPost('ap_materno'));
        $telefono = trim((string) $this->request->getPost('telefono'));
        $cargo = trim((string) $this->request->getPost('cargo'));
        $areaId = $this->request->getPost('area');
        $rolId = $this->request->getPost('rol_id');
        $correo = trim((string) $this->request->getPost('correo'));
        $estado = $this->request->getPost('estado') ?? 1;

        if (!$id || !$nombre || !$apPaterno || !$rolId || !$areaId || !$correo) {
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'missing_fields',
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $usersModel = new UsersModel();
        $accesosModel = new AccesosModel();

        try {
            $usersModel->update((int) $id, [
                'nombre' => $nombre,
                'ap_paterno' => $apPaterno,
                'ap_materno' => $apMaterno,
                'telefono' => $telefono,
                'cargo' => $cargo,
                'area_id' => (int) $areaId,
                'rol_id' => (int) $rolId,
                'estado' => (int) $estado,
            ]);

            $accesosModel->where('user_id', (int) $id)
                ->set('username', $correo)
                ->update();

            $db->transComplete();
        } catch (\Throwable $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'db',
            ]);
        }

        return $this->response->setJSON(['ok' => true]);
    }
}
