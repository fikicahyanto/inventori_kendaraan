<?= $this->extend('layouts/frontend') ?>
<?= $this->section('content') ?>

<div class="container py-5">
    <h3 class="section-title mb-4">Rental Saya</h3>

    <?php if (empty($rentals)): ?>
        <div class="text-center py-5">
            <i class="fa-solid fa-file-invoice fa-3x text-muted mb-3"></i>
            <p class="text-muted">Anda belum memiliki riwayat rental.</p>
            <a href="<?= base_url('katalog') ?>" class="btn btn-primary">Sewa Laptop Sekarang</a>
        </div>
    <?php else: ?>
        <div class="table-card">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Tanggal Sewa</th>
                            <th>Total Hari</th>
                            <th>Total Bayar</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rentals as $r): ?>
                        <?php
                        $statusMap = [
                            'pending'   => ['warning', 'Menunggu Konfirmasi'],
                            'approved'  => ['info', 'Disetujui - Menunggu Bayar'],
                            'rejected'  => ['danger', 'Ditolak'],
                            'ongoing'   => ['primary', 'Sedang Berjalan'],
                            'returned'  => ['success', 'Selesai'],
                            'cancelled' => ['secondary', 'Dibatalkan'],
                        ];
                        [$color, $label] = $statusMap[$r['status']] ?? ['secondary', $r['status']];
                        ?>
                        <tr>
                            <td class="fw-semibold"><?= esc($r['invoice_code']) ?></td>
                            <td><?= date('d M Y', strtotime($r['start_date'])) ?> - <?= date('d M Y', strtotime($r['end_date'])) ?></td>
                            <td><?= $r['total_days'] ?> hari</td>
                            <td>Rp <?= number_format($r['total_price'], 0, ',', '.') ?></td>
                            <td><span class="badge bg-<?= $color ?>"><?= $label ?></span></td>
                            <td><a href="<?= base_url('rental-saya/' . $r['id']) ?>" class="btn btn-sm btn-outline-primary">Detail</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
