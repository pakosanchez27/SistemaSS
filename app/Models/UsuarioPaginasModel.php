<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioPaginasModel extends Model
{
    protected $table = 'usuario_paginas';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'usuario_id',
        'pagina_id',
        'puede_ver',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';
}
