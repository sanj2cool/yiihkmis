<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_user".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string|null $alias
 * @property string|null $user_key
 * @property int $fk_role_id
 * @property int|null $fk_employee_id
 * @property string|null $auth_key
 * @property string $ip
 * @property int $status 1 active 2 blocked 0 inactive
 * @property string $crt_by
 * @property string $mod_by
 * @property string $crt_time
 * @property string|null $mod_time
 */
class TblUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            [['fk_role_id', 'fk_employee_id', 'status'], 'integer'],
            [['crt_time', 'mod_time'], 'safe'],
            [['username', 'password', 'user_key', 'auth_key'], 'string', 'max' => 100],
            [['alias'], 'string', 'max' => 20],
            [['ip', 'crt_by'], 'string', 'max' => 40],
            [['mod_by'], 'string', 'max' => 50],
        ];
    }
    public function beforeSave($insert) {
        $session = Yii::$app -> session;
        if ($insert) {
          $this -> ip = Yii::$app -> getRequest() -> getUserIp();
          $this -> crt_by = $session -> get('userId');
          $this -> crt_time = date('Y-m-d H:i:s');
          $this -> status = 1;
        } else {
          $this -> ip = Yii::$app -> getRequest() -> getUserIp();
          $this -> mod_by = $session -> get('userId');
          $this -> mod_time = date('Y-m-d H:i:s');
        }
        return parent::beforeSave($insert);
      }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'alias' => 'Alias',
            'user_key' => 'User Key',
            'fk_role_id' => 'Role',
            'fk_employee_id' => 'Employee',
            'auth_key' => 'Auth Key',
            'ip' => 'Ip',
            'status' => 'Status',
            'crt_by' => 'Crt By',
            'mod_by' => 'Mod By',
            'crt_time' => 'Crt Time',
            'mod_time' => 'Mod Time',
        ];
    }
    public function getUserLocations()
    {
        return $this->hasMany(TblUserLocation::className(), ['fk_user_id' => 'id'])
                    ->onCondition(['tbl_user_location.status'=>1]);
    }
    // Relation to mechanics through work_order_mechanic
    public function getLocations()
    {
        return $this->hasMany(TblOwnershipCompany::className(), ['id' => 'fk_location_id'])
            ->via('userLocations');
    }
}
