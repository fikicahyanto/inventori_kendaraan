<?= $this->extend('layouts/frontend') ?>
<?= $this->section('content') ?>

<div class="container py-5">
    <h3 class="section-title mb-4">Keranjang Sewa</h3>

    <?php if (empty($items)): ?>
        <div class="text-center py-5">
            <i class="fa-solid fa-cart-shopping fa-3x text-muted mb-3"></i>
            <p class="text-muted">Keranjang Anda masih kosong.</p>
            <a href="<?= base_url('katalog') ?>" class="btn btn-primary">Mulai Sewa Laptop</a>
        </div>
    <?php else: ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="table-card">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Laptop</th>
                                <th>Harga/Hari</th>
                                <th style="width:110px">Qty</th>
                                <th>Subtotal/Hari</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr class="cart-item-row" data-price="<?= $item['price_per_day'] ?>" data-month-price="<?= $item['price_per_month'] ?>" data-qty="<?= $item['qty'] ?>">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if (!empty($item['image'])): ?>
                                            <img src="<?= base_url('assets/uploads/laptops/' . $item['image']) ?>" style="width:55px;height:40px;object-fit:cover;border-radius:6px;">
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-semibold"><?= esc($item['name']) ?></div>
                                            <small class="text-muted"><?= esc($item['brand']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>Rp <?= number_format($item['price_per_day'], 0, ',', '.') ?></td>
                                <td>
                                    <input type="number" class="form-control cart-qty-input" data-id="<?= $item['id'] ?>" value="<?= $item['qty'] ?>" min="1" max="<?= $item['stock'] ?>">
                                </td>
                                <td class="subtotal-cell">Rp <?= number_format($item['price_per_day'] * $item['qty'], 0, ',', '.') ?></td>
                                <td>
                                    <a href="<?= base_url('cart/remove/' . $item['id']) ?>" class="btn btn-sm btn-outline-danger btn-confirm-delete" data-message="Hapus item ini dari keranjang?"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="table-card">
                <h5 class="fw-bold mb-3">Detail Sewa</h5>
                <form action="<?= base_url('cart/checkout') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Jenis Sewa</label>
                        <select name="rental_type" id="rental_type" class="form-select">
                            <option value="daily">Harian</option>
                            <option value="monthly">Bulanan (tarif per 30 hari)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Mulai Sewa</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Selesai Sewa</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <p id="totalDaysInfo" class="small text-muted mb-3"></p>
                    <div class="mb-3">
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Keperluan sewa, lokasi pengantaran, dll"></textarea>
                    </div>

                    <hr>
                    <h6 class="fw-bold mb-2"><i class="fa-solid fa-id-card"></i> Data Pengambilan Unit</h6>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="is_self_pickup" id="pickupSelf" value="yes" checked>
                            <label class="form-check-label" for="pickupSelf">Saya ambil sendiri (identitas sesuai akun & KTP profil)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="is_self_pickup" id="pickupOther" value="no">
                            <label class="form-check-label" for="pickupOther">Diwakilkan orang lain</label>
                        </div>
                    </div>

                    <div id="recipientFields" class="d-none border rounded p-3 mb-3 bg-light">
                        <p class="small text-muted">Wajib diisi lengkap beserta foto KTP penerima untuk keperluan verifikasi identitas saat serah terima unit.</p>
                        <div class="mb-2">
                            <label class="form-label">Nama Penerima</label>
                            <input type="text" name="recipient_name" class="form-control">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">No. HP Penerima</label>
                                <input type="text" name="recipient_phone" class="form-control">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Hubungan dengan Anda</label>
                                <input type="text" name="recipient_relation" class="form-control" placeholder="cth: Rekan Kerja, Keluarga">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Nomor KTP Penerima</label>
                            <input type="text" name="recipient_ktp_number" class="form-control" maxlength="16" placeholder="16 digit NIK">
                        </div>
                        <div class="mb-1">
                            <label class="form-label">Foto/Scan KTP Penerima</label>
                            <input type="file" name="recipient_ktp_photo" class="form-control input-image-preview" data-preview="#previewRecipientKtp" accept="image/*">
                        </div>
                        <img src="" id="previewRecipientKtp" class="d-none img-fluid rounded border mt-2" style="max-height:160px;">
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-semibold">Estimasi Total</span>
                        <span class="fw-bold fs-5 text-primary" id="estimatedTotal">Rp 0</span>
                    </div>

                    <button type="submit" id="checkoutBtn" class="btn btn-primary w-100">
                        <i class="fa-solid fa-paper-plane"></i> Ajukan Sewa
                    </button>
                    <p class="small text-muted mt-2 mb-0">Pesanan akan diverifikasi admin sebelum diproses.</p>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(function () {
    function updateEstimate() {
        const type = $('#rental_type').val();
        const start = new Date($('#start_date').val() + 'T00:00:00');
        const end = new Date($('#end_date').val() + 'T00:00:00');
        const days = $('#start_date').val() && $('#end_date').val() && end >= start ? Math.floor((end - start) / 86400000) + 1 : 0;
        const units = type === 'monthly' ? Math.ceil(days / 30) : days;
        let total = 0;
        $('.cart-item-row').each(function () {
            total += (type === 'monthly' ? Number($(this).data('month-price')) : Number($(this).data('price'))) * Number($(this).data('qty')) * units;
        });
        $('#totalDaysInfo').text(days ? (type === 'monthly' ? units + ' bulan tagihan (30 hari per bulan)' : days + ' hari') : 'Pilih tanggal sewa untuk melihat estimasi.');
        $('#estimatedTotal').text('Rp ' + Math.round(total).toLocaleString('id-ID'));
    }
    $('#rental_type, #start_date, #end_date').on('change', updateEstimate);
    updateEstimate();

    function toggleRecipientFields() {
        const isSelf = $('input[name="is_self_pickup"]:checked').val();
        const $fields = $('#recipientFields');
        if (isSelf === 'no') {
            $fields.removeClass('d-none');
            $fields.find('input[type=text], input[type=file]').not('[name="recipient_relation"]').prop('required', true);
        } else {
            $fields.addClass('d-none');
            $fields.find('input').prop('required', false);
        }
    }
    $('input[name="is_self_pickup"]').on('change', toggleRecipientFields);
    toggleRecipientFields();
});
</script>
<?= $this->endSection() ?>
