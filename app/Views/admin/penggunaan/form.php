<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="table-card"><form action="<?= base_url('admin/transaksi/simpan') ?>" method="post"><?= csrf_field() ?><div class="row g-3">
<div class="col-md-6"><label class="form-label">Kendaraan</label><select name="kendaraan_id" class="form-select" required><option value="">-- Pilih Kendaraan --</option><?php foreach ($kendaraans as $k): ?><option value="<?= $k['id'] ?>"><?= esc($k['code']) ?> - <?= esc($k['name']) ?> (stok <?= $k['stock'] ?>)</option><?php endforeach; ?></select></div>
<div class="col-md-6"><label class="form-label">Pegawai Penanggung Jawab</label><select name="pegawai_id" class="form-select" required><option value="">-- Pilih Pegawai --</option><?php foreach ($pegawais as $p): ?><option value="<?= $p['id'] ?>"><?= esc($p['name']) ?><?= $p['nip'] ? ' (' . esc($p['nip']) . ')' : '' ?></option><?php endforeach; ?></select></div>
<div class="col-md-6"><label class="form-label">Instansi Pengguna</label><select name="instansi_id" class="form-select" required><option value="">-- Pilih Instansi --</option><?php foreach ($instansis as $i): ?><option value="<?= $i['id'] ?>"><?= esc($i['code']) ?> - <?= esc($i['name']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-6"><label class="form-label">Tanggal Penggunaan</label><input type="date" name="assigned_at" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
<div class="col-12"><label class="form-label">Catatan</label><textarea name="notes" class="form-control" rows="3"></textarea></div>
</div><div class="mt-4"><button class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan Penggunaan</button> <a href="<?= base_url('admin/transaksi') ?>" class="btn btn-secondary">Batal</a></div></form></div>
<?= $this->endSection() ?>
