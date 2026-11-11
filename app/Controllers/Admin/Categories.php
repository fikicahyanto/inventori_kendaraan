<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

class Categories extends BaseController
{
    protected CategoryModel $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $data = [
            'title'      => 'Manajemen Kategori',
            'categories' => $this->categoryModel->orderBy('id', 'DESC')->findAll(),
        ];
        return view('admin/categories/index', $data);
    }

    public function store()
    {
        if (!$this->validate($this->categoryModel->validationRules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $this->categoryModel->insert([
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ]);

        return redirect()->to('/admin/kategori')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $this->categoryModel->update($id, [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ]);

        return redirect()->to('/admin/kategori')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $this->categoryModel->delete($id);
        return redirect()->to('/admin/kategori')->with('success', 'Kategori berhasil dihapus.');
    }
}
