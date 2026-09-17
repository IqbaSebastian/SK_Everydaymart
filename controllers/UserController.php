<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class UserController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest || !Yii::$app->user->identity->isSuperAdmin()) {
            Yii::$app->session->setFlash('error', 'Akses ditolak! Halaman ini khusus Administrator.');
            return $this->redirect(['/site/index'])->send();
        }
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find()->where(['!=', 'role_id', 1]),
            'pagination' => ['pageSize' => 10],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionCreate()
    {
        $model = new User();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                if (!empty($model->password)) {
                    $model->password = Yii::$app->security->generatePasswordHash($model->password);
                }

                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'User berhasil ditambahkan!');
                    return $this->redirect(['index']);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldPassword = $model->password;

        if ($this->request->isPost && $model->load($this->request->post())) {
            if (!empty($model->password)) {
                $model->password = Yii::$app->security->generatePasswordHash($model->password);
            } else {
                $model->password = $oldPassword;
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Data user berhasil diperbarui!');
                return $this->redirect(['index']);
            }
        }

        $model->password = '';

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ((int)$model->role_id === 1) {
            Yii::$app->session->setFlash('error', 'Akun Administrator Utama tidak dapat dihapus!');
            return $this->redirect(['index']);
        }

        $model->delete();
        Yii::$app->session->setFlash('success', 'User berhasil dihapus!');

        return $this->redirect(['index']);
    }
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman tidak ditemukan.');
    }
} 