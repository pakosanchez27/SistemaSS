<?php

namespace App\Models;
use CodeIgniter\Model;

class RolesModel extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nombre'];
    protected $useSoftDeletes = true;
    protected $deletedField  = 'deleted_at';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function listRoles()
    {
        return $this->orderBy('id', 'DESC')->findAll();
    }
}
