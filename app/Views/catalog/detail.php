<?= $this->extend('layouts/frontend') ?>
<?= $this->section('content') ?>

<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Beranda</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('katalog') ?>">Katalog</a></li>
            <li class="breadcrumb-item active"><?= esc($laptop['name']) ?></li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-md-5">
            <?php if (!empty($laptop['image'])): ?>
                <img src="<?= base_url('assets/uploads/laptops/' . $laptop['image']) ?>" class="img-fluid rounded-4 shadow-sm w-100" alt="<?= esc($laptop['name']) ?>">
            <?php else: ?>
                <div class="img-placeholder rounded-4" style="height:320px;"><i class="fa-solid fa-laptop fa-3x"></i></div>
            <?php endif; ?>
        </div>
        <div class="col-md-7">
            <span class="badge bg-primary-subtle text-primary mb-2"><?= esc($laptop['category_name']) ?></span>
            <h2 class="fw-bold"><?= esc($laptop['name']) ?></h2>
            <p class="text-muted"><?= esc($laptop['brand']) ?> &bull; Kode Unit: <?= esc($laptop['code']) ?></p>

            <span class="badge badge-status-<?= $laptop['status'] ?> px-3 py-2 mb-3 d-inline-block">
                <?= $laptop['status'] === 'available' ? 'Tersedia' : ($laptop['status'] === 'maintenance' ? 'Maintenance' : 'Tidak Aktif') ?>
            </span>

            <table class="table table-borderless w-auto mb-3">
                <tr><td class="fw-semibold">Processor</td><td>: <?= esc($laptop['processor'] ?: '-') ?></td></tr>
                <tr><td class="fw-semibold">RAM</td><td>: <?= esc($laptop['ram'] ?: '-') ?></td></tr>
                <tr><td class="fw-semibold">Storage</td><td>: <?= esc($laptop['storage'] ?: '-') ?></td></tr>
                <tr><td class="fw-semibold">Layar</td><td>: <?= esc($laptop['screen_size'] ?: '-') ?></td></tr>
                <tr><td class="fw-semibold">Stok Unit</td><td>: <?= (int) $laptop['stock'] ?> unit</td></tr>
            </table>

            <?php if (!empty($laptop['specification'])): ?>
                <p><?= nl2br(esc($laptop['specification'])) ?></p>
            <?php endif; ?>

            <h4 class="price-tag mb-1">Rp <?= number_format($laptop['price_per_day'], 0, ',', '.') ?> <span class="fs-6 text-muted fw-normal">/ hari</span></h4>
            <p class="text-muted mb-3">Rp <?= number_format($laptop['price_per_month'], 0, ',', '.') ?> / bulan</p>

            <?php if (session()->get('logged_in') && $laptop['status'] === 'available'): ?>
                <form class="form-add-cart d-flex gap-2 align-items-center" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="laptop_id" value="<?= $laptop['id'] ?>">
                    <input type="number" name="qty" class="form-control" style="width:90px" value="1" min="1" max="<?= $laptop['stock'] ?>">
                    <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-cart-plus"></i> Tambah ke Keranjang</button>
                </form>
            <?php elseif (!session()->get('logged_in')): ?>
                <a href="<?= base_url('login') ?>" class="btn btn-primary"><i class="fa-solid fa-right-to-bracket"></i> Login untuk Menyewa</a>
            <?php else: ?>
                <button class="btn btn-secondary" disabled>Tidak Tersedia</button>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
