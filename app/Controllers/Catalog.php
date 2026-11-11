<?php

namespace App\Controllers;

use App\Models\KendaraanModel;
use App\Models\CategoryModel;

class Catalog extends BaseController
{
    protected KendaraanModel $kendaraanModel;
    protected CategoryModel $categoryModel;

    public function __construct()
    {
        $this->kendaraanModel = new KendaraanModel();
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $keyword    = $this->request->getGet('q');
        $categoryId = $this->request->getGet('category');
        $sort       = $this->request->getGet('sort');

        $data = [
            'title'      => 'Katalog Kendaraan',
            'kendaraans' => $this->kendaraanModel->search($keyword, $categoryId, null),
            'categories' => $this->categoryModel->findAll(),
            'keyword'    => $keyword,
            'categoryId' => $categoryId,
            'sort'       => $sort,
        ];

        // Jika request AJAX, kembalikan partial view untuk live search
        if ($this->request->isAJAX()) {
            return view('catalog/_grid', $data);
        }

        return view('catalog/index', $data);
    }

    public function detail(int $id)
    {
        $kendaraan = $this->kendaraanModel->getWithCategory($id);

        if (!$kendaraan) {
            return redirect()->to('/katalog')->with('error', 'Kendaraan tidak ditemukan.');
        }

        $data = [
            'title' => $kendaraan['name'],
            'kendaraan' => $kendaraan,
        ];

        return view('catalog/detail', $data);
    }
}
