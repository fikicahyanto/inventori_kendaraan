<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table         = 'payments';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'rental_id', 'amount', 'payment_method', 'proof_image',
        'status', 'paid_at', 'verified_at'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public function getAllWithRental(?string $status = null)
    {
        $builder = $this->select('payments.*, rentals.invoice_code, users.name as customer_name')
                         ->join('rentals', 'rentals.id = payments.rental_id')
                         ->join('users', 'users.id = rentals.user_id');
        if ($status) {
            $builder->where('payments.status', $status);
        }
        return $builder->orderBy('payments.id', 'DESC')->findAll();
    }

    public function getByRental(int $rentalId)
    {
        return $this->where('rental_id', $rentalId)->orderBy('id', 'DESC')->findAll();
    }
}
