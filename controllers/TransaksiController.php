<?php 

namespace app\controllers; 

use app\models\Transaksi; 
use app\models\TransaksiSearch; 
use app\models\DetailTransaksi; 
use app\models\DetailTransaksiBatal; 
use app\models\Barang; 
use app\models\StokBarang; 
use yii\web\Controller; 
use yii\web\NotFoundHttpException; 
use yii\filters\VerbFilter; 
use yii\data\ActiveDataProvider; 
use yii\helpers\ArrayHelper;
use Yii; 

/** 
 * TransaksiController implements the CRUD actions for Transaksi model. 
 */ 
class TransaksiController extends Controller 
{ 
    /** 
     * @inheritDoc 
     */ 
    public function behaviors() 
    { 
        return array_merge( 
            parent::behaviors(), 
            [ 
                'verbs' => [ 
                    'class' => VerbFilter::className(), 
                    'actions' => [ 
                        'delete' => ['POST'], 
                    ], 
                ], 
            ] 
        ); 
    } 

    /** 
     * Lists all Transaksi models. 
     * @return string 
     */ 
    public function actionIndex()
    {
        $searchModel = new TransaksiSearch();
        $user = Yii::$app->user->identity;

        $queryParams = $this->request->queryParams;

        if ($user && !$user->isSuperAdmin()) {
            $queryParams['TransaksiSearch']['id_outlet'] = $user->id_outlet;
        }

        $dataProvider = $searchModel->search($queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /** 
     * Displays a single Transaksi model. 
     * @param int $id ID 
     * @return string 
     * @throws NotFoundHttpException if the model cannot be found 
     */ 
    public function actionView($id) 
    { 
        return $this->render('view', [ 
            'model' => $this->findModel($id), 
        ]); 
    } 

    /** 
     * Creates a new Transaksi model. 
     * @return string|\yii\web\Response 
     */ 
    public function actionCreate()
    {
        $model = new Transaksi();
        $user = Yii::$app->user->identity;

        if ($this->request->isPost) {
            $postData = $this->request->post();

            if ($model->load($postData)) {
                $transaction = Yii::$app->db->beginTransaction();

                try {
                    // 1. Kunci ID Outlet
                    if (!$user->isSuperAdmin()) {
                        $model->id_outlet = $user->id_outlet;
                    }

                    // 2. Auto Generate No Nota Berdasarkan Outlet
                    if (empty($model->no_nota)) {
                        if (method_exists($model, 'generateNoNota')) {
                            $model->no_nota = $model->generateNoNota($model->id_outlet);
                        } else {
                            $outlet = \app\models\Outlet::findOne($model->id_outlet);
                            $prefixOutlet = 'TRX';
                            
                            if ($outlet) {
                                // Gunakan kolom 'kode_nota' dari tabel outlet
                                $prefixOutlet = !empty($outlet->kode_nota) 
                                    ? strtoupper($outlet->kode_nota) 
                                    : strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $outlet->nama), 0, 3));
                            }
                            
                            $model->no_nota = $prefixOutlet . '-' . date('YmdHis') . rand(10, 99);
                        }
                    }

                    // 3. Kunci ID Kasir / User
                    if ($model->hasProperty('id_kasir')) {
                        $model->id_kasir = $user->id;
                    } elseif ($model->hasProperty('user_id')) {
                        $model->user_id = $user->id;
                    } elseif ($model->hasProperty('id_user')) {
                        $model->id_user = $user->id;
                    }

                    // 4. Kunci Tanggal Transaksi
                    if ($model->hasProperty('tgl_transaksi')) {
                        $model->tgl_transaksi = date('Y-m-d H:i:s');
                    } elseif ($model->hasProperty('tanggal')) {
                        $model->tanggal = date('Y-m-d H:i:s');
                    }

                    // 5. Set Status Bawaan
                    if ($model->hasProperty('status')) {
                        $model->status = 'SUCCESS';
                    }

                    // 6. Simpan Transaksi Induk
                    if (!$model->save()) {
                        $errors = implode(', ', ArrayHelper::getColumn($model->getErrors(), 0));
                        Yii::$app->session->setFlash('error', 'Gagal simpan header transaksi: ' . $errors);
                        $transaction->rollBack();
                        return $this->render('create', ['model' => $model]);
                    }

                    // 7. Tangkap Detail Keranjang Belanja
                    $details = $postData['DetailTransaksi'] ?? [];
                    if (empty($details)) {
                        Yii::$app->session->setFlash('error', 'Keranjang belanja tidak boleh kosong!');
                        $transaction->rollBack();
                        return $this->render('create', ['model' => $model]);
                    }

                    // 8. TAHAP 1: VALIDASI SEMUA STOK BARANG DI KERANJANG TERLEBIH DAHULU
                    $stokErrors = [];
                    foreach ($details as $item) {
                        $qtyBeli = (int)$item['jumlah'];

                        $stok = StokBarang::findOne([
                            'id_barang' => $item['id_barang'],
                            'id_outlet' => $model->id_outlet,
                        ]);

                        $stokTersedia = $stok ? (int)$stok->jumlah_stok : 0;

                        if ($qtyBeli > $stokTersedia) {
                            $barang = Barang::findOne($item['id_barang']);
                            $namaBarang = $barang ? ($barang->nama ?? $barang->nama_barang ?? 'Barang') : 'Barang';
                            
                            $stokErrors[] = "<b>{$namaBarang}</b> (Tersedia: {$stokTersedia} Pcs, Diminta: {$qtyBeli} Pcs)";
                        }
                    }

                    // JIKA ADA DITEMUKAN STOK KURANG, DIBATALKAN DAN DIKUMPULKAN PESAN ERRORNYA
                    if (!empty($stokErrors)) {
                        $pesanError = "Stok barang berikut tidak mencukupi:<br>• " . implode("<br>• ", $stokErrors);
                        throw new \Exception($pesanError);
                    }

                    // 9. TAHAP 2: JIKA SEMUA STOK TERSEDIA, SIMPAN DETAIL TRANSAKSI & POTONG STOK
                    foreach ($details as $item) {
                        $qtyBeli = (int)$item['jumlah'];

                        $detail = new DetailTransaksi();
                        $detail->id_transaksi = $model->id;
                        $detail->id_barang = $item['id_barang'];
                        $detail->jumlah = $qtyBeli;
                        
                        if ($detail->hasProperty('total_harga_item')) {
                            $detail->total_harga_item = $item['total_harga_item'];
                        } elseif ($detail->hasProperty('subtotal')) {
                            $detail->subtotal = $item['total_harga_item'];
                        }

                        if (!$detail->save()) {
                            $errors = implode(', ', ArrayHelper::getColumn($detail->getErrors(), 0));
                            Yii::$app->session->setFlash('error', 'Gagal simpan barang keranjang: ' . $errors);
                            $transaction->rollBack();
                            return $this->render('create', ['model' => $model]);
                        }

                        // Potong Stok Barang
                        $stok = StokBarang::findOne([
                            'id_barang' => $item['id_barang'],
                            'id_outlet' => $model->id_outlet,
                        ]);

                        if ($stok) {
                            $stok->jumlah_stok -= $qtyBeli;
                            $stok->save(false);
                        }
                    }

                    // Commit transaksi
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'Transaksi berhasil disimpan & stok dipotong!');
                    return $this->redirect(['view', 'id' => $model->id]);

                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', $e->getMessage());
                    return $this->render('create', ['model' => $model]);
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /** 
     * Updates an existing Transaksi model. 
     * @param int $id ID 
     * @return string|\yii\web\Response 
     * @throws NotFoundHttpException if the model cannot be found 
     */ 
    public function actionUpdate($id) 
    { 
        $model = $this->findModel($id); 

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) { 
            return $this->redirect(['view', 'id' => $model->id]); 
        } 

        return $this->render('update', [ 
            'model' => $model, 
        ]); 
    } 

    /** 
     * Deletes an existing Transaksi model. 
     * @param int $id ID 
     * @return \yii\web\Response 
     * @throws NotFoundHttpException if the model cannot be found 
     */ 
    public function actionDelete($id) 
    { 
        $this->findModel($id)->delete(); 

        return $this->redirect(['index']); 
    } 

    public function actionLaporan()
    {
        $user = Yii::$app->user->identity;

        if ($user && ($user->isKasir() || $user->isGudang())) {
            Yii::$app->session->setFlash('warning', 'Halaman tidak ditemukan.');
            return $this->redirect(['/transaksi/create']);
        }

        $query = Transaksi::find()
            ->where(['transaksi.status' => 'SUCCESS']);

        if ($user && $user->isManager()) {
            $query->andWhere(['transaksi.id_outlet' => $user->id_outlet]);
            $idOutlet = $user->id_outlet;
        } else {
            $idOutlet = Yii::$app->request->get('id_outlet');
            if ($idOutlet) {
                $query->andWhere(['transaksi.id_outlet' => $idOutlet]);
            }
        }

        $totalOmset = (clone $query)->sum('total_harga') ?? 0;
        $totalTransaksi = (clone $query)->count();

        $trendQuery = (clone $query)
            ->select([
                'bulan_key'   => "DATE_FORMAT(transaksi.tgl_transaksi, '%Y-%m')",
                'bulan_label' => "DATE_FORMAT(transaksi.tgl_transaksi, '%M %Y')",
                'total'       => 'SUM(transaksi.total_harga)'
            ])
            ->groupBy([
                "DATE_FORMAT(transaksi.tgl_transaksi, '%Y-%m')",
                "DATE_FORMAT(transaksi.tgl_transaksi, '%M %Y')"
            ])
            ->orderBy(["DATE_FORMAT(transaksi.tgl_transaksi, '%Y-%m')" => SORT_ASC])
            ->asArray()
            ->all();

        $bulanLabels = [];
        $bulanOmset = [];
        foreach ($trendQuery as $t) {
            $bulanLabels[] = $t['bulan_label'];
            $bulanOmset[] = (float)$t['total'];
        }

        $outletQuery = (clone $query)
            ->select([
                'nama_outlet' => 'outlet.nama',
                'total'       => 'SUM(transaksi.total_harga)'
            ])
            ->joinWith('outlet')
            ->groupBy(['transaksi.id_outlet', 'outlet.nama'])
            ->orderBy(['total' => SORT_DESC])
            ->asArray()
            ->all();

        $outletLabels = [];
        $outletOmset = [];
        foreach ($outletQuery as $o) {
            $outletLabels[] = $o['nama_outlet'] ?? 'Tanpa Outlet';
            $outletOmset[] = (float)$o['total'];
        }

        $query->orderBy(['transaksi.tgl_transaksi' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('laporan', [
            'dataProvider'   => $dataProvider,
            'totalOmset'     => $totalOmset,
            'totalTransaksi' => $totalTransaksi,
            'idOutlet'       => $idOutlet,
            'bulanLabels'    => $bulanLabels,
            'bulanOmset'     => $bulanOmset,
            'outletLabels'   => $outletLabels,
            'outletOmset'    => $outletOmset,
        ]);
    }

    public function actionRequestCancel($id)
    {
        $model = $this->findModel($id);
            
        if ($this->request->isPost) {
            $alasan = Yii::$app->request->post('alasan_batal');
            $model->status = 'PENDING_CANCEL';
            $model->alasan_batal = $alasan;
                
            if ($model->save(false)) {
                Yii::$app->session->setFlash('warning', 'Permintaan pembatalan nota berhasil diajukan ke Manager.');
            }
        }
        return $this->redirect(['index']);
    }

    /**
     * Approve Pembatalan oleh Manager (Membutuhkan Password Manager + Pilihan Opsi Batal)
     */
    public function actionApproveCancel($id)
    {
        $user = Yii::$app->user->identity;

        if (!$user || (!$user->isManager() && !$user->isSuperAdmin())) {
            Yii::$app->session->setFlash('error', 'Anda tidak memiliki hak akses otorisasi.');
            return $this->redirect(['index']);
        }

        $model = $this->findModel($id);

        if ($this->request->isPost) {
            $passwordInput = Yii::$app->request->post('password_manager');
            $idOpsi = Yii::$app->request->post('id_opsi');

            if (!$user->validatePassword($passwordInput)) {
                Yii::$app->session->setFlash('error', 'Password Otorisasi Salah!');
                return $this->redirect(['index']);
            }

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->status = 'CANCELLED';
                if ($model->save(false)) {
                    $details = DetailTransaksi::findAll(['id_transaksi' => $model->id]);
                    
                    foreach ($details as $detail) {
                        
                        // 1. Catat ke tabel detail_transaksi_batal
                        $batal = new DetailTransaksiBatal();
                        $batal->id_detail_transaksi = $detail->id;
                        $batal->id_barang = $detail->id_barang;
                        $batal->jumlah_awal = $detail->jumlah;
                        $batal->jumlah_akhir = 0; // Karena pembatalan seluruh nota
                        $batal->id_opsi = $idOpsi;
                        
                        if (!$batal->save(false)) {
                            throw new \Exception('Gagal mencatat detail pembatalan barang.');
                        }

                        // 2. Kembalikan / Restore Stok Barang ke Outlet
                        $stok = StokBarang::findOne([
                            'id_barang' => $detail->id_barang,
                            'id_outlet' => $model->id_outlet,
                        ]);

                        if ($stok) {
                            $stok->jumlah_stok += $detail->jumlah; 
                            $stok->save(false);
                        }
                    }

                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'Transaksi berhasil dibatalkan, histori dicatat & stok dikembalikan.');
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Gagal membatalkan: ' . $e->getMessage());
            }
        }

        return $this->redirect(['index']);
    }

    /** 
     * Finds the Transaksi model based on its primary key value. 
     * @param int $id ID 
     * @return Transaksi the loaded model 
     * @throws NotFoundHttpException if the model cannot be found 
     */ 
    protected function findModel($id) { 
        if (($model = Transaksi::findOne(['id' => $id])) !== null) { 
            return $model; 
        } 

        throw new NotFoundHttpException('The requested page does not exist.'); 
    } 
}