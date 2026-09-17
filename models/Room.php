<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "room".
 *
 * @property int $id
 * @property string $nama
 * @property int $id_outlet
 *
 * @property Outlet $outlet
 * @property Transaksi[] $transaksis
 */
class Room extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'room';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nama', 'id_outlet'], 'required'],
            [['id_outlet'], 'integer'],
            [['nama'], 'string', 'max' => 50],
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
            'nama' => 'Nama',
            'id_outlet' => 'Id Outlet',
        ];
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
     * Gets query for [[Transaksis]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTransaksis()
    {
        return $this->hasMany(Transaksi::class, ['id_room' => 'id']);
    }

}
