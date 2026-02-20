<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\RolesModel;
use App\Models\AreasModel;
use App\Models\PermisosModel;
use App\Models\AccesosModel;
use App\Models\PaginasModel;

class UsuariosController extends BaseController
{
    public function index()
    {

        $db = \Config\Database::connect();
        $currentUserId = session()->get('user_id');
        helper('user');
        $currentUser = current_user();
        $isRoot = (int) ($currentUser['is_root'] ?? 0) === 1;

        $query = $db->table('users u')
            ->select('u.id, u.nombre, u.ap_paterno, u.ap_materno, u.cargo, u.telefono, u.estado, a.nombre as area, r.nombre as rol, ac.username as correo')
            ->join('areas a', 'a.id = u.area_id', 'left')
            ->join('roles r', 'r.id = u.rol_id', 'left')
            ->join('accesos ac', 'ac.user_id = u.id', 'left')
            ->where('u.deleted_at', null)
            ->where('u.is_root', 0);

        if ($currentUserId) {
            $query->where('u.id !=', (int) $currentUserId);
        }
        if (!$isRoot && !empty($currentUser['area_id'])) {
            $query->where('u.area_id', (int) $currentUser['area_id']);
        }

        $usuarios = $query->get()->getResultArray();

        $usuario = $currentUser;
        $paginasModel = new PaginasModel();

        if ($usuario && $usuario['is_root'] == 1) {
            $paginas = $paginasModel->where('estado', 1)->orderBy('nombre', 'ASC')->findAll();
        } elseif ($usuario) {
            $paginas = $paginasModel->getPaginasPorArea((int) $usuario['area_id']);
        } else {
            $paginas = [];
        }

        $paginasGlobales = [];
        $paginasArea = [];
        $paginasPorArea = [];
        if ($usuario && ($usuario['is_root'] ?? 0) == 1) {
            foreach ($paginas as $p) {
                if ((int) ($p['es_global'] ?? 0) === 1) {
                    $paginasGlobales[] = $p;
                } else {
                    $paginasArea[] = $p;
                }
            }

            $rows = $db->table('area_paginas ap')
                ->select('a.id as area_id, a.nombre as area_nombre, p.id as pagina_id, p.nombre as pagina_nombre')
                ->join('areas a', 'a.id = ap.area_id', 'inner')
                ->join('paginas p', 'p.id = ap.pagina_id', 'inner')
                ->where('p.estado', 1)
                ->where('p.deleted_at', null)
                ->orderBy('a.nombre', 'ASC')
                ->orderBy('p.nombre', 'ASC')
                ->get()
                ->getResultArray();

            foreach ($rows as $row) {
                $areaName = $row['area_nombre'];
                if (!isset($paginasPorArea[$areaName])) {
                    $paginasPorArea[$areaName] = [];
                }
                $paginasPorArea[$areaName][] = [
                    'id' => (int) $row['pagina_id'],
                    'nombre' => $row['pagina_nombre'],
                ];
            }
        }

        $rolesModel = new RolesModel();
        $roles = $rolesModel->findAll();

        $areasModel = new AreasModel();
        if (!$isRoot && !empty($currentUser['area_id'])) {
            $areas = $areasModel->where('id', (int) $currentUser['area_id'])->findAll();
        } else {
            $areas = $areasModel->findAll();
        }

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
                'permisos' => $permisos,
                'paginas' => $paginas,
                'paginasGlobales' => $paginasGlobales,
                'paginasArea' => $paginasArea,
                'paginasPorArea' => $paginasPorArea,
                'isRoot' => (int) ($usuario['is_root'] ?? 0) === 1
            ])
            . view('base/footer');
    }

    public function create() {}

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
        $paginaIds = $this->request->getPost('paginas') ?? [];

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

            if (is_array($paginaIds) && !empty($paginaIds)) {
                $paginaIds = array_values(array_unique(array_map('intval', $paginaIds)));
                $rows = [];
                foreach ($paginaIds as $paginaId) {
                    $rows[] = [
                        'usuario_id' => (int) $userId,
                        'pagina_id' => $paginaId,
                        'puede_ver' => 1,
                    ];
                }
                if (!empty($rows)) {
                    $db->table('usuario_paginas')->insertBatch($rows);
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

        helper('user');
        $currentUser = current_user();
        $isRoot = (int) ($currentUser['is_root'] ?? 0) === 1;
        $currentAreaId = (int) ($currentUser['area_id'] ?? 0);

        $db = \Config\Database::connect();
        try {
            $permisos = $db->query("
                SELECT p.id, p.name, p.description,
                       CASE WHEN up.user_id IS NULL THEN 0 ELSE 1 END AS assigned
                FROM permisos p
                LEFT JOIN user_permisos up
                  ON up.permiso_id = p.id AND up.user_id = ?
                WHERE p.deleted_at IS NULL
                ORDER BY p.name
            ", [(int) $userId])->getResultArray();

            if ($isRoot) {
                $paginas = $db->query("
                    SELECT p.id, p.nombre, p.es_global,
                           CASE WHEN up.usuario_id IS NULL THEN 0 ELSE 1 END AS assigned
                    FROM paginas p
            LEFT JOIN usuario_paginas up
                      ON up.pagina_id = p.id AND up.usuario_id = ?
                    WHERE p.deleted_at IS NULL AND p.estado = 1
                    ORDER BY p.nombre
                ", [(int) $userId])->getResultArray();
                $paginasGlobales = array_values(array_filter($paginas, function ($p) {
                    return (int) ($p['es_global'] ?? 0) === 1;
                }));
                $paginasArea = array_values(array_filter($paginas, function ($p) {
                    return (int) ($p['es_global'] ?? 0) !== 1;
                }));

                $rows = $db->query("
                    SELECT a.nombre as area_nombre, p.id as pagina_id, p.nombre as pagina_nombre,
                           CASE WHEN up.usuario_id IS NULL THEN 0 ELSE 1 END AS assigned
                    FROM area_paginas ap
                    INNER JOIN areas a ON a.id = ap.area_id
                    INNER JOIN paginas p ON p.id = ap.pagina_id
            LEFT JOIN usuario_paginas up
                      ON up.pagina_id = p.id AND up.usuario_id = ?
                    WHERE p.deleted_at IS NULL AND p.estado = 1
                    ORDER BY a.nombre, p.nombre
                ", [(int) $userId])->getResultArray();

                $paginasPorArea = [];
                foreach ($rows as $row) {
                    $areaName = $row['area_nombre'];
                    if (!isset($paginasPorArea[$areaName])) {
                        $paginasPorArea[$areaName] = [];
                    }
                    $paginasPorArea[$areaName][] = [
                        'id' => (int) $row['pagina_id'],
                        'nombre' => $row['pagina_nombre'],
                        'assigned' => (int) $row['assigned'],
                    ];
                }
            } else {
                $paginas = $db->query("
                    SELECT DISTINCT p.id, p.nombre,
                           CASE WHEN up.usuario_id IS NULL THEN 0 ELSE 1 END AS assigned
                    FROM paginas p
                    LEFT JOIN area_paginas ap ON ap.pagina_id = p.id
            LEFT JOIN usuario_paginas up
                      ON up.pagina_id = p.id AND up.usuario_id = ?
                    WHERE p.deleted_at IS NULL AND p.estado = 1
                      AND (p.es_global = 1 OR ap.area_id = ?)
                    ORDER BY p.nombre
                ", [(int) $userId, $currentAreaId])->getResultArray();
                $paginasGlobales = [];
                $paginasArea = [];
                $paginasPorArea = [];
            }
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'ok' => false,
                'error' => $e->getMessage(),
            ]);
        }

        return $this->response->setJSON([
            'ok' => true,
            'data' => $permisos,
            'paginas' => $paginas,
            'paginas_globales' => $paginasGlobales ?? [],
            'paginas_area' => $paginasArea ?? [],
            'paginas_por_area' => $paginasPorArea ?? [],
            'is_root' => $isRoot,
        ]);
    }

    public function updatePermisos()
    {
        $userId = $this->request->getPost('user_id');
        $permIds = $this->request->getPost('permisos') ?? [];
        $paginaIds = $this->request->getPost('paginas') ?? [];

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

            $db->table('usuario_paginas')->where('usuario_id', (int) $userId)->delete();

            if (is_array($paginaIds) && !empty($paginaIds)) {
                $paginaIds = array_values(array_unique(array_map('intval', $paginaIds)));
                $rows = [];
                foreach ($paginaIds as $paginaId) {
                    $rows[] = [
                        'usuario_id' => (int) $userId,
                        'pagina_id' => $paginaId,
                        'puede_ver' => 1,
                    ];
                }
                if (!empty($rows)) {
                    $db->table('usuario_paginas')->insertBatch($rows);
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

    public function delete()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $id = (int) ($this->request->getPost('id') ?? 0);
        if ($id <= 0) {
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'missing_id',
            ]);
        }

        $usersModel = new UsersModel();
        $accesosModel = new AccesosModel();

        try {
            $usersModel->delete($id);
            $accesosModel->where('user_id', $id)->set('estado', 0)->update();
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'db',
            ]);
        }

        return $this->response->setJSON(['ok' => true]);
    }

    public function perfil()
    {
        helper('user');
        $usuario = current_user();
        if (!$usuario) {
            return redirect()->to('/login');
        }

        $css = [
            'style' => 'admin.css'
        ];

        return view('base/head', $css)
            . view('base/header')
            . view('admin/perfil', [
                'usuario' => $usuario
            ])
            . view('base/footer');
    }

    public function updatePerfil()
    {
        helper('user');
        $usuario = current_user();
        if (!$usuario) {
            return redirect()->to('/login');
        }

        $nombre = trim((string) $this->request->getPost('nombre'));
        $apPaterno = trim((string) $this->request->getPost('ap_paterno'));
        $apMaterno = trim((string) $this->request->getPost('ap_materno'));
        $telefono = trim((string) $this->request->getPost('telefono'));
        $cargo = trim((string) $this->request->getPost('cargo'));
        $correo = trim((string) $this->request->getPost('correo'));

        if (!$nombre || !$apPaterno || !$correo) {
            return redirect()->to('/admin/perfil')
                ->with('error', 'Faltan campos obligatorios.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $usersModel = new UsersModel();
        $accesosModel = new AccesosModel();

        try {
            $usersModel->update((int) $usuario['id'], [
                'nombre' => $nombre,
                'ap_paterno' => $apPaterno,
                'ap_materno' => $apMaterno,
                'telefono' => $telefono,
                'cargo' => $cargo,
            ]);

            $accesosModel->where('user_id', (int) $usuario['id'])
                ->set('username', $correo)
                ->update();

            $db->transComplete();
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->to('/admin/perfil')
                ->with('error', 'No se pudo actualizar el perfil.');
        }

        return redirect()->to('/admin/perfil')->with('success', 'Perfil actualizado.');
    }
}
