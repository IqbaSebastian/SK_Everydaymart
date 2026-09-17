<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "detail_paket".
 *
 * @property int $id
 * @property int $id_paket
 * @property int $barang
 * @property int $jumlah
 *
 * @property Barang $barang0
 * @property Paket $paket
 */
class DetailPaket extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'detail_paket';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_paket', 'barang', 'jumlah'], 'required'],
            [['id_paket', 'barang', 'jumlah'], 'integer'],
            [['id_paket'], 'exist', 'skipOnError' => true, 'targetClass' => Paket::class, 'targetAttribute' => ['id_paket' => 'id']],
            [['barang'], 'exist', 'skipOnError' => true, 'targetClass' => Barang::class, 'targetAttribute' => ['barang' => 'id']],
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
            'barang' => 'Barang',
            'jumlah' => 'Jumlah',
        ];
    }

    /**
     * Gets query for [[Barang0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBarang0()
    {
        return $this->hasOne(Barang::class, ['id' => 'barang']);
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
