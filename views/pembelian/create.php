<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Buat Nota Pembelian';
$this->params['breadcrumbs'][] = ['label' => 'Pembelian', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pembelian-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['id' => 'pembelian-form']); ?>

    <div class="card mb-4">
        <div class="card-header font-weight-bold">Detail Nota Belanja</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label>Tujuan Outlet</label>
                    <input type="text" class="form-control" value="<?= Html::encode($outlet->nama ?? $outlet->nama_outlet ?? 'Outlet Utama') ?>" readonly>
                </div>
                <div class="col-md-3">
                    <label>Nomor Nota (Otomatis)</label>
                    <input type="text" class="form-control" value="<?= Html::encode($previewNoNota) ?>" readonly>
                </div>
                <div class="col-md-3">
                    <label>Supplier / Pemasok</label>
                    <input type="text" name="supplier" class="form-control" placeholder="Nama Supplier" required>
                </div>
                <div class="col-md-3">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="font-weight-bold">Tambah Detail Barang</span>
            <button type="button" class="btn btn-success btn-sm" id="btn-tambah-baris">+ Tambah Baris</button>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered m-0" id="table-items">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th style="width: 200px;">Jumlah</th>
                        <th style="width: 250px;">Harga Beli</th>
                        <th style="width: 80px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="item-rows">
                    <tr class="item-row">
                        <td>
                            <select name="items[0][barang_id]" class="form-control select-barang" required>
                                <option value="">-- Pilih Barang --</option>
                                <?php foreach ($barangList as $b): ?>
                                    <option value="<?= $b->id ?>"><?= Html::encode($b->nama ?? $b->nama_barang) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <input type="number" name="items[0][jumlah]" class="form-control" value="1" min="1" required>
                        </td>
                        <td>
                            <input type="number" name="items[0][harga_beli]" class="form-control" value="0" min="0">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm btn-hapus">Hapus</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Simpan Penerimaan', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Batal', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let rowIndex = 1;
    const btnTambah = document.getElementById('btn-tambah-baris');
    const container = document.getElementById('item-rows');

    btnTambah.addEventListener('click', function () {
        const firstRow = container.querySelector('.item-row');
        const newRow = firstRow.cloneNode(true);

        // Reset input nilai dan ganti index array name
        newRow.querySelectorAll('select, input').forEach(function (input) {
            let name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace(/\[\d+\]/, '[' + rowIndex + ']'));
            }
            if (input.tagName === 'INPUT') {
                if (input.type === 'number') {
                    input.value = input.getAttribute('min') || 0;
                } else {
                    input.value = '';
                }
            } else if (input.tagName === 'SELECT') {
                input.selectedIndex = 0;
            }
        });

        container.appendChild(newRow);
        rowIndex++;
    });

    container.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('btn-hapus')) {
            const rows = container.querySelectorAll('.item-row');
            if (rows.length > 1) {
                e.target.closest('tr').remove();
            } else {
                alert('Minimal harus ada satu baris barang.');
            }
        }
    });
});
</script>