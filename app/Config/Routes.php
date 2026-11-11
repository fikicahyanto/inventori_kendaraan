<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('katalog', 'Catalog::index');
$routes->get('katalog/(:num)', 'Catalog::detail/$1');

$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::attemptRegister');
$routes->get('logout', 'Auth::logout');

// ================= FRONTEND (CUSTOMER - LOGIN REQUIRED) =================
$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('cart', 'Cart::index');
    $routes->post('cart/add', 'Cart::add');
    $routes->post('cart/update', 'Cart::update');
    $routes->get('cart/remove/(:num)', 'Cart::remove/$1');
    $routes->post('cart/checkout', 'Cart::checkout');
    $routes->get('cart/count', 'Cart::count');

    $routes->get('rental-saya', 'Rental::index');
    $routes->get('rental-saya/(:num)', 'Rental::detail/$1');
    $routes->post('rental-saya/bayar/(:num)', 'Rental::uploadPayment/$1');
    $routes->get('rental-saya/batal/(:num)', 'Rental::cancel/$1');

    $routes->get('profil', 'Profile::index');
    $routes->post('profil/update', 'Profile::update');
    $routes->post('profil/password', 'Profile::updatePassword');
});

// ================= BACKEND (ADMIN - LOGIN + ROLE REQUIRED) =================
$routes->group('admin', ['filter' => 'admin', 'namespace' => 'App\Controllers\Admin'], function ($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->get('dashboard', 'Dashboard::index');

    // Kategori
    $routes->get('kategori', 'Categories::index');
    $routes->post('kategori/simpan', 'Categories::store');
    $routes->post('kategori/update/(:num)', 'Categories::update/$1');
    $routes->get('kategori/hapus/(:num)', 'Categories::delete/$1');

    // Kendaraan
    $routes->get('kendaraan', 'Kendaraans::index');
    $routes->get('kendaraan/tambah', 'Kendaraans::create');
    $routes->post('kendaraan/simpan', 'Kendaraans::store');
    $routes->get('kendaraan/edit/(:num)', 'Kendaraans::edit/$1');
    $routes->post('kendaraan/update/(:num)', 'Kendaraans::update/$1');
    $routes->get('kendaraan/hapus/(:num)', 'Kendaraans::delete/$1');
    $routes->get('kendaraan/cetak-barcode/(:num)', 'Kendaraans::printBarcode/$1');

    // Transaksi 
    $routes->get('transaksi', 'Rentals::index');
    $routes->get('transaksi/(:num)', 'Rentals::detail/$1');
    $routes->post('transaksi/approve/(:num)', 'Rentals::approve/$1');
    $routes->post('transaksi/reject/(:num)', 'Rentals::reject/$1');
    $routes->post('transaksi/hapus/(:num)', 'Rentals::delete/$1');
    $routes->post('transaksi/kembali/(:num)', 'Rentals::returned/$1');
    $routes->post('transaksi/verifikasi-ktp-peminjam/(:num)', 'Rentals::verifyBorrowerKtp/$1');
    $routes->post('transaksi/tolak-ktp-peminjam/(:num)', 'Rentals::rejectBorrowerKtp/$1');
    $routes->post('transaksi/verifikasi-ktp-penerima/(:num)', 'Rentals::verifyRecipientKtp/$1');
    $routes->post('transaksi/tolak-ktp-penerima/(:num)', 'Rentals::rejectRecipientKtp/$1');
    $routes->get('transaksi/tambah', 'Rentals::create');
    $routes->post('transaksi/simpan', 'Rentals::simpan');

    // Pembayaran
    $routes->get('pembayaran', 'Payments::index');
    $routes->post('pembayaran/verifikasi/(:num)', 'Payments::verify/$1');
    $routes->post('pembayaran/tolak/(:num)', 'Payments::reject/$1');

    // Pengguna
    $routes->get('pengguna', 'Users::index');
    $routes->post('pengguna/simpan', 'Users::store');
    $routes->post('pengguna/update/(:num)', 'Users::update/$1');
    $routes->get('pengguna/hapus/(:num)', 'Users::delete/$1');
    $routes->post('pengguna/verifikasi-ktp/(:num)', 'Users::verifyKtp/$1');
    $routes->post('pengguna/tolak-ktp/(:num)', 'Users::rejectKtp/$1');

    // Instansi pengguna kendaraan
    $routes->get('instansi', 'Instansis::index');
    $routes->post('instansi/simpan', 'Instansis::store');
    $routes->post('instansi/update/(:num)', 'Instansis::update/$1');
    $routes->get('instansi/hapus/(:num)', 'Instansis::delete/$1');

    // Pegawai penanggung jawab kendaraan
    $routes->get('pegawai', 'Pegawais::index');
    $routes->post('pegawai/simpan', 'Pegawais::store');
    $routes->post('pegawai/update/(:num)', 'Pegawais::update/$1');
    $routes->get('pegawai/hapus/(:num)', 'Pegawais::delete/$1');

    // Laporan
    $routes->get('laporan', 'Reports::index');
    $routes->get('laporan/export', 'Reports::export');
});
