<?php

namespace App\Models;

use CodeIgniter\Model;

class AccesosModel extends Model
{
    protected $table = 'accesos';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id',
        'username',
        'password',
        'last_login',
        'estado'
    ];
    protected $useTimestamps = false;
}
