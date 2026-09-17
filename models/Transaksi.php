<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "transaksi".
 *
 * @property int $id
 * @property int|null $id_room
 * @property string $no_nota
 * @property string|null $customer
 * @property string $tgl_transaksi
 * @property int $total_harga
 * @property int $id_outlet
 * @property string|null $waiter
 *
 * @property DetailTransaksiPaket[] $detailTransaksiPakets
 * @property DetailTransaksi[] $detailTransaksis
 * @property Outlet $outlet
 * @property Room $room
 */
class Transaksi extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transaksi';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_room', 'customer', 'waiter'], 'default', 'value' => null],
            [['id_room', 'total_harga', 'id_outlet'], 'integer'],
            [['no_nota', 'tgl_transaksi', 'total_harga', 'id_outlet'], 'required'],
            [['no_nota'], 'string', 'max' => 50],
            [['customer', 'waiter'], 'string', 'max' => 100],
            [['id_outlet', 'no_nota'], 'unique', 'targetAttribute' => ['id_outlet', 'no_nota']],
            [['id_room'], 'exist', 'skipOnError' => true, 'targetClass' => Room::class, 'targetAttribute' => ['id_room' => 'id']],
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
            'id_room' => 'Id Room',
            'no_nota' => 'No view',
            'customer' => 'Customer',
            'total_harga' => 'Total Harga',
            'id_outlet' => 'Id Outlet',
            'waiter' => 'Waiter',
        ];
    }

    /**
     * Gets query for [[DetailTransaksiPakets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDetailTransaksiPakets()
    {
        return $this->hasMany(DetailTransaksiPaket::class, ['id_transaksi' => 'id']);
    }

    /**
     * Gets query for [[DetailTransaksis]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDetailTransaksis()
    {
        return $this->hasMany(DetailTransaksi::class, ['id_transaksi' => 'id']);
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

    /**
     * Gets query for [[Room]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRoom()
    {
        return $this->hasOne(Room::class, ['id' => 'id_room']);
    }

}
