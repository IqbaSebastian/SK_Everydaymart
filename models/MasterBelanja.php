<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "master_belanja".
 *
 * @property int $id
 * @property string $nama
 * @property int|null $id_stock_rak
 * @property int $id_outlet
 *
 * @property Outlet $outlet
 * @property StockRak $stockRak
 */
class MasterBelanja extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'master_belanja';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_stock_rak'], 'default', 'value' => null],
            [['nama', 'id_outlet'], 'required'],
            [['id_stock_rak', 'id_outlet'], 'integer'],
            [['nama'], 'string', 'max' => 100],
            [['id_stock_rak'], 'exist', 'skipOnError' => true, 'targetClass' => StockRak::class, 'targetAttribute' => ['id_stock_rak' => 'id']],
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
            'id_stock_rak' => 'Id Stock Rak',
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
     * Gets query for [[StockRak]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStockRak()
    {
        return $this->hasOne(StockRak::class, ['id' => 'id_stock_rak']);
    }

}
