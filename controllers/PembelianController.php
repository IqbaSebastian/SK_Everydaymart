<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Pembelian;
use app\models\PembelianDetail;
use app\models\Barang;
use app\models\StokBarang;
use app\models\Outlet;

class PembelianController extends Controller
{
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function actionIndex()
    {
        $pembelianList = Pembelian::find()->orderBy(['id' => SORT_DESC])->all();

        return $this->render('index', [
            'pembelianList' => $pembelianList,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $db = Yii::$app->db;
        $user = Yii::$app->user->identity;

        // 1. Ambil Outlet ID berdasarkan user yang login
        $outletId = $user->id_outlet 
            ?? $user->outlet_id 
            ?? Yii::$app->session->get('id_outlet') 
            ?? Yii::$app->session->get('outlet_id');

        $outlet = Outlet::findOne($outletId);
        if (!$outlet) {
            $outlet = Outlet::find()->one();
            $outletId = $outlet ? $outlet->id : 1;
        }

        if ($request->isPost) {
            $transaction = $db->beginTransaction();

            try {
                $postData = $request->post();

                // 2. Generate Nomor Nota Otomatis berdasarkan Outlet User Login
                $nomorBerjalan = (($outlet->nomor_berjalan ?? 0) + 1);
                $kodeNota = !empty($outlet->kode_nota) ? $outlet->kode_nota : 'NOTA';
                $noNotaOtomatis = $kodeNota . '-' . str_pad($nomorBerjalan, 5, '0', STR_PAD_LEFT);

                // 3. Simpan Header Pembelian
                $pembelian = new Pembelian();
                $pembelian->no_nota = $noNotaOtomatis;
                $pembelian->supplier = !empty($postData['supplier']) ? $postData['supplier'] : 'Supplier General';
                $pembelian->tanggal = !empty($postData['tanggal']) ? $postData['tanggal'] : date('Y-m-d');
                $pembelian->outlet_id = $outletId;
                $pembelian->user_id = $user->id ?? 1;

                if (!$pembelian->save(false)) {
                    throw new \Exception('Gagal menyimpan Nota Pembelian.');
                }

                // 4. Update Nomor Berjalan Outlet
                if ($outlet) {
                    $outlet->nomor_berjalan = $nomorBerjalan;
                    $outlet->save(false);
                }

                // 5. Ambil array items
                $items = $postData['items'] ?? [];
                if (empty($items)) {
                    throw new \Exception('Minimal harus memilih 1 barang.');
                }

                foreach ($items as $item) {
                    $barangId = isset($item['barang_id']) ? (int)$item['barang_id'] : 0;
                    $jumlahMasuk = isset($item['jumlah']) ? (int)$item['jumlah'] : 0;
                    $hargaBeli = isset($item['harga_beli']) ? (float)$item['harga_beli'] : 0;

                    if ($barangId <= 0 || $jumlahMasuk <= 0) {
                        continue;
                    }

                    // A. Simpan Detail Transaksi Pembelian
                    $detail = new PembelianDetail();
                    $detail->pembelian_id = $pembelian->id;
                    $detail->barang_id = $barangId;
                    $detail->jumlah = $jumlahMasuk;
                    $detail->harga_beli = $hargaBeli;
                    $detail->save(false);

                    // B. UPDATE STOK PADA TABEL stok_barang BERDASARKAN id_barang DAN id_outlet
                    $stokBarang = StokBarang::findOne([
                        'id_barang' => $barangId,
                        'id_outlet' => $outletId,
                    ]);

                    if ($stokBarang) {
                        // Jika stok di outlet tersebut sudah ada, tambahkan
                        $stokBarang->jumlah_stok = (int)$stokBarang->jumlah_stok + $jumlahMasuk;
                        $stokBarang->save(false);
                    } else {
                        // Jika belum ada record stok di outlet tersebut, buat baru
                        $stokBarang = new StokBarang();
                        $stokBarang->id_barang = $barangId;
                        $stokBarang->id_outlet = $outletId;
                        $stokBarang->jumlah_stok = $jumlahMasuk;
                        $stokBarang->save(false);
                    }
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', "Berhasil! Penerimaan barang tersimpan dengan No Nota: {$noNotaOtomatis}");

                return $this->redirect(['index']);

            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', "Gagal Menyimpan: " . $e->getMessage());
            }
        }

        // Preview nomor nota untuk form awal
        $previewNomor = (($outlet->nomor_berjalan ?? 0) + 1);
        $previewNoNota = (!empty($outlet->kode_nota) ? $outlet->kode_nota : 'NOTA') . '-' . str_pad($previewNomor, 5, '0', STR_PAD_LEFT);

        return $this->render('create', [
            'barangList' => Barang::find()->all(),
            'outlet' => $outlet,
            'previewNoNota' => $previewNoNota,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Pembelian::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman tidak ditemukan.');
    }
}