<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "stock_rak".
 *
 * @property int $id
 * @property string $nama
 * @property string|null $deskripsi
 *
 * @property Barang[] $barangs
 * @property MasterBelanja[] $masterBelanjas
 */
class StockRak extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stock_rak';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['deskripsi'], 'default', 'value' => null],
            [['nama'], 'required'],
            [['deskripsi'], 'string'],
            [['nama'], 'string', 'max' => 50],
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
            'deskripsi' => 'Deskripsi',
        ];
    }

    /**
     * Gets query for [[Barangs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBarangs()
    {
        return $this->hasMany(Barang::class, ['id_stock_rak' => 'id']);
    }

    /**
     * Gets query for [[MasterBelanjas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMasterBelanjas()
    {
        return $this->hasMany(MasterBelanja::class, ['id_stock_rak' => 'id']);
    }

}
