<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var string $content */

use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\helpers\Html;

$this->render('_head');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <?php $this->head() ?>
    <title><?= Html::encode($this->title) ?></title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .app-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: 100%;
        }
        .content-area {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        /* --- STYLES FOR MOBILE RESPONSIVE --- */
        @media (max-width: 767.98px) {
            /* Agar semua tabel otomatis memiliki scrollbar horizontal & tidak merusak lebar screen */
            .grid-view, .table-responsive, .table {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            /* Bikin layout flex header (seperti judul & tombol tambah) menjadi bertumpuk vertikal */
            .d-flex.justify-content-between {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 12px;
            }

            /* Mengatur tombol aksi utama agar melebar penuh di tampilan HP */
            .d-flex.justify-content-between .btn {
                width: 100% !important;
            }

            /* Mengurangi padding konten agar pas di layar kecil */
            main#main {
                padding: 0.75rem !important;
            }
        }
    </style>
</head>
<body>
<?php $this->beginBody() ?>

<div class="app-container">
    <!-- HEADER ATAS (NAVBAR UTAMA DESKTOP & TOGGLE MOBILE) -->
    <?= $this->render('_header') ?>

    <!-- SIDEBAR VERSI MOBILE / OFFCANVAS (Muncul saat tombol ☰ diklik di HP) -->
    <div class="offcanvas offcanvas-start bg-dark text-white p-0" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel" style="width: 260px;">
        <div class="offcanvas-header pb-0">
            <button type="button" class="btn-close btn-close-white text-reset ms-auto" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <?= $this->render('_sidebar') ?>
        </div>
    </div>

    <!-- AREA KONTEN UTAMA -->
    <div class="content-area">
        <main id="main" class="flex-grow-1 p-3 p-md-4" role="main">
            <div class="container-fluid">
                <?php if (!empty($this->params['breadcrumbs'])): ?>
                    <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
                <?php endif ?>
                <?= Alert::widget() ?>
                <?= $content ?>
            </div>
        </main>

        <!-- FOOTER -->
        <?= $this->render('_footer') ?>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>