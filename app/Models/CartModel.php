<?php

namespace App\Models;

use CodeIgniter\Model;

class CartModel extends Model
{
    protected $table         = 'carts';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['user_id', 'laptop_id', 'qty'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public function getCartByUser(int $userId)
    {
        return $this->select('carts.*, laptops.name, laptops.brand, laptops.image, laptops.price_per_day, laptops.price_per_month, laptops.stock, laptops.status as laptop_status')
                    ->join('laptops', 'laptops.id = carts.laptop_id')
                    ->where('carts.user_id', $userId)
                    ->orderBy('carts.id', 'DESC')
                    ->findAll();
    }

    public function findItem(int $userId, int $laptopId)
    {
        return $this->where('user_id', $userId)->where('laptop_id', $laptopId)->first();
    }

    public function countItems(int $userId): int
    {
        return $this->where('user_id', $userId)->countAllResults();
    }

    public function clearUserCart(int $userId)
    {
        return $this->where('user_id', $userId)->delete();
    }
}
