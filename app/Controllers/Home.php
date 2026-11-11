<?php

namespace App\Controllers;

use App\Models\KendaraanModel;
use App\Models\CategoryModel;

class Home extends BaseController
{
    public function index()
    {
        $kendaraanModel = new KendaraanModel();
        $categoryModel = new CategoryModel();

        $data = [
            'title'      => 'Beranda',
            'featured'   => $kendaraanModel->search(null, null, null),
            'categories' => $categoryModel->findAll(),
        ];

        // Batasi laptop unggulan ke 6 item di view
        return view('home/index', $data);
    }
}
