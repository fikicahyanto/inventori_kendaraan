<?php

namespace App\Models;

use CodeIgniter\Model;

class PenempatanModel extends Model
{
    protected $table = 'penempatan_kendaraan';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['kendaraan_id', 'pegawai_id', 'instansi_id', 'assigned_at', 'returned_at', 'status', 'notes'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getAllWithRelations(?string $status = null): array
    {
        $builder = $this->select('penempatan_kendaraan.*, kendaraans.code as kendaraan_code, kendaraans.name as kendaraan_name, pegawais.name as pegawai_name, pegawais.nip, instansis.code as instansi_code, instansis.name as instansi_name')
            ->join('kendaraans', 'kendaraans.id = penempatan_kendaraan.kendaraan_id')
            ->join('pegawais', 'pegawais.id = penempatan_kendaraan.pegawai_id')
            ->join('instansis', 'instansis.id = penempatan_kendaraan.instansi_id');
        if ($status) $builder->where('penempatan_kendaraan.status', $status);
        return $builder->orderBy('penempatan_kendaraan.id', 'DESC')->findAll();
    }
}
