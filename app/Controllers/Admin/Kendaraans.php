<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KendaraanModel;
use App\Models\CategoryModel;

class Kendaraans extends BaseController
{
    protected KendaraanModel $kendaraanModel;
    protected CategoryModel $categoryModel;

    public function __construct()
    {
        $this->kendaraanModel   = new KendaraanModel();
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $data = [
            'title'   => 'Manajemen Kendaraan',
            'kendaraans' => $this->kendaraanModel->getWithCategory(),
        ];
        return view('admin/kendaraans/index', $data);
    }

    public function create()
    {
        $data = [
            'title'      => 'Tambah Kendaraan',
            'categories' => $this->categoryModel->findAll(),
        ];
        return view('admin/kendaraans/form', $data);
    }

    public function store()
    {
        $rules = [
            'code'          => 'required|is_unique[kendaraans.code]',
            'name'          => 'required|min_length[3]',
            'brand'         => 'required',
            'category_id'   => 'required|integer',
            'stock'         => 'required|integer|greater_than_equal_to[0]',
            'image'         => 'permit_empty|is_image[image]|max_size[image,2048]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'category_id'    => $this->request->getPost('category_id'),
            'code'           => $this->request->getPost('code'),
            'name'           => $this->request->getPost('name'),
            'brand'          => $this->request->getPost('brand'),
            'warna'      => $this->request->getPost('processor'),
            'specification'  => $this->request->getPost('specification'),
            'stock'          => $this->request->getPost('stock'),
            'status'         => $this->request->getPost('status') ?: 'baik',
        ];

        $file = $this->request->getFile('image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'assets/uploads/kendaraans', $newName);
            $data['image'] = $newName;
        }

        $this->kendaraanModel->insert($data);

        return redirect()->to('/admin/kendaraan')->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $kendaraan = $this->kendaraanModel->find($id);
        if (!$kendaraan) {
            return redirect()->to('/admin/kendaraan')->with('error', 'Kendaraan tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit Kendaraan',
            'kendaraan'     => $kendaraan,
            'categories' => $this->categoryModel->findAll(),
        ];
        return view('admin/kendaraans/form', $data);
    }

    public function update(int $id)
    {
        $kendaraan = $this->kendaraanModel->find($id);
        if (!$kendaraan) {
            return redirect()->to('/admin/kendaraan')->with('error', 'Kendaraan tidak ditemukan.');
        }

        $rules = [
            'code'          => "required|is_unique[kendaraans.code,id,{$id}]",
            'name'          => 'required|min_length[3]',
            'stock'         => 'required|integer|greater_than_equal_to[0]',
            'image'         => 'permit_empty|is_image[image]|max_size[image,2048]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'category_id'    => $this->request->getPost('category_id'),
            'code'           => $this->request->getPost('code'),
            'name'           => $this->request->getPost('name'),
            'brand'          => $this->request->getPost('brand'),
            'warna'      => $this->request->getPost('processor'),
            'screen_size'    => $this->request->getPost('screen_size'),
            'specification'  => $this->request->getPost('specification'),
            'stock'          => $this->request->getPost('stock'),
            'status'         => $this->request->getPost('status'),
        ];

        $file = $this->request->getFile('image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'assets/uploads/kendaraans', $newName);
            $data['image'] = $newName;

            if ($kendaraan['image'] && file_exists(FCPATH . 'assets/uploads/kendaraans/' . $kendaraan['image'])) {
                unlink(FCPATH . 'assets/uploads/kendaraans/' . $kendaraan['image']);
            }
        }

        $this->kendaraanModel->update($id, $data);

        return redirect()->to('/admin/kendaraan')->with('success', 'Kendaraan berhasil diperbarui.');
    }

    /**
     * Menampilkan halaman cetak label barcode untuk sebuah unit Kendaraan.
     * Gunakan query string ?copies=N untuk mencetak beberapa salinan sekaligus.
     */
    public function printBarcode(int $id)
    {
        $kendaraan = $this->kendaraanModel->find($id);
        if (!$kendaraan) {
            return redirect()->to('/admin/kendaraan')->with('error', 'Kendaraan tidak ditemukan.');
        }

        return view('admin/kendaraans/print_barcode', ['kendaraan' => $kendaraan]);
    }

    public function delete(int $id)
    {
        $kendaraan = $this->kendaraanModel->find($id);
        if ($kendaraan) {
            if ($kendaraan['image'] && file_exists(FCPATH . 'assets/uploads/kendaraans/' . $kendaraan['image'])) {
                unlink(FCPATH . 'assets/uploads/kendaraans/' . $kendaraan['image']);
            }
            $this->kendaraanModel->delete($id);
        }
        return redirect()->to('/admin/kendaraan')->with('success', 'Kendaraan berhasil dihapus.');
    }
}
