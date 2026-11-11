<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <input type="text" class="form-control table-search w-auto" data-target="#tblPayment" placeholder="Cari pembayaran...">
    <div class="btn-group">
        <a href="<?= base_url('admin/pembayaran') ?>" class="btn btn-sm btn-outline-secondary <?= !$status ? 'active' : '' ?>">Semua</a>
        <a href="<?= base_url('admin/pembayaran?status=pending') ?>" class="btn btn-sm btn-outline-warning <?= $status == 'pending' ? 'active' : '' ?>">Pending</a>
        <a href="<?= base_url('admin/pembayaran?status=verified') ?>" class="btn btn-sm btn-outline-success <?= $status == 'verified' ? 'active' : '' ?>">Terverifikasi</a>
        <a href="<?= base_url('admin/pembayaran?status=rejected') ?>" class="btn btn-sm btn-outline-danger <?= $status == 'rejected' ? 'active' : '' ?>">Ditolak</a>
    </div>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table align-middle" id="tblPayment">
            <thead><tr><th>Bukti</th><th>Invoice</th><th>Customer</th><th>Jumlah</th><th>Metode</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($payments as $p): ?>
                <?php $pColor = ['pending' => 'warning', 'verified' => 'success', 'rejected' => 'danger'][$p['status']]; ?>
                <tr>
                    <td>
                        <?php if (!empty($p['proof_image'])): ?>
                            <a href="<?= base_url('assets/uploads/payments/' . $p['proof_image']) ?>" target="_blank">
                                <img src="<?= base_url('assets/uploads/payments/' . $p['proof_image']) ?>" style="width:60px;height:42px;object-fit:cover;border-radius:6px;">
                            </a>
                        <?php endif; ?>
                    </td>
                    <td class="fw-semibold"><?= esc($p['invoice_code']) ?></td>
                    <td><?= esc($p['customer_name']) ?></td>
                    <td>Rp <?= number_format($p['amount'], 0, ',', '.') ?></td>
                    <td><?= esc($p['payment_method']) ?></td>
                    <td><?= date('d M Y H:i', strtotime($p['paid_at'])) ?></td>
                    <td><span class="badge bg-<?= $pColor ?>"><?= ucfirst($p['status']) ?></span></td>
                    <td>
                        <?php if ($p['status'] === 'pending'): ?>
                            <form action="<?= base_url('admin/pembayaran/verifikasi/' . $p['id']) ?>" method="post" class="d-inline">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-success"><i class="fa-solid fa-check"></i></button>
                            </form>
                            <form action="<?= base_url('admin/pembayaran/tolak/' . $p['id']) ?>" method="post" class="d-inline">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-danger"><i class="fa-solid fa-xmark"></i></button>
                            </form>
                        <?php else: ?>
                            <span class="text-muted small">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($payments)): ?>
                <tr><td colspan="8" class="text-center text-muted">Belum ada data pembayaran.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
