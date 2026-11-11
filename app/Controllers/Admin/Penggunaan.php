<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InstansiModel;
use App\Models\KendaraanModel;
use App\Models\PegawaiModel;
use App\Models\PenempatanModel;

class Penggunaan extends BaseController
{
    protected PenempatanModel $penempatanModel;
    protected KendaraanModel $kendaraanModel;
    protected PegawaiModel $pegawaiModel;
    protected InstansiModel $instansiModel;

    public function __construct()
    {
        $this->penempatanModel = new PenempatanModel();
        $this->kendaraanModel = new KendaraanModel();
        $this->pegawaiModel = new PegawaiModel();
        $this->instansiModel = new InstansiModel();
    }

    public function index()
    {
        return view('admin/penggunaan/index', [
            'title' => 'Penggunaan Barang',
            'status' => $this->request->getGet('status'),
            'penggunaans' => $this->penempatanModel->getAllWithRelations($this->request->getGet('status')),
        ]);
    }

    public function create()
    {
        return view('admin/penggunaan/form', [
            'title' => 'Input Penggunaan Barang',
            'kendaraans' => $this->kendaraanModel->where('status', 'baik')->where('stock >', 0)->findAll(),
            'pegawais' => $this->pegawaiModel->where('status', 'active')->orderBy('name')->findAll(),
            'instansis' => $this->instansiModel->where('status', 'active')->orderBy('name')->findAll(),
        ]);
    }

    public function store()
    {
        $rules = [
            'kendaraan_id' => 'required|is_natural_no_zero',
            'pegawai_id' => 'required|is_natural_no_zero',
            'instansi_id' => 'required|is_natural_no_zero',
            'assigned_at' => 'required|valid_date[Y-m-d]',
            'notes' => 'permit_empty|max_length[1000]',
        ];
        if (!$this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());

        $kendaraanId = (int) $this->request->getPost('kendaraan_id');
        $kendaraan = $this->kendaraanModel->where('id', $kendaraanId)->where('status', 'baik')->where('stock >', 0)->first();
        if (!$kendaraan) return redirect()->back()->withInput()->with('error', 'Kendaraan tidak tersedia.');

        $this->penempatanModel->insert([
            'kendaraan_id' => $kendaraanId,
            'pegawai_id' => $this->request->getPost('pegawai_id'),
            'instansi_id' => $this->request->getPost('instansi_id'),
            'assigned_at' => $this->request->getPost('assigned_at'),
            'status' => 'assigned',
            'notes' => $this->request->getPost('notes'),
        ]);
        $this->kendaraanModel->reduceStock($kendaraanId, 1);
        return redirect()->to('/admin/transaksi')->with('success', 'Penggunaan kendaraan berhasil dicatat.');
    }

    public function returned(int $id)
    {
        $item = $this->penempatanModel->find($id);
        if (!$item || $item['status'] !== 'assigned') return redirect()->back()->with('error', 'Penempatan tidak dapat dikembalikan.');
        $this->penempatanModel->update($id, ['status' => 'returned', 'returned_at' => date('Y-m-d')]);
        $this->kendaraanModel->addStock((int) $item['kendaraan_id'], 1);
        return redirect()->to('/admin/transaksi')->with('success', 'Kendaraan berhasil dikembalikan.');
    }

    public function delete(int $id)
    {
        $item = $this->penempatanModel->find($id);
        if ($item) {
            if ($item['status'] === 'assigned') $this->kendaraanModel->addStock((int) $item['kendaraan_id'], 1);
            $this->penempatanModel->delete($id);
        }
        return redirect()->to('/admin/transaksi')->with('success', 'Data penggunaan berhasil dihapus.');
    }
}
