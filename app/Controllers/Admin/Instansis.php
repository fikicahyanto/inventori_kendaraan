<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InstansiModel;

class Instansis extends BaseController
{
    protected InstansiModel $instansiModel;

    public function __construct()
    {
        $this->instansiModel = new InstansiModel();
    }

    public function index()
    {
        return view('admin/instansis/index', [
            'title' => 'Manajemen Instansi',
            'instansis' => $this->instansiModel->orderBy('id', 'DESC')->findAll(),
        ]);
    }

    public function store()
    {
        $rules = [
            'code' => 'required|max_length[30]|is_unique[instansis.code]',
            'name' => 'required|min_length[3]|max_length[150]',
            'phone' => 'permit_empty|max_length[30]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->instansiModel->insert([
            'code' => trim($this->request->getPost('code')),
            'name' => trim($this->request->getPost('name')),
            'address' => $this->request->getPost('address'),
            'phone' => $this->request->getPost('phone'),
            'status' => 'active',
        ]);

        return redirect()->to('/admin/instansi')->with('success', 'Instansi berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $instansi = $this->instansiModel->find($id);
        if (!$instansi) {
            return redirect()->back()->with('error', 'Instansi tidak ditemukan.');
        }

        $rules = [
            'code' => "required|max_length[30]|is_unique[instansis.code,id,{$id}]",
            'name' => 'required|min_length[3]|max_length[150]',
            'phone' => 'permit_empty|max_length[30]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->instansiModel->update($id, [
            'code' => trim($this->request->getPost('code')),
            'name' => trim($this->request->getPost('name')),
            'address' => $this->request->getPost('address'),
            'phone' => $this->request->getPost('phone'),
            'status' => $this->request->getPost('status') === 'inactive' ? 'inactive' : 'active',
        ]);

        return redirect()->to('/admin/instansi')->with('success', 'Instansi berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $this->instansiModel->delete($id);
        return redirect()->to('/admin/instansi')->with('success', 'Instansi berhasil dihapus.');
    }
}
