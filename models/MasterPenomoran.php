<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "master_penomoran".
 *
 * @property int $id
 * @property int $no_berjalan
 * @property string $format_penuh
 * @property int $periode_atur_ulang
 * @property string $periode_berjalan
 * @property int $id_outlet
 *
 * @property Outlet $outlet
 */
class MasterPenomoran extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'master_penomoran';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['no_berjalan'], 'default', 'value' => 1],
            [['no_berjalan', 'periode_atur_ulang', 'id_outlet'], 'integer'],
            [['format_penuh', 'periode_atur_ulang', 'periode_berjalan', 'id_outlet'], 'required'],
            [['format_penuh'], 'string', 'max' => 100],
            [['periode_berjalan'], 'string', 'max' => 20],
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
            'no_berjalan' => 'No Berjalan',
            'format_penuh' => 'Format Penuh',
            'periode_atur_ulang' => 'Periode Atur Ulang',
            'periode_berjalan' => 'Periode Berjalan',
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

}
