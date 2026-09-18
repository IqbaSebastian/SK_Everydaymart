<?php

use app\models\Barang;
use app\models\Group;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\BarangSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

// 1. Pengecekan status Admin
$user = Yii::$app->user->identity;
$isAdmin = $user && (
    (isset($user->role) && strtolower($user->role) === 'admin') ||
    (isset($user->level) && strtolower($user->level) === 'admin')
);

$this->title = 'Barang';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="barang-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // 2. Tombol Create hanya muncul jika user adalah Admin ?>
    <?php if ($isAdmin): ?>
        <p>
            <?= Html::a('Create Barang', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    <?php endif; ?>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => array_merge(
            [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                'nama',
                'satuan',
                'harga',
                
                // FILTER DROPDOWN GROUP / KATEGORI
                [
                    'attribute' => 'id_group',
                    'label' => 'Group / Kategori',
                    'filter' => ArrayHelper::map(Group::find()->all(), 'id', 'nama'),
                    'value' => function ($model) {
                        return $model->group->nama 
                            ?? $model->group->nama_group 
                            ?? $model->group->nama_kategori 
                            ?? null;
                    },
                ],

                [
                    'label' => 'Total Stok',
                    'value' => function ($model) {
                        return \app\models\StokBarang::find()
                            ->where(['id_barang' => $model->id])
                            ->sum('jumlah_stok') ?? 0;
                    },
                ],
            ],
            // 3. ActionColumn (View, Edit, Delete) hanya digabungkan jika user adalah Admin
            $isAdmin ? [
                [
                    'class' => ActionColumn::className(),
                    'urlCreator' => function ($action, Barang $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'id' => $model->id]);
                    }
                ],
            ] : []
        ),
    ]); ?>

</div>