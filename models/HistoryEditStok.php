<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "history_edit_stok".
 *
 * @property int $id
 * @property int $id_stok_barang
 * @property int $id_user
 * @property string|null $keterangan
 * @property string $tanggal
 * @property int $stok_awal
 * @property int $stok_akhir
 *
 * @property StokBarang $stokBarang
 * @property User $user
 */
class HistoryEditStok extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'history_edit_stok';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['keterangan'], 'default', 'value' => null],
            [['id_stok_barang', 'id_user', 'tanggal', 'stok_awal', 'stok_akhir'], 'required'],
            [['id_stok_barang', 'id_user', 'stok_awal', 'stok_akhir'], 'integer'],
            [['keterangan'], 'string'],
            [['tanggal'], 'safe'],
            [['id_stok_barang'], 'exist', 'skipOnError' => true, 'targetClass' => StokBarang::class, 'targetAttribute' => ['id_stok_barang' => 'id']],
            [['id_user'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['id_user' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_stok_barang' => 'Id Stok Barang',
            'id_user' => 'Id User',
            'keterangan' => 'Keterangan',
            'tanggal' => 'Tanggal',
            'stok_awal' => 'Stok Awal',
            'stok_akhir' => 'Stok Akhir',
        ];
    }

    /**
     * Gets query for [[StokBarang]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStokBarang()
    {
        return $this->hasOne(StokBarang::class, ['id' => 'id_stok_barang']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'id_user']);
    }

}
