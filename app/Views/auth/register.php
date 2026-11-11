<?= $this->extend('layouts/frontend') ?>
<?= $this->section('content') ?>

<div class="auth-wrapper py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card auth-card p-4">
                    <div class="card-body">
                        <h4 class="fw-bold text-center mb-1"><i class="fa-solid fa-user-plus text-primary"></i> Daftar Akun</h4>
                        <p class="text-center text-muted mb-4">Buat akun untuk mulai menyewa laptop</p>

                        <form action="<?= base_url('register') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" value="<?= old('name') ?>" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">No. HP</label>
                                    <input type="text" name="phone" class="form-control" value="<?= old('phone') ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea name="address" class="form-control" rows="2"><?= old('address') ?></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Konfirmasi Password</label>
                                    <input type="password" name="confirm_password" class="form-control" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Daftar</button>
                        </form>

                        <p class="text-center mt-3 mb-0">Sudah punya akun? <a href="<?= base_url('login') ?>">Masuk di sini</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
