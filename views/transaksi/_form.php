<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Barang;
use app\models\Outlet;
use app\models\StokBarang;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $model app\models\Transaksi */
/* @var $form yii\widgets\ActiveForm */

$user = Yii::$app->user->identity;

if ($user && !$user->isSuperAdmin()) {
    $model->id_outlet = $user->id_outlet;
}

// 1. Tentukan ID Outlet aktif awal
$idOutletAktif = $model->id_outlet ?? $user?->id_outlet;

// 2. Ambil seluruh outlet untuk dipassing ke JavaScript (PPN & Charge dinamis)
$allOutlets = Outlet::find()->asArray()->all();
$outletConfig = [];
foreach ($allOutlets as $o) {
    $outletConfig[$o['id']] = [
        'ppn' => floatval($o['ppn'] ?? 0),
        'charge' => floatval($o['charge'] ?? 0),
    ];
}

// 3. Ambil data barang & stok khusus outlet aktif
$barangs = Barang::find()->all();
$stokList = ArrayHelper::map(
    StokBarang::find()->where(['id_outlet' => $idOutletAktif])->all(),
    'id_barang',
    'jumlah_stok'
);

?>

<div class="transaksi-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row mb-3">
        <div class="col-md-3">
            <?= $form->field($model, 'customer')->textInput(['maxlength' => true, 'placeholder' => 'Pelanggan (Opsional)']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'waiter')->textInput([
                'maxlength' => true, 
                'placeholder' => 'Nama Kasir/Waiter',
                'value' => $model->waiter ?? $user?->username
            ]) ?>
        </div>
        <div class="col-md-3">
            <?php if ($user && $user->isSuperAdmin()): ?>
                <?= $form->field($model, 'id_outlet')->dropDownList(
                    ArrayHelper::map(Outlet::find()->all(), 'id', 'nama'),
                    ['prompt' => '-- Pilih Outlet --', 'id' => 'select-outlet', 'required' => true]
                )->label('Outlet Transaksi') ?>
            <?php else: ?>
                <?= $form->field($model, 'id_outlet')->hiddenInput(['value' => $user?->id_outlet, 'id' => 'select-outlet'])->label(false) ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">Outlet Transaksi</label>
                    <input type="text" class="form-control fw-bold" value="<?= Html::encode($user?->outlet->nama ?? 'Outlet Utama') ?>" readonly disabled>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'total_harga')->textInput([
                'id' => 'total-harga-input',
                'readonly' => true,
                'value' => 0,
                'class' => 'form-control fs-4 fw-bold text-success'
            ]) ?>
        </div>
    </div>

    <div class="row mb-4 p-3 rounded" style="background-color: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1);">
        <div class="col-md-6">
            <label class="form-label fw-bold">Uang Bayar (Cash)</label>
            <input type="number" id="uang-bayar" class="form-control fs-4 fw-bold" placeholder="0" min="0">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold">Kembalian</label>
            <input type="text" id="uang-kembali" class="form-control fs-4 fw-bold text-danger" value="Rp 0" readonly>
        </div>
    </div>

    <h4 class="mb-3">Pilih Barang</h4>

    <div class="row mb-3">
        <div class="col-md-6">
            <select id="select-barang" class="form-control">
                <option value="">-- Pilih Barang --</option>
                <?php foreach ($barangs as $b): 
                    $sisaStok = $stokList[$b->id] ?? 0;
                    $ppnVal = $b->ppn_id ?? $b->id_ppn ?? $b->ppn ?? 0;
                    $isPpn = ($ppnVal == 2) ? 1 : 0;
                    $labelPpn = ($isPpn == 1) ? '(Kena PPN)' : '(Non-PPN)';
                ?>
                    <option value="<?= $b->id ?>" 
                            data-harga="<?= $b->harga ?>" 
                            data-isppn="<?= $isPpn ?>"
                            data-stok="<?= $sisaStok ?>"
                            data-nama="<?= Html::encode($b->nama) ?>">
                        <?= Html::encode($b->nama) ?> - Rp <?= number_format($b->harga, 0, ',', '.') ?> | [Stok: <?= $sisaStok ?> Pcs] <?= $labelPpn ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" id="input-qty" class="form-control" value="1" min="1">
        </div>
        <div class="col-md-4">
            <button type="button" id="btn-tambah-item" class="btn btn-success w-100">+ Tambah Ke Keranjang</button>
        </div>
    </div>

    <table class="table table-bordered align-middle" id="tabel-keranjang">
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Harga Satuan</th>
                <th>Qty</th>
                <th>Status Pajak</th>
                <th>Subtotal</th>
                <th width="100">Aksi</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot class="fw-bold">
            <tr>
                <td colspan="4" class="text-end">Total Barang:</td>
                <td colspan="2" id="text-subtotal-barang">Rp 0</td>
            </tr>
            <tr id="row-ppn-outlet" style="display: none;">
                <td colspan="4" class="text-end" id="label-ppn-outlet">PPN Outlet (0%):</td>
                <td colspan="2" class="text-info" id="text-nominal-ppn">+ Rp 0</td>
            </tr>
            <tr id="row-charge-outlet" style="display: none;">
                <td colspan="4" class="text-end" id="label-charge-outlet">Charge / Service (0%):</td>
                <td colspan="2" class="text-warning" id="text-nominal-charge">+ Rp 0</td>
            </tr>
            <tr class="table-active fs-5">
                <td colspan="4" class="text-end text-success">SUBTOTAL:</td>
                <td colspan="2" class="text-success" id="text-grand-total">Rp 0</td>
            </tr>
        </tfoot>
    </table>

    <div class="form-group mt-4">
        <?= Html::submitButton('Proses & Simpan Transaksi', [
            'class' => 'btn btn-primary btn-lg w-100',
            'id' => 'btn-simpan'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$jsonOutletConfig = Json::encode($outletConfig);
$defaultOutletId = $idOutletAktif ?? 1;

$script = <<<JS
let itemIndex = 0;
let outletConfigMap = {$jsonOutletConfig};
let activeOutletId = "{$defaultOutletId}";

// Fungsi untuk mendapatkan persen PPN & Charge Outlet saat ini
function getOutletSetting() {
    let selectedOutlet = $('#select-outlet').val() || activeOutletId;
    if (outletConfigMap[selectedOutlet]) {
        return outletConfigMap[selectedOutlet];
    }
    return { ppn: 0, charge: 0 };
}

// Event saat SuperAdmin memilih/mengubah Outlet
$('#select-outlet').on('change', function() {
    activeOutletId = $(this).val();
    hitungTotal();
});

$('#btn-tambah-item').on('click', function() {
    let select = $('#select-barang option:selected');
    let idBarang = select.val();
    let namaBarang = select.data('nama');
    let harga = parseInt(select.data('harga'));
    let isPpn = parseInt(select.data('isppn'));
    let stokTersedia = parseInt(select.data('stok')) || 0;
    let qty = parseInt($('#input-qty').val());

    if (!idBarang) {
        alert('Pilih barang terlebih dahulu!');
        return;
    }

    if (qty <= 0 || isNaN(qty)) {
        alert('Jumlah barang harus lebih dari 0!');
        return;
    }

    if (qty > stokTersedia) {
        alert('Stok "' + namaBarang + '" tidak mencukupi! Sisa stok tersedia: ' + stokTersedia + ' Pcs.');
        return;
    }

    let subtotal = harga * qty;
    let labelPpn = (isPpn === 1) 
        ? '<span class="badge bg-info">Kena PPN</span>' 
        : '<span class="badge bg-secondary">Bebas PPN</span>';

    let row = `
        <tr id="row-\${itemIndex}" class="item-row" data-is-ppn="\${isPpn}" data-subtotal="\${subtotal}">
            <td>
                \${namaBarang}
                <input type="hidden" name="DetailTransaksi[\${itemIndex}][id_barang]" value="\${idBarang}">
            </td>
            <td>Rp \${harga.toLocaleString('id-ID')}</td>
            <td>
                \${qty}
                <input type="hidden" name="DetailTransaksi[\${itemIndex}][jumlah]" value="\${qty}">
            </td>
            <td>\${labelPpn}</td>
            <td>Rp \${subtotal.toLocaleString('id-ID')}
                <input type="hidden" name="DetailTransaksi[\${itemIndex}][total_harga_item]" value="\${subtotal}">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm btn-hapus" data-index="\${itemIndex}">Hapus</button>
            </td>
        </tr>
    `;

    $('#tabel-keranjang tbody').append(row);
    itemIndex++;
    hitungTotal();

    $('#select-barang').val('');
    $('#input-qty').val(1);
});

$(document).on('click', '.btn-hapus', function() {
    let idx = $(this).data('index');
    $('#row-' + idx).remove();
    hitungTotal();
});

function hitungTotal() {
    let setting = getOutletSetting();
    let ppnPersenOutlet = setting.ppn;
    let chargePersenOutlet = setting.charge;

    let subtotalBarangKenaPpn = 0;
    let totalSubtotalSemuaBarang = 0;

    $('.item-row').each(function() {
        let subtotal = parseFloat($(this).data('subtotal')) || 0;
        let isPpn = parseInt($(this).attr('data-is-ppn'));

        totalSubtotalSemuaBarang += subtotal;

        if (isPpn === 1) {
            subtotalBarangKenaPpn += subtotal;
        }
    });

    let nominalPpn = Math.round(subtotalBarangKenaPpn * (ppnPersenOutlet / 100));
    let nominalCharge = Math.round(totalSubtotalSemuaBarang * (chargePersenOutlet / 100));
    let grandTotal = totalSubtotalSemuaBarang + nominalPpn + nominalCharge;

    $('#text-subtotal-barang').text('Rp ' + totalSubtotalSemuaBarang.toLocaleString('id-ID'));
    
    // Tampilan PPN Dinamis
    if (nominalPpn > 0) {
        $('#label-ppn-outlet').text('PPN Outlet (' + ppnPersenOutlet + '%):');
        $('#text-nominal-ppn').text('+ Rp ' + nominalPpn.toLocaleString('id-ID'));
        $('#row-ppn-outlet').show();
    } else {
        $('#row-ppn-outlet').hide();
    }

    // Tampilan Charge Dinamis (Hanya jika charge > 0)
    if (nominalCharge > 0) {
        $('#label-charge-outlet').text('Charge / Service (' + chargePersenOutlet + '%):');
        $('#text-nominal-charge').text('+ Rp ' + nominalCharge.toLocaleString('id-ID'));
        $('#row-charge-outlet').show();
    } else {
        $('#row-charge-outlet').hide();
    }

    $('#text-grand-total').text('Rp ' + grandTotal.toLocaleString('id-ID'));
    $('#total-harga-input').val(grandTotal);

    hitungKembali();
}

$('#uang-bayar').on('input keyup', function() {
    hitungKembali();
});

function hitungKembali() {
    let total = parseFloat($('#total-harga-input').val()) || 0;
    let bayar = parseFloat($('#uang-bayar').val()) || 0;
    let kembali = bayar - total;

    if (total > 0 && bayar >= total) {
        $('#uang-kembali').val('Rp ' + kembali.toLocaleString('id-ID'));
        $('#btn-simpan').prop('disabled', false);
    } else if (total > 0 && bayar < total) {
        $('#uang-kembali').val('Uang Kurang!');
        $('#btn-simpan').prop('disabled', true);
    } else {
        $('#uang-kembali').val('Rp 0');
        $('#btn-simpan').prop('disabled', false);
    }
}

// === BACA DATA SEMENTARA DARI LOCALSTORAGE KATALOG ===
let savedCart = JSON.parse(localStorage.getItem('everyday_cart')) || [];
if (savedCart.length > 0) {
    savedCart.forEach(function(item) {
        $('#select-barang').val(item.id);
        $('#input-qty').val(item.qty);
        $('#btn-tambah-item').click();
    });
    // Hapus penyimpanan lokal setelah berhasil dipindahkan ke tabel
    localStorage.removeItem('everyday_cart');
}

// Jalankan kalkulasi pertama kali
hitungTotal();
JS;
$this->registerJs($script);
?>