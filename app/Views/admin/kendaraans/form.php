<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php $isEdit = isset($kendaraan); ?>
<div class="table-card">
    <form action="<?= $isEdit ? base_url('admin/kendaraan/update/' . $kendaraan['id']) : base_url('admin/kendaraan/simpan') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-4 text-center">
                <?php if ($isEdit && !empty($kendaraan['image'])): ?>
                    <img src="<?= base_url('assets/uploads/kendaraans/' . $kendaraan['image']) ?>" id="previewImg" class="img-fluid rounded mb-2" style="max-height:200px;">
                <?php else: ?>
                    <div class="img-placeholder rounded mb-2" style="height:180px;">
                        <img src="" id="previewImg" class="img-fluid rounded d-none" style="max-height:200px;">
                        <i class="fa-solid fa-motorcycle fa-2x" id="iconPlaceholder"></i>
                    </div>
                <?php endif; ?>
                <input type="file" name="image" class="form-control input-image-preview" data-preview="#previewImg" accept="image/*">
                <small class="text-muted">Max 2MB (jpg/png)</small>
            </div>

            <div class="col-md-8">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Kode Unit / Barcode</label>
                        <div class="input-group">
                            <input type="text" id="codeInput" name="code" class="form-control" value="<?= esc($kendaraan['code'] ?? '') ?>" placeholder="Ketik manual atau scan barcode" required>
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalScanBarcode">
                                <i class="fa-solid fa-barcode"></i> Scan
                            </button>
                        </div>
                        <div class="mt-2" id="barcodePreviewWrap" style="<?= !empty($kendaraan['code']) ? '' : 'display:none;' ?>">
                            <svg id="barcodePreview"></svg>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kategori</label>
                        <select name="category_id" class="form-select" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= (isset($kendaraan) && $kendaraan['category_id'] == $cat['id']) ? 'selected' : '' ?>><?= esc($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Nama Kendaraan</label>
                        <input type="text" name="name" class="form-control" value="<?= esc($kendaraan['name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Brand</label>
                        <input type="text" name="brand" class="form-control" value="<?= esc($kendaraan['brand'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Warna</label>
                        <input type="text" name="warna" class="form-control" value="<?= esc($kendaraan['warna'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="baik" <?= (isset($kendaraan) && $kendaraan['status'] == 'baik') ? 'selected' : '' ?>>Baik</option>
                            <option value="rusak_ringan" <?= (isset($kendaraan) && $kendaraan['status'] == 'rusak_ringan') ? 'selected' : '' ?>>Rusak Ringan</option>
                            <option value="rusak_berat" <?= (isset($kendaraan) && $kendaraan['status'] == 'rusak_berat') ? 'selected' : '' ?>>Rusak Berat</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Stok Unit</label>
                        <input type="number" name="stock" class="form-control" value="<?= esc($kendaraan['stock'] ?? 1) ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Spesifikasi Tambahan</label>
                        <textarea name="specification" class="form-control" rows="3"><?= esc($kendaraan['specification'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan</button>
            <a href="<?= base_url('admin/kendaraan') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<!-- ===================== MODAL SCAN BARCODE ===================== -->
<div class="modal fade" id="modalScanBarcode" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-barcode"></i> Scan Barcode Kendaraan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-2">Arahkan kamera ke barcode/QR pada stiker unit kendaraan. Membutuhkan izin akses kamera pada browser.</p>
                <div id="barcodeReader" style="width:100%;"></div>
                <div id="scanResultInfo" class="alert alert-success mt-2 d-none"></div>

                <hr>
                <p class="text-muted small mb-1">Atau input manual jika kamera tidak tersedia:</p>
                <div class="input-group">
                    <input type="text" id="manualBarcodeInput" class="form-control" placeholder="Ketik / tempel kode barcode di sini">
                    <button type="button" class="btn btn-primary" id="btnUseManualBarcode">Gunakan</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Library scan barcode via kamera (mendukung CODE_128, EAN, QR, dll) -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<!-- Library generate gambar barcode untuk preview & label cetak -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>

<script>
$(function () {
    let html5QrCode = null;

    function renderBarcodePreview(code) {
        if (!code) {
            $('#barcodePreviewWrap').hide();
            return;
        }
        try {
            JsBarcode('#barcodePreview', code, { format: 'CODE128', height: 40, displayValue: true, fontSize: 12 });
            $('#barcodePreviewWrap').show();
        } catch (e) {
            $('#barcodePreviewWrap').hide();
        }
    }

    // Render preview awal jika sudah ada kode (mode edit)
    renderBarcodePreview($('#codeInput').val());

    // Update preview live saat kode diketik manual
    $('#codeInput').on('input', function () {
        renderBarcodePreview($(this).val());
    });

    // Auto-fokus ke field kode agar siap menerima input dari scanner USB/Bluetooth
    // (scanner fisik bekerja seperti keyboard: mengetik cepat lalu menekan Enter)
    $('#codeInput').trigger('focus');

    // Cegah Enter dari scanner fisik langsung men-submit form;
    // pindahkan fokus ke field berikutnya agar admin bisa melanjutkan input.
    $('#codeInput').on('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            renderBarcodePreview($(this).val());
            $('select[name="category_id"]').trigger('focus');
        }
    });

    // Mulai kamera saat modal dibuka
    $('#modalScanBarcode').on('shown.bs.modal', function () {
        $('#scanResultInfo').addClass('d-none').text('');
        html5QrCode = new Html5Qrcode('barcodeReader');
        const config = { fps: 10, qrbox: { width: 250, height: 120 } };

        Html5Qrcode.getCameras().then(function (cameras) {
            if (!cameras || cameras.length === 0) {
                $('#scanResultInfo').removeClass('d-none alert-success').addClass('alert-warning')
                    .text('Kamera tidak ditemukan. Silakan gunakan input manual di bawah.');
                return;
            }
            const cameraId = cameras[cameras.length - 1].id; // biasanya kamera belakang di HP
            html5QrCode.start(
                cameraId,
                config,
                function onScanSuccess(decodedText) {
                    $('#codeInput').val(decodedText);
                    renderBarcodePreview(decodedText);
                    $('#scanResultInfo').removeClass('d-none alert-warning').addClass('alert-success')
                        .text('Barcode terdeteksi: ' + decodedText);
                    // Otomatis tutup modal setelah scan berhasil
                    setTimeout(function () {
                        $('#modalScanBarcode').modal('hide');
                    }, 800);
                },
                function onScanFailure() {
                    // diabaikan, terus mencoba scan
                }
            ).catch(function () {
                $('#scanResultInfo').removeClass('d-none alert-success').addClass('alert-warning')
                    .text('Tidak dapat mengakses kamera. Pastikan izin kamera diaktifkan atau gunakan input manual.');
            });
        }).catch(function () {
            $('#scanResultInfo').removeClass('d-none alert-success').addClass('alert-warning')
                .text('Tidak dapat mengakses kamera. Pastikan izin kamera diaktifkan atau gunakan input manual.');
        });
    });

    // Hentikan kamera saat modal ditutup (wajib agar kamera tidak terus menyala)
    $('#modalScanBarcode').on('hidden.bs.modal', function () {
        if (html5QrCode) {
            html5QrCode.stop().then(function () {
                html5QrCode.clear();
            }).catch(function () {});
        }
    });

    // Input manual sebagai fallback jika kamera tidak tersedia
    $('#btnUseManualBarcode').on('click', function () {
        const val = $('#manualBarcodeInput').val().trim();
        if (val) {
            $('#codeInput').val(val);
            renderBarcodePreview(val);
            $('#modalScanBarcode').modal('hide');
        }
    });
});
</script>
<?= $this->endSection() ?>
