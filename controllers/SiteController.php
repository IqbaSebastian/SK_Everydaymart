<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\models\ContactForm;
use app\models\LoginForm;
use app\models\Barang;
use app\models\Outlet;
use app\models\StokBarang;
use yii\captcha\CaptchaAction;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\base\Security;
use yii\mail\MailerInterface;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;

class SiteController extends Controller
{
    /**
     * Halaman Utama / Katalog Produk per Outlet
     */
    public function actionIndex($id_outlet = null)
    {
        $outlets = Outlet::find()->all();
        
        // Tentukan outlet yang dipilih (default ke outlet pertama jika tidak ada)
        if (!$id_outlet && !empty($outlets)) {
            $id_outlet = $outlets[0]->id;
        }

        $selectedOutlet = Outlet::findOne($id_outlet);

        // Ambil semua barang dan relasi stoknya pada outlet yang dipilih
        $stokBarang = StokBarang::find()
            ->where(['id_outlet' => $id_outlet])
            ->indexBy('id_barang')
            ->all();

        $barangs = Barang::find()->all();

        return $this->render('index', [
            'outlets' => $outlets,
            'selectedOutlet' => $selectedOutlet,
            'barangs' => $barangs,
            'stokBarang' => $stokBarang,
            'currentOutletId' => $id_outlet,
        ]);
    }

    /**
     * AJAX Action untuk mengambil detail produk
     */
    public function actionDetailBarang($id_barang, $id_outlet)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $barang = Barang::findOne($id_barang);
        if (!$barang) {
            return ['success' => false, 'message' => 'Barang tidak ditemukan'];
        }

        $stok = StokBarang::findOne(['id_barang' => $id_barang, 'id_outlet' => $id_outlet]);
        $outlet = Outlet::findOne($id_outlet);

        $ppnVal = $barang->ppn_id ?? $barang->id_ppn ?? $barang->ppn ?? 0;
        $isPpn = ($ppnVal == 2);

        return [
            'success' => true,
            'nama' => $barang->nama,
            'harga' => number_format($barang->harga, 0, ',', '.'),
            'harga_raw' => $barang->harga,
            'stok' => $stok ? $stok->jumlah_stok : 0,
            'outlet' => $outlet ? $outlet->nama : '-',
            'status_ppn' => $isPpn ? 'Kena PPN (11%)' : 'Non-PPN',
            'deskripsi' => $barang->deskripsi ?? 'Tidak ada deskripsi produk.'
        ];
    }

    public function __construct(
        $id,
        $module,
        private readonly MailerInterface $mailer,
        private readonly Security $security,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
            'captcha' => [
                'class' => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'transparent' => true,
            ],
        ];
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact(): Response|string
    {
        $model = new ContactForm();

        $contact = $model->load($this->request->post()) && $model->contact(
            $this->mailer,
            Yii::$app->params['adminEmail'],
            Yii::$app->params['senderEmail'],
            Yii::$app->params['senderName'],
        );

        if ($contact) {
            Yii::$app->session->setFlash(
                'success',
                'Thank you for contacting us. We will respond to you as soon as possible.',
            );

            return $this->refresh();
        }

        return $this->render('contact', ['model' => $model]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout(): string
    {
        return $this->render('about');
    }
}