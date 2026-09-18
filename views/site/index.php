<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $barangs app\models\Barang[] */
/* @var $stokBarang app\models\StokBarang[] */

$user = Yii::$app->user->identity;
$isKasir = !Yii::$app->user->isGuest && $user?->isKasir();
$userOutlet = $user?->outlet ?? null;

$this->title = $isKasir ? 'Kasir POS - EverydayMart' : 'Welcome - EverydayMart';
?>

<?php if (!$isKasir): ?>
    <!-- TAMPILAN NON-KASIR / GUEST / ADMIN / MANAGER / GUDANG -->
    <div class="d-flex align-items-center justify-content-center border rounded bg-white shadow-sm p-5 my-4 text-center" style="min-height: 60vh;">
        <div>
            <h1 class="display-3 fw-bold text-primary mb-3">Welcome, EverydayMart</h1>
            <p class="lead text-muted">Selamat datang di Sistem Manajemen Operasional EverydayMart.</p>
            
            <?php if (Yii::$app->user->isGuest): ?>
                <div class="mt-4">
                    <a href="<?= Url::to(['/site/login']) ?>" class="btn btn-primary btn-lg px-4 fw-semibold">
                        🔑 Login Sistem
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php else: ?>
    <!-- TAMPILAN KASIR POS (KATALOG & SIDEBAR CART SEMENTARA) -->
    <div class="site-index">
        <div class="row g-3">
            
            <!-- BAGIAN KIRI: KATALOG PRODUK (~70%) -->
            <div class="col-lg-8 col-md-7">
                <div class="bg-white p-3 rounded border shadow-sm mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h4 class="fw-bold m-0 text-dark">🏪 Katalog Produk</h4>
                        <small class="text-muted">Cabang: <strong class="text-primary"><?= Html::encode($userOutlet->nama ?? 'Semua Outlet') ?></strong></small>
                    </div>
                    <!-- Form Cari Barang Cepat -->
                    <div style="max-width: 280px;" class="w-100">
                        <input type="text" id="search-barang" class="form-control form-control-sm" placeholder="🔍 Cari nama barang...">
                    </div>
                </div>

                <!-- Grid Card Produk -->
                <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-3" id="container-katalog">
                    <?php foreach ($barangs as $barang): 
                        $stok = isset($stokBarang[$barang->id]) ? (int)$stokBarang[$barang->id]->jumlah_stok : 0;
                        $ppnVal = $barang->ppn_id ?? $barang->id_ppn ?? $barang->ppn ?? 0;
                        $isPpn = ($ppnVal == 2);
                        
                        $foto = $barang->foto ?? $barang->gambar ?? null;
                        if (!empty($foto) && file_exists(Yii::getAlias('@webroot/uploads/' . $foto))) {
                            $imgUrl = Url::to('@web/uploads/' . $foto);
                        } else {
                            $imgUrl = 'https://placehold.co/400x300/f8f9fa/6c757d?text=' . urlencode($barang->nama);
                        }
                    ?>
                        <div class="col item-barang" data-nama="<?= strtolower(Html::encode($barang->nama)) ?>">
                            <div class="card h-100 rounded-3 shadow-sm border-0 position-relative">
                                
                                <div class="position-relative overflow-hidden rounded-top" style="height: 140px; background-color: #f8f9fa;">
                                    <img src="<?= $imgUrl ?>" class="card-img-top w-100 h-100" style="object-fit: cover;" alt="<?= Html::encode($barang->nama) ?>" onerror="this.src='https://placehold.co/400x300/f8f9fa/6c757d?text=Produk';">
                                    
                                    <div class="position-absolute top-0 start-0 p-2">
                                        <span class="badge <?= $isPpn ? 'bg-info' : 'bg-secondary' ?>">
                                            <?= $isPpn ? 'PPN' : 'Non-PPN' ?>
                                        </span>
                                    </div>
                                    <div class="position-absolute top-0 end-0 p-2">
                                        <?php if ($stok > 0): ?>
                                            <span class="badge bg-success">Stok: <?= $stok ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Habis</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="card-body d-flex flex-column p-3">
                                    <h6 class="card-title fw-bold text-dark mb-1 text-truncate" title="<?= Html::encode($barang->nama) ?>">
                                        <?= Html::encode($barang->nama) ?>
                                    </h6>
                                    
                                    <div class="mt-auto pt-2 border-top">
                                        <div class="fw-bold text-primary fs-6 mb-2">
                                            Rp <?= number_format((float)$barang->harga, 0, ',', '.') ?>
                                        </div>

                                        <button type="button" 
                                                class="btn btn-primary btn-sm w-100 btn-add-cart fw-semibold" 
                                                data-id="<?= $barang->id ?>" 
                                                data-nama="<?= Html::encode($barang->nama) ?>"
                                                data-harga="<?= (float)$barang->harga ?>"
                                                data-stok="<?= $stok ?>"
                                                <?= $stok <= 0 ? 'disabled' : '' ?>>
                                            ➕ Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- BAGIAN KANAN: SIDEBAR CART SEMENTARA (~30%) -->
            <div class="col-lg-4 col-md-5">
                <div class="bg-white p-3 rounded border shadow-sm sticky-top" style="top: 20px; z-index: 100;">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                        <h5 class="fw-bold m-0 text-dark">🛒 Keranjang Sementara</h5>
                        <button class="btn btn-outline-danger btn-sm px-2" id="btn-clear-cart" title="Kosongkan Keranjang">🗑️ Bersihkan</button>
                    </div>

                    <!-- Daftar Item Keranjang -->
                    <div id="cart-list" class="mb-3 overflow-auto" style="max-height: 320px; min-height: 140px;">
                        <div class="text-center text-muted py-4" id="cart-empty-msg">
                            <p class="m-0 fs-6">Keranjang masih kosong.</p>
                            <small>Klik "+ Tambah" pada barang di katalog.</small>
                        </div>
                    </div>

                    <!-- Ringkasan & Tombol Pindah ke Transaksi -->
                    <div class="border-top pt-3 bg-light p-3 rounded">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-semibold text-muted">Total Estimasi:</span>
                            <span class="fw-bold fs-5 text-primary" id="text-total-bayar">Rp 0</span>
                        </div>

                        <a href="<?= Url::to(['/transaksi/create']) ?>" class="btn btn-success btn-lg w-100 fw-bold shadow-sm disabled" id="btn-lanjut-pos">
                            💳 Lanjut ke Pembayaran POS ➔
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php
    $script = <<<JS
    // Ambil data keranjang dari localStorage jika ada
    let cart = JSON.parse(localStorage.getItem('everyday_cart')) || [];
    renderCart();

    // Filter Pencarian Barang Realtime
    $('#search-barang').on('keyup', function() {
        let value = $(this).val().toLowerCase();
        $('.item-barang').filter(function() {
            $(this).toggle($(this).data('nama').indexOf(value) > -1);
        });
    });

    // Tambah Barang ke Keranjang
    $('.btn-add-cart').on('click', function() {
        let id = $(this).data('id');
        let nama = $(this).data('nama');
        let harga = parseFloat($(this).data('harga'));
        let maxStok = parseInt($(this).data('stok'));

        let item = cart.find(i => i.id === id);

        if (item) {
            if (item.qty < maxStok) {
                item.qty++;
            } else {
                alert('Stok barang tidak mencukupi!');
                return;
            }
        } else {
            cart.push({ id: id, nama: nama, harga: harga, qty: 1, maxStok: maxStok });
        }

        saveAndRender();
    });

    function saveAndRender() {
        localStorage.setItem('everyday_cart', JSON.stringify(cart));
        renderCart();
    }

    // Render Ulang Daftar Keranjang
    function renderCart() {
        let container = $('#cart-list');
        container.empty();

        if (cart.length === 0) {
            container.html('<div class="text-center text-muted py-4"><p class="m-0 fs-6">Keranjang masih kosong.</p><small>Klik "+ Tambah" pada barang di katalog.</small></div>');
            $('#text-total-bayar').text('Rp 0');
            $('#btn-lanjut-pos').addClass('disabled');
            return;
        }

        let total = 0;
        cart.forEach((item, index) => {
            let subtotal = item.harga * item.qty;
            total += subtotal;

            let html = `
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <div style="max-width: 55%;">
                        <div class="fw-bold text-dark text-truncate small">\${item.nama}</div>
                        <small class="text-muted">Rp \${item.harga.toLocaleString('id-ID')}</small>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <button class="btn btn-outline-secondary btn-sm p-0 px-2 btn-qty-minus" data-index="\${index}">-</button>
                        <span class="fw-bold px-1 small">\${item.qty}</span>
                        <button class="btn btn-outline-secondary btn-sm p-0 px-2 btn-qty-plus" data-index="\${index}">+</button>
                        <button class="btn btn-link text-danger btn-sm p-0 ms-1 btn-remove-item" data-index="\${index}">❌</button>
                    </div>
                </div>
            `;
            container.append(html);
        });

        $('#text-total-bayar').text('Rp ' + total.toLocaleString('id-ID'));
        $('#btn-lanjut-pos').removeClass('disabled');
    }

    // Event Qty & Hapus
    $(document).on('click', '.btn-qty-plus', function() {
        let idx = $(this).data('index');
        if (cart[idx].qty < cart[idx].maxStok) {
            cart[idx].qty++;
            saveAndRender();
        } else {
            alert('Stok barang maksimal tercapai!');
        }
    });

    $(document).on('click', '.btn-qty-minus', function() {
        let idx = $(this).data('index');
        if (cart[idx].qty > 1) {
            cart[idx].qty--;
        } else {
            cart.splice(idx, 1);
        }
        saveAndRender();
    });

    $(document).on('click', '.btn-remove-item', function() {
        let idx = $(this).data('index');
        cart.splice(idx, 1);
        saveAndRender();
    });

    $('#btn-clear-cart').on('click', function() {
        if (confirm('Kosongkan keranjang sementara?')) {
            cart = [];
            saveAndRender();
        }
    });
JS;
    $this->registerJs($script);
    ?>
<?php endif; ?>k