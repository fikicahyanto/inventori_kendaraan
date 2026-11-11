<?php

namespace App\Controllers;

use App\Models\LaptopModel;
use App\Models\CategoryModel;

class Home extends BaseController
{
    public function index()
    {
        $laptopModel   = new LaptopModel();
        $categoryModel = new CategoryModel();

        $data = [
            'title'      => 'Beranda',
            'featured'   => $laptopModel->search(null, null, null),
            'categories' => $categoryModel->findAll(),
        ];

        // Batasi laptop unggulan ke 6 item di view
        return view('home/index', $data);
    }
}
