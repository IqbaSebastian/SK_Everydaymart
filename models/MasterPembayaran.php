<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "master_pembayaran".
 *
 * @property int $id
 * @property string $kode
 * @property string|null $keterangan
 *
 * @property DetailBelanjaBarang[] $detailBelanjaBarangs
 */
class MasterPembayaran extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'master_pembayaran';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['keterangan'], 'default', 'value' => null],
            [['kode'], 'required'],
            [['kode'], 'string', 'max' => 20],
            [['keterangan'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'kode' => 'Kode',
            'keterangan' => 'Keterangan',
        ];
    }

    /**
     * Gets query for [[DetailBelanjaBarangs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDetailBelanjaBarangs()
    {
        return $this->hasMany(DetailBelanjaBarang::class, ['id_master_pembayaran' => 'id']);
    }

}
