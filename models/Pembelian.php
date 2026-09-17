<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Pembelian extends ActiveRecord
{
    /**
     * Nama tabel di database (sesuaikan jika nama tabelmu berbeda, misal 'pembelian' atau 'transaksi_pembelian')
     */
    public static function tableName()
    {
        return 'pembelian'; 
    }

    public function rules()
    {
        return [
            [['no_nota', 'supplier', 'tanggal', 'outlet_id'], 'safe'],
        ];
    }

    public function getOutlet()
    {
        return $this->hasOne(Outlet::class, ['id' => 'outlet_id']);
    }

    public function getPembelianDetails()
    {
        return $this->hasMany(PembelianDetail::class, ['pembelian_id' => 'id']);
    }
}