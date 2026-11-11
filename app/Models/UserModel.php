<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'name', 'email', 'phone', 'address', 'ktp_number', 'ktp_photo',
        'ktp_verified', 'ktp_verified_at',
        'password', 'role', 'photo', 'status'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'     => 'required|min_length[3]|max_length[150]',
        'email'    => 'required|valid_email',
        'password' => 'permit_empty|min_length[6]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Email sudah terdaftar, silakan gunakan email lain.',
        ],
    ];

    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    public function customers()
    {
        return $this->where('role', 'pegawai')->orderBy('id', 'DESC')->findAll();
    }

    /**
     * Cek apakah data KTP peminjam sudah lengkap (nomor + foto) dan
     * berhasil diverifikasi oleh admin. Dipakai sebagai syarat sebelum
     * transaksi sewa boleh disetujui.
     */
    public function isKtpVerified(int $userId): bool
    {
        $user = $this->find($userId);
        return $user && $user['ktp_verified'] === 'verified';
    }
}
