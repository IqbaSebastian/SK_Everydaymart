<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Barang $model */

$this->title = $model->nama ?? $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Barangs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="barang-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'id',
                'label' => 'ID Barang',
            ],
            [
                'attribute' => 'nama',
                'label' => 'Nama Barang',
            ],
            [
                'attribute' => 'satuan',
                'label' => 'Satuan (Pcs/Kg/Pack)',
            ],
            [
                'attribute' => 'harga',
                'label' => 'Harga Jual (Rp)',
                'value' => function($model) {
                    return 'Rp ' . number_format($model->harga ?? 0, 0, ',', '.');
                }
            ],
            // 1. Tampilan Group / Kategori
            [
                'attribute' => 'id_group',
                'label' => 'Group / Kategori',
                'value' => function($model) {
                    // Mengambil relasi ke Group/Kategori jika ada, atau fallback ke ID
                    return $model->group->nama ?? $model->group->nama_group ?? ('Kategori #' . $model->id_group);
                }
            ],
            [
                'attribute' => 'id_stock_rak',
                'label' => 'Lokasi Rak',
                'value' => function($model) {
                    // Pengecekan relasi getStockRak() atau relasi lainnya
                    if (isset($model->stockRak)) {
                        return $model->stockRak->nama_rak ?? $model->stockRak->nama ?? $model->stockRak->lokasi;
                    }
                    
                    if (isset($model->rak)) {
                        return $model->rak->nama_rak ?? $model->rak->nama ?? $model->rak->lokasi;
                    }

                    return 'Rak ID: ' . $model->id_stock_rak;
                }
            ],
            // 3. Tampilan Skema PPN
            [
                'attribute' => 'id_ppn',
                'label' => 'Skema PPN',
                'value' => function($model) {
                    // Mengambil nama relasi PPN dari DB jika ada
                    if (isset($model->ppn->nama)) {
                        return $model->ppn->nama;
                    }
                    
                    // Pemetaan manual jika id_ppn berupa pilihan opsi static
                    $skemaPpn = [
                        1 => 'Non PPN (0%)',
                        2 => 'Include PPN (11%)',
                        3 => 'Exclude PPN (11%)',
                    ];
                    
                    return $skemaPpn[$model->id_ppn] ?? ('PPN #' . $model->id_ppn);
                }
            ],
        ],
    ]) ?>

</div>