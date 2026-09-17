<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var array $pembelianList */

$this->title = 'Daftar Penerimaan Barang (Pembelian)';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pembelian-index">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a('+ Input Barang Masuk', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped table-bordered m-0">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th>No. Nota</th>
                        <th>Supplier / Pemasok</th>
                        <th>Tanggal</th>
                        <th>Outlet</th>
                        <th style="width: 100px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pembelianList)): ?>
                        <?php foreach ($pembelianList as $index => $item): ?>
                            <tr>
                                <td class="text-center"><?= $index + 1 ?></td>
                                <td>
                                    <strong><?= Html::encode($item->no_nota) ?></strong>
                                </td>
                                <td><?= Html::encode($item->supplier ?? '-') ?></td>
                                <td><?= Html::encode(date('Y-m-d', strtotime($item->tanggal))) ?></td>
                                <td>
                                    <?= Html::encode($item->outlet->nama_outlet ?? $item->outlet->nama ?? ('Outlet #' . $item->outlet_id)) ?>
                                </td>
                                <td class="text-center">
                                    <?= Html::a('Detail', ['view', 'id' => $item->id], ['class' => 'btn btn-info btn-sm']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Belum ada data penerimaan barang.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>