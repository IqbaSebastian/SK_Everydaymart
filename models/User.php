<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    public static function tableName()
    {
        return 'user';
    }

    public function rules()
    {
        return [
            [['username', 'password', 'role_id'], 'required'],
            [['role_id', 'id_outlet'], 'integer'],
            [['username', 'password', 'name', 'photo_url'], 'string', 'max' => 255],
            [['last_login', 'last_logout'], 'safe'],
            [['username'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'name' => 'Nama Lengkap',
            'role_id' => 'Role ID',
            'id_outlet' => 'Outlet Penugasan',
        ];
    }

    public function getOutlet()
    {
        return $this->hasOne(Outlet::class, ['id' => 'id_outlet']);
    }

    public function isSuperAdmin()
    {
        return (int)$this->role_id === 1;
    }

    public function isManager()
    {
        return (int)$this->role_id === 2;
    }

    public function isKasir()
    {
        return (int)$this->role_id === 3;
    }

    public function isGudang()
    {
        return (int)$this->role_id === 4;
    }

    public function getRoleName()
    {
        return match((int)$this->role_id) {
            1 => 'ADMIN',
            2 => 'MANAGER',
            3 => 'KASIR',
            4 => 'GUDANG',
            default => 'USER',
        };
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return null;
    }

    public function validateAuthKey($authKey)
    {
        return true;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }
}