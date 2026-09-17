<?php

use yii\helpers\Html;
use app\models\DetailTransaksi;

/* @var $this yii\web\View $this */
/* @var $model app\models\Transaksi */

$this->title = 'Nota Transaksi: ' . $model->no_nota;
$details = DetailTransaksi::find()->where(['id_transaksi' => $model->id])->all();
$user = Yii::$app->user->identity;
?>

<style>
/* Container & Struk Styling */
.receipt-wrapper {
    max-width: 380px;
    margin: 0 auto;
}

.receipt-box {
    background-color: #ffffff !important;
    color: #212529 !important;
    border-radius: 12px 12px 0 0;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    font-family: 'Courier New', Courier, monospace;
    position: relative;
}

/* Fix Khusus Tabel dalam Dark Mode Bootstrap 5 */
.receipt-box table, 
.receipt-box table th, 
.receipt-box table td,
.receipt-box .table {
    background-color: transparent !important;
    --bs-table-bg: transparent !important;
    --bs-table-color: #212529 !important;
    color: #212529 !important;
}

/* Motif Gerigi Struk Thermal (Bottom Sawtooth) */
.receipt-box::after {
    content: "";
    position: absolute;
    bottom: -15px;
    left: 0;
    width: 100%;
    height: 15px;
    background: linear-gradient(-45deg, transparent 10px, #ffffff 0), linear-gradient(45deg, transparent 10px, #ffffff 0);
    background-repeat: repeat-x;
    background-size: 15px 15px;
}

.receipt-box *, .receipt-box th, .receipt-box td {
    color: #212529 !important;
}

.receipt-divider {
    border-top: 1px dashed #6c757d !important;
    margin: 12px 0;
}

.receipt-double-divider {
    border-top: 2px dashed #212529 !important;
    margin: 12px 0;
}

@media print {
    .no-print, nav, footer, .navbar, .breadcrumb, .alert {
        display: none !important;
    }
    body {
        background-color: #ffffff !important;
    }
    .receipt-wrapper {
        max-width: 100% !important;
        margin: 0 !important;
    }
    .receipt-box {
        box-shadow: none !important;
        border-radius: 0 !important;
    }
    .receipt-box::after {
        display: none !important;
    }
}
</style>

<div class="transaksi-view container mt-4 mb-5">

    <div class="no-print mb-3 receipt-wrapper d-flex justify-content-between align-items-center">
        <div>
            <?php if ($user && ($user->isKasir() || $user->isSuperAdmin())): ?>
                <?= Html::a('<i class="bi bi-plus-circle"></i> Transaksi Baru', ['create'], ['class' => 'btn btn-success btn-sm me-1 shadow-sm']) ?>
            <?php endif; ?>
            
            <?= Html::a('Riwayat', ['index'], ['class' => 'btn btn-outline-secondary btn-sm shadow-sm']) ?>
        </div>
        <button onclick="window.print()" class="btn btn-primary btn-sm fw-bold shadow-sm">
            <i class="bi bi-printer"></i> Cetak Struk
        </button>
    </div>

    <div class="receipt-wrapper">
        <div class="receipt-box p-4">
            
            <div class="text-center mb-3">
                <h4 class="fw-bold m-0" style="letter-spacing: 2px;">EVERYDAYMART</h4>
                <div class="fw-bold text-uppercase small"><?= Html::encode($model->outlet->nama_outlet ?? $model->outlet->nama ?? 'Outlet Utama') ?></div>
                <div class="text-muted small mt-1">No: <?= Html::encode($model->no_nota) ?></div>
            </div>

            <div class="receipt-divider"></div>

            <div class="small lh-sm">
                <div class="d-flex justify-content-between">
                    <span>Tgl   : <?= date('d/m/Y H:i', strtotime($model->tgl_transaksi)) ?></span>
                </div>
                <div class="d-flex justify-content-between mt-1">
                    <span>Kasir : <?= Html::encode($user->username ?? 'Admin') ?></span>
                    <span>Pelanggan: <?= Html::encode($model->customer ?? 'Umum') ?></span>
                </div>
            </div>

            <div class="receipt-divider"></div>

            <!-- TABEL DETAIL DENGAN FIX TRANSPARAN DARI DARK MODE -->
            <table class="table table-borderless table-sm small mb-0 align-middle">
                <thead>
                    <tr style="border-bottom: 1px solid #212529;">
                        <th class="ps-0 bg-transparent text-dark">Item</th>
                        <th class="text-center bg-transparent text-dark">Qty</th>
                        <th class="text-end pe-0 bg-transparent text-dark">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($details as $item): ?>
                        <tr>
                            <td class="ps-0 bg-transparent text-dark">
                                <?= Html::encode($item->barang->nama_barang ?? $item->barang->nama ?? 'Barang') ?>
                            </td>
                            <td class="text-center bg-transparent text-dark"><?= $item->jumlah ?></td>
                            <td class="text-end pe-0 bg-transparent text-dark">Rp <?= number_format($item->total_harga_item, 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="receipt-double-divider"></div>

            <div class="d-flex justify-content-between fw-bold fs-5 my-2">
                <span>TOTAL:</span>
                <span>Rp <?= number_format($model->total_harga, 0, ',', '.') ?></span>
            </div>

            <div class="receipt-divider"></div>

            <div class="text-center small mt-3 text-muted" style="font-size: 0.75rem;">
                <div class="fw-bold">-- TERIMA KASIH --</div>
                <div>Barang yang sudah dibeli</div>
                <div>tidak dapat ditukar / dikembalikan</div>
            </div>

        </div>
    </div>

</div>