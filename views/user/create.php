<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Outlet;

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->title = 'Tambah User Baru';
$this->params['breadcrumbs'][] = ['label' => 'User', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create container py-3" style="max-width: 600px;">

    <h3><?= Html::encode($this->title) ?></h3>

    <div class="card border-0 shadow-sm p-4 mt-3">
        <?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'username')->textInput(['required' => true]) ?>
        
        <?= $form->field($model, 'name')->textInput()->label('Nama Lengkap') ?>

        <?= $form->field($model, 'password')->passwordInput()->label('Password') ?>

        <?= $form->field($model, 'role_id')->dropDownList([
            2 => 'Manager Outlet',
            3 => 'Kasir',
            4 => 'Petugas Gudang',
        ], ['prompt' => '-- Pilih Role Staf --', 'required' => true])->label('Role Akses') ?>

        <?= $form->field($model, 'id_outlet')->dropDownList(
            ArrayHelper::map(app\models\Outlet::find()->all(), 'id', function($o) {
                return $o->nama_outlet ?? $o->nama ?? ('Outlet #' . $o->id);
            }),
            ['prompt' => '-- Tanpa Outlet (Khusus Super Admin) --']
        )->label('Outlet Penugasan') ?>

        <div class="form-group mt-4">
            <?= Html::submitButton('Simpan User', ['class' => 'btn btn-success fw-bold']) ?>
            <?= Html::a('Batal', ['index'], ['class' => 'btn btn-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>