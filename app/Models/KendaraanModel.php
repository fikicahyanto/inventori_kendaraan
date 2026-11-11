<?php

namespace App\Models;

use CodeIgniter\Model;

class KendaraanModel extends Model
{
    protected $table         = 'kendaraans';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'category_id', 'code', 'name', 'brand', 'warna', 'ram',
        'specification',
        'stock', 'image', 'status'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // protected $validationRules = [
    //     'code'          => 'required|is_unique[kendaraans.code,id,{id}]',
    //     'name'          => 'required|min_length[3]',
    //     'brand'         => 'required',
    //     'price_per_day' => 'required|numeric|greater_than[0]',
    //     'stock'         => 'required|integer|greater_than_equal_to[0]',
    // ];

    public function getWithCategory(?int $id = null)
    {
        $builder = $this->select('kendaraans.*, categories.name as category_name')
                         ->join('categories', 'categories.id = kendaraans.category_id');
        if ($id !== null) {
            return $builder->where('kendaraans.id', $id)->first();
        }
        return $builder->orderBy('kendaraans.id', 'DESC')->findAll();
    }

    /**
     * Cari jumlah unit yang sedang disewa (belum returned/cancelled/rejected)
     * dalam rentang tanggal tertentu, untuk cek ketersediaan stok riil.
     */
    public function getBookedQty(int $kendaraanId, string $startDate, string $endDate, ?int $excludeRentalId = null): int
    {
        $db = \Config\Database::connect();
        $builder = $db->table('rental_items ri')
            ->selectSum('ri.qty')
            ->join('rentals r', 'r.id = ri.rental_id')
            ->where('ri.laptop_id', $kendaraanId)
            ->whereIn('r.status', ['pending', 'approved', 'ongoing'])
            ->groupStart()
                ->where('r.start_date <=', $endDate)
                ->where('r.end_date >=', $startDate)
            ->groupEnd();

        if ($excludeRentalId) {
            $builder->where('r.id !=', $excludeRentalId);
        }

        $result = $builder->get()->getRow();
        return (int) ($result->qty ?? 0);
    }

    public function availableStock(int $kendaraanId, string $startDate, string $endDate, ?int $excludeRentalId = null): int
    {
        $laptop = $this->find($kendaraanId);
        if (!$laptop) return 0;
        $booked = $this->getBookedQty($kendaraanId, $startDate, $endDate, $excludeRentalId);
        return max(0, (int)$laptop['stock'] - $booked);
    }

    public function search(?string $keyword = null, ?int $categoryId = null, ?string $sort = null)
    {
        $builder = $this->select('kendaraans.*, categories.name as category_name')
                         ->join('categories', 'categories.id = kendaraans.category_id')
                         ->where('kendaraans.status', 'baik');

        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('kendaraans.name', $keyword)
                ->orLike('kendaraans.brand', $keyword)
                ->groupEnd();
        }
        if (!empty($categoryId)) {
            $builder->where('kendaraans.category_id', $categoryId);
        }

        switch ($sort) {
            case 'price_asc':
                $builder->orderBy('kendaraans.price_per_day', 'ASC');
                break;
            case 'price_desc':
                $builder->orderBy('kendaraans.price_per_day', 'DESC');
                break;
            default:
                $builder->orderBy('kendaraans.id', 'DESC');
        }

        return $builder->findAll();
    }

    public function reduceStock(int $kendaraanId, int $qty)
    {
        $this->set('stock', 'stock - ' . $qty, false)->update($kendaraanId);
    }

    public function addStock(int $kendaraanId, int $qty)
    {
        $this->set('stock', 'stock + ' . $qty, false)->update($kendaraanId);
    }
}
