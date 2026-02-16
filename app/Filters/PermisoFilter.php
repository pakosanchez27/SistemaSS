<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UsersModel;

class PermisoFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $user_id = session()->get('user_id');

        if (!$user_id) {
            return redirect()->to('/login');
        }

        $permisoRequerido = $arguments[0];

        $db = \Config\Database::connect();

        // Permisos por rol
        $rolPermiso = $db->query("
            SELECT p.name
            FROM users u
            JOIN roles_permisos rp ON rp.rol_id = u.rol_id
            JOIN permisos p ON p.id = rp.permiso_id
            WHERE u.id = ? AND p.name = ?
        ", [$user_id, $permisoRequerido])->getRow();

        // Permiso individual
        $userPermiso = $db->query("
            SELECT p.name
            FROM user_permisos up
            JOIN permisos p ON p.id = up.permiso_id
            WHERE up.user_id = ? AND p.name = ?
        ", [$user_id, $permisoRequerido])->getRow();

        if (!$rolPermiso && !$userPermiso) {
            return redirect()->to('/admin/dashboard')
                ->with('error','No tienes permiso para acceder');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null){}
}
