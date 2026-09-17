<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "belanja_barang".
 *
 * @property int $id
 * @property string $no_nota
 * @property string $pegawai
 * @property string $tanggal
 * @property int $total_biaya
 * @property int $id_outlet
 *
 * @property DetailBelanjaBarang[] $detailBelanjaBarangs
 * @property Outlet $outlet
 */
class BelanjaBarang extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'belanja_barang';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['no_nota', 'pegawai', 'tanggal', 'total_biaya', 'id_outlet'], 'required'],
            [['tanggal'], 'safe'],
            [['total_biaya', 'id_outlet'], 'integer'],
            [['no_nota'], 'string', 'max' => 50],
            [['pegawai'], 'string', 'max' => 100],
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
            'no_nota' => 'No Nota',
            'pegawai' => 'Pegawai',
            'tanggal' => 'Tanggal',
            'total_biaya' => 'Total Biaya',
            'id_outlet' => 'Id Outlet',
        ];
    }

    /**
     * Gets query for [[DetailBelanjaBarangs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDetailBelanjaBarangs()
    {
        return $this->hasMany(DetailBelanjaBarang::class, ['id_belanja_barang' => 'id']);
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
