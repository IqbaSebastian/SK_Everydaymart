<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $totalOmset float */
/* @var $totalTransaksi int */
/* @var $bulanLabels array */
/* @var $bulanOmset array */
/* @var $outletLabels array */
/* @var $outletOmset array */

$this->title = 'Laporan Omset & Analitik Penjualan';
?>

<!-- Style CSS Khusus Print Browser & PDF -->
<style>
@media print {
    /* Sembunyikan bagian bertanda no-print saat tombol browser print (Ctrl+P) */
    .no-print, header, nav, footer, .btn {
        display: none !important;
    }
}
</style>

<!-- Library Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Library html2pdf.js untuk Generate & Download PDF Otomatis -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<div class="laporan-penjualan">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?= Html::encode($this->title) ?></h2>
        <div>
            <!-- Tombol Mengunduh PDF -->
            <button id="btnDownloadPdf" class="btn btn-primary">
                <i class="fa fa-download"></i> Cetak Omset
            </button>
        </div>
    </div>

    <!-- Area yang akan di-convert ke PDF / Print -->
    <div id="areaCetak" class="p-2 bg-white">
        
        <!-- BAGIAN 1: KARTU SUMMARY (Disembunyikan Saat Print) -->
        <div class="row mb-4 no-print d-print-none">
            <div class="col-3">
                <div class="card border-0 shadow-sm rounded-3 bg-primary text-white p-3">
                    <div class="card-body p-1">
                        <h6 class="text-white-50 text-uppercase fw-bold mb-2" style="font-size: 0.8rem;">TOTAL OMSET</h6>
                        <h3 class="fw-bold mb-0 text-nowrap" style="font-size: 1.3rem;">
                            Rp <?= number_format($totalOmset ?? 0, 0, ',', '.') ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card border-0 shadow-sm rounded-3 bg-success text-white p-3">
                    <div class="card-body p-1">
                        <h6 class="text-white-50 text-uppercase fw-bold mb-2" style="font-size: 0.8rem;">TOTAL TRANSAKSI</h6>
                        <h3 class="fw-bold mb-0" style="font-size: 1.3rem;">
                            <?= number_format($totalTransaksi ?? 0, 0, ',', '.') ?> Nota
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card border-0 shadow-sm rounded-3 bg-warning text-dark p-3">
                    <div class="card-body p-1">
                        <h6 class="text-dark-50 text-uppercase fw-bold mb-2" style="font-size: 0.8rem;">RATA-RATA TRANSAKSI</h6>
                        <h3 class="fw-bold mb-0 text-nowrap" style="font-size: 1.3rem;">
                            Rp <?= $totalTransaksi > 0 ? number_format($totalOmset / $totalTransaksi, 0, ',', '.') : 0 ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card border-0 shadow-sm rounded-3 bg-info text-white p-3">
                    <div class="card-body p-1">
                        <h6 class="text-white-50 text-uppercase fw-bold mb-2" style="font-size: 0.8rem;">STATUS LAPORAN</h6>
                        <h3 class="fw-bold mb-0" style="font-size: 1.3rem;">Realtime</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- BAGIAN 2: GRAFIK / CHART (Disembunyikan Saat Print) -->
        <div class="row mb-4 no-print d-print-none">
            <div class="col-7">
                <div class="card border-0 shadow-sm rounded-3 p-3">
                    <div class="card-header bg-transparent border-0 fw-bold fs-5">
                        Grafik Penjualan Omset
                    </div>
                    <div class="card-body">
                        <canvas id="chartOmset" style="max-height: 250px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Grafik Kontribusi Outlet -->
            <div class="col-5">
                <div class="card border-0 shadow-sm rounded-3 p-3">
                    <div class="card-header bg-transparent border-0 fw-bold fs-5">
                        Kontribusi Outlet
                    </div>
                    <div class="card-body">
                        <canvas id="chartOutlet" style="max-height: 250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- BAGIAN 3: RINCIAN TRANSAKSI (Hanya Ini Yang Terlihat & Dicetak) -->
        <div class="card border-0 shadow-sm rounded-3 p-3">
            <div class="card-header bg-transparent border-0 fw-bold fs-5 mb-2">
                Rincian Transaksi
            </div>
            <div class="card-body p-0">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'summary' => false,
                    'tableOptions' => ['class' => 'table table-hover align-middle mb-0'],
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'attribute' => 'no_nota',
                            'label' => 'No Nota',
                            'format' => 'raw',
                            'value' => function($model) {
                                return '<strong>' . Html::encode($model->no_nota) . '</strong>';
                            }
                        ],
                        [
                            'attribute' => 'tgl_transaksi',
                            'label' => 'Tanggal & Waktu',
                            'value' => function($model) {
                                return date('d M Y H:i', strtotime($model->tgl_transaksi));
                            }
                        ],
                        [
                            'attribute' => 'id_outlet',
                            'label' => 'Outlet',
                            'value' => 'outlet.nama',
                        ],
                        [
                            'attribute' => 'customer',
                            'label' => 'Pelanggan',
                            'value' => function($model) {
                                return $model->customer ?: '-';
                            }
                        ],
                        [
                            'attribute' => 'total_harga',
                            'label' => 'Total Pembayaran',
                            'format' => 'raw',
                            'value' => function($model) {
                                return '<span class="fw-bold text-success">Rp ' . number_format($model->total_harga, 0, ',', '.') . '</span>';
                            }
                        ],
                    ],
                ]); ?>
            </div>
        </div>

    </div>

