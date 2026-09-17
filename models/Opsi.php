<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "opsi".
 *
 * @property int $id
 * @property string $nama
 *
 * @property DetailTransaksiBatal[] $detailTransaksiBatals
 * @property TransaksiBatal[] $transaksiBatals
 */
class Opsi extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'opsi';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nama'], 'required'],
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
        ];
    }

    /**
     * Gets query for [[DetailTransaksiBatals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDetailTransaksiBatals()
    {
        return $this->hasMany(DetailTransaksiBatal::class, ['id_opsi' => 'id']);
    }

    /**
     * Gets query for [[TransaksiBatals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTransaksiBatals()
    {
        return $this->hasMany(TransaksiBatal::class, ['id_opsi' => 'id']);
    }

}
