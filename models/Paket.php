<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "paket".
 *
 * @property int $id
 * @property string $nama
 * @property string $masa_berlaku
 * @property int $harga
 *
 * @property DetailPaket[] $detailPakets
 * @property DetailTransaksiPaket[] $detailTransaksiPakets
 * @property Outlet[] $outlets
 * @property PaketOutlet[] $paketOutlets
 */
class Paket extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'paket';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nama', 'masa_berlaku', 'harga'], 'required'],
            [['masa_berlaku'], 'safe'],
            [['harga'], 'integer'],
            [['nama'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nama' => 'Nama',
            'masa_berlaku' => 'Masa Berlaku',
            'harga' => 'Harga',
        ];
    }

    /**
     * Gets query for [[DetailPakets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDetailPakets()
    {
        return $this->hasMany(DetailPaket::class, ['id_paket' => 'id']);
    }

    /**
     * Gets query for [[DetailTransaksiPakets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDetailTransaksiPakets()
    {
        return $this->hasMany(DetailTransaksiPaket::class, ['id_paket' => 'id']);
    }

    /**
     * Gets query for [[Outlets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOutlets()
    {
        return $this->hasMany(Outlet::class, ['id' => 'id_outlet'])->viaTable('paket_outlet', ['id_paket' => 'id']);
    }

    /**
     * Gets query for [[PaketOutlets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPaketOutlets()
    {
        return $this->hasMany(PaketOutlet::class, ['id_paket' => 'id']);
    }

}
