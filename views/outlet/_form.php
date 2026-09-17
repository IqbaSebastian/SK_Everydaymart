<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Outlet */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="outlet-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'nama')->textInput([
                'maxlength' => true,
                'placeholder' => '...'
                ])->label('Nama Outlet') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'kode_nota')->textInput([
                'maxlength' => true, 
                'placeholder' => 'EVD-xxx-xxx'
                ])->label('Kode Prefix Nota') ?>
        </div>
    </div>

    <?= $form->field($model, 'alamat')->textarea([
        'rows' => 3,
        'placeholder' => '...'
    ]) ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'no_telp')->textInput(['maxlength' => true, 'placeholder' => '+62'])->label('No. Telepon') ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'ppn')->textInput([
                'type' => 'number', 
                'step' => '0.01',
                'value' => $model->isNewRecord ? 11 : $model->ppn,
                'placeholder' => '11'
            ])->label('PPN (%)') ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'charge')->textInput([
                'type' => 'number', 
                'step' => '0.01',
                'value' => $model->isNewRecord ? 0 : $model->charge,
                'placeholder' => '0'
            ])->label('Charge') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'has_room')->dropDownList([
                1 => 'Iya',
                0 => 'Tidak'
            ])->label('Fasilitas Room') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'nomor_berjalan')->textInput(['type' => 'number'])->label('Nomor Berjalan Nota') ?>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton('Simpan Outlet', ['class' => 'btn btn-success fw-bold']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>