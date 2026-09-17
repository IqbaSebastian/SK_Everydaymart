<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "detail_promo".
 *
 * @property int $id
 * @property int $id_promo
 * @property int $id_outlet
 *
 * @property Outlet $outlet
 * @property Promo $promo
 */
class DetailPromo extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'detail_promo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_promo', 'id_outlet'], 'required'],
            [['id_promo', 'id_outlet'], 'integer'],
            [['id_promo'], 'exist', 'skipOnError' => true, 'targetClass' => Promo::class, 'targetAttribute' => ['id_promo' => 'id']],
            [['id_outlet'], 'exist', 'skipOnError' => true, 'targetClass' => Outlet::class, 'targetAttribute' => ['id_outlet' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_promo' => 'Id Promo',
            'id_outlet' => 'Id Outlet',
        ];
    }

    /**
     * Gets query for [[Outlet]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOutlet()
    {
        return $this->hasOne(Outlet::class, ['id' => 'id_outlet']);
    }

    /**
     * Gets query for [[Promo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPromo()
    {
        return $this->hasOne(Promo::class, ['id' => 'id_promo']);
    }

}
