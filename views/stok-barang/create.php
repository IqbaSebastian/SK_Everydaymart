<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Barang;
use app\models\Outlet;

/** @var yii\web\View $this */
/** @var app\models\StokBarang $model */

$this->title = 'Input Barang Masuk (Tambah Stok)';
$this->params['breadcrumbs'][] = ['label' => 'Stok Barang', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user->identity;
?>
<div class="stok-barang-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="stok-barang-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'id_barang')->dropDownList(
            ArrayHelper::map(Barang::find()->all(), 'id', function($barang) {
                return $barang->nama_barang ?? $barang->nama ?? $barang->nama_produk ?? ('Barang #' . $barang->id);
            }),
            ['prompt' => '-- Pilih Barang --', 'required' => true]
        )->label('Nama Barang') ?>

        <?= $form->field($model, 'jumlah_stok')->textInput([
            'type' => 'number', 
            'min' => 1, 
            'required' => true,
            'placeholder' => 'Masukkan jumlah barang masuk'
        ])->label('Jumlah Stok Masuk') ?>

        <div class="form-group mt-3">
            <?= Html::submitButton('Simpan & Tambah Stok', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Batal', ['index'], ['class' => 'btn btn-secondary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>