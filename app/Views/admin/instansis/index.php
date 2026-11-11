<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <input type="text" class="form-control table-search w-auto" data-target="#tblInstansi" placeholder="Cari instansi...">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddInstansi"><i class="fa-solid fa-plus"></i> Tambah Instansi</button>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table align-middle" id="tblInstansi">
            <thead><tr><th>#</th><th>Kode</th><th>Nama Instansi</th><th>Alamat</th><th>No. Telepon</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($instansis as $i => $instansi): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td class="fw-semibold"><?= esc($instansi['code']) ?></td>
                    <td><?= esc($instansi['name']) ?></td>
                    <td><?= esc($instansi['address'] ?: '-') ?></td>
                    <td><?= esc($instansi['phone'] ?: '-') ?></td>
                    <td><span class="badge bg-<?= $instansi['status'] === 'active' ? 'success' : 'secondary' ?>"><?= $instansi['status'] === 'active' ? 'Aktif' : 'Nonaktif' ?></span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $instansi['id'] ?>"><i class="fa-solid fa-pen"></i></button>
                        <a href="<?= base_url('admin/instansi/hapus/' . $instansi['id']) ?>" class="btn btn-sm btn-outline-danger btn-confirm-delete" data-message="Hapus instansi ini?"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
                <div class="modal fade" id="modalEdit<?= $instansi['id'] ?>">
                    <div class="modal-dialog"><div class="modal-content">
                        <form action="<?= base_url('admin/instansi/update/' . $instansi['id']) ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="modal-header"><h5 class="modal-title">Edit Instansi</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                            <div class="modal-body">
                                <div class="mb-2"><label class="form-label">Kode</label><input name="code" class="form-control" value="<?= esc($instansi['code']) ?>" required></div>
                                <div class="mb-2"><label class="form-label">Nama Instansi</label><input name="name" class="form-control" value="<?= esc($instansi['name']) ?>" required></div>
                                <div class="mb-2"><label class="form-label">Alamat</label><textarea name="address" class="form-control"><?= esc($instansi['address']) ?></textarea></div>
                                <div class="mb-2"><label class="form-label">No. Telepon</label><input name="phone" class="form-control" value="<?= esc($instansi['phone']) ?>"></div>
                                <div class="mb-2"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active" <?= $instansi['status'] === 'active' ? 'selected' : '' ?>>Aktif</option><option value="inactive" <?= $instansi['status'] === 'inactive' ? 'selected' : '' ?>>Nonaktif</option></select></div>
                            </div>
                            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
                        </form>
                    </div></div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($instansis)): ?><tr><td colspan="7" class="text-center text-muted">Belum ada data instansi.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalAddInstansi"><div class="modal-dialog"><div class="modal-content">
    <form action="<?= base_url('admin/instansi/simpan') ?>" method="post">
        <?= csrf_field() ?>
        <div class="modal-header"><h5 class="modal-title">Tambah Instansi</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-2"><label class="form-label">Kode</label><input name="code" class="form-control" required></div>
            <div class="mb-2"><label class="form-label">Nama Instansi</label><input name="name" class="form-control" required></div>
            <div class="mb-2"><label class="form-label">Alamat</label><textarea name="address" class="form-control"></textarea></div>
            <div class="mb-2"><label class="form-label">No. Telepon</label><input name="phone" class="form-control"></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
    </form>
</div></div></div>

<?= $this->endSection() ?>
