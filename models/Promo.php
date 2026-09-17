<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "promo".
 *
 * @property int $id
 * @property string $judul
 * @property string|null $foto
 *
 * @property DetailPromo[] $detailPromos
 */
class Promo extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'promo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['foto'], 'default', 'value' => null],
            [['judul'], 'required'],
            [['judul'], 'string', 'max' => 150],
            [['foto'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'judul' => 'Judul',
            'foto' => 'Foto',
        ];
    }

    /**
     * Gets query for [[DetailPromos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDetailPromos()
    {
        return $this->hasMany(DetailPromo::class, ['id_promo' => 'id']);
    }

}
