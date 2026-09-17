<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Pembelian $model */

$this->title = 'Detail Nota: ' . $model->no_nota;
$this->params['breadcrumbs'][] = ['label' => 'Daftar Penerimaan Barang', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pembelian-view">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a('Kembali', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <!-- Informasi Header Nota -->
    <div class="card mb-4">
        <div class="card-header font-weight-bold bg-light">Informasi Nota</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>No. Nota:</strong>
                    <p class="text-primary h5 mt-1"><?= Html::encode($model->no_nota) ?></p>
                </div>
                <div class="col-md-3">
                    <strong>Tujuan Outlet:</strong>
                    <p class="mt-1"><?= Html::encode($model->outlet->nama_outlet ?? $model->outlet->nama ?? ('Outlet #' . $model->outlet_id)) ?></p>
                </div>
                <div class="col-md-3">
                    <strong>Supplier / Pemasok:</strong>
                    <p class="mt-1"><?= Html::encode($model->supplier ?? '-') ?></p>
                </div>
                <div class="col-md-3">
                    <strong>Tanggal Penerimaan:</strong>
                    <p class="mt-1"><?= Html::encode(date('d/m/Y', strtotime($model->tanggal))) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Rincian Barang Masuk -->
    <div class="card">
        <div class="card-header font-weight-bold bg-light">Rincian Barang Diterima</div>
        <div class="card-body p-0">
            <table class="table table-bordered table-striped m-0">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th>Nama Barang</th>
                        <th style="width: 150px;" class="text-center">Jumlah Masuk</th>
                        <th style="width: 200px;" class="text-right">Harga Beli</th>
                        <th style="width: 200px;" class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $grandTotal = 0;
                    if (!empty($model->pembelianDetails)): 
                        foreach ($model->pembelianDetails as $index => $detail): 
                            $subtotal = $detail->jumlah * $detail->harga_beli;
                            $grandTotal += $subtotal;
                    ?>
                        <tr>
                            <td class="text-center"><?= $index + 1 ?></td>
                            <td>
                                <?= Html::encode($detail->barang->nama_barang ?? $detail->barang->nama ?? ('Barang #' . $detail->barang_id)) ?>
                            </td>
                            <td class="text-center"><?= number_format($detail->jumlah, 0, ',', '.') ?> Pcs</td>
                            <td class="text-right">Rp <?= number_format($detail->harga_beli, 0, ',', '.') ?></td>
                            <td class="text-right">Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                        </tr>
                    <?php 
                        endforeach; 
                    else:
                    ?>
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada rincian barang.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <?php if ($grandTotal > 0): ?>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-right">Total Transaksi</th>
                        <th class="text-right">Rp <?= number_format($grandTotal, 0, ',', '.') ?></th>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>

</div>