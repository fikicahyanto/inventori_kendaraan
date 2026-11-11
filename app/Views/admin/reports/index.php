<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="table-card mb-4">
    <form action="<?= base_url('admin/laporan') ?>" method="get" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="start" class="form-control" value="<?= esc($start) ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="end" class="form-control" value="<?= esc($end) ?>">
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Tampilkan</button>
            <a href="<?= base_url('admin/laporan/export?start=' . $start . '&end=' . $end) ?>" class="btn btn-outline-success"><i class="fa-solid fa-file-csv"></i> Export CSV</a>
        </div>
    </form>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card bg-grad-2">
            <i class="fa-solid fa-sack-dollar"></i>
            <p class="mb-0">Total Pendapatan Periode Ini</p>
            <h3>Rp <?= number_format($totalRevenue, 0, ',', '.') ?></h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card bg-grad-1">
            <i class="fa-solid fa-file-invoice"></i>
            <p class="mb-0">Jumlah Transaksi</p>
            <h3><?= count($rentals) ?></h3>
        </div>
    </div>
</div>

<div class="table-card">
    <h6 class="fw-bold mb-3">Detail Transaksi (<?= date('d M Y', strtotime($start)) ?> - <?= date('d M Y', strtotime($end)) ?>)</h6>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Invoice</th><th>Customer</th><th>Tanggal Sewa</th><th>Total Hari</th><th>Subtotal</th><th>Denda</th><th>Total</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($rentals as $r): ?>
                <tr>
                    <td><?= esc($r['invoice_code']) ?></td>
                    <td><?= esc($r['customer_name']) ?></td>
                    <td><?= date('d M Y', strtotime($r['start_date'])) ?> - <?= date('d M Y', strtotime($r['end_date'])) ?></td>
                    <td><?= $r['total_days'] ?></td>
                    <td>Rp <?= number_format($r['subtotal'], 0, ',', '.') ?></td>
                    <td>Rp <?= number_format($r['late_fee'], 0, ',', '.') ?></td>
                    <td class="fw-semibold">Rp <?= number_format($r['total_price'], 0, ',', '.') ?></td>
                    <td><?= ucfirst($r['status']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($rentals)): ?>
                <tr><td colspan="8" class="text-center text-muted">Tidak ada transaksi pada periode ini.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
