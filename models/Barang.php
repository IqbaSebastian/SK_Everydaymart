<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "barang".
 *
 * @property int $id
 * @property string $nama
 * @property string $satuan
 * @property int $harga
 * @property string|null $foto
 * @property int|null $id_group
 * @property int|null $id_stock_rak
 * @property int|null $id_ppn
 *
 * @property BarangOutlet[] $barangOutlets
 * @property DetailBelanjaBarang[] $detailBelanjaBarangs
 * @property DetailPaket[] $detailPakets
 * @property DetailTransaksiBatal[] $detailTransaksiBatals
 * @property DetailTransaksi[] $detailTransaksis
 * @property Diskon[] $diskons
 * @property Group $group
 * @property Outlet[] $outlets
 * @property Outlet[] $outlets0
 * @property Ppn $ppn
 * @property StockRak $stockRak
 * @property StokBarang[] $stokBarangs
 * @property TransaksiBatal[] $transaksiBatals
 */
class Barang extends \yii\db\ActiveRecord
{
    /**
     * @var UploadedFile
     */
    public $imageFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'barang';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
{
    return [
        [['id_group', 'id_stock_rak', 'id_ppn', 'foto'], 'default', 'value' => null],
        [['nama', 'satuan', 'harga'], 'required'],
        [['harga', 'id_group', 'id_stock_rak', 'id_ppn'], 'integer'],
        [['nama'], 'string', 'max' => 150],
        [['satuan'], 'string', 'max' => 20],
        [['foto'], 'string', 'max' => 255],
        [['imageFile'], 'file', 
            'skipOnEmpty' => true, 
            'extensions' => 'png, jpg, jpeg, webp', 
            'maxSize' => 1024 * 1024 * 2,
            'checkExtensionByMimeType' => false
        ],

        [['id_group'], 'exist', 'skipOnError' => true, 'targetClass' => Group::class, 'targetAttribute' => ['id_group' => 'id']],
        [['id_stock_rak'], 'exist', 'skipOnError' => true, 'targetClass' => StockRak::class, 'targetAttribute' => ['id_stock_rak' => 'id']],
        [['id_ppn'], 'exist', 'skipOnError' => true, 'targetClass' => Ppn::class, 'targetAttribute' => ['id_ppn' => 'id']],
    ];
}

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID Barang',
            'nama' => 'Nama Barang',
            'satuan' => 'Satuan (Pcs/Kg/Pack)',
            'harga' => 'Harga Jual (Rp)',
            'foto' => 'Foto Produk',
            'imageFile' => 'Upload Foto Produk',
            'id_group' => 'Group / Kategori',
            'id_stock_rak' => 'Lokasi Rak',
            'id_ppn' => 'Skema PPN',
        ];
    }

    /**
     * Gets query for [[BarangOutlets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBarangOutlets()
    {
        return $this->hasMany(BarangOutlet::class, ['id_barang' => 'id']);
    }

    /**
     * Gets query for [[DetailBelanjaBarangs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDetailBelanjaBarangs()
    {
        return $this->hasMany(DetailBelanjaBarang::class, ['id_barang' => 'id']);
    }

    /**
     * Gets query for [[DetailPakets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDetailPakets()
    {
        return $this->hasMany(DetailPaket::class, ['barang' => 'id']);
    }

    /**
     * Gets query for [[DetailTransaksiBatals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDetailTransaksiBatals()
    {
        return $this->hasMany(DetailTransaksiBatal::class, ['id_barang' => 'id']);
    }

    /**
     * Gets query for [[DetailTransaksis]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDetailTransaksis()
    {
        return $this->hasMany(DetailTransaksi::class, ['id_barang' => 'id']);
    }

    /**
     * Gets query for [[Diskons]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDiskons()
    {
        return $this->hasMany(Diskon::class, ['id_barang' => 'id']);
    }

    /**
     * Gets query for [[Group]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Group::class, ['id' => 'id_group']);
    }

    /**
     * Gets query for [[Outlets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOutlets()
    {
        return $this->hasMany(Outlet::class, ['id' => 'id_outlet'])->viaTable('barang_outlet', ['id_barang' => 'id']);
    }

    /**
     * Gets query for [[Outlets0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOutlets0()
    {
        return $this->hasMany(Outlet::class, ['id' => 'id_outlet'])->viaTable('stok_barang', ['id_barang' => 'id']);
    }

    /**
     * Gets query for [[Ppn]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPpn()
    {
        return $this->hasOne(Ppn::class, ['id' => 'id_ppn']);
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

    /**
     * Gets query for [[StokBarangs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStokBarangs()
    {
        return $this->hasMany(StokBarang::class, ['id_barang' => 'id']);
    }

    /**
     * Gets query for [[TransaksiBatals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTransaksiBatals()
    {
        return $this->hasMany(TransaksiBatal::class, ['id_barang' => 'id']);
    }
}