</div>

<?php
$jsonBulanLabels = json_encode($bulanLabels ?? []);
$jsonBulanOmset = json_encode($bulanOmset ?? []);
$jsonOutletLabels = json_encode($outletLabels ?? []);
$jsonOutletOmset = json_encode($outletOmset ?? []);

$script = <<<JS
// 1. Render Chart Omset
const ctxOmset = document.getElementById('chartOmset').getContext('2d');
new Chart(ctxOmset, {
    type: 'bar',
    data: {
        labels: {$jsonBulanLabels},
        datasets: [{
            label: 'Total Omset',
            data: {$jsonBulanOmset},
            backgroundColor: '#0d6efd',
            borderRadius: 6,
            barThickness: 30
        }]
    },
    options: {
        responsive: true,
        animation: false,
        plugins: {
            legend: { display: false }
        }
    }
});

// 2. Render Chart Outlet
const ctxOutlet = document.getElementById('chartOutlet').getContext('2d');
new Chart(ctxOutlet, {
    type: 'doughnut',
    data: {
        labels: {$jsonOutletLabels},
        datasets: [{
            data: {$jsonOutletOmset},
            backgroundColor: ['#0dcaf0', '#ffc107', '#198754', '#fd7e14', '#6f42c1'],
        }]
    },
    options: {
        responsive: true,
        animation: false,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

// 3. Fungsi Download PDF Otomatis (Mengabaikan Grafik & Summary)
document.getElementById('btnDownloadPdf').addEventListener('click', function () {
    const element = document.getElementById('areaCetak');
    const elementsToHide = element.querySelectorAll('.no-print');
    
    // Sembunyikan elemen non-cetak sebelum mengunduh PDF
    elementsToHide.forEach(el => el.style.display = 'none');

    // Opsi konfigurasi file PDF
    const opt = {
        margin:       0.5,
        filename:     'Laporan_Rincian_Transaksi.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, useCORS: true },
        jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' } // diset portrait agar cocok untuk tabel
    };

    // Ubah status tombol sementara
    const originalText = this.innerHTML;
    this.innerText = 'Mengunduh PDF...';
    this.disabled = true;

    // Eksekusi Pembuatan PDF
    html2pdf().set(opt).from(element).save().then(() => {
        // Kembalikan lagi elemen yang disembunyikan setelah PDF terunduh
        elementsToHide.forEach(el => el.style.display = '');
        this.innerHTML = originalText;
        this.disabled = false;
    });
});
JS;
$this->registerJs($script);
?>