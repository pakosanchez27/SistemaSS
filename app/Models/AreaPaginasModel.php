<?php

namespace App\Models;

use CodeIgniter\Model;

class AreaPaginasModel extends Model
{
    protected $table = 'area_paginas';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'area_id',
        'pagina_id',
        'orden',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';
}
