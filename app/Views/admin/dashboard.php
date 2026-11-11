<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="stat-card bg-grad-1">
        <i class="fa-solid fa-car"></i>
            <p class="mb-0">Total Kendaraan</p>
            <h3><?= $totalKendaraan ?></h3>
            <small><?= $totalBaik ?> kondisi baik</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card bg-grad-2">
            <i class="fa-solid fa-building"></i>
            <p class="mb-0">Total Instansi</p>
            <h3><?= $totalInstansi ?></h3>
            <small>Instansi aktif</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card bg-grad-3">
            <i class="fa-solid fa-user-tie"></i>
            <p class="mb-0">Total Pegawai</p>
            <h3><?= $totalPegawai ?></h3>
            <small>Pegawai aktif</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card bg-grad-4">
            <i class="fa-solid fa-clipboard-check"></i>
            <p class="mb-0">Kendaraan Digunakan</p>
            <h3><?= $totalPenempatan ?></h3>
            <small>Penempatan aktif</small>
        </div>
    </div>
</div>

<div class="table-card">
    <h6 class="fw-bold mb-3">Ringkasan Sistem Inventori</h6>
    <p class="text-muted mb-0">Gunakan menu Kendaraan, Pegawai, dan Instansi untuk mengelola data inventori.</p>
</div>

<?= $this->endSection() ?>
