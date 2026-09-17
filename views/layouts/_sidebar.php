<?php

declare(strict_types=1);

use yii\bootstrap5\Nav;
use yii\helpers\Html;
use yii\helpers\Url;

$user = Yii::$app->user->identity;

$sidebarItems = [
    [
        'label' => '🏠 Home / Katalog',
        'url' => ['/site/index'],
        'visible' => !Yii::$app->user->isGuest && $user?->isKasir()
    ],
    [
        'label' => '📦 Master System',
        'items' => [
            ['label' => 'Kelola Outlet', 'url' => ['/outlet/index']],
            ['label' => 'Kelola User & Role', 'url' => ['/user/index']],
        ],
        'visible' => !Yii::$app->user->isGuest && $user?->isSuperAdmin(),
    ],
    [
        'label' => '🛒 Transaksi',
        'items' => [
            [
                'label' => 'Laporan Omset',
                'url' => ['/transaksi/laporan'],
                'visible' => !Yii::$app->user->isGuest && ($user?->isSuperAdmin() || $user?->isManager()),
            ],
            [
                'label' => 'Kasir / POS',
                'url' => ['/transaksi/create'],
                'visible' => !Yii::$app->user->isGuest && $user?->isKasir(),
            ],
            [
                'label' => 'Riwayat Penjualan',
                'url' => ['/transaksi/index'],
                'visible' => !Yii::$app->user->isGuest && ($user?->isSuperAdmin() || $user?->isManager() || $user?->isKasir()),
            ],
        ],
        'visible' => !Yii::$app->user->isGuest && ($user?->isSuperAdmin() || $user?->isManager() || $user?->isKasir()),
    ],
    [
        'label' => '📊 Manajemen Stok',
        'items' => [
            [
                'label' => 'Kategori Barang', 
                'url' => ['/group/index'],
                'visible' => !Yii::$app->user->isGuest && $user?->isSuperAdmin(),
            ],
            [
                'label' => 'Data Barang', 
                'url' => ['/barang/index'],
                'visible' => !Yii::$app->user->isGuest && $user?->isSuperAdmin(),
            ],
            [
                'label' => 'Stok Barang Outlet', 
                'url' => ['/stok-barang/index'],
                'visible' => !Yii::$app->user->isGuest && ($user?->isKasir() || $user?->isGudang() || $user?->isManager() || $user?->isSuperAdmin()),
            ],
            [
                'label' => 'Penerimaan Stok (Restock)', 
                'url' => ['/pembelian/create'],
                'visible' => !Yii::$app->user->isGuest && $user?->isGudang(),
            ],
            [
                'label' => 'Nota Pembelian', 
                'url' => ['/pembelian/index'],
                'visible' => !Yii::$app->user->isGuest && $user?->isGudang(),
            ],
        ],
        'visible' => !Yii::$app->user->isGuest && ($user?->isGudang() || $user?->isSuperAdmin() || $user?->isManager() || $user?->isKasir()),
    ],
];
?>

<!-- ISI MENU SIDEBAR -->
<div class="d-flex flex-column h-100 p-3 bg-dark text-white">
    <a href="<?= Url::to(['/site/index']) ?>" class="d-flex align-items-center mb-3 text-white text-decoration-none px-2 pt-2">
        <span class="fs-4 fw-bold">👕 EverydayMart</span>
    </a>
    <hr class="text-secondary my-2">

    <div class="sidebar-nav flex-grow-1">
        <?= Nav::widget([
            'options' => ['class' => 'nav nav-pills flex-column nav-dark-custom'],
            'items' => $sidebarItems,
        ]) ?>
    </div>

    <hr class="text-secondary my-2">
    <div class="px-2 small text-white">
        Status: 
        <strong class="<?= Yii::$app->user->isGuest ? 'text-warning' : 'text-success' ?>">
            <?= Yii::$app->user->isGuest ? 'Guest' : Html::encode($user->username) ?>
        </strong>
    </div>
</div>

<style>
.sidebar-nav .nav-link {
    color: #cbd5e1 !important;
    padding: 10px 14px;
    margin-bottom: 4px;
    border-radius: 6px;
    font-size: 0.95rem;
}
.sidebar-nav .nav-link:hover, 
.sidebar-nav .nav-link.active {
    background-color: #0d6efd !important;
    color: #ffffff !important;
}
.sidebar-nav .dropdown-menu {
    background-color: #1e293b;
    border: none;
    margin-left: 10px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.3);
}
.sidebar-nav .dropdown-item {
    color: #cbd5e1;
    padding: 8px 16px;
}
.sidebar-nav .dropdown-item:hover {
    background-color: #334155;
    color: #ffffff;
}
</style>