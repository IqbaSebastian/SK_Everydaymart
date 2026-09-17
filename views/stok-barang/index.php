<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\StokBarangSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Stok Barang Outlet';
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user->identity;

$columns = [
    ['class' => 'yii\grid\SerialColumn'],
    [
        'attribute' => 'id_barang',
        'label' => 'Nama Barang',
        'value' => function ($model) {
            $barang = $model->barang;
            if ($barang) {
                return $barang->nama_barang ?? $barang->nama ?? $barang->nama_produk ?? ('Barang #' . $barang->id);
            }
            return '-';
        },
    ],
];

if ($user && $user->isSuperAdmin()) {
    $columns[] = [
        'attribute' => 'id_outlet',
        'label' => 'Outlet',
        'value' => function ($model) {
            return $model->outlet->nama_outlet ?? $model->outlet->nama ?? ('Outlet #' . $model->id_outlet);
        },
    ];
}

$columns[] = [
    'attribute' => 'jumlah_stok',
    'label' => 'Jumlah Stok',
    'value' => function ($model) {
        return number_format($model->jumlah_stok, 0, ',', '.') . ' Pcs';
    },
];
?>
<div class="stok-barang-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if ($user && $user->isGudang()): ?>
            <?= Html::a('<i class="bi bi-plus-lg"></i> Input Barang Masuk', ['pembelian/create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $columns,
    ]); ?>

</div>