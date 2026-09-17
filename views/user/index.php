<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Kelola User & Hak Akses';

$totalUsers = $dataProvider->getTotalCount();
$totalRoles = 4; 
$totalOutlets = \app\models\Outlet::find()->count();
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
    .user-index, 
    .user-index *,
    .user-index .card-summary *,
    .user-index .main-card * {
        box-sizing: border-box;
    }

    .user-index .card-summary {
        border: 1px solid #e2e8f0 !important;
        border-radius: 16px !important;
        background-color: #ffffff !important;
        padding: 20px !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important;
    }
    .user-index .icon-box {
        width: 50px !important;
        height: 50px !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 22px !important;
        flex-shrink: 0 !important;
    }

    .user-index .bg-purple-box { background-color: #f3e8ff !important; color: #7e22ce !important; }
    .user-index .bg-blue-box { background-color: #e0f2fe !important; color: #0284c7 !important; }
    .user-index .bg-orange-box { background-color: #ffedd5 !important; color: #ea580c !important; }
    
    .user-index .card-label { color: #64748b !important; font-size: 0.875rem !important; font-weight: 500 !important; }
    .user-index .card-value { color: #0f172a !important; font-weight: 700 !important; margin: 0 !important; }

    .user-index .main-card {
        border: 1px solid #e2e8f0 !important;
        border-radius: 20px !important;
        background-color: #ffffff !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05) !important;
        padding: 24px !important;
        margin-top: 15px !important;
    }

    .user-index .title-text { color: #0f172a !important; font-weight: 700 !important; margin: 0 !important; }
    .user-index .subtitle-text { color: #64748b !important; font-size: 0.875rem !important; }

    .user-index .badge-role-custom {
        background-color: #f3e8ff !important;
        color: #6b21a8 !important;
        border: 1px solid #d8b4fe !important;
        border-radius: 8px !important;
        padding: 6px 14px !important;
        font-weight: 700 !important;
        display: inline-block !important;
        font-size: 0.85rem !important;
        text-transform: capitalize;
    }

    .user-index table.custom-table {
        background-color: #ffffff !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        width: 100% !important;
    }
    .user-index table.custom-table th {
        background-color: #f8fafc !important;
        color: #0f172a !important;
        font-weight: 700 !important;
        border-bottom: 2px solid #e2e8f0 !important;
        padding: 12px !important;
    }
    .user-index table.custom-table td {
        background-color: #ffffff !important;
        color: #334155 !important;
        border-bottom: 1px solid #f1f5f9 !important;
        padding: 12px !important;
    }
</style>

<div class="user-index container-fluid py-3">

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card-summary d-flex align-items-center gap-3">
                <div class="icon-box bg-purple-box">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <span class="card-label d-block">Total User</span>
                    <h3 class="card-value"><?= number_format($totalUsers) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-summary d-flex align-items-center gap-3">
                <div class="icon-box bg-blue-box">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <div>
                    <span class="card-label d-block">Total Hak Akses (Role)</span>
                    <h3 class="card-value"><?= $totalRoles ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-summary d-flex align-items-center gap-3">
                <div class="icon-box bg-orange-box">
                    <i class="bi bi-shop"></i>
                </div>
                <div>
                    <span class="card-label d-block">Total Outlet Terdaftar</span>
                    <h3 class="card-value"><?= number_format($totalOutlets) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="main-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="title-text">All Users List</h4>
                <span class="subtitle-text">Kelola data akun pengguna dan penempatan outlet</span>
            </div>
            <div>
                <?= Html::a('<i class="bi bi-plus-lg me-1"></i> Tambah User', ['create'], ['class' => 'btn px-4 py-2', 'style' => 'border-radius: 10px; background-color: #6b21a8 !important; color: #ffffff !important; font-weight: 600; border: none;']) ?>
            </div>
        </div>

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
                    'attribute' => 'username',
                    'header' => 'User Name',
                    'format' => 'raw',
                    'value' => function($model) {
                        return '<div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px; background-color: #6b21a8 !important; color: #ffffff !important;">' 
                                . strtoupper(substr($model->username, 0, 1)) . 
                            '</div>
                            <div>
                                <span class="fw-bold d-block" style="color: #0f172a !important;">' . Html::encode($model->username) . '</span>
                            </div>
                        </div>';
                    }
                ],
                [
                    'header' => 'Role Access',
                    'format' => 'raw',
                    'value' => function($model) {
                        if (isset($model->roleRelation) && !empty($model->roleRelation->nama)) {
                            $userRole = $model->roleRelation->nama;
                        } 
                        else {
                            $roleId = $model->role_id ?? $model->role ?? null;
                            $roleMap = [
                                1 => 'ADMIN',
                                2 => 'MANAGER',
                                3 => 'KASIR',
                                4 => 'PETUGAS GUDANG',
                            ];

                            if (is_numeric($roleId) && isset($roleMap[$roleId])) {
                                $userRole = $roleMap[$roleId];
                            } else {
                                $userRole = !empty($roleId) ? $roleId : 'Kasir';
                            }
                        }

                        return '<span class="badge-role-custom">' . Html::encode($userRole) . '</span>';
                    }
                ],
                [
                    'label' => 'Outlet Assignment',
                    'format' => 'raw',
                    'value' => function($model) {
                        $outletName = $model->outlet ? $model->outlet->nama : 'Semua Outlet (Global)';
                        return '<span style="color: #334155 !important; font-weight: 600;">' . Html::encode($outletName) . '</span>';
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => 'Action',
                    'template' => '{update} {delete}',
                    'headerOptions' => ['style' => 'text-align: center; width: 100px;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                    'buttons' => [
                        'update' => function ($url) {
                            return Html::a('<i class="bi bi-pencil-square fs-5 me-2" style="color: #eab308 !important;"></i>', $url, ['title' => 'Edit']);
                        },
                        'delete' => function ($url) {
                            return Html::a('<i class="bi bi-trash3-fill fs-5" style="color: #ef4444 !important;"></i>', $url, [
                                'title' => 'Delete',
                                'data-confirm' => 'Apakah Anda yakin ingin menghapus user ini?',
                                'data-method' => 'post',
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>