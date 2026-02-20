<?php

namespace App\Models;

use CodeIgniter\Model;

class PaginasModel extends Model
{
    protected $table = 'paginas';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nombre',
        'slug',
        'ruta',
        'icono',
        'es_global',
        'estado',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';


    public function getPaginasPorArea($areaId)
    {
        return $this->db->table('paginas p')
            ->select('p.*')
            ->join('area_paginas ap', 'ap.pagina_id = p.id', 'left')
            ->where('(p.es_global = 1 OR ap.area_id = ' . (int) $areaId . ')')
            ->where('p.estado', 1)
            ->groupBy('p.id')
            ->get()
            ->getResultArray();
    }
}
