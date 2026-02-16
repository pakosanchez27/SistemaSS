<?php

namespace App\Models;

use CodeIgniter\Model;

class SoftwareModel extends Model
{
    protected $table = 'software';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nombre', 'estado'];
}
