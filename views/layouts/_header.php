<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\bootstrap5\Nav;
use yii\helpers\Html;
use yii\helpers\Url;

$user = Yii::$app->user->identity;

// Item menu gabungan untuk desktop navbar
$navItems = [
    [
        'label' => 'Katalog',
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

<header class="bg-white border-bottom shadow-sm px-3 py-2">
    <div class="container-fluid d-flex align-items-center justify-content-between">
        <!-- BRANDING & MENU DESKTOP -->
        <div class="d-flex align-items-center gap-3">
            <!-- Tombol Hamburger untuk Layar HP -->
            <button class="btn btn-outline-dark d-md-none p-1 px-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                ☰
            </button>

            <!-- Brand Logo EverydayMart -->
            <a href="<?= Url::to(['/site/index']) ?>" class="navbar-brand fw-bold fs-4 m-0 text-dark text-decoration-none">
                🏪 EverydayMart
            </a>

            <!-- Navigation Links (Desktop Layar Besar) -->
            <div class="d-none d-md-block ms-3">
                <?= Nav::widget([
                    'options' => ['class' => 'nav nav-pills gap-1'],
                    'items' => $navItems,
                ]) ?>
            </div>
        </div>

        <!-- AUTHENTICATION (USER / LOGIN) -->
        <div>
            <?php if (Yii::$app->user->isGuest): ?>
                <a href="<?= Url::to(['/site/login']) ?>" class="btn btn-sm btn-outline-primary fw-semibold px-3">
                    Login
                </a>
            <?php else: ?>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark border px-2 py-2 fs-6">
                        👤 <?= Html::encode($user?->username) ?>
                    </span>
                    <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'd-inline'])
                        . Html::submitButton('Logout', ['class' => 'btn btn-sm btn-outline-danger fw-semibold'])
                        . Html::endForm() ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>