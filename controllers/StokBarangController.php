<?php

namespace app\controllers;

use app\models\StokBarang;
use app\models\StokBarangSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

class StokBarangController extends Controller
{
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
     * Menampilkan daftar stok barang (Terisolasi per Outlet)
     */
    public function actionIndex()
    {
        $searchModel = new StokBarangSearch();
        $user = Yii::$app->user->identity;

        $queryParams = $this->request->queryParams;

        if ($user && !$user->isSuperAdmin()) {
            $queryParams['StokBarangSearch']['id_outlet'] = $user->id_outlet;
        }

        $dataProvider = $searchModel->search($queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new StokBarang();
        $user = Yii::$app->user->identity;

        if ($this->request->isPost) {
            $post = $this->request->post('StokBarang');
            $idBarang = $post['id_barang'] ?? null;
            $jumlahTambahan = (int)($post['jumlah_stok'] ?? 0);

            $idOutlet = (!$user->isSuperAdmin()) ? $user->id_outlet : ($post['id_outlet'] ?? null);

            if ($idBarang && $idOutlet && $jumlahTambahan > 0) {
                $stok = StokBarang::findOne([
                    'id_barang' => $idBarang,
                    'id_outlet' => $idOutlet,
                ]);

                if (!$stok) {
                    $stok = new StokBarang();
                    $stok->id_barang = $idBarang;
                    $stok->id_outlet = $idOutlet;
                    $stok->jumlah_stok = 0;
                }

                $stok->jumlah_stok += $jumlahTambahan;

                if ($stok->save()) {
                    Yii::$app->session->setFlash('success', 'Stok barang berhasil ditambahkan!');
                    return $this->redirect(['index']);
                }
            } else {
                Yii::$app->session->setFlash('error', 'Jumlah stok harus lebih dari 0 dan data barang harus dipilih.');
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = StokBarang::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman tidak ditemukan.');
    }
}