<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PegawaiModel;

class Pegawais extends BaseController
{
    protected PegawaiModel $pegawaiModel;

    public function __construct()
    {
        $this->pegawaiModel = new PegawaiModel();
    }

    public function index()
    {
        return view('admin/pegawais/index', [
            'title' => 'Manajemen Pegawai',
            'pegawais' => $this->pegawaiModel->orderBy('id', 'DESC')->findAll(),
        ]);
    }

    public function store()
    {
        $rules = ['nip' => 'permit_empty|max_length[40]|is_unique[pegawais.nip]', 'name' => 'required|min_length[3]|max_length[150]', 'position' => 'permit_empty|max_length[100]', 'phone' => 'permit_empty|max_length[30]'];
        if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        $this->pegawaiModel->insert(['nip' => $this->request->getPost('nip') ?: null, 'name' => trim($this->request->getPost('name')), 'position' => $this->request->getPost('position'), 'phone' => $this->request->getPost('phone'), 'status' => 'active']);
        return redirect()->to('/admin/pegawai')->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        if (!$this->pegawaiModel->find($id)) return redirect()->back()->with('error', 'Pegawai tidak ditemukan.');
        $rules = ['nip' => "permit_empty|max_length[40]|is_unique[pegawais.nip,id,{$id}]", 'name' => 'required|min_length[3]|max_length[150]', 'position' => 'permit_empty|max_length[100]', 'phone' => 'permit_empty|max_length[30]'];
        if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        $this->pegawaiModel->update($id, ['nip' => $this->request->getPost('nip') ?: null, 'name' => trim($this->request->getPost('name')), 'position' => $this->request->getPost('position'), 'phone' => $this->request->getPost('phone'), 'status' => $this->request->getPost('status') === 'inactive' ? 'inactive' : 'active']);
        return redirect()->to('/admin/pegawai')->with('success', 'Pegawai berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $this->pegawaiModel->delete($id);
        return redirect()->to('/admin/pegawai')->with('success', 'Pegawai berhasil dihapus.');
    }
}
