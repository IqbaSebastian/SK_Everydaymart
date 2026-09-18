<?php

use app\models\Group;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\GroupSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

// 1. Pengecekan role Admin
$user = Yii::$app->user->identity;
$isAdmin = $user && (
    (isset($user->role) && strtolower($user->role) === 'admin') ||
    (isset($user->level) && strtolower($user->level) === 'admin')
);

$this->title = 'Kategori';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // 2. Tombol Create Kategori hanya muncul jika user adalah Admin ?>
    <?php if ($isAdmin): ?>
        <p>
            <?= Html::a('Create Kategori', ['create'], ['class' => 'btn btn-success']) ?>
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
                'deskripsi:ntext',
            ],
            // 3. ActionColumn (View, Edit, Delete) hanya digabungkan jika user adalah Admin
            $isAdmin ? [
                [
                    'class' => ActionColumn::className(),
                    'urlCreator' => function ($action, Group $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'id' => $model->id]);
                    }
                ],
            ] : []
        ),
    ]); ?>

</div>