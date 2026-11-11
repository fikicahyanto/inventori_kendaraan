<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? esc($title) . ' - Admin Inventori' : 'Admin Inventori' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="admin-body">

<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-laptop"></i> <span>Inventori</span>
        </div>
        <ul class="sidebar-menu">
            <?php $seg = service('uri')->getSegment(2) ?? 'dashboard'; ?>
            <li class="<?= $seg == 'dashboard' || $seg == '' ? 'active' : '' ?>">
                <a href="<?= base_url('admin/dashboard') ?>"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            </li>
            <li class="<?= $seg == 'kategori' ? 'active' : '' ?>">
                <a href="<?= base_url('admin/kategori') ?>"><i class="fa-solid fa-tags"></i> Kategori</a>
            </li>

            <li class="<?= $seg == 'kendaraan' ? 'active' : '' ?>">
                <a href="<?= base_url('admin/kendaraan') ?>"><i class="fa-solid fa-motorcycle"></i> Data Kendaraan</a>
            </li>
            <li class="<?= $seg == 'transaksi' ? 'active' : '' ?>">
                <a href="<?= base_url('admin/transaksi') ?>"><i class="fa-solid fa-file-invoice"></i> Penggunaan Barang</a>
            </li>
            <li class="<?= $seg == 'pembayaran' ? 'active' : '' ?>">
                <a href="<?= base_url('admin/pembayaran') ?>"><i class="fa-solid fa-money-check-dollar"></i> Pembayaran</a>
            </li>
            <li class="<?= $seg == 'pengguna' ? 'active' : '' ?>">
                <a href="<?= base_url('admin/pengguna') ?>"><i class="fa-solid fa-user-shield"></i> Akun Login</a>
            </li>
            <li class="<?= $seg == 'instansi' ? 'active' : '' ?>">
                <a href="<?= base_url('admin/instansi') ?>"><i class="fa-solid fa-building"></i> Instansi</a>
            </li>
            <li class="<?= $seg == 'pegawai' ? 'active' : '' ?>">
                <a href="<?= base_url('admin/pegawai') ?>"><i class="fa-solid fa-user-tie"></i> Pegawai</a>
            </li>
            <li class="<?= $seg == 'laporan' ? 'active' : '' ?>">
                <a href="<?= base_url('admin/laporan') ?>"><i class="fa-solid fa-chart-line"></i> Laporan</a>
            </li>
            <li class="mt-3">
                <a href="<?= base_url('/') ?>" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> Lihat Situs</a>
            </li>
            <li>
                <a href="<?= base_url('logout') ?>" class="text-danger"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            </li>
        </ul>
    </aside>

    <div class="admin-content">
        <nav class="admin-topbar">
            <button id="sidebarToggle" class="btn btn-sm btn-light d-lg-none"><i class="fa-solid fa-bars"></i></button>
            <h5 class="mb-0"><?= esc($title ?? 'Dashboard') ?></h5>
            <div class="admin-user">
                <i class="fa-solid fa-circle-user"></i> <?= esc(session()->get('name')) ?>
            </div>
        </nav>

        <div class="admin-body-content">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= esc(session()->getFlashdata('success')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= esc(session()->getFlashdata('error')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0 list-unstyled">
                    <?php foreach (session()->getFlashdata('errors') as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/script.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
