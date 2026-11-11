<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Label Barcode - <?= esc($laptop['name']) ?></title>
    <style>
        * { font-family: 'Poppins', Arial, sans-serif; margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #f1f5f9; padding: 30px; }
        .toolbar { text-align: center; margin-bottom: 20px; }
        .toolbar button {
            padding: 10px 22px; border: none; border-radius: 6px; background: #4f46e5; color: #fff;
            font-size: 14px; cursor: pointer;
        }
        .label-wrap { display: flex; flex-wrap: wrap; gap: 14px; justify-content: center; }
        .label-card {
            width: 260px; border: 1px dashed #94a3b8; border-radius: 8px; background: #fff;
            padding: 14px; text-align: center;
        }
        .label-card h4 { font-size: 13px; margin-bottom: 4px; }
        .label-card p { font-size: 11px; color: #475569; margin-bottom: 8px; }
        .label-card svg { max-width: 100%; }

        @media print {
            body { background: #fff; padding: 0; }
            .toolbar { display: none; }
            .label-card { border: 1px solid #000; page-break-inside: avoid; }
        }
    </style>
</head>
<body>

<div class="toolbar">
    <button onclick="window.print()"><i class="fa-solid fa-print"></i> Cetak Label</button>
</div>

<div class="label-wrap">
    <?php
    // Cetak beberapa salinan label sesuai jumlah stok unit (opsional, default 1 label per request cetak)
    $copies = isset($_GET['copies']) ? max(1, (int) $_GET['copies']) : 1;
    for ($i = 0; $i < $copies; $i++):
    ?>
    <div class="label-card">
        <h4><?= esc($laptop['name']) ?></h4>
        <p><?= esc($laptop['brand']) ?> &bull; <?= esc($laptop['ram']) ?> / <?= esc($laptop['storage']) ?></p>
        <svg class="barcode-label" data-code="<?= esc($laptop['code']) ?>"></svg>
    </div>
    <?php endfor; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
    document.querySelectorAll('.barcode-label').forEach(function (el) {
        JsBarcode(el, el.dataset.code, { format: 'CODE128', height: 50, fontSize: 13, margin: 6 });
    });
</script>
</body>
</html>
