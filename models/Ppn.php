<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ppn".
 *
 * @property int $id
 * @property string $nama
 * @property int $value
 *
 * @property Barang[] $barangs
 */
class Ppn extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ppn';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nama', 'value'], 'required'],
            [['value'], 'integer'],
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
            'value' => 'Value',
        ];
    }

    /**
     * Gets query for [[Barangs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBarangs()
    {
        return $this->hasMany(Barang::class, ['id_ppn' => 'id']);
    }

}
