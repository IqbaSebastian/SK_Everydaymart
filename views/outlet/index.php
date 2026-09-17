<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Kelola Outlet & Cabang';
?>

<!-- Load Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
    /* Isolation dari Dark Mode */
    .outlet-index, 
    .outlet-index *,
    .outlet-index .main-card * {
        box-sizing: border-box;
    }

    /* Main Container Card */
    .outlet-index .main-card {
        border: 1px solid #e2e8f0 !important;
        border-radius: 20px !important;
        background-color: #ffffff !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05) !important;
        padding: 24px !important;
        margin-top: 10px !important;
    }

    .outlet-index .title-text { color: #0f172a !important; font-weight: 700 !important; margin: 0 !important; }
    .outlet-index .subtitle-text { color: #64748b !important; font-size: 0.875rem !important; }

    /* Custom Badges Statistik */
    .outlet-index .info-badge {
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        padding: 4px 10px !important;
        border-radius: 6px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 5px !important;
    }
    .outlet-index .badge-user {
        background-color: #f3e8ff !important;
        color: #7e22ce !important;
        border: 1px solid #d8b4fe !important;
    }
    /* .outlet-index .badge-barang {
        background-color: #e0f2fe !important;
        color: #0284c7 !important;
        border: 1px solid #bae6fd !important;
    } */

    /* Table Reset & Styling */
    .outlet-index table.custom-table {
        background-color: #ffffff !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        width: 100% !important;
    }
    .outlet-index table.custom-table th {
        background-color: #f8fafc !important;
        color: #0f172a !important;
        font-weight: 700 !important;
        border-bottom: 2px solid #e2e8f0 !important;
        padding: 14px 12px !important;
    }
    .outlet-index table.custom-table td {
        background-color: #ffffff !important;
        color: #334155 !important;
        border-bottom: 1px solid #f1f5f9 !important;
        padding: 14px 12px !important;
    }
</style>

<div class="outlet-index container-fluid py-3">

    <!-- MAIN TABLE CARD -->
    <div class="main-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="title-text">All Outlets List</h4>
                <span class="subtitle-text">Kelola data cabang toko, lokasi operasional, dan statistik outlet</span>
            </div>
            <div>
                <?= Html::a('<i class="bi bi-plus-lg me-1"></i> Tambah Outlet', ['create'], ['class' => 'btn px-4 py-2', 'style' => 'border-radius: 10px; background-color: #ea580c !important; color: #ffffff !important; font-weight: 600; border: none;']) ?>
            </div>
        </div>

        <!-- YII2 GRIDVIEW -->
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table custom-table align-middle m-0'],
            'summary' => '<div style="color: #64748b !important; font-size: 0.875rem;" class="mb-3">Showing {begin} to {end} of {totalCount} entries</div>',
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'headerOptions' => ['style' => 'width: 50px; text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center; color: #475569 !important; font-weight: 600;'],
                ],
                [
                    'attribute' => 'nama',
                    'header' => 'Nama & Statistik Outlet',
                    'format' => 'raw',
                    'value' => function($model) {
                        $stafCount = 0;
                        try {
                            if (isset($model->users)) {
                                $stafCount = count($model->users);
                            } else {
                                $stafCount = \app\models\User::find()->where(['id_outlet' => $model->id])->count();
                            }
                        } catch (\Exception $e) {
                            $stafCount = 0;
                        }

                        // $barangCount = 0;
                        // try {
                        //     if (isset($model->barangs)) {
                        //         $barangCount = count($model->barangs);
                        //     } else {
                        //         $barangCount = \app\models\Barang::find()->where(['id_outlet' => $model->id])->count();
                        //     }
                        // } catch (\Exception $e) {
                        //     $barangCount = 0;
                        // }

                        return '<div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 42px; height: 42px; background-color: #ea580c !important; color: #ffffff !important;">' 
                                . strtoupper(substr($model->nama, 0, 1)) . 
                            '</div>
                            <div>
                                <span class="fw-bold d-block text-dark mb-1" style="color: #0f172a !important; font-size: 0.95rem;">' . Html::encode($model->nama) . '</span>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="info-badge badge-user"><i class="bi bi-people-fill"></i> ' . $stafCount . ' Staf</span>
                                </div>
                            </div>
                        </div>';
                    }
                ],               
                [
                    'attribute' => 'alamat',
                    'header' => 'Alamat Cabang',
                    'format' => 'raw',
                    'value' => function($model) {
                        $alamat = !empty($model->alamat) ? $model->alamat : 'Alamat belum diisi';
                        return '<span style="color: #334155 !important; font-weight: 500;">' . Html::encode($alamat) . '</span>';
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => 'Action',
                    'template' => '{view} {update} {delete}', // Menambahkan aksi {view} kembali
                    'headerOptions' => ['style' => 'text-align: center; width: 130px;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                    'buttons' => [
                        'view' => function ($url) {
                            return Html::a('<i class="bi bi-eye-fill fs-5 me-2" style="color: #0284c7 !important;"></i>', $url, ['title' => 'Detail Outlet']);
                        },
                        'update' => function ($url) {
                            return Html::a('<i class="bi bi-pencil-square fs-5 me-2" style="color: #eab308 !important;"></i>', $url, ['title' => 'Edit']);
                        },
                        'delete' => function ($url) {
                            return Html::a('<i class="bi bi-trash3-fill fs-5" style="color: #ef4444 !important;"></i>', $url, [
                                'title' => 'Delete',
                                'data-confirm' => 'Apakah Anda yakin ingin menghapus outlet ini?',
                                'data-method' => 'post',
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>