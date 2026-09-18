<?php

namespace app\controllers;

use Yii;
use app\models\Barang;
use app\models\BarangSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use yii\db\IntegrityException;

/**
 * BarangController implements the CRUD actions for Barang model.
 */
class BarangController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            // Kasir dan Admin bisa melihat daftar & detail barang
                            'actions' => ['index', 'view'],
                            'allow' => true,
                            'roles' => ['@'], // User yang sudah login
                        ],
                        [
                            // Hanya Admin yang bisa Create, Update, dan Delete
                            'actions' => ['create', 'update', 'delete'],
                            'allow' => true,
                            'matchCallback' => function ($rule, $action) {
                                $user = Yii::$app->user->identity;
                                // Menyesuaikan dengan field role/level di database kamu
                                return $user && (
                                    (isset($user->role) && strtolower($user->role) === 'admin') ||
                                    (isset($user->level) && strtolower($user->level) === 'admin')
                                );
                            }
                        ],
                    ],
                ],
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
     * Lists all Barang models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new BarangSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Barang model.
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
     * Creates a new Barang model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Barang();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                
                $model->imageFile = UploadedFile::getInstance($model, 'imageFile');

                if ($model->imageFile) {
                    $fileName = time() . '_' . uniqid() . '.' . $model->imageFile->extension;
                    $uploadPath = Yii::getAlias('@webroot/uploads/');

                    // Buat direktori jika belum tersedia
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }

                    if ($model->imageFile->saveAs($uploadPath . $fileName)) {
                        $model->foto = $fileName;
                    }
                }

                if ($model->save()) {
                    if (class_exists('\app\models\StokBarang')) {
                        // Tentukan ID Outlet (jika user tidak memiliki outlet, default ke 1)
                        $user = Yii::$app->user->identity;
                        $idOutlet = $user?->id_outlet ?? 1;

                        $stokBarang = new \app\models\StokBarang();
                        if ($stokBarang->hasAttribute('id_barang') && $stokBarang->hasAttribute('jumlah_stok')) {
                            $stokBarang->id_barang = $model->id;
                            
                            if ($stokBarang->hasAttribute('id_outlet')) {
                                $stokBarang->id_outlet = $idOutlet;
                            }

                            $stokBarang->jumlah_stok = 0;
                            $stokBarang->save(false);
                        }
                    }

                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Barang model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $fotoLama = $model->foto; // Simpan nama foto lama

        if ($this->request->isPost && $model->load($this->request->post())) {
            
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');

            if ($model->imageFile) {
                $fileName = time() . '_' . uniqid() . '.' . $model->imageFile->extension;
                $uploadPath = Yii::getAlias('@webroot/uploads/');

                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                if ($model->imageFile->saveAs($uploadPath . $fileName)) {
                    $model->foto = $fileName;
                    
                    // Hapus gambar lama dari server jika ada
                    if ($fotoLama && file_exists($uploadPath . $fotoLama)) {
                        @unlink($uploadPath . $fotoLama);
                    }
                }
            } else {
                // Pertahankan gambar lama jika tidak mengupload gambar baru
                $model->foto = $fotoLama;
            }

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Barang model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // 1. Cek apakah barang ini ada di riwayat transaksi
        $sudahDibeli = false;

        if (class_exists('\app\models\DetailTransaksi')) {
            $sudahDibeli = \app\models\DetailTransaksi::find()->where(['id_barang' => $id])->exists();
        } elseif (class_exists('\app\models\TransaksiDetail')) {
            $sudahDibeli = \app\models\TransaksiDetail::find()->where(['id_barang' => $id])->exists();
        } elseif (class_exists('\app\models\PenjualanDetail')) {
            $sudahDibeli = \app\models\PenjualanDetail::find()->where(['id_barang' => $id])->exists();
        } elseif (class_exists('\app\models\DetailPenjualan')) {
            $sudahDibeli = \app\models\DetailPenjualan::find()->where(['id_barang' => $id])->exists();
        }

        // 2. Jika barang pernah dibeli, tolak penghapusan
        if ($sudahDibeli) {
            Yii::$app->session->setFlash('error', 'Barang tidak bisa dihapus karena sudah memiliki riwayat transaksi/penjualan!');
            return $this->redirect(['index']);
        }

        // 3. Jika belum pernah dibeli, jalankan proses hapus
        try {
            if ($model->delete()) {
                // Hapus stok barang terkait
                if (class_exists('\app\models\StokBarang')) {
                    \app\models\StokBarang::deleteAll(['id_barang' => $id]);
                }

                // Hapus foto fisik dari folder uploads
                if ($model->foto) {
                    $filePath = Yii::getAlias('@webroot/uploads/') . $model->foto;
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                }
                Yii::$app->session->setFlash('success', 'Barang berhasil dihapus.');
            }
        } catch (IntegrityException $e) {
            Yii::$app->session->setFlash('error', 'Barang tidak bisa dihapus karena masih terikat dengan data lain!');
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'Gagal menghapus barang: ' . $e->getMessage());
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Barang model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Barang the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Barang::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}