<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "detail_belanja_barang".
 *
 * @property int $id
 * @property int $id_belanja_barang
 * @property int $id_barang
 * @property int $jumlah
 * @property int $harga
 * @property string|null $tgl_kadaluarsa
 * @property int|null $id_master_pembayaran
 *
 * @property Barang $barang
 * @property BelanjaBarang $belanjaBarang
 * @property MasterPembayaran $masterPembayaran
 */
class DetailBelanjaBarang extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'detail_belanja_barang';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tgl_kadaluarsa', 'id_master_pembayaran'], 'default', 'value' => null],
            [['id_belanja_barang', 'id_barang', 'jumlah', 'harga'], 'required'],
            [['id_belanja_barang', 'id_barang', 'jumlah', 'harga', 'id_master_pembayaran'], 'integer'],
            [['tgl_kadaluarsa'], 'safe'],
            [['id_belanja_barang'], 'exist', 'skipOnError' => true, 'targetClass' => BelanjaBarang::class, 'targetAttribute' => ['id_belanja_barang' => 'id']],
            [['id_barang'], 'exist', 'skipOnError' => true, 'targetClass' => Barang::class, 'targetAttribute' => ['id_barang' => 'id']],
            [['id_master_pembayaran'], 'exist', 'skipOnError' => true, 'targetClass' => MasterPembayaran::class, 'targetAttribute' => ['id_master_pembayaran' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_belanja_barang' => 'Id Belanja Barang',
            'id_barang' => 'Id Barang',
            'jumlah' => 'Jumlah',
            'harga' => 'Harga',
            'tgl_kadaluarsa' => 'Tgl Kadaluarsa',
            'id_master_pembayaran' => 'Id Master Pembayaran',
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
     * Gets query for [[BelanjaBarang]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBelanjaBarang()
    {
        return $this->hasOne(BelanjaBarang::class, ['id' => 'id_belanja_barang']);
    }

    /**
     * Gets query for [[MasterPembayaran]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMasterPembayaran()
    {
        return $this->hasOne(MasterPembayaran::class, ['id' => 'id_master_pembayaran']);
    }

}
