<?php

namespace App\Models;

use CodeIgniter\Model;

class RentalModel extends Model
{
    protected $table         = 'rentals';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'invoice_code', 'user_id', 'start_date', 'end_date', 'actual_return_date',
        'total_days', 'rental_type', 'subtotal', 'late_fee', 'total_price', 'status', 'notes',
        'is_self_pickup', 'recipient_name', 'recipient_phone', 'recipient_relation',
        'recipient_ktp_number', 'recipient_ktp_photo',
        'recipient_ktp_verified', 'recipient_ktp_verified_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generateInvoiceCode(): string
    {
        return 'INV-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }

    public function getForUser(int $userId)
    {
        return $this->where('user_id', $userId)->orderBy('id', 'DESC')->findAll();
    }

    public function getDetail(int $rentalId)
    {
        $rental = $this->select('rentals.*, users.name as customer_name, users.email, users.phone, users.address,
                                  users.ktp_number as borrower_ktp_number, users.ktp_photo as borrower_ktp_photo,
                                  users.ktp_verified as borrower_ktp_verified')
                        ->join('users', 'users.id = rentals.user_id')
                        ->find($rentalId);
        return $rental;
    }

    public function getAllWithUser(?string $status = null)
    {
        $builder = $this->select('rentals.*, users.name as customer_name, users.email')
                         ->join('users', 'users.id = rentals.user_id');
        if ($status) {
            $builder->where('rentals.status', $status);
        }
        return $builder->orderBy('rentals.id', 'DESC')->findAll();
    }

    public function countByStatus(string $status): int
    {
        return $this->where('status', $status)->countAllResults();
    }

    public function totalRevenue(): float
    {
        $result = $this->selectSum('total_price')
                        ->whereIn('status', ['approved', 'ongoing', 'returned'])
                        ->first();
        return (float) ($result['total_price'] ?? 0);
    }

    public function revenueBetween(string $start, string $end): array
    {
        return $this->select('rentals.*, users.name as customer_name')
                    ->join('users', 'users.id = rentals.user_id')
                    ->where('rentals.created_at >=', $start . ' 00:00:00')
                    ->where('rentals.created_at <=', $end . ' 23:59:59')
                    ->whereIn('rentals.status', ['approved', 'ongoing', 'returned'])
                    ->orderBy('rentals.created_at', 'ASC')
                    ->findAll();
    }
}
