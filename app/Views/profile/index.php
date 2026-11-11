<?= $this->extend('layouts/frontend') ?>
<?= $this->section('content') ?>

<div class="container py-5">
    <h3 class="section-title mb-4">Profil Saya</h3>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="table-card">
                <h6 class="fw-bold mb-3">Informasi Akun</h6>
                <form action="<?= base_url('profil/update') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="text-center mb-3">
                        <?php if (!empty($user['photo'])): ?>
                            <img src="<?= base_url('assets/uploads/users/' . $user['photo']) ?>" id="previewImg" class="rounded-circle" style="width:100px;height:100px;object-fit:cover;">
                        <?php else: ?>
                            <img src="" id="previewImg" class="rounded-circle d-none" style="width:100px;height:100px;object-fit:cover;">
                            <i class="fa-solid fa-circle-user fa-5x text-muted" id="defaultIcon"></i>
                        <?php endif; ?>
                        <div class="mt-2">
                            <input type="file" name="photo" class="form-control form-control-sm input-image-preview" data-preview="#previewImg" accept="image/*">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="<?= esc($user['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="<?= esc($user['email']) ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. HP</label>
                        <input type="text" name="phone" class="form-control" value="<?= esc($user['phone']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" class="form-control" rows="2"><?= esc($user['address']) ?></textarea>
                    </div>

                    <hr>
                    <h6 class="fw-bold mb-2"><i class="fa-solid fa-id-card"></i> Verifikasi KTP</h6>
                    <?php
                        $ktpBadge = [
                            'unverified' => ['secondary', 'Belum Diverifikasi'],
                            'verified'   => ['success', 'Terverifikasi'],
                            'rejected'   => ['danger', 'Ditolak - Silakan Unggah Ulang'],
                        ][$user['ktp_verified'] ?? 'unverified'];
                    ?>
                    <p class="mb-2">Status: <span class="badge bg-<?= $ktpBadge[0] ?>"><?= $ktpBadge[1] ?></span></p>

                    <div class="mb-3">
                        <label class="form-label">Nomor KTP</label>
                        <input type="text" name="ktp_number" class="form-control" value="<?= esc($user['ktp_number']) ?>" placeholder="16 digit NIK" maxlength="16">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto/Scan KTP</label>
                        <?php if (!empty($user['ktp_photo'])): ?>
                            <div class="mb-2">
                                <img src="<?= base_url('assets/uploads/ktp/' . $user['ktp_photo']) ?>" id="previewKtp" class="img-fluid rounded border" style="max-height:160px;">
                            </div>
                        <?php else: ?>
                            <img src="" id="previewKtp" class="img-fluid rounded d-none border mb-2" style="max-height:160px;">
                        <?php endif; ?>
                        <input type="file" name="ktp_photo" class="form-control input-image-preview" data-preview="#previewKtp" accept="image/*">
                        <small class="text-muted">Pastikan foto jelas dan seluruh sisi KTP terlihat. Mengunggah ulang akan mereset status verifikasi.</small>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </form>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="table-card">
                <h6 class="fw-bold mb-3">Ubah Password</h6>
                <form action="<?= base_url('profil/password') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Password Lama</label>
                        <input type="password" name="old_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-outline-primary">Ubah Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
