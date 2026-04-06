<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_menu_access".
 *
 * @property int $id
 * @property int $fk_menu_id
 * @property int $fk_user_id
 * @property int $view_crud
 * @property int $create_crud
 * @property int $edit_crud
 * @property int $delete_crud
 * @property int $status
 * @property string $ip
 * @property int $crt_by
 * @property string $crt_time
 * @property int $mod_by
 * @property string $mod_time
 */
class TblMenuAccess extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_menu_access';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fk_menu_id', 'fk_user_id'], 'required'],
            [['fk_menu_id', 'fk_user_id', 'view_crud', 'create_crud', 'edit_crud', 'delete_crud', 'status', 'crt_by', 'mod_by'], 'integer'],
            [['crt_time', 'mod_time'], 'safe'],
            [['ip'], 'string', 'max' => 30],
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
            'fk_menu_id' => 'Fk Menu ID',
            'fk_user_id' => 'Fk User ID',
            'view_crud' => 'View Crud',
            'create_crud' => 'Create Crud',
            'edit_crud' => 'Edit Crud',
            'delete_crud' => 'Delete Crud',
            'status' => 'Status',
            'ip' => 'Ip',
            'crt_by' => 'Crt By',
            'crt_time' => 'Crt Time',
            'mod_by' => 'Mod By',
            'mod_time' => 'Mod Time',
        ];
    }
}
