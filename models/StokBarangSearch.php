<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\StokBarang;

class StokBarangSearch extends StokBarang
{
    public function rules()
    {
        return [
            [['id', 'id_barang', 'id_outlet', 'jumlah_stok'], 'integer'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = StokBarang::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'id_barang' => $this->id_barang,
            'id_outlet' => $this->id_outlet,
            'jumlah_stok' => $this->jumlah_stok,
        ]);

        return $dataProvider;
    }
}