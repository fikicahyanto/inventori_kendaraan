<?php

namespace App\Models;

use CodeIgniter\Model;

class RentalItemModel extends Model
{
    protected $table         = 'rental_items';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['rental_id', 'laptop_id', 'qty', 'price_per_day', 'price_per_month', 'subtotal'];
    protected $useTimestamps = false;

    public function getItemsByRental(int $rentalId)
    {
        return $this->select('rental_items.*, laptops.name, laptops.brand, laptops.image, laptops.code')
                    ->join('laptops', 'laptops.id = rental_items.laptop_id')
                    ->where('rental_id', $rentalId)
                    ->findAll();
    }
}
