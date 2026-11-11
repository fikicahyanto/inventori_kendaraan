<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php
    $isEdit = isset($rental);
    $actionUrl = $isEdit ? base_url('admin/transaksi/update/' . $rental['id']) : base_url('admin/transaksi/simpan');
    $old = static fn($field, $default = '') => old($field, $isEdit ? ($rental[$field] ?? $default) : $default);
    $itemsLama = $items ?? [];
    $selfPickup = $old('is_self_pickup', 'yes');
?>

<div class="card-box">
    <form action="<?= $actionUrl ?>" method="post" enctype="multipart/form-data" id="formSewa">
        <?= csrf_field() ?>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Penyewa (Pegawai) <span class="text-danger">*</span></label>
                <select name="user_id" class="form-select" required>
                    <option value="">-- Pilih Pegawai --</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $old('user_id') == $c['id'] ? 'selected' : '' ?>>
                            <?= esc($c['name']) ?> (<?= esc($c['email']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <?php foreach (['pending'=>'Pending','approved'=>'Approved','ongoing'=>'Ongoing','returned'=>'Returned','rejected'=>'Rejected','cancelled'=>'Cancelled'] as $val => $label): ?>
                        <option value="<?= $val ?>" <?= $old('status', $isEdit ? $rental['status'] : 'approved') === $val ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="<?= esc($old('start_date', date('Y-m-d'))) ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Selesai (Rencana) <span class="text-danger">*</span></label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="<?= esc($old('end_date', date('Y-m-d', strtotime('+1 day')))) ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Jenis Penggunaan</label>
                <select name="rental_type" id="rental_type" class="form-select">
                    <option value="daily" <?= $old('rental_type', $isEdit ? ($rental['rental_type'] ?? 'daily') : 'daily') === 'daily' ? 'selected' : '' ?>>Harian</option>
                    <option value="monthly" <?= $old('rental_type', $isEdit ? ($rental['rental_type'] ?? 'daily') : 'daily') === 'monthly' ? 'selected' : '' ?>>Bulanan</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Catatan</label>
                <input type="text" name="notes" class="form-control" value="<?= esc($old('notes')) ?>">
            </div>
        </div>

        <hr class="my-4">
        <h6 class="mb-3">Pengambilan Unit</h6>

        <div class="row g-3 align-items-start">
            <div class="col-12">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="is_self_pickup" id="pickupYes" value="yes" <?= $selfPickup === 'yes' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="pickupYes">Diambil sendiri oleh penanggung jawab</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="is_self_pickup" id="pickupNo" value="no" <?= $selfPickup === 'no' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="pickupNo">Diwakilkan ke orang lain</label>
                </div>
            </div>
        </div>

        <div id="recipientSection" class="row g-3 mt-1" style="<?= $selfPickup === 'no' ? '' : 'display:none' ?>">
            <div class="col-md-4">
                <label class="form-label">Nama Penerima <span class="text-danger">*</span></label>
                <input type="text" name="recipient_name" class="form-control" value="<?= esc($old('recipient_name')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">No. Telepon Penerima <span class="text-danger">*</span></label>
                <input type="text" name="recipient_phone" class="form-control" value="<?= esc($old('recipient_phone')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Hubungan dengan Peminjam</label>
                <input type="text" name="recipient_relation" class="form-control" placeholder="mis: Rekan Kerja / Keluarga" value="<?= esc($old('recipient_relation')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Nomor KTP Penerima <span class="text-danger">*</span></label>
                <input type="text" name="recipient_ktp_number" class="form-control" value="<?= esc($old('recipient_ktp_number')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Foto KTP Penerima</label>
                <input type="file" name="recipient_ktp_photo" class="form-control" accept="image/*">
                <?php if ($isEdit && !empty($rental['recipient_ktp_photo'])): ?>
                    <div class="mt-2">
                        <img src="<?= base_url('assets/img/ktp/' . $rental['recipient_ktp_photo']) ?>" class="thumb-sm" alt="">
                        <span class="badge badge-ktp-<?= $rental['recipient_ktp_verified'] ?> ms-1"><?= ucfirst($rental['recipient_ktp_verified']) ?></span>
                        <small class="text-muted d-block">Kosongkan jika tidak ingin mengganti foto. Mengganti foto akan mereset status verifikasi.</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <hr class="my-4">
        <h6 class="mb-3">Daftar Kendaraan yang Digunakan</h6>

        <div class="table-responsive">
            <table class="table table-bordered align-middle" id="tabelItem">
                <thead class="table-light">
                    <tr>
                        <th style="min-width:260px">Kendaraan</th>
                        <th style="width:120px">Jumlah</th>
                        <th style="width:60px"></th>
                    </tr>
                </thead>
                <tbody id="bodyItem">
                    <?php if ($isEdit && !empty($itemsLama)): ?>
                        <?php foreach ($itemsLama as $it): ?>
                        <tr class="baris-item">
                            <td>
                                <select name="laptop_id[]" class="form-select select-laptop" required>
                                    <option value="">-- Pilih Kendaraan --</option>
                                    <?php foreach ($kendaraans as $l): ?>
                                        <option value="<?= $l['id'] ?>" data-stok="<?= $l['stock'] ?>" <?= $l['id'] == $it['laptop_id'] ? 'selected' : '' ?>>
                                            <?= esc($l['name']) ?> (stok: <?= $l['stock'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="number" name="qty[]" class="form-control input-jumlah" min="1" value="<?= $it['qty'] ?>" required></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger btn-hapus-baris"><i class="fa-solid fa-trash"></i></button></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr class="baris-item">
                            <td>
                                <select name="laptop_id[]" class="form-select select-laptop" required>
                                    <option value="">-- Pilih Kendaraan --</option>
                                    <?php foreach ($kendaraans as $l): ?>
                                        <option value="<?= $l['id'] ?>" data-stok="<?= $l['stock'] ?>">
                                            <?= esc($l['name']) ?> (stok: <?= $l['stock'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="number" name="qty[]" class="form-control input-jumlah" min="1" value="1" required></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger btn-hapus-baris"><i class="fa-solid fa-trash"></i></button></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <button type="button" id="btnTambahItem" class="btn btn-sm btn-outline-primary mb-3">
            <i class="fa-solid fa-plus"></i> Tambah Kendaraan Lain
        </button>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-floppy-disk"></i> <?= $isEdit ? 'Update Penyewaan' : 'Simpan Penyewaan' ?>
            </button>
            <a href="<?= base_url('admin/rental') ?>" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>

<!-- Template baris item tersembunyi untuk di-clone jQuery -->
<table style="display:none">
    <tbody id="templateBaris">
        <tr class="baris-item">
            <td>
                <select name="laptop_id[]" class="form-select select-laptop" required>
                    <option value="">-- Pilih Kendaraan --</option>
                    <?php foreach ($kendaraans as $l): ?>
                        <option value="<?= $l['id'] ?>" data-stok="<?= $l['stock'] ?>">
                            <?= esc($l['name']) ?> (stok: <?= $l['stock'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td><input type="number" name="qty[]" class="form-control input-jumlah" min="1" value="1" required></td>
            <td><span class="harga-display">Rp 0</span></td>
            <td><span class="subtotal-display fw-bold">Rp 0</span></td>
            <td><button type="button" class="btn btn-sm btn-outline-danger btn-hapus-baris"><i class="fa-solid fa-trash"></i></button></td>
        </tr>
    </tbody>
</table>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function formatRupiah(angka) {
    return 'Rp ' + Math.round(angka).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function hitungTotalHari() {
    var mulai = new Date($('#start_date').val());
    var selesai = new Date($('#end_date').val());
    var selisih = Math.round((selesai - mulai) / (1000 * 60 * 60 * 24));
    return selisih > 0 ? selisih : 0;
}

function hitungSemua() {
    var totalHari = hitungTotalHari();
    var tipeSewa = $('#rental_type').val();
    var unitTagihan = tipeSewa === 'monthly' ? Math.max(1, Math.ceil(totalHari / 30)) : totalHari;
    $('#totalHariDisplay').text(totalHari + ' hari' + (tipeSewa === 'monthly' && totalHari ? ' (' + unitTagihan + ' bulan tagihan)' : ''));

    var totalBiaya = 0;
    $('.baris-item').each(function () {
        var row = $(this);
        var opt = row.find('.select-laptop option:selected');
        var harga = parseFloat(tipeSewa === 'monthly' ? opt.data('harga-bulan') : opt.data('harga-hari')) || 0;
        var jumlah = parseInt(row.find('.input-jumlah').val()) || 0;
        var stok = parseInt(opt.data('stok')) || 0;

        row.find('.harga-display').text(formatRupiah(harga));

        if (jumlah > stok && stok > 0) {
            row.find('.input-jumlah').addClass('is-invalid');
        } else {
            row.find('.input-jumlah').removeClass('is-invalid');
        }

        var subtotal = harga * jumlah * unitTagihan;
        row.find('.subtotal-display').text(formatRupiah(subtotal));
        totalBiaya += subtotal;
    });

    $('#totalBiayaDisplay').text(formatRupiah(totalBiaya));
}

function toggleRecipientSection() {
    var isSelf = $('input[name="is_self_pickup"]:checked').val();
    if (isSelf === 'no') {
        $('#recipientSection').slideDown(150);
        $('#recipientSection input[name="recipient_name"], #recipientSection input[name="recipient_phone"], #recipientSection input[name="recipient_ktp_number"]').attr('required', true);
    } else {
        $('#recipientSection').slideUp(150);
        $('#recipientSection input[name="recipient_name"], #recipientSection input[name="recipient_phone"], #recipientSection input[name="recipient_ktp_number"]').attr('required', false);
    }
}

$(function () {
    hitungSemua();
    toggleRecipientSection();

    $(document).on('change', '.select-laptop, .input-jumlah', hitungSemua);
    $('#start_date, #end_date, #rental_type').on('change', hitungSemua);
    $('input[name="is_self_pickup"]').on('change', toggleRecipientSection);

    $('#btnTambahItem').on('click', function () {
        var baris = $('#templateBaris .baris-item').clone();
        baris.find('select, input').val('');
        $('#bodyItem').append(baris);
        hitungSemua();
    });

    $(document).on('click', '.btn-hapus-baris', function () {
        if ($('.baris-item').length > 1) {
            $(this).closest('tr').remove();
            hitungSemua();
        } else {
            alert('Minimal harus ada 1 laptop yang disewa.');
        }
    });

    $('#formSewa').on('submit', function (e) {
        if ($('.input-jumlah.is-invalid').length > 0) {
            e.preventDefault();
            alert('Ada jumlah laptop yang melebihi stok tersedia. Mohon periksa kembali.');
        }
    });
});
</script>
<?= $this->endSection() ?>
