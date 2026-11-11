<?php

namespace App\Controllers;

use App\Models\LaptopModel;
use App\Models\CategoryModel;

class Catalog extends BaseController
{
    protected LaptopModel $laptopModel;
    protected CategoryModel $categoryModel;

    public function __construct()
    {
        $this->laptopModel   = new LaptopModel();
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $keyword    = $this->request->getGet('q');
        $categoryId = $this->request->getGet('category');
        $sort       = $this->request->getGet('sort');

        $data = [
            'title'      => 'Katalog Laptop',
            'laptops'    => $this->laptopModel->search($keyword, $categoryId, $sort),
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
        $laptop = $this->laptopModel->getWithCategory($id);

        if (!$laptop) {
            return redirect()->to('/katalog')->with('error', 'Laptop tidak ditemukan.');
        }

        $data = [
            'title'  => $laptop['name'],
            'laptop' => $laptop,
        ];

        return view('catalog/detail', $data);
    }
}
