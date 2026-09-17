<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Outlet extends ActiveRecord
{
    public static function tableName()
    {
        return 'outlet';
    }

    public function rules()
    {
        return [
            [['nama', 'alamat'], 'required'],
            [['alamat'], 'string'],
            [['ppn', 'charge'], 'number'],
            [['has_room', 'nomor_berjalan'], 'integer'],
            [['ppn'], 'default', 'value' => 11],
            [['charge'], 'default', 'value' => 0],
            [['nama', 'kode_nota', 'no_telp'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID Outlet',
            'nama' => 'Nama Outlet',
            'kode_nota' => 'Kode Prefix Nota',
            'alamat' => 'Alamat Lengkap',
            'no_telp' => 'No. Telepon',
            'ppn' => 'PPN (%)',
            'charge' => 'Charge(%)',
            'has_room' => 'Fasilitas Room',
            'nomor_berjalan' => 'Nomor Berjalan Nota',
        ];
    }
}