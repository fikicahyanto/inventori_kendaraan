<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <input type="text" class="form-control table-search w-auto" data-target="#tblCategory" placeholder="Cari kategori...">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddCategory"><i class="fa-solid fa-plus"></i> Tambah Kategori</button>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table align-middle" id="tblCategory">
            <thead><tr><th>#</th><th>Nama</th><th>Deskripsi</th><th style="width:130px">Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($categories as $i => $cat): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td class="fw-semibold"><?= esc($cat['name']) ?></td>
                    <td><?= esc($cat['description']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $cat['id'] ?>"><i class="fa-solid fa-pen"></i></button>
                        <a href="<?= base_url('admin/kategori/hapus/' . $cat['id']) ?>" class="btn btn-sm btn-outline-danger btn-confirm-delete" data-message="Hapus kategori ini?"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>

                <!-- Modal Edit -->
                <div class="modal fade" id="modalEdit<?= $cat['id'] ?>">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="<?= base_url('admin/kategori/update/' . $cat['id']) ?>" method="post">
                                <?= csrf_field() ?>
                                <div class="modal-header"><h5 class="modal-title">Edit Kategori</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Kategori</label>
                                        <input type="text" name="name" class="form-control" value="<?= esc($cat['name']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Deskripsi</label>
                                        <textarea name="description" class="form-control"><?= esc($cat['description']) ?></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($categories)): ?>
                <tr><td colspan="4" class="text-center text-muted">Belum ada kategori.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Add -->
<div class="modal fade" id="modalAddCategory">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('admin/kategori/simpan') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header"><h5 class="modal-title">Tambah Kategori</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
