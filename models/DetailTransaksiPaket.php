<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "detail_transaksi_paket".
 *
 * @property int $id
 * @property int $id_transaksi
 * @property int $id_paket
 * @property int $jumlah
 * @property int $harga
 *
 * @property Paket $paket
 * @property Transaksi $transaksi
 */
class DetailTransaksiPaket extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'detail_transaksi_paket';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_transaksi', 'id_paket', 'jumlah', 'harga'], 'required'],
            [['id_transaksi', 'id_paket', 'jumlah', 'harga'], 'integer'],
            [['id_transaksi'], 'exist', 'skipOnError' => true, 'targetClass' => Transaksi::class, 'targetAttribute' => ['id_transaksi' => 'id']],
            [['id_paket'], 'exist', 'skipOnError' => true, 'targetClass' => Paket::class, 'targetAttribute' => ['id_paket' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_transaksi' => 'Id Transaksi',
            'id_paket' => 'Id Paket',
            'jumlah' => 'Jumlah',
            'harga' => 'Harga',
        ];
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

    /**
     * Gets query for [[Transaksi]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTransaksi()
    {
        return $this->hasOne(Transaksi::class, ['id' => 'id_transaksi']);
    }

}
