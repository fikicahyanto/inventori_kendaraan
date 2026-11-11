<?php

namespace App\Models;

use CodeIgniter\Model;

class PegawaiModel extends Model
{
    protected $table = 'pegawais';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['nip', 'name', 'position', 'phone', 'status'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
