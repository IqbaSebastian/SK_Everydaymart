<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "detail_transaksi".
 *
 * @property int $id
 * @property int $id_transaksi
 * @property int $id_barang
 * @property int $jumlah
 * @property int $total_harga_item
 * @property int|null $total_ppn
 * @property int|null $total_charge
 *
 * @property Barang $barang
 * @property DetailTransaksiBatal[] $detailTransaksiBatals
 * @property Transaksi $transaksi
 * @property TransaksiBatal[] $transaksiBatals
 */
class DetailTransaksi extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'detail_transaksi';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['total_charge'], 'default', 'value' => 0],
            [['id_transaksi', 'id_barang', 'jumlah', 'total_harga_item'], 'required'],
            [['id_transaksi', 'id_barang', 'jumlah', 'total_harga_item', 'total_ppn', 'total_charge'], 'integer'],
            [['id_transaksi'], 'exist', 'skipOnError' => true, 'targetClass' => Transaksi::class, 'targetAttribute' => ['id_transaksi' => 'id']],
            [['id_barang'], 'exist', 'skipOnError' => true, 'targetClass' => Barang::class, 'targetAttribute' => ['id_barang' => 'id']],
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
            'id_barang' => 'Id Barang',
            'jumlah' => 'Jumlah',
            'total_harga_item' => 'Total Harga Item',
            'total_ppn' => 'Total Ppn',
            'total_charge' => 'Total Charge',
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
     * Gets query for [[DetailTransaksiBatals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDetailTransaksiBatals()
    {
        return $this->hasMany(DetailTransaksiBatal::class, ['id_detail_transaksi' => 'id']);
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

    /**
     * Gets query for [[TransaksiBatals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTransaksiBatals()
    {
        return $this->hasMany(TransaksiBatal::class, ['id_detail_transaksi' => 'id']);
    }

}
