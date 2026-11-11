<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KendaraanModel;
use App\Models\InstansiModel;
use App\Models\PegawaiModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $kendaraanModel = new KendaraanModel();
        $instansiModel  = new InstansiModel();
        $pegawaiModel   = new PegawaiModel();
        $db = db_connect();

        $data = [
            'title'            => 'Dashboard Admin',
            'totalKendaraan'   => $kendaraanModel->countAllResults(),
            'totalBaik'        => $kendaraanModel->where('status', 'baik')->countAllResults(),
            'totalInstansi'    => $instansiModel->where('status', 'active')->countAllResults(),
            'totalPegawai'     => $pegawaiModel->where('status', 'active')->countAllResults(),
            'totalPenempatan'  => $db->table('penempatan_kendaraan')->where('status', 'assigned')->countAllResults(),
        ];

        return view('admin/dashboard', $data);
    }
}
