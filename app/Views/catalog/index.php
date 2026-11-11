<?= $this->extend('layouts/frontend') ?>
<?= $this->section('content') ?>
<div class="container py-5"><h3 class="section-title mb-4">Katalog Kendaraan</h3><div class="row g-3 mb-4"><div class="col-md-8"><input type="text" id="searchKatalog" class="form-control" placeholder="Cari nama, kode, atau merek kendaraan..." value="<?= esc($keyword ?? '') ?>"></div><div class="col-md-4"><select id="filterCategory" class="form-select"><option value="">Semua Kategori</option><?php foreach ($categories as $cat): ?><option value="<?= $cat['id'] ?>" <?= ($categoryId == $cat['id']) ? 'selected' : '' ?>><?= esc($cat['name']) ?></option><?php endforeach; ?></select></div></div><div id="catalogGrid"><?= $this->include('catalog/_grid') ?></div></div>
<?= $this->endSection() ?>
