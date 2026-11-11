<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <input type="text" class="form-control table-search w-auto" data-target="#tblKendaraan" placeholder="Cari kendaraan...">
    <a href="<?= base_url('admin/kendaraan/tambah') ?>" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah kendaraan</a>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table align-middle" id="tblKendaraan">
            <thead>
                <tr><th>#</th><th>Foto</th><th>Kode / Barcode</th><th>Nama</th><th>Kategori</th><th>Stok</th><th>Status</th><th style="width:140px">Aksi</th></tr>
            </thead>
            <tbody>
            <?php foreach ($kendaraans as $i => $l): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td>
                        <?php if (!empty($l['image'])): ?>
                            <img src="<?= base_url('assets/uploads/kendaraans/' . $l['image']) ?>" style="width:60px;height:42px;object-fit:cover;border-radius:6px;">
                        <?php else: ?>
                            <div class="bg-light text-center rounded" style="width:60px;height:42px;line-height:42px;"><i class="fa-solid fa-kendaraan text-muted"></i></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="fw-semibold small"><?= esc($l['code']) ?></div>
                        <svg class="barcode-mini" data-code="<?= esc($l['code']) ?>"></svg>
                    </td>
                    <td class="fw-semibold"><?= esc($l['name']) ?><br><small class="text-muted"><?= esc($l['brand']) ?></small></td>
                    <td><?= esc($l['category_name']) ?></td>
                    <td><?= $l['stock'] ?></td>
                    <td><span class="badge badge-status-<?= $l['status'] ?>"><?= $l['status'] ?></span></td>
                    <td>
                        <a href="<?= base_url('admin/kendaraan/edit/' . $l['id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa-solid fa-pen"></i></a>
                        <a href="<?= base_url('admin/kendaraan/cetak-barcode/' . $l['id']) ?>" target="_blank" class="btn btn-sm btn-outline-dark" title="Cetak Label Barcode"><i class="fa-solid fa-print"></i></a>
                        <a href="<?= base_url('admin/kendaraan/hapus/' . $l['id']) ?>" class="btn btn-sm btn-outline-danger btn-confirm-delete" data-message="Hapus kendaraan ini?" title="Hapus"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($kendaraans)): ?>
                <tr><td colspan="9" class="text-center text-muted">Belum ada data kendaraan.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
$(function () {
    // Render barcode kecil untuk setiap baris tabel
    $('.barcode-mini').each(function () {
        const code = $(this).data('code');
        if (!code) return;
        try {
            JsBarcode(this, String(code), { format: 'CODE128', width: 1.3, height: 30, displayValue: false, margin: 0 });
        } catch (e) { /* abaikan jika format tidak valid */ }
    });
});
</script>
<?= $this->endSection() ?>
