<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div class="btn-group">
        <a href="<?= base_url('admin/transaksi') ?>" class="btn btn-sm btn-outline-secondary <?= !$status ? 'active' : '' ?>">Semua</a>
        <a href="<?= base_url('admin/transaksi?status=pending') ?>" class="btn btn-sm btn-outline-warning <?= $status == 'pending' ? 'active' : '' ?>">Pending</a>
        <a href="<?= base_url('admin/transaksi?status=approved') ?>" class="btn btn-sm btn-outline-info <?= $status == 'approved' ? 'active' : '' ?>">Disetujui</a>
        <a href="<?= base_url('admin/transaksi?status=ongoing') ?>" class="btn btn-sm btn-outline-primary <?= $status == 'ongoing' ? 'active' : '' ?>">Berjalan</a>
        <a href="<?= base_url('admin/transaksi?status=returned') ?>" class="btn btn-sm btn-outline-success <?= $status == 'returned' ? 'active' : '' ?>">Selesai</a>
    </div>
    <div class="d-flex gap-2">
    <input type="text" class="form-control table-search w-auto" data-target="#tblRental" placeholder="Cari transaksi...">
        <a href="<?= base_url('admin/transaksi/tambah') ?>" class="btn btn-primary text-nowrap">
            <i class="fa-solid fa-plus"></i> Input Penggunaaan Barang
        </a>
    </div>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table align-middle" id="tblRental">
            <thead><tr><th>Invoice</th><th>Pegawai</th><th>Tanggal Penggunaan</th><th>Total</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($penggunaans as $r): ?>
                <?php
                $statusMap = [
                    'pending' => 'warning', 'approved' => 'info', 'rejected' => 'danger',
                    'ongoing' => 'primary', 'returned' => 'success', 'cancelled' => 'secondary',
                ];
                ?>
                <tr>
                    <td class="fw-semibold"><?= esc($r['invoice_code']) ?></td>
                    <td><?= esc($r['customer_name']) ?><br><small class="text-muted"><?= esc($r['email']) ?></small></td>
                    <td><?= date('d M Y', strtotime($r['start_date'])) ?> - <?= date('d M Y', strtotime($r['end_date'])) ?></td>
                    <td>Rp <?= number_format($r['total_price'], 0, ',', '.') ?></td>
                    <td><span class="badge bg-<?= $statusMap[$r['status']] ?>"><?= ucfirst($r['status']) ?></span></td>
                    <td class="text-nowrap">
                        <a href="<?= base_url('admin/transaksi/' . $r['id']) ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                        <form action="<?= base_url('admin/transaksi/hapus/' . $r['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus transaksi <?= esc($r['invoice_code'], 'js') ?>? Data pembayaran dan item transaksinya juga akan dihapus.');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i> Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($penggunaan)): ?>
                <tr><td colspan="6" class="text-center text-muted">Tidak ada data transaksi.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
