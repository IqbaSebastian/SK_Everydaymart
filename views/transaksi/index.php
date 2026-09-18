<?php

use app\models\Transaksi;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\TransaksiSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Rincian Penjualan';
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user->identity;
?>
<div class="transaksi-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'no_nota',
            'tgl_transaksi',
            [
                'attribute' => 'total_harga',
                'value' => function ($model) {
                    return 'Rp ' . number_format($model->total_harga, 0, ',', '.');
                },
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->status === 'CANCELLED') {
                        return '<span class="badge bg-danger">Dibatalkan</span>';
                    } elseif ($model->status === 'PENDING_CANCEL') {
                        return '<span class="badge bg-warning text-dark">Menunggu Otorisasi Batal</span>';
                    }
                    return '<span class="badge bg-success">Berhasil</span>';
                },
            ],

            [
                'class' => ActionColumn::className(),
                'template' => '{view} {action-batal}',
                'buttons' => [
                    'action-batal' => function ($url, $model, $key) use ($user) {
                        if ($model->status === 'CANCELLED') {
                            return '';
                        }

                        // 1. OPSI UNTUK KASIR: Pengajuan Batal
                        if ($user && $user->isKasir() && $model->status === 'SUCCESS') {
                            return Html::button('<i class="bi bi-x-circle"></i> Ajukan Batal', [
                                'class' => 'btn btn-sm btn-outline-danger ms-1',
                                'data-bs-toggle' => 'modal',
                                'data-bs-target' => '#modalRequestCancel-' . $model->id,
                            ]);
                        }

                        // 2. OPSI UNTUK MANAGER / SUPER ADMIN: Otorisasi Pembatalan
                        if ($user && ($user->isManager() || $user->isSuperAdmin()) && $model->status === 'PENDING_CANCEL') {
                            return Html::button('<i class="bi bi-shield-lock"></i> Otorisasi Batal', [
                                'class' => 'btn btn-sm btn-danger ms-1',
                                'data-bs-toggle' => 'modal',
                                'data-bs-target' => '#modalApproveCancel-' . $model->id,
                            ]);
                        }

                        return '';
                    },
                ],
            ],
        ],
    ]); ?>

    <?php foreach ($dataProvider->getModels() as $model): ?>
        
        <?php if ($user && $user->isKasir() && $model->status === 'SUCCESS'): ?>
            <div class="modal fade" id="modalRequestCancel-<?= $model->id ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <?php $form = \yii\widgets\ActiveForm::begin([
                            'action' => ['transaksi/request-cancel', 'id' => $model->id],
                            'method' => 'post',
                        ]); ?>
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title">Ajukan Pembatalan Nota #<?= Html::encode($model->no_nota) ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Masukkan alasan mengapa transaksi ini ingin dibatalkan:</p>
                            <textarea name="alasan_batal" class="form-control" rows="3" required placeholder="Contoh: Salah input barang / Pelanggan batal beli"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning">Kirim Pengajuan</button>
                        </div>
                        <?php \yii\widgets\ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- MODAL 2: Otorisasi oleh Manager -->
        <?php if ($user && ($user->isManager() || $user->isSuperAdmin()) && $model->status === 'PENDING_CANCEL'): ?>
            <div class="modal fade" id="modalApproveCancel-<?= $model->id ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <?php $form = \yii\widgets\ActiveForm::begin([
                            'action' => ['transaksi/approve-cancel', 'id' => $model->id],
                            'method' => 'post',
                        ]); ?>
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">Otorisasi Pembatalan Nota #<?= Html::encode($model->no_nota) ?></h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Alasan Pembatalan dari Kasir:</strong></p>
                            <blockquote class="blockquote fs-6 text-muted border-start ps-3">
                                "<?= Html::encode($model->alasan_batal ?? 'Tidak ada alasan') ?>"
                            </blockquote>
                            <hr>

                            <!-- Tambahan Field: Dropdown Opsi Pembatalan (id_opsi) -->
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Kategori / Opsi Pembatalan:</label>
                                <select name="id_opsi" class="form-select" required>
                                    <option value="">-- Pilih Opsi --</option>
                                    <option value="1">Kesalahan Input Kasir</option>
                                    <option value="2">Pelanggan Batal Beli</option>
                                    <option value="3">Barang Rusak / Cacat</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Masukkan Password Manager Anda untuk Menyetujui:</label>
                                <input type="password" name="password_manager" class="form-control" required placeholder="Password Manager">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tolak / Batal</button>
                            <button type="submit" class="btn btn-danger">Setujui & Rollback Stok</button>
                        </div>
                        <?php \yii\widgets\ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    <?php endforeach; ?>

</div>