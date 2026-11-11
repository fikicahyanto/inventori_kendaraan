<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table         = 'categories';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['name', 'description'];
    protected $useTimestamps = false;

    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[100]',
    ];
}
