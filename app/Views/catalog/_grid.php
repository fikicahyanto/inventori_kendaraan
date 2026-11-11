<div class="row g-4">
    <?php foreach ($laptops as $laptop): ?>
    <div class="col-md-4 col-lg-3">
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
                    <span class="price-tag">Rp <?= number_format($laptop['price_per_day'], 0, ',', '.') ?>/hari</span><br><small class="text-muted">Rp <?= number_format($laptop['price_per_month'], 0, ',', '.') ?>/bulan</small>
                    <a href="<?= base_url('katalog/' . $laptop['id']) ?>" class="btn btn-sm btn-primary">Detail</a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($laptops)): ?>
        <div class="col-12 text-center text-muted py-5">
            <i class="fa-solid fa-magnifying-glass fa-2x mb-3"></i>
            <p>Tidak ada laptop yang cocok dengan pencarian Anda.</p>
        </div>
    <?php endif; ?>
</div>
