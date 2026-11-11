<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <input type="text" class="form-control table-search w-auto" data-target="#tblUser" placeholder="Cari akun login...">
    <a class="btn btn-outline-primary" href="<?= base_url('admin/instansi') ?>"><i class="fa-solid fa-building"></i> Kelola Instansi</a>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddUser"><i class="fa-solid fa-plus"></i> Tambah Akun</button>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table align-middle" id="tblUser">
            <thead><tr><th>#</th><th>Nama</th><th>Email</th><th>No. HP</th><th>Role</th><th>Status</th><th>KTP</th><th style="width:160px">Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($users as $i => $u): ?>
                <?php
                    $ktpColor = ['unverified' => 'secondary', 'verified' => 'success', 'rejected' => 'danger'][$u['ktp_verified'] ?? 'unverified'];
                    $ktpLabel = ['unverified' => 'Belum Verifikasi', 'verified' => 'Terverifikasi', 'rejected' => 'Ditolak'][$u['ktp_verified'] ?? 'unverified'];
                ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td class="fw-semibold"><?= esc($u['name']) ?></td>
                    <td><?= esc($u['email']) ?></td>
                    <td><?= esc($u['phone']) ?></td>
                    <td><span class="badge bg-<?= $u['role'] == 'admin' ? 'dark' : 'secondary' ?>"><?= ucfirst($u['role']) ?></span></td>
                    <td><span class="badge bg-<?= $u['status'] == 'active' ? 'success' : 'danger' ?>"><?= ucfirst($u['status']) ?></span></td>
                    <td>
                        <?php if ($u['role'] === 'customer'): ?>
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalKtp<?= $u['id'] ?>">
                                <span class="badge bg-<?= $ktpColor ?>"><?= $ktpLabel ?></span>
                            </button>
                        <?php else: ?>
                            <span class="text-muted small">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditUser<?= $u['id'] ?>"><i class="fa-solid fa-pen"></i></button>
                        <a href="<?= base_url('admin/pengguna/hapus/' . $u['id']) ?>" class="btn btn-sm btn-outline-danger btn-confirm-delete" data-message="Hapus pengguna ini?"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>

                <!-- Modal Lihat & Verifikasi KTP -->
                <?php if ($u['role'] === 'customer'): ?>
                <div class="modal fade" id="modalKtp<?= $u['id'] ?>">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Verifikasi KTP - <?= esc($u['name']) ?></h5>
                                <button class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center">
                                <p class="mb-2">No. KTP: <strong><?= esc($u['ktp_number'] ?: '-') ?></strong></p>
                                <p class="mb-3">Status saat ini: <span class="badge bg-<?= $ktpColor ?>"><?= $ktpLabel ?></span></p>
                                <?php if (!empty($u['ktp_photo'])): ?>
                                    <a href="<?= base_url('assets/uploads/ktp/' . $u['ktp_photo']) ?>" target="_blank">
                                        <img src="<?= base_url('assets/uploads/ktp/' . $u['ktp_photo']) ?>" class="img-fluid rounded border mb-3" style="max-height:220px;">
                                    </a>
                                <?php else: ?>
                                    <p class="text-muted">Pengguna belum mengunggah foto KTP.</p>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($u['ktp_photo'])): ?>
                            <div class="modal-footer">
                                <?php if ($u['ktp_verified'] !== 'verified'): ?>
                                <form action="<?= base_url('admin/pengguna/verifikasi-ktp/' . $u['id']) ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-success"><i class="fa-solid fa-check"></i> Verifikasi</button>
                                </form>
                                <?php endif; ?>
                                <?php if ($u['ktp_verified'] !== 'rejected'): ?>
                                <form action="<?= base_url('admin/pengguna/tolak-ktp/' . $u['id']) ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-outline-danger"><i class="fa-solid fa-xmark"></i> Tolak</button>
                                </form>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="modal fade" id="modalEditUser<?= $u['id'] ?>">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="<?= base_url('admin/pengguna/update/' . $u['id']) ?>" method="post">
                                <?= csrf_field() ?>
                                <div class="modal-header"><h5 class="modal-title">Edit Pengguna</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body">
                                    <div class="mb-2">
                                        <label class="form-label">Nama</label>
                                        <input type="text" name="name" class="form-control" value="<?= esc($u['name']) ?>" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">No. HP</label>
                                        <input type="text" name="phone" class="form-control" value="<?= esc($u['phone']) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Alamat</label>
                                        <textarea name="address" class="form-control"><?= esc($u['address']) ?></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 mb-2">
                                            <label class="form-label">Role</label>
                                            <select name="role" class="form-select">
                                                <option value="customer" <?= $u['role'] == 'customer' ? 'selected' : '' ?>>Operator</option>
                                                <option value="admin" <?= $u['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                            </select>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="active" <?= $u['status'] == 'active' ? 'selected' : '' ?>>Aktif</option>
                                                <option value="inactive" <?= $u['status'] == 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Password Baru (kosongkan jika tidak diubah)</label>
                                        <input type="password" name="password" class="form-control">
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
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalAddUser">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('admin/pengguna/simpan') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header"><h5 class="modal-title">Tambah Pengguna</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">No. HP</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" class="form-control"></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="customer">Operator</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
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
