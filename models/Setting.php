<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "setting".
 *
 * @property int $id
 * @property int|null $min_stok_barang
 * @property string|null $password_transaksi
 */
class Setting extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'setting';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['password_transaksi'], 'default', 'value' => null],
            [['min_stok_barang'], 'default', 'value' => 0],
            [['min_stok_barang'], 'integer'],
            [['password_transaksi'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'min_stok_barang' => 'Min Stok Barang',
            'password_transaksi' => 'Password Transaksi',
        ];
    }

}
