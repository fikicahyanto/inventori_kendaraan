<?= $this->extend('layouts/frontend') ?>
<?= $this->section('content') ?>

<div class="container py-5">
    <?php
    $statusMap = [
        'pending'   => ['warning', 'Menunggu Konfirmasi Admin'],
        'approved'  => ['info', 'Disetujui - Silakan Lakukan Pembayaran'],
        'rejected'  => ['danger', 'Ditolak'],
        'ongoing'   => ['primary', 'Sedang Berjalan'],
        'returned'  => ['success', 'Selesai'],
        'cancelled' => ['secondary', 'Dibatalkan'],
    ];
    [$color, $label] = $statusMap[$rental['status']] ?? ['secondary', $rental['status']];
    ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="section-title mb-0">Invoice #<?= esc($rental['invoice_code']) ?></h3>
        <span class="badge bg-<?= $color ?> fs-6 px-3 py-2"><?= $label ?></span>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="table-card mb-4">
                <h6 class="fw-bold mb-3">Item yang Disewa</h6>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>Laptop</th><th>Qty</th><th><?= ($rental['rental_type'] ?? 'daily') === 'monthly' ? 'Harga/Bulan' : 'Harga/Hari' ?></th><th>Subtotal</th></tr></thead>
                        <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= esc($item['name']) ?> <br><small class="text-muted"><?= esc($item['code']) ?></small></td>
                                <td><?= $item['qty'] ?></td>
                                <td>Rp <?= number_format(($rental['rental_type'] ?? 'daily') === 'monthly' ? $item['price_per_month'] : $item['price_per_day'], 0, ',', '.') ?></td>
                                <td>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <table class="table table-borderless w-auto ms-auto text-end">
                    <tr><td class="fw-semibold">Subtotal</td><td>Rp <?= number_format($rental['subtotal'], 0, ',', '.') ?></td></tr>
                    <?php if ($rental['late_fee'] > 0): ?>
                    <tr><td class="fw-semibold text-danger">Denda Keterlambatan</td><td class="text-danger">Rp <?= number_format($rental['late_fee'], 0, ',', '.') ?></td></tr>
                    <?php endif; ?>
                    <tr><td class="fw-bold fs-5">Total</td><td class="fw-bold fs-5 text-primary">Rp <?= number_format($rental['total_price'], 0, ',', '.') ?></td></tr>
                </table>
            </div>

            <?php if ($rental['status'] === 'approved'): ?>
            <div class="table-card">
                <h6 class="fw-bold mb-3"><i class="fa-solid fa-money-check-dollar"></i> Upload Bukti Pembayaran</h6>
                <form action="<?= base_url('rental-saya/bayar/' . $rental['id']) ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Metode Pembayaran</label>
                        <select name="payment_method" class="form-select">
                            <option>Transfer Bank</option>
                            <option>E-Wallet</option>
                            <option>Tunai</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bukti Transfer (gambar, max 2MB)</label>
                        <input type="file" name="proof_image" class="form-control input-image-preview" data-preview="#previewImg" accept="image/*" required>
                    </div>
                    <img src="" id="previewImg" class="d-none img-fluid rounded mb-3" style="max-height:200px;">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-upload"></i> Kirim Bukti Bayar</button>
                </form>
            </div>
            <?php endif; ?>

            <?php if (!empty($payments)): ?>
            <div class="table-card mt-4">
                <h6 class="fw-bold mb-3">Riwayat Pembayaran</h6>
                <?php foreach ($payments as $p): ?>
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <div>
                            <div class="fw-semibold">Rp <?= number_format($p['amount'], 0, ',', '.') ?> - <?= esc($p['payment_method']) ?></div>
                            <small class="text-muted"><?= date('d M Y H:i', strtotime($p['paid_at'])) ?></small>
                        </div>
                        <?php
                        $pColor = ['pending' => 'warning', 'verified' => 'success', 'rejected' => 'danger'][$p['status']];
                        ?>
                        <span class="badge bg-<?= $pColor ?>"><?= ucfirst($p['status']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <div class="table-card mb-4">
                <h6 class="fw-bold mb-3">Informasi Sewa</h6>
                <table class="table table-borderless table-sm">
                    <tr><td class="text-muted">Mulai</td><td><?= date('d M Y', strtotime($rental['start_date'])) ?></td></tr>
                    <tr><td class="text-muted">Selesai</td><td><?= date('d M Y', strtotime($rental['end_date'])) ?></td></tr>
                    <tr><td class="text-muted">Total Hari</td><td><?= $rental['total_days'] ?> hari</td></tr>
                    <tr><td class="text-muted">Jenis Sewa</td><td><?= ($rental['rental_type'] ?? 'daily') === 'monthly' ? 'Bulanan (' . max(1, (int) ceil($rental['total_days'] / 30)) . ' bulan)' : 'Harian' ?></td></tr>
                    <?php if ($rental['notes']): ?>
                    <tr><td class="text-muted">Catatan</td><td><?= esc($rental['notes']) ?></td></tr>
                    <?php endif; ?>
                </table>

                <?php if (in_array($rental['status'], ['pending', 'approved'])): ?>
                <a href="<?= base_url('rental-saya/batal/' . $rental['id']) ?>" class="btn btn-outline-danger w-100 btn-confirm-delete" data-message="Batalkan pesanan sewa ini?">
                    <i class="fa-solid fa-ban"></i> Batalkan Pesanan
                </a>
                <?php endif; ?>
            </div>

            <div class="table-card">
                <h6 class="fw-bold mb-3"><i class="fa-solid fa-id-card"></i> Identitas Pengambil Unit</h6>
                <?php if ($rental['is_self_pickup'] === 'yes'): ?>
                    <p class="mb-1"><span class="badge bg-info">Diambil Sendiri</span></p>
                    <p class="text-muted small mb-0">Unit diambil langsung oleh pemilik akun. Pastikan KTP di halaman <a href="<?= base_url('profil') ?>">Profil</a> sudah terverifikasi sebelum jadwal pengambilan.</p>
                <?php else: ?>
                    <p class="mb-2"><span class="badge bg-warning text-dark">Diwakilkan</span></p>
                    <table class="table table-borderless table-sm">
                        <tr><td class="text-muted">Nama</td><td><?= esc($rental['recipient_name']) ?></td></tr>
                        <tr><td class="text-muted">No. HP</td><td><?= esc($rental['recipient_phone']) ?></td></tr>
                        <tr><td class="text-muted">Hubungan</td><td><?= esc($rental['recipient_relation'] ?: '-') ?></td></tr>
                        <tr><td class="text-muted">No. KTP</td><td><?= esc($rental['recipient_ktp_number']) ?></td></tr>
                    </table>
                    <?php
                        $rColor = ['unverified' => 'secondary', 'verified' => 'success', 'rejected' => 'danger'][$rental['recipient_ktp_verified']];
                        $rLabel = ['unverified' => 'Menunggu Verifikasi', 'verified' => 'KTP Terverifikasi', 'rejected' => 'KTP Ditolak'][$rental['recipient_ktp_verified']];
                    ?>
                    <span class="badge bg-<?= $rColor ?>"><?= $rLabel ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
