<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Users extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Manajemen Pengguna',
            'users' => $this->userModel->orderBy('id', 'DESC')->findAll(),
        ];
        return view('admin/users/index', $data);
    }

    public function store()
    {
        $rules = [
            'name'     => 'required|min_length[3]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'role'     => 'required|in_list[admin,pegawai,dinas]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $this->userModel->insert([
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'phone'    => $this->request->getPost('phone'),
            'address'  => $this->request->getPost('address'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'     => $this->request->getPost('role'),
            'status'   => 'active',
        ]);

        return redirect()->to('/admin/pengguna')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $data = [
            'name'    => $this->request->getPost('name'),
            'phone'   => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'role'    => $this->request->getPost('role'),
            'status'  => $this->request->getPost('status'),
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $data);

        return redirect()->to('/admin/pengguna')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        if ($id == session()->get('user_id')) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }
        $this->userModel->delete($id);
        return redirect()->to('/admin/pengguna')->with('success', 'Pengguna berhasil dihapus.');
    }

    public function verifyKtp(int $id)
    {
        $user = $this->userModel->find($id);
        if (!$user || empty($user['ktp_photo'])) {
            return redirect()->back()->with('error', 'Pengguna belum mengunggah foto KTP.');
        }

        $this->userModel->update($id, [
            'ktp_verified'    => 'verified',
            'ktp_verified_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/pengguna')->with('success', 'KTP pengguna berhasil diverifikasi.');
    }

    public function rejectKtp(int $id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan.');
        }

        $this->userModel->update($id, [
            'ktp_verified'    => 'rejected',
            'ktp_verified_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/pengguna')->with('success', 'KTP pengguna ditolak.');
    }
}
