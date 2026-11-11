<?= $this->extend('layouts/frontend') ?>
<?= $this->section('content') ?>

<section class="hero-section">
    <div class="container text-center">
        <h1 class="display-5 mb-3">Sewa Laptop Berkualitas, Kapan Saja</h1>
        <p class="lead mb-4">Solusi rental laptop untuk kerja, kuliah, gaming, dan kebutuhan bisnis Anda. Proses cepat, harga terjangkau.</p>
        <a href="<?= base_url('katalog') ?>" class="btn btn-light btn-lg px-4"><i class="fa-solid fa-magnifying-glass"></i> Lihat Katalog</a>
    </div>
</section>

<section class="container py-5">
    <div class="row text-center g-4 mb-5">
        <div class="col-md-4">
            <i class="fa-solid fa-truck-fast fa-2x text-primary mb-2"></i>
            <h5 class="fw-bold">Proses Cepat</h5>
            <p class="text-muted">Pesan online, konfirmasi dalam hitungan menit.</p>
        </div>
        <div class="col-md-4">
            <i class="fa-solid fa-shield-halved fa-2x text-primary mb-2"></i>
            <h5 class="fw-bold">Terpercaya</h5>
            <p class="text-muted">Unit terawat dan siap pakai, garansi selama masa sewa.</p>
        </div>
        <div class="col-md-4">
            <i class="fa-solid fa-tags fa-2x text-primary mb-2"></i>
            <h5 class="fw-bold">Harga Bersaing</h5>
            <p class="text-muted">Tarif harian transparan tanpa biaya tersembunyi.</p>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="section-title">Laptop Unggulan</h3>
        <a href="<?= base_url('katalog') ?>" class="btn btn-outline-primary btn-sm">Lihat Semua <i class="fa-solid fa-arrow-right"></i></a>
    </div>

    <div class="row g-4">
        <?php foreach (array_slice($featured, 0, 6) as $laptop): ?>
        <div class="col-md-4 col-lg-4">
            <div class="card laptop-card">
                <?php if (!empty($laptop['image'])): ?>
                    <img src="<?= base_url('assets/uploads/laptops/' . $laptop['image']) ?>" alt="<?= esc($laptop['name']) ?>">
                <?php else: ?>
                    <div class="img-placeholder"><i class="fa-solid fa-laptop"></i></div>
                <?php endif; ?>
                <div class="card-body">
                    <span class="badge bg-light text-dark mb-2"><?= esc($laptop['category_name']) ?></span>
                    <h6 class="fw-bold mb-1"><?= esc($laptop['name']) ?></h6>
                    <p class="text-muted small mb-2"><?= esc($laptop['brand']) ?> &bull; <?= esc($laptop['ram']) ?> &bull; <?= esc($laptop['storage']) ?></p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="price-tag">Rp <?= number_format($laptop['price_per_day'], 0, ',', '.') ?>/hari</span>
                        <a href="<?= base_url('katalog/' . $laptop['id']) ?>" class="btn btn-sm btn-primary">Detail</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($featured)): ?>
            <p class="text-center text-muted">Belum ada laptop tersedia saat ini.</p>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>
