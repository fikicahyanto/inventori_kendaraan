<?= $this->extend('layouts/frontend') ?>
<?= $this->section('content') ?>

<div class="auth-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card auth-card p-4">
                    <div class="card-body">
                        <h4 class="fw-bold text-center mb-1"><i class="fa-solid fa-laptop text-primary"></i> Masuk</h4>
                        <p class="text-center text-muted mb-4">Masuk untuk mulai menyewa laptop</p>

                        <form action="<?= base_url('login') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Masuk</button>
                        </form>

                        <p class="text-center mt-3 mb-0">Belum punya akun? <a href="<?= base_url('register') ?>">Daftar sekarang</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
