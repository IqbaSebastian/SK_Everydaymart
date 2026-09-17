<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "diskon".
 *
 * @property int $id
 * @property int $id_barang
 * @property string $jenis
 * @property int $value
 * @property string $masa_berlaku
 * @property int $id_outlet
 *
 * @property Barang $barang
 * @property Outlet $outlet
 */
class Diskon extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'diskon';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_barang', 'jenis', 'value', 'masa_berlaku', 'id_outlet'], 'required'],
            [['id_barang', 'value', 'id_outlet'], 'integer'],
            [['masa_berlaku'], 'safe'],
            [['jenis'], 'string', 'max' => 20],
            [['id_barang'], 'exist', 'skipOnError' => true, 'targetClass' => Barang::class, 'targetAttribute' => ['id_barang' => 'id']],
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
            'id_barang' => 'Id Barang',
            'jenis' => 'Jenis',
            'value' => 'Value',
            'masa_berlaku' => 'Masa Berlaku',
            'id_outlet' => 'Id Outlet',
        ];
    }

    /**
     * Gets query for [[Barang]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBarang()
    {
        return $this->hasOne(Barang::class, ['id' => 'id_barang']);
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

}
