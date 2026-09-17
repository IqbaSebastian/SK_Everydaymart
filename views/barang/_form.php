<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Group;
use app\models\StockRak;
use app\models\Ppn;

/* @var $this yii\web\View */
/* @var $model app\models\Barang */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="barang-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'nama')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'satuan')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'harga')->textInput(['type' => 'number']) ?>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-4">
            <?= $form->field($model, 'id_group')->dropDownList(
                ArrayHelper::map(Group::find()->all(), 'id', 'nama'),
                ['prompt' => '-- Pilih Group / Kategori --']
            ) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'id_stock_rak')->dropDownList(
                ArrayHelper::map(StockRak::find()->all(), 'id', 'nama'),
                ['prompt' => '-- Pilih Lokasi Rak --']
            ) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'id_ppn')->dropDownList(
                ArrayHelper::map(Ppn::find()->all(), 'id', 'nama'),
                ['prompt' => '-- Pilih Jenis PPN --']
            ) ?>
        </div>
    </div>

    <!-- Input File Foto Produk -->
    <div class="row mt-2">
        <div class="col-md-12">
            <?= $form->field($model, 'imageFile')->fileInput(['class' => 'form-control'])->label('Foto Produk') ?>
            
            <?php if (!$model->isNewRecord && $model->foto): ?>
                <div class="mt-2">
                    <label class="form-label d-block text-muted">Preview Foto Saat Ini:</label>
                    <?= Html::img('@web/uploads/' . $model->foto, ['width' => '120', 'class' => 'img-thumbnail rounded shadow-sm']) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-group mt-4">
        <?= Html::submitButton('Simpan Barang', ['class' => 'btn btn-success fw-bold']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>