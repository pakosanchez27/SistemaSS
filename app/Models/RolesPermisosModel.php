<?php

namespace App\Models;

use CodeIgniter\Model;

class RolesPermisosModel extends Model
{
    protected $table = 'roles_permisos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['rol_id', 'permiso_id'];
}
