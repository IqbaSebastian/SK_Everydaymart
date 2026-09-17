<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "action".
 *
 * @property int $id
 * @property string $controller_id
 * @property string $action_id
 * @property string $name
 *
 * @property RoleAction[] $roleActions
 * @property Role[] $roles
 */
class Action extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'action';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['controller_id', 'action_id', 'name'], 'required'],
            [['controller_id', 'action_id', 'name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'controller_id' => 'Controller ID',
            'action_id' => 'Action ID',
            'name' => 'Name',
        ];
    }

    /**
     * Gets query for [[RoleActions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRoleActions()
    {
        return $this->hasMany(RoleAction::class, ['action_id' => 'id']);
    }

    /**
     * Gets query for [[Roles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRoles()
    {
        return $this->hasMany(Role::class, ['id' => 'role_id'])->viaTable('role_action', ['action_id' => 'id']);
    }

}
