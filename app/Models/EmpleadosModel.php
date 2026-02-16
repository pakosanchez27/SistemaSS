<?php

namespace App\Models;

use CodeIgniter\Model;

class EmpleadosModel extends Model
{
    protected $table = 'empleados';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'parent_id',
        'tipo',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'telefono',
        'correo',
        'area',
        'cargo',
        'anos_laborando',
        'grado_estudios',
        'profesion',
        'sexo',
        'edad',
        'descripcion_labores',
        //'manejo_software',
    ];

    protected $useTimestamps = false; // usas CURRENT_TIMESTAMP en MySQL
}
