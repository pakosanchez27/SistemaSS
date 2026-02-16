<?php

namespace App\Models;
use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nombre',
        'ap_paterno',
        'ap_materno',
        'telefono',
        'cargo',
        'area_id',
        'rol_id',
        'estado'
    ];
}
