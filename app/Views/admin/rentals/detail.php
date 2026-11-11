<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php
$statusMap = [
    'pending' => 'warning', 'approved' => 'info', 'rejected' => 'danger',
    'ongoing' => 'primary', 'returned' => 'success', 'cancelled' => 'secondary',
];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Invoice #<?= esc($rental['invoice_code']) ?></h5>
    <span class="badge bg-<?= $statusMap[$rental['status']] ?> fs-6 px-3 py-2"><?= ucfirst($rental['status']) ?></span>
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
                <tr><td class="fw-semibold text-danger">Denda</td><td class="text-danger">Rp <?= number_format($rental['late_fee'], 0, ',', '.') ?></td></tr>
                <?php endif; ?>
                <tr><td class="fw-bold fs-5">Total</td><td class="fw-bold fs-5 text-primary">Rp <?= number_format($rental['total_price'], 0, ',', '.') ?></td></tr>
            </table>
        </div>

        <?php if (!empty($payments)): ?>
        <div class="table-card">
            <h6 class="fw-bold mb-3">Bukti Pembayaran</h6>
            <?php foreach ($payments as $p): ?>
                <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                    <div class="d-flex align-items-center gap-3">
                        <?php if (!empty($p['proof_image'])): ?>
                            <a href="<?= base_url('assets/uploads/payments/' . $p['proof_image']) ?>" target="_blank">
                                <img src="<?= base_url('assets/uploads/payments/' . $p['proof_image']) ?>" style="width:70px;height:50px;object-fit:cover;border-radius:6px;">
                            </a>
                        <?php endif; ?>
                        <div>
                            <div class="fw-semibold">Rp <?= number_format($p['amount'], 0, ',', '.') ?> - <?= esc($p['payment_method']) ?></div>
                            <small class="text-muted"><?= date('d M Y H:i', strtotime($p['paid_at'])) ?></small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <?php $pColor = ['pending' => 'warning', 'verified' => 'success', 'rejected' => 'danger'][$p['status']]; ?>
                        <span class="badge bg-<?= $pColor ?>"><?= ucfirst($p['status']) ?></span>
                        <?php if ($p['status'] === 'pending'): ?>
                            <form action="<?= base_url('admin/pembayaran/verifikasi/' . $p['id']) ?>" method="post" class="d-inline">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-success"><i class="fa-solid fa-check"></i></button>
                            </form>
                            <form action="<?= base_url('admin/pembayaran/tolak/' . $p['id']) ?>" method="post" class="d-inline">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-danger"><i class="fa-solid fa-xmark"></i></button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <div class="table-card mb-4">
            <h6 class="fw-bold mb-3">Data Customer</h6>
            <table class="table table-borderless table-sm">
                <tr><td class="text-muted">Nama</td><td><?= esc($rental['customer_name']) ?></td></tr>
                <tr><td class="text-muted">Email</td><td><?= esc($rental['email']) ?></td></tr>
                <tr><td class="text-muted">No. HP</td><td><?= esc($rental['phone']) ?></td></tr>
                <tr><td class="text-muted">Alamat</td><td><?= esc($rental['address']) ?></td></tr>
            </table>
        </div>

        <div class="table-card mb-4">
            <h6 class="fw-bold mb-3"><i class="fa-solid fa-id-card"></i> Verifikasi KTP Peminjam</h6>
            <?php
                $bColor = ['unverified' => 'secondary', 'verified' => 'success', 'rejected' => 'danger'][$rental['borrower_ktp_verified'] ?? 'unverified'];
                $bLabel = ['unverified' => 'Belum Diverifikasi', 'verified' => 'Terverifikasi', 'rejected' => 'Ditolak'][$rental['borrower_ktp_verified'] ?? 'unverified'];
            ?>
            <p class="mb-2">Status: <span class="badge bg-<?= $bColor ?>"><?= $bLabel ?></span></p>
            <p class="mb-2 small">No. KTP: <strong><?= esc($rental['borrower_ktp_number'] ?: '-') ?></strong></p>

            <?php if (!empty($rental['borrower_ktp_photo'])): ?>
                <a href="<?= base_url('assets/uploads/ktp/' . $rental['borrower_ktp_photo']) ?>" target="_blank">
                    <img src="<?= base_url('assets/uploads/ktp/' . $rental['borrower_ktp_photo']) ?>" class="img-fluid rounded border mb-2" style="max-height:160px;">
                </a>
            <?php else: ?>
                <p class="text-muted small mb-2">Peminjam belum mengunggah foto KTP di profilnya.</p>
            <?php endif; ?>

            <?php if (!empty($rental['borrower_ktp_photo']) && $rental['borrower_ktp_verified'] !== 'verified'): ?>
                <form action="<?= base_url('admin/transaksi/verifikasi-ktp-peminjam/' . $rental['id']) ?>" method="post" class="d-inline">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-success"><i class="fa-solid fa-check"></i> Verifikasi</button>
                </form>
            <?php endif; ?>
            <?php if (!empty($rental['borrower_ktp_photo']) && $rental['borrower_ktp_verified'] !== 'rejected'): ?>
                <form action="<?= base_url('admin/transaksi/tolak-ktp-peminjam/' . $rental['id']) ?>" method="post" class="d-inline">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-xmark"></i> Tolak</button>
                </form>
            <?php endif; ?>
        </div>

        <?php if ($rental['is_self_pickup'] === 'no'): ?>
        <div class="table-card mb-4">
            <h6 class="fw-bold mb-3"><i class="fa-solid fa-user-check"></i> Verifikasi KTP Penerima (Diwakilkan)</h6>
            <?php
                $rColor = ['unverified' => 'secondary', 'verified' => 'success', 'rejected' => 'danger'][$rental['recipient_ktp_verified']];
                $rLabel = ['unverified' => 'Belum Diverifikasi', 'verified' => 'Terverifikasi', 'rejected' => 'Ditolak'][$rental['recipient_ktp_verified']];
            ?>
            <table class="table table-borderless table-sm mb-2">
                <tr><td class="text-muted">Nama</td><td><?= esc($rental['recipient_name']) ?></td></tr>
                <tr><td class="text-muted">No. HP</td><td><?= esc($rental['recipient_phone']) ?></td></tr>
                <tr><td class="text-muted">Hubungan</td><td><?= esc($rental['recipient_relation'] ?: '-') ?></td></tr>
                <tr><td class="text-muted">No. KTP</td><td><?= esc($rental['recipient_ktp_number']) ?></td></tr>
            </table>
            <p class="mb-2">Status: <span class="badge bg-<?= $rColor ?>"><?= $rLabel ?></span></p>

            <?php if (!empty($rental['recipient_ktp_photo'])): ?>
                <a href="<?= base_url('assets/uploads/ktp/' . $rental['recipient_ktp_photo']) ?>" target="_blank">
                    <img src="<?= base_url('assets/uploads/ktp/' . $rental['recipient_ktp_photo']) ?>" class="img-fluid rounded border mb-2" style="max-height:160px;">
                </a>
            <?php endif; ?>

            <?php if ($rental['recipient_ktp_verified'] !== 'verified'): ?>
                <form action="<?= base_url('admin/transaksi/verifikasi-ktp-penerima/' . $rental['id']) ?>" method="post" class="d-inline">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-success"><i class="fa-solid fa-check"></i> Verifikasi</button>
                </form>
            <?php endif; ?>
            <?php if ($rental['recipient_ktp_verified'] !== 'rejected'): ?>
                <form action="<?= base_url('admin/transaksi/tolak-ktp-penerima/' . $rental['id']) ?>" method="post" class="d-inline">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-xmark"></i> Tolak</button>
                </form>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="table-card">
            <h6 class="fw-bold mb-3">Aksi Transaksi</h6>

            <?php if ($rental['status'] === 'pending'): ?>
                <?php if ($rental['borrower_ktp_verified'] !== 'verified' || ($rental['is_self_pickup'] === 'no' && $rental['recipient_ktp_verified'] !== 'verified')): ?>
                    <div class="alert alert-warning small py-2">
                        <i class="fa-solid fa-triangle-exclamation"></i> KTP belum sepenuhnya terverifikasi. Disarankan verifikasi identitas terlebih dahulu sebelum menyetujui transaksi.
                    </div>
                <?php endif; ?>
                <form action="<?= base_url('admin/transaksi/approve/' . $rental['id']) ?>" method="post" class="mb-2">
                    <?= csrf_field() ?>
                    <button class="btn btn-success w-100"><i class="fa-solid fa-check"></i> Setujui Transaksi</button>
                </form>
                <form action="<?= base_url('admin/transaksi/reject/' . $rental['id']) ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="text" name="reason" class="form-control mb-2" placeholder="Alasan penolakan (opsional)">
                    <button class="btn btn-outline-danger w-100"><i class="fa-solid fa-xmark"></i> Tolak Transaksi</button>
                </form>
            <?php elseif (in_array($rental['status'], ['ongoing', 'approved'])): ?>
                <form action="<?= base_url('admin/transaksi/kembali/' . $rental['id']) ?>" method="post">
                    <?= csrf_field() ?>
                    <label class="form-label">Tanggal Pengembalian</label>
                    <input type="date" name="actual_return_date" class="form-control mb-2" value="<?= date('Y-m-d') ?>">
                    <button class="btn btn-primary w-100"><i class="fa-solid fa-box"></i> Tandai Selesai / Dikembalikan</button>
                    <small class="text-muted d-block mt-2">Denda otomatis dihitung jika melewati tanggal <?= date('d M Y', strtotime($rental['end_date'])) ?>.</small>
                </form>
            <?php else: ?>
                <p class="text-muted mb-0">Tidak ada aksi tersedia untuk status ini.</p>
            <?php endif; ?>

            <hr>
            <form action="<?= base_url('admin/transaksi/hapus/' . $rental['id']) ?>" method="post" onsubmit="return confirm('Hapus transaksi <?= esc($rental['invoice_code'], 'js') ?>? Data pembayaran dan item transaksinya juga akan dihapus.');">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-outline-danger w-100"><i class="fa-solid fa-trash"></i> Hapus Transaksi</button>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
