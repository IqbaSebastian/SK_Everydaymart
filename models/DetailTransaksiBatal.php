<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "detail_transaksi_batal".
 *
 * @property int $id
 * @property int $id_detail_transaksi
 * @property int $id_barang
 * @property int $jumlah_awal
 * @property int $jumlah_akhir
 * @property int|null $id_opsi
 *
 * @property Barang $barang
 * @property DetailTransaksi $detailTransaksi
 * @property Opsi $opsi
 */
class DetailTransaksiBatal extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'detail_transaksi_batal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_opsi'], 'default', 'value' => null],
            [['id_detail_transaksi', 'id_barang', 'jumlah_awal', 'jumlah_akhir'], 'required'],
            [['id_detail_transaksi', 'id_barang', 'jumlah_awal', 'jumlah_akhir', 'id_opsi'], 'integer'],
            [['id_detail_transaksi'], 'exist', 'skipOnError' => true, 'targetClass' => DetailTransaksi::class, 'targetAttribute' => ['id_detail_transaksi' => 'id']],
            [['id_barang'], 'exist', 'skipOnError' => true, 'targetClass' => Barang::class, 'targetAttribute' => ['id_barang' => 'id']],
            [['id_opsi'], 'exist', 'skipOnError' => true, 'targetClass' => Opsi::class, 'targetAttribute' => ['id_opsi' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_detail_transaksi' => 'Id Detail Transaksi',
            'id_barang' => 'Id Barang',
            'jumlah_awal' => 'Jumlah Awal',
            'jumlah_akhir' => 'Jumlah Akhir',
            'id_opsi' => 'Id Opsi',
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
     * Gets query for [[DetailTransaksi]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDetailTransaksi()
    {
        return $this->hasOne(DetailTransaksi::class, ['id' => 'id_detail_transaksi']);
    }

    /**
     * Gets query for [[Opsi]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOpsi()
    {
        return $this->hasOne(Opsi::class, ['id' => 'id_opsi']);
    }

}
