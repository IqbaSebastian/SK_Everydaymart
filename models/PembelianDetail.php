<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class PembelianDetail extends ActiveRecord
{
    /**
     * Nama tabel di database (sesuaikan jika nama tabelmu berbeda, 
     * misal: 'pembelian_detail' atau 'detail_pembelian')
     */
    public static function tableName()
    {
        return 'pembelian_detail'; 
    }

    public function rules()
    {
        return [
            [['pembelian_id', 'barang_id', 'jumlah', 'harga_beli'], 'required'],
            [['pembelian_id', 'barang_id', 'jumlah'], 'integer'],
            [['harga_beli'], 'number'],
            [['expired_date'], 'safe'],
        ];
    }

    /**
     * Relasi ke model Pembelian
     */
    public function getPembelian()
    {
        return $this->hasOne(Pembelian::class, ['id' => 'pembelian_id']);
    }

    /**
     * Relasi ke model Barang
     */
    public function getBarang()
    {
        return $this->hasOne(Barang::class, ['id' => 'barang_id']);
    }
}