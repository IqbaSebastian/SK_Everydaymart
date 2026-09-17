<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "paket_outlet".
 *
 * @property int $id
 * @property int $id_paket
 * @property int $id_outlet
 *
 * @property Outlet $outlet
 * @property Paket $paket
 */
class PaketOutlet extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'paket_outlet';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_paket', 'id_outlet'], 'required'],
            [['id_paket', 'id_outlet'], 'integer'],
            [['id_paket', 'id_outlet'], 'unique', 'targetAttribute' => ['id_paket', 'id_outlet']],
            [['id_paket'], 'exist', 'skipOnError' => true, 'targetClass' => Paket::class, 'targetAttribute' => ['id_paket' => 'id']],
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
            'id_paket' => 'Id Paket',
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
     * Gets query for [[Paket]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPaket()
    {
        return $this->hasOne(Paket::class, ['id' => 'id_paket']);
    }

}
