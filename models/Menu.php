<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "menu".
 *
 * @property int $id
 * @property string $name
 * @property string|null $controller
 * @property string|null $action
 * @property string|null $icon
 * @property int|null $order
 * @property int|null $parent_id
 *
 * @property Menu[] $menus
 * @property Menu $parent
 * @property RoleMenu[] $roleMenus
 * @property Role[] $roles
 */
class Menu extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'menu';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['controller', 'action', 'icon', 'parent_id'], 'default', 'value' => null],
            [['order'], 'default', 'value' => 0],
            [['name'], 'required'],
            [['order', 'parent_id'], 'integer'],
            [['name', 'controller', 'action'], 'string', 'max' => 100],
            [['icon'], 'string', 'max' => 50],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Menu::class, 'targetAttribute' => ['parent_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'controller' => 'Controller',
            'action' => 'Action',
            'icon' => 'Icon',
            'order' => 'Order',
            'parent_id' => 'Parent ID',
        ];
    }

    /**
     * Gets query for [[Menus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMenus()
    {
        return $this->hasMany(Menu::class, ['parent_id' => 'id']);
    }

    /**
     * Gets query for [[Parent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Menu::class, ['id' => 'parent_id']);
    }

    /**
     * Gets query for [[RoleMenus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRoleMenus()
    {
        return $this->hasMany(RoleMenu::class, ['menu_id' => 'id']);
    }

    /**
     * Gets query for [[Roles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRoles()
    {
        return $this->hasMany(Role::class, ['id' => 'role_id'])->viaTable('role_menu', ['menu_id' => 'id']);
    }

}